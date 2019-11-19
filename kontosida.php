<?php 
    include 'ServerCommunication.php'; 
    session_start(); 


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
                        <input type="email" placeholder="Välj en ny email" name="mail" required>

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
                        
                        <button type="submit">Uppdatera</button>
                </form>
		    </div>

            <div id="kolumn">
                <p>Här ska tidigare köp visas</p>

            </div>
        </div>     
	</body>
</html>