<?php
	session_start();
	require('inc/config.inc.php');
	
	if(isset($_POST['LOGOUT']))
	{
		$req = $bdd->prepare("
		SELECT
			*
		FROM
			USERS_INTER
		WHERE
			ID = :userId");
		$req->execute(array(
			'userId'	=>	$_SESSION['USER_ID']
		)) or die(print_r($req->errorInfo()));
		$user = $req->fetch(2);
		$bdd->query("UPDATE USERS_INTER SET TOKEN = NULL WHERE ID = '".$_SESSION['USER_ID']."'") or die(print_r($bdd->errorInfo()));
		session_destroy();
		die();
	}
	
	if($_SESSION['LOGGED'])
	{
		header('Location:	./');
	}
?>

<!DOCTYPE html>

<head>
	<link href="css/login.css" rel="stylesheet" />
	<script src="scripts/jquery.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Cabin Condensed" rel="stylesheet">
</head>

<script>
	document.addEventListener("DOMContentLoaded", init);
	function init()
	{
		var content = $("#content")[0];
		var width	= (window.innerWidth / 100) * 50;
		
		content.style.width = width + "px";
	}
	
	function send()
	{
		var values = $("[name]");
		var	data = new Object();
		
		var submit = $.post("scripts/checkLogin.php", {
			username:	$('[name=username]')[0].value,
			password:	$('[name=password]')[0].value,
			stayLog:	$('[name=stayLog]')[0].checked,
		});
		
		submit.done(
		function(data)
		{
			var data = JSON.parse(data);
			if(data['SUCCESS'])
			{
				if(data['REQUEST'] == "")
				{
					window.location = "./";
				}
				else
				{
					window.location = data['REQUEST'];
				}
			}
			else
			{
				$("#error")[0].innerHTML = data['ERROR'];
			}
		});
	}
</script>

<html>
	<body>
		<div id="content">
			<div id="login">
				<div id="img"></div>
				<span id="error"></span>
				<input class="infos" type="text" placeholder="Nom d'Utilisateur" name="username"/><br/>
				<input class="infos" type="password" placeholder="Mot de Passe" name="password"/><br />
				<div id="stayLog">
					<input type="checkbox" id="stay" name="stayLog" />
					<label for="stay">Rester Connecté</label>
				</div>
				<input class="infos" type="button" value="Connexion" onclick="send()"/>
			</div>
		</div>
	</body>
</html>

