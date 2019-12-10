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
                		if (isset($_GET["uppdatera"])) {
                				$info[0] = $_GET['mail'];
						        $info[1] = $_GET['town'];
						        $info[2] = $_GET['postnum'];
						        $info[3] = $_GET['addrnum'];
						        $info[4] = $_GET['telnum'];
						        $info[5] = $_GET['password'];
						        $p=OpenCon();  

						        $uname = $_GET['id'];
						        $account = "UPDATE konto SET Lösenord='$info[5]', Mail='$info[0]', Stad='$info[1]', Postnummer='$info[2]', Address='$info[3]', Telefonnummer='$info[4]' 
        WHERE `Namn` = '$uname'";
						        if ($p->query($account)) {
						            echo "<script type='text/javascript'>alert('Dina kontouppgifter har blivit uppdaterade');</script>";
						            header('Location: support.php?Person_ID='.$uname.'');
						        }
						        else {
						            echo "Fek ".$conn->error;
						        }
						        CloseCon($p);
						}
                		elseif (isset($_GET["Person_ID"])) {
            				$conn = OpenCon();
                			$user_id = $_GET["Person_ID"];
                			printUppdateraForm($user_id, $conn, "get");
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
							}
							CloseCon($conn);
						}
					?>
				</form>
			</div>
		</div>
	</body>
</html>