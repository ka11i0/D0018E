<?php session_start(); 
include 'ServerCommunication.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="produkter.css">
</head>
<body>
<?php
	var_dump( 10 - round((1 - currentKampanj("2")*0.01)*5, 0)  );
?>
</body>
</html>