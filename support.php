<?php 
include 'ServerCommunication.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="produkter.css">
    <link rel="stylesheet" type="text/css" href="kontosida.css">
    <link rel="stylesheet" type="text/css" href="support.css">
</head>
	<body>
		<?php include_once 'navbar.php'; ?>
		<div id="box">
                	<?php
                		if (isset($_POST["uppdatera"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
                				var_dump($_POST);
                				$info[0] = $_POST['mail'];
						        $info[1] = $_POST['town'];
						        $info[2] = $_POST['postnum'];
						        $info[3] = $_POST['addrnum'];
						        $info[4] = $_POST['telnum'];
						        $info[5] = $_POST['password'];
						        $p=OpenCon();  

						        $uname = $_POST['id'];
						        $account = "UPDATE konto SET Lösenord='$info[5]', Mail='$info[0]', Stad='$info[1]', Postnummer='$info[2]', Address='$info[3]', Telefonnummer='$info[4]' 
        							WHERE Person_ID = '$uname'";
						        if ($p->query($account)) {
						            header('Location: support.php?Person_ID='.$uname.'');
						        }
						        else {
						            echo "Fek ".$conn->error;
						        }
						        CloseCon($p);
						}
						elseif (isset($_POST["UppdateraHist"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
							$index=0;
							$conn = OpenCon();
							foreach ($_POST as $key => $value) {
								if ($key != "UppdateraHist" && $key != "status") {
										$info[$index] = explode("__", $key);
										$info[$index][2] = $value;
										$index++;
								}
								elseif ($key == "UppdateraHist") {
									$user_id = $value;
								}
							}
							$len = count($info);
							$prod_amount_query = "SELECT COUNT(*) FROM historik WHERE Transaktion_ID =".$info[0][0];
							$result = $conn->query($prod_amount_query)->fetch_assoc();
							$seq = $result["COUNT(*)"];
							$index = 0;
							foreach ($_POST["status"] as $key => $value) {
								//count number of prod in transaction
								$val = $index;
								for ($i=0 + $val; $i < ($seq + $val); $i++) { 
									$info[$i][3] = $value;
									$index++;
								}
								if ($index<$len) {
									$prod_amount_query = "SELECT COUNT(*) FROM historik WHERE Transaktion_ID =".$info[$index][0];
									$result = $conn->query($prod_amount_query)->fetch_assoc();
									$seq = $result["COUNT(*)"];	
								}	
							}
							$historik_query = "SELECT * FROM historik WHERE Person_ID = ".$user_id." ORDER BY Transaktion_ID DESC, Produkt_ID ASC";

							$n_query = "SELECT COUNT(*) FROM historik WHERE Person_ID = ".$user_id;
							$result = $conn->query($n_query)->fetch_assoc();
							$index = $result["COUNT(*)"] - 1;
							$konto_query = "SELECT Saldo FROM konto WHERE Person_ID = ".$user_id;
							$result = $conn->query($konto_query)->fetch_assoc();
							$user_saldo = $result["Saldo"];
							$index=0;
							try{
								$conn->begin_transaction();
								$result = $conn->query($historik_query);
								while($row=$result->fetch_assoc()) {
									$produkt_query = "SELECT * FROM produkt WHERE Produktnamn = '".$info[$index][1]."'";
									$produkt_result=$conn->query($produkt_query)->fetch_assoc();
									$produkt_saldo = $produkt_result["Saldo"];
									$produkt_pris = $produkt_result["Pris"];
									$produkt_id = $produkt_result["Produkt_ID"];
									if ($row["status"]!=$info[$index][3]){
										//update msg
										$message = $info[$index][3];
										$tran_id = $info[$index][0];
										$msg_query = "UPDATE historik SET status = '$message' WHERE Transaktion_ID='$tran_id' AND Produkt_ID = '$produkt_id'";
										if (!$conn->query($msg_query)) {
											throw new Exception("update status message error");
										}
									}
									if ($info[$index][2]==0) {
										//refund, pengasaldo ökning + lagersaldo ökning
										$new_lagersaldo = $row["quantity"] + $produkt_saldo;
										if (isKampanj($produkt_id, $row["Datum"])) {
											$new_pengasaldo = round($row["quantity"] * $produkt_pris * (1-getKampanj($produkt_id, $row["Datum"])*0.01),0) + $user_saldo;
										}
										else {
											$new_pengasaldo = $row["quantity"] * $produkt_pris + $user_saldo;
										}
										$delete_query = "DELETE FROM historik WHERE Transaktion_ID='".$info[$index][0]."' AND Produkt_ID = ".$produkt_id;
										$update_konto_query = "UPDATE konto SET Saldo = ".$new_pengasaldo." WHERE Person_ID = ".$user_id;
										$update_produkt_query = "UPDATE produkt SET Saldo = ".$new_lagersaldo." WHERE Produkt_ID = ".$produkt_id;
										if (!$conn->query($delete_query) || !$conn->query($update_konto_query) || !$conn->query($update_produkt_query)) {
											throw new Exception("remove historik row error");
										}

									}
									elseif ($row["quantity"]>$info[$index][2]) {
										//antal minskning, pengasaldo ökning + lagersaldo ökning
										$new_lagersaldo = $row["quantity"] + $produkt_saldo - $info[$index][2];
										if (isKampanj($produkt_id, $row["Datum"])) {
											$new_pengasaldo = round(($row["quantity"] - $info[$index][2]) * $produkt_pris * (1-getKampanj($produkt_id, $row["Datum"])*0.01),0) + $user_saldo;
										}
										else {
											$new_pengasaldo = ($row["quantity"] - $info[$index][2]) * $produkt_pris + $user_saldo;
										}
										$new_quantity = $info[$index][2];
										$update_historik_query = "UPDATE historik SET quantity = ".$new_quantity." WHERE Transaktion_ID='".$info[$index][0]."' AND Produkt_ID = ".$produkt_id;
										$update_konto_query = "UPDATE konto SET Saldo = ".$new_pengasaldo." WHERE Person_ID = ".$user_id;
										$update_produkt_query = "UPDATE produkt SET Saldo = ".$new_lagersaldo." WHERE Produkt_ID = ".$produkt_id;
										if (!$conn->query($update_historik_query) || !$conn->query($update_konto_query) || !$conn->query($update_produkt_query)) {
											throw new Exception("update historik row error");
										}
									}
									elseif ($row["quantity"]<$info[$index][2]) {
										//antal ökning, pengasaldo minskning + lagersaldo minskning
										$new_lagersaldo = $produkt_saldo - $info[$index][2] + $row["quantity"];
										if ($new_lagersaldo<0) {
											throw new Exception("lagersaldo error");
										}
										if (isKampanj($produkt_id, $row["Datum"])) {
											$new_pengasaldo = $user_saldo - round(($info[$index][2] - $row["quantity"]) * $produkt_pris *(1-getKampanj($produkt_id, $row["Datum"])*0.01),0);
										}
										else {
											$new_pengasaldo = $user_saldo - ($info[$index][2] - $row["quantity"]) * $produkt_pris;
										}
										if ($new_pengasaldo<0) {
											throw new Exception("pengarsaldo error");
										}
										$new_quantity = $info[$index][2];
										$update_historik_query = "UPDATE historik SET quantity = ".$new_quantity." WHERE Transaktion_ID='".$info[$index][0]."' AND Produkt_ID = ".$produkt_id;
										$update_konto_query = "UPDATE konto SET Saldo = ".$new_pengasaldo." WHERE Person_ID = ".$user_id;
										$update_produkt_query = "UPDATE produkt SET Saldo = ".$new_lagersaldo." WHERE Produkt_ID = ".$produkt_id;
										if (!$conn->query($update_historik_query) || !$conn->query($update_konto_query) || !$conn->query($update_produkt_query)) {
											throw new Exception("update historik row error");
										}
									}
									$index++;
								}
								$result = $conn->query($historik_query);
								$index=0;
								$conn->commit();
							}
							catch (Exception $e) {
								$conn->rollback();
								echo "<script type='text/javascript'>alert('".$e->getMessage()."');</script>";
							}
							CloseCon($conn);
							header('Location: support.php?Person_ID='.$user_id.'');
						}
						elseif (isset($_POST["tillbaka"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
							header('Location: support.php');
						}
                		elseif (isset($_GET["Person_ID"])) {
            				$conn = OpenCon();
                			$user_id = $_GET["Person_ID"];
                			printUppdateraForm($user_id, $conn, 2);
	                        printHistorik($user_id, $conn);	
	                        CloseCon($conn);
	                    }
                		elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET["uppdatera"])) {
							foreach ($_POST as $key => $value) {
								$user = $key;
							}
							header('Location: support.php?Person_ID='.$user.'');
						}
						elseif (isset($_SESSION["user"])) {
							if ($_SESSION["privilegie"]==1) {
								$conn = OpenCon();
								//printa alla användare som knappar
								$user_query = "SELECT * FROM konto WHERE Privilegie=0";
								$result = $conn->query($user_query);
								echo '<div id="kolumn">';
								echo '<form method="post">';
								while ($row=$result->fetch_assoc()) {
									echo '<button type="submit" name="'.$row["Person_ID"].'">Person_ID: '.$row["Person_ID"].' Namn: '.$row["Namn"].'</button>';
									echo "<br>";
								}
								CloseCon($conn);
							}
							else {
								echo '<div id="positiontext">
						            <h1>Kontakt</h1>
						            <p>Nå oss på telefon mellan tiderna 10-16 på veckodagar via numret:  1111111111. <br>Det går även att kontakta oss via e-mail: snus@experten.se.</p>
						        	</div>';
							}
						}
						else {
							echo '<div id="positiontext">
					            <h1>Kontakt</h1>
					            <p>Nå oss på telefon mellan tiderna 10-16 på veckodagar via numret:  1111111111. <br>Det går även att kontakta oss via e-mail: snus@experten.se.</p>
					        	</div>';
						}
					?>
				</form>
			</div>
		</div>
	</body>
</html>