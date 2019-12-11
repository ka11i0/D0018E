<?php 
    include 'ServerCommunication.php'; 
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="produkter.css">
    <link rel="stylesheet" type="text/css" href="login.css">
</head>
	<body>
        <?php include_once 'navbar.php'; ?>
        	<?php
        	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addFunds"])) {
        		$user_id = $_SESSION["id"];
        		$query_konto = "SELECT * FROM konto WHERE Person_ID='$user_id'";
        		$conn = OpenCon();
        		$result = $conn->query($query_konto)->fetch_assoc();
        		$new_saldo = $result["Saldo"] + $_POST["monies"];
        		$addedAmount = $_POST["monies"];
        		$update_query = "UPDATE konto SET Saldo = '$new_saldo' WHERE Person_ID='$user_id'";
        		if ($conn->query($update_query)) {
        			$_SESSION["saldo"]=$new_saldo;
        			header('Location: saldo.php');
        		}
        		else {
        			echo "Error ".$conn->error;
        		}
        		CloseCon($conn);

        	}
        	if (isset($_SESSION["user"])) {
        		if ($_SESSION["privilegie"]==0) {
		        	echo '
		        	<form method="post">
			        	<div class="container">
						  	<label for="uname"><b>Saldo påfyllning</b></label>
						  	<input type="number" min="0" placeholder="Ange hur många pengar att lägga till" name="monies" required>
						  	<button type="submit" name="addFunds" >Fyll på pengar</button>
						</div>
					</form>';
				}
			}
			?>
	</body>
</html>