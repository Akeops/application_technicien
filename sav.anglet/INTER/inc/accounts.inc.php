<?php
	require('config.inc.php');
	
	session_start();
	
	try
	{
		$bdd = new PDO('mysql:host=' . $_BDD['HOST'] . ';dbname=' . $_BDD['NAME'] . ';charset=' . $_BDD['CHARSET'], $_BDD['USER'], $_BDD['PWD']);
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
	
	if(isset($_GET['login']))
	{
		if(login($bdd))
		{
			header('Location: /INTER/index.php');
			exit();
		}
		else
		{
			header('Location: /INTER/login.php?deny');
			exit();
		}
	}
	elseif(isset($_GET['logout']))
	{
		logout($bdd);
		exit();
	}
	elseif(isset($_GET['register']))
	{
		if(register($bdd))
		{
			$_SESSION['register'] = true;
			header('Location: /INTER/new_user');
			exit();
		}
	}
	else
	{
		if(!logged($bdd))
		{
			header('Location:	/INTER/login.php');
			exit();
		}
	}
	
	function register($bdd)
	{
		$pwd = password_hash($_POST['PWD'], PASSWORD_DEFAULT);
		$req = $bdd->prepare('INSERT INTO USERS_INTER(NAME, PSEUDO, PWD, RIGHTS, MAIL) VALUES(:nom, :pseudo, :pwd, :rights, :mail)');
		$req->execute(
			array(
				'nom' 		=> $_POST['NAME'],
				'pseudo'	=> $_POST['PSEUDO'],
				'pwd'		=> $pwd,
				'rights'	=> $_POST['RIGHTS'],
				'mail'		=> $_POST['MAIL']
			)
		);

		return true;
	}
	
	function logout($bdd)
	{
		$req = $bdd->prepare('UPDATE USERS_INTER SET TOKEN= :token WHERE PSEUDO= :pseudo');
		$req->execute(array
			(
				'token'		=> '',
				'pseudo'	=> $_SESSION['user']
			)
		);
		
		unset($_COOKIE[$GLOBALS['_COOKIE_AUTH_NAME']]);
		setcookie($GLOBALS['_COOKIE_AUTH_NAME'], null, -1, '/');
		session_destroy();
		
		header('Location:	/INTER/login.php');
		exit();
	}
	
	function login($bdd)
	{
		$req = $bdd->prepare('SELECT * FROM USERS_INTER WHERE PSEUDO= :pseudo');
		$req->execute(array('pseudo' => $_POST['pseudo']));
		$user = $req->fetch();
		
		if(password_verify($_POST['pwd'], $user['PWD']))
		{
			$_SESSION['user']	= $user['PSEUDO'];
			$_SESSION['name']	= $user['NAME'];
			$_SESSION['id']		= $user['ID'];
			$_SESSION['rights']	= $user['RIGHTS'];
			
			if($_POST['stayLog'] == true)
			{
				if(empty($user['TOKEN']))
				{
					$req = $bdd->prepare('UPDATE USERS_INTER SET TOKEN= :token WHERE PSEUDO= :pseudo');

					$token = hash('sha256', $user->id . time());
					setcookie($GLOBALS['_COOKIE_AUTH_NAME'], $token, time()+60*60*24*365, '/');

					$req->execute(array
						(
							'token'		=> $token,
							'pseudo'	=> $user['PSEUDO']
						)
					);
				}
				else
				{
					setcookie($GLOBALS['_COOKIE_AUTH_NAME'], $user['TOKEN'], time()+60*60*24*365, '/');
				}
			}
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function logged($bdd)
	{
		if(isset($_SESSION['user']))
		{
			return true;
		}
		elseif(!empty($_COOKIE[$GLOBALS['_COOKIE_AUTH_NAME']]))
		{
			$req = $bdd->prepare("SELECT * FROM USERS_INTER WHERE TOKEN= :token");
			$req->execute(array
				(
					'token'	=> $_COOKIE[$GLOBALS['_COOKIE_AUTH_NAME']]
				)
			);
			$user = $req->fetch();
			
			if(!empty($user))
			{
				$_SESSION['user']	= $user['PSEUDO'];
				$_SESSION['name']	= $user['NAME'];
				$_SESSION['id']		= $user['ID'];
				$_SESSION['rights']	= $user['RIGHTS'];
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
?>