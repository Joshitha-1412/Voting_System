<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "voting_system";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
function addCandidate($name, $desc, $img){
    global $conn;
    $stmt = $conn->prepare("INSERT INTO candidates (name, description, image) VALUES (?,?,?)");
    $stmt->bind_param("sss",$name,$desc,$img);
    return $stmt->execute();
}

function removeCandidate($id){
    global $conn;
    $stmt = $conn->prepare("DELETE FROM candidates WHERE id=?");
    $stmt->bind_param("i",$id);
    return $stmt->execute();
}

function getCandidates(){
    global $conn;
    $res = $conn->query("SELECT * FROM candidates");
    return $res->fetch_all(MYSQLI_ASSOC);
}

function voteCandidate($id){
    global $conn;
    $stmt = $conn->prepare("UPDATE candidates SET votes=votes+1 WHERE id=?");
    $stmt->bind_param("i",$id);
    return $stmt->execute();
}

function getResults(){
    global $conn;
    $res = $conn->query("SELECT * FROM candidates ORDER BY votes DESC LIMIT 2");
    $top = $res->fetch_all(MYSQLI_ASSOC);
    if(count($top)==2) $top[0]['diff']=$top[0]['votes']-$top[1]['votes'];
    elseif(count($top)==1) $top[0]['diff']=$top[0]['votes'];
    return $top;
}

// Publish result toggle
function isPublished(){
    global $conn;
    $res = $conn->query("SELECT value FROM settings WHERE name='publish_result'");
    return ($res->num_rows>0) ? $res->fetch_assoc()['value'] : 0;
}

function setPublished($val){
    global $conn;
    $conn->query("INSERT INTO settings (name,value) VALUES('publish_result',$val)
        ON DUPLICATE KEY UPDATE value=$val");
}
?>
