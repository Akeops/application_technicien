<!DOCTYPE html>

<head>
	<script src='public/js/jquery.js'></script>
</head>

<html>
	<header>
	</header>
	<body>
		<form method='post'>
			<input type='text' placeholder='Identifiant' name='username'/><br />
			<input type='password' placeholder='Mot de Passe' name='password'/>
			<p><input type='checkbox' name='stay'/>Rester connecté</p>
			<button type='button' onclick='send()'>Valider</button>
		</form>
		<script>
			function send()
			{
				var data = $('form').serialize();
				data = data + '&action=login';
				$.post({
					url:		'.',
					data:		data,
					success:	(data) => {
						window.location.reload(true);
					}
				});
			}
		</script>
	</body>
	<footer>
	</footer>
</html>