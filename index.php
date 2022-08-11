<?php
date_default_timezone_set('Europe/Moscow');
header("Access-Control-Allow-Origin: *");
$_POST = json_decode(file_get_contents('php://input'), true);
$config = include('config.php');

require_once('classes/Db.php');
require_once('classes/Basic.php');
require_once('classes/RouterLite.php');
require_once('classes/controllers/Admin.php');
require_once('classes/controllers/API.php');
require_once('classes/controllers/Bot.php');
require_once('classes/controllers/Statistics.php');

RouterLite::addRoute('', 'Admin/main');
RouterLite::addRoute('/stats', 'Admin/stats');
RouterLite::addRoute('/bot', 'Bot/main');
RouterLite::addRoute('/getData', 'API/getData');
RouterLite::addRoute('/markRules', 'API/markRules');
RouterLite::addRoute('/sendData', 'API/sendData');
RouterLite::addRoute('/prize', 'Statistics/prize');
RouterLite::addRoute('/saveStats', 'Statistics/save');

RouterLite::addRoute('/test', 'API/test');
RouterLite::addRoute('/notFound', 'API/notFound');
RouterLite::dispatch();

?>