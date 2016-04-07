<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require('vendor/autoload.php');

require 'config.php';

require 'db.php';

function str_replace_first($search, $replace, $subject) 
{
    return implode($replace, explode($search, $subject, 2));
}



class IntegrationTest extends PHPUnit_Framework_TestCase
{
    protected $client;
    
    public $number_tests = 1;
    
    public $number_docs = 1000;
    
    //$time_delta_docs is the time difference between docs in seconds
    // 86400 is one day
    public $time_delta_docs = 86400;
    
    public $test_spacename = 'newrelic_demo_';    

    public $api_url = 'http://172.27.85.169/api';

    public $external_url = 'http://172.27.85.169/api';
    
    public $integration_path = '/integration/';
    
    public $all_searchs = array();
    
    public $all_users = array('udhokale', 'josegarcia', 'gretkowsky', 'kvenable', 'jguies');
    
    public $current_user = '';
    
    public $next_user = '';

    public $type_entries = array('OPEN', 'ACKNOWLEDGED', 'CLOSE');

    public $pause_documents = 0.1;

    #public $random_time = true;
       
        
    //public $integration_results = array();

    protected function setUp()
    {
        $this->client = new GuzzleHttp\Client(); 
//        $this->client = new GuzzleHttp\Client([
//            'base_url' => 'http://localhost',
//            'defaults' => ['exceptions' => false]
//        ]);        
        $this->json_schema = '{
    "owner": "Jose Garcia",
    "severity": "INFO",
    "policy_url": "https://alerts.newrelic.com/accounts/146182/policies/0",
    "current_state": "OPEN",
    "policy_name": "New Relic Alert - Test Policy",
    "incident_url": "https://alerts.newrelic.com/accounts/146182/incidents/0",
    "incident_acknowledge_url": "https://alerts.newrelic.com/accounts/146182/incidents/0/acknowledge",
    "targets": [{
        "id": "12345",
        "name": "Test Target",
        "link": "http://localhost/sample/callback/link/12345",
        "labels": {
            "label": "value"
        },
        "product": "TESTING",
        "type": "test"
    }],
    "version": "1.0",
    "condition_id": 0,
    "account_id": 146182,
    "incident_id": 0,
    "event_type": "NOTIFICATION",
    "runbook_url": "http://localhost/runbook/url",
    "account_name": "Upwork Global Inc.",
    "details": "New Relic Alert - Channel Test",
    "condition_name": "New Relic Alert - Test Condition",
    "timestamp": 1457633198874
    }';

    }

    
    public function deleteTests()
    //public function test_deleteTests()
    {
        $config = array(
            'username' => '',
            'password' => '',
            'dbname' => 'timelinedb',
            //'cn' 	   => sprintf('mongodb://%s:%d/%s', $hosts, $port,$database),
            'connection_string'=> sprintf('mongodb://%s:%d/','127.0.0.1','27017'),
            'integrationsColl' => 'INTERNAL_integrations'
        );
        
        $store = new DB($config);
        //($store);
        $collections = $store->db->getCollectionNames();        
        foreach ($collections as $coll) 
        {
            if (strpos( $coll, 'phpunit_newrelic_') === 0)
            {                
                //$store->db->$coll->drop();
                $delete_url = $this->api_url . $this->integration_path . 'remove';

                
                var_dump($delete_url);                
                $post_body = array(
                    'integration_name' => $coll
                );

                var_dump($post_body);
                $response = $this->client->post($delete_url, ['json' => $post_body]);                
                $this->assertEquals(200, $response->getStatusCode());
                echo "$coll  deleted!\n";
            }
        }
    }
    
        
    
    
    
    public function test_integrations()
    //public function integrations()
    {
        
        for ($x=0; $x<$this->number_tests; $x++)
        {            
            $unique_id = uniqid();
            var_dump('UNIQID: ' . $unique_id);
            $integration_name = $this->test_spacename . $unique_id;                        
            $url = $this->api_url . $this->integration_path . 'add';

            //var_dump($url);

            $username = 'phpunit_user_' . $unique_id;
            $post_body = array(
                'integration_name' => $integration_name,
                'created_by'=> $username, 
                'type' => 'newrelic'
            );

            echo (json_encode($post_body, true));     	    

            $response = $this->client->post($url, [
                'json' => $post_body
            ]);

            var_dump($response->getBody());

            $this->assertEquals(200, $response->getStatusCode());

            $body = $response->getBody();

            $body_data = json_decode($body, true);

            var_dump($body_data);
            
            $apiKey = $body_data['apiKey'];

            sleep(1);
            
            $this->generate_docs($apiKey, $username, $unique_id, $integration_name);
            
            //$this->searches($integration_name);
            
            $this->all_unique_searchs = array();
                        
            
        }  
            
    }
    
    public function generate_docs($apiKey, $username, $unique_id, $integration_name, $custom_url='')
    {  
        $webhook_ulr = $this->external_url . "/webhooks/$integration_name/$apiKey";
        
        var_dump($webhook_ulr);
                        
        for ($x=0; $x<$this->number_docs; $x++ )
        {
            $search_data = array();
            
            $search_data['common_id'] = $unique_id;
            
            $unique_search_string = $unique_id . "NTEST_$x";
            
            $search_data['search_string'] = $unique_search_string;
            
            $str_test = "PHPUNITTESTING: $unique_id NTEST_$x";

            $modified_schema = str_replace('New Relic Alert - Test Policy', $str_test, $this->json_schema );

            $nuevo_unique_id = uniqid();

            $modified_schema = str_replace('"incident_id": 0', '"incident_id": ' . '"' . $nuevo_unique_id . '"'   , $modified_schema );

            $rstate = $this->get_random_state();

            $modified_schema = str_replace('OPEN', $rstate, $modified_schema );

            $random_day = rand(1, 30) + $x;

            $random_hours = rand(1, 24) + $x;

            $random_seconds = rand(1, 60) + $x;
            
            //$new_date = date(DATE_ISO8601, strtotime("-$random_time day -$random_time hours -$random_time seconds"));

            //$new_date = date(DATE_ISO8601, strtotime("-$random_day day -$random_hours hours -$random_seconds seconds"));

            $custom_hour = $x + 2;

            $new_date = date(DATE_ISO8601, strtotime("+$custom_hour hours "));

            $new_date = strtotime($new_date);

            // if ($x%2 == 0)
            // {
            //     $new_date = date(DATE_ISO8601, strtotime("+$x hours "));    
            // } else {
            //     $new_date = date(DATE_ISO8601, strtotime("-$x hours "));    
            // }


            

            //var_dump('NEW DATE');
            //var_dump($new_date);
                                    
            $search_data['date'] = $new_date;
            
            $modified_schema = str_replace("1457633198874", $new_date, $modified_schema);
            
            $this->all_unique_searchs[] = $search_data;

            error_log('modified_schema');
            error_log($modified_schema);
            
            $testing_array = json_decode($modified_schema, true);           
            
	       
	
            if ($custom_url)
            {
                $incident = $testing_array['messages'][0]['data']['incident'];
        
                $data_needed = array(
                    "username"=>  $this->get_next_user(),
                    "event" => "Making change to " . $unique_id,
                    "start_date" => $new_date
                );

                $testing_array = array_merge($incident, $data_needed);
                //$testing_array = $this->$custom_function($testing_array, $unique_id, $new_date);
                                
            }
            
            var_dump($webhook_ulr);
            echo(json_encode($this->json_schema));

            $response_webhook = $this->client->post($webhook_ulr, [
                    'body' => $modified_schema
                ]);
            
            $hook_body = $response_webhook->getBody();
            
            var_dump('HOOKBODY:');
            echo $hook_body;

            $hook_response_data = json_decode($hook_body, true);

            $this->assertEquals(200, $response_webhook->getStatusCode());

            if ($this->pause_documents)
            {
                sleep($this->pause_documents);  
            } 

	        var_dump('STATUS', $response_webhook->getStatusCode());

            

            //var_dump($hook_response_data);
                        
        }
                
    }
    
    public function searches($integration_name)
    {
        
        $counter = 1;
            
        foreach ($this->all_unique_searchs as $search_data)
        {            
            
            $search_api_url = $this->api_url . "/webhooks/integrations/$integration_name/search";
            
            var_dump($search_api_url);
            
            $valid_search_post = array(
                "search" => $search_data['common_id'],
                "type" => "pagerduty",
                "start_date" => $search_data['date'],
                "end_date" => date(DATE_ISO8601)
            );
            
            var_dump(json_encode($valid_search_post));
            
            $response_search = $this->client->post($search_api_url, [
                'json' => $valid_search_post
            ]);
            
            $body_search = json_decode($response_search->getBody(), true);
            
            //var_dump($body_search);
            
            $search_results = count($body_search);
            
            var_dump($search_results);
            
            
            $calculated_result = (2 * $counter);
            
            var_dump('CALCULATED: '. $calculated_result  . " Real:  $search_results");
            
            $counter++;
                                    
            //$this->assertEquals($calculated_result, $search_results);
            
            //$this->assertNotEmpty($body_search);

        }
        
        
                
        
            
    }
        
    public function common_events()                                                        
    //public function test_common_events()    
    {
	 /* this is necessary to create the common integration
         //  POST http://localhost/api/integration/add
         * {
                "integration_name": "common_integration",
                "created_by": "GOD",
                "type": "custom"
           }
         * 
         * result:
            {
                "integration_name": "common_integration",
                "user": "GOD",
                "apiKey": "S07795bVYm4rT058kft50Rhq822a8BF9446zAV2103814dcsfg",
                "createdOn": "2015-10-02T00:22:26+0200",
                "type": "custom",
                "_id": "560db2227bcfb6e4238b4573"
            }

         * 
         * Common integrations must have 
         */
        
        $integration_name = 'common_integration';
        
        //$apiKey = 'S07795bVYm4rT058kft50Rhq822a8BF9446zAV2103814dcsfg';
	    $apiKey = 'e5CIK9Xgx9n9PnH3G192ueR426oxf31u7f48L0xaG16iSCt9W4';
        
        $webhook_ulr =  "$this->api_url/webhooks/$integration_name/$apiKey";
        
        $unique_id = uniqid();
        
        $this->generate_docs($apiKey, 'GOD', $unique_id , $integration_name, $webhook_ulr);
        
        $this->searches($integration_name);
        
        
        
        
    }

        /*
        $this->assertArrayHasKey('bookId', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('author', $data);
        $this->assertEquals(42, $data['price']);
         * 
         */    
    
    
    function get_next_user()
    {
        $current_user = array_shift($this->all_users);
        $this->all_users[] = $current_user;
        return $current_user;
    }


    function get_random_state()
    {
        $rand_key = array_rand($this->type_entries);
        $rand_value = $this->type_entries[$rand_key];
        return ($rand_value);
    }
    
    
    
}

























//$m = new Mongo();
//
//$db = $m->learningmongo;
//
//$people = $db->people;
//
//$people->insert(array('name'=> 'Pepe', 'trabajo' => 'recogedor de cartones'));
//
//$cursor = $people->find();
//
//
//if ($cursor->count() > 0)
//{
//    foreach ($cursor as $doc)
//    {
//        echo $doc['name'];
//    }
//}
