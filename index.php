<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// include database info
require_once('includes/config.php');
require_once('includes/safemysql.php');
require_once('includes/MovieDB.php');

$moviedb = new MovieDB ($_SERVER['REQUEST_METHOD'], $_GET['_URL']);
$moviedb->serve();

?>