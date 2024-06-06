<?php
	require('inc/verify.inc.php');
	
	$req = $bdd->prepare("
		UPDATE
			USERS_INTER
		SET
			SIGN = :sign,
			AGENCY = :agency
		WHERE
			PSEUDO = :user
		");
	$req->execute(array(
		'sign' => $_POST['sign'],
		'agency' => $_POST['agency'],
		'user'	=> $_POST['username']
	)) or die(print_r($req->errorInfo()));
	
	$json['SUCCESS'] = 1;
	echo json_encode($json);
?>