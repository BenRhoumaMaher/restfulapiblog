<?php

$url = $_SERVER['REQUEST_URI'];
// checking if slash is first character in route otherwise add it
if(strpos($url,"/") !== 0){
 $url = "/$url";
}

// create an instance of the DB class
$dbInstance = new DB();
$dbConn = $dbInstance->connect($db);

header("Content-Type:application/json");

/// GET for the Blog Post Listing
if($url == '/posts' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $posts = getAllPosts($dbConn);
    echo json_encode($posts);
   }
// get the posts from the database
function getAllPosts($db) {
    $statement = $db->prepare("SELECT * FROM posts");
    $statement->execute();
    $result = $statement->setFetchMode(PDO::FETCH_ASSOC);
    return $statement->fetchAll();
}

/// POST for the Blog Post Creating
if($url == '/posts' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = $_POST;
    $id = addPost($input, $dbConn);
    if($id){
    $input['id'] = $id;
    $input['link'] = "/posts/$id";
    }
    echo json_encode($input);
   }
// add the posts to the database
function addPost($input, $db){
    $sql = "INSERT INTO posts
    (title, status, content, user_id)
    VALUES
    (:title, :status, :content, :user_id)";
    $statement = $db->prepare($sql);
    
    bindAllValues($statement, $input);

    $statement->execute();
    // This is to return the auto-incremented id of the record that was just created.
    return $db->lastInsertId();
}
function bindAllValues($statement, $params){
    $allowedFields = ['title', 'status', 'content', 'user_id'];
    foreach($params as $param => $value){
    if(in_array($param, $allowedFields)){
    $statement->bindValue(':'.$param, $value);
    }
    }
    return $statement;
}

/// GET single post 
// checking if the pattern is /posts/{id} where id can be any number
// And then we are calling our custom function getPost() that will fetch the post record from the database.
if(preg_match("/posts\/([0-9])+/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'GET'){
 $id = $matches[1];
 $post = getPost($dbConn, $id);
 echo json_encode($post);
}
// fetching single record from the database
function getPost($db, $id) {
    $statement = $db->prepare("SELECT * FROM posts where id=:id");
    $statement->bindValue(':id', $id);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
}

/// Update post
// checks if the URL is of the format : /posts/{id} and then checks if the Request method is PATCH
if(preg_match("/posts\/([0-9])+/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'PATCH'){
 $input = $_GET;
 $id = $matches[1];
 // In that case, it calls the updatePost() method. The updatePost() method 
 // gets key value pairs as comma separated strings through the getParams() method
 updatePost($input, $dbConn, $id);
 $post = getPost($dbConn, $id);
 echo json_encode($post);
}

function getParams($input) {
 $allowedFields = ['title', 'status', 'content', 'user_id'];
 $filterParams = [];
 foreach($input as $param => $value){
 if(in_array($param, $allowedFields)){
 $filterParams[] = "$param=:$param";
 }
 }
 return implode(", ", $filterParams);
}

function updatePost($input, $db, $id){
 $fields = getParams($input);
 $input['id'] = $id;
 $sql = "
 UPDATE posts
 SET $fields
 WHERE id=:id
 ";
 $statement = $db->prepare($sql);
 bindAllValues($statement, $input);
 $statement->execute();
 return $id;
}

/// Delete post
if(preg_match("/posts\/([0-9])+/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'DELETE'){
 $postId = $matches[1];
 deletePost($dbConn, $postId);
 echo json_encode([
 'id'=> $postId,
 'deleted'=> 'true'
 ]);
}
function deletePost($db, $id) {
 $statement = $db->prepare("DELETE FROM posts where id=:id");
 $statement->bindValue(':id', $id);
 $statement->execute();
}




      
