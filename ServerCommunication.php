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