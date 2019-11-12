<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="produkter.css">
	<link rel="stylesheet" type="text/css" href="login.css">
	<link rel="stylesheet" type="text/css" href="registrera.css">
</head>
	<body>
		<nav id="navigation">
			<ul>
				<li><a href="produkter.html" class="left">Butik</a></li>
				<li><a href="custom.html" class="left">Custom Snus</a></li>
				<li><a href="support.html" class="left">Support</a></li>
				<li><a href="om.html" class="left">Om oss</a></li>
				<li><a href="varukorg.html" class="right">Varukorg</a></li>
				<li><a href="login.php" class="right"><u>Logga in/Registrera</u></a></li>
			</ul>
        </nav>
        
		<form action="#" method="post">
		  
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