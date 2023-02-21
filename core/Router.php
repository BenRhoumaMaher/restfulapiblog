<?php
class Router {
private $routes = [];
 // the first method is taking an array of routes and storing it
 function setRoutes(Array $routes) {
 $this->routes = $routes;
 }
 // the second method is responsible for deciding which file to serve against which URL
 // we are using strpos() that checks if the string in $route exists in $url and, if it exists, it
 // returns the appropriate filename.
 function getFilename(string $url) {
 foreach($this->routes as $route => $file) {
 if(strpos($url, $route) !== false){
 return $file;
 }
 }
 }
}