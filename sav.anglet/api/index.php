<?php
	require('inc/verify.inc.php');
	require('../admin/inc/config.inc.php');
	require('../admin/sellsy/sellsy.php');
	//header('Content-Type: application/json');
	header('Access-Control-Allow-Origin: *');
	
	// START API //
	
	//Check if a quotation is researched
	if($_POST['type'] == 'quotation' && isset($_POST['quotation']))
	{
		$quotation = getQuotation($_POST['quotation']);
		$json = json_encode($quotation);
		echo $json;
		die;
	}
	
	//Check the synchro to the database
	$req = $bdd->query("SELECT NAME, VALUE FROM SETTINGS WHERE NAME = 'LAST_SYNCHRO' OR NAME = 'INTERVAL_SYNCHRO'") or die(print_r($bdd->errorInfo()));
	$result = $req->fetchAll(2);
	$synchro = array();
	$synchro[$result[0]['NAME']] = $result[0]['VALUE'];
	$synchro[$result[1]['NAME']] = $result[1]['VALUE'];
	$today = time();
	$diff = $today - $synchro['LAST_SYNCHRO'];

	// if($diff >= $synchro['INTERVAL_SYNCHRO'])
	// {
		refreshBDD($bdd);
		// $bdd->query("UPDATE SETTINGS SET VALUE = ".$today." WHERE NAME = 'LAST_SYNCHRO'") or die(print_r($bdd->errorInfo()));
	// }
	
	//Start synchro
	
	$tables = $bdd->query("SHOW TABLES FROM ".$_BDD['NAME']);
	
	while($table = $tables->fetch())
	{
		$columns = $bdd->query("
			SELECT
				`COLUMN_NAME`
			FROM
				`INFORMATION_SCHEMA`.`COLUMNS`
			WHERE
				`TABLE_SCHEMA` = '".$_BDD['NAME']."'
			AND
				`TABLE_NAME` = '".$table[0]."'
		") or die(print_r($bdd->errorInfo()));
		
		$data['BDD']['TABLES'][$table[0]]['COLUMNS'] = array();
		
		while($column = $columns->fetch())
		{
			array_push($data['BDD']['TABLES'][$table[0]]['COLUMNS'], $column[0]);
		}

		switch($table[0])
		{
			case "CUSTOMERS":
				$values = $bdd->query("
					SELECT
						*
					FROM
						".$table[0]."
					WHERE
						INTITULE = 'CLIENT'
					OR
						INTITULE = ''
					ORDER BY
						NOM
				");
			break;
			case "PRODUITS":
				$values = $bdd->query("
					SELECT
						*
					FROM
						".$table[0]."
					ORDER BY
						LIBELLE
				");
			break;
			case "INTER":
				$values = $bdd->query("
					SELECT
						DATE_END, DESCRIPTION, AGENCY, INSTALL, MAINTENANCE, TRAINING, RECOVERY, RENEWAL, DELIVERY, NEAR_VISIT, CUSTOMER_LABEL, CUSTOMER_CIVILITY, CUSTOMER_NAME, CUSTOMER_QUALITY
					FROM
						".$table[0]."
					ORDER BY
						DATE_END DESC
					LIMIT 1000
				");
			break;
			default:
				$values = $bdd->query("
					SELECT
						*
					FROM
						".$table[0]
				);
			break;
		}

		for($x = 0; $value = $values->fetch(3); $x++)
		{
			$data['BDD']['TABLES'][$table[0]]['VALUES'][$x] = $value;
		}
	}
	
	for($x = 0; $x < sizeOf($_TABLES_FORBIDDEN); $x++)
	{
		unset($data['BDD']['TABLES'][$_TABLES_FORBIDDEN[$x]]);
	}
	
	$data['SUCCESS'] = 1;
	unset($user['PWD']);
	if(empty($user['SIGN']))
	{
		$sign = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/signatures/TECHS/null.png'); 
		$user['SIGN'] = 'data:image/png;base64,'.base64_encode($sign);
		$user['SIGN_SET'] = false;
	}
	else
	{
		$user['SIGN_SET'] = true;
	}
	$data['BDD']['TABLES']['USER'] = $user;
	
	$json = json_encode($data);
	echo $json;