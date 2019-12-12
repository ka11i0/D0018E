<?php
include 'ServerCommunication.php';
//get the q parameter from URL
$q=$_GET["q"];

//lookup all links from the xml file if length of q>0
if (strlen($q)>0) {
    $hint='';
    $conn = OpenCon();
    $searchTerm = $q;
    $query = "SELECT Produktnamn FROM produkt WHERE Produktnamn LIKE '%".$searchTerm."%'";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $hint = $hint.'<a href="produkter.php?produkt='.$row["Produktnamn"].'" class="small" style="">'.$row["Produktnamn"].'</a><br>';
    }
    CloseCon($conn);
    
}

// Set output to "no suggestion" if no hint was found
// or to the correct values
if ($hint=="") {
  $response="no suggestion";
} else {
  $response=$hint;
}

//output the response
echo $response;
?>