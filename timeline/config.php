<?php

### Master configuration file for the ldap manager project ###
$logdir = '/var/log/apache2/';

$debug_mode = false;

$ldap_auth_config = array(
	'ldap_server' => '', 
	'dn' => "" //cn=%s,ou=People,dc=yourcompany,dc=com
);

$config_data = array(
	'bug_report' => 'yourdev@yourhost',
	'company_logo' => 'images/upwork_logo_light.png',
	'contact_mail' => 'yourcontactmail@yourhost',
);



?>
