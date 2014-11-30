<?php

error_reporting(0);
session_start();
mb_internal_encoding("UTF-8");

/*------ Configuration start ------*/

$host = 'http://algoprog.com/cityreport';
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'cityreport';
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', '1qw23e');
define('PATH',$_SERVER['DOCUMENT_ROOT'].'/cityreport');
date_default_timezone_set("Europe/Athens");

/*------ Configuration end ------*/

mysql_connect($db_host, $db_user, $db_pass) or die('MySQL connection error.');
mysql_select_db($db_name);
mysql_query("SET names 'utf8'");

?>