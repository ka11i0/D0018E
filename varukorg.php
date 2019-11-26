<?php session_start(); 
include 'ServerCommunication.php';
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
	<table>
		<tr id="title">
		    <th>Namn</th>
		    <th>Antal</th>
		    <th>Pris</th>
		</tr>
		<?php
			$person_id = $_SESSION["id"];
			$table_query = "SELECT varukorg.Produkt_ID, produkter.namn FROM varukorg WHERE Person_ID = '$person_id' INNER JOIN produkter ON varukorg.Produkt_ID = produkter.namn";
			$conn = OpenCon();
			//print_r($conn->query($table_query));
		?>
	</table>
</body>
</html>