
<?php
	include 'ServerCommunication.php'; 
	session_start();
    function LoginStatus($sql_query1,$p)
    {
    	$s=UserCheck($sql_query1,$p); 
    	if($s!=0)
    	{
	 		$_SESSION["user"]=$s["Namn"];
	 		header('Location: produkter.php'); 
	    }
 		else 
	 	{
	 		echo "Användaren eller Lösenordet är fel!";
	 	}
    }

//main 
  	$info = array('uname','psw');
    if(CheckPOST($info)){
		$psw = $_POST['psw']; //ta bort
		$uname = $_POST['uname'];
    	$sql_query1 ="SELECT Namn,Lösenord FROM `konto` WHERE 
		Namn='{$uname}' AND Lösenord='{$psw}'";
    	$p=OpenCon(); // skapar ett connection objekt
   		LoginStatus($sql_query1,$p);
    	CloseCon($p);
    }
    
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="produkter.css">
	<link rel="stylesheet" type="text/css" href="login.css">
</head>
	<body>
		<?php include_once 'navbar.php'; ?>
		<form action="<?php $_PHP_SELF ?>" method="post">
		  
			<div class="container">
			  <label for="uname"><b>Användarnamn</b></label>
			  <input type="text" placeholder="Skriv ditt användarnamn" name="uname" required>
		  
			  <label for="psw"><b>Lösenord</b></label>
			  <input type="password" placeholder="Skriv ditt lösenord" name="psw" required>
		  
			  <button type="submit">Logga in</button>
			  <div>
			  		<span style="float:left"> <a href="nyttkonto.php">Skapa nytt konto</a></span>
                    <span style="float:right"> <a href="#">Glömt lösenord?</a></span>
              </div>
			</div>
		  
			
		</div>
	</body>
</html>