<?php 
    include 'ServerCommunication.php'; 
    session_start();
    $person_id = $_SESSION["id"];
    $info = array("email", "town", "postnum", "addrnum", "telnum");

    //vid uppdatering
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $info[0] = $_POST['mail'];
        $info[1] = $_POST['town'];
        $info[2] = $_POST['postnum'];
        $info[3] = $_POST['addrnum'];
        $info[4] = $_POST['telnum'];

        //skapa databas connection
        $p=OpenCon();  

        $uname = $_SESSION["user"];

        
        $account = "UPDATE konto SET Mail='$info[0]', Stad='$info[1]', Postnummer='$info[2]', Address='$info[3]', Telefonnummer='$info[4]' 
        WHERE `Namn` = '$uname'";
        if ($p->query($account)) {
            //uppdatera session info
            $_SESSION["email"]=$_POST['mail'];
            $_SESSION["town"]=$_POST['town'];
            $_SESSION["pnr"]=$_POST['postnum'];
            $_SESSION["addr"]=$_POST['addrnum'];
            $_SESSION["telnr"]=$_POST['telnum'];
            echo "<script type='text/javascript'>alert('Dina kontouppgifter har blivit uppdaterade');</script>";
        }
        else {
            echo "<script type='text/javascript'>alert('Den emailen är redan tagen');</script>";
        }   
    }


?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="produkter.css">
    <link rel="stylesheet" type="text/css" href="kontosida.css">
</head>
	<body>
        <?php include_once 'navbar.php'; ?>
        <div id="box">
            <div id="kolumn">
                <form action="<?php $_PHP_SELF ?>" method="post">
                    <p id="headline"><h3>Kontouppgifter</h3></p>
                        <label for="email"><b>Email</b></label>
                        <input type="email" value="<?php echo $_SESSION["email"] ?>" name="mail" required>

                        <div id="uppg">
                        <label for="town"><b>Stad</b></label>
                        <input type="text" value="<?php echo $_SESSION["town"] ?>" name="town" required>
                        </div>

                        <div id="uppg">
                        <label for="post"><b>Postnummer</b></label>
                        <input type="number" value="<?php echo $_SESSION["pnr"] ?>" name="postnum" required>
                        </div>

                        <div id="uppg">
                        <label for="addr"><b>Address</b></label>
                        <input type="text" value="<?php echo $_SESSION["addr"] ?>" name="addrnum" required>
                        </div>

                        <div id="uppg">
                        <label for="tel"><b>Telefonnummer</b></label>
                        <input type="telnum" value="<?php echo $_SESSION["telnr"] ?>" name="telnum" required>
                        </div>
                            
                        <button type="submit">Uppdatera</button>
                    </form>
    		    </div>
                    <?php
                        echo '<div id="kolumn2">
                            <p id="headline"><h3>Köp historik</h3></p>
                            <div id="scroll">';
                        $table_query = "SELECT historik.Transaktion_ID, historik.Datum, historik.Tid, historik.quantity, produkt.Produktnamn, produkt.Pris FROM historik INNER JOIN produkt ON historik.Produkt_ID = produkt.Produkt_ID WHERE Person_ID='$person_id' ORDER BY historik.Datum DESC, historik.tid DESC, produkt.Produkt_ID ASC";
                        $conn = OpenCon();
                        $result = $conn->query($table_query);
                        $index = 0;
                        if ($result->num_rows>0) {
                                echo "<table>
                                <thead>
                                    <tr id='title' class='bottom'>
                                        <th>Namn</th>
                                        <th>Antal (st)</th>
                                        <th>Pris per enhet (kr)</th>
                                        <th>Totalkostnad (kr)</th>
                                        <th>Datum</th>
                                        <th>Klockslag</th>
                                    </tr>
                                </thead>";
                                echo "<tbody>";
                            while($row = $result->fetch_assoc()) {
                                $query_data[$index][0] = $row["Transaktion_ID"];
                                $query_data[$index][1] = $row["Produktnamn"];
                                $query_data[$index][2] = $row["Pris"];
                                $query_data[$index][3] = $row["quantity"];
                                $query_data[$index][4] = $row["Datum"];
                                $query_data[$index][5] = $row["Tid"];
                                $index++;
                            }
                        }
                        else {
                            echo "Inget köpt ännu.";
                        }
                        $current_queue = NULL;
                        $count = 0;
                        for ($i=0; $i < $index; $i++) {
                            if ($current_queue == $query_data[$i][0]) {
                                $count++;
                                echo "<tr>";
                            }
                            else {
                                $count = 0;
                                $current_queue = $query_data[$i][0];
                                echo "<tr class='top'>";
                            }
                            echo "<th>".$query_data[$i][1]."</th>";
                            echo "<th>".$query_data[$i][3]."</th>";
                            echo "<th>".$query_data[$i][2]."</th>";
                            echo "<th>".$query_data[$i][2]*$query_data[$i][3]."</th>";
                            if ($count==0 || $count<1) {
                                echo "<th>".$query_data[$i][4]."</th>";
                                echo "<th>".$query_data[$i][5]."</th>";
                            }
                            else {
                                echo "<th></th>";
                                echo "<th></th>";
                            }
                            echo "</tr>";
                        }
                        echo "</tbody></table>";
                        CloseCon($conn);
                    ?>
                </div>
            </div>
        </div>
	</body>
</html>