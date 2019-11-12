
<?php

	include 'ServerCommunication.php'; 
	session_start();
    if(isset($_POST['uname']) and isset($_POST['psw'])) //isset() checks if variable exists != null
	{ 
		$uname=$_POST['uname'];
		$psw=$_POST['psw'];
	 	$p=OpenCon(); // skapar ett connection objekt p
	 	$sql_query ="SELECT Namn,Lösenord FROM `konto` WHERE Namn='{$uname}' AND Lösenord='{$psw}'";
	 	$result = $p->query($sql_query); 
	 	if ($result->num_rows > 0) //vi har dock bara en rad 
	 	{ //användaren finns ifall vilkoret är sant
	 		$row = $result->fetch_assoc();
	 		$_SESSION["user"]=$row["Namn"];
	 		header('Location: login.php'); //redirect page after logged in skicka variabler för att vissa att personen är inloggad
	 		
	    }
 	else 
	 	{
	 		echo "Användaren eller Lösenordet är fel!";
	 	}
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
		<nav id="navigation">
			<ul>
				<li><a href="produkter.php" class="left">Butik</a></li>
				<li><a href="custom.php" class="left">Custom Snus</a></li>
				<li><a href="support.php" class="left">Support</a></li>
				<li><a href="om.php" class="left">Om oss</a></li>
				<li><a href="varukorg.php" class="right">Varukorg</a></li>
				<li><a 	
				<?php 
				if (isset($_SESSION["user"])) 
				{ print "href=logout.php";}
				else
				{print "href=login.php";} 
				?> 
				class="right"><u>
				<?php 
					if (isset($_SESSION["user"])) {
						print_r($_SESSION["user"]);
						//echo "string";
					}
					else{
						echo "Logga in/Registrera";	
					}
				?>
			</u></a></li>
			</ul>
		</nav>
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