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
	$table_query = "SELECT historik.Transaktion_ID, historik.Datum, historik.Tid, historik.quantity, historik.status, produkt.Produktnamn, produkt.Pris FROM historik INNER JOIN produkt ON historik.Produkt_ID = produkt.Produkt_ID WHERE Person_ID='$user_id' ORDER BY historik.Datum DESC, historik.tid DESC, produkt.Produkt_ID ASC";
	$result = $conn->query($table_query);
	$index = 0;
	echo '<div id="kolumn2">
    <p id="headline"><h3>Köp historik för '.$namn.'</h3></p>
    <div id="scroll">';
	if ($result->num_rows>0) {
		if ($_SESSION["privilegie"]==1) {
			echo "<form method='post' width='100%'>";
		}
		echo "<table>
		<thead>
		<tr id='title' style='background-color:SlateBlue;'>
		<th>Namn</th>
		<th>Antal (st)</th>
		<th>Pris per enhet (kr)</th>
		<th>Totalkostnad (kr)</th>
		<th>Datum</th>
		<th>Klockslag</th>
		<th>Status</th>
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
	        $query_data[$index][6] = $row["status"];
	        $index++;
	    }
	}
	else {
	    echo "Inget köpt ännu.";
	}
	$current_queue = NULL;
	$count = 0;
	$color = "powderblue";
	for ($i=0; $i < $index; $i++) {
	    if ($current_queue == $query_data[$i][0]) {
	        $count++;
	        echo '<tr style="background-color:'.$color.';">';
		}
	    else {
	    	if ($color == "powderblue") {
            	$color = "DodgerBlue";
	        }
            else {
            	$color = "powderblue";
	        }
	        $count = 0;
	        $current_queue = $query_data[$i][0];
	        echo '<tr style="background-color:'.$color.';">';
	    }
	    echo "<th>".$query_data[$i][1]."</th>";
	    if ($_SESSION["privilegie"]==1) {
	    	echo "<th><input min='0' type='number' style='padding: 0px;width:50px;' value='".$query_data[$i][3]."' name='".$query_data[$i][0]."__".$query_data[$i][1]."'></th>";
	    }
	    else {
	    	echo "<th>".$query_data[$i][3]."</th>";
	    }
	    echo "<th>".$query_data[$i][2]."</th>";
	    echo "<th>".$query_data[$i][2]*$query_data[$i][3]."</th>";
	    if ($count==0 || $count<1) {
	        echo "<th>".$query_data[$i][4]."</th>";
	        echo "<th>".$query_data[$i][5]."</th>";
	        if ($_SESSION["privilegie"]==1 && $query_data[$i][6]=="incomplete") {
	        	echo "<th>";
	        	echo "<select name='status[]'>";
	        	echo "<option value='incomplete'>incomplete</option>";
	        	echo "<option value='complete'>complete</option>";
	        	echo "</select>";
	        	echo "</th>";
	        }
	        elseif ($_SESSION["privilegie"]==1 && $query_data[$i][6]=="complete") {
	        	echo "<th>";
	        	echo "<select name='status[]'>";
	        	echo "<option value='complete'>complete</option>";
	        	echo "<option value='incomplete'>incomplete</option>";
	        	echo "</select>";
	        	echo "</th>";
	        }
	    	else {
	    		echo "<th>".$query_data[$i][6]."</th>";
	    	}
	    }
	    else {
	    	echo "<th></th>";
	    	echo "<th></th>";
	    	echo "<th></th>";
	    }
	    echo "</tr>";
	}
        echo "</tbody></table>";
        echo "</div>";
        if ($_SESSION["privilegie"]==1) {
        	echo '<button type="submit" name="UppdateraHist" value="'.$user_id.'">Uppdatera historik</button>';
			echo "</form>";
		}
		echo "</div>";
}
function printUppdateraForm($user_id, $conn, $nmr) {
	$user_query = "SELECT * FROM konto WHERE Person_ID='$user_id'";
	$result = $conn->query($user_query)->fetch_assoc();
	echo '<div id="kolumn">
        <form method="post" id="update">
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
                <button type="submit" name="uppdatera">Uppdatera kontoinformation</button>';
                if ($nmr == 2) {
                	echo '<button type="submit" name="tillbaka">Tillbaka</button>';
                }
            echo '</form>
	    </div>';
}

function isKampanj ($Produkt_ID, $date) {
	$conn = OpenCon();
	$date = explode("-", $date);
	$query = "SELECT Start, Slut, Procent FROM kampanj WHERE Produkt_ID = '$Produkt_ID'";
	$result = $conn->query($query);
	$index = 0;
	$info="";
	while ($row = $result->fetch_assoc()) {
		$info[$index]=explode("-", $row["Start"]);
		$info[$index+1]=explode("-", $row["Slut"]);
		$index = $index + 2;
	}
	$index = 0;
	while ($index<count($info)) {
		if ($info[$index][0]<=$date[0] && $date[0]<=$info[$index+1][0]) {
			if ($info[$index][1]<=$date[1] && $date[1]<=$info[$index+1][1]) {
				if ($info[$index][2]<=$date[2] && $date[2]<=$info[$index+1][2]) {
					return TRUE;
				}
			}
		}
		$index = $index + 2;
	}
	CloseCon($conn);
	return FALSE;
}
function currentKampanj ($Produkt_ID) {
	$conn = OpenCon();
	$date = explode("-", date("Y-m-d"));
	$query = "SELECT Start, Slut, Procent FROM kampanj WHERE Produkt_ID = '$Produkt_ID'";
	$result = $conn->query($query);
	$index = 0;
	while ($row = $result->fetch_assoc()) {
		$info[$index]=explode("-", $row["Start"]);
		$info[$index][3]=$row["Procent"];
		$info[$index+1]=explode("-", $row["Slut"]);
		$index = $index + 2;
	}
	$index = 0;
	while ($index<count($info)) {
		if ($info[$index][0]<=$date[0] && $date[0]<=$info[$index+1][0]) {
			if ($info[$index][1]<=$date[1] && $date[1]<=$info[$index+1][1]) {
				if ($info[$index][2]<=$date[2] && $date[2]<=$info[$index+1][2]) {
					return $info[$index][3];
				}
			}
		}
		$index = $index + 2;
	}
	CloseCon($conn);
	return FALSE;
}