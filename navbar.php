<nav id="navigation">
    <ul>
        <li><a href="produkter.php" class="left">Butik</a></li>
        <li><a href="custom.php" class="left">Custom Snus</a></li>
        <li><a href="support.php" class="left">Support</a></li>
        <li><a href="om.php" class="left">Om oss</a></li>
        <li><a href="varukorg.php" class="right">Varukorg</a></li>

        <?php <li><a
            if (isset($_SESSION["user"])) 
                { print "href=logout.php";}
             class="right" >
            if (isset($_SESSION["user"])) {
                echo "Logga ut";
            }
        </a></li>?>

        <li><a <?php 
            if (isset($_SESSION["user"])) 
                {print "href=logout.php";}
            else
                {print "href=login.php";} 
            ?> class="right">
        <?php 
            if (isset($_SESSION["user"])) {
                print_r($_SESSION["user"]);
                //echo "string";
            }
            else{
                echo "Logga in/Registrera";	
            }
        ?></a></li>
    </ul>
</nav>