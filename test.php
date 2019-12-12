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
	if (isKampanj("1","2019-12-24")) {
		echo "yeboi";
	}
	else {
		echo "bepis";
	}
?>
</body>
</html>