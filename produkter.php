<?php
include 'ServerCommunication.php'; 
session_start();
//select,update,delete,insert needed

    function OutputProducts($sql,$p)
    {
    	$result = $p->query($sql);
    	if($result->num_rows > 0) 
    	{
   			return $result;
   		}
   		return null;
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="produkter.css">
</head>
	<body>
	<?php include_once 'navbar.php'; ?>
	<?php 
		$sql_query1 ="SELECT * FROM `produkt`"; // hämta all produkt info
   		$p=OpenCon(); // skapar ett connection objekt
   		$q=Outputproducts($sql_query1,$p);
   		echo '<div id="topPadding"></div>';
   		if($q != null) 
   		  {	
   			while($row = $q->fetch_assoc())
   			{
   				$s=$row["Produktnamn"]; 
   				echo '<a href="produkter.php?produkt='."$s".'">'."$s".'</a><br>';
   				
   			}
   				   		}
   		if(isset($_SESSION["user"])){
        If($_SESSION["user"] == "admin"){
   			echo '<button onclick="updateform()"type="button"> Lägg till</button>';
   			//skapa ny databas produkt form
        }
    }
   	CloseCon($p);
   	$s=$_GET['produkt'];
	?>
<script>
function updateform() {
  document.getElementById("demo").innerHTML = "Hello World";
			}
</script>

	</body>
</html>