<?php
include 'ServerCommunication.php'; 
//include 'kommentarer.php';
session_start();

$admin=false;

if(isset($_SESSION["user"]))
   		{
        	If($_SESSION["user"] == "admin")
        		{
        			$admin=true;
        		}
        }

function hanteravarukorg()
{
$p=OpenCon(); 
$info1 = array('produkt','amount');
if (CheckPOST($info1)) {
    if (isset($_SESSION["user"])) {
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
    }
    else {
        echo "<script type='text/javascript'>alert('Logga in för att börja handla.');</script>";
    }
}
CloseCon($p);
}  
function uppdaterakommentarer(){
$p=OpenCon(); 
if (isset($_GET['comment'])) {
  if (isset($_SESSION["user"])) 
    {
      if ($_GET['comment']!="") {
            $g=$_GET['comment'];
            $Kom_id=nextCommentId($p);
            $prod = $_GET['produkt'];
            $s=$_SESSION["user"];
            $prod_query = "SELECT Produkt_ID FROM produkt WHERE Produktnamn='$prod'";
            $userid_query = "SELECT Person_ID FROM konto WHERE Namn='$s'";
            $prod_result = $p->query($prod_query)->fetch_assoc();
            $userid_result = $p->query($userid_query)->fetch_assoc();
            $user_idd = $userid_result["Person_ID"];
            $prod_idd = $prod_result["Produkt_ID"];
            $query3= "INSERT INTO `kommentarer` (`Kommentar_ID`, `Person_ID`, `Produkt_ID`, `kommentar`, `Datum`) VALUES ('$Kom_id', '$user_idd', '$prod_idd', '$g', CURRENT_DATE())";
            $p->query($query3);
            header('Location: produkter.php?produkt='."$prod".''); //annars om du refresha uppdateras sidan om igen för för get requesten är kvar i headern.
        }
      }     
  else
    {
    echo "<script type='text/javascript'>alert('Du måste vara inloggad för att kunna kommentera');</script>";
    }
}
CloseCon($p);
}
function tabortprodukt(){
$p=OpenCon();
if(isset($_GET['uppdatera']))
{		
	  $ä=$_GET['uppdatera'];
	  $squery = "DELETE FROM produkt WHERE Produktnamn='{$ä}';";
	  if ($p->query($squery)) {
      echo "<script type='text/javascript'>alert('Produkten finns inte längre');</script>";
    }	
}
CloseCon($p);
}
function läggtillprodukt(){
$p=OpenCon();
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
CloseCon($p);
}
/*
function uploadfile(){ //yoink
echo exec('whoami'); 
$target_dir = "";
$target_file = $target_dir . basename($_FILES["Img_filsökväg"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["Img_filsökväg"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["Img_filsökväg"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["Img_filsökväg"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["Img_filsökväg"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
}

uploadfile();
*/
hanteravarukorg();
uppdaterakommentarer();
tabortprodukt();
läggtillprodukt();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="produkter.css">
</head>
	<body>
	<?php include_once 'navbar.php'; ?>
	<div id="topPadding"></div>
	<div id="main">
	<div id="produktlänkar">
	<?php 
		$p=OpenCon();
		$sql_query1 ="SELECT Produktnamn FROM `produkt`"; // hämta all produkt info
   		$q=Outputproducts($sql_query1,$p);
   		if($q != null) 
   		{	
   			while($row = $q->fetch_assoc())
   			{
   				$s=$row["Produktnamn"]; 
   				$x="";
   				if($admin){
   					$x='<a href="produkter.php?uppdatera='."$s".'"><button>Ta bort</button></a>'; //lägg till här
   				}
   				echo '<a href="produkter.php?produkt='."$s".'">'."$s".'</a> &nbsp;'."$x".'<br><br>'; //kanske ta bort space och ha produktbild istället
   			}
   		}
   	?>	
   <button onclick="updateform()"type="button" id="toggla2">Ny vara</button>
	<form id="toggla" action="produkter.php" method="post" enctype="multipart/form-data"><br>
	<label for="x"><b>varunamn</b></label>
	<input type="text" placeholder="Välj namn" name="Produktnamn" required><br>
	<label for="x"><b>Produktbild</b></label>
    <input type="file" name="Img_filsökväg"><br>
	<label for="x"><b>Pris</b></label>
	<input type="text" placeholder="Välj ett pris" name="Pris" required><br>
	<label for="x"><b>Saldo</b></label>
	<input type="text" placeholder="Saldo" name="Saldo" required><br>
	<label for="x"><b>Produktbeskrivning</b></label>
	<input type="text" placeholder="beskriv produkten" name="Produktbeskrivning" required><br>
	<button type="submit">Lägg till</button>
</form>
</div>
<script> document.getElementById("toggla").style.display = "none" </script>
<div id="bildotext">
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
<?php    	
if(!$admin)
	{
		echo '<script> document.getElementById("toggla2").style.display = "none" </script>';
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
        $f=$row["Produkt_ID"];
   		  
   		  echo '<div id="omProdukt">'."$s".'<br><br>
			Pris: $'."$b".'.00<br><br>'
			."$d".'<br><br>
			Genomsnittlig rating: 3.5/5 icon<br><br>
			<form id="kop" method="post">
				Antal:
				<input type="number" name="amount" min="1" value="1">
                <input type="hidden" name="produkt" value="'."$s".'">

				<button type="submit">Lägg till i kundkorg</button>
			</form>
		</div>		
		<div id="prodImg">
			<img src="">
		</div>';
    include_once 'kommentarer.php';
    } 
 ?>
</div>
</div>
<?php 
  CloseCon($p);
?>


</body>
</html>