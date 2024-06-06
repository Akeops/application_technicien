<?php
	// define('AJAX_REQUEST', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	// if(!AJAX_REQUEST) 
	// {
		// header('Location:	../');
		// die();
	// }
	session_start();
	include('../inc/config.inc.php');

	if(empty($_POST))
	{
		die();
	}
	
	$req = $bdd->prepare("
		SELECT
			*
		FROM
			USERS_INTER
		WHERE
			PSEUDO = :user");
	$req->execute(array(
		'user'	=>	$_POST['username']
	)) or die(print_r($req->errorInfo()));
	$user = $req->fetch(2);
	
	if($user === false || !password_verify($_POST['password'], $user['PWD']))
	{
		$json['SUCCESS'] = 0;
		$json['ERROR'] = "Identifiant/Mot de Passe Incorrecte.";
		echo json_encode($json);
		die();
	}
	else if($_POST['stayLog'] == "true")
	{
		if(empty($user['TOKEN']))
		{
			$token = hash('sha256', $user->id . time());
			if(setcookie($GLOBALS['_COOKIE_AUTH_NAME'], $token, time()+60*60*24*365))
			{
				$bdd->query("UPDATE USERS_INTER SET TOKEN = '".$_COOKIE[$GLOBALS['_COOKIE_AUTH_NAME']]."' WHERE ID = '".$user['ID']."'")
				or die(print_r($bdd->errorInfo()));
			}
		}
		else
		{
			setcookie($GLOBALS['_COOKIE_AUTH_NAME'], $user['TOKEN'], time()+60*60*24*365);
		}
	}
	else
	{
		unset($_COOKIE[$GLOBALS['_COOKIE_AUTH_NAME']]);
		setcookie($GLOBALS['_COOKIE_AUTH_NAME'], '', time() - 3600);
		$req = $bdd->query("UPDATE USERS_INTER SET TOKEN = '' WHERE PSEUDO = '".$user['PSEUDO']."'") or die(print_r($bdd->errorInfo()));
	}

	stayLog($user);
	$json['SUCCESS'] = 1;
	$json['REQUEST'] = $_SESSION['REQUEST_URI'] ? $_SESSION['REQUEST_URI'] : '';
	echo json_encode($json);
	die();
	
	function stayLog($user)
	{
		$_SESSION['LOGGED'] = 1;
		$_SESSION['USER_ID'] = $user['ID'];
	}
?>