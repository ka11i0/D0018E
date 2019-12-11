<nav id="navigation">
    <ul>
        <li><a href="produkter.php" class="left">Butik</a></li>
        <li><a href="custom.php" class="left">Custom Snus</a></li>
        <?php
            //ifall användare med admin privilegier är inloggad så kan man komma åt support sidan
            if (isset($_SESSION["user"])) {
                echo '<li><a href="support.php" class="left">Support</a></li>';
            }
        ?>
        <li><a href="om.php" class="left">Om oss</a></li>
        <!-- ifall en användare är inloggad så ska man komma åt varukorgen samt utloggning -->
        <?php if (isset($_SESSION["user"])) :?>
                <li><a href=logout.php class='right'>Logga ut</a></li>
                <li><a href="varukorg.php" class="right">Varukorg</a></li>
        <?php endif; ?>
        <?php
        //inloggade användare har också tillgång till sitt saldo
        if (isset($_SESSION["user"])) {
            echo '<li><a class="right">'.$_SESSION["saldo"].' kr</a></li>';
        }
        ?>
        <!-- När en användare är inloggad så visas dess namn på en länk till kontosidan -->
        <li><a <?php 
            if (isset($_SESSION["user"])) 
                {print "href='kontosida.php' id='user'";}
            else
                {print "href=login.php";} 
            ?> class="right">
        <?php //När ingen användare är inloggad så finns en länk till inlogg sidan
            if (isset($_SESSION["user"])) {
                print_r($_SESSION["user"]);
            }
            else{
                echo "Logga in/Registrera";	
            }
        ?></a></li>
    </ul>
</nav>
<!-- När länken där användarnamn visas har muspekarn över sig byts texten till "kontosida" -->
<script type="text/javascript">
    document.getElementById("user").onmouseover = function() {mouseOver()};
    document.getElementById("user").onmouseout = function() {mouseOut()};
    var username;
    function mouseOver() {
        username = document.getElementById("user").innerHTML;
        document.getElementById("user").innerHTML = "kontosida";
    }
    function mouseOut() {
        document.getElementById("user").innerHTML = username;
    }
</script>