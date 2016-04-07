<?php

require 'vendor/autoload.php';

require 'db.php';

require 'config.php';

$app = new \Slim\Slim();

$app->config('debug', $debug_mode);

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



/**
 * This API is only to show the nice 404 page in case the user 
 * clicks on the integration
 */
$app->get('/webhooks/:integration_name/:apiKey',
        function($integration_name, $apiKey) {
    $app = \Slim\Slim::getInstance();
    
    $view = $app->view();
    $view->setTemplatesDirectory('.');
    $app->status(404);
    $app->render('404_custom.html');
    $app->stop();

});





/**
 * This API is to get the integrations webhook data from db and
 * search in JSON with search_term
 * type parameter could be Pagerduty or Custom
 */

$app->post(
    '/webhooks/integrations/:integration_name/search', 
        function($integration_name) 
{
    // JSON structure
    //    {
    //        "search": "this is a search string",
    //        "type": "pagerduty"
    //        "start_date": "2012-11-02T08:40:12.569Z",
    //        "end_date": "2012-11-02T08:40:12.569Z",
    //    }

    //we disabled ip authorization
    /*
    if (!ipAuthorization())
    {
        returnResponse(401, "Not Authorized", "IP blacklisted");
    }
    */
    
    $app = \Slim\Slim::getInstance();        
    $json_body = $app->request()->getBody();        
    $json_array = checkJSON($json_body);            

    // if for some reason we don't get real data we send 400
    if (!$json_array)
    {
        $app->response->setStatus(400);
        $app->response()->headers->set('Content-Type', 'application/json');
        $json_error = json_last_error();
        if (!$json_error)
        {
            $json_error = "Not valid request!";
        }
        else
        {
            error_log($json_error);
            echo json_encode($json_error);    
            $app->stop();
        }        
    }    
    
    // validation functions for all the JSON
    $validation_rules = array(
        'search' => 'is_Empty',
        'type' => 'required|contains,pagerduty custom newrelic',
        "start_date"=> "mongodate_OR_empty",
        "end_date"=> "mongodate_OR_empty",
    );
    
    $validation_result = validatePOST($json_array, $validation_rules);
    
    if ($validation_result !== true)
    {
        returnResponse(400, "Not valid Request", $validation_result);
    }
    
    //we get the parameters
    $search = array_key_exists('search', $json_array) ? $json_array['search'] : '';
    $start_date = array_key_exists('start_date', $json_array) ? $json_array['start_date'] : false;
    $end_date = array_key_exists('end_date', $json_array) ? $json_array['end_date'] : false;
                
    $store = new DB($app->config('config'));    
    $collection_exists = $store->checkCollectionExists($integration_name);            

    //if there is not such integration we send 404
    if (!$collection_exists)
    {           
        returnResponse(404, "Not Found", "Integration name doesn't exists");        
    }
    $search_criteria = array();
    
    //if there is search term we use it for the search
    if ($search)
    {        
        $search_criteria = array(
                '$text' => array('$search' => $search));        
    }
    
    //we get the search field depending on integration type
    $date_key = getSearchField($json_array['type'], $store, $integration_name);

    error_log('SEARCH FIELD: ' . $date_key);

    $date_criteria = array();
    
    if ($date_key)
    {        
        if ($start_date && $end_date)
        {         
            $startOfDay = $start_date;        
            $endOfDay = $end_date;        
            $date_criteria = array(
                $date_key => array('$gt' => $startOfDay, '$lte' => $endOfDay)
            );        
        }
        elseif ($start_date && !$end_date)
        {
            $startOfDay = new MongoDate(strtotime($start_date));
            $endOfDay = new MongoDate();
            $date_criteria = array(
                $date_key => array('$gt' => $startOfDay, '$lte' => $endOfDay)
            );
        }
        elseif (!$start_date && $end_date)
        {
            //we start with early date in case we don't have start_date
            $startOfDay = new MongoDate(strtotime("2012-07-08 11:14:15.638276"));
            $endOfDay = new MongoDate(strtotime($end_date));
            $date_criteria = array(
                $date_key => array('$gt' => $startOfDay, '$lte' => $endOfDay)
            );
        }
    }
        
    $cursor = array();
    //$compound_search = array_merge($search_criteria, $date_criteria);    

    $compound_search = $search_criteria;

    //var_dump($compound_search);

    //var_dump(array(date(DATE_ISO8601,$startOfDay->sec), date(DATE_ISO8601,$endOfDay->sec)));

    //start test
    //$start = new MongoDate(strtotime('2016-02-29T23:32:34+0000'));
    //$end = new MongoDate(strtotime('2016-03-31 23:59:59'));
    //$end = new MongoDate(strtotime("2016-02-29T23:32:40+0000"));

    // {"search":"","type":"custom","start_date":"2016-03-17T18:46:51.837Z","end_date":"2016-03-31T17:46:51.837Z"}

    // Now find documents with create_date between 1971 and 1999
    $cursor=$store->db->$integration_name->find(array($date_key => array('$gte'=> $startOfDay, '$lte' => $endOfDay)));

    //var_dump($cursor);

    //var_dump($cursor->explain());

    //end test

    //$cursor=$store->db->$integration_name->find($compound_search);

    $all_data = array();
    foreach ($cursor as $doc) {        
        $all_data[] = $doc;        
    }

    //we convert it to human JSON to the response
    $all_data = prettyPrintMongoObj($all_data);
    
    
    returnResponse(200, $all_data);
    

});





/**
 * This API is to create new integrations
 *
 */
$app->post('/integration/:method', function($method) {
    
    /*
     * {
            "integration_name": "Odesk Pagerduty",
            "created_by" : "udhokale",
            "type": "pagerduty"
        }
     */
    //custom integration added, it's going to be saved to db as is
    /*
     * {
            "integration_name": "Odesk Pagerduty",
            "created_by" : "udhokale",
            "type": "custom"
        }
     * }
     */

    /*
    if (!ipAuthorization())
    {
        returnResponse(401, "Not Authorized", "IP blacklisted");
    }
    */    

    if ($method !== 'add' && $method !== 'remove')
    {
        returnResponse(404, "Not found", "Not valid method!");
    }
    
    $app = \Slim\Slim::getInstance();
    $json_body = $app->request()->getBody();
    $json_array = checkJSON($json_body);    
              
    if (!$json_array)
    {
        returnResponse(400, "Not valid Request", "Bad JSON");
    }
    
    if ($method == 'remove')
    {
        $validation_rules = array(
            'integration_name' => 'required|max_len,60'
        );
    }
    else // else we are adding a new integration
    {
        $validation_rules = array(
            'integration_name' => 'required|max_len,60|not_Valid_chars',
            'created_by' => 'required|max_len,30',
            'type' => 'required|contains, pagerduty custom newrelic'
        );
    }    
    
    
    $validation_result = validatePOST($json_array, $validation_rules);
    
    if ($validation_result !== true)
    {
        returnResponse(400, "Not valid Request", $validation_result);
    }
        
    $integration_name = $json_array['integration_name'];
    
    //INTERNAL_ integrations are forbidden because we use it for integrations 
    //database    
    if (preg_match('/^INTERNAL_/', $integration_name) === 1) 
    {
        returnResponse(400, "Not valid Request", "INTERNAL_ name not valid!");
    }
    
    $store = new DB($app->config('config'));
        
    if ($method == 'remove')
    {                
        if (!$store->checkCollectionExists($integration_name))
        {
            returnResponse(404, "Unprocessable Entity", "Integration name does not exists");
        }

        //we delete the integrations collection
        $delete_result = $store->db->$integration_name->drop();
        $integrationsColl = $app->config('config')['integrationsColl'];

        //and we remove from the INTERNAL integration
        $internal_delete_res = $store->db->$integrationsColl->remove(
                array('integration_name' => $integration_name)
        );

        if (!$delete_result || !$internal_delete_res)
        {
            returnResponse(500, "Internal Server Error", "Database issue");
        }

        returnResponse(200, $internal_delete_res);
    }    
    
    
    //if we are here the method is add
    if ($store->checkCollectionExists($integration_name))
    {
        returnResponse(422, "Unprocessable Entity", "Integration name already exists");
    }    
        
    $integrationsColl = $app->config('config')['integrationsColl'];
    $internal_integration = $store->getCollectionNew($integrationsColl);
    $uniqueKey = getUniqueKey($internal_integration);
    $mongo_now = new MongoDate();


    $document = array(
        'integration_name' => $integration_name,
        'user' => $json_array['created_by'],
        'apiKey' => $uniqueKey,
        'createdOn' => $mongo_now,
        'type' => $json_array['type']
    );       

    //we create the integration
    $new_collection = $store->db->createCollection($integration_name);
    $result = $internal_integration->insert($document);    

    if (!$result)
    {
        returnResponse(500, "Internal Server Error", "Problems with DB");
    }
    
    $document = prettyPrintMongoObj($document);

    returnResponse(200, $document);
        
                


});





/**
 * This API is to get all the events in the integration
 */
$app->get('/integration(/:integration_name)', function($integration_name='') {

    /*
    if (!ipAuthorization())
    {
        returnResponse(401, "Not Authorized", "IP blacklisted");
    }
    */
    
    $app = \Slim\Slim::getInstance();    
    if (preg_match('/^INTERNAL_/', $integration_name) === 1) 
    {
        returnResponse(400, "Not valid Request", "INTERNAL_ name not valid!");
    }
    
    $store = new DB($app->config('config'));

    if ($integration_name == '')
    {
        $all_valid_integrations = getAllValidIntegrations($store);
        returnResponse(200, $all_valid_integrations);        
    }
    
    if (!$store->checkCollectionExists($integration_name))
    {
        returnResponse(404, "Not found", "Integration name not exists");
    }
    
    $cursor = $store->db->$integration_name->find();
    $all_documents = array();
            
    foreach ($cursor as $doc) 
    {
        $all_documents[] = $doc;               
    }
    
    $all_documents = prettyPrintMongoObj($all_documents);
    $integration_result = getIntegrationData($store, $integration_name);
    $customized_output = array();
    
    if ($integration_result['type'] == 'pagerduty')
    {
        $customized_output = customizeOutputPagerduty($all_documents);
    }
    elseif ($integration_result['type'] == 'custom')
    {
        $customized_output = customizeOutputCustom($all_documents);
    }
    elseif ($integration_result['type'] == 'newrelic')
    {
        //$customized_output = customizeOutputNewRelic($all_documents);
    }
    
    if (!$customized_output)
    {
        returnResponse(200, $all_documents);
    }

    returnResponse(200, $customized_output);
    
});











/**
 * This API is to get all the Integrations from INTERNAL
 */
$app->get('/integrations/getAll', function() {

    /*
    if (!ipAuthorization())
    {
        returnResponse(401, "Not Authorized", "IP blacklisted");
    }
    */
    
    $app = \Slim\Slim::getInstance();    
    $store = new DB($app->config('config'));
    $integrationsColl = $app->config('config')['integrationsColl'];
    $internal_integration = $store->getCollectionNew($integrationsColl);    
    $integration_result = $internal_integration->find();
    $all_data = array();
    
    foreach ($integration_result as $data) 
    {
        $all_data[] = $data;
    }    
    $all_data = prettyPrintMongoObj($all_data);
            
    returnResponse(200, $all_data);

});


/**
 * This is the common API where send common events 
 */
$app->post('/webhook/create_event', function() {
    /*
        {   
            "username": "udhokale",
            "event": "Making change to LB",
            "start_date": "2015-09-10T22:23:44Z"
        }
     * 
     */

    /*
    if (!ipAuthorization())
    {
        returnResponse(401, "Not Authorized", "IP blacklisted");
    }
    */
            
    $app = \Slim\Slim::getInstance();        
    
    $json_body = $app->request()->getBody();

    $json_array = checkJSON($json_body);
    
    if (!$json_array)
    {
        returnResponse(400, "Not valid Request", "Bad JSON");
    }
    
    
    $validation_rules = array(  
        'username' => 'required|max_len,60|min_len,2|alpha_numeric',
        'event' => 'required',
        'start_date' => 'required|date'
    );
    
    $validation_result = validatePOST($json_array, $validation_rules);
    
    if ($validation_result !== true)
    {
        returnResponse(400, "Not valid Request", $validation_result);
    }
        
    $store = new DB($app->config('config'));
    
    $eventsColl = $app->config('config')['eventsColl'];
        
    $events_store = $store->getCollectionNew($eventsColl);
    
    $result_save = $events_store->save($json_array);
    
    if ($result_save)
    {
        returnResponse(200, $result_save);
    }
    
    returnResponse(500, "Internal Server Error", "Problems with DB");
            
    
    
    
    

});














$app->run();


















?>
