<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require 'config.php';
require 'controllers/user.php';

function my_autoloader($className) {
    $arr = preg_split('/(?=[A-Z])/', $className);
    $arr = array_slice($arr, 1);
    $path = false;
    $path = "models/" . $className . ".php";
    if (file_exists($path)) {
        require_once $path;
    }
}
spl_autoload_register("my_autoloader");

$app = new \Slim\App();


/*USER AUTHENTICATION APIS*/
$app->post('/register', 'UserAuthenticationController::register');
$app->get('/verify-email/{bluffCode}/{token}','UserAuthenticationController::verifyEmail');
$app->post('/login', 'UserAuthenticationController::login');
$app->get('/validate/{token}', 'UserAuthenticationController::validate');
$app->get('/logout/{email}', 'UserAuthenticationController::logout');
$app->get('/resetPassword/{email}', 'UserAuthenticationController::resetPassword');
$app->get('/verify-reset-password/{bluffCode}/{token}','UserAuthenticationController::verifyResetEmail');
$app->post('/changePassword', 'UserAuthenticationController::changePassword');
$app->post('/updatePassword', 'UserAuthenticationController::updatePassword');

$app->run();

?>