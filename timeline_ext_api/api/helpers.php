<?php



function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}




/**
 * We automate the response code and JSON for the data
 *
 */
function returnResponse($status_code, $response, $data="") 
{
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');
    
    if ($status_code != 200)
    {
        $return['code'] = $status_code;
        $return['response'] = $response;
        $return['data'] = $data;
        echo json_encode($return, JSON_PRETTY_PRINT);        
    }
    else
    {
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    
    $app->stop();            
    
        
}



/**
 * This function try to convert all the dates on the
 * data to MongoDate so we can index by date correctly
 *
 */
function convertToMongo(&$item, $key)
{

    if ($key == 'timestamp') 
    {
        if (is_numeric($item))
        {
            try
            {                                
                $item = new MongoDate($item/1000);
                error_log('item: ' . print_r($item, true) );                    
            } catch (Exception $ex) {
                error_log($ex->getMessage());
            }

                
        }
                
    }
    if (strpos($key, 'incident') !== false)
    {
        return false;
    }
    $valid_datetime = strtotime($item);    
    if ($valid_datetime)
    {        
        if (strlen($item > 9))
        {            
            try
            {                                
                $item = new MongoDate($valid_datetime);                
                //var_dump(array($key=>$item));
            } catch (Exception $ex) {
                //var_dump($ex->getMessage());
            }
            
        }
    }
}



/**
 * This function traverse all the JSON array recursively
 * to get first Mongodate on the data so we 
 *
 */
function getDocumentDate($json_array)
{    
    foreach ($json_array as $k => $v) {

        if ($v instanceof MongoDate)
        {                                                
            return $v;            
        }
        if  (is_array($v))
        {                       
            $result = getDocumentDate($v);
            if ($result !== false)
            {
                return $result;
            }            
        }           
    }

    return false;

    
}

/**
 * Make sure that we get a correct JSON and in that  
 * case we convert to array we valid Mongodates
 *
 */
function checkJSON($string)
{
    $string_decoded = json_decode($string);       
            
    if (is_string($string) && is_object($string_decoded) && (json_last_error() == JSON_ERROR_NONE))
    {
        $json_array = json_decode($string, true);
        array_walk_recursive($json_array, 'convertToMongo');                
        return $json_array;
    }

    return false;
}



/**
 * We convert the Mongodb dates on database
 * on human dates for printing and mongoids
 * to string
 *
 */
function convertToHuman(&$item, $key)
{
    
    if ($item instanceof MongoDate)
    {        
        try
        {        
            $seconds = $item->sec;
            $item = date(DATE_ISO8601, $seconds);        
            //var_dump($item);
        } catch (Exception $ex) {

        }
        
    }    
    elseif (($key == '_id') && ($item instanceof MongoId))
    {
        $item = $item->__toString();
    }
    
    
    
}

//this function is to convert the Mongodates to the string iso
function prettyPrintMongoObj($mongo_array)
{
    array_walk_recursive($mongo_array, 'convertToHuman');
    return $mongo_array;
}



/**
 * This functions is to show the Pagerduty info
 * more user friendly
 *
 */
function customizeOutputPagerduty($json_array)
{
                
    $custom_output = array();
    foreach ($json_array as $event) 
    {
        $map_custom = array();
        $map_custom['id'] = $event['id'];
        $map_custom['content'] = $event['trigger_summary_data']['subject'];
        $map_custom['start'] = $event['created_on'];
        $map_custom['end'] = $event['last_status_change_on'];
        $map_custom['incident_id'] = $event['id'];
        $map_custom['url'] = $event['html_url'];
        $map_custom['status'] = $event['status'];
        $map_custom['group'] = 0;
        $custom_output[] = $map_custom;
    }
    
    return $custom_output;
}

/**
 * This functions is to show the Custom integrations info 
 * pretty printed
 *
 */
function customizeOutputCustom($json_array)
{
    $output = array();
    
    $output_fields = array('content', 'start', 'end', 'owner');    
    
    foreach ($json_array as $event)
    {
        if (!array_key_exists('content', $event) || !array_key_exists('start', $event))
        {
            return false;
        }
        $custom_event = array();
        foreach ($output_fields as $key_field) 
        {           
            if (array_key_exists($key_field, $event))
            {                
                $custom_event[$key_field] = $event[$key_field];
            }
            elseif ($key_field == '_id')
            {
                $custom_event['id'] = $event[$key_field];
            }
        }
        if (array_key_exists('_id', $event))
        {
            $custom_event['id'] = $event['_id'];
        }
        $output[] = $custom_event;        
    }
    
    return $output;
}

/**
 * This functions is to show the NewRelic integrations info 
 * pretty printed
 *
 */
function customizeOutputNewRelic($json_array)
{
//    {
//        "created_at":"2014-03-04T22:31:35+00:00",
//        "alert_policy_name":"Default application alert policy",
//        "account_name":"Account name",
//        "severity":"Critical",
//        "message":"Message about alert",
//        "short_description":"Short description about alert ended",
//        "long_description":"Long description about alert ended",
//        "alert_url":"http://PATH_TO_NEW_RELIC",
//        "application_name":""
//    }
    
    $output = array();
    //$mandatory_field = array('message', 'created_at');
    foreach ($json_array as $event)
    {
        if (!array_key_exists('message', $event) || !array_key_exists('created_at', $event))
        {
            return false;
        }        
        $custom_event = array();
        $custom_event['start'] = $event['created_at'];
        $custom_event['content'] = $event['message'];
        $custom_event['url'] = array_key_exists('alert_url', $event) ? $event['alert_url'] : '';
        
        $output[] = $custom_event;
    }
    
    return $output;
}



/**
 * This function try to validate the input and if not
 * return the errors with useful info 
 *
 *
 */
function validatePOST($array_json, $validation_rules)
{    

    $gump = new GUMP();
    $gump->validation_rules($validation_rules);

    if ($gump->run($array_json) === false) 
    {
        return $gump->get_readable_errors(true);
    } 
    else 
    {
        return true;
    }
    
            
}

/**
 * This function generate a random API key for the
 * integrations
 *
 */
function generateAPIkey($length=50)
{    
    $key = "";
    // Alphabetical range
    $alph_from = 65;
    $alph_to = 90;

    // Numeric
    $num_from = 48;
    $num_to = 57;
    for ($i=1;$i<=$length;$i++) 
    {
        // Add a random num/alpha character
        $chr = rand(0,1)?(chr(rand($alph_from,$alph_to))):(chr(rand($num_from,$num_to)));
        if (rand(0,1)) 
        {
            $chr = strtolower($chr);
        }
        
        $key.=$chr;        
    }   

    return($key);

}



/**
 * Make sure the key generated is unique
 *
 */
function getUniqueKey($integrationColl)
{
    while (true)
    {
        $apiKey = generateAPIkey();        
        if ($integrationColl->count(array('apiKey' => $apiKey)) == 0)
        {
            return $apiKey;
        }
    }
   
}




/**
 * This function logs the Integration Name, the 
 *  timestamp, and event time to a unique file name 
 *  so the interface check if there has been changes 
 *  in the last 10 minutes
 *
 */
function tempLogging($integration_name, $all_data, $integration_type, $config)
{
    
    $now_datetime = new DateTime();
    $now_formatted = $now_datetime->format(DateTime::ISO8601);    
    

    $data_event = array(
        'integration_name' => $integration_name,
        'integration_type' => $integration_type,
        'event_date' => $now_formatted,
        'json_data' => $all_data
    );

    $data_log = json_encode($data_event);    

    $timestamp_filename = str_replace(':', '_', $now_formatted);
    $timestamp_filename = str_replace(' ', '-', $timestamp_filename);
    $random_key = generateAPIkey(15);
    $temp_file_name = "{$timestamp_filename}-{$random_key}.log";
    $full_path = $config['events_logs_path'] . $temp_file_name;    
    $result = file_put_contents($full_path, $data_log);    


}




/**
 * This function generate a list of all the Integrations
 * which is the collections names
 */
function getAllValidIntegrations($store)
{
    $not_valid = 'INTERNAL_integrations';
    $collections = $store->db->getCollectionNames();
    $valid_integrations = array();
    foreach ($collections as $col_name) 
    {
        if ($col_name != $not_valid)
        {
            $valid_integrations[] = $col_name;
        }            
    }
    
    return $valid_integrations;
}




/**
 * Depending on the integration type we get the field
 * to search for
 */
function getSearchField($type, $store, $integration_name)
{
    if ($type == 'pagerduty')
    {
        return 'created_on';
    }

    //error_log('getSearchField');

    if ($type == 'custom')
    {
        return 'start_date';
    }

    if ($type == 'newrelic')
    {
        return 'timestamp';
    }
    
    // if ($type == 'custom' || $type == 'newrelic')
    // {
    //     $doc_sample = $store->db->$integration_name->findOne();    
    //     if ($doc_sample)
    //     {
    //         foreach ($doc_sample as $key => $value) 
    //         {
    //             if ($value instanceof MongoDate)
    //             {
    //                 error_log('Search field:'. $key);
    //                 return $key;
    //             }
    //         }
    //     }
    // }
    
    return '';
    
    
    
}











/**
 * Depending on the integration type we get the 
 * integration data needed
 */
function get_JSON_data($integration_type, $json_array)
{
    #Pager duty json
    // "messages": [
    //     {
    //       "id": "bb8b8fe0-e8d5-11e2-9c1e-22000afd16cf",
    //       "created_on": "2013-07-09T20:25:44Z",
    //       "type": "incident.trigger",
    //       "data": {
    //         "incident": {
    //           "id": "PIJ90N7",
    //           "incident_number": 1,
    //           "created_on": "2013-07-09T20:25:44Z",
    //           "status": "triggered",
    //           "html_url": "https://acme.pagerduty.com/incidents/PIJ90N7",
    //           "incident_key": "null",
    //           "service": {
    //             "id": "PBAZLIU",
    //             "name": "service",
    //             "html_url": "https://acme.pagerduty.com/services/PBAZLIU"
    //           },
    //           "assigned_to_user": {
    //             "id": "PPI9KUT",
    //             "name": "Alan Kay",
    //             "email": "alan@pagerduty.com",
    //             "html_url": "https://acme.pagerduty.com/users/PPI9KUT"
    //           },
    //           "trigger_summary_data": {
    //             "subject": "45645"
    //           },
    //           "trigger_details_html_url": "https://acme.pagerduty.com/incidents/PIJ90N7/log_entries/PIJ90N7",
    //           "last_status_change_on": "2013-07-09T20:25:44Z",
    //           "last_status_change_by": "null"
    //         }
    //       }
    //     },

    $all_incidents = array();
    
    if ($integration_type == 'pagerduty')
    {
        
        $valid_types = array('incident.trigger', 
            'incident.acknowledge',
            'incident.resolve'
        );

        foreach ($json_array['messages'] as $message)
        {
            $incident_type = $message['type'];
            if (in_array($incident_type, $valid_types))
            {	        
                $incident = $message['data']['incident'];
                $incident['_id'] = $incident['id'];
                $all_incidents[] = $incident;
            }
            
        }        

        return ($all_incidents);     
    }

    if ($integration_type == 'newrelic')
    {
                         
        // this code is for old style alerts
        // if (array_key_exists('short_description', $json_array))
        // {
        //     if (startsWith($json_array['short_description'], 'Ended alert') || 
        //         startsWith($json_array['short_description'], 'All alerts have been closed'))
        //     {
        //         return false;
        //     }

        // }

        if (array_key_exists('incident_id', $json_array))
        {
            //var_dump($json_array['incident_id']);
            //echo 'INCIDENTE: ' . $json_array['incident_id'];
            $json_array['_id'] = $json_array['incident_id'];
        }        

        return $json_array;

        
        // if (array_key_exists('alert_url', $json_array) || (array_key_exists('deployment_url', $json_array)))
        // {   
            
            
        //     if ($json_array['alert_url'])
        //     {
        //         $divided = explode('/', $json_array['alert_url']);
        //         $len = count($divided) - 1;                                
        //         $before_end = $divided[$len - 1];                 
        //         if ($before_end == 'incidents') 
        //         {
        //             $json_array['_id'] = $divided[$len];
        //         }
        //     }
            
                    
        // }
        // else
        // {
        //     return false;
        // }
            
            
    }

    //this is for custom integrations
    if ($integration_type == 'custom')
    {
        if (array_key_exists('message', $json_array) &&
            array_key_exists('username', $json_array))
        {
            if (!array_key_exists('start_date', $json_array))
            {
                $now_datetime = new DateTime();
                $json_array['start_date'] = $now_datetime->format(DateTime::ISO8601);     
            }
            return $json_array;
        }
    }
    



    
}









/**
 *  This function is to get the INTERNAL_ data from integration
 */
function getIntegrationData($store, $integration_name, $apiKey=false)
{

    $app = \Slim\Slim::getInstance();
    
    if (!$store->checkCollectionExists($integration_name))
    {
        returnResponse(404, "Not Found", "Integration name doesn't exists");        
    }
    
    $integrationsColl = $app->config('config')['integrationsColl'];
    $internal_integration = $store->getCollectionNew($integrationsColl);
    
    if ($apiKey === false)
    {
        $integration_result = $internal_integration->findOne(array(         
            'integration_name' => $integration_name            
        ));
    }
    else
    {
        $integration_result = $internal_integration->findOne(array(         
            'integration_name' => $integration_name,
            'apiKey' => $apiKey
        ));
    }    
    
    if (!$integration_result)
    {
        returnResponse(404, "Not Found", "Integration name doesn't exists");
    }
    
    return $integration_result;
            
}





/**
 *  This function is to check the ip request
 *  of the client
 */
function ipAuthorization() {    
    //range ips: "172.16.0.0 - 172.31.255.255" 

    $app = \Slim\Slim::getInstance();

    //$response->getBody()->write('BEFORE');   
    $first_octect = '172.';
    //31   
    $ip = $app->request->getIp();    

    for ($x=16; $x<=31; $x++)
    {
        $start = "{$first_octect}{$x}.";
        if (startsWith($ip, $start))
        {                             
            return true;            
        }
    }
    
    if ($ip == '54.183.142.213' || $ip == '83.231.120.183')
    {
        return true;
    }
   
    return false;

    
}


/**
 *  This function check the ip client request
 *  is a valid pagerduty ip
 */
function pagerdutyAuth()
{
    $app = \Slim\Slim::getInstance();
    $ip = $app->request->getIp();
    $query_host = gethostbyaddr($ip);            

    if ( ($ip == '54.177.29.131') ||
       ($ip == '54.188.21.146')   || 
       ($ip == '54.189.168.180')  ||
       ($ip == '104.42.123.77')   ||
       ($ip == '50.18.129.59')    ||
       ($ip == '54.212.55.143'))
    {        
        return true; 
    }

    if (ipAuthorization())
    {   
        return true;
    }

    return false;

}

/**
 *  This function check the ip client request
 *  is a valid NewRelic ip
 */
function newRelicAuth()
{
    $app = \Slim\Slim::getInstance();    
    $ip = $app->request->getIp();
    
    if (startsWith($ip, '50.31.164')   ||
        startsWith($ip, '162.247.240') || 
        startsWith($ip, '162.247.241') || 
        startsWith($ip, '162.247.242') || 
        startsWith($ip, '162.247.243'))
    {                             
        return true;
    }    

    if (ipAuthorization())
    {
        return true;
    }

    return false;

    
}



/**
 *  Add Mongodate validation to the GUMP library
 */
function addMongoDateValidator()
{
    // Add the custom validator
    GUMP::add_validator("mongodate_OR_empty", function($field, $input, $param = NULL) {
        try
        {
            if (get_class($input[$field]) == 'MongoDate')
            {
                return true;
            }

            throw new Exception('Is not Mongodate');
        }
        catch (Exception $e) 
        {
        
            return (empty($input[$field]));
        }                
             
    });    
}

/**
 *  Add simple empty validator to the GUMP library
 */
function addIsEmptyValidator()
{
    // Add the custom validator
    GUMP::add_validator("is_Empty", function($field, $input, $param = NULL) {
                
        return true;
    });    
}


/**
 *  Check we are not getting no valid strings for mongodb
 */
function addNotValidChars()
{
    GUMP::add_validator("not_Valid_chars", function($field, $input, $param = NULL) {        
        if (strpos($input[$field],'$') !== false) {
            return false;
        }

        if (strpos($input[$field],'system.') !== false) {
            return false;
        }
        return true;        
    });    
}













?>