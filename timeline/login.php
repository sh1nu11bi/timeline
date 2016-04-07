<?php
### This file authenticates the user against LDAP ####
	### Start the session ###
	session_start();
	require 'vendor/autoload.php';
	require_once('config.php');
  	$logger = new Katzgrau\KLogger\Logger("$logdir");

	if(($_SERVER['REQUEST_METHOD'] != 'POST') && (!$_SESSION['user_authenticated'] || !isset($_SESSION['user_authenticated']))) {
	$_SESSION['user_authenticated'] = false;
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {


		#### Verify the credentials ###		
		$ds = ldap_connect($ldap_auth_config['ldap_server']);		
		$dn = sprintf($ldap_auth_config['dn'], $_POST['username']);				
		ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
		$login = ldap_bind($ds, $dn, $_POST['password']);

		### If authenticated grab the User Information from LDAP and set session variables ###
		if ($login) {			
			$ds = ldap_connect($ldap_auth_config['ldap_server']);			
			$dn = sprintf($ldap_auth_config['dn'], $_POST['username']);			
			$filter = '(objectclass=*)';
			$attributes = array('cn', 'displayName', 'mail', 'skypeId', 'mobile', 'odeskAccountActive', 'odeskVpnGroup', 'givenname');
			$sr=ldap_read($ds, $dn, $filter, $attributes);
			$user = ldap_get_entries($ds, $sr);
			ldap_close($ds);
			$_SESSION['cn'] = $user[0]["cn"][0];
			$_SESSION['displayName'] = $user[0]["displayname"][0];
			$_SESSION['mail'] = $user[0]["mail"][0];
			$_SESSION['skypeId'] = $user[0]["skypeid"][0];
			$_SESSION['mobile'] = $user[0]["mobile"][0];
			$_SESSION['odeskAccountActive'] = $user[0]["odeskaccountactive"][0];
			$_SESSION['odeskVpnGroup'] = $user[0]["odeskvpngroup"][0];
			$_SESSION['givenname'] = $user[0]["givenname"][0];

			if($_SESSION['odeskAccountActive']) { // make sure the account is active
				$logger->info("User {$_POST['username']} logged in"); // Log that user is logged in
				$_SESSION['user_authenticated'] = true; // Set the user as authenticated
			}

			### Set the apache note ###
			apache_note('username', $_SESSION['cn']);

			### Add the session cookies. Session timeout set to 8 hours * 60 min * 60 sec = 288000 ###
			setcookie(session_name('account_manager'),session_id(),time()+28800, '/'); 

		}
		else {
			### Currently not needed but kept for future use ###
		}

	}		
	
?>
