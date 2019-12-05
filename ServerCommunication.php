<?php

//inkludera denna fil i filen som ska kommunicera med server för alla kommandon med server.


function OpenCon() //retunerar ett connection objekt till servern
{
	$dbhost = "127.0.0.1"; 
	$dbuser = "960224";
	$dbpass = "apa";
	$db = "db960224"; //zerosdb
	$conn = new mysqli($dbhost, $dbuser, $dbpass,$db);
	return $conn;
}

function CloseCon($conn) //tar bort connection ur objectet
{
	$conn -> close();
}

function UserCheck($sql_query1,$currentconnection) //returns associative array with user data if at least one user exists. måste implementera för mer än bara en user.
{	
	$result = $currentconnection->query($sql_query1); 
	if ($result->num_rows > 0) ///användare finns ifall vilkoret är sant 
		{ 
			while($row = $result->fetch_assoc())  //for loop with as number of iterations $result->num_rows
		{
		  	return $row;
		}
	}
	return 0; //flag 
}

function CheckPOST($info)
{
	$counter=0;
	for($x = 0; $x < count($info); $x++) 
		{
	 		if(isset($_POST[$info[$x]])) 
		  		{ 
					$counter++;
					//$info[$x] = $_POST[$info[$x]];
		    	}
		}	
	if($counter==count($info))
	{
		return true;
	}
		return false;
}

function nextCommentId($p){
  $id_query = "SELECT MAX(Kommentar_ID) FROM kommentarer";
  $result = $p->query($id_query)->fetch_assoc();
  if ($result['MAX(Kommentar_ID)'] !=NULL) {
  	return $result['MAX(Kommentar_ID)'] + 1;
  }
  return 0;
}
function OutputProducts($sql,$p)
{
  $result = $p->query($sql);
  if($result->num_rows > 0) 
  {
    return $result;
  }
    return null;
}

function nextprodId($p){
  $id_query = "SELECT MAX(Produkt_ID) FROM produkt";
  $result = $p->query($id_query)->fetch_assoc();
  return $result['MAX(Produkt_ID)'] + 1;
}
function nextHistId($p){
    $id_query = "SELECT MAX(Transaktion_ID) FROM historik";
    $result = $p->query($id_query)->fetch_assoc();
    return $result['MAX(Transaktion_ID)'] + 1;
}