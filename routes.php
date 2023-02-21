<?php
// posts and comments are part of the URL that we're expecting
// if the URL will have posts,it will serve the posts.php file, and it will serve comments.php 
// if the URL will have comments in it.
$routes = [
 'posts' => 'posts.php',
 'comments' => 'comments.php'
];