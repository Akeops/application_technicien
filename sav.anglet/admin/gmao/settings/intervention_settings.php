<?php
	if(isset($_POST['create']) && $_POST['page'] == "type" && (!empty($_POST['NAME']) && !empty($_POST['DESCRIPTION'])))
	{
		require('../../inc/config.inc.php');
		$req = $bdd->prepare("
			INSERT INTO
				ACTIVITIES (NAME,DESCRIPTION)
			VALUES
				(:name,:description)
			");
		$req->execute(array(
			'name'			=>	$_POST['NAME'],
			'description'	=>	$_POST['DESCRIPTION']
		)) or die(print_r($req->errorInfo()));
		
		$json['success'] = true;
		
		echo JSON_encode($json);
		
		die();
	}
	else if(isset($_POST['delete']) && $_POST['page'] == "type" && !empty($_POST['ID']))
	{
		require('../../inc/config.inc.php');
		$req = $bdd->prepare("
			DELETE FROM
				ACTIVITIES
			WHERE
				ID = :id
			");
		$req->execute(array(
			'id'			=>	$_POST['ID']
		)) or die(print_r($req->errorInfo()));
		
		$json['success'] = true;
		
		echo JSON_encode($json);
		
		die();
	}

	require_once('../inc/config.inc.php');
	if(!isset($bdd))
	{
		header('Location: /');
		die();
	}
	
	$req = $bdd->query("SELECT * FROM ACTIVITIES");
?>

<link href="css/intervention_settings.css" rel="stylesheet" />
<script src="scripts/intervention_settings.js" />

<div id="navbar">
	<a href="javascript:void(0)">
		Global
	</a>
	<a class="gmao_link" href="gmao.php?page=settings&setting=intervention&intervention=activities">
		Activités
	</a>
</div>

<div id="activities" style="<?=($_GET['intervention'] == "activities") ? "" : "display:none"?>">
	<table cellspacing=0 cellpadding=0 id="activities">
		<?php while($type = $req->fetch(2)):?>
			<tr>
				<td title="<?=$type['DESCRIPTION']?>">
					<a class="gmao_link" href="javascript:void(0)" onclick="editType(<?=$type['ID']?>)"><?=$type['NAME']?></a>
				</td>
				<td class="tools">
					<a href="javascript:void(0)" onclick="editType(<?=$type['ID']?>)"><img src="images/edit.png"></a>
					<a href="javascript:void(0)" onclick="openDeleteType(<?=$type['ID']?>,this)"><img src="images/delete.png"></a>
				</td>
			</tr>
		<?php endwhile; ?>
		<tr>
			<td colspan="2" style="padding:0">
				<input type="button" value="+" style="border: none;background: rgba(0,0,0,0.2);width: 100%;height: 100%;outline: none;color: white;font-weight: bold;font-size: 20px;cursor:pointer;" onclick="addType()"/>
			</td>
		</tr>
	</table>

	<div style="display:none" id="uiAddType" class="ui">
		<table>
			<tr>
				<td>
					<h1>Ajouter</h1>
				</td>
			</tr>
			<tr>
				<td>
					<input name="NAME" type="text" placeholder="Nom du Type de Produit"/>
				</td>
			</tr>
			<tr>
				<td>
					<textarea cols="60" rows="20" name="DESCRIPTION" placeholder="Description"></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<input type="button" value="ANNULER" onclick="closeType()"/>
					<input type="button" value="CREER" onclick="createType()"/>
				</td>
			</tr>
		</table>
	</div>
	
	<div style="display:none" id="uiDeleteType" class="ui">
		<table>
			<tr>
				<td>
					<h1>Supprimer</h1>
				</td>
			</tr>
			<tr>
				<td id="deleteTypeMsg">
					<h3></h3>
				</td>
			</tr>
			<tr>
				<td id="deleteTypeDesc">
				</td>
			</tr>
			<tr>
				<td>
					Cette action est irréversible. Continuer ?
				</td>
			</tr>
			<tr>
				<td>
					<input type="button" value="NON" onclick="$('#uiDeleteType')[0].style.display = 'none'"/>
					<input type="button" value="OUI" id="uiButtonYes" onclick=""/>
				</td>
			</tr>
		</table>
	</div>
</div>