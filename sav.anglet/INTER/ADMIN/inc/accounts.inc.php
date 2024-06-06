<?php
	require('config.inc.php');
	
	session_start();
	
	if(empty($_SESSION['REQUEST']) && !isset($_GET['login']))
	{
		$script = str_replace($_SERVER['SCRIPT_URL'], "", $_SERVER['SCRIPT_URI']);
		$_SESSION['REQUEST'] = $script.$_SERVER['REQUEST_URI'];
	}
	
	if(isset($_GET['login']))
	{
		if(login($bdd))
		{
			if(!empty($_SESSION['REQUEST']))
			{
				$request = $_SESSION['REQUEST'];
				$_SESSION['REQUEST'] = null;
				header('Location: '.$request);
			}
			else
			{
				header('Location: /INTER/ADMIN/');
			}
			exit();
		}
		else
		{
			echo "<script>window.parent.location = '/INTER/ADMIN/login.php?deny&request=".$_SESSION['REQUEST']."';</script>";
			exit();
		}
	}
	elseif(isset($_GET['logout']))
	{
		logout($bdd);
		exit();
	}
	else
	{
		if(!logged($bdd))
		{
			echo "<script>window.parent.location = '/INTER/ADMIN/login.php?deny&request=".$_SESSION['REQUEST']."';</script>";
			exit();
		}
		else
		{
			$req = $bdd->prepare("
				SELECT
					*
				FROM
					USERS_INTER
				WHERE
					TOKEN= :token");
			$req->execute(array
				(
					'token'	=> $_COOKIE[$GLOBALS['_COOKIE_AUTH_NAME']]
				)
			);
			$user = $req->fetch();
			
			if(!empty($user))
			{
				if($user['DISABLED'])
				{
					logout($bdd);
				}

				$_SESSION['user']	= $user['PSEUDO'];
				$_SESSION['name']	= $user['NAME'];
				$_SESSION['id']		= $user['ID'];
				unset($_SESSION['rights']);
				$_SESSION['rights'] = array();
				
				$req = $bdd->query("
					SELECT
						*
					FROM
						RIGHTS
					WHERE
						ID =".$user['RIGHTS']) or die(print_r($req->errorInfo()));
				$rights = $req->fetchAll(2);
				for($x = 0; $x < sizeOf($rights[0]); $x++)
				{
					$_SESSION['rights'][key($rights[0])] = current($rights[0]) ? current($rights[0]) : "0";
					next($rights[0]);
				}
			}
		}
	}
	
	function logout($bdd)
	{
		$req = $bdd->prepare('UPDATE USERS_INTER SET TOKEN = NULL WHERE ID = :id');
		$req->execute(array
			(
				'id'	=> $_SESSION['id']
			)
		);
		
		unset($_COOKIE[$GLOBALS['_COOKIE_AUTH_NAME']]);
		setcookie($GLOBALS['_COOKIE_AUTH_NAME'], null, -1, '/');
		session_destroy();
		
		if(!isset($_GET['recon']))
		{
			echo "<script>window.parent.location = '/INTER/ADMIN/login.php';</script>";
		}
		else
		{
			echo "<script>window.parent.location = '/INTER/ADMIN/login.php?recon&request=".$_SESSION['REQUEST']."';</script>";
		}

		exit();
	}
	
	function login($bdd)
	{
		$req = $bdd->prepare("
			SELECT
				*
			FROM
				USERS_INTER
			WHERE
				PSEUDO= :pseudo");
		$req->execute(array('pseudo' => $_POST['pseudo'])) or die(print_r($req->errorInfo()));
		$user = $req->fetch();
		
		if(password_verify($_POST['pwd'], $user['PWD']))
		{
			if($user['DISABLED'])
			{
				echo "<script>window.parent.location = '/INTER/ADMIN/login.php?isDisabled&request=".$_SESSION['REQUEST']."';</script>";
				die();
			}
			$_SESSION['user']	= $user['PSEUDO'];
			$_SESSION['name']	= $user['NAME'];
			$_SESSION['id']		= $user['ID'];
			unset($_SESSION['rights']);
			$_SESSION['rights'] = array();
			
			$req = $bdd->query("
				SELECT
					*
				FROM
					RIGHTS
				WHERE
					ID =".$user['RIGHTS']) or die(print_r($req->errorInfo()));
			$rights = $req->fetchAll(2);
			for($x = 0; $x < sizeOf($rights[0]); $x++)
			{
				$_SESSION['rights'][key($rights[0])] = current($rights[0]);
				next($rights[0]);
			}
			
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
				return true;
		}
		else
		{
			return false;
		}
	}
?>