<?php
	session_start();
	require('inc/login.inc.php');

	if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
		header('Location:	index.php');
		die();
	}
	
	header('Content-Type: application/json');
	
	$data['URL'] = "?".$_SERVER['QUERY_STRING'];
	
	switch($_GET['page'])
	{
		case 'about':
			$file = 'about.php';
		break;
		case 'stats':
			$file = 'stats.php';
		break;
		case 'settings':
			$file = 'settings.php'.$data['URL'];
			$data['TITLE'] = 'Paramètres';
		break;
		case 'inter':
			$file = 'intervention/intervention.php'.$data['URL'];
			$data['TITLE'] = 'Dernières Interventions';
		break;
		case 'customers':
			$file = 'customers.php'.$data['URL'];
			$data['TITLE'] = 'Listing Clientèle';
		break;
		default:
			$file = 'home.php';
			$data['TITLE'] = 'Accueil';
		break;
	}
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, str_replace($_SERVER['argv'][0],"",$_SERVER['SCRIPT_URI']).'gmao/'.$file);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close($ch);
	
	$data['URL'] = "index.php".$data['URL'];
	$data['HTML'] = $output;
	$data['SUCCESS'] = true;

	echo json_encode($data);
?>