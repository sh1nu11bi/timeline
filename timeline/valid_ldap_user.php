<?php
### Checks to see if the User is a valid LDAP User ###
#header('Content-type: application/json');
session_start();
require_once('config.php');

### Set the apache note ###
// apache_note('username', $_SESSION['cn']);

$valid = true;
$message = '' ;


### Open a connection to LDAP  and Search the User ###
    if (isset($_POST['username']) || isset($_POST['uidmanager'])) {
        $cn = isset($_POST['username']) ? $_POST['username'] : $_POST['uidmanager'] ;
        $ds = ldap_connect("ldap.odesk.com");
        $dn = 'ou=People,dc=odesk,dc=com';
        $filter = "cn={$cn}";
        $attributes = array('givenname');
        $sr=ldap_search($ds, $dn, $filter, $attributes);
        $count = ldap_count_entries($ds, $sr);
        if ($count == 1) { $valid = true; } else { $valid = false; $message = 'Invalid LDAP User'; }
    }
        

### If calling this script for validate the manager UID get the department for the user as well ####
    if (isset($_POST['uidmanager'])) {
        $ds = ldap_connect("ldap.odesk.com");
        $dn = "cn={$_POST['uidmanager']},ou=People,dc=odesk,dc=com";
        $filter = '(objectclass=*)';
        $attributes = array('departmentNumber');
        $sr=ldap_read($ds, $dn, $filter, $attributes);
        $user = ldap_get_entries($ds, $sr);
        ldap_close($ds);
        $_SESSION['managerdepartment'] = $user[0]["departmentnumber"][0];
        }    

echo json_encode(
    $valid ? array('valid' => $valid) : array('valid' => $valid, 'message' => $message)
);
