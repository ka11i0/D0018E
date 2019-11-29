
<?php //css för kommentarer finns i produkter.css

		$s=$_GET['produkt'];
		$sql_query5 ="SELECT * FROM (SELECT konto.Namn, kommentarer.kommentar, kommentarer.Datum, kommentarer.Produkt_ID FROM kommentarer INNER JOIN konto ON konto.Person_ID=kommentarer.Person_ID) as alias where Produkt_ID='{$f}'"; //$f finns i produkter.php
		 // hämta relevant all produkt info
   		$q=Outputproducts($sql_query5,$p);
   		if($q != null) 
   		{	
   			while($row = $q->fetch_assoc())
   			{	
   			 $b=$row["Namn"];
   		 	 $c=$row["kommentar"];
   		  	 $d=$row["Datum"];
   		  	 if($b=="admin"){
   		  	 	$x="admin.png";
   		  	 }
   		  	 else{
   		  	 	$x="user.png";
   		  	 }

   				echo '
   				<div id="Endakommentar" >
				<div class="namnicon">
				    <img src="'.$x.'" class="img1">
				    <span class="caption">'.$b.'</span>
				</div>
				<div class="textkommentar">
				 '.$c.'
				<div id=datum>'.$d.' </div></div><br>
				</div> ';	
   			}
   		}
   	?>	

<div id="nykommentar">
<form action="produkter.php" method="get" id="usrform">
<div class="namnicon">
 	<img src="<?php if(isset($_SESSION["user"]) and $_SESSION["user"] =="admin") 
   		{	
    		echo"admin.png";		
   		}
    	else{
    		 echo"user.png";
    		} ?>"
    		 class="img1"><span class="caption"><?php if(isset($_SESSION["user"])) {$m=$_SESSION["user"]; echo"$m"; } else{echo"user";} ?> </span>
</div>
<textarea id="kommetararea" rows="4" cols="50" name="comment" form="usrform"></textarea><br>
<textarea class="hidden" rows="4" cols="50" name="produkt" form="usrform"><?php echo"$s";?></textarea><br>
<input type="submit" id="submitknapp">

</form>
</div>