<nav id="navigation">
    <ul>
        <li><a href="produkter.php" class="left">Butik</a></li>
        <li><a href="custom.php" class="left">Custom Snus</a></li>
        <?php
        echo '<li><a href="support.php" class="left">Support</a></li>';
        ?>
        <li><a href="om.php" class="left">Om oss</a></li>
        <!-- ifall en användare är inloggad så ska man komma åt varukorgen samt utloggning -->
        <?php if (isset($_SESSION["user"])) :?>
                <li><a href=logout.php class='right'>Logga ut</a></li>
        <?php endif; ?>
        <?php
        if (isset($_SESSION["user"])) {
            if ($_SESSION["privilegie"] == 0) {
                echo '<li><a href=varukorg.php class="right">Varukorg</a></li>';
            }
        }
        //inloggade användare har också tillgång till sitt saldo
        if (isset($_SESSION["user"])) {
            if ($_SESSION["privilegie"]==0) {
                echo '<li><a class="right" id="saldo" href="saldo.php" style="
                        width: 140px;">'.$_SESSION["saldo"].' kr</a></li>';
            }
        }
        ?>
        <!-- När en användare är inloggad så visas dess namn på en länk till kontosidan -->
        <li><a <?php 
            if (isset($_SESSION["user"])) 
                {print "href='kontosida.php' id='user'";}
            else
                {print "href=login.php";} 
            ?> class="right" style="
    width: 100px;">
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
<script type="text/javascript">
    var username;
    var monies;

    document.getElementById("user").onmouseover = function() {mouseOverKonto()};
    document.getElementById("user").onmouseout = function() {mouseOutKonto()};
    document.getElementById("saldo").onmouseover = function() {mouseOverSaldo()};
    document.getElementById("saldo").onmouseout = function() {mouseOutSaldo()};

    function mouseOverKonto() {
        username = document.getElementById("user").innerHTML;
        document.getElementById("user").innerHTML = "kontosida";
    }
    function mouseOutKonto() {
        document.getElementById("user").innerHTML = username;
    }
    function mouseOverSaldo() {
        monies = document.getElementById("saldo").innerHTML;
        document.getElementById("saldo").innerHTML = "Klicka för påfyllning.";
    }
    function mouseOutSaldo() {
        document.getElementById("saldo").innerHTML = monies;
    }
</script>
<!-- När länken där användarnamn visas har muspekarn över sig byts texten till "kontosida" -->