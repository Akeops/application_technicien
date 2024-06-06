<?php
	require('../inc/config.inc.php');
	require('../inc/accounts.inc.php');
	
	session_start();
	if($_SESSION['rights'] < "3"){
		header('Location: widgets/home.php');
	}

	if(!empty($_POST))
	{	
		$req = $bdd->prepare("
		UPDATE
			TARIFS
		SET
			WORKFORCE	=	:workforce,
			TVA1		=	:tva1,
			TVA2		=	:tva2,
			TVA3		=	:tva3,
			TVA4		=	:tva4,
			ZONE1		=	:zone1,
			ZONE2		=	:zone2,
			ZONE3		=	:zone3,
			ZONE4		=	:zone4
		WHERE
			ID	=	0");
		
		$req->execute(array(
			'workforce'		=>	$_POST['WORKFORCE'],
			'tva1'			=>	$_POST['TVA1'],
			'tva2'			=>	$_POST['TVA2'],
			'tva3'			=>	$_POST['TVA3'],
			'tva4'			=>	$_POST['TVA4'],
			'zone1'			=>	$_POST['ZONE1'],
			'zone2'			=>	$_POST['ZONE2'],
			'zone3'			=>	$_POST['ZONE3'],
			'zone4'			=>	$_POST['ZONE4']
		)) or die(print_r($req->errorInfo()));
		
		$req = $bdd->prepare("
		UPDATE
			TARIFS
		SET
			ZONE1		=	:zone1,
			ZONE2		=	:zone2,
			ZONE3		=	:zone3,
			ZONE4		=	:zone4
		WHERE
			ID	=	1");
		
		$req->execute(array(
			'zone1'			=>	$_POST['ZONE1_LIBELLE'],
			'zone2'			=>	$_POST['ZONE2_LIBELLE'],
			'zone3'			=>	$_POST['ZONE3_LIBELLE'],
			'zone4'			=>	$_POST['ZONE4_LIBELLE']
		)) or die(print_r($req->errorInfo()));
	}
	
	$req = $bdd->query("SELECT * FROM TARIFS") or die(print_r($req->errorInfo()));
	$tarifs = $req->fetchAll(2);
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
			<h2>TARIFICATIONS</h2>
		</div>
		<div class="sectionContent">
			<form method="post" action="">
				<table style="width:40%">
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
						<td>
						</td>
						<td colspan="2">
							<span>Coût de main d'oeuvre horaire <i>(HT)</i></span><input name="WORKFORCE" type="number" step="0.01" value="<?=number_format($tarifs[0]['WORKFORCE'],2,"."," ")?>"/>
						</td>
						<td>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="text-align:center;padding-top:20px">
							<span><b>Déplacement</b></span>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span>ZONE1</span><input name="ZONE1" type="number" step="0.01" value="<?=number_format($tarifs[0]['ZONE1'],2,"."," ")?>"/>
						</td>
						<td colspan="2">
							<span>Libellé</span><input name="ZONE1_LIBELLE" type="text" name="ZONE1_LIBELLE" value="<?=$tarifs[1]['ZONE1']?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span>ZONE2</span><input name="ZONE2" type="number" step="0.01" value="<?=number_format($tarifs[0]['ZONE2'],2,"."," ")?>"/>
						</td>
						<td colspan="2">
							<span>Libellé</span><input name="ZONE2_LIBELLE" type="text" value="<?=$tarifs[1]['ZONE2']?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span>ZONE3</span><input name="ZONE3" type="number" step="0.01" value="<?=number_format($tarifs[0]['ZONE3'],2,"."," ")?>"/>
						</td>
						<td colspan="2">
							<span>Libellé</span><input name="ZONE3_LIBELLE" type="text" value="<?=$tarifs[1]['ZONE3']?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span>ZONE4</span><input name="ZONE4" type="number" step="0.01" value="<?=number_format($tarifs[0]['ZONE4'],2,"."," ")?>"/>
						</td>
						<td colspan="2">
							<span>Libellé</span><input name="ZONE4_LIBELLE" type="text" value="<?=$tarifs[1]['ZONE4']?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="text-align:center;padding-top:20px">
							<span><b>TVA</b><span>
						</td>
					</tr>
					<tr>
						<td>
							<span>TVA A <i>(NORMAL)</i></span><input name="TVA1" type="number" step="0.01" value="<?=number_format($tarifs[0]['TVA1'],2,"."," ")?>"/>
						</td>
						<td>
							<span>TVA B <i>(INTER)</i></span><input name="TVA2" type="number" step="0.01" value="<?=number_format($tarifs[0]['TVA2'],2,"."," ")?>"/>
						</td>
						<td>
							<span>TVA C <i>(REDUIT)</i></span><input name="TVA3" type="number" step="0.01" value="<?=number_format($tarifs[0]['TVA3'],2,"."," ")?>"/>
						</td>
						<td>
							<span>TVA D <i>(ZERO)</i></span><input name="TVA4" type="number" step="0.01" value="<?=number_format($tarifs[0]['TVA4'],2,"."," ")?>"/>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top:40px">
							<input type="reset" value="Réinitialiser" onclick="">
						</td>
						<td colspan="2" style="padding-top:40px">
							<input type="submit" value="Valider les Changements" onclick="">
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>