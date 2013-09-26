<?php
$domain = "tinyfx";
$base_url = "http://localhost/tinyfx/";
$base_url_public = "http://localhost/tinyfx/public/";
$public_files_root = "./public/";

$minify_html = false;
$compress_html = true;

//$display_errors = false;
//$record_errors = false;

//$disallow_hotlinking = false;

//$minify_icluded_js = false;
//$minify_icluded_css = false;

date_default_timezone_set("Asia/Colombo");

//error_reporting(E_ERROR);

// Database settings
define ("C_DB_HOST", "localhost");// Hostname of MySQL
define ("C_DB_NAME", "database");// Database name
define ("C_DB_USER", "user");// User
define ("C_DB_PASS", "password");// Password
define ("C_DB_TYPE", "mysql");
define ("NEO_DB_DEBUG", true);

$preload = array("database");

?>
