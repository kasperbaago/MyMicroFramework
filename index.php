<?php
/*
 * :: DEBUG MODE - UNCOMMENT IF NTO DEBUGGING!
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('ABS_PATH', __DIR__);

require 'vendor/autoload.php';
$app = new \app\Application();
$app->init();
?>
