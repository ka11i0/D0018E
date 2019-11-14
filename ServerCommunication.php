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

function DB_insertProduct() //kunna skapa en ny produkt i databasen som admin
{

}
function DB_update() //både users och produkter samt historik ska kunna uppdateras 
{
}
/*
function CheckIfTaken($currentconnection,$info) //returns what selected value in query that doesn exist in database custom sql_query very reuseable
{
$msg = ""
for($x = 0; $x < $count(info); $x++) 
{
	$sql_query ="SELECT {$info[$x]} FROM 'konto' WHERE Namn='{$_POST[$info[$x]]}'" ;(primary key)
 	$userinfo=UserCheck($sql_query,$currentconnection);
	if( != 0);
		 	$msg .="{$info[$x]} ";
}
return $msg;
}
*/

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
	
