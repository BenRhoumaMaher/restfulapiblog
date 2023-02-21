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

/// GET for the Blog Comments Listing
if($url == '/comments' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $comments = getAllComments($dbConn);
    echo json_encode($comments);
   }
// get the posts from the database
function getAllComments($db) {
    $statement = $db->prepare("SELECT * FROM comments");
    $statement->execute();
    $result = $statement->setFetchMode(PDO::FETCH_ASSOC);
    return $statement->fetchAll();
}

/// POST for the Blog Comments Creating
if($url == '/comments' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = $_POST;
    $id = addPost($input, $dbConn);
    if($id){
    $input['id'] = $id;
    $input['link'] = "/comments/$id";
    }
    echo json_encode($input);
   }
// add the comments to the database
function addPost($input, $db){
    $sql = "INSERT INTO comments
    (comment, post_id, user_id)
    VALUES
    (:comment, :post_id, :user_id)";
    $statement = $db->prepare($sql);
    
    bindAllValues($statement, $input);

    $statement->execute();
    // This is to return the auto-incremented id of the record that was just created.
    return $db->lastInsertId();
}
function bindAllValues($statement, $params){
    $allowedFields = ['comment', 'post_id', 'user_id'];
    foreach($params as $param => $value){
    if(in_array($param, $allowedFields)){
    $statement->bindValue(':'.$param, $value);
    }
    }
    return $statement;
}

/// GET single comment 
if(preg_match("/comments\/([0-9])+/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'GET'){
 $id = $matches[1];
 $comment = getComment($dbConn, $id);
 echo json_encode($comment);
}
// fetching single record from the database
function getComment($db, $id) {
    $statement = $db->prepare("SELECT * FROM comments where id=:id");
    $statement->bindValue(':id', $id);
    $statement->execute();
    return $statement->fetch(PDO::FETCH_ASSOC);
}

/// Update comment
if(preg_match("/comments\/([0-9])+/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'PATCH'){
 $input = $_GET;
 $id = $matches[1];
 updateComment($input, $dbConn, $id);
 $comment = getComment($dbConn, $id);
 echo json_encode($post);
}

function getParams($input) {
 $allowedFields = ['comment', 'post_id', 'user_id'];
 $filterParams = [];
 foreach($input as $param => $value){
 if(in_array($param, $allowedFields)){
 $filterParams[] = "$param=:$param";
 }
 }
 return implode(", ", $filterParams);
}

function updateComment($input, $db, $id){
 $fields = getParams($input);
 $input['id'] = $id;
 $sql = "
 UPDATE comments
 SET $fields
 WHERE id=:id
 ";
 $statement = $db->prepare($sql);
 bindAllValues($statement, $input);
 $statement->execute();
 return $id;
}

/// Delete post
if(preg_match("/comments\/([0-9])+/", $url, $matches) && $_SERVER['REQUEST_METHOD'] == 'DELETE'){
 $commentId = $matches[1];
 deleteComment($dbConn, $commentId);
 echo json_encode([
 'id'=> $commentId,
 'deleted'=> 'true'
 ]);
}
function deleteComment($db, $id) {
 $statement = $db->prepare("DELETE FROM comments where id=:id");
 $statement->bindValue(':id', $id);
 $statement->execute();
}




      
