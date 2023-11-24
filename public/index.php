<?php 

define("ROOT_PATH", dirname(__DIR__));

require_once ROOT_PATH.'/vendor/autoload.php';
use App\Core\App;
use App\Controllers;



$app = new App();

$app->router->get('/', function(){
    $homeController = new Controllers\HomeController();
    $homeController->index();   
});
$app->router->post('/', function(){
    $homeController = new Controllers\HomeController();
    $homeController->index();   
});

$app->router->post('/upload', function(){
  
    $homeController = new Controllers\HomeController();
    $homeController->upload();
    
});


$app->run();