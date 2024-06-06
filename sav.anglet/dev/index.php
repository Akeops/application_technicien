<?php
	include_once( __DIR__ . '/inc/config.inc.php');
	include_once( __DIR__ . '/controller/controller.php');
	include_once( __DIR__ . '/model/model.php');
	session_start();
	
	if(!login())
	{
		show_login();
		die();
	}
	
	switch($_GET['action'])
	{
		case 'logout':
			session_destroy();
			setcookie(COOKIE_AUTH_NAME, "", time() - 3600);
			header('Location: .');
		break;
		default:
			show_index();
		break;
	}
	
	function login()
	{
		if($_SESSION['user'])
		{
			return true;
		}
		
		if($_COOKIE[COOKIE_AUTH_NAME])
		{
			$user = getUserByToken($_COOKIE[COOKIE_AUTH_NAME]);
			if(!$user)
			{
				return false;
			}
			$_SESSION['user'] = $user;
			return true;
		}
		
		if(!empty($_POST) && $_POST['action'] === 'login')
		{
			$user = getUserByUsername($_POST['username']);
			if(!$user)
			{
				return false;
			}
			
			if(password_verify($_POST['password'], $user['PWD']))
			{
				$_SESSION['user'] = $user;
				if(isset($_POST['stay']))
				{
					stayLogin($user);
				}
				return true;
			}
		}
		
		return false;
	}