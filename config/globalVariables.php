<?php

define('BASEPATH', 'http://localhost/rex');
//define('BASEPATH', 'https://trex.aquila-tech.in');
define('INDEX', BASEPATH . '/index.php');
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/rex');
define('GLOBAL_PATH', BASEPATH . '/global');
define('FILES', GLOBAL_PATH . '/files');
define('PACKAGES', BASEPATH . '/packages');
define('MODULES', BASEPATH . '/modules');
define('TIMEZONE', 'Asia/Kolkata');
define('COUNTRY', 'India');
define('COUNTRY_CODE', 'IN');
define('STATE', 'Pondicherry');
define('LANG', 'EN');
define('CURRENCY', 'INR');
define('CURRENCY_SYMBOL', '&#8377;'); // Indian Rupee symbol
define("COOKIE_TIME_OUT", 10); //specify cookie timeout in days (default is 10 days)

date_default_timezone_set(TIMEZONE);

//application hosted date
define('HOSTED_DATE', '2021-01-01');
//date format
define('DATE_FORMAT', 'd-m-Y');
//time format
define('TIME_FORMAT', 'h:i:s A');
//date time format
define('DATETIME_FORMAT', 'd-m-Y h:i:s A');

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 0;
$employee_name = isset($_SESSION['employee_name']) ? $_SESSION['employee_name'] : '';
$login_id = isset($_SESSION['login_id']) ? $_SESSION['login_id'] : 0;
