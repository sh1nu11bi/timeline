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
    
    public $test_spacename = 'phpunit_testing_';
    
    public $api_url = 'http://localhost/api';
    
    public $integration_path = '/integration/';
    
    public $all_searchs = array();
    
    public $all_users = array('udhokale', 'josegarcia', 'gretkowsky', 'kvenable', 'jguies');
    
    public $current_user = '';
    
    public $next_user = '';
       
        
    //public $integration_results = array();

    protected function setUp()
    {
        $this->client = new GuzzleHttp\Client(); 
//        $this->client = new GuzzleHttp\Client([
//            'base_url' => 'http://localhost',
//            'defaults' => ['exceptions' => false]
//        ]);
        $this->json_schema = '{
  "messages": [
    {
      "id": "bb8b8fe0-e8d5-11e2-9c1e-22000afd16cf",
      "created_on": "2013-07-09T20:25:44Z",
      "type": "incident.trigger",
      "data": {
        "incident": {
          "id": "PIJ90N7",
          "incident_number": 1,
          "created_on": "2013-07-09T20:25:44Z",
          "status": "triggered",
          "html_url": "https://acme.pagerduty.com/incidents/PIJ90N7",
          "incident_key": "PHPUNITTESTING",
          "service": {
            "id": "PBAZLIU",
            "name": "service",
            "html_url": "https://acme.pagerduty.com/services/PBAZLIU"
          },
          "assigned_to_user": {
            "id": "PPI9KUT",
            "name": "Alan Kay",
            "email": "alan@pagerduty.com",
            "html_url": "https://acme.pagerduty.com/users/PPI9KUT"
          },
          "trigger_summary_data": {
            "subject": "45645"
          },
          "trigger_details_html_url": "https://acme.pagerduty.com/incidents/PIJ90N7/log_entries/PIJ90N7",
          "last_status_change_on": "2013-07-09T20:25:44Z",
          "last_status_change_by": "null"
        }
      }
    },
    {
      "id": "8a1d6420-e9c4-11e2-b33e-f23c91699516",
      "created_on": "2013-07-09T20:25:45Z",
      "type": "incident.resolve",
      "data": {
        "incident": {
          "id": "PIJ90N7",
          "incident_number": 1,
          "created_on": "2013-07-09T20:25:44Z",
          "status": "resolved",
          "html_url": "https://acme.pagerduty.com/incidents/PIJ90N7",
          "incident_key": "null",
          "service": {
            "id": "PBAZLIU",
            "name": "service",
            "html_url": "https://acme.pagerduty.com/services/PBAZLIU"
          },
          "assigned_to_user": "null",
          "resolved_by_user": {
            "id": "PPI9KUT",
            "name": "Alan Kay",
            "email": "alan@pagerduty.com",
            "html_url": "https://acme.pagerduty.com/users/PPI9KUT"
          },
          "trigger_summary_data": {
            "subject": "45645"
          },
          "trigger_details_html_url": "https://acme.pagerduty.com/incidents/PIJ90N7/log_entries/PIJ90N7",
          "last_status_change_on": "2013-07-09T20:25:45Z",
          "last_status_change_by": {
            "id": "PPI9KUT",
            "name": "Alan Kay",
            "email": "alan@pagerduty.com",
            "html_url": "https://acme.pagerduty.com/users/PPI9KUT"
          }
        }
      }
    }
  ]
}';
    }
    
    //public function deleteTests()
    public function test_deleteTests()
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
            if (strpos( $coll, 'phpunit_testing_') === 0)
            {                
                $store->db->$coll->drop();
                echo "$coll  deleted!\n";
            }
        }
    }
    
        
    
    
    
    //public function test_integrations()
    public function integrations()
    {
        
        for ($x=0; $x<$this->number_tests; $x++)
        {
            $unique_id = uniqid();
            $integration_name = $this->test_spacename . $unique_id;                        
            $url = $this->api_url . $this->integration_path . 'add';

            //var_dump($url);

            $username = 'phpunit_user_' . $unique_id;
            $post_body = array(
                'integration_name' => $integration_name,
                'created_by'=> $username, 
                'type' => 'Pagerduty'
            );

            $response = $this->client->post($url, [
                'json' => $post_body
            ]);

            $this->assertEquals(200, $response->getStatusCode());

            $body = $response->getBody();

            $body_data = json_decode($body, true);

            //($body_data);
            
            $apiKey = $body_data['apiKey'];
            
            $this->generate_docs($apiKey, $username, $unique_id, $integration_name);
            
            $this->searches($integration_name);
            
            $this->all_unique_searchs = array();
                        
            
        }  
            
    }
    
    public function generate_docs($apiKey, $username, $unique_id, $integration_name, $custom_url='')
    {  
        $webhook_ulr = $this->api_url . "/webhooks/$integration_name/$apiKey";
        
        //var_dump($webhook_ulr);
                        
        for ($x=0; $x<$this->number_docs; $x++ )
        {
            $search_data = array();
            
            $search_data['common_id'] = $unique_id;
            
            $unique_search_string = $unique_id . "NTEST_$x";
            
            $search_data['search_string'] = $unique_search_string;
            
            $str_test = "PHPUNITTESTING: $unique_id NTEST_$x";

            $modified_schema = str_replace("Alan Kay", $str_test, $this->json_schema );
            
            $new_date = date(DATE_ISO8601, strtotime("-$x day"));
                                    
            $search_data['date'] = $new_date;
            
            $modified_schema = str_replace("2013-07-09T20:25:44Z", $new_date, $modified_schema);
                                    
            $modified_schema = str_replace_first('"id": "PIJ90N7"', '"id": "' . $unique_search_string . '"' , $modified_schema);
            
            $this->all_unique_searchs[] = $search_data;
                                    
            $modified_schema = str_replace_first('"id": "PIJ90N7"', '"id": "' . $unique_search_string . 'SECOND"', $modified_schema);                        
            
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
            
            var_dump($custom_url);
            echo(json_encode($testing_array));

            $response_webhook = $this->client->post($webhook_ulr, [
                    'json' => $testing_array
                ]);
            
            $hook_body = $response_webhook->getBody();
            
            var_dump('HOOKBODY:');
            echo $hook_body;

            $hook_response_data = json_decode($hook_body, true);

            $this->assertEquals(200, $response_webhook->getStatusCode());

            

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
                "type" => "Pagerduty",
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
            
            
            //$calculated_result = (2 * $counter);
            
            //var_dump('CALCULATED: '. $calculated_result  . " Real:  $search_results");
            
            $counter++;
                                    
            //$this->assertEquals($calculated_result, $search_results);
            
            //$this->assertNotEmpty($body_search);

        }
        
        
                
        
            
    }
        
    //public function common_events()                                                        
    public function test_common_events()    
    {
        /*Common integrations is already created:
         *  POST http://localhost/api/integration/add
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
