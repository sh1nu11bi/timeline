<?php

$debug_mode = false;

$config = array(
	'username' => '',
	'password' => '',
    'dbname' => 'timelinedb',
	//'cn' 	   => sprintf('mongodb://%s:%d/%s', $hosts, $port,$database),
	'connection_string'=> sprintf('mongodb://%s:%d/','127.0.0.1','27017'),
    'integrationsColl' => 'INTERNAL_integrations',
    'eventsColl' => 'INTERNAL_events',
    'events_logs_path' => '/var/www/html/timeline/event_logs/'

);

