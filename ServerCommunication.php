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
function printHistorik($user_id, $conn){
	$name_query = "SELECT Namn FROM konto WHERE Person_ID='$user_id'";
	$result = $conn->query($name_query)->fetch_assoc();
	$namn = $result["Namn"];
	$table_query = "SELECT historik.Transaktion_ID, historik.Datum, historik.Tid, historik.quantity, produkt.Produktnamn, produkt.Pris FROM historik INNER JOIN produkt ON historik.Produkt_ID = produkt.Produkt_ID WHERE Person_ID='$user_id' ORDER BY historik.Datum DESC, historik.tid DESC, produkt.Produkt_ID ASC";
	$result = $conn->query($table_query);
	$index = 0;
	echo '<div id="kolumn2">
    <p id="headline"><h3>Köp historik för '.$namn.'</h3></p>
    <div id="scroll">';
	if ($result->num_rows>0) {
		echo "<table>
		<thead>
		<tr id='title' class='bottom'>
		<th>Namn</th>
		<th>Antal (st)</th>
		<th>Pris per enhet (kr)</th>
		<th>Totalkostnad (kr)</th>
		<th>Datum</th>
		<th>Klockslag</th>
		</tr>
		</thead>";
		echo "<tbody>";
		while($row = $result->fetch_assoc()) {
	        $query_data[$index][0] = $row["Transaktion_ID"];
	        $query_data[$index][1] = $row["Produktnamn"];
	        $query_data[$index][2] = $row["Pris"];
	        $query_data[$index][3] = $row["quantity"];
	        $query_data[$index][4] = $row["Datum"];
	        $query_data[$index][5] = $row["Tid"];
	        $index++;
	    }
	}
	else {
	    echo "Inget köpt ännu.";
	}
	$current_queue = NULL;
	$count = 0;
	for ($i=0; $i < $index; $i++) {
	    if ($current_queue == $query_data[$i][0]) {
	        $count++;
	        echo "<tr>";
		}
	    else {
	        $count = 0;
	            $current_queue = $query_data[$i][0];
	            echo "<tr class='top'>";
	    }
	    echo "<th>".$query_data[$i][1]."</th>";
	    echo "<th>".$query_data[$i][3]."</th>";
	    echo "<th>".$query_data[$i][2]."</th>";
	    echo "<th>".$query_data[$i][2]*$query_data[$i][3]."</th>";
	    if ($count==0 || $count<1) {
	        echo "<th>".$query_data[$i][4]."</th>";
	        echo "<th>".$query_data[$i][5]."</th>";
	    }
	    else {
	    	echo "<th></th>";
	    	echo "<th></th>";
	    }
	    echo "</tr>";
	}
        echo "</tbody></table>";
		echo "</div>";
		echo "</div>";
}
function printUppdateraForm($user_id, $conn, $method) {
	$user_query = "SELECT * FROM konto WHERE Person_ID='$user_id'";
	$result = $conn->query($user_query)->fetch_assoc();
	echo '<div id="kolumn">
        <form method="'.$method.'">
            <p id="headline"><h3>Kontouppgifter för '.$result["Namn"].'</h3></p>
                <label for="email"><b>Email</b></label>
                <input type="email" value="'.$result["Mail"].'" name="mail" required>

                <div id="uppg">
                <label for="town"><b>Stad</b></label>
                <input type="text" value="'.$result["Stad"].'" name="town" required>
                </div>

                <div id="uppg">
                <label for="post"><b>Postnummer</b></label>
                <input type="number" value="'.$result["Postnummer"].'" name="postnum" required>
                </div>

                <div id="uppg">
                <label for="addr"><b>Address</b></label>
                <input type="text" value="'.$result["Address"].'" name="addrnum" required>
                </div>

                <div id="uppg">
                <label for="tel"><b>Telefonnummer</b></label>
                <input type="telnum" value="'.$result["Telefonnummer"].'" name="telnum" required>
                </div>

                <div id="uppg">
                <label for="town"><b>Lösenord</b></label>
                <input type="text" value="'.$result["Lösenord"].'" name="password" required>
                </div>
                <input type="hidden" value="'.$result["Person_ID"].'" name="id">
                <button type="submit" name="uppdatera">Uppdatera</button>
            </form>
	    </div>';
}
