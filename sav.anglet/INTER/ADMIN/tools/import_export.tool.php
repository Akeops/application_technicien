<?php

	require "../inc/config.inc.php";
	require "../inc/accounts.inc.php";
	
	//SCRIPT UTILISE POUR L'UPLOAD AUTO DE LA BASE CLIENT.
	
	if($_POST['import'])
	{
		$file = fopen("../../BDD/CLIENTS.csv", "r");
		
		$bdd->query("TRUNCATE CLIENTS");
		
		$import_sql = "
			INSERT INTO
				CLIENTS (CODE, INTITULE, NOM, TELEPHONE1, EMAIL, ADLIVR_LIGNE1, ADLIVR_LIGNE2, ADLIVR_VILLE, ADLIVR_CODE_POSTAL, CONTRAT_DU, CONTRAT_AU, CODE_VENDEUR, SOLDE)
			VALUES
				(:code, :intitule, :nom, :telephone, :email, :adresse1, :adresse2, :ville, :code_postal, :contrat_du, :contrat_au, :commercial, :solde)";
		
		while (($column = fgetcsv($file, 10000, ";")) !== FALSE) {
			$req = $bdd->prepare($import_sql);
			$req->execute(array(
				'code'			=>	$column[0],
				'intitule'		=>	$column[1],
				'nom'			=>	$column[2],
				'telephone'		=>	$column[3],
				'email'			=>	$column[4],
				'adresse1'		=>	$column[5],
				'adresse2'		=>	$column[6],
				'ville'			=>	$column[7],
				'code_postal'	=>	$column[8],
				'contrat_du'	=>	$column[9],
				'contrat_au'	=>	$column[10],
				'commercial'	=>	$column[11],
				'solde'			=>	$column[12]
				)
			) or die(print_r($req->errorInfo()));;
		}
		
		echo true;
		die();
	}
?>