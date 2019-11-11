<?php
//inkludera denna i filen som ska kommunicera med server
function OpenCon()
 {
 $dbhost = "127.0.0.1"; //url
 $dbuser = "960224";
 $dbpass = "apa";
 $db = "db960224"; //kolla upp
 $conn = new mysqli($dbhost, $dbuser, $dbpass,$db);
 return $conn;
}

 
function CloseCon($conn)
 {
 $conn -> close();
 }

function InsertDatabase($sql_query)
{if ($conn->query($sql_query) === TRUE)  {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
}
//create query and send to function kanske ha type of operation som parameter för mer logik ifall det behövs eller ha separata funktioner för olika operationer som update,search,delete o insertion.


