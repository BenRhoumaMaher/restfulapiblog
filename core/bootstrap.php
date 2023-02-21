<?php
// DB class responsible for DB related stuff
require __DIR__.'/DB.php';
// Router class responsible for directing the URL to proper files
require __DIR__.'/Router.php';
// tell wich file to serve in case of wich URL
require __DIR__.'/../routes.php';
// this is contain the application configurations such as DB settingg
require __DIR__ .'/../config.php';
// create new object of the class Router
$router = new Router;
// use the setRoutes method of the class Router on the object with $routes as a parametre
$router->setRoutes($routes);
// this is contains the URL path after the host name
$url = $_SERVER['REQUEST_URI'];
require __DIR__."/../api/".$router->getFilename($url);