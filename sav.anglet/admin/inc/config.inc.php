<?php
	$_BDD = array
	(
		'HOST'		=> '127.0.0.1',	// Adresse de la Base. tacteosesavgmaoa.mysql.db
		'NAME'		=> 'bdd_tacteo',			// Nom de la Base. tacteosesavgmaoa
		'CHARSET'	=> 'utf8',						// Encodage de la Base.
		'USER'		=> 'root',			// Nom d'Utilisateur de la Base. tacteosesavgmaoa
		'PWD'		=> ''				// Mot de Passe de la Base.
	);

	$_COOKIE_AUTH_NAME = "authTokenTacteoseSAV";//Nom du cookie utilis� pour le token de connexion.

	$username = "admin";
	$password = ":}MF.5vWL/s7r%4eD7q2";

	$_TABLES_FORBIDDEN = array(
		'INTERVENTIONS',
		'USERS_INTER',
		'DEVICES',
		'CLIENTS'
	);

	try
	{
		$bdd = new PDO('mysql:host=' . $_BDD['HOST'] . ';dbname=' . $_BDD['NAME'] . ';charset=' . $_BDD['CHARSET'], $_BDD['USER'], $_BDD['PWD']);
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}

	$GLOBALS['userApiKey'] = "ae408ce23e62e798964737f720486e93";
?>
