<?php

### Destroy the session, unset all the session variables and send the user back to main page ###
 session_start();
 require 'vendor/autoload.php';
 require_once('config.php');
 $logger = new Katzgrau\KLogger\Logger("$logdir");
 $logger->info("User {$_SESSION['cn']} logged out"); // Log that user is logged out
 unset($_SESSION);
 session_destroy();
 header("Location: /");

?>
