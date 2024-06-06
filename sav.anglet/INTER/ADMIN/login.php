<?php
	session_start();
	
	if(isset($_GET['request']))
	{
		if(empty($_SESSION['REQUEST']))
		{
			$_SESSION['REQUEST'] = $_GET['request'];
		}
	}
	
	if(isset($_SESSION['user']))
	{
		if(!empty($_SESSION['REQUEST']))
		{
			$request = $_SESSION['REQUEST'];
			$_SESSION['REQUEST'] = null;
			header('Location: '.$request);
			die();
		}
		else
		{
			header('Location: /INTER/');
			die();
		}
		exit();
	}
	elseif(isset($_COOKIE['authTokenTacteoSAV']))
	{
		$bdd = new PDO('mysql:host=tacteosebdd.mysql.db;dbname=tacteosebdd;charset=utf8', 'tacteosebdd', 'cWp2x8Q5');
		$req = $bdd->prepare("SELECT * FROM USERS_INTER WHERE TOKEN= :token");
		$req->execute(array
			(
				'token'	=> $_COOKIE['authTokenTacteoSAV']
			)
		);
		$user = $req->fetch();
		
		if(!empty($user))
		{
			$_SESSION['user']	= $user['PSEUDO'];
			$_SESSION['name']	= $user['NAME'];
			$_SESSION['id']		= $user['ID'];
			$_SESSION['rights']	= $user['RIGHTS'];
			header('Location:	/INTER/index.php');
			exit();
		}
	}
?>

<!DOCTYPE html>

<html>
	<head>
		<title>TEST MYSQL</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="initial-scale=1">
	</head>
	
	<body>
			<form method="post" action="inc/accounts.inc.php?login">
				<table>
					<tr>
						<td>
							<p>VEUILLEZ VOUS CONNECTER</p>
							<?php
								if(isset($_GET['deny']))
								{
									echo ("<p>Identifiant ou mot de passe invalide.</p>");
								}
								else if(isset($_SESSION['register']) && $_SESSION['register'] == true)
								{
									echo ("<p>Enregistrement validé ! Veuillez vous connecter.</p>");
								}
								else if(isset($_GET['recon']))
								{
									echo ("<p>Veuillez vous reconnecter.</p>");
								}
							?>
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" name="pseudo" placeholder="Identifiant">
						</td>
					</tr>
					<tr>
						<td>
							<input type="password" name="pwd" placeholder="Mot de Passe">
						</td>
					</tr>
				</table>
				<table>
					<tr id="stay">
						<td>
							<input type="checkbox" name="stayLog" id="stayLog" value="true">
						</td>
						<td>
							<label for="stayLog">Rester Connecté</label>
						</td>
					</tr>
					<tr>
						<td>
							<button type="submit">Connexion</button>
						</td>
					</tr>
				</table>
			</form>
	</body>
	
	<footer>
	</footer>
</html>

<style>
	html{
		background: url(http://sav.tacteo.fr/INTER/images/logo.png),rgba(35,35,35,1);
		background-position: top;
		background-size: 45%;
		background-repeat: no-repeat;
	}
	
	body{
	    border-radius: 10px;
		display: block;
		background: white;
		box-shadow: inset 0px 0px 20px black;
		width: 24vw;
		height: 30vh;
		margin: 0 auto;
		transform: translate(-50%, -50%);
		position: absolute;
		top: 50%;
		left: 50%;
	}
	
	img{
		width: 150px;
		margin: 20px auto;
		display: block;
	}
	
	h1{
		font-size:	35px;
		margin:		0;
	}
	
	p{
		font-size:	20px;
	}
	
	#stay{
		display:	inline;
	}
	
	#stay>input{
		width:	auto;
		height:	auto;
	}
	
	span{
	}
	
	form{
		width: 100%;
		display: block;
		margin: 0 auto;
		text-align: center;
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}
	
	button{
		margin:	10px;
		border-color: #232323;
		background: #232323;
		width: auto;
		height: auto;
		color: white;
		font-size: auto;
		margin-top:	20px;
	}
	
	table{
		width:	100%;
		margin: 0 auto;
	}
	
	input[type="text"],input[type="password"]{
		border: none;
		border: 1.5px rgb(35, 35, 35) solid;
		border-radius:	10px;
		font-size: 20px;
		height: 25px;
		padding: 0;
		width: 20vw;
		margin: 0;
	}
	
	@media (max-width: 640px) {
		body{
			border-radius: 10px;
			display: block;
			background: white;
			box-shadow: inset 0px 0px 20px black;
			width: 50vw;
			height: 50vh;
			margin: 0 auto;
			transform: translate(-50%, -50%);
			position: absolute;
			top: 50%;
			left: 50%;
		}
		
		input[type="text"],input[type="password"]{
			border: none;
			border: 1.5px rgb(35, 35, 35) solid;
			border-radius:	10px;
			font-size: 20px;
			height: 25px;
			padding: 0;
			width: 40vw;
			margin: 0;
		}
	}
	
</style>