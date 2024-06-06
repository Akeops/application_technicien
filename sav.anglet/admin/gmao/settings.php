<?php
	include('../inc/config.inc.php');
	if(!empty($_POST) && isset($_POST['PAPERWORK_NAME']))
	{
		$req = $bdd->prepare("
			UPDATE
				PAPERWORK
			SET
				NAME =			:name,
				BODY =			:body,
				ACTIVITIES =	:activities,
				TYPES =			:types,
				SELLING =		:selling,
				LEASING =		:leasing,
				REQUIRE_SIGN =	:require,
				COUNTRACT =		:countract,
				DESCRIPTION =	:description
			WHERE
				ID = :id
		");
		
		$req->execute(array(
			'id'			=> $_POST['ID'],
			'name'			=> $_POST['PAPERWORK_NAME'],
			'body'			=> $_POST['BODY'],
			'activities'	=> $_POST['ACTIVITIES'],
			'types'			=> $_POST['TYPES'],
			'selling'		=> $_POST['SELLING'],
			'leasing'		=> $_POST['LEASING'],
			'require'		=> $_POST['REQUIRE_SIGN'],
			'countract'		=> $_POST['COUNTRACT'],
			'description'	=> $_POST['DESCRIPTION']
		));
		$json['success'] = 1;
		echo json_encode($json);
		die();
	}
	else if(!empty($_POST) && isset($_POST['create']))
	{
		$bdd->query("
			INSERT INTO
				PAPERWORK (NAME)
			VALUES
				('Nouveau Document')
		") or die(print_r($bdd->errorInfo()));
		$result = $bdd->query("SELECT LAST_INSERT_ID() ID")->fetch(2);
		$json['success'] = 1;
		$json['id'] = $result['ID'];
		echo json_encode($json);
		die();
	}
	else if(!empty($_POST) && isset($_POST['DELETE']) && !empty($_POST['DELETE']))
	{
		$req = $bdd->prepare("
			DELETE FROM
				PAPERWORK
			WHERE
				ID = :id
		");
		
		$req->execute(array(
			'id'			=> $_POST['DELETE'],
		)) or die(print_r($req->errorInfo()));
		$json['success'] = 1;
		echo json_encode($json);
		die();
	}
?>

<link href="css/settings.css" rel="stylesheet" />

<div id="sections">
	<a title="Global" class="gmao_link"><img src="images/globals.png" /></a>
	<a title="Etablissement" class="gmao_link"><img src="images/establishment.png" /></a>
	<a title="Traification" class="gmao_link"><img src="images/prices.png" /></a>
	<a title="Gestion Utilisateur" class="gmao_link"><img src="images/user_manager.png" /></a>
	<a title="Gestion des Groupes" class="gmao_link"><img src="images/group_manager.png" /></a>
	<a title="Gestion des Agences" class="gmao_link"><img src="images/agency_manager.png" /></a>
	<a <?=($_GET['setting'] == "intervention") ? "class='selected gmao_link'" : "class='gmao_link'"?> title="Intervention" class="gmao_link" href="gmao.php?page=settings&setting=intervention"><img src="images/intervention.png" /></a>
	<a <?=($_GET['setting'] == "paperwork") ? "class='selected gmao_link'" : "class='gmao_link'"?> title="Papiers Administratif" href="gmao.php?page=settings&setting=paperwork" ><img src="images/paperwork.png" /></a>
	<a title="Gestion des Télécommandes" href="gmao.php?page=settings&setting=devices" class="gmao_link"><img src="images/devices.png" /></a>
</div>

<?php
	if($_GET['setting'] == "paperwork")
	{
		include('settings/paperwork.php');
	}
	else if($_GET['setting'] == "intervention")
	{
		include('settings/intervention_settings.php');
	}
	else if($_GET['setting'] == "devices")
	{
		include('settings/devices.php');
	}
?>