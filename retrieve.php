<?php

//Page to retrieve from database

//Create connection to db
$server = "127.0.0.1";
$username = "root";
$password = "Nanoune12!";

$conn = new PDO("mysql:host=$server;dbname=websites", $username, $password);

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