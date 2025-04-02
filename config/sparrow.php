<?php

/*

* SPARROW - A PHP Framework For Web Artisans

*/

header("Access-Control-Allow-Origin: *");
//start session
session_start();

//error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// error log file
//ini_set('error_log', 'https://trex.aquila-tech.in/error_log.txt');
//ini_set('error_log', 'https://trex.aquila-tech.in/error_log.txt');



//include sqLfunctions.php
require_once 'sqlFunctions.php';

//include globalVariables.php
require_once 'globalVariables.php';

//include globalFunctions.php
require_once 'globalFunctions.php';



$currentLocation = getCurrentLocation();

$getRewrittenUrl = getRewrittenUrl();
if ($getRewrittenUrl !== "Login" && $getRewrittenUrl !== "Rex") {
    checkLogin($user_id, $login_id);
}
