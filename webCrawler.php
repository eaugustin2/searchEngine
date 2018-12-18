<?php

//Should have a starting Page to scrape links
$startPage = "https://www.google.com/search?q=movies&oq=movies&aqs=chrome..69i57j69i61j0l4.782j0j4&sourceid=chrome&ie=UTF-8";
$linkArr = array();
$current = array();

//Global mysql connection

//mysql://b4c2bd269d5d55:b0e01885@us-cdbr-iron-east-01.cleardb.net/heroku_a41d33233a6bf80?reconnect=true


$server = "us-cdbr-iron-east-01.cleardb.net";
$username= "b4c2bd269d5d55";
$password = "b0e01885";


try{
    //creating connection 
    $conn = new PDO("mysql:host=$servername; dbname=heroku_a41d33233a6bf80",$username,$password);

    //setting error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "connection successful!";
}
catch (PDOException $e)
{
    echo "Failed connection: " . $e->getMessage();
}

function getDetails($link){

    

    global $conn;

    //loading the linkof the web page
    $doc = new DOMDocument();
    @$doc->loadHTML(@file_get_contents($link));

    //To get title from the web page, if there is more than one take the first one
    $title = $doc->getElementsByTagName("title");
    $title = $title->item(0) ->nodeValue;

    $description="";
    $keywords="";

    //Descriptions are usually held in meta tags
    $metas = $doc->getElementsByTagName("meta");
    for($i =0; $i<$metas->length; $i++){
        $meta = $metas->item($i);

        if($meta->getAttribute("name") == strtolower("description")){
            $description = $meta->getAttribute("content");
        }

        else if($meta->getAttribute("name") == strtolower("keywords")){
            $keywords = $meta->getAttribute("content");
        } 
    }
    echo $title . "\n";
    echo $description . "\n";
    echo $link . "\n";

    //counting if any rows match data to be inputted
    $stmt = $conn->prepare("SELECT count(*) FROM alreadyCrawled WHERE websitelink =?");
    $stmt->execute([$link]);
    $counter = $stmt->fetchColumn();
    
    //counts if particular link is in databse, if there is atleast one instance, update it
    if($counter > 0){ 
        
       
        

        try{
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE alreadyCrawled SET title=?, keywords =?, explanation=?, websitelink=? WHERE websitelink=?";
            $update = $conn->prepare($sql);
            $update->execute([$title, $keywords, $description, $link, $link]);
            echo "Update successful..." . "\n";
        }
        catch(PDOException $e){
            echo "Failed to update: " . $e->getMessage();
        }
    }
    else{

        try{
            //setting error mode
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $insert = "INSERT INTO alreadyCrawled (title,explanation,keywords,websitelink) VALUES ('$title', '$description', '$keywords', '$link')";
            $conn->exec($insert);
            echo "Insert complete..." . "\n";
        }
        catch(PDOException $e){
            echo "Failed insert: " . $e->getMessage();
        }

    }
    
}


//function to follow the links
function followLinks($page){

    

    global $linkArr;
    global $current;

    //Load the HTMl page
    $doc = new DOMDocument();
    //read reading the file that's been parsed
    //@ suppresses warnings
    @$doc->loadHTML(@file_get_contents($page));

    

    $linkList = $doc->getElementsByTagName("a");

    

    //For each linkList refer as link and get the attribute href, which is the link
    foreach($linkList as $link){
        $retrievedLink = $link->getAttribute("href") . "\n";

        //Different Cases for diferent types of links retrieved through search/crawl
        if(substr($retrievedLink,0,1) == "/" && substr($retrievedLink,0,2) != "//"){

            $retrievedLink = parse_url($page)["scheme"]."://".parse_url($page)["host"].$retrievedLink;
        }
        else if(substr($retrievedLink,0,2) == "//"){
            $retrievedLink = parse_url($page)["scheme"] . ":" . $retrievedLink;
        }
        else if(substr($retrievedLink,0,2) == "./"){
            $retrievedLink = parse_url($page)["scheme"]."://".parse_url($page)["host"].dirname(parse_url($page)["path"]).substr($retrievedLink, 1);
        }
        else if(substr($retrievedLink,0,1) == "#"){
            $retrievedLink = parse_url($page)["scheme"]."://".parse_url($page)["host"].parse_url($page)["path"].$retrievedLink;
        }
        else if(substr($retrievedLink,0,3) == "../"){
            $retrievedLink = parse_url($page)["scheme"]."://".parse_url($page)["host"]."/".$retrievedLink;
        }
        else if(substr($retrievedLink,0,11) == "javascript:"){
            continue; //not needed
        }
        else if(substr($retrievedLink,0,5) == "https" && substr($retrievedLink,0,4) != "http"){
            $retrievedLink = parse_url($page)["scheme"]."://".parse_url($page)["host"]."/".$retrievedLink;
        }

        

        //Add links to array, if not already in it
        if(!in_array($retrievedLink, $linkArr)){
            $linkArr[] = $retrievedLink;
            $current[] = $retrievedLink;

            

            //holds the JSON format of information from getDetails
            getDetails($retrievedLink);

        }


       
    }

    array_shift($current);
    foreach($current as $website){
        followLinks($website);
    }

}



followLinks($startPage);




?>