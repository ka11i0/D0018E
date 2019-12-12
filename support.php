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
                		//Formuläret för konto data blir skickad
                		if (isset($_POST["uppdatera"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
                				//ta data från formuläret
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
        						//query gick igenom, tillbaks till vart vi kom ifrån
						        if ($p->query($account)) {
						            header('Location: support.php?Person_ID='.$uname.'');
						        }
						        else {
						            echo "Nånting gick fel:  ".$conn->error;
						        }
						        CloseCon($p);
						}
						//uppdatera historik formuläret skickades
						elseif (isset($_POST["UppdateraHist"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
							$index=0;
							$conn = OpenCon();
							//pga hur historik tabellen är strukturerad så är de två attributen Transaktion_ID, Produkt_ID primary key tillsammans därav behövdes det på något sätt förmedlas i input fältets namn vilken rad i historik den refererade till, därav är de två inlagda i dess namn separerade med två _.
							foreach ($_POST as $key => $value) {
								if ($key != "UppdateraHist" && $key != "status") {
										//lägg in Transaktion_ID på 0 och Produktnamn på 1, Produktnamn översätts längre ner till dess ID.
										$info[$index] = explode("__", $key);
										//spara antalet som givits i formuläret på index 2
										$info[$index][2] = $value;
										$index++;
								}
								//Namnet på knappet innehåller vilken användare som ska uppdateras
								elseif ($key == "UppdateraHist") {
									$user_id = $value;
								}
							}
							$len = count($info);
							//de sista att hämta ut ifrån formuläret är  statusen, denna är invecklad att få fram
							//på grund av hur tabellen ser ut så är all data från <select> i det andra elementet i $_POST arrayen och det finns bara så många element i denna som antalet transaktioner för den valda användaren.
							//Därför så måste de räknas ut för varje element i $_POST["status"] hur många rader i historik den tillhör
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
								//om sista elementet i historik är nått så ska vi inte göra fler queries 
								if ($index<$len) {
									$prod_amount_query = "SELECT COUNT(*) FROM historik WHERE Transaktion_ID =".$info[$index][0];
									$result = $conn->query($prod_amount_query)->fetch_assoc();
									$seq = $result["COUNT(*)"];	
								}	
							}
							//varje rad som returneras ska itereras över då de korresponderar till ett index i info som innehåller det nya datat
							$historik_query = "SELECT * FROM historik WHERE Person_ID = ".$user_id." ORDER BY Transaktion_ID DESC, Produkt_ID ASC";
							//konto saldot för valda användaren behövs för att kunna ändra saldot vid ändring i ordern
							$konto_query = "SELECT Saldo FROM konto WHERE Person_ID = ".$user_id;
							$result = $conn->query($konto_query)->fetch_assoc();
							$user_saldo = $result["Saldo"];
							$index=0;
							//eftersom många UPDATE och DELETE ska ske så blir try catch ypperligt, då såfort nånting är fel kan en exception kastas och en rollback kan ske. Om inget blir fel kan alla ändringar commitas.
							try{
								$conn->begin_transaction();
								$result = $conn->query($historik_query);
								while($row=$result->fetch_assoc()) {
									//ta fram relevant information om produkten
									$produkt_query = "SELECT * FROM produkt WHERE Produktnamn = '".$info[$index][1]."'";
									$produkt_result=$conn->query($produkt_query)->fetch_assoc();
									$produkt_saldo = $produkt_result["Saldo"];
									$produkt_pris = $produkt_result["Pris"];
									$produkt_id = $produkt_result["Produkt_ID"];
									//om status meddelandet skiljer sig från det på databasen så ska den ändras
									if ($row["status"]!=$info[$index][3]){
										$message = $info[$index][3];
										$tran_id = $info[$index][0];
										$msg_query = "UPDATE historik SET status = '$message' WHERE Transaktion_ID='$tran_id' AND Produkt_ID = '$produkt_id'";
										if (!$conn->query($msg_query)) {
											throw new Exception("update status message error");
										}
									}
									//en hel rad ska tas bort
									if ($info[$index][2]==0) {
										//refund, pengasaldo ökning + lagersaldo ökning
										$new_lagersaldo = $row["quantity"] + $produkt_saldo;
										//om köpet skedde under en kampanj så ska det reapriset återbetalas
										if (isKampanj($produkt_id, $row["Datum"])) {
											//(1-getKampanj($produkt_id, $row["Datum"])*0.01) räknar ut rea procenten i decimalform
											//round(INT, 0) avrundar till närmaste heltal
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
										//om köpet skedde under en kampanj så ska det reapriset återbetalas
										if (isKampanj($produkt_id, $row["Datum"])) {
											//(1-getKampanj($produkt_id, $row["Datum"])*0.01) räknar ut rea procenten i decimalform
											//round(INT, 0) avrundar till närmaste heltal
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
										//lagersaldot täcker inte köpet
										if ($new_lagersaldo<0) {
											throw new Exception("lagersaldo error");
										}
										//om köpet skedde under en kampanj så ska det reapriset återbetalas
										if (isKampanj($produkt_id, $row["Datum"])) {
											//(1-getKampanj($produkt_id, $row["Datum"])*0.01) räknar ut rea procenten i decimalform
											//round(INT, 0) avrundar till närmaste heltal
											$new_pengasaldo = $user_saldo - round(($info[$index][2] - $row["quantity"]) * $produkt_pris *(1-getKampanj($produkt_id, $row["Datum"])*0.01),0);
										}
										else {
											$new_pengasaldo = $user_saldo - ($info[$index][2] - $row["quantity"]) * $produkt_pris;
										}
										//användaren har inte tillräckligt många pengar
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
							//gå tillbacks till vart formuläret skickades
							header('Location: support.php?Person_ID='.$user_id.'');
						}
						//tillbaka knappen tryckt
						elseif (isset($_POST["tillbaka"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
							header('Location: support.php');
						}
						//vid refresh efter att if satsen nedan gjorts så skrivs data om den valda användaren ut
                		elseif (isset($_GET["Person_ID"])) {
            				$conn = OpenCon();
                			$user_id = $_GET["Person_ID"];
                			//uppdatera vald användares konto formulär 2an gör att tillbaka knappen skrivs ut
                			printUppdateraForm($user_id, $conn, 2);
                			//tabellen med order historik skrivs ut för vald användare
	                        printHistorik($user_id, $conn);	
	                        CloseCon($conn);
	                    }
	                    //vid tryck av specifik användare knappen så redirectas man och get ser till att denna skickas
                		elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET["uppdatera"])) {
							foreach ($_POST as $key => $value) {
								$user = $key;
							}
							header('Location: support.php?Person_ID='.$user.'');
						}
						//vid första inläsning av sidan så ska en rad knappar med de olika användarna skrivas ut
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
							//om användaren inte är admin så skrivs kontakt information ut
							else {
								echo '<div id="positiontext">
						            <h1>Kontakt</h1>
						            <p>Nå oss på telefon mellan tiderna 10-16 på veckodagar via numret:  1111111111. <br>Det går även att kontakta oss via e-mail: snus@experten.se.</p>
						        	</div>';
							}
						}
						else {
							//om ingen är inloggad skrivs kontakt information ut
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