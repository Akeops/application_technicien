<?php
	require('../../admin/inc/config.inc.php');
	header("access-control-allow-origin: *");
	
	echo json_encode($_POST);
	die;
	if(empty($_POST) || empty($_POST['username']) || empty($_POST['password']))
	{
		echo success(false, '403', 'Forbidden');
		die;
	}
	
	$req = $bdd->prepare("SELECT * FROM USERS_INTER WHERE PSEUDO = :username") or die(print_r($req->errorInfo()));
	$req->execute(array
		(
			'username'	=>	$_POST['username']
		)
	);
	$user = $req->fetch(2);
	
	if(password_verify($_POST['password'], $user['PWD']))
	{
		echo success(true);
		die;
	}
	else
	{
		echo success(false, '403', 'Forbidden');
		die;
	}
	
	function success($success, $error = null, $message = null)
	{
		if(!$success)
		{
			$json['success'] = $success;
			$json['error'] = $error;
			$json['message'] = $message;
			return json_encode($json);
		}
		
		$json['success'] = $success;
		return json_encode($json);
	}
?>