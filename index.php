<?php
header("Content-type:text/html;charset=utf-8");
ini_set('display_errors', 1);
error_reporting(E_ALL);

define("APP_PATH",realpath(dirname(__FILE__))); 
define("VIEW_PATH",APP_PATH."/application/views");
define("WEB_NAME",'推淘网');
define("ADMIN_USER", 'admin');

$app = new Yaf_Application(APP_PATH."/conf/init.ini","product");
$app->bootstrap()->run();
?>