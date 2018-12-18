<!DOCTYPE html>
<html>
    <head>
        <title>Search Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link type="text/css" rel="stylesheet" href="styling.css">
    </head>
    <body>
        <div class="main">
                <h1><a href="index.html">Search Engine</a></h1>

         <form method="GET" action="retrieve.php">
                    <input type="text" id="userInput" name="userInput" placeholder="look up...">
                    <input type="submit" value="Search">
                </form>

        </div>

<?php


$server = "us-cdbr-iron-east-01.cleardb.net";
$username= "b4c2bd269d5d55";
$password = "b0e01885";

/*
dbname=heroku_a41d33233a6bf80
*/


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


               
    </body>

</html>