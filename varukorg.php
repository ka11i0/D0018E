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
	$name_query = "SELECT varukorg.Produkt_ID, varukorg.quantity, varukorg.snapshot FROM varukorg INNER JOIN produkt ON varukorg.Produkt_ID = produkt.Produkt_ID WHERE Person_ID='$person_id' ORDER BY produkt.Produkt_ID ASC";
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
	//sätt variablerna som ska in i historik
	$hist_id = nextHistId($conn);
	$date = date("Y-m-d");
	$time = date("H:i:s");
	$saldo_query = "SELECT Saldo FROM konto WHERE Person_ID = ".$person_id;
	$result = $conn->query($saldo_query)->fetch_assoc();
	$nytt_saldo = $result["Saldo"];
	$information_query = "SELECT varukorg.Produkt_ID, varukorg.quantity, produkt.Saldo, produkt.Pris FROM varukorg INNER JOIN produkt ON varukorg.Produkt_ID = produkt.Produkt_ID WHERE Person_ID='$person_id' ORDER BY produkt.Produkt_ID ASC";
	try{
		$conn->begin_transaction();
		$result = $conn->query($information_query);
		while($row=$result->fetch_assoc()) {
			//kolla så att lagersaldo räcker till köpet
			if ($row["Saldo"]>=$row["quantity"]) {
				$Produkt_ID = $row["Produkt_ID"];
				$quant = $row["quantity"];
				//nytt kontosaldo för kontot
				//ifall det är rea på varan så måste reafaktorn räknas ut och multipliceras in
				if (isKampanj($Produkt_ID, date("Y-m-d"))) {
					$nytt_saldo = $nytt_saldo - round($row["Pris"] * $row["quantity"] * (1 - currentKampanj($Produkt_ID)*0.01),0);
				}
				else {
					$nytt_saldo = $nytt_saldo - $row["Pris"] * $row["quantity"];
				}
				//nytt lagersaldo för produkten
				$new_saldo = $row["Saldo"] - $quant;
				$purchase_query = "INSERT INTO historik (Transaktion_ID, Person_ID, Datum, Tid, Produkt_ID, quantity) VALUES ('$hist_id', '$person_id','$date', '$time','$Produkt_ID', '$quant')";
				$delete_varukorg_query = "DELETE FROM varukorg WHERE Person_ID = '$person_id' AND Produkt_ID = '$Produkt_ID'";
				$update_saldo_query = "UPDATE produkt SET saldo = '$new_saldo' WHERE Produkt_ID = '$Produkt_ID'";
				$update_pengar_query = "UPDATE konto SET Saldo = '$nytt_saldo' WHERE Person_ID = '$person_id'";
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
				if ($nytt_saldo < 0) {
					throw new Exception("saldo error");
				}
				else {
					if (!$conn->query($update_pengar_query)) {
						echo "Error: " . $update_pengar_query . "<br>" . $conn->error;
						throw new Exception("update_pengar_query error");
					}
				}
			}
			else {
				throw new Exception('Lagersaldot kan ej täcka valda varor.');
			}
		}
		$_SESSION["saldo"] = $nytt_saldo;
		$conn->commit();
	}
	catch (Exception $e){
		$conn->rollback();
		echo "<script type='text/javascript'>alert('".$e->getMessage()."');</script>";
	}
	CloseCon($conn);
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
			<?php
				//Ta fram all data till tabellen
				$total_kostnad = 0;
				$table_query = "SELECT varukorg.quantity, produkt.Produkt_ID, produkt.Produktnamn, produkt.Pris, produkt.Saldo FROM varukorg INNER JOIN produkt ON varukorg.Produkt_ID = produkt.Produkt_ID WHERE Person_ID='$person_id' ORDER BY produkt.Produkt_ID ASC";
				$conn = OpenCon();
				$result = $conn->query($table_query);
				if ($result->num_rows>0) {
					//skrivut headern av tabellen
					echo "<form method='post'>
							<table>
								<tr id='title'>
								    <th>Namn</th>
								    <th>Antal (st)</th>
								    <th>Ordinarie pris (kr)</th>
								    <th>Rea</th>
								    <th>Lagersaldo (st)</th>
								</tr>";
					while($row = $result->fetch_assoc()) {
						//skrivut bodyn av tabellen
						echo "<tr>";
		    			echo "<th>".$row["Produktnamn"]."</th>";

		    			echo "<th>";
		    			echo "<input type='number' name='".$row["Produktnamn"]."' value='".$row["quantity"]."' min='0'>";
		    			echo "</th>";

		    			echo "<th>".$row["Pris"]."</th>";

		    			echo "<th>";
		    			if (isKampanj($row["Produkt_ID"],date("Y-m-d"))) {
		    				$total_kostnad += round($row["quantity"]*$row["Pris"]*(1-currentKampanj($row["Produkt_ID"])*0.01), 0);
		    				echo currentKampanj($row["Produkt_ID"])."%";
		    			}
		    			else {
		    				$total_kostnad += $row["quantity"]*$row["Pris"];
		    				echo "0%";
		    			}
		    			echo "</th>";

		    			echo "<th>";
		    			echo "".$row["Saldo"]."";
		    			echo "</th>";

		    			echo "</tr>";

		    		}
		    		//i sista raden så skrivs totalkostnaden för köpet ut
		    		echo "<tr class='lastrow'>";
		    		echo "<th colspan='2'>Total kostnad:</th>";
		    		echo "<th colspan='3'>".$total_kostnad." kr</th>";
		    		echo "</tr>";
		    		echo "</table>";
		    		echo "<button name='uppdatera'>Uppdatera kundkorg</button>";
		    		echo "<button name='bekrafta'>Köp</button>";
		    		echo "</form>";
		    		CloseCon($conn);
		    	}
		    	//om det inte finns nåt i varukorgen
		    	else {
		    		echo "<h3 style='text-align:center'>Börja handla för att saker ska visas här.</h3>";
		    	}
			?>
</body>
</html>