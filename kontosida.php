<?php 
    include 'ServerCommunication.php'; 
    session_start();

    $info = array("email", "town", "postnum", "addrnum", "telnum");

    //vid uppdatering
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $info[0] = $_POST['mail'];
        $info[1] = $_POST['town'];
        $info[2] = $_POST['postnum'];
        $info[3] = $_POST['addrnum'];
        $info[4] = $_POST['telnum'];

        //skapa databas connection
        $p=OpenCon();  

        $uname = $_SESSION["user"];

        if(isUnique($info, $p))
        {
            $account = "UPDATE konto SET Mail='$info[0]', Stad='$info[1]', Postnummer='$info[2]', Address='$info[3]', Telefonnummer='$info[4]' 
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
                echo "Error: " . $account . "<br>" . $p->error;
            }
        }
    }

    function isUnique($info, $p)//kollar om email är unik
    {
        $email_query = "SELECT EMAIL FROM `konto` WHERE EMAIL = '$info[0]'";
        if ($p->query($email_query)->num_rows > 0)
        {
            return false;
        } 
        else{
            return true;
    }
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
            <div id="kolumn">
                
                <form action="<?php $_PHP_SELF ?>" method="post">
                <p id="headline">Kontouppgifter</p>
                        <label for="email"><b>Email</b></label>
                        <input type="email" value="<?php echo $_SESSION["email"] ?>" name="mail" required>

                        <div id="uppg">
                        <label for="town"><b>Stad</b></label>
                        <input type="text" value="<?php echo $_SESSION["town"] ?>" name="town" required>
                        </div>

                        <div id="uppg">
                        <label for="post"><b>Postnummer</b></label>
                        <input type="number" value="<?php echo $_SESSION["pnr"] ?>" name="postnum" required>
                        </div>

                        <div id="uppg">
                        <label for="addr"><b>Address</b></label>
                        <input type="text" value="<?php echo $_SESSION["addr"] ?>" name="addrnum" required>
                        </div>

                        <div id="uppg">
                        <label for="tel"><b>Telefonnummer</b></label>
                        <input type="telnum" value="<?php echo $_SESSION["telnr"] ?>" name="telnum" required>
                        </div>
                        
                        <button type="submit">Uppdatera</button>
                </form>
                
		    </div>

            <div id="kolumn">
                <p>Här ska tidigare köp visas</p>

            </div>
        </div>     
	</body>
</html>