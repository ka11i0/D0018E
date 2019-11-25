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

	function nextprodId($p){
    $id_query = "SELECT MAX(Produkt_ID) FROM produkt";
    $result = $p->query($id_query)->fetch_assoc();
    return $result['MAX(Produkt_ID)'] + 1;
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
		$p=OpenCon(); 
		$admin=false;
		if(isset($_SESSION["user"]))
   		{
        	If($_SESSION["user"] == "admin")
        		{
        			$admin=true;
        		}
        }

		$info = array('Produktnamn','Img_filsökväg','Pris','Saldo','Produktbeskrivning');
   	    if(CheckPOST($info))
   	    {
 		      $info[0] = $_POST['Produktnamn'];
   			  $info[1] = $_POST['Img_filsökväg'];
    	      $info[2] = $_POST['Pris'];
   			  $info[3] = $_POST['Saldo'];
   			  $info[4] = $_POST['Produktbeskrivning'];
   	    	  $id = nextprodId($p);
        	  $squery = "INSERT INTO produkt(Produktnamn,Produkt_ID,Img_filsökväg,Pris,Saldo,Produktbeskrivning) VALUES ('$info[0]','$id','$info[1]','$info[2]','$info[3]','$info[4]')";
       		  $p->query($squery);
   	    }

   	    if(isset($_GET['uppdatera']))
   	    {		
   	    	  $ä=$_GET['uppdatera'];
   	    	  $squery = "DELETE FROM produkt WHERE Produktnamn='{$ä}';";
       		  $p->query($squery);	
   	    }



		$sql_query1 ="SELECT Produktnamn FROM `produkt`"; // hämta all produkt info
   		$p=OpenCon(); // skapar ett connection objekt
   		$q=Outputproducts($sql_query1,$p);
   		echo '<div id="topPadding"><br>';
   		if($q != null) 
   		{	
   			while($row = $q->fetch_assoc())
   			{
   				$s=$row["Produktnamn"]; 
   				$x="";
   				if($admin){
   					$x='<a href="produkter.php?uppdatera='."$s".'"><button>Ta bort</button></a>';
   				}
   				echo '<a href="produkter.php?produkt='."$s".'">'."$s".'</a> &nbsp;'."$x".'<br><br>'; //kanske ta bort space för bild istället
   			}
   		}

        if($admin)
        	{
   				echo 
   				 '
   				   <button onclick="updateform()"type="button">Ny vara</button>
   				   	<form id="toggla" action="produkter.php" method="post"><br>
			  			<label for="x"><b>varunamn</b></label>
			  			<input type="text" placeholder="Välj namn" name="Produktnamn" required><br>
			  			<label for="x"><b>Bildsökväg</b></label>
			  			<input type="text" placeholder="filepath här" name="Img_filsökväg" required><br>
		  				<label for="x"><b>Pris</b></label>
			  			<input type="text" placeholder="Välj ett pris" name="Pris" required><br>
			  			<label for="x"><b>Saldo</b></label>
			  			<input type="text" placeholder="Saldo" name="Saldo" required><br>
			  			<label for="x"><b>Produktbeskrivning</b></label>
			  			<input type="text" placeholder="välj din beskrivning på produkten" name="Produktbeskrivning" required><br>

                 		<button type="submit">Lägg till</button>
                 	</form>
                   </div>

                 <script>
  				  document.getElementById("toggla").style.display = "none";
				 </script>';
   					//skapa ny databas produkt form
       		 }
    	else
    		{
    			echo '</div>';
    		}


   		if(isset($_GET['produkt']))
   		{
   		  $s=$_GET['produkt'];
   		  $sql_query2 ="SELECT * FROM `produkt`WHERE Produktnamn='{$s}' "; // hämta all produkt info
   		  $q=Outputproducts($sql_query2,$p);
   		  $row = $q->fetch_assoc();
   		  $a=$row["Img_filsökväg"];
   		  $b=$row["Pris"];
   		  $c=$row["Saldo"];
   		  $d=$row["Produktbeskrivning"];
   		  
   		  echo '<div id="omProdukt">'."$s".'<br><br>
			Pris: $'."$b".'.00<br><br>'
			."$d".'<br>
			<form id="kop">
				Antal:
				<input type="number" name="amount" min="1" value="1">
				<button>Lägg till i kundkorg</button>
			</form>
		</div>
		<div id="prodImg">
			<img src="">
		</div>
		</div>';
  	 	}

	CloseCon($p);
	?>
<script>
function updateform() { //ica basic
  var x = document.getElementById("toggla");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}
			
</script>

	</body>
</html>