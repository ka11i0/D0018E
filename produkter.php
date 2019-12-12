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
            $query4="INSERT INTO `kommentarer` (`Kommentar_ID`, `Person_ID`, `Produkt_ID`, `kommentar`, `Datum`) VALUES ('$Kom_id', '$user_idd', '$prod_idd', '$g', CURRENT_DATE())";
            $p->query($query4);
            header('Location: produkter.php?produkt='."$prod".''); //annars om du refresha uppdateras sidan om igen för för get requesten är kvar i headern.
        }
      }     
  else
    {
    echo "<script type='text/javascript'>alert('Du måste vara inloggad för att kunna kommentera och betygsätta varan');</script>";
    }
}
CloseCon($p);
}

function uppdaterarating(){
$p=OpenCon(); 
if (isset($_GET['rate'])) {
  if (isset($_SESSION["user"])) 
    { 
            $a=$_GET['rate'];
            $prod = $_GET['produkt'];
            $s=$_SESSION["user"];
            $prod_query = "SELECT Produkt_ID FROM produkt WHERE Produktnamn='$prod'";
            $userid_query = "SELECT Person_ID FROM konto WHERE Namn='$s'";
            $prod_result = $p->query($prod_query)->fetch_assoc();
            $userid_result = $p->query($userid_query)->fetch_assoc();
            $user_idd = $userid_result["Person_ID"];
            $prod_idd = $prod_result["Produkt_ID"];
            $quant_query = "SELECT rating FROM rating WHERE Person_ID='$user_idd' AND Produkt_ID='$prod_idd'";
       	    $quant_result = $p->query($quant_query);
            if(mysqli_num_rows($quant_result)>0){	
            	$query4= "UPDATE `rating` SET `rating`='$a' WHERE Person_ID='$user_idd' AND Produkt_ID='$prod_idd'";
            }
            else{
            	$query4= "INSERT INTO `rating` (`Person_ID`, `Produkt_ID`, `rating`) VALUES ('$user_idd', '$prod_idd', '$a')";
            }
            $p->query($query4); 
            header('Location: produkter.php?produkt='."$prod".''); //annars om du refresha uppdateras sidan om igen för för get requesten är kvar i headern.
      }     
}
CloseCon($p);
}

function Tabortochredigerakommentar()
{
$p=OpenCon(); 

 
if(isset($_GET['kommentarstatus']) && $_GET['kommentarstatus'] == "kommentarbort")
  {
    $åäöm =$_GET['KomID'];   
    $squery = "DELETE FROM `kommentarer` WHERE `kommentarer`.`Kommentar_ID` ='{$åäöm}' ";
    $p->query($squery);
    $äöå=$_GET['produkt'];
    header('Location: produkter.php?produkt='."$äöå".''); 
  }
  if(isset($_GET['uppdatecomment']) )
  {
     $nytext =$_GET['uppdatecomment'];  
     $KOMID3 =$_GET['KomID90'];  
     echo "$KOMID3";
     $squery = "UPDATE `kommentarer` SET `kommentar` = '{$nytext}',`Datum` = CURRENT_DATE() WHERE `kommentarer`.`Kommentar_ID` = '{$KOMID3}'";
     $p->query($squery);
     $äöå=$_GET['produkt'];
     header('Location: produkter.php?produkt='."$äöå".''); 
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
function läggtillochuppdateraprodukt(){
$p=OpenCon();
$info = array('Produktnamn','Img_filsökväg','Pris','Saldo','Produktbeskrivning','funk',);
if(CheckPOST($info))
{	  
	  $info[0] = $_POST['Produktnamn'];
	  $info[1] = $_POST['Img_filsökväg'];
      $info[2] = $_POST['Pris'];
	  $info[3] = $_POST['Saldo'];
	  $info[4] = $_POST['Produktbeskrivning'];
	  $val = $_POST['funk'];
	  if($val == 1)
	  {
		  $id = nextprodId($p);
	      $squery = "INSERT INTO produkt(Produktnamn,Produkt_ID,Img_filsökväg,Pris,Saldo,Produktbeskrivning) VALUES ('$info[0]','$id','$info[1]','$info[2]','$info[3]','$info[4]')";
		   if(!($p->query($squery)))
		   	{
	      		echo "<script type='text/javascript'>alert('kan inte läggas till då produkten redan finns uppdatera den istället');</script>";
	  	 	}
  	  }
  	  else
  	  {		
  	  		$id = $_POST['id'];
  	  		$squery = "UPDATE `produkt` SET `Produktnamn` = '$info[0]', `Img_filsökväg` = '$info[1]', `Pris` = '$info[2]', `Saldo` = '$info[3]', `Produktbeskrivning` = '$info[4]' WHERE `produkt`.`Produkt_ID` = '$id';";
  	  		$p->query($squery);
  	  		$procent3 = $_POST['reaprocent'];			 	
  	  }
}
CloseCon($p);
}

function nextKampanjId($p){
  $id_query = "SELECT MAX(Kampanj_ID) FROM kampanj";
  $result = $p->query($id_query)->fetch_assoc();
  if ($result['MAX(Kampanj_ID)'] !=NULL) {
  	return $result['MAX(Kampanj_ID)'] + 1;
  }
  return 0;
}
function hanterakampanj()
{
$p=OpenCon();
$info = array('Produkter','reaprocent','Start','Slut');
if(CheckPOST($info)){
 	  $info[0] = $_POST['Produkter'];
 	  $nyid=nextKampanjId($p);
	  $info[1] = $_POST['reaprocent'];
      $info[2] = $_POST['Start'];
	  $info[3] = $_POST['Slut'];
	  foreach($info[0] as $proddddu){
	  	$nyid=nextKampanjId($p);
	  	$sql22="SELECT Produkt_ID FROM produkt WHERE Produktnamn='$proddddu'";
	  	$prod_result = $p->query($sql22)->fetch_assoc();
        $prodiddd = $prod_result["Produkt_ID"];
	  	$sqlll="INSERT INTO `kampanj` (`Kampanj_ID`, `Produkt_ID`, `Procent`, `Start`, `Slut`) VALUES ('$nyid', '$prodiddd', '$info[1]', '$info[2]', '$info[3]');";
	  	$p->query($sqlll);
	  }
}
CloseCon($p);
}

hanterakampanj();
Tabortochredigerakommentar();
hanteravarukorg();
uppdaterakommentarer();
uppdaterarating();
tabortprodukt();
läggtillochuppdateraprodukt();
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
	<div id="produktlänkar"><h id="varulista">Varulista</h><br><br>
	<?php 
		$p=OpenCon();
		$sql_query1 ="SELECT * FROM `produkt`"; // hämta all produkt info
   		$q=Outputproducts($sql_query1,$p);
   		$allID = array("toggla","toggla87");
   		$allaproduktnamn=array();
   		if($q != null) 
   		{	
   			while($row = $q->fetch_assoc())
   			{
   				$s=$row["Produktnamn"]; 
   				$id=$row["Produkt_ID"];
   				$a=$row["Img_filsökväg"];
   		  		$b=$row["Pris"];
   		 	    $c=$row["Saldo"];
   		  		$d=$row["Produktbeskrivning"];
   		  		$reainfo="";
   		  		$procent="";
   		  		if(currentKampanj($id)){
   		  			$procent=currentKampanj($id);
   		  			$reainfo=''."$procent".'% REA JUST NU &darr;';
   		  		}
   				$allID[]=$id;
   				$allaproduktnamn[]=$s;
   				$x="";
   				if($admin){
   					$x='<a href="produkter.php?uppdatera='."$s".'"><button>Ta bort</button></a>
   					<button onclick="updateform('."$id".')"type="button" id="toggla3">Uppdatera</button>
					 <form id="'."$id".'" action="produkter.php" method="post" enctype="multipart/form-data" style="display:none;"><br>
						<label for="x"><b>varunamn</b></label>
						<input type="text" Value="'."$s".'" name="Produktnamn" required><br>
						<label for="x"><b>Produktbild</b></label>
					    <input type="text" Value="'."$a".'" name="Img_filsökväg" required><br>
						<label for="x"><b>Pris</b></label>
						<input type="text" Value="'."$b".'" name="Pris" required><br>
						<label for="x"><b>Saldo</b></label>	
						<input type="text" Value="'."$c".'" name="Saldo" required><br>
						<label for="x"><b>Produktbeskrivning</b></label>
						<input type="text" Value="'."$d".'" name="Produktbeskrivning" required><br>
						<input type="funktion" class="hidden" name="funk" value="2" />
						<input type="IDEntit" class="hidden" name="id" value="'."$id".'" />
						<button type="submit">Uppdatera</button>
					 </form>'; 
   				}
   				echo '<div class="reatext">'."$reainfo".'</div><a class="produktinf" href="produkter.php?produkt='."$s".'">'."$s".'</a> '."$x".'<br><br>'; //kanske ta bort space och ha produktbild istället
   			}

   		}
   	?>	
<button onclick="updateform('toggla')"type="button" id="toggla2">Ny vara</button>
 <form id="toggla" action="produkter.php" method="post" enctype="multipart/form-data"><br>
	<label for="x"><b>varunamn</b></label>
	<input type="text" placeholder="Välj namn" name="Produktnamn" required><br>
	<label for="x"><b>Produktbild</b></label>
    <input type="text" placeholder="Ange filsökväg" name="Img_filsökväg" required><br>
	<label for="x"><b>Pris</b></label>
	<input type="text" placeholder="Välj ett pris" name="Pris" required><br>
	<label for="x"><b>Saldo</b></label>
	<input type="text" placeholder="Saldo" name="Saldo" required><br>
	<label for="x"><b>Produktbeskrivning</b></label>
	<input type="text" placeholder="beskriv produkten" name="Produktbeskrivning" required><br>
	<input type="funktion" class="hidden" name="funk" value="1" />
	<button type="submit">Lägg till</button>
 </form>

<button onclick="updateform('toggla87')"type="button" id="toggla4"> NyKampanj</button>
	 <form id="toggla87" action="produkter.php" method="post" enctype="multipart/form-data" style="display:none;"><br>
		<label for="x"><b>Välj produkter</b></label> 
	    <select name="Produkter[]" multiple="multiple">
	    	<?php foreach ( $allaproduktnamn as $tgg){
		echo '<option value="'."$tgg".'">'."$tgg".'</option>';} ?>
		</select><br>
		<label for="x"><b>Reaprocentsats</b></label>
		 <input type="text" placeholder="XX%"  name="reaprocent"><br>
		<label for="start">Startdatum:</label>
		 <input type="date" id="start" name="Start"placeholder="yyyy-mm-dd"min="<?php $gggg=date("Y-m-d"); echo "$gggg"; ?>" max="<?php echo date('Y-m-d', strtotime(' + 7 days')) ?>"required><br>	
		<label for="x"><b>Slutdatum:</b></label>
		 <input type="date" id="start" name="Slut" placeholder="yyyy-mm-dd" min="<?php echo date('Y-m-d', strtotime(' + 7 days')) ?>" max="<?php echo date('Y-m-d', strtotime(' + 30 days')) ?>" required><br>
		<button type="submit">Skapa</button>
	 </form>	
</div>
<script> document.getElementById("toggla").style.display = "none"</script>
<script> document.getElementById("toggla87").style.display = "none"</script>
<div id="bildotext">
<script>
function updateform(which) 
{ 
var g = which;
var IDs= <?php echo json_encode($allID);?>;
for(var i=0;i<IDs.length;i++)
{		
	if(IDs[i]==g)
	{
		var x = document.getElementById(g);
		if (x.style.display === 'none') 
			{
				x.style.display = 'block';
			} 
	    else 
			{
		    	x.style.display = 'none';
			}
	}	
}
}
</script>
<?php    
if(!$admin)
	{
		echo '<script> document.getElementById("toggla2").style.display = "none" </script>';
		echo '<script> document.getElementById("toggla4").style.display = "none" </script>';
    } 	


if(isset($_GET['produkt']))
   		{
   		  $s=$_GET['produkt'];
   		  $sql_query9 ="SELECT AVG(rating) AS AVG FROM rating WHERE Produkt_ID=(SELECT Produkt_ID from produkt where Produktnamn='{$s}')";
   		  $sql_query2 ="SELECT * FROM `produkt`WHERE Produktnamn='{$s}' "; // hämta all produkt info
   		  $sql_query89 ="SELECT COUNT(rating) AS ANTAL FROM rating WHERE Produkt_ID=(SELECT Produkt_ID from produkt where Produktnamn='{$s}');";
   		  $antalrating = $p->query($sql_query89)->fetch_assoc();
   		  $antal= $antalrating["ANTAL"];
   		  $q=Outputproducts($sql_query2,$p);
   		  $row = $q->fetch_assoc();
   		  $a=$row["Img_filsökväg"];
   		  $b=$row["Pris"];
   		  $c=$row["Saldo"];
   		  $d=$row["Produktbeskrivning"];
          $f=$row["Produkt_ID"];
          $text4 = 'Pris: $'."$b".'.0';
          if(currentKampanj($f))
          {
   		  		$procent2=currentKampanj($f);
   		  		$nypris= $b - ($b * 0.01 * $procent2); 
   		  		$text4 = 'Ordinarie Pris: $'."$b".' Reapris: $'."$nypris".'';
   		  }
          $res = $p->query($sql_query9)->fetch_assoc();
          $z=$res["AVG"];
          if($z==null){
          	$z=0;
          }
   		  $z=round($z);

   		  echo '<div id="omProdukt">'."$s".'<br><br>'."$text4".'
			<br><br>
			I lager: '."$c".' St<br><br>
			'."$d".'<br><br> <div id="star">★</div><div id="grade">'."$z".'/5 betygsatt av '."$antal".' personer</div><br><br>
			<form id="kop" method="post">
				Antal:
				<input type="number" name="amount" min="1" value="1">
                <input type="hidden" name="produkt" value="'."$s".'">

				<button type="submit">Lägg till i kundkorg</button>
			</form>
		</div>';
    include_once 'kommentarer.php';
    } 
 ?>
</div>
<?php 
  CloseCon($p);
?>


</body>
</html>