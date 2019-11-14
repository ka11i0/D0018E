<?php 
session_start(); 
include 'ServerCommunication.php'; 





function CheckIfTaken($currentconnection,$info) //kollar med databasen ifall det info du fyller i är taget
{
$msg = ""
for($x = 0; $x < $count(info); $x++) 
{
	$sql_query ="SELECT $info[$x] FROM 'konto' WHERE Namn='{$_POST[$info[$x]]}'" ;
 	$userinfo=UserCheck($sql_query,$currentconnection);
	if( != 0);
		 	$msg .="{$info[$x]} ";
}
return $msg;
}


$info = array("uname", "psw", "email", "date", "town", "country");
if(CheckPOST($info))
{
 	$p=OpenCon(); // skapar ett connection objekt p
 	$t=CheckIfTaken($p,$info)
    CloseCon($p);
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
                <input type="password" placeholder="Välj ett användarnamn" name="psw" required>
                
                <label for="email"><b>Email*</b></label>
                <input type="email" placeholder="Fyll i din email" name="mail" required>

                <div id="uppg">
                <label for="date"><b>Födelsedatum*</b></label>
                <input type="date" name="date" required>
                </div>

                <div id="uppg">
                <label for="town"><b>Stad</b></label>
                <input type="text" placeholder="">
                </div>

                <div id="uppg">
                <label for="post"><b>Postnummer</b></label>
                <input type="number" placeholder="">
                </div>


                <div id="uppg">
                <label for="addr"><b>Address</b></label>
                <input type="text" placeholder="">
                </div>


                <div id="uppg">
                <label for="tel"><b>Telefonnummer</b></label>
                <input type="telnum" placeholder="">
                </div>
                
                
                <button type="submit">Registrera</button>
			</div>
		  
			
		</div>
	</body>
</html>