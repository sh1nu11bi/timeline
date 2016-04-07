<?php

require 'vendor/autoload.php';

require 'config.php';

require 'db.php';



$app = new \Slim\Slim();

$app->config('debug', false);

//add a default 404 for all
$app->notFound(function () use ($app) {
    $view = $app->view();
    $view->setTemplatesDirectory('.');
    $app->render('404.html');
});

$app->config('config', $config);


require 'helpers.php';



// add custom validations
addMongoDateValidator();

addIsEmptyValidator();

addNotValidChars();


 
/**
 * This API is to accept and save to db the Pagerduty/New Relic webhooks json
 *
 */
$app->post('/webhooks/:integration_name/:apiKey', 
        function($integration_name, $apiKey) {    

    $app = \Slim\Slim::getInstance();                
    $json_body = $app->request()->getBody();

    $store = new DB($app->config('config'));

    //we make index in all text fields
    //$store->db->$integration_name->createIndex(array("$**"=> "text"));

    $integration_result = getIntegrationData($store,
            $integration_name, $apiKey);
    //we show all to apache logs    
    error_log('API POST DATA:' . $json_body);    

    //new relic integrations
    if ($integration_result['type'] == 'newrelic')
    {
        // $json_body =  urldecode($json_body);

        // if (startsWith($json_body, 'alert='))
        // {
        //     $before_start = 'alert=';
        // } elseif (startsWith($json_body, 'deployment='))
        // {
        //     returnResponse(200, array('deployment alert ignered'));
        // }
        // else
        // {
        //     $before_start = '';
        // }

        // $divided_body = explode($before_start, $json_body, 2);
      
        // if (count($divided_body) == 2)
        // {       
        //     $json_body = $divided_body[1];
        // }

        //$json_array = checkJSON($json_body);

    

    }

    //we get all the JSON info
    $json_array = checkJSON($json_body);
   
    if (!$json_array)
    {
        error_log('NOT JSON ARRAY');
        $app->response->setStatus(400);
        $app->response()->headers->set('Content-Type', 'application/json');
        $json_error = json_last_error();
        if (!$json_error)
        {
            $json_error = "Not valid request";
        }
        else
        {
            error_log($json_error);
            echo json_encode($json_error);    
            $app->stop();
        }        
    }
    
    //we get the information depending on the integration type    
    $all_data = get_JSON_data($integration_result['type'], $json_array);
            
    if (!$all_data)
    {           
        error_log("No valid data on JSON: " . $integration_result['type']);
        returnResponse(400, "Bad Request", "No valid data on JSON");        
    }            
    
    //depending on the integration type we update the database on the correct
    //way
    if ($integration_result['type'] == 'pagerduty')
    {        
        $update_result = $store->bulkUpdate($integration_name, $all_data, false);
    }
    elseif (($integration_result['type'] == 'newrelic'))
    {                    
        $update_result = $store->bulkUpdate($integration_name, $all_data);     
    }
    else
    {
        $update_result = $store->bulkUpdate($integration_name, $all_data);
    }    

    // if we have updated the database correctly
    if ($update_result)
    {        
        error_log('SAVED DATA!!');      
        $all_data = prettyPrintMongoObj($all_data);
        if ($integration_result['type'] == 'pagerduty')
        {
            foreach ($all_data as $only_doc) {
                //we log the data created to events_logs file for the daemon
                tempLogging($integration_name, $only_doc, $integration_result['type'], $app->config('config'));
            }               
        }
        else 
        {
            tempLogging($integration_name, $all_data, $integration_result['type'], $app->config('config'));
        }                    
        returnResponse(200, $all_data);        
    }
    else
    {        
        returnResponse(500, 
                'Internal Server Error', 'Something went wrong with DB');        
    }
    
 
});













$app->run();


















?>
