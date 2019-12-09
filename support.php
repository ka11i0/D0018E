<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="produkter.css">
    <link rel="stylesheet" type="text/css" href="kontosida.css">
    <link rel="stylesheet" type="text/css" href="support.css">
</head>
	<body>
		<?php include_once 'navbar.php'; 
			if (isset($_SESSION["user"])) {
				if ($_SESSION["privilegie"]==1) {
					$user_query = "SELECT namn FROM konto";
				}
			}
		?>
	</body>
</html>