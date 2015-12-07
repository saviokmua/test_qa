<?php
ini_set("display_errors", "1");
ini_set("error_reporting", E_ALL & ~(defined('E_STRICT') ? E_STRICT : 0));
require '../vendor/autoload.php';
require '../config.php';

// Setup custom Twig view
$twigView = new \Slim\Views\Twig(array('debug' => true));
 
$app = new \Slim\Slim(array(
    'debug' => true,
    'view' => $twigView,
    'templates.path' => '../templates/',
));
$app->notFound(function () use ($app) {
    $app->render('404.html');
});

// Automatically load router files
$routers = glob('../routers/*.router.php');
foreach ($routers as $router) {
    require $router;
}

$app->run();

