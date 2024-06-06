<?php
	require('../inc/config.inc.php');
	require('../inc/accounts.inc.php');
	
	session_start();
	if($_SESSION['rights'] < "3"){
		header('Location: widgets/home.php');
	}

	$users = $bdd->query("
		SELECT
			r.NAME 'GROUP', USERS_INTER.*
		FROM
			USERS_INTER
		INNER JOIN
			RIGHTS r ON (USERS_INTER.RIGHTS = r.ID)
		WHERE
			NOT(RIGHTS = 0)
		ORDER BY
			USERS_INTER.ID
		") or die(print_r($bdd->errorInfo()));
?>

<head>
	<link href="https://fonts.googleapis.com/css?family=Cabin Condensed" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="../scripts/jQuery.min.js"></script>
	<script src="conf.js"></script>
</head>

<html>
	<body>
		<div class="section">
			<h2>OUTILS</h2>
		</div>
		<div class="sectionContent">
			<form method="post" action="">
				<table>
					<tr>
						<td>
							<input onclick="refresh(this)" type="button" value="Rafraichir Liste Client">
						</td>
						<td>
							<span id="error"></span>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>