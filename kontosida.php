<?php 
    include 'ServerCommunication.php'; 
    session_start();
    $person_id = $_SESSION["id"];
    $info = array("email", "town", "postnum", "addrnum", "telnum");

    //vid uppdatering
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $info[0] = $_POST['mail'];
        $info[1] = $_POST['town'];
        $info[2] = $_POST['postnum'];
        $info[3] = $_POST['addrnum'];
        $info[4] = $_POST['telnum'];
        $info[5] = $_POST['password'];

        //skapa databas connection
        $p=OpenCon();  

        $uname = $_SESSION["user"];

        
        $account = "UPDATE konto SET Lösenord='$info[5]', Mail='$info[0]', Stad='$info[1]', Postnummer='$info[2]', Address='$info[3]', Telefonnummer='$info[4]' 
        WHERE `Namn` = '$uname'";
        if ($p->query($account)) {
            //uppdatera session info
            $_SESSION["email"]=$_POST['mail'];
            $_SESSION["town"]=$_POST['town'];
            $_SESSION["pnr"]=$_POST['postnum'];
            $_SESSION["addr"]=$_POST['addrnum'];
            $_SESSION["telnr"]=$_POST['telnum'];
            echo "<script type='text/javascript'>alert('Dina kontouppgifter har blivit uppdaterade');</script>";
        }
        else {
            echo "<script type='text/javascript'>alert('Den emailen är redan tagen');</script>";
        }   
        CloseCon($p);
    }


?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="produkter.css">
    <link rel="stylesheet" type="text/css" href="kontosida.css">
</head>
	<body>
        <?php include_once 'navbar.php'; ?>
        <div id="box">
        <?php
            $conn = OpenCon();
            printUppdateraForm($_SESSION["id"], $conn, "post");
            printHistorik($_SESSION["id"], $conn);
            CloseCon($conn);
        ?>
        </div>
	</body>
</html>