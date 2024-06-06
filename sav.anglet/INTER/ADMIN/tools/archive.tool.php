<?php
	require('../inc/config.inc.php');
	require('../inc/accounts.inc.php');
	
	session_start();
	
	if( (intval($_GET['inter']) == 0) || (empty($_GET['inter'])) || ($_SESSION['rights'] < "2") )
	{
		header('Location:	../index.php?_request=inter&_action=list');
	}
	
	$req = $bdd->prepare("SELECT * FROM INTERVENTIONS WHERE ID = :id");
	$req->execute(array( 'id' => $_GET['inter'])) or die(print_r($req->errorInfo()));
	$inter = $req->fetch();
	
	if($inter['ARCHIVE'] == 1)
	{
		$req = $bdd->prepare("UPDATE INTERVENTIONS SET ARCHIVE = '2' WHERE ID = :id");
		$req = $req->execute(array( 'id' => $_GET['inter'])) or die(print_r($req->errorInfo()));
		echo("
			<script>
				window.parent.location = '../widgets/list_inter.php?_action=archives';
			</script>
		");
	}
	else
	{
		$req = $bdd->prepare("UPDATE INTERVENTIONS SET ARCHIVE = '1' WHERE ID = :id");
		$req = $req->execute(array( 'id' => $_GET['inter'])) or die(print_r($req->errorInfo()));
		echo("
			<script>
				window.parent.location = '../widgets/list_inter.php?_action=list';
			</script>
		");
	}
?>