<nav id="navigation">
    <ul>
        <li><a href="produkter.php" class="left">Butik</a></li>
        <li><a href="custom.php" class="left">Custom Snus</a></li>
        <?php
            if (isset($_SESSION["user"])) {
                if ($_SESSION["privilegie"]==1) {
                    echo '<li><a href="support.php" class="left">Support</a></li>';
                }
            }
        ?>
        <li><a href="om.php" class="left">Om oss</a></li>
    
        <?php if (isset($_SESSION["user"])) :?>
                <li><a href=logout.php class='right'>Logga ut</a></li>
                <li><a href="varukorg.php" class="right">Varukorg</a></li>
        <?php endif; ?>
        <?php
        if (isset($_SESSION["user"])) {
            echo '<li><a class="right">'.$_SESSION["saldo"].' kr</a></li>';
        }
        ?>

        <li><a <?php 
            if (isset($_SESSION["user"])) 
                {print "href='kontosida.php' id='user'";}
            else
                {print "'href=login.php'";} 
            ?> class="right">
        <?php 
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