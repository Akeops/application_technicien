<?php
	session_start();
	require('config.inc.php');
	
	if($_SESSION['LOGGED'])
	{
	}
	else if(!empty($_COOKIE[$GLOBALS['_COOKIE_AUTH_NAME']]))
	{
		$token = $_COOKIE[$GLOBALS['_COOKIE_AUTH_NAME']];
		$req = $bdd->prepare("
			SELECT
				*
			FROM
				USERS_INTER
			WHERE
				TOKEN = :token");
		$req->execute(array(
			'token'	=>	$token
		)) or die(print_r($req->errorInfo()));
		$user = $req->fetch(2);
		
		if($user === false)
		{
			unset($_COOKIE[$GLOBALS['_COOKIE_AUTH_NAME']]);
			setcookie($GLOBALS['_COOKIE_AUTH_NAME'], '', time() - 3600);
			header('Location:	/admin/login.php');
			die();
		}
		else
		{
			logged($user);
		}
	}
	else
	{
		unset($_COOKIE[$GLOBALS['_COOKIE_AUTH_NAME']]);
		setcookie($GLOBALS['_COOKIE_AUTH_NAME'], '', time() - 3600);
		
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
			$link = "https"; 
		else
			$link = "http"; 
		$link .= "://"; 
		$link .= $_SERVER['HTTP_HOST']; 
		$link .= $_SERVER['REQUEST_URI']; 
		$_SESSION['REQUEST_URI'] = $link;
		header('Location:	/admin/login.php');
		die();
	}
	
	function logged($user)
	{
		$_SESSION['LOGGED'] = 1;
		$_SESSION['USER_ID'] = $user['ID'];
	}
	
	$req = $bdd->prepare("
		SELECT
			*
		FROM
			USERS_INTER
		WHERE
			ID = :id");
	$req->execute(array(
		'id'	=>	$_SESSION['USER_ID']
	)) or die(print_r($req->errorInfo()));
	$user = $req->fetch(2);
?>