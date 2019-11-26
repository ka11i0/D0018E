<?php
include 'ServerCommunication.php'; 
session_start();
//select,update,delete,insert needed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION["user"])) {
        $p=OpenCon();
        //få antalet, produkt namn, användare id
        $prod = $_POST['produkt'];
        $quant = $_POST['amount'];
        $user_id = $_SESSION["id"];
        //översätta produkt namn till dess id
        $prod_query = "SELECT Produkt_ID FROM produkt WHERE Produktnamn='$prod'";
        $prod_result = $p->query($prod_query)->fetch_assoc();
        $prod = $prod_result["Produkt_ID"];
        //få fram om produkten redan finns i varukorg tabellen
        $quant_query = "SELECT quantity FROM varukorg WHERE Person_ID='$user_id' AND Produkt_ID='$prod'";
        $quant_result = $p->query($quant_query);
        //om produkten finns i tabellen updatera antalet, annars lägg in i tabellen
        if (mysqli_num_rows($quant_result)>0) {
            $quant_result=$quant_result->fetch_assoc();
            $quant = $quant_result["quantity"] + $quant;
            $varukorg_query = "UPDATE varukorg SET quantity = '$quant' WHERE Person_ID = '$user_id' AND Produkt_ID = '$prod'";
        }
        else{
            $varukorg_query = "INSERT INTO varukorg (Person_ID, Produkt_ID, quantity) VALUES ('$user_id', '$prod', '$quant')";
        }
        if ($p->query($varukorg_query)){
                echo "<script type='text/javascript'>alert('Vald vara finns nu i din varukorg.');</script>";
        }
        else{
            echo "<script type='text/javascript'>alert('".mysqli_error($p)."');</script>";   
        }
        CloseCon($p);
    }
    else {
        echo "<script type='text/javascript'>alert('Logga in för att börja handla.');</script>";
    }
}
  
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
			<form method="post">
				Antal:
				<input type="number" name="amount" min="1" value="1">
                <input type="hidden" name="produkt" value="'."$s".'">

				<button type="submit">Lägg till i kundkorg</button>
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