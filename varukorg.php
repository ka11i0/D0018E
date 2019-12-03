<?php session_start(); 
include 'ServerCommunication.php';
$person_id = $_SESSION["id"];
if (isset($_POST["uppdatera"])) {
	$conn = OpenCon();
	$index = 0;
	//placera namn och data från formulär i array med korresponderande index mellan de två över arrayerna
	foreach ($_POST as $key => $value) {
		$post_name[$index] = $key;
		$post_info[$index] = $value;
		$index++;
	}
	//ta fram quantity i varukorg för att se om ändringar ska utföras
	$name_query = "SELECT varukorg.Produkt_ID, varukorg.quantity FROM varukorg INNER JOIN produkt ON varukorg.Produkt_ID = produkt.Produkt_ID WHERE Person_ID='$person_id' ORDER BY produkt.Produkt_ID ASC";
	$result = $conn->query($name_query);
	$index = 0;
	while($row=$result->fetch_assoc()) {
		//ta bort vara från kundkorgen om quantity är 0
		if ($post_info[$index]==0){
			$produkt_id = $row["Produkt_ID"];
			$delete_query = "DELETE FROM varukorg WHERE Produkt_ID = '$produkt_id' AND Person_ID = '$person_id'";
			if ($conn->query($delete_query)) {
				echo "<script type='text/javascript'>alert('Vara borttagen');</script>";
			}
			else{
				echo "<script type='text/javascript'>alert('Nånting gick fel');</script>";	
			}
		}
		//ändra i kundkorgen om databasens quantity skiljer med värde från $_POST
		elseif($post_info[$index]!=$row["quantity"]) {
			$new_quant = $post_info[$index];
			$produkt_id = $row["Produkt_ID"];
			$update_query ="UPDATE varukorg SET quantity = '$new_quant' WHERE Produkt_ID = '$produkt_id' AND Person_ID = '$person_id'";
			if($conn->query($update_query)){
				echo "<script type='text/javascript'>alert('Ändringar utförda');</script>";
			}
			else {
				echo "<script type='text/javascript'>alert('Nånting gick fel');</script>";
			}
		}
		$index++;
	}
	CloseCon($conn);
}
elseif (isset($_POST["bekrafta"])) {
	$conn = OpenCon();
	$hist_id = nextHistId($conn);
	$date = date("Y-m-d");
	$time = date("H:i:s");
	$information_query = "SELECT varukorg.Produkt_ID, varukorg.quantity, produkt.Saldo FROM varukorg INNER JOIN produkt ON varukorg.Produkt_ID = produkt.Produkt_ID WHERE Person_ID='$person_id' ORDER BY produkt.Produkt_ID ASC";
	try{
		$conn->begin_transaction();
		$result = $conn->query($information_query);
		while($row=$result->fetch_assoc()) {
			if ($row["Saldo"]>=$row["quantity"]) {
				$Produkt_ID = $row["Produkt_ID"];
				$quant = $row["quantity"];
				$new_saldo = $row["Saldo"] - $quant;
				$purchase_query = "INSERT INTO historik (Transaktion_ID, Person_ID, Datum, Tid, Produkt_ID, quantity) VALUES ('$hist_id', '$person_id','$date', '$time','$Produkt_ID', '$quant')";
				$delete_varukorg_query = "DELETE FROM varukorg WHERE Person_ID = '$person_id' AND Produkt_ID = '$Produkt_ID'";
				$update_saldo_query = "UPDATE produkt SET saldo = '$new_saldo' WHERE Produkt_ID = '$Produkt_ID'";
				if (!$conn->query($update_saldo_query)) {
					echo "Error: " . $update_saldo_query . "<br>" . $conn->error;
					throw new Exception("update_saldo_query error");
				}
				if (!$conn->query($purchase_query)) {
					echo "Error: " . $purchase_query . "<br>" . $conn->error;
					throw new Exception("purchase_query error");
				}
				if (!$conn->query($delete_varukorg_query)) {
					echo "Error: " . $delete_varukorg_query . "<br>" . $conn->error;
					throw new Exception("delete_varukorg_query error");
				}
				$conn->commit();
			}
			else {
				throw new Exception('Lagersaldot kan ej täcka valda varor.');
			}
		}
	}
	catch (Exception $e){
		$conn->rollback();
		echo "<script type='text/javascript'>alert('".$e->getMessage()."');</script>";
	}
	CloseCon($conn);
}
function checkForm()
{
	foreach ($_POST as $key => $value) {
		
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="produkter.css">
	<link rel="stylesheet" type="text/css" href="varukorg.css">
</head>
<body>
	<?php include_once 'navbar.php'; ?>
	<form method="post">
		<table>
			<tr id="title">
			    <th>Namn</th>
			    <th>Antal (st)</th>
			    <th>Pris per enhet (kr)</th>
			    <th>Lagersaldo (st)</th>
			</tr>
			<?php
				$total_kostnad = 0;
				$table_query = "SELECT varukorg.quantity, produkt.Produktnamn, produkt.Pris, produkt.Saldo FROM varukorg INNER JOIN produkt ON varukorg.Produkt_ID = produkt.Produkt_ID WHERE Person_ID='$person_id' ORDER BY produkt.Produkt_ID ASC";
				$conn = OpenCon();
				$result = $conn->query($table_query);
				if ($result->num_rows>0) {
					while($row = $result->fetch_assoc()) {
						echo "<tr>";
		    			echo "<th>".$row["Produktnamn"]."</th>";

		    			echo "<th>";
		    			echo "<input type='number' name='".$row["Produktnamn"]."' value='".$row["quantity"]."' min='0'>";
		    			echo "</th>";

		    			echo "<th>".$row["Pris"]."</th>";

		    			echo "<th>";
		    			echo "".$row["Saldo"]."";
		    			echo "</th>";

		    			echo "</tr>";
		    			$total_kostnad += $row["quantity"]*$row["Pris"];
		    		}
		    		echo "<tr class='lastrow'>";
		    		echo "<th id='lr_left'>Total kostnad:</th>";
		    		echo "<th id='lr_right'>".$total_kostnad." kr</th>";
		    		echo "</tr>";
		    		echo "</table>";
		    		echo "<input type='submit' name='uppdatera' value='Uppdatera kundkorg'>";
		    		echo "<input type='submit' name='bekrafta' value='Köp'>";
		    		CloseCon($conn);
		    	}
		    	else {
		    		echo "</table>;";
		    	}
			?>
	</form>
</body>
</html>