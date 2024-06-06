<?php

	require('../inc/config.inc.php');
	require('../inc/accounts.inc.php');

	if(empty($_GET) || $_SESSION['rights'] < "3")
	{
		header('Location: widgets/home.php');
		die();
	}
	
	$req = $bdd->prepare("
		SELECT
			*
		FROM
			USERS_INTER
		WHERE
			ID = :userID
		") or die(print_r($req->errorInfo()));

	$req->execute(array(
		'userID'	=> $_GET['userID']
		)) or die(print_r($req->errorInfo()));
	
	$userInfo = $req->fetch(2);
	
	$req = $bdd->query("
		SELECT
			*
		FROM
			INTERVENTIONS
		") or die(print_r($req->errorInfo()));
		
	$inter = $req->fetchAll(2);
			
	
	$req = $bdd->prepare("
		SELECT
			*
		FROM
			INTERVENTIONS
		WHERE
			TECHNICIEN = :userID
		") or die(print_r($req->errorInfo()));
	
	$req->execute(array(
		'userID'	=>	$_GET['userID']
	)) or die(print_r($req->errorInfo()));
	
	$interMake = 0;
	$interInst = 0;
	$interMain = 0;
	$interForm = 0;
	$interReno = 0;
	$interRecu = 0;
	$h = 0;
	$interDate;
	
	while($interM = $req->fetch())
	{
		
		for($x = 12; $x != 0; $x--)
		{
			$date = $interM['DATE'];
			$date = explode("-",$date);
			$month = intval($date[1]);
			
			if($month == $x && $date[0] == date('Y'))
			{
				if(empty($interDate[$x]))
				{
					$interDate[$x] = 1;
				}
				else
				{
					$interDate[$x]++;
				}
			}			
		}
		
		$s = strtotime($interM['HEURE_ARRIVEE']);
		$e = strtotime($interM['HEURE_DEPART']);
		
		$heures = $e - $s;
		
		$h = $h + $heures;
		
		switch($interM['INSTALLATION'])
		{
			case 1:
				$interInst++;
			break;
		}
		
		switch($interM['MAINTENANCE'])
		{
			case 1:
				$interMain++;
			break;
		}
		
		switch($interM['FORMATION'])
		{
			case 1:
				$interForm++;
			break;
		}
		
		switch($interM['RENOUVELLEMENT'])
		{
			case 1:
				$interReno++;
			break;
		}
		
		switch($interM['RECUPERATION'])
		{
			case 1:
				$interRecu++;
			break;
		}
		
		$interMake++;
	}
?>

<!DOCTYPE html>

<html>
	<head>
		<link href="https://fonts.googleapis.com/css?family=Cabin Condensed" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
	</head>

	<body>
		<div class="section">
			<h2><?=$userInfo['NAME']?></h2>
		</div>
		<table>
			<tr>
				<td>
					<div style="position:relative;width:100%;height:100%">
						<canvas id="interStats"></canvas>
					<div>
				</td>
				<td style="text-align:center;font-size:3em;vertical-align:middle">
					<?php
						$minutes =	$h			/	60;
						$heures =	$minutes	/	60;
						$jours =	$heures		/	24;
					
						echo "<b>".round($heures,1)." Heure(s)</b> ou <b>".round($jours,1)." Jour(s)</b><br>";
						echo "Soit <b>".($interMake ? round($heures/$interMake,1) : "0")." Heure(s)</b> par Intervention";
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<canvas id="progress"></canvas>
				</td>
			</tr>
		</table>
	</body>
</html>

<script>
	var ctx = document.getElementById('interStats').getContext('2d');
	var interStats = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: ['Réalisées : (<?=$interMake?>)','Maintenances : (<?=$interMain?>)','Installations : (<?=$interInst?>)','Formations : (<?=$interForm?>)','Récuperations : (<?=$interRecu?>)','Renouvellements : (<?=$interReno?>)'],
			datasets: [{
				label: 'Nombre d\'Intervention',
				data: [<?=$interMake?>,<?=$interMain?>,<?=$interInst?>,<?=$interForm?>,<?=$interRecu?>,<?=$interReno?>],
				backgroundColor: [
					'rgba(54, 162, 235, 1)',
					'rgba(255, 99, 132, 1)',
					'rgba(255, 159, 64, 1)',
					'rgba(255, 205, 86, 1)',
					'rgba(75, 192, 192, 1)',
					'rgba(153, 102, 255, 1)'
				]
			}]
		},
		options: {
			legend: {
				display:	false
			},
			maintainAspectRatio: false,
			title:{
				display:	true,
				text:		'Interventions'
			}
		}
	});

	var ctx = document.getElementById('progress').getContext('2d');
	var progress = new Chart(ctx, {
		type: 'line',
		data: {
			labels: ["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre"],
			datasets: [{
				type: 'line',
				fill: true,
				pointRadius: 0,
				pointHitRadius: 50,
				borderColor: 'red',
				lineTension: 0.0,
				color: 'red',
				label: 'Nombre d\'Intervention',
				data: [
					<?=$interDate[1] ? $interDate[1] : "0"?>,
					<?=$interDate[2] ? $interDate[2] : "0"?>,
					<?=$interDate[3] ? $interDate[3] : "0"?>,
					<?=$interDate[4] ? $interDate[4] : "0"?>,
					<?=$interDate[5] ? $interDate[5] : "0"?>,
					<?=$interDate[6] ? $interDate[6] : "0"?>,
					<?=$interDate[7] ? $interDate[7] : "0"?>,
					<?=$interDate[8] ? $interDate[8] : "0"?>,
					<?=$interDate[9] ? $interDate[9] : "0"?>,
					<?=$interDate[10] ? $interDate[10] : "0"?>,
					<?=$interDate[11] ? $interDate[11] : "0"?>,
					<?=$interDate[12] ? $interDate[12] : "0"?>
				],
			}]
		},
		options: {
			legend: {
				display:	false
			},
			maintainAspectRatio: false,
			title:{
				display:	true,
				text:		'Interventions (<?=date('Y')?>)'
			}
		}
	});
</script>

<style>
	body,html{
		margin:				0;
		background:			white;
	}
	
	table{
		margin:				75px auto 20px auto;
		width:				85vw;
		height:				25vh;
		border-collapse:	collapse;
	}
	
	table tr{
		height:				40vh;
	}
	
	table td{
		width:				50%;
		padding:			10px;
	}
	
	canvas{
		margin:				0 auto;
	}
</style>