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
	print_r(currentKampanj("1"));
?>
</body>
</html>