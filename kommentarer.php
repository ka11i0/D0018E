
<?php //css för kommentarer finns i produkter.css
    
		$s=$_GET['produkt'];
		$sql_query5 ="SELECT * FROM (SELECT konto.Namn, kommentarer.kommentar,  kommentarer.Kommentar_ID, kommentarer.Datum, kommentarer.Produkt_ID FROM kommentarer INNER JOIN konto ON konto.Person_ID=kommentarer.Person_ID) as alias where Produkt_ID='{$f}'"; //$f finns i produkter.php
		 // hämta relevant all produkt info
		
   		$q=Outputproducts($sql_query5,$p);
  echo '</div><div id= "allakommentarer">
    <div id="prodImg">
      <img src="bilder/'."$a".'" id="produkten">
    </div>'; 
   		if($q != null) 
   		{	 
        
   			while($row = $q->fetch_assoc())
   			{	
         $IDkom=$row["Kommentar_ID"];
   			 $b=$row["Namn"];
   		 	 $c=$row["kommentar"];
   		  	 $d=$row["Datum"];
   		  	 if($b=="admin"){
   		  	 	$x="bilder/admin.png";
   		  	 }
   		  	 else{
   		  	 	$x="bilder/user.png";
   		  	 }
    $t = "";   
    if (isset($_SESSION["user"]) && ($b == $_SESSION["user"] || $_SESSION["user"] == "admin")) 
    {          
    $t='<div id= "edit"><form>
          <input type="text" class="hidden" name="produkt" value="'."$s".'" />
          <input type="text" class="hidden" name="kommentarstatus" value="kommentarbort" />
          <input type="text" class="hidden" name="KomID" value="'."$IDkom".'" />
          <button id="knappen7" type="submit">Ta bort</button>
        </form>
        <form>
          <input type="text" class="hidden" name="produkt" value="'."$s".'" />
          <input type="text" class="hidden" name="kommentarstatus" value="kommentarredigera" />
          <input type="text" class="hidden" name="KomID" value="'."$IDkom".'" />
          <button id="knappen2" type="submit">Redigera</button>   
      </form></div>';
    }  
  
if(isset($_GET["kommentarstatus"]) && $_GET["kommentarstatus"] == "kommentarredigera" && $IDkom ==$_GET["KomID"])
  { 
  $swap='<form action="produkter.php" method="get" id="usrform"><textarea id="redigeratextbox" rows="4" cols="45" name="uppdatecomment" form="usrform" >'.$c.'</textarea>
        <textarea class="hidden" rows="4" cols="50" name="KomID90" form="usrform">'.$IDkom.'</textarea>
  <input type="submit" id="redigeraknapp"> 
  <a href="produkter.php?produkt='.$s.'">
       <input type="button" id="cancel" value="Avbryt" />
  </a></form> ';
  $t=""; 
}
else{ 
  $swap='<div class="textkommentar">
         '.$c.'
             <div id=datum>
                '.$d.'
             </div>
        </div>';
}

echo '<div class="Endakommentar" >
				<div class="namnicon">
				    <img src="'.$x.'" class="img1">
				    <span class="caption">'.$b.'</span>
				</div>
			'.$swap.''.$t.'
</div>';	
   			}
   		}
   	?>	


<div id="nykommentar">
<form action="produkter.php" method="get" id="usrform">
<div class="namnicon">
 	<img src="<?php if(isset($_SESSION["user"]) and $_SESSION["user"] =="admin") 
   		{	
    		echo"bilder/admin.png";		
   		}
    	else{
    		 echo"bilder/user.png";
    		} ?>"
    		 class="img1"><span class="caption"><?php if(isset($_SESSION["user"])) {$m=$_SESSION["user"]; echo"$m"; } else{echo"user";} ?> </span>
</div>
<textarea id="kommetararea" rows="4" cols="45" name="comment" form="usrform" placeholder="Vad är din åsikt?"></textarea>
  <div class="rate">
    <input type="radio" id="star5" name="rate" value="5" />
    <label for="star5" title="text">5 stars</label>
    <input type="radio" id="star4" name="rate" value="4" />
    <label for="star4" title="text">4 stars</label>
    <input type="radio" id="star3" name="rate" value="3" />
    <label for="star3" title="text">3 stars</label>
    <input type="radio" id="star2" name="rate" value="2" />
    <label for="star2" title="text">2 stars</label>
    <input type="radio" id="star1" name="rate" value="1" />
    <label for="star1" title="text">1 star</label>
  </div>
  <textarea class="hidden" rows="4" cols="50" name="produkt" form="usrform"><?php echo"$s";?></textarea><br><br>
  <input type="submit" id="submitknapp">
  </form>
</div>
</div>