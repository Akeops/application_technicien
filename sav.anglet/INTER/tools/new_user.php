<?php
	require('../inc/accounts.inc.php');
	
	if($_SESSION['rights'] < 3)
	{
		header('Location: /');
	}
?>

<!DOCTYPE html>

<head>
</head>

<html>
	<header>
	</header>

	<body>
		<?php echo "Last Id : ".$req['ID'];?>
		<form method="post" action="/INTER/inc/accounts.inc.php?register">
				Nom :			<input type="text" value="" name="NAME">
				Pseudo :		<input type="text" value="" name="PSEUDO">
				Mot de Passe :	<input type="text" value="" name="PWD">
				Mail :			<input type="mail" value="" name="MAIL">
				Droits :		<select name="RIGHTS">
									<option value="1">Technicien</option>
									<option value="2">Gestion</option>
									<option value="3">Admin</option>
								</select>
				<button type="submit">Submit</button>
		</form>
	</body>
	
	<footer>
	</footer>
</html>