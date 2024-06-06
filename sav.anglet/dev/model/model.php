<?php
	function connectDb()
	{
		try
		{
			$db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PWD);
		}
		catch (Exception $e)
		{
			die('Erreur : ' . $e->getMessage());
		}
		
		return $db;
	}

	function getUserByToken($token)
	{
		$db = connectDb();
		if(!$token)
		{
			return false;
		}
		
		$req = $db->prepare("SELECT * FROM USERS_INTER WHERE TOKEN = :token");
		$req->execute(array('token' => $token)) or die(print_r($req->errorInfo()));
		$user = $req->fetch(2);
		return $user;
	}
	
	function getUserByUsername($username)
	{
		$db = connectDb();
		if(empty($username))
		{
			return false;
		}
		
		$req = $db->prepare("SELECT * FROM USERS_INTER WHERE PSEUDO = :username");
		$req->execute(array('username' => $username)) or die(print_r($req->errorInfo()));
		$user = $req->fetch(2);
		return $user;
	}
	
	function stayLogin($user)
	{
		$db = connectDb();
		$hash = hash('md5',$user['PSEUDO'].$user['PWD'].time());
		setcookie(COOKIE_AUTH_NAME, $hash, time() + 3600 * 24 * 365);
		$db->query("UPDATE USERS_INTER SET TOKEN = '{$hash}' WHERE ID = {$user['ID']}") or die(print_r($req->errorInfo()));
		return true;
	}