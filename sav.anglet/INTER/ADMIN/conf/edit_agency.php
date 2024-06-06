<?php
	require('../inc/config.inc.php');
	require('../inc/accounts.inc.php');
	
	session_start();
	
	if(!$_SESSION['rights']['ADMIN'] && !$_SESSION['rights']['AGENCY'] && !$_SESSION['rights']['AGENCY_EDIT']){
		header('Location: ../errors/error_rights.php');
		die();
	}
	elseif(!empty($_POST['NAME']) && !empty($_POST['ADDRESS_LINE1']) && !empty($_POST['CITY']) && !empty($_POST['POSTAL_CODE']))
	{	
		if(!empty($_GET['create']))
		{
			$bdd->query("
				INSERT INTO
					AGENCES (NAME)
				VALUES
					('".$_POST['NAME']."')
			") or die(print_r($bdd->errorInfo()));
			
			$req = $bdd->query("
				SELECT
					*
				FROM
					AGENCES
				WHERE
					".key($_POST)." = '".current($_POST)."'
			") or die(print_r($bdd->errorInfo()));
			$agency = $req->fetch(2);
			
			for($x = 0; $x < sizeOf($_POST); $x++)
			{
				$bdd->query("
					UPDATE
						AGENCES
					SET
						".key($_POST)." = '".current($_POST)."'
					WHERE
						ID = ".$agency['ID']."
				");
				next($_POST);
			}
			header('Location:	agency_manager.php');
			die();
		}
		elseif(is_numeric($_SESSION['agencyID']))
		{	
			for($x = 0; $x < sizeOf($_POST); $x++)
			{
				$bdd->query("
					UPDATE
						AGENCES
					SET
						".key($_POST)." = '".current($_POST)."'
					WHERE
						ID = ".$_SESSION['agencyID']."
				");
				next($_POST);
			}
			header('Location:	agency_manager.php');
			die();
		}
		else
		{
			header('Location:	agency_manager.php');
			die();
		}
	}
	elseif(is_numeric($_GET['disable']))
	{
		$req = $bdd->prepare("
			UPDATE
				AGENCES
			SET
				DISABLED = 1
			WHERE
				ID = :agencyID
		");
		
		$req->execute(array(
			'agencyID'	=> $_GET['disable']
		)) or die(print_r($req->errorInfo()));
		
		header('Location:	agency_manager.php');
		die();
	}
	elseif(is_numeric($_GET['enable']))
	{
		$req = $bdd->prepare("
			UPDATE
				AGENCES
			SET
				DISABLED = 0
			WHERE
				ID = :agencyID
		");
		
		$req->execute(array(
			'agencyID'	=> $_GET['enable']
		)) or die(print_r($req->errorInfo()));
		
		header('Location:	agency_manager.php');
		die();
	}
	elseif(is_numeric($_POST['agencyID']))
	{
		$req = $bdd->query("
			SELECT
				*
			FROM
				AGENCES
			WHERE
				ID = '".$_POST['agencyID']."'
		") or die(print_r($bdd->errorInfo()));
		
		$agency = $req->fetch(2);
		
		if(!$agency)
		{
			header('Location:	agency_manager.php');
			die();
		}
	}
	else
	{
		header('Location:	agency_manager.php');
		die();
	}
	
	$_SESSION['agencyID'] = $agency['ID'];
?>

<html>

	<head>
		<link href="https://fonts.googleapis.com/css?family=Cabin Condensed" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="style.css">
		<script src="../scripts/jQuery.min.js"></script>
		<script src="conf.js"></script>
	</head>

	<body>
		<div>
			<a href="agency_manager.php" id="btnback"><p>X</p></a>
		</div>
		<div class="section">
			<h2>EDITER L'AGENCE : <i><?=$agency['NAME']?></i></h2>
		</div>
		<div class="sectionContent">
			<form method="post" action="edit_agency.php" id="agencyForm">
				<table style="width:40%">
					<tr>
						<td colspan="2">
							<?php if(isset($_GET['error'])):?>								
								<span><span>Une erreur s'est produite. Vérifiez que les champs renseignés soient correcte ou contactez un administrateur.</span></span>								
							<?php endif;?>
						</td>
					</tr>
					<tr>
						<td>
							<span>Nom<span>*</span></span><input onkeyup="setValidate()" name="NAME" type="text" value="<?=$agency['NAME']?>" placeholder="Nom de l'Agence" required />
						</td>
						<td>
							<span>Adresse<span>*</span></span><input onkeyup="setValidate()" autocomplete="agencyname" name="ADDRESS_LINE1" type="text" value="<?=$agency['ADDRESS_LINE1']?>" placeholder="6 rue Adresse Exemple" required />
						</td>
					</tr>
					<tr>
						<td>
							<span>Téléphone<span>*</span></span><input onkeyup="setValidate()" name="PHONE" type="phone" placeholder="09 87 65 43 21" value="<?=$agency['PHONE']?>" />
						</td>
						<td>
							<span>Complément d'Adresse</span><input onkeyup="setValidate()" name="ADDRESS_LINE2" type="text" value="<?=$agency['ADDRESS_LINE2']?>" placeholder="Zone Exemple" />
						</td>
					</tr>
					<tr>
						<td>
							<span>FAX</span><input onkeyup="setValidate()" name="FAX" type="phone" value="<?=$agency['FAX']?>" placeholder="05 76 54 32 10" />
						</td>
						<td>
							<span>Ville<span>*</span></span><input onkeyup="setValidate()" name="CITY" type="text" value="<?=$agency['CITY']?>" placeholder="Ville-sur-Exemple" required />
						</td>
					</tr>
					<tr>
						<td>
							<span>Mail</span><input onkeyup="setValidate()" name="MAIL" type="text" value="<?=$agency['MAIL']?>" placeholder="contact@exemple.com" />
						</td>
						<td>
							<span>Code Postal<span>*</span></span><input onkeyup="setValidate()" name="POSTAL_CODE" type="text" value="<?=$agency['POSTAL_CODE']?>" placeholder="33000" required />
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span>Autre</span><input onkeyup="setValidate()" name="OTHER" type="text" value="<?=$agency['OTHER']?>" placeholder="Informations Complémentaires etc..." />
						</td>
					</tr>
					<tr>
						<td>
							<span><b><span>* </span>: Champs Obligatoires.</b></span>
						</td>
						<td>
						</td>
					</tr>
					<tr>
						<td>
							<input type="reset" value="Réinitialiser">
						</td>
						<td>
							<input type="submit" value="Valider les Changements" id="validate">
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>