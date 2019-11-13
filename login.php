
<?php
	include 'ServerCommunication.php'; 
	session_start();
	

	function CheckPOST($info)
	{
	$counter=0;
	for($x = 0; $x < count($info); $x++) {
 	 if(isset($_POST[$info[$x]])) 
  		{ 
		$counter++;
		//$info[$x] = $_POST[$info[$x]];
    	}
	}	
	if($counter==count($info)){
	return true;
	}
	return false;
	}
	
	
	function UserCheck($sql_query1,$currentconnection)
	{
	 	$result = $currentconnection->query($sql_query1); 
	 	if ($result->num_rows > 0) //vi har dock bara en rad 
	 	{ //användaren finns ifall vilkoret är sant
	 		$row = $result->fetch_assoc(); 
	 		$_SESSION["user"]=$row["Namn"];
	 		header('Location: produkter.php'); 
	    }
 	else 
	 	{
	 		echo "Användaren eller Lösenordet är fel!";
	 	}
 	
    }
  	$info = array('uname','psw');
    if(CheckPOST($info)){
		$psw = $_POST['psw']; //ta bort
		$uname = $_POST['uname'];
    	$sql_query1 ="SELECT Namn,Lösenord FROM `konto` WHERE 
		Namn='{$uname}' AND Lösenord='{$psw}'";
    	$p=OpenCon(); // skapar ett connection objekt
    	UserCheck($sql_query1,$p);
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
			  <label>
				<input type="checkbox" checked="checked" name="remember"> Kom ihåg användarnamn
			  </label>
			  <div>
			  		<span style="float:left"> <a href="nyttkonto.php">Skapa nytt konto</a></span>
                    <span style="float:right"> <a href="#">Glömt lösenord?</a></span>
              </div>
			</div>
		  
			
		</div>
	</body>
</html>