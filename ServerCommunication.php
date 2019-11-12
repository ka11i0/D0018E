<?php

//inkludera denna fil i filen som ska kommunicera med server för alla kommandon med server.


function OpenCon() //retunerar ett connection objekt till servern
 {
 $dbhost = "127.0.0.1"; 
 $dbuser = "960224";
 $dbpass = "apa";
 $db = "db960224"; //zerosdb
 $conn = new mysqli($dbhost, $dbuser, $dbpass,$db);
 return $conn;
}

function CloseCon($conn) //tar bort connection ur objectet
 {
 $conn -> close();
 }

function DB_Check($sql_query)  //kollar om något attribut som efterfrågas finns i databsen

}
function DB_insertProduct() //kunna skapa en ny produkt i databasen som admin
{

}
function DB_update() //både users och produkter samt historik ska kunna uppdateras 
{
}

