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
					}
					else{
						echo "Logga in/Registrera";	
					}
				?>
			</u></a></li>
			</ul>
		</nav>
		<div id="topPadding"></div>
		<nav id="prodList">
			<ul>
				<li id="prod1">Snus1</li>
				<li id="prod2">Snus2</li>
				<li id="prod3">Snus3</li>
				<li id="prod4">Snus4</li>
			</ul>
		</nav>
		<div id="omProdukt">
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus dolor mi, varius ut sodales vitae, consequat ultrices felis. Etiam tincidunt interdum vehicula. Fusce vel justo lorem. Mauris diam lectus, sagittis vitae risus in, tristique scelerisque neque. In tincidunt egestas dolor, at auctor erat. Maecenas et faucibus eros. Mauris tristique bibendum ante, vitae eleifend libero commodo vitae. Curabitur at arcu eget risus mollis laoreet in non est. Donec fermentum dignissim risus, aliquam dignissim ex consequat bibendum. Ut mauris tellus, malesuada a lectus quis, sodales accumsan ex. Nulla lacinia accumsan turpis, a consectetur est scelerisque aliquet.
			<br><br>
			In hac habitasse platea dictumst. Aliquam auctor mollis libero, id dapibus quam gravida non. Suspendisse et ipsum neque. Aenean ut velit vitae justo vulputate mollis. Vivamus ultricies gravida sapien, eu varius augue ullamcorper non. Donec iaculis nunc in posuere pulvinar. Aliquam eget pulvinar urna, eu tincidunt sem. Duis et finibus orci, ut pretium sem. Nulla viverra aliquet nibh, in semper nisi congue eu. Suspendisse potenti.
			<form id="kop">
				InsertPrisHere
				<input type="number" name="amount" min="1" value="1">
				<button>Lägg till i kundkorg</button>
			</form>
		</div>
		<div id="prodImg">
			<img src="">
		</div>
	</body>
</html>