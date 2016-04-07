<?php



// The worker will execute every X seconds:
$seconds = 2;

// We work out the micro seconds ready to be used by the 'usleep' function.
$micro = $seconds * 1000000;

$pipe_file_name = 'all_logs.log';

$tmp_file = 'all_logs.tmp';

$new_file = 'all_logs.new';

$old_file = 'all_logs.old';

$ignored = array('.', '..', basename(__FILE__), $pipe_file_name, $tmp_file, $new_file, $old_file, 'psysh');

//print_r($ignored);

$current_dir = dirname(__FILE__);

$max_time_logs = 30; //we will have 30 seconds

//require('psysh');


function tryToReadJson($file, $error_str)
{
    try 
    {
        $json_str = file_get_contents($file);
        if ($json_str === false)
        {
            error_log("$error_str \n");
            $json_str = '[]';
        }        
    } catch (Exception $e) {
        // Deal with it.
        error_log($error_str . $e->getMessage() . " $file \n");
        $json_str = '[]';
    }

    return $json_str;
}

function tryToGetData($json_str, $error_str)
{
    try
    {
        $json_a = json_decode($json_str, true); 
        if ($json_a === NULL)
        {
            error_log($error_str . $json_str);
            $json_a = array();
        }
    } catch (Exception $e) {
        error_log($error_str . $e->getMessage() . " $json_str \n");               
        $json_a = array();
    }

    return $json_a;
}

function cleanOldEvents($json_a, $current_limit)
{
    $updated_a = array();

    try
    {
        if (count($json_a) > 0)
        {
            foreach ($json_a as $json_event) {
                $event_time = new Datetime($json_event['event_date']);
                if ($event_time >= $current_limit)
                {
                    //print_r('json_event: ');
                    //var_dump($json_event);
                    $updated_a[] = $json_event;
                }
            }
        }
        else
        {
            echo ("No data on pipe.. \n");
        }    
    } catch (Exception $e) { 
        error_log("Error cleaning events: " . $e->getMessage() . print_r($json_a) . " \n");               
        $json_a = array();
    }

    return $updated_a;    
}


function getNewFiles($current_dir, $ignored)
{
    //we read all log files created
    $all_files = scandir($current_dir);
    //print_r($all_files);
    $log_files = array_diff($all_files, $ignored);
    $files = array();    
    foreach ($log_files as $file) {
        //print_r("$file \n");        
        $full_path_file = $current_dir . '/' . $file;
        $files[$full_path_file] = filemtime($full_path_file);
    }
    //we sort by mtime
    arsort($files);

    return $files;
}

function processEventFiles($files, $updated_a, $current_limit)
{
    foreach ($files as $file_name => $mtime) {

        //eval(\Psy\sh());

        try
        {            
            $json_one = file_get_contents($file_name);
            if ($json_one == false)
            {
                error_log("Could not read $file_name \n");
                $json_one = '';    
            }
                                                                        

        } catch (Exception $e) {
            error_log("Could not read $file_name ". $e->getMessage() . " \n");
            $json_one = '';
        }

        try
        {

            //eval(\Psy\sh());
            $json_one_data = json_decode($json_one, true);

            $one_time = new Datetime($json_one_data['event_date']);

            if ($one_time >= $current_limit)
            {
                $updated_a[] = $json_one_data;
                print_r('json_one_data: ');
                print_r($json_one_data);
            }        
            //print_r("$file_name \n");
            unlink($file_name);
            print_r("Deleted: " . $file_name . "\n");
        } catch (Exception $e) {
            error_log("Error processing event file: " . $e->getMessage() . $file_name ." \n");
        }
        
        
    }

    //eval(\Psy\sh());

    $updated_json = json_encode($updated_a);

    return $updated_json;
}



while (true) {

    print_r("Execution \n");
    
    $current_limit = new Datetime();
    $current_limit->modify("-$max_time_logs seconds");

    $pipe_full_path = $current_dir . '/' . $pipe_file_name;    

    $json_str = tryToReadJson($pipe_full_path, "Could not read pipe_file_name: ");

    $json_a = tryToGetData($json_str, "Error decoding json_string: ");

    $updated_a = cleanOldEvents($json_a, $current_limit);

    
    $files = getNewFiles($current_dir, $ignored);


    $updated_json = processEventFiles($files, $updated_a, $current_limit);
        
    if ($updated_json == NULL)
    {
        error_log("Error processing events  " . $e->getMessage() . print_r($updated_a) ." \n");
        $updated_json = '[]';        
    }

    if ($updated_json)
    {
        file_put_contents($current_dir . '/' . $tmp_file, $updated_json);

        rename($current_dir . '/' . $pipe_file_name, $current_dir . '/' .$old_file);
        rename($current_dir . '/' .$tmp_file, $current_dir . '/' . $pipe_file_name);    
    }    

    // Now before we 'cycle' again, we'll sleep for a bit...
    usleep($micro);
}
