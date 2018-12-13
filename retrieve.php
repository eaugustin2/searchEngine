<?php

//Page to retrieve from database

//Create connection to db

//mysql://b4c2bd269d5d55:b0e01885@us-cdbr-iron-east-01.cleardb.net/heroku_a41d33233a6bf80?reconnect=true



$server = "us-cdbr-iron-east-01.cleardb.net";
$username= "b4c2bd269d5d55";
$password = "b0e01885";


$conn = new PDO("mysql:host=$server;dbname=heroku_a41d33233a6bf80", $username, $password);

//Retrieve value from user
$userInput = $_GET["userInput"];


$sql ="SELECT title,websitelink,explanation FROM alreadyCrawled WHERE explanation LIKE '%$userInput%' OR title LIKE '%$userInput%' OR websitelink LIKE '%$userInput%'";
$stmt = $conn->prepare($sql);
$stmt->execute();

$results = $stmt->fetchAll();

echo count($results) . " results found";
echo "<hr>";

foreach($results as $result){

    echo "<strong>Title: </strong>" . $result["title"];
    echo "</br>";
    echo "<strong>URL: </strong>". "<a href='". $result["websitelink"] . "'>".$result["websitelink"]."</a>";
    echo "</br>";
    echo "<strong>Description: </strong>" . $result["explanation"];
    echo "<hr>";

}





?>