<?php
session_start(); 
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="produkter.css">
</head>
	<body>
		<nav id="navigation">
			<ul>
				<li><a href="produkter.php" class="left">Butik</a></li>
				<li><a href="custom.php" class="left">Custom Snus</a></li>
				<li><a href="support.php" class="left">Support</a></li>
				<li><a href="om.php" class="left">Om oss</a></li>
				<li><a href="varukorg.php" class="right">Varukorg</a></li>
				<li><a href="login.php" class="right"><u>
				<?php 
					if (session_status()==PHP_SESSION_ACTIVE) {
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
	</body>
</html>