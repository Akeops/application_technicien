<?php
	require('../inc/config.inc.php');
	require('../inc/accounts.inc.php');
	
	session_start();
	
	$file = fopen("../../BDD/CLIENTS.csv", "r");
		
	$bdd->query("TRUNCATE CLIENTS");
	
	$import_sql = "
		INSERT INTO
			CLIENTS (CODE, INTITULE, NOM, TELEPHONE1, EMAIL, ADLIVR_LIGNE1, ADLIVR_LIGNE2, ADLIVR_VILLE, ADLIVR_CODE_POSTAL, CONTRAT_DU, CONTRAT_AU, CODE_VENDEUR, SOLDE)
		VALUES
			(:code, :intitule, :nom, :telephone, :email, :adresse1, :adresse2, :ville, :code_postal, :contrat_du, :contrat_au, :commercial, :solde)";
	
	while (($column = fgetcsv($file, 10000, ";")) !== FALSE) {
		$req = $bdd->prepare($import_sql);
		$req->execute(array(
			'code'			=>	$column[0],
			'intitule'		=>	$column[1],
			'nom'			=>	$column[2],
			'telephone'		=>	$column[3],
			'email'			=>	$column[4],
			'adresse1'		=>	$column[5],
			'adresse2'		=>	$column[6],
			'ville'			=>	$column[7],
			'code_postal'	=>	$column[8],
			'contrat_du'	=>	$column[9],
			'contrat_au'	=>	$column[10],
			'commercial'	=>	$column[11],
			'solde'			=>	$column[12]
			)
		) or die(print_r($req->errorInfo()));;
	}
	
	if( (intval($_GET['inter']) == 0) || (empty($_GET['inter'])) || ($_SESSION['rights'] < "2") ){
		header('Location:	../index.php?_request=inter&_action=list');
	}
	
	if($_POST['SENDMAIL'] === "true"):?>
		<script src="/INTER/ADMIN/scripts/jQuery.min.js"></script>
		<script>
			$.post({
				url: "../widgets/view_inter.php?pdf=1",
				data: {
					ID: "<?=$_GET['inter']?>",
					SENDMAIL: true
				}
			});
			
			window.parent.location.href = '../widgets/list_inter.php?inter=<?=$_GET['inter']?>';
		</script>
	<?php elseif($_POST['SENDMAIL'] === "false"):?>
		<script>
			window.parent.location.href = '../widgets/list_inter.php?inter=<?=$_GET['inter']?>';
		</script>
	<?php
	endif;
	
	if(!empty($_POST)){
		update($_GET['inter']);
	}
	
	$req = $bdd->prepare("
		SELECT
			*
		FROM
			INTERVENTIONS
		WHERE
			ID = :id");
			
	$req->execute(array('id' => intval($_GET['inter']))) or die(print_r($req->errorInfo()));
	
	$inter = $req->fetch(2);
	
	$req = $bdd->prepare("
		SELECT
			*
		FROM
			TARIFS");
			
	$req->execute() or die(print_r($req->errorInfo()));
	
	$tarif = $req->fetchAll(2);
	
	$req = $bdd->prepare("
		SELECT
			*
		FROM
			ETABLISSEMENT");
			
	$req->execute() or die(print_r($req->errorInfo()));
	
	$etablissement = $req->fetch(2);
	
	$produits = $bdd->query("SELECT * FROM PRODUITS") or die(print_r($req->errorInfo()));
	
	$techs = $bdd->query("SELECT * FROM USERS_INTER") or die(print_r($req->errorInfo()));
	
	if(!$inter){
		header('Location:	../index.php?_request=inter&_action=list');
	}
	
	$req = $bdd->query("SELECT * FROM CLIENTS");
?>

<html>
	<head>
		<?php include ('../inc/head.inc.php');?>
	</head>
	
	<body style="visibility:hidden" onload="document.getElementsByTagName('body')[0].style.visibility = 'visible'; document.getElementById('textLoading').style.visibility = 'hidden';">
		<span id="textLoading" style="visibility:visible;margin:0 auto;position:absolute;bottom:0;background:lightyellow"><p>Chargement...</p></span>
		
		<form id="UPDATE" method="post" action="">
			<div id="tabContent">
				<div id="btnCLIENT" class="tabs" onclick="tab('CLIENT')">
					CLIENT
				</div>
				<div id="btnINFOS" class="tabs" onclick="tab('INFOS')">
					ENTETE
				</div>
				<div id="btnCOUTS" class="tabs" onclick="tab('COUTS')">
					COÛTS
				</div>
				<div id="btnDESC" class="tabs" onclick="tab('DESC')">
					CORPS
				</div>
				<div id="btnFACTU" class="tabs" onclick="tab('FACTU')">
					FACTURATION TIERCE
				</div>
				<div id="btnSIGNS" class="tabs" onclick="tab('SIGNS')">
					SIGNATURES
				</div>
			</div>

			<table id="CLIENT" style="border-collapse:collapse">
				<tbody id="clients">
					<tr>
						<td>
							CODE :<br><input class="option infosClient" type="text" name="CODE_CLIENT" value="<?=$inter['CODE_CLIENT']?>">
						</td>
						<td>
							NOM :<br><input class="option infosClient" type="text" name="CLIENT" value="<?=$inter['CLIENT']?>">
						</td>
						<td>
							TELEPHONE :<br><input class="option infosClient" type="text" name="TELEPHONE" value="<?=$inter['TELEPHONE']?>">
						</td>
						<td>
							EMAIL :<br><input class="option infosClient" type="text" name="EMAIL" value="<?=$inter['EMAIL']?>">
						</td>
						<td colspan="3">
							ADRESSE :<br><textarea class="option infosClient" tabindex="0" name="ADRESSE" rows="3" cols="100%"><?=$inter['ADRESSE']?></textarea>
						</td>
					</tr>
					<tr style="border-bottom:1px black solid;">
						<td style="text-align:right" colspan="7">
							<button type="button" onclick="resetClient()">Reset</button>
						</td>
						<script>
							function resetClient()
							{
								var inputs = document.getElementsByClassName("infosClient");
								
								for(var x = 0; x < inputs.length; x++)
								{
									inputs[x].value = inputs[x].defaultValue;
								}
							}
						</script>
					</tr>
					<tr>
						<td colspan="6" style="padding-top:20px">
							<input type="search"  value="<?=$inter['CODE_CLIENT']?>" placeholder="Rechercher..." id="searching"><button type="button" onclick="SEARCH()">FIND</button>
						</td>
					</tr>
					<tr id="client_head">
						<td>
							CODE
						</td>
						<td>
							NOM
						</td>
						<td>
							TELEPHONE
						</td>
						<td>
							EMAIL
						</td>
						<td>
							CONTRAT DU
						</td>
						<td>
							CONTRAT AU
						</td>
						<td>
							SOLDE
						</td>
					</tr>
					<?php
						while($client = $req->fetch()):
					?>
						<tr class="CLIENT" id="<?=$client['CODE']?>" onclick="SELECTED(<?=$client['CODE']?>)" style="display:none;">
							<td class="code">
								<?php echo $client['CODE']; ?>
							</td>
							<td class="name">
								<?php echo $client['NOM']; ?>
							</td>
							<td class="telephone">
								<?php echo $client['TELEPHONE1']; ?>
							</td>
							<td class="mail">
								<?php echo $client['EMAIL']; ?>
							</td>
							<td class="contrat_du">
								<?php echo $client['CONTRAT_DU']; ?>
							</td>
							<td class="contrat_au">
								<?php echo $client['CONTRAT_AU']; ?>
							</td>
							<td class="solde">
								<?php echo $client['SOLDE']; ?>
							</td>
							<td class="address"><?php echo($client['ADLIVR_LIGNE1']."\n".$client['ADLIVR_LIGNE2']."\n".$client['ADLIVR_CODE_POSTAL']." ".$client['ADLIVR_VILLE']);?></td>
						</tr>
					<?php
						endwhile;
					?>
				</tbody>
			</table>
			<table id="INFOS" style="">					<!-- INFORMATIONS D'ARRIVE/DEPART/CONTRAT/TVA-->
				<tr>
					<td colspan="2">
						Modification de l'Intervention N°<b><?=$inter['ID']?></b>
					</td>
					<td colspan="2">
						Client : <b><?=$inter['CLIENT']?></b>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						<span>Agence :</span>
					</td>
					<td>
						<select class="option" name="AGENCE">
						<?php if($inter['AGENCE']): ?>
							<option value="0">Floirac</option>
							<option value="1" selected>Bayonne</option>
						<?php else: ?>
							<option value="0" selected>Floirac</option>
							<option value="1">Bayonne</option>
						<?php endif; ?>
						</select>
					</td>
					<td style="text-align:right">
						<span>Sous Contrat :</span>
					</td>
					<td class="option">
						<select class="option" name="CONTRAT" onchange="hour()">
						<?php if($inter['CONTRAT']): ?>
							<option value="1" selected>Oui</option>
							<option value="0">Non</option>
						<?php else: ?>
							<option value="1">Oui</option>
							<option value="0" selected>Non</option>
						<?php endif; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						<label for="INS">INSTALLATION :</label>
					</td>
					<td>
						<input type="hidden" value="0" name="INSTALLATION">
						<input type="checkbox" value="1" name="INSTALLATION" id="INS" <?php if($inter['INSTALLATION']){ echo "checked";}?>>
					</td>
					<td style="text-align:right">
						<label for="FOR">FORMATION :</label>
					</td>
					<td>
						<input type="hidden" value="0" name="FORMATION">
						<input type="checkbox" value="1" name="FORMATION" id="FOR" <?php if($inter['FORMATION']){ echo "checked";}?>>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						<label for="MAI">MAINTENANCE :</label>
					</td>
					<td>
						<input type="hidden" value="0" name="MAINTENANCE">
						<input type="checkbox" value="1" name="MAINTENANCE" id="MAI" <?php if($inter['MAINTENANCE']){ echo "checked";}?>>
					</td>
					<td style="text-align:right">
						<label for="REN">RENOUVELLEMENT :</label>
					</td>
					<td>
						<input type="hidden" value="0" name="RENOUVELLEMENT">
						<input type="checkbox" value="1" name="RENOUVELLEMENT" id="REN" <?php if($inter['RENOUVELLEMENT']){ echo "checked";}?>>
					</td>
				</tr>
				<tr>
					<td colspan="3" style="text-align:right">
						<label for="REC">RECUPERATION MATERIEL(S) :</label>
					</td>
					<td>
						<input type="hidden" value="0" name="RECUPERATION">
						<input type="checkbox" value="1" name="RECUPERATION" id="REC" <?php if($inter['RECUPERATION']){ echo "checked";}?>>
					</td>
				</tr>
			</table>
			<table id="COUTS" style="">					<!-- COUTS DU DEPLACEMENT -->
				<tr>
					<td style="text-align:right">
						<span>Prix Main d'Oeuvre :</span>
					</td>
					<td>
						<input class="option" type="number" value="<?=number_format($inter['WORKFORCE'],2,".","")?>" name="WORKFORCE" onmouseup="hour()" style="width: 4em;text-align:right">€/Heures
					</td>
					<td style="text-align:right">
						<span>Heure d'Arrivée :</span>
					</td>
					<td class="option">
						<input class="option" type="time" value="<?=$inter['HEURE_ARRIVEE']?>" name="HEURE_ARRIVEE" onmouseup="hour()" step="300">
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						<span>Déplacement :</span>
					</td>
					<td>
						<select name="DEPLACEMENT" class="option" onchange="travelCalc()">
						<?php switch($inter['DEPLACEMENT']):
								case 0:?>
									<option value="0" selected>ZONE1</option>
									<option value="1">ZONE2</option>
									<option value="2">ZONE3</option>
									<option value="4">ZONE4</option>
									<option value="-1">AUCUN</option>
						<?php 		break;
								case 1:?>
									<option value="0">ZONE1</option>
									<option value="1" selected>ZONE2</option>
									<option value="2">ZONE3</option>
									<option value="4">ZONE4</option>
									<option value="-1">AUCUN</option>
						<?php 		break;
								case 2:?>
									<option value="0">ZONE1</option>
									<option value="1">ZONE2</option>
									<option value="2" selected>ZONE3</option>
									<option value="3">ZONE4</option>
									<option value="-1">AUCUN</option>
						<?php		break;
								case 3:?>
									<option value="0">ZONE1</option>
									<option value="1">ZONE2</option>
									<option value="2">ZONE3</option>
									<option value="3" selected>ZONE4</option>
									<option value="-1">AUCUN</option>
						<?php	default:?>
									<option value="0" selected>ZONE1</option>
									<option value="1">ZONE2</option>
									<option value="2">ZONE3</option>
									<option value="3">ZONE4</option>
									<option value="-1" selected>AUCUN</option>
						<?php		break;
								endswitch; ?>
						</select>
					</td>
					<td style="text-align:right">
						<span>Heure de Départ :</span>
					</td>
					<td class="option">
						<input class="option" type="time" value="<?=$inter['HEURE_DEPART']?>" name="HEURE_DEPART" onmouseup="hour()" step="300">
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						<span>Taux TVA :</span>
					</td>
					<td>
						<input style="width:4em;text-align:right" type="number" name="TVA" class="option" onchange="hour()" value="<?=number_format($inter['TVA'],2,".","")?>">%
					</td>
					<td style="text-align:right">
						<span>Temps de Travail :</span>
					</td>
					<td>
						<b><input type="time" id="timeWork" readonly></b>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						<span>Coût de Main d'Oeuvre :</span>
					</td>
					<td>
						<b><input type="text" id="priceWork" style="width:4em;text-align:right" readonly></b>€
					</td>
					<td style="text-align:right">
						<span>TOTAL HT :</span>
					</td>
					<td>
						<b><input type="text" id="totalHour" style="width:4em;text-align:right" readonly></b>€
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						<span>Coût Déplacement :</span>
					</td>
					<td>
						<b><input type="number" id="travel" class="option" style="width:4em;text-align:right" name="TRAVEL_PRICE" onchange="hour()"></b>€
					</td>
					<td style="text-align:right">
						<span>TOTAL TTC :</span>
					</td>
					<td>
						<b><input type="text" id="totalHourTTC" style="width:4em;text-align:right" readonly></b>€
					</td>
				</tr>	
			</table>
			<table id="DESC" style="display:none">				<!-- DESCRIPTION / COMPLEMENTS-->
				<tr>
					<th width="50%" colspan="2">
						DESCRIPTION
					</th>
					<th width="50%" colspan="2">
						COMPLEMENTS
					</th>
				</tr>
				<tr>
					<td colspan="2">
						<div>
							<textarea name="DESCRIPTION" class="option" rows="20"><?=$inter['DESCRIPTION']?></textarea>
						</div>
					</td>
					<td colspan="2">
						<div>
							<textarea name="COMPLEMENTS" class="option" rows="20"><?=$inter['COMPLEMENTS']?></textarea>
						</div>
					</td>
				</tr>
				<tr>
					<th width="50%" colspan="2">
						MATERIEL(S) DE PRET
					</th>
					<th width="50%" colspan="2">
						NUMERO(S) DE SERIE(S)
					</th>
				</tr>
				<tr>
					<td colspan="2">
						<textarea name="MATERIELS" class="option" rows="5" style="text-align:right;"><?=$inter['MATERIELS']?></textarea>
					</td>
					<td colspan="2">
						<textarea name="NUMEROS_SERIES" class="option" rows="5"><?=$inter['NUMEROS_SERIES']?></textarea>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;">
						Test de bon fonctionnement : 
					</td>
					<td>
						<select name="FONCTIONNEMENT" class="option">
							<?php switch($inter['FONCTIONNEMENT']):
								case "1":?>
								<option value="1" selected>Oui</option>
								<option value="0">Non</option>
								<option value="-1">Non Concerné</option>
							<?php break; case "0":?>
								<option value="1">Oui</option>
								<option value="0" selected>Non</option>
								<option value="-1">Non Concerné</option>
							<?php break; default:?>
								<option value="1">Oui</option>
								<option value="0">Non</option>
								<option value="-1" selected>Non Concerné</option>
							<?php break; endswitch;?>
						</select>
					</td>
					<td style="text-align:right;">
						Formation à la sauvegarde : 
					</td>
					<td>
						<select name="FORMATION_SAUVEGARDE" class="option">
							<?php switch($inter['FORMATION_SAUVEGARDE']):
								case "1":?>
								<option value="1" selected>Oui</option>
								<option value="0">Non</option>
								<option value="-1">Non Concerné</option>
							<?php break; case "0":?>
								<option value="1">Oui</option>
								<option value="0" selected>Non</option>
								<option value="-1">Non Concerné</option>
							<?php break; default:?>
								<option value="1">Oui</option>
								<option value="0">Non</option>
								<option value="-1" selected>Non Concerné</option>
							<?php break; endswitch;?>
						</select>
					</td>
				</tr>
				<tr>
					<td style="text-align:right;">
						Mise à jour : 
					</td>
					<td>
						<select name="MISE_A_JOUR" class="option">
							<?php switch($inter['MISE_A_JOUR']):
								case "1":?>
								<option value="1" selected>Oui</option>
								<option value="0">Non</option>
								<option value="-1">Non Concerné</option>
							<?php break; case "0":?>
								<option value="1">Oui</option>
								<option value="0" selected>Non</option>
								<option value="-1">Non Concerné</option>
							<?php break; default:?>
								<option value="1">Oui</option>
								<option value="0">Non</option>
								<option value="-1" selected>Non Concerné</option>
							<?php break; endswitch;?>
						</select>
					</td>
					<td style="text-align:right;">
						Numéro de Version : 
					</td>
					<td>
						<input class="option" type="text" name="VERSION" value="<?=$inter['VERSION']?>" name="VERSION">
					</td>
				</tr>
				<tr>
					<td colspan="3" style="text-align:right;">
						Le matériel est mis à jour conformément à la loi 2018-1317 du 28 décembre 2018 : 
					</td>
					<td>
						<select name="LOI_NF" class="option">
							<?php switch($inter['LOI_NF']):
								case "1":?>
								<option value="1" selected>Oui</option>
								<option value="0">Non</option>
								<option value="-1">Non Concerné</option>
							<?php break; case "0":?>
								<option value="1">Oui</option>
								<option value="0" selected>Non</option>
								<option value="-1">Non Concerné</option>
							<?php break; default:?>
								<option value="1">Oui</option>
								<option value="0">Non</option>
								<option value="-1" selected>Non Concerné</option>
							<?php break; endswitch;?>
						</select>
					</td>
				</tr>
			</table>
			<table id="SIGNS" style="display:none">				<!-- INFOS SIGNATAIRE / TECHNICIEN-->
				<tr>
					<th colspan="4">
						INFORMATIONS SIGNATAIRES
					</th>
				</tr>
				<tr>
					<td style="text-align:right">
						<span>Technicien :</span>
					</td>
					<td>
						<select name="TECHNICIEN" class="option">
							<?php while($tech = $techs->fetch()):?>
								<option value="<?=$tech['ID']?>"><?=$tech['NAME']?></option>
							<?php endwhile;?>
						</select>
					</td>
					<td style="text-align:right">
						<span>Signataire :</span>
					</td>
					<td>
						<input name="SIGNATAIRE" type="text" class="option" value="<?=$inter['SIGNATAIRE']?>"><input name="QUALITE_SIGNATAIRE" type="text" class="option" value="<?=$inter['QUALITE_SIGNATAIRE']?>">
					</td>
				</tr>
			</table>				
			<table id="FACTU" style="display:none">				<!-- FACTURATION TIERCE-->
				<tr>
					<th colspan="4">
						FACTURATION TIERCE
					</th>
				</tr>
				<tr>
					<td style="text-align:left">
						<input type="search" id="srch" onchange="searchingProduct()" placeholder="Rechercher...">
					</td>
					<td>
						Prix HT
					</td>
					<td>
						Remise
					</td>
					<td>
						Total HT
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						<select style="width:100%" name="FACTU1" class="option FACTU" onmouseup="factu()">
							<option value=""></option>
							<?php while($produit = $produits->fetch()): ?>
								<option style="" value="<?=$produit['ID']?>"<?php if($inter['FACTU1'] == $produit['LIBELLE'] || $inter['FACTU1'] == $produit['ID']){echo "selected";}?>><?=$produit['LIBELLE']?></option>
							<?php endwhile; ?>
						</select>
					</td>
					<td>
						<input id="PRIX1" style="width:4em" readonly>€
						<select id="productPrice" class="option" hidden>
							<?php $produits = $bdd->query("SELECT	* FROM PRODUITS") or die(print_r($req->errorInfo()));
								while($produit = $produits->fetch()): ?>
								<option value="<?=$produit['ID']?>"<?php if($inter['FACTU1'] == $produit['LIBELLE'] || $inter['FACTU1'] == $produit['ID']){echo "selected";}?>><?=$produit['PVHT']?></option>
							<?php endwhile; ?>
						</select>
					</td>
					<td>
						<select name="REMISE1" onchange="factu()">
							<?php switch($inter['REMISE1']):
								case "10":?>
								<option value="10" selected>10%</option>
								<option value="20">20%</option>
								<option value="30">30%</option>
								<option value="0">0</option>
							<?php break; case "20":?>
								<option value="10">10%</option>
								<option value="20" selected>20%</option>
								<option value="30">30%</option>
								<option value="0">0</option>
							<?php break; case "30":?>
								<option value="10">10%</option>
								<option value="20">20%</option>
								<option value="30" selected>30%</option>
								<option value="0">0</option>
							<?php break; default:?>
								<option value="10">10%</option>
								<option value="20">20%</option>
								<option value="30">30%</option>
								<option value="0" selected>0</option>
							<?php break; endswitch;?>
						</select>
					</td>
					<td>
						<input id="ftot1" name="FACTU_PRIX1" readonly style="width:4em">€
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						<select style="width:100%" name="FACTU2" class="option FACTU" onmouseup="factu()">
							<option value=""></option>
							<?php 	$produits = $bdd->query("SELECT	* FROM PRODUITS") or die(print_r($req->errorInfo()));
								while($produit = $produits->fetch()): ?>
								<option style="" value="<?=$produit['ID']?>"<?php if($inter['FACTU2'] == $produit['LIBELLE'] || $inter['FACTU2'] == $produit['ID']){echo "selected";}?>><?=$produit['LIBELLE']?></option>
							<?php endwhile; ?>
						</select>
					</td>
					<td>
						<input id="PRIX2" style="width:4em" readonly>€
					</td>
					<td>
						<select name="REMISE2" onchange="factu()">
							<?php switch($inter['REMISE2']):
								case "10":?>
								<option value="10" selected>10%</option>
								<option value="20">20%</option>
								<option value="30">30%</option>
								<option value="0">0</option>
							<?php break; case "20":?>
								<option value="10">10%</option>
								<option value="20" selected>20%</option>
								<option value="30">30%</option>
								<option value="0">0</option>
							<?php break; case "30":?>
								<option value="10">10%</option>
								<option value="20">20%</option>
								<option value="30" selected>30%</option>
								<option value="0">0</option>
							<?php break; default:?>
								<option value="10">10%</option>
								<option value="20">20%</option>
								<option value="30">30%</option>
								<option value="0" selected>0</option>
							<?php break; endswitch;?>
						</select>
					</td>
					<td>
						<input id="ftot2" name="FACTU_PRIX2" readonly style="width:4em">€
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						<select style="width:100%" name="FACTU3" class="option FACTU" onmouseup="factu()">
							<option value=""></option>
							<?php $produits = $bdd->query("SELECT	* FROM PRODUITS") or die(print_r($req->errorInfo()));
								while($produit = $produits->fetch()): ?>
								<option style="" value="<?=$produit['ID']?>"<?php if($inter['FACTU3'] == $produit['LIBELLE'] || $inter['FACTU3'] == $produit['ID']){echo "selected";}?>><?=$produit['LIBELLE']?></option>
							<?php endwhile; ?>
						</select>
					</td>
					<td>
						<input id="PRIX3" style="width:4em" readonly>€
					</td>
					<td>
						<select name="REMISE3" onchange="factu()">
							<?php switch($inter['REMISE3']):
								case "10":?>
								<option value="10" selected>10%</option>
								<option value="20">20%</option>
								<option value="30">30%</option>
								<option value="0">0</option>
							<?php break; case "20":?>
								<option value="10">10%</option>
								<option value="20" selected>20%</option>
								<option value="30">30%</option>
								<option value="0">0</option>
							<?php break; case "30":?>
								<option value="10">10%</option>
								<option value="20">20%</option>
								<option value="30" selected>30%</option>
								<option value="0">0</option>
							<?php break; default:?>
								<option value="10">10%</option>
								<option value="20">20%</option>
								<option value="30">30%</option>
								<option value="0" selected>0</option>
							<?php break; endswitch;?>
						</select>
					</td>
					<td>
						<input id="ftot3" name="FACTU_PRIX3" readonly style="width:4em">€
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						<select style="width:100%" name="FACTU4" class="option FACTU" onmouseup="factu()">
							<option value=""></option>
							<?php $produits = $bdd->query("SELECT	* FROM PRODUITS") or die(print_r($req->errorInfo()));
								while($produit = $produits->fetch()): ?>
								<option style="" value="<?=$produit['ID']?>"<?php if($inter['FACTU4'] == $produit['LIBELLE'] || $inter['FACTU4'] == $produit['ID']){echo "selected";}?>><?=$produit['LIBELLE']?></option>
							<?php endwhile; ?>
						</select>
					</td>
					<td>
						<input id="PRIX4" style="width:4em" readonly>€
					</td>
					<td>
						<select name="REMISE4" onchange="factu()">
							<?php switch($inter['REMISE4']):
								case "10":?>
								<option value="10" selected>10%</option>
								<option value="20">20%</option>
								<option value="30">30%</option>
								<option value="0">0</option>
							<?php break; case "20":?>
								<option value="10">10%</option>
								<option value="20" selected>20%</option>
								<option value="30">30%</option>
								<option value="0">0</option>
							<?php break; case "30":?>
								<option value="10">10%</option>
								<option value="20">20%</option>
								<option value="30" selected>30%</option>
								<option value="0">0</option>
							<?php break; default:?>
								<option value="10">10%</option>
								<option value="20">20%</option>
								<option value="30">30%</option>
								<option value="0" selected>0</option>
							<?php break; endswitch;?>
						</select>
					</td>
					<td>
						<input id="ftot4" name="FACTU_PRIX4" readonly style="width:4em">€
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						<select style="width:100%" name="FACTU5" class="option FACTU" onmouseup="factu()">
							<option value=""></option>
							<?php $produits = $bdd->query("SELECT	* FROM PRODUITS") or die(print_r($req->errorInfo()));
								while($produit = $produits->fetch()): ?>
								<option style="" value="<?=$produit['ID']?>"<?php if($inter['FACTU5'] == $produit['LIBELLE'] || $inter['FACTU5'] == $produit['ID']){echo "selected";}?>><?=$produit['LIBELLE']?></option>
							<?php endwhile; ?>
						</select>
					</td>
					<td>
						<input id="PRIX5" style="width:4em" readonly>€
					</td>
					<td>
						<select name="REMISE5" onchange="factu()">
							<?php switch($inter['REMISE5']):
								case "10":?>
								<option value="10" selected>10%</option>
								<option value="20">20%</option>
								<option value="30">30%</option>
								<option value="0">0</option>
							<?php break; case "20":?>
								<option value="10">10%</option>
								<option value="20" selected>20%</option>
								<option value="30">30%</option>
								<option value="0">0</option>
							<?php break; case "30":?>
								<option value="10">10%</option>
								<option value="20">20%</option>
								<option value="30" selected>30%</option>
								<option value="0">0</option>
							<?php break; default:?>
								<option value="10">10%</option>
								<option value="20">20%</option>
								<option value="30">30%</option>
								<option value="0" selected>0</option>
							<?php break; endswitch;?>
						</select>
					</td>
					<td>
						<input id="ftot5" name="FACTU_PRIX5" readonly style="width:4em">€
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						<select style="width:100%" name="FACTU6" class="option FACTU" onmouseup="factu()">
							<option value=""></option>
							<?php $produits = $bdd->query("SELECT	* FROM PRODUITS") or die(print_r($req->errorInfo()));
								while($produit = $produits->fetch()): ?>
								<option style="" value="<?=$produit['ID']?>"<?php if($inter['FACTU6'] == $produit['LIBELLE'] || $inter['FACTU6'] == $produit['ID']){echo "selected";}?>><?=$produit['LIBELLE']?></option>
							<?php endwhile; ?>
						</select>
					</td>
					<td>
						<input id="PRIX6" style="width:4em" readonly>€
					</td>
					<td>
						<select name="REMISE6" onchange="factu()">
							<?php switch($inter['REMISE6']):
								case "10":?>
								<option value="10" selected>10%</option>
								<option value="20">20%</option>
								<option value="30">30%</option>
								<option value="0">0</option>
							<?php break; case "20":?>
								<option value="10">10%</option>
								<option value="20" selected>20%</option>
								<option value="30">30%</option>
								<option value="0">0</option>
							<?php break; case "30":?>
								<option value="10">10%</option>
								<option value="20">20%</option>
								<option value="30" selected>30%</option>
								<option value="0">0</option>
							<?php break; default:?>
								<option value="10">10%</option>
								<option value="20">20%</option>
								<option value="30">30%</option>
								<option value="0" selected>0</option>
							<?php break; endswitch;?>
						</select>
					</td>
					<td>
						<input id="ftot6" name="FACTU_PRIX6" readonly style="width:4em">€
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						<span>TOTAL :</span>
					</td>
					<td style="border-top:solid 1px black">
						<input id="factuTotHT" style="width:4em" readonly>€
					</td>
					<td style="border-top:solid 1px black">
						<input id="factuTotRemise" style="width:4em" readonly>%
					</td>
					<td style="border-top:solid 1px black">
						<input id="factuTotTTC" style="width:4em" readonly>€
					</td>
				</tr>
			</table>
			
			<input type="hidden" name="BILLING">
			
			<div style="text-align:right;transform:translateY(-1px)">
				<div type="button"  class="tabs" style="background:#b6ff92; margin-left:15px;" onclick="mailing();">
					VALIDER
				</div>
				<div type="button"  class="tabs" style="padding: 15px 0 15px 0;background:#ff9292;" onclick="">
					<a style="padding:15px;text-decoration: none;color: black;" href="/INTER/ADMIN/widgets/view_inter.php?inter=<?=$inter['ID']?>" target="_self">ANNULER</a>
				</div>
			</div>
			
			<script>				
				function billing(){
					var contrat = document.getElementsByName('CONTRAT')[0].value;
					var totalHour = document.getElementById('totalHour').value;
					var f = document.getElementById('factuTotTTC').value;
					var billing = document.getElementsByName("BILLING")[0];
					
					if(contrat == "1"){
						billing.value = Math.round(parseFloat(f)*100)/100;
					}
					else{
						billing.value = Math.round((parseFloat(totalHour) + parseFloat(f))*100)/100;
					}
				}
				
				var travelVal;
				travelCalc();
				function travelCalc(){
					var travel = parseInt(document.getElementsByName('DEPLACEMENT')[0].value);
					switch(travel){
						case 0:
							var travelVal = <?=$tarif[0]['ZONE1']?>;
							document.getElementById('travel').value = <?=number_format(floatval($tarif[0]['ZONE1']),2,","," ")?>;
							break;
						case 1:
							console.log(travel);
							var travelVal = <?=$tarif[0]['ZONE2']?>;
							document.getElementById('travel').value = <?=number_format(floatval($tarif[0]['ZONE2']),2,","," ")?>;
							break;
						case 2:
							var travelVal = <?=$tarif[0]['ZONE3']?>;
							document.getElementById('travel').value = <?=number_format(floatval($tarif[0]['ZONE3']),2,","," ")?>;
							break;
						case 3:
							var travelVal = <?=$tarif[0]['ZONE4']?>;
							document.getElementById('travel').value = <?=number_format(floatval($tarif[0]['ZONE4']),2,","," ")?>;
							break;
						default:
							var travelVal = 0;
							document.getElementById('travel').value = 0;
							break;
					}
					hour();
				}
				
				<!-- CALCUL COUTS -->
				document.getElementById('travel').value = <?=number_format(floatval($inter['TRAVEL_PRICE']),2,","," ")?>;
				function hour(){
					var contrat = document.getElementsByName('CONTRAT')[0].value;
					var start = document.getElementsByName('HEURE_ARRIVEE')[0].value;
					var end = document.getElementsByName('HEURE_DEPART')[0].value;
					var workforce = document.getElementsByName('WORKFORCE')[0].value;
					var tva = document.getElementsByName('TVA')[0].value;
					var travelVal = parseFloat(document.getElementById('travel').value);
					
					start = start.split(":");
					end = end.split(":");
					
					var startMin = (start[0]*60) + parseInt(start[1]);
					var endMin = (end[0]*60) + parseInt(end[1]);
					
					var totalMin = endMin - startMin;
					
					var coef = Math.round((totalMin/60)*100)/100;
					
					if(Math.trunc(totalMin/60) < 10){ //HEURES
						var total = ("0" + Math.trunc(totalMin/60)) + ":";
					}
					else{
						var total = (Math.trunc(totalMin/60)) + ":";
					}
					
					if((totalMin- (Math.trunc(totalMin/60)*60)) < 10){ //MINUTES
						total = total + "0" + (totalMin-(Math.trunc(totalMin/60)*60));
					}
					else{
						total = total + (totalMin-(Math.trunc(totalMin/60)*60));
					}
					
					document.getElementById('timeWork').value = total;
					
					var priceWork = Math.round((coef*workforce)*100)/100;
					
					document.getElementById('priceWork').value = (priceWork).toFixed(2);
					
					document.getElementById('totalHour').value = (priceWork + travelVal).toFixed(2);
					
					billing();
					
					if(contrat == "0"){
						var totalHour = parseFloat(document.getElementById('totalHour').value);
						var tva = parseFloat(document.getElementsByName('TVA')[0].value);
						
						var totalHourTTC = Math.round((totalHour + (totalHour/100)*tva)*100)/100;
						
						document.getElementById('totalHourTTC').value = (totalHourTTC).toFixed(2);;
					}
					else{
						document.getElementById('totalHourTTC').value = "0.00"
					}
				}
				
				<!-- CALCULS SIGNATAIRES-->
				tech()
				function tech(){
					var tech = <?=$inter['TECHNICIEN']?>;
					var output = document.getElementsByName('TECHNICIEN')[0];
					
					output.value = tech;
				}
				
				<!-- CALCUL DE FACTURATIONS TIERCES-->
				factu();
				function factu(){
					hour();
					var listPrix = document.getElementById('productPrice');
					
					var factu1 = document.getElementsByName('FACTU1')[0].value;
					var factu2 = document.getElementsByName('FACTU2')[0].value;
					var factu3 = document.getElementsByName('FACTU3')[0].value;
					var factu4 = document.getElementsByName('FACTU4')[0].value;
					var factu5 = document.getElementsByName('FACTU5')[0].value;
					var factu6 = document.getElementsByName('FACTU6')[0].value;
					
					var prix1 = document.getElementById('PRIX1');
					var prix2 = document.getElementById('PRIX2');
					var prix3 = document.getElementById('PRIX3');
					var prix4 = document.getElementById('PRIX4');
					var prix5 = document.getElementById('PRIX5');
					var prix6 = document.getElementById('PRIX6');
					
					var remise1 = document.getElementsByName('REMISE1')[0];
					var remise2 = document.getElementsByName('REMISE2')[0];
					var remise3 = document.getElementsByName('REMISE3')[0];
					var remise4 = document.getElementsByName('REMISE4')[0];
					var remise5 = document.getElementsByName('REMISE5')[0];
					var remise6 = document.getElementsByName('REMISE6')[0];
					
					var ftot1 = document.getElementById('ftot1');
					var ftot2 = document.getElementById('ftot2');
					var ftot3 = document.getElementById('ftot3');
					var ftot4 = document.getElementById('ftot4');
					var ftot5 = document.getElementById('ftot5');
					var ftot6 = document.getElementById('ftot6');
					
					var factuTotHT = document.getElementById('factuTotHT');
					var factuTotTTC = document.getElementById('factuTotTTC');
					var factuTotRemise = document.getElementById('factuTotRemise');
					
					listPrix.value = factu1;
					prix1.value = parseFloat(listPrix.options[listPrix.selectedIndex].text).toFixed(2);
					
					listPrix.value = factu2;
					prix2.value = parseFloat(listPrix.options[listPrix.selectedIndex].text).toFixed(2);
					
					listPrix.value = factu3;
					prix3.value = parseFloat(listPrix.options[listPrix.selectedIndex].text).toFixed(2);
					
					listPrix.value = factu4;
					prix4.value = parseFloat(listPrix.options[listPrix.selectedIndex].text).toFixed(2);
					
					listPrix.value = factu5;
					prix5.value = parseFloat(listPrix.options[listPrix.selectedIndex].text).toFixed(2);
					
					listPrix.value = factu6;
					prix6.value = parseFloat(listPrix.options[listPrix.selectedIndex].text).toFixed(2);
					
					factuTotHT.value = parseFloat(prix6.value) + parseFloat(prix5.value) + parseFloat(prix4.value) + parseFloat(prix3.value) + parseFloat(prix2.value) + parseFloat(prix1.value);
					
					factuTotHT.value = parseFloat(factuTotHT.value).toFixed(2);
					
					billing();
					
					ftot1.value = (Math.round((parseFloat(prix1.value) - (parseFloat(prix1.value)/100 * parseFloat(remise1.value)))*100)/100).toFixed(2);
					ftot2.value = (Math.round((parseFloat(prix2.value) - (parseFloat(prix2.value)/100 * parseFloat(remise2.value)))*100)/100).toFixed(2);
					ftot3.value = (Math.round((parseFloat(prix3.value) - (parseFloat(prix3.value)/100 * parseFloat(remise3.value)))*100)/100).toFixed(2);
					ftot4.value = (Math.round((parseFloat(prix4.value) - (parseFloat(prix4.value)/100 * parseFloat(remise4.value)))*100)/100).toFixed(2);
					ftot5.value = (Math.round((parseFloat(prix5.value) - (parseFloat(prix5.value)/100 * parseFloat(remise5.value)))*100)/100).toFixed(2);
					ftot6.value = (Math.round((parseFloat(prix6.value) - (parseFloat(prix6.value)/100 * parseFloat(remise6.value)))*100)/100).toFixed(2);
					
					factuTotTTC.value = parseFloat(ftot1.value) + parseFloat(ftot2.value) + parseFloat(ftot3.value) + parseFloat(ftot4.value) + parseFloat(ftot5.value) + parseFloat(ftot6.value);
					
					factuTotTTC.value = parseFloat(factuTotTTC.value).toFixed(2);
					
					factuTotRemise.value = (((parseFloat(factuTotHT.value) - parseFloat(factuTotTTC.value))*100)/parseFloat(factuTotHT.value)).toFixed(2);
				}
				
				function searchingProduct(){
					var str = document.getElementById('srch').value.toUpperCase();
					var sel = document.getElementsByClassName('FACTU');
					
					for(var x = 0; x < sel.length; x++){
						var opt = sel[x].getElementsByTagName('OPTION');
						
						for(var y = 0; y < opt.length; y++){
							if((opt[y].innerText.toUpperCase()).search(str) > -1){
								opt[y].style.display = "";
							}
							else{
								opt[y].style.display = "none";
							}
						}
					}
				}
				
				billing();
			</script>
			<input type="hidden" name="SENDMAIL">
		</form>
		
		<span id="confirm" style="display:none">
			
			<div id="fog"></div>
				
			<div id="mailit">
				<table>
					<tr>
						<td colspan="3">
							Renvoyer par mail le bon d'intervention au client ?
						</td>
					<tr>
					<tr class="split">
						<td>
						</td>
					</tr>
					<tr id="choice">
						<td onclick="submitUpdates(true, true)">
							OUI
						</td>
						<td onclick="submitUpdates(false, true)">
							NON
						</td>
					</tr>
					<tr class="split">
						<td>
						</td>
					</tr>
					<tr id="cancel">
						<td colspan="2" onclick="submitUpdates(false, false)">
							ANNULER
						</td>
					</tr>
				</table>
			</div>
		</span>
	</body>
	
	<footer>
	</footer>
</html>

<script>
	tab("INFOS");
	function tab(opt){
		var btnopt = "btn" + opt;
		
		var CLIENT = document.getElementById('CLIENT');
		var INFOS = document.getElementById('INFOS');
		var DESC = document.getElementById('DESC');
		var SIGNS = document.getElementById('SIGNS');
		var FACTU = document.getElementById('FACTU');
		var COUTS = document.getElementById('COUTS');
		
		var btnCLIENT = document.getElementById('btnCLIENT');
		var btnCOUTS = document.getElementById('btnCOUTS');
		var btnINFOS = document.getElementById('btnINFOS');
		var btnDESC = document.getElementById('btnDESC');
		var btnSIGNS = document.getElementById('btnSIGNS');
		var btnFACTU = document.getElementById('btnFACTU');
		
		CLIENT.style.display = "none";
		btnCLIENT.style.backgroundColor = "";
		btnCLIENT.style.fontWeight = "";
		
		INFOS.style.display = "none";
		btnINFOS.style.backgroundColor = "";
		btnINFOS.style.fontWeight = "";
		
		DESC.style.display = "none";
		btnDESC.style.backgroundColor = "";
		btnDESC.style.fontWeight = "";
		
		SIGNS.style.display = "none";
		btnSIGNS.style.backgroundColor = "";
		btnSIGNS.style.fontWeight = "";
		
		FACTU.style.display = "none";
		btnFACTU.style.backgroundColor = "";
		btnFACTU.style.fontWeight = "";
		
		COUTS.style.display = "none";
		btnCOUTS.style.backgroundColor = "";
		btnCOUTS.style.fontWeight = "";
		
		var opt = document.getElementById(opt);
		var btnopt = document.getElementById(btnopt);
		
		btnopt.style.backgroundColor = "white";
		btnopt.style.fontWeight = "bold";
		opt.style.display = "";
	}
	
	SEARCH();
	function SEARCH() {
		var input, filter, select, option, a, i, txtValue, found;
		
		input = document.getElementById('searching');
		filter = input.value.toUpperCase();
		
		select = document.getElementById("clients");
		option = select.getElementsByClassName('CLIENT');
		search = select.getElementsByClassName('name');
		
		code = select.getElementsByClassName('code');
		telephone = select.getElementsByClassName('telephone');
		mail = select.getElementsByClassName('mail');
			
		for (i = 0; i < option.length; i++) {
			switch(input.value){
				case "*":
					option[i].style.display = "";
				break;
				case "":
					option[i].style.display = "none";
				break;
				default:
					a = search[i];
					txtValue = a.textContent + code[i].textContent.toUpperCase() + telephone[i].textContent.toUpperCase() + mail[i].textContent.toUpperCase();
					if (txtValue.toUpperCase().indexOf(filter) > -1) {
						option[i].style.display = "";
					} else {
						option[i].style.display = "none";
					}
				break;
			}
		}
	}
	
	function SELECTED(code){
		document.getElementById('searching').value = code;
		document.getElementsByName("CODE_CLIENT")[0].value = code;
		
		var CLIENT = document.getElementsByName("CLIENT")[0];
		var TELEPHONE = document.getElementsByName("TELEPHONE")[0];
		var ADRESSE = document.getElementsByName("ADRESSE")[0];
		var EMAIL = document.getElementsByName("EMAIL")[0];
		
		var select = document.getElementById(code);
		var content = select.getElementsByTagName('td');
		
		CLIENT.value =		content[1].innerText ? content[1].innerText : CLIENT.value;
		TELEPHONE.value =	content[2].innerText ? content[2].innerText : TELEPHONE.value;
		EMAIL.value =		content[3].innerText ? content[3].innerText : EMAIL.value;
		ADRESSE.value =		content[7].innerText ? content[7].innerText : ADRESSE.value;
		
		SEARCH();
	}
	
	var inputSearch = document.getElementById('searching');
	
	inputSearch.addEventListener("keydown", function(e) {
		// space and arrow keys
		if([13].indexOf(e.keyCode) > -1) {
			e.preventDefault();
		}
	}, false);
	
	inputSearch.onkeydown = checkKey;
	
	function checkKey(e){

		e = e || inputSearch.event;

		if(e.keyCode == '13')
		{
			SEARCH();
		}
	}
	
	function mailing(){
		var confirm = document.getElementById('confirm');
		
		confirm.style.display = "";
	}
	
	function submitUpdates(mail, choice){
		var confirm = document.getElementById('confirm');
		var sendMail = document.getElementsByName('SENDMAIL')[0];
		
		if(choice){
			switch(mail){
				case true:
					sendMail.value = true;
					document.forms[0].submit();
					break;
				case false:
					sendMail.value = false;
					document.forms[0].submit();
					//window.parent.location.href = '../widgets/list_inter.php?inter=<?=$_GET['inter']?>';
					break;
			}
		}
		else{
			confirm.style.display = "none";
		}
	}
	
</script>

<style>
	html{
		background:			#adadad;
	}
	
	form{
		width:				60%;
		margin:				0 auto;
	}
	
	.option{
		color:				blue;
	}
	
	th{
		text-align:			center;
		padding:			15px 0 15px 0;
	}
	
	td:not(.CLIENT td){
		width:				25%;
		padding:			7.5px 0 7.5px 15px;
	}
	
	table{
		box-shadow: 1px 1px 20px -10px black;
		margin:				0 auto;
		width:				100%;
		padding:			10px;
		margin-bottom:		1em;
		background:			white;
	}
	
	#FACTU td{
		width:				calc(1040px / 4);
		text-align:			center;
	}
	
	#DESC textarea{
		width:				calc(100% - 40px);
		margin:				0 20px 20px 20px;
	}
	
	#tabContent{
		width:				100%;
		margin:				3% auto 15px auto;
		transform:			translateY(1px);
	}
	
	.tabs{
		margin-left:		1px;
		padding:			15px;
		display:			inline;
		cursor:				pointer;
		user-select:		none;
		background:			grey;
	}
	
	button[type="submit"]{
		position:			absolute;
		bottom:				15%;
		right:				50%;
		transform:			translateX(50%);
	}
	
	#UPDATE{
		width:				100%;
	}
	
	#client_head{
		font-weight:		bold;
		text-align:			center;
	}
	
	#client_head td{
		border-bottom:				solid 2px black;
	}
	
	.CLIENT{
		background:			lightgrey;
		cursor:				pointer;
		text-align:			center;
	}
	
	.CLIENT:hover{
		background:			grey;
	}
	
	.CLIENT td{
		border-bottom:		solid grey 1px;
		height:				25px;
		width:				calc(100% / 7);
	}
	
	.CLIENT .address{
		display:			none;
	}
	
	.infosClient{
		width:				100%;
	}
	
	#mailit{
		margin:				0 auto;
		width:				500px;
		height:				auto;
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%,-50%);
	}
	
	#mailit table{
		text-align:			center;
	}
	
	#mailit td{
		font-size:			20px;
		width:				50%;
		height:				50px;
	}
	
	#mailit #choice td{
		cursor:				pointer;
		background:			#ff9292;
	}
	
	#mailit #choice td:first-child{
		background:			#b6ff92;
	}
	
	#mailit .split td{
		height:				20px;
	}
	
	#mailit #cancel{
		cursor:				pointer;
		text-shadow:		0 0 1px black;
		color:				#ff9292;
	}
	
	#mailit #cancel:hover{
		text-decoration:	underline;
	}
		
	#fog{
		position:			absolute;
		top:				0;
		left:				0;
		width:				100vw;
		height:				100vh;
		background:			rgba(0,0,0,0.9);
	}
</style>

<?php
	function update($id){
		
		//UTILISE POUR LES LOGS.
		
		$req = $GLOBALS['bdd']->prepare("SELECT * FROM INTERVENTIONS WHERE ID = :id");
		$req->execute(array(
			'id'	=>	$id
		)) or die(error($x, $req, $id));
		
		$inter = $req->fetchAll(2);
		
		//UTILISE POUR LES LOGS.
		
		for($x = 0; $x < sizeOf($_POST); $x++){
			
			if(key($_POST) != "SENDMAIL"){
				$req = $GLOBALS['bdd']->prepare("UPDATE INTERVENTIONS SET ".key($_POST)." = :val WHERE ID = :id");
				$req->execute(array(
					'val'	=>	current($_POST),
					'id'	=>	$id
				)) or die(error($x, $req, $id));
				
				next($_POST);
			}
			else{
				next($_POST);
			}
		}
		
		$req = $GLOBALS['bdd']->prepare("UPDATE INTERVENTIONS SET UPDATED_BY = :uid WHERE ID = :id");
		$req->execute(array(
			'uid'		=>	$_SESSION['id'],
			'id'		=>	$id
		)) or die(error(0, $req, $id));
		
		// echo "<pre>";
			// print_r($_POST);
		// echo "</pre>";
	}
	
	function error($x, $req, $id){
		echo "<pre>";
		echo "Une erreur s'est produite lors de l'actualisation de la base de donnée.";
		echo "<br>Champs modifié(s) : ".$x."/".sizeOf($_POST);
		echo "<br>Champs erroné : ".key($_POST)." => ".current($_POST);
		echo "<br><br>Modification(s) apporté(s) à l'intervention N°".$id." :";
		prev($_POST);
		for($x = $x; $x != 0; $x--){
			echo "<br>	[".key($_POST)."]		=>		".current($_POST);
			prev($_POST);
		}
		echo "<br><br>Infos Utilisateur : ";
		echo "<br>	UID :		".$_SESSION['id'];
		echo "<br>	NAME :		".$_SESSION['name'];
		echo "<br>	RIGHTS :	".$_SESSION['rights'];
		echo "<br><br>Code d'erreur :<br>";
		print_r($req->errorInfo());
		echo "<br><br>Veuillez contacter un administrateur pour plus d'informations.";
		echo "</pre>";
		die();
	}
?>