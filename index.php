<?php
session_start(); 
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="index.css">
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script type="text/javascript" src="http://tympanus.net/Development/Arctext/js/jquery.arctext.js"></script>
</head>
	<body>
		<div id="icon">
			<div id="title">SnusExperten</div>
			<div id="iconCenter"></div>
			<a href="produkter.html" id="redirectLink">Butik</a>
		</div>
	</body>

<script type="text/javascript">
	$().ready(function() {
	$('#title').arctext({radius: 205})
	});

	$().ready(function() {
	$('#redirectLink').arctext({radius: 205, dir:-1})
	});
</script>
</html>