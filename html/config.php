<?php
define('DBHOST', 'localhost');
define('DBUSER', 'cmx_info');
define('DBPW', '12345678');
define('DBNAME', 'cmx_info');

$db = new mysqli(DBHOST, DBUSER, DBPW, DBNAME);
$db -> query('SET NAMES utf8');
?>
