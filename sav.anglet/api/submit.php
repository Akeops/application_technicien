<?php	
	require('inc/verify.inc.php');
	require_once('../admin/inc/config.inc.php');
	//require_once('../admin/axonaut/axonaut.php');
	
	function error($error)
	{
		$json['SUCCESS'] = 0;
		$json['ERROR'] = $error;
		echo json_encode($json);
		die();
	}
	
	if(!$rights['INTER_CREATE'])
	{
		$json['SUCCESS'] = 0;
		$json['ERROR'] = "Vous n'avez pas les autorisations requises pour la création de bons d'interventions.";
		echo json_encode($json);
		die();
	}
	else if(empty($_POST['data']) || !isset($_POST['type']))
	{
		$json['SUCCESS'] = 0;
		$json['ERROR'] = "La demande n'a pas pu être traité car il manque des informations.";
		echo json_encode($json);
		die();
	}
	
	$req = $bdd->query("
		SELECT
			*
		FROM
			TARIFS
	") or die(error($bdd->errorInfo()));
	$prices = $req->fetchAll(2);
	
	// START UPDATE
	
	$data = Array();

	for($x = 0; $x < sizeOf($_POST['data']); $x++)
	{
		
		$data[key($_POST['data'][$x])] = $_POST['data'][$x][key($_POST['data'][$x])];
	}
	
	$required = array(
		'CUSTOMER_CODE',
		'CUSTOMER_ADDRESS_LINE1',
		'CUSTOMER_EMAIL',
		'CUSTOMER_COUNTRACT',
		'CUSTOMER_SIGN',
		'CUSTOMER_LABEL',
		'CUSTOMER_NAME',
		'CUSTOMER_CIVILITY',
		'CUSTOMER_QUALITY',
		'CUSTOMER_ADDRESS_CITY',
		'CUSTOMER_ADDRESS_POSTAL_CODE',
		'AGENCY',
		'DATE_START',
		'DATE_END',
		'DESCRIPTION',
		'TRAVEL_LABEL',
		'TRAVEL_PRICE',
		'VAT_RATE'
	);
	
	$types = array('INSTALL','MAINTENANCE','TRAINING','RECOVERY','RENEWAL','DELIVERY', 'NEAR_VISIT');
	$typeCheck = 0;
	$interTypes = "";
	
	for($x = 0; $x < sizeOf($types); $x++)
	{
		if($data[$types[$x]] == "true")
		{
			$typeCheck++;
			if(empty($interTypes))
			{
				switch($types[$x])
				{
					case "INSTALL":
						$interTypes = $interTypes."INSTALLATION";
					break;
					case "MAINTENANCE":
						$interTypes = $interTypes."MAINTENANCE";
					break;
					case "TRAINING":
						$interTypes = $interTypes."FORMATION";
					break;
					case "RECOVERY":
						$interTypes = $interTypes."RECUPERATION MATERIEL";
					break;
					case "RENEWAL":
						$interTypes = $interTypes."RENOUVELLEMENT";
					break;
					case "DELIVERY":
						$interTypes = $interTypes."LIVRAISON";
					break;
					case "NEAR_VISIT":
						$interTypes = $interTypes."PRE-VISITE";
					break;
				}
			}
			else
			{
				switch($types[$x])
				{
					case "INSTALL":
						$interTypes = $interTypes.", INSTALLATION";
					break;
					case "MAINTENANCE":
						$interTypes = $interTypes.", MAINTENANCE";
					break;
					case "TRAINING":
						$interTypes = $interTypes.", FORMATION";
					break;
					case "RECOVERY":
						$interTypes = $interTypes.", RECUPERATION MATERIEL";
					break;
					case "RENEWAL":
						$interTypes = $interTypes.", RENOUVELLEMENT";
					break;
					case "DELIVERY":
						$interTypes = $interTypes.", LIVRAISON";
					break;
					case "NEAR_VISIT":
						$interTypes = $interTypes.", PRE-VISITE";
					break;
				}
			}
		}
	}
	
	if($typeCheck < 1)
	{
		$json['SUCCESS'] = 0;
		$json['ERROR'] = "Une ou plusieurs informations sont manquante : Intervention_type";
		echo json_encode($json);
		die();
	}
	
	for($x = 0; $x < sizeOf($required); $x++)
	{
		for($y = 0; $y < sizeOf($data); $y++)
		{
			if(empty($data[$required[$x]]) && $data[$required[$x]] != "0")
			{
				$json['SUCCESS'] = 0;
				$json['ERROR'] = "Une ou plusieurs informations sont manquante : ".ucwords(strtolower($required[$x]));
				echo json_encode($json);
				die();
			}
		}
	}
	
	$req = $bdd->prepare("
		SELECT
			*
		FROM
			INTER
		WHERE
			USER_UPLOAD		= :user				AND
			DATE_START		= :date_start		AND
			CUSTOMER_CODE	= :customer_code	AND
			CUSTOMER_LABEL	= :customer_label	AND
			DESCRIPTION		= :description");
	$array = array(
			'user'				=>	$user['ID'],
			'date_start'		=>	$data['DATE_START'],
			'customer_code'		=>	$data['CUSTOMER_CODE'],
			'customer_label'	=>	$data['CUSTOMER_LABEL'],
			'description'		=>	$data['DESCRIPTION']
	);
	$req->execute($array);
	
	if($req->rowCount() > 0)
	{
		$json['SUCCESS'] = 1;
		// $json['ERROR'] = "Doublon :\nCette intervention existe déjà.";
		echo json_encode($json);
		die();
	}
	
	if($prices[1]['WORKFORCE'] != "-1")
	{
		if($prices[1]['WORKFORCE'] == "1" || $data['CUSTOMER_CONTRACT'] != "true")
		{
			$start = strtotime($data['DATE_START']);
			$end = strtotime($data['DATE_END']);
			$hourPrice = $prices[0]['WORKFORCE'];
			$workTime = abs($end - $start);
			
			$retour = array();
			$price = array();
		 
			$tmp = $workTime;
			$retour['second'] = $tmp % 60;
		 
			$tmp = floor( ($tmp - $retour['second']) /60 );
			$retour['minute'] = $tmp % 60;
			$price['minute'] = ($hourPrice / 60) * $tmp;
		 
			$tmp = floor( ($tmp - $retour['minute'])/60 );
			$retour['hour'] = $tmp % 24;
			$price['hour'] = $hourPrice * $tmp;
		 
			$tmp = floor( ($tmp - $retour['hour'])  /24 );
			$retour['day'] = $tmp;
			$price['day'] = ($hourPrice * 24) * $tmp;
			
			unset($tmp);
			
			$workforcePrice = round($price['minute'] + $price['hour'] + $price['day'], 2);
		}
		else
		{
			$workforcePrice = 0;
		}
	}
	else
	{
		$workforcePrice = 0;
	}
	
	if(!empty($data['CUSTOMER_CONTRACT']))
	{
		$data['CUSTOMER_COUNTRACT'] = $data['CUSTOMER_CONTRACT'];
	}
	
	$data['DATE_LAST_MODIFY'] = null;
	$data['USER_MODIFY'] = null;
	$data['WORKFORCE_HOUR_PRICE'] = $prices[0]['WORKFORCE'];
	$data['WORKFORCE_TYPE'] = $prices[1]['WORKFORCE'];
	$data['WORKFORCE_PRICE'] = $workforcePrice;
	$data['USER_UPLOAD'] = $user['ID'];
	$data['IS_ARCHIVED'] = 0;
	$data['DATE_UPLOADED'] = date("Y-m-d H:i:s");
	
	// CHECKING COST
	
	$dateStart = new DateTime($data['DATE_START']);
	$dateEnd = new DateTime($data['DATE_END']);
	$dateStart = $dateStart->getTimeStamp();
	$dateEnd = $dateEnd->getTimeStamp();
	$interval = $dateEnd - $dateStart;
	$coef = round($interval / 3600, 2);
	$data['WORKFORCE_PRICE'] = $coef * $data['WORKFORCE_HOUR_PRICE'];
	
	if($data['CUSTOMER_COUNTRACT'])
	{
		$data['WORKFORCE_PRICE'] = 0;
		$data['TRAVEL_PRICE'] = 0;
	}
	
	$tht = $data['WORKFORCE_PRICE'] + $data['TRAVEL_PRICE'];
	$data['BILLINGS_THIRD'] = $data['BILLINGS_THIRD'] ? json_decode($data['BILLINGS_THIRD'], true) : array();
	for($x = 0; $data['BILLINGS_THIRD'] != NULL && $x < count($data['BILLINGS_THIRD']); $x++)
	{
		$item = $data['BILLINGS_THIRD'][$x];
		$tht += $item['tht'];
	}
	
	$ttc = $tht + ($tht * ($data['VAT_RATE']/100));
	$data['COST'] = $ttc;
	
	//END CHECKING COST
	
	$queryColumns = "(";
	$queryValues = "(";
	
	for($x = 0; $x < sizeOf($data); $x++)
	{
		$queryColumns = $queryColumns.key($data);
		
		if(current($data) == "true" || current($data) == "false")
		{
			$queryValues = $queryValues.current($data);
		}
		else
		{
			$queryValues = $queryValues."'".str_replace("'", "\\'", current($data))."'";
		}
		
		if($x + 1 != sizeOf($data))
		{
			$queryColumns = $queryColumns.",";
			$queryValues = $queryValues.",";
		}
		else
		{
			$queryColumns = $queryColumns.")";
			$queryValues = $queryValues.")";
		}
		
		next($data);
	}
	
	$bdd->query
	("
		INSERT INTO INTER 
			".$queryColumns."
		VALUES
			".$queryValues
	) or die(error($bdd->errorInfo()));
	
	$json['SUCCESS'] = 1;
	
	$req = $bdd->query
	("
		SELECT
			*
		FROM
			INTER
		WHERE
			ID = LAST_INSERT_ID()
	") or die(print_r($bdd->errorInfo()));
	$inter = $req->fetch(2);
	
	//Génération et envois mail de l'intervention.
	require_once('../admin/inc/config.inc.php');
	//set the parameters for the sendmail
	$_GET['inter'] = $inter['ID'];
	require_once('../admin/inc/sendmail.php');
	
	echo(json_encode($json));
?>