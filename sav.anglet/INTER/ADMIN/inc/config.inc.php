<?php
	$_BDD = array
	(
		'HOST'		=> 'tacteosesavgmaoa.mysql.db',	// Adresse de la Base.
		'NAME'		=> 'tacteosesavgmaoa',			// Nom de la Base.
		'CHARSET'	=> 'utf8',					// Encodage de la Base.
		'USER'		=> 'tacteosesavgmaoa',			// Nom d'Utilisateur de la Base.
		'PWD'		=> 'pFgW3g2SC67f'				// Mot de Passe de la Base.
	);

	$_COOKIE_AUTH_NAME = "authTokenTacteoseSAV";//Nom du cookie utilisé pour le token de connexion.

	$username = "admin";
	$password = ":}MF.5vWL/s7r%4eD7q2";

	$_TABLES_FORBIDDEN = array(
		'INTERVENTIONS',
		'INTER',
		'USERS_INTER'
	);

	try
	{
		$bdd = new PDO('mysql:host=' . $_BDD['HOST'] . ';dbname=' . $_BDD['NAME'] . ';charset=' . $_BDD['CHARSET'], $_BDD['USER'], $_BDD['PWD']);
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}
?>
