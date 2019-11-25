<?php 
session_start(); 
include 'ServerCommunication.php'; 

$info = array("uname", "psw", "email", "date", "town", "postnum", "addrnum", "telnum");

//när submit klickas
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    //fyll array med data från formuläret
    $info[0] = $_POST['uname'];
    $info[1] = $_POST['psw'];
    $info[2] = $_POST['mail'];
    $info[3] = $_POST['date'];
    $info[4] = $_POST['town'];
    $info[5] = $_POST['postnum'];
    $info[6] = $_POST['addrnum'];
    $info[7] = $_POST['telnum'];
    //skapa databas connection
    $p=OpenCon();
    //kolla så att användare/email är unik
    if(isUnique($info, $p))
    {
        //få id för nästa user
        $id = nextUserId($p);
        //lägg till formulär data till databasen
        $account = "INSERT INTO konto (Person_ID, Namn, Lösenord, Födelsedag, 
        privilegie, Mail, Stad, Postnummer, Address, Telefonnummer) VALUES ('$id','$info[0]','$info[1]','$info[3]','0'
        ,'$info[2]','$info[4]','$info[5]','$info[6]','$info[7]')";
        if ($p->query($account)) {//Query gick igenom, användare finns i databasen
            echo "<script type='text/javascript'>alert('Användare skapad');</script>";
        }
        else{//om query returnerar error så skriv felmeddelande ut
            echo "<script type='text/javascript'>alert('Nånting gick fel, pröva igen.');</script>";
        }
    }
    else
    {
    }

    CloseCon($p);
}
function isUnique($info, $p)
{
    $name_query = "SELECT Namn FROM `konto` WHERE Namn ='$info[0]'";
    $email_query = "SELECT EMAIL FROM `konto` WHERE EMAIL = '$info[2]'";
    if ($p->query($name_query)->num_rows > 0 && 
        $p->query($email_query)->num_rows > 0)
    {
        return false;
    } 
    else{
        return true;
    }
}
function nextUserId($p){
    $id_query = "SELECT MAX(Person_ID) FROM konto";
    $result = $p->query($id_query)->fetch_assoc();
    return $result['MAX(Person_ID)'] + 1;
}

?>
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="produkter.css">
	<link rel="stylesheet" type="text/css" href="login.css">
	<link rel="stylesheet" type="text/css" href="nyttkonto.css">
</head>
	<body>
        <?php include_once 'navbar.php'; ?>
        
		<form action="<?php $_PHP_SELF ?>" method="post">
		  
			<div class="container">
                <label for="uname"><b>Användarnamn*</b></label>
                <input type="text" placeholder="Välj ett användarnamn" name="uname" required>
            
                <label for="psw"><b>Lösenord*</b></label>
                <input type="password" placeholder="Välj ett lösenord" name="psw" required>
                
                <label for="email"><b>Email*</b></label>
                <input type="email" placeholder="Fyll i din email" name="mail" required>

                <div id="uppg">
                <label for="date"><b>Födelsedatum*</b></label>
                <input type="date" name="date" required>
                </div>

                <div id="uppg">
                <label for="town"><b>Stad</b></label>
                <input type="text" placeholder="" name="town" required>
                </div>

                <div id="uppg">
                <label for="post"><b>Postnummer</b></label>
                <input type="number" placeholder="" name="postnum" required>
                </div>


                <div id="uppg">
                <label for="addr"><b>Address</b></label>
                <input type="text" placeholder="" name="addrnum" required>
                </div>


                <div id="uppg">
                <label for="tel"><b>Telefonnummer</b></label>
                <input type="telnum" placeholder="" name="telnum" required>
                </div>
                
                
                <button type="submit">Registrera</button>
			</div>
		  
			
        </form>
	</body>
</html>