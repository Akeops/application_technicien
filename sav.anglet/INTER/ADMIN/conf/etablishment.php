<?php
	require('../inc/config.inc.php');
	require('../inc/accounts.inc.php');
	
	session_start();
	if($_SESSION['rights'] < "3"){
		header('Location: widgets/home.php');
	}

	if(!empty($_POST))
	{
		echo "<pre>";
			print_r($_POST);
		echo "</pre>";
		
		$req = $bdd->prepare("
		UPDATE
			ETABLISSEMENT
		SET
			SOCIAL_REASON	=	:social_reason,
			TRADE_NAME		=	:trade_name,
			SIRET			=	:siret,
			APE				=	:ape,
			TVA_INTRA		=	:tva_intra,
			TELEPHONE1		=	:telephone1,
			TELEPHONE2		=	:telephone2,
			FAX1			=	:fax1,
			FAX2			=	:fax2,
			ADDRESS_LINE1	=	:address_line1,
			ADDRESS_LINE2	=	:address_line2,
			POSTAL_CODE		=	:postal_code,
			CITY			=	:city,
			COUNTRY			=	:country,
			MAIL1			=	:mail1,
			MAIL2			=	:mail2,
			WEBSITE1		=	:website1,
			WEBSITE2		=	:website2,
			END_TEXT		=	:end_text,
			OTHER			=	:other
		WHERE
			ID = '1'");
		
		$req->execute(array(
			'social_reason'		=>	$_POST['SOCIAL_REASON'],
			'trade_name'		=>	$_POST['TRADE_NAME'],
			'siret'				=>	$_POST['SIRET'],
			'ape'				=>	$_POST['APE'],
			'tva_intra'			=>	$_POST['TVA_INTRA'],
			'telephone1'		=>	$_POST['TELEPHONE1'],
			'telephone2'		=>	$_POST['TELEPHONE2'],
			'fax1'				=>	$_POST['FAX1'],
			'fax2'				=>	$_POST['FAX2'],
			'address_line1'		=>	$_POST['ADDRESS_LINE1'],
			'address_line2'		=>	$_POST['ADDRESS_LINE2'],
			'postal_code'		=>	$_POST['POSTAL_CODE'],
			'city'				=>	$_POST['CITY'],
			'country'			=>	$_POST['COUNTRY'],
			'mail1'				=>	$_POST['MAIL1'],
			'mail2'				=>	$_POST['MAIL2'],
			'website1'			=>	$_POST['WEBSITE1'],
			'website2'			=>	$_POST['WEBSITE2'],
			'end_text'			=>	$_POST['END_TEXT'],
			'other'				=>	$_POST['OTHER']
		)) or die(print_r($req->errorInfo()));
	}
	
	$req = $bdd->query("SELECT * FROM ETABLISSEMENT") or die(print_r($req->errorInfo()));
	$etablishment = $req->fetch(2);
?>

<head>
	<link href="https://fonts.googleapis.com/css?family=Cabin Condensed" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="../scripts/jQuery.min.js"></script>
	<script src="conf.js"></script>
</head>

<html>
	<body>
		<div class="section">
			<h2>ETABLISSEMENT</h2>
		</div>
		<div class="sectionContent">
			<form method="post" action="">
				<table id="sectionEtablishment">
					<tr>
						<td>
						</td>
						<td>
						</td>
						<td>
						</td>
						<td>
						</td>
					</tr>
					<tr>
						<td colspan="2" rowspan="3" style="text-align:center;background:white">
							<img src="img/logo_etablishment.png" height="100%"/>
						</td>
						<td colspan="2">
							<span>Raison Social<span>*</span></span>
							<input name="SOCIAL_REASON" type="text" value="<?=$etablishment['SOCIAL_REASON']?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span>Nom Commercial</span>
							<input name="TRADE_NAME" type="text" value="<?=$etablishment['TRADE_NAME']?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span>Numéro de Siret<span>*</span></span>
							<input name="SIRET" type="text" value="<?=$etablishment['SIRET']?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span>Changer Logo :</span>
							<input name="LOGO" type="file" value=""/>
						</td>
						<td>
							<span>Code APE<span>*</span></span>
							<input name="APE" type="text" value="<?=$etablishment['APE']?>"/>
						</td>
						<td>
							<span>TVA Intra<span>*</span></span>
							<input name="TVA_INTRA" type="text" value="<?=$etablishment['TVA_INTRA']?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span>Téléphone 1</span>
							<input name="TELEPHONE1" type="tel" value="<?=$etablishment['TELEPHONE1']?>"/>
						</td>
						<td colspan="2">
							<span>Adresse</span>
							<input name="ADDRESS_LINE1" type="text" value="<?=$etablishment['ADDRESS_LINE1']?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span>Téléphone 2</span>
							<input name="TELEPHONE2" type="tel" value="<?=$etablishment['TELEPHONE2']?>"/>
						</td>
						<td colspan="2">
							<span>Complément d'Adresse</span>
							<input name="ADDRESS_LINE2" type="text" value="<?=$etablishment['ADDRESS_LINE2']?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span>Mail 1</span>
							<input name="MAIL1" type="mail" value="<?=$etablishment['MAIL1']?>"/>
						</td>
						<td>
							<span>Code Postal</span>
							<input name="POSTAL_CODE" type="text" value="<?=$etablishment['POSTAL_CODE']?>"/>
						</td>
						<td>
							<span>Ville</span>
							<input name="CITY" type="text" value="<?=$etablishment['CITY']?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span>Mail 2</span>
							<input name="MAIL2" type="mail" value="<?=$etablishment['MAIL2']?>"/>
						</td>
						<td colspan="2">
							<span>Pays</span>
							<input name="COUNTRY" type="text" value="<?=$etablishment['COUNTRY']?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span>Fax 1</span>
							<input name="FAX1" type="text" value="<?=$etablishment['FAX1']?>"/>
						</td>
						<td colspan="2">
							<span>Fax 2</span>
							<input name="FAX2" type="text" value="<?=$etablishment['FAX2']?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<span>Autres Informations</span>
							<textarea name="OTHER" style="height:auto;resize:none;text-align:center;" rows="5"><?=$etablishment['OTHER']?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<span>Informations Légale <i>(affiché en bas de page lors de la génération de documents PDF)</i></span>
							<textarea name="END_TEXT" style="height:auto;resize:none;text-align:center;" rows="5"><?=$etablishment['END_TEXT']?></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="text-align:left">
							<b><span><span>*</span> : Nécessaire à la validation de votre licence.</span></b>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="button" onclick="reset('1')" value="Reinitialiser"/>
						</td>
						<td colspan="2">
							<input type="submit" value="Valider les changements"/>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>