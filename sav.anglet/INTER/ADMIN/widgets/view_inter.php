<?php
	include('../inc/config.inc.php');
	include('../inc/accounts.inc.php');

	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}

	//=====Récupération des information établissement.
	$req = $bdd->query("SELECT * FROM ETABLISSEMENT");
	$etablissement = $req->fetch();
	//==========

	//=====Récupération des informations de l'Intervention.
	$req = $bdd->prepare("SELECT * FROM INTERVENTIONS WHERE ID = :id");
	$req->execute(array
		(
			'id' => $_POST['ID'] ? $_POST['ID'] : $_GET['inter']
		)
	);
	$inter = $req->fetch();
	//==========

	//=====Récupération des informations de l'utilisateur.
	$req = $bdd->prepare("SELECT * FROM USERS_INTER WHERE ID = :id");
	$req->execute(array
		(
			'id' => $inter['TECHNICIEN']
		)
	);
	$user = $req->fetch();
	//=========

	//=====Récupération des informations de tarifications.
	$req = $bdd->query("SELECT * FROM TARIFS");
	$tarif = $req->fetchAll(2);
	//=========

	//=====Traitement des produits à facturer.
	$req = $bdd->prepare("
	SELECT
		ID,LIBELLE
	FROM
		PRODUITS
	WHERE
		(ID = :factu1)
		OR
		(ID = :factu2)
		OR
		(ID = :factu3)
		OR
		(ID = :factu4)
		OR
		(ID = :factu5)
		OR
		(ID = :factu6)
		");
	$req->execute(array
		(
			'factu1' => $inter['FACTU1'],
			'factu2' => $inter['FACTU2'],
			'factu3' => $inter['FACTU3'],
			'factu4' => $inter['FACTU4'],
			'factu5' => $inter['FACTU5'],
			'factu6' => $inter['FACTU6']
		)
	);
	$factuName = $req->fetchAll();
	//=========

	ob_start();
?>

<style>

	<?php
		if($_GET['pdf'] != 1){
			echo ".hightlight{color:blue;font-weight:bold;}";
		}
	?>

	:root{
		--td-height:	24px;
	}

	body{
		margin:				0;
		background:			white;
	}

	table{
		margin:				10vw auto;
		width:				80vw;
		border-spacing:		0;
	}

	td{
		width:				25%;
		height:				12px;
		margin:				0;
	}

	.bleft{
		border-left:		black 0.5mm solid;
	}

	.bright{
		border-right:		black 0.5mm solid;
	}

	.bbottom{
		border-bottom:		black 0.5mm solid;
	}

	.btop{
		border-top:			black 0.5mm solid;
	}

	.acenter{
		text-align:			center;
	}

	.aright{
		text-align:			right;
	}

	.aleft{
		text-align:			left;
	}

	.vtop{
		vertical-align:		top;
	}

	.vmiddle{
		vertical-align:		middle;
	}

	.vbottom{
		vertical-align:		bottom;
	}
</style>

<?php
	if($_GET['pdf'] != 1)
	{
		include('../inc/head.inc.php');
	}
?>

<table cellspacing="0">
	<tbody>
		<tr>
			<td rowspan="6" class="acenter" style="height:calc(24px*6)">
				<img src="../conf/img/logo_etablishment.png" alt="logo_tacteo">
			</td>
			<td rowspan="6">
				<b>SARL TACTEO SE<br>
				45 - 47 RUE EMILE COMBES<br>
				33270 FLOIRAC<br>
				36 RUE ARNAUD DETROYAT<br>
				ZA LE FORUM, 64100 BAYONNE<br>
				Tél 05 56 94 26 96</b>
			</td>
			<td>
			</td>
			<td>
			</td>
		</tr>
		<tr>
			<td class="aright">
				<b><span>Agence de :</span></b>
			</td>
			<td class="hightlight">
				<?php
					if($inter['AGENCE'])
					{
						echo("BAYONNE");
					}
					else
					{
						echo("FLOIRAC");
					}
				?>
			</td>
		</tr>
		<tr>
			<td class="aright">
				<b>Date d'intervention :</b>
			</td>
			<td class="highlight">
				<?php
					list($date, $hour) = explode(" ", $inter['DATE']);
					$date = explode("-", $date);
					echo $date[2]."/".$date[1]."/".$date[0]." à ".$hour;

				?>
			</td>
		</tr>
		<tr>
			<td class="aright">
				<b>BON D'INTERVENTION N°</b>
			</td>
			<td class="highlight">
				<?php echo($inter['ID']);?>
			</td>
		</tr>
		<tr>
			<td class="aright">
				<b><span >CODE CLIENT :</span></b>
			</td>
			<td class="highlight">
				<?php echo($inter['CODE_CLIENT']);?>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td>
			</td>
		</tr>
		<tr>
			<td class="bleft btop">
				<b><span >CLIENT :</span></b>
			</td>
			<td class="bleft btop aright">
				<b><span >COORDONNES :</span></b>
			</td>
			<td class="bright btop">
			</td>
			<td class="bright btop">
				<b><span >OBJET DE L'INTERVENTION :</span></b>
			</td>
		</tr>
		<tr>
			<td rowspan="2" class="bleft hightlight">
				<?php echo($inter['CLIENT']);?>
			</td>
			<td class="hightlight bleft bbottom aright">
				<?php echo($inter['TELEPHONE']." ");?>
			</td>
			<td class="hightlight bright bbottom">
				<?php echo($inter['EMAIL']);?>
			</td>
			<td rowspan="5" class="hightlight bright bbottom aright">
				<?php
					if($inter['INSTALLATION'])
					{
						echo("INSTALLATION<br>");
					}
					if($inter['MAINTENANCE'])
					{
						echo("MAINTENANCE<br>");
					}
					if($inter['FORMATION'])
					{
						echo("FORMATION<br>");
					}
					if($inter['RENOUVELLEMENT'])
					{
						echo("RENOUVELLEMENT<br>");
					}
					if($inter['RECUPERATION'])
					{
						echo("RECUPERATION MATERIELS");
					}
				?>
			</td>
		</tr>
		<tr>
			<td class="bleft bbottom aright">
				<b><span >HEURE D'ARRIVEE :</span></b>
			</td>
			<td class="hightlight bleft bbottom bright">
				<?php
					$a = explode(':', $inter['HEURE_ARRIVEE']);

					echo("&nbsp;" . $a[0] . ":" . $a[1]);
				?>
			</td>
		</tr>
		<tr>
			<td rowspan="3" class="hightlight bleft bbottom">
				<?php echo($inter['ADRESSE']);?>
			</td>
			<td class="bleft bbottom aright">
				<b><span >HEURE DE DEPART :</span></b>
			</td>
			<td class="hightlight bleft bbottom bright">
				<?php
					$d = explode(':', $inter['HEURE_DEPART']);

					echo("&nbsp;" . $d[0] . ":" . $d[1]);
				?>
			</td>
		</tr>
		<tr>
			<td class="bleft bbottom aright">
				<b>TOTAL D'HEURES :</b>
			</td>
			<td class="hightlight bleft bbottom bright">
				<?php
					$ma = $a[1] + ($a[0]*60);
					$md = $d[1] + ($d[0]*60);

					$tm = $md - $ma;
					$th = floor($tm / 60);
					$c = $tm / 60;
					$tm = $tm - ($th * 60);

					if($th < 10)
					{
						$fh = "0" . $th . ":";
						if($tm < 10)
						{
							$fh = $fh . "0" . $tm;
						}
						else
						{
							$fh = $fh . $tm;
						}
					}
					else
					{
						$fh = $th . ":";
						if($tm < 10)
						{
							$fh = $fh . "0" . $tm;
						}
						else
						{
							$fh = $fh . $tm;
						}
					}
					echo("&nbsp;" . $fh);
				?>
			</td>
		</tr>
		<tr>
			<td class="bleft bbottom aright">
				<b>SOIT :</b>
			</td>
			<td class="hightlight bleft bbottom bright">
				<?php
					echo("&nbsp;" . round($c, 2) . " heure(s).");
				?>
			</td>
		</tr>
		<tr>
			<td colspan="4">
			</td>
		</tr>
		<?php
			if(!$_GET['pdf'])
			{
				$descSize = "";
			}
			else
			{
				$descSize = "font-size:10px";
			}
		?>
		<tr style="padding:-2">
			<td colspan="2" class="btop bleft bright bbottom vtop" style="padding:0;">
				<b><span >DESCRIPTION DE L'INTERVENTION :<br/><br/></span></b>
				<span class="hightlight" style="<?php echo $descSize;?>">
					<?php echo str_replace("\n", "<br>", $inter['DESCRIPTION']);?>
				</span>
			</td>
			<td colspan="2" class="btop" style="padding:0;margin:0;">
				<table id="billing" cellspacing="0" style="height:100%;margin:0;padding:0;background: white;">
					<tr>
						<td colspan="3" class="bbottom bright">
							<b><span >SOUMIS A DEVIS :</span></b>
						</td>
					</tr>
					<tr>
						<td style="width:50%;text-align:center;" class="bbottom bright">
							<b>DESIGNATION</b>
						</td>
						<td style="width:25%;text-align:center;" class="bbottom bright">
							<b>REMISE</b>
						</td>
						<td style="width:25%;text-align:center;" class="bbottom bright">
							<b>PRIX</b>
						</td>
					</tr>
					<tr>
						<td style="width:50%" class="hightlight bbottom bright aleft designation">
							<?php
								if(is_numeric($inter['FACTU1'])){
									for($x = 0;$x < sizeOf($factuName);$x++){
										if(array_search($inter['FACTU1'], $factuName[$x])){
											echo $factuName[$x]['LIBELLE'];
										}
									}
								}
								else{
									echo $inter['FACTU1'];
								}
							?>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php echo $inter['REMISE1'] ? $inter['REMISE1']."%" : "";?>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php echo $inter['FACTU_PRIX1'] ? number_format(floatval($inter['FACTU_PRIX1']),2,","," ")."€" : "";?>
						</td>
					</tr>
					<tr>
						<td style="width:50%" class="hightlight bbottom bright aleft designation">
							<?php
								if(is_numeric($inter['FACTU2'])){
									for($x = 0;$x < sizeOf($factuName);$x++){
										if(array_search($inter['FACTU2'], $factuName[$x])){
											echo $factuName[$x]['LIBELLE'];
										}
									}
								}
								else{
									echo $inter['FACTU2'];
								}
							?>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php echo $inter['REMISE2'] ? $inter['REMISE2']."%" : "";?>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php echo $inter['FACTU_PRIX2'] ? number_format(floatval($inter['FACTU_PRIX2']),2,","," ")."€" : "";?>
						</td>
					</tr>
					<tr>
						<td style="width:50%" class="hightlight bbottom bright aleft designation">
							<?php
								if(is_numeric($inter['FACTU3'])){
									for($x = 0;$x < sizeOf($factuName);$x++){
										if(array_search($inter['FACTU3'], $factuName[$x])){
											echo $factuName[$x]['LIBELLE'];
										}
									}
								}
								else{
									echo $inter['FACTU3'];
								}
							?>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php echo $inter['REMISE3'] ? $inter['REMISE3']."%" : "";?>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php echo $inter['FACTU_PRIX3'] ? number_format(floatval($inter['FACTU_PRIX3']),2,","," ")."€" : "";?>
						</td>
					</tr>
					<tr>
						<td style="width:50%" class="hightlight bbottom bright aleft designation">
							<?php
								if(is_numeric($inter['FACTU4'])){
									for($x = 0;$x < sizeOf($factuName);$x++){
										if(array_search($inter['FACTU4'], $factuName[$x])){
											echo $factuName[$x]['LIBELLE'];
										}
									}
								}
								else{
									echo $inter['FACTU4'];
								}
							?>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php echo $inter['REMISE4'] ? $inter['REMISE4']."%" : "";?>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php echo $inter['FACTU_PRIX4'] ? number_format(floatval($inter['FACTU_PRIX4']),2,","," ")."€" : "";?>
						</td>
					</tr>
					<tr>
						<td style="width:50%" class="hightlight bbottom bright aleft designation">
							<?php
								if(is_numeric($inter['FACTU5'])){
									for($x = 0;$x < sizeOf($factuName);$x++){
										if(array_search($inter['FACTU5'], $factuName[$x])){
											echo $factuName[$x]['LIBELLE'];
										}
									}
								}
								else{
									echo $inter['FACTU5'];
								}
							?>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php echo $inter['REMISE5'] ? $inter['REMISE5']."%" : "";?>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php echo $inter['FACTU_PRIX5'] ? number_format(floatval($inter['FACTU_PRIX5']),2,","," ")."€" : "";?>
						</td>
					</tr>
					<tr>
						<td style="width:50%" class="hightlight bbottom bright aleft designation">
							<?php
								if(is_numeric($inter['FACTU6'])){
									for($x = 0;$x < sizeOf($factuName);$x++){
										if(array_search($inter['FACTU6'], $factuName[$x])){
											echo $factuName[$x]['LIBELLE'];
										}
									}
								}
								else{
									echo $inter['FACTU6'];
								}
							?>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php echo $inter['REMISE6'] ? $inter['REMISE6']."%" : "";?>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php echo $inter['FACTU_PRIX6'] ? number_format(floatval($inter['FACTU_PRIX6']),2,","," ")."€" : "";?>
						</td>
					</tr>
					<tr>
						<td style="width:50%" class="bright">
						</td>
						<td style="width:25%" class="bbottom bright aright">
							<b>TOTAL HT :</b>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php
								$factuTotHT = floatval($inter['FACTU_PRIX6'] + $inter['FACTU_PRIX5'] + $inter['FACTU_PRIX4'] + $inter['FACTU_PRIX3'] + $inter['FACTU_PRIX2'] + $inter['FACTU_PRIX1']);

								echo number_format($factuTotHT,2,","," ")."€";
							?>
						</td>
					</tr>
					<tr>
						<td style="width:50%" class="bright">
						</td>
						<td style="width:25%" class="bbottom bright aright">
							<b>TAUX TVA :</b>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php
								echo number_format(floatval($inter['TVA']),2,","," ")."%";
								$tva = floatval($inter['TVA']);
							?>
						</td>
					</tr>
					<tr>
						<td style="width:50%" class="bright">
						</td>
						<td style="width:25%" class="bbottom bright aright">
							<b>TOTAL TVA :</b>
						</td>
						<td style="width:25%" class="hightlight bbottom bright acenter">
							<?php
								echo number_format((($factuTotHT/100)*$tva),2,","," ")."€";
							?>
						</td>
					</tr>
					<tr>
						<td style="width:50%" class="bright">
						</td>
						<td style="width:25%" class="bright aright bbottom">
							<b>TOTAL TTC :</b>
						</td>
						<td style="width:25%" class="hightlight bright acenter bbottom">
							<?php
								$factuTotTTC = $factuTotHT + (($factuTotHT/100)*$tva);
								echo number_format($factuTotTTC,2,","," ")."€"
							?>
						</td>
					</tr>
				</table>
				<style>
					#billing .designation{
						font-size:	10px;
					}
				</style>
			</td>
		</tr>
		<tr>
			<td colspan="4">
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td class="bleft btop acenter">
				<b>QUANTITE</b>
			</td>
			<td class="bleft btop acenter">
				<b>PRIX UNITAIRE</b>
			</td>
			<td class="bleft btop bright acenter">
				<b>TOTAL</b>
			</td>
		</tr>
		<tr>
			<td class="bleft btop aright">
				<b>MAIN D'OEUVRE (en heure) :</b>
			</td>
			<td class="hightlight bleft btop acenter">
				<?php
					echo($fh);
				?>
			</td>
			<td class="hightlight bleft btop acenter">
				<?php echo number_format($inter['WORKFORCE'],2,","," ") . "€"?>
			</td>
			<td class="hightlight bleft btop bright acenter">
				<?php
					$mo = round($c, 2) * $inter['WORKFORCE'];
					echo number_format($mo, 2,","," ") . "€";
				?>
			</td>
		</tr>
		<tr>
			<td class="bleft btop bbottom aright">
				<b><span >DEPLACEMENT :</span></b>
			</td>
			<td class="hightlight bleft btop bbottom acenter">
				<?php
					$depl = $inter['TRAVEL_PRICE'];

					switch($inter['DEPLACEMENT']){
						case 0:
							echo($tarif[1]['ZONE1']);
							$depl = $tarif[0]['ZONE1'];
							break;
						case 1:
							echo($tarif[1]['ZONE2']);
							$depl = $tarif[0]['ZONE2'];
							break;
						case 2:
							echo($tarif[1]['ZONE3']);
							$depl = $tarif[0]['ZONE3'];
							break;
						case 3:
							echo($tarif[1]['ZONE4']);
							$depl = $tarif[0]['ZONE4'];
							break;
						default:
							echo("AUCUNS");
							$depl = 0;
							break;
					}
				?>
			</td>
			<td class="hightlight bleft btop bbottom acenter">
				<?php
					echo number_format($depl,2,","," ") . "€";
				?>
			</td>
			<td class="hightlight bleft btop bbottom bright acenter">
				<?php
					echo number_format($depl,2,","," ") . "€";
				?>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td colspan="3" style="background: black;">
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td class="bleft btop aright">
				<b>TOTAL HT :</b>
			</td>
			<td class="hightlight bleft btop acenter">
				<?php
					$htu = $inter['WORKFORCE'] + $depl;
					echo number_format($htu,2,","," ") . "€";
				?>
			</td>
			<td class="hightlight bleft btop bright acenter">
				<?php
					$htt = round($mo, 2) + $depl;
					echo(number_format(round($htt,2),2,","," ") . "€");
				?>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td class="bleft btop aright">
				<b>TAUX TVA :</b>
			</td>
			<td class="hightlight bleft btop acenter">
				<?php echo number_format($inter['TVA'],2,","," ")."%"?>
			</td>
			<td class="hightlight bleft btop bright acenter">
				<?php echo number_format($inter['TVA'],2,","," ")."%"?>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td class="bleft btop aright">
				<b>TOTAL TVA :</b>
			</td>
			<td class="hightlight bleft btop acenter">
				<?php
					$tvau = ($htu / 100) * 20;
					echo(number_format(round($tvau, 2),2,","," ") . "€");
				?>
			</td>
			<td class="hightlight bleft btop bright acenter">
				<?php
					$tvat = ($htt / 100) * 20;
					echo(number_format(round($tvat, 2),2,","," ") . "€");
				?>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td class="bleft btop bbottom aright">
				<b>TOTAL TTC :</b>
			</td>
			<td class="hightlight bleft btop bbottom acenter">
				<?php
					$ttcu = round($tvau, 2) + $htu;
					echo(number_format($ttcu,2,","," ") . "€");
				?>
			</td>
			<td class="hightlight bleft btop bbottom bright acenter">
				<?php
					$ttct = round($tvat, 2) + $htt;
					echo(number_format($ttct,2,","," ") . "€");
				?>
			</td>
		</tr>
		<tr>
			<td>
			</td>
			<td class="bleft bbottom aright">
				<b>CONTRAT DE MAINTENANCE :</b>
			</td>
			<td class="hightlight bleft bbottom acenter">
				<?php
					if($inter['CONTRAT'])
					{
						$contrat = "INCLUS";
					}
					else
					{
						$contrat = "NON INCLUS";
					}
					echo("<span class='updated'>" . $contrat . "</span>");
				?>
			</td>
			<td class="hightlight bleft bbottom bright acenter">
				<?php
					echo("<span class='updated'>" . $contrat . "</span>");
				?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			</td>
			<td class="bleft aright">
				<b>TOTAL :</b>
			</td>
			<td class="hightlight bleft bright acenter">
				<?php
					if($inter['CONTRAT'])
					{
						echo floatval($factuTotTTC) ? number_format(floatval($factuTotTTC),2,","," ")."€" : "00,00€";
					}
					else
					{

						echo $ttct + floatval($factuTotTTC) ? number_format($ttct + floatval($factuTotTTC),2,","," ")."€" : "00,00€";
					}
				?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			</td>
			<td colspan="2" class="aright btop">
				<i>*Hors facturation tierses. Sous réserve de modifications.</i>
			</td>
		</tr>
		<tr>
			<td class="bleft btop">
				<b>Matériel prêté :</b>
			</td>
			<td class="btop">
			</td>
			<td class="bleft btop">
				<b>Numéro de série :</b>
			</td>
			<td class="bright btop">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="hightlight bleft aright bbottom bleft">
				<span >
					<?php
						if($inter['MATERIELS'] == ""){
							echo "Aucun";
						}
						else{
							echo str_replace("\n", "<br>", $inter['MATERIELS']);
						}
					?>
				</span>
			</td>
			<td colspan="2" class="hightlight bleft bright bbottom bleft bright">
				<span >
					<?php
						if($inter['NUMEROS_SERIES'] == ""){
							echo "Aucun";
						}
						else{
							echo str_replace("\n", "<br>", $inter['NUMEROS_SERIES']);
						}
					?>
				</span>
			</td>
		</tr>
		<tr>
			<td	colspan="4">
			</td>
		</tr>
		<tr>
			<td class="btop bleft aright">
				<b>Test de bon fonctionnement :</b>
			</td>
			<td class="hightlight btop">
				<span ><?php
					if($inter['FONCTIONNEMENT'])
					{
						echo("Oui");
					}
					else if($inter['FONCTIONNEMENT'] == 0)
					{
						echo("Non");
					}
					else
					{
						echo("Non Concerné");
					}
				?></span>
			</td>
			<td class="btop bleft aright">
				<b>Formation à la sauvegarde :</b>
			</td>
			<td class="hightlight btop bright">
				<span ><?php
					if($inter['FORMATION_SAUVGARDE'])
					{
						echo("Oui");
					}
					else if($inter['FORMATION_SAUVGARDE'] == 0)
					{
						echo("Non");
					}
					else
					{
						echo("Non Concerné");
					}
				?></span>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="aright bleft btop bbottom vmiddle">
				Le matériel et/ou le logiciel sur lequel nous intervenons
			</td>
			<td colspan="2" class="hightlight aleft btop bright bbottom vmiddle" style="font-size:14px;">
				<span ><?php
					if($inter['LOI_NF'])
					{
						echo("<b>est mis à jour conformément à la loi 2018-1317 du 28 décembre 2018.</b>");
					}
					else
					{
						echo("<b>n'est pas conforme à la loi 2018-1317 du 28 décembre 2018.</b>");
					}
				?></span>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			</td>
			<td class="hightlight bleft aright">
				<?php
					if($inter['LOI_NF'])
					{
						echo("<b>Numéro de Version :</b>");
					}
					else
					{
						echo("<b>Si NON, mise à jour réalisée :</b>");
					}
				?>
			</td>
			<td class="hightlight bright">
				<?php
					if($inter['LOI_NF'])
					{
						echo($inter['VERSION']);
					}
					else
					{
						if($inter['MISE_A_JOUR'] == "1")
						{
							echo("OUI");
						}
						else if($inter['MISE_A_JOUR'] == "-1")
						{
							echo("Non Concerné");
						}
						else
						{
							echo("NON");
						}
					}
				?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			</td>
			<td class="hightlight bleft bbottom aright">
				<?php
					if($inter['MISE_A_JOUR'] == "1")
					{
						echo("<b>Mise à jour réalisée :</b>");
					}
					else
					{
						echo("");
					}
				?>
			</td>
			<td class="hightlight bright bbottom">
				<?php
					if($inter['MISE_A_JOUR'] == "1")
					{
						echo("Oui (".$inter['VERSION'].")");
					}
					else
					{
						echo("");
					}
				?>
			</td>
		</tr>
		<tr>
			<td colspan="4">
			</td>
		</tr>
		<tr>
			<td colspan="4" class="bleft btop bright">
				<b>Compléments :</b>
			</td>
		</tr>
		<tr>
			<td colspan="4" class="hightlight bleft bright bbottom bleft bright">
				<span ><?php
					if($inter['COMPLEMENTS'] != ""){
						echo str_replace("\n", "<br>", $inter['COMPLEMENTS']);
					}
					else{
						echo "Aucun";
					}
				?></span>
			</td>
		</tr>
		<tr>
			<td colspan="4">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="bleft btop acenter vmiddle">
				<span>Nom et signature du technicien</span>
			</td>
			<td colspan="2" class="bleft btop bright acenter vmiddle">
				<span>Nom, Qualité et Signature précédée de la mention "Lu et pprouvé"</span>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="bleft">
			</td>
			<td class="bleft">
				Lu et approuvé,
			</td>
			<td class="bright">
			</td>
		</tr>
		<tr>
			<td colspan="2" class="hightlight bleft acenter">
				<span ><?php echo($user['NAME']);?></span>
			</td>
			<td colspan="2" class="hightlight bleft acenter bright">
				<span ><?php echo($inter['SIGNATAIRE']);?>, <?php echo($inter['QUALITE_SIGNATAIRE']);?></span>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="bleft bbottom acenter" style="height:calc(24px*8)">
				<?php if($_GET['pdf']):?>
					<img src="<?php echo  "/home/tacteose/sav/signatures/TECHS/".$user['ID'].".png";?>" alt="SIGNATURE TECHNICIEN" style="width:209px;height:88px;">
				<?php else:?>
					<span id="sign"><p>Signature active lors de la génération du pdf.</p></span>
				<?php endif;?>
			</td>
			<td colspan="2" class="bleft bbottom bright acenter" style="height:calc(24px*8)">
				<?php if($_GET['pdf']):?>
					<img src="<?php echo $inter['SIGNATURE'];?>" alt="SIGNATURE CLIENT" style="width:209px;height:88px;">
				<?php else:?>
					<span id="sign"><p>Signature active lors de la génération du pdf.</p></span>
				<?php endif;?>
			</td>
			<td>
			</td>
			<td>
			</td>
		</tr>
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
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
		</tr>
		<tr>
			<td colspan="4" rowspan="3" style="height:calc(24px*3); font-size:10px;" class="acenter">
				"SARL TACTEO SE au capital de 10 000€uros - Siège social 11 B Avenue de Bellevue 33650 LA BREDE<br>
				RCS de BORDEAUX 539 064 204 - TVA intra FR 8853964204 Tél: 05.56.94.26.96 - Fax: 09.51.09.24.64 - Email: contact@tacteo-se.fr<br>
				site: www.tacteo-se.fr"
			</td>
		</tr>
	</tbody>
</table>

<?php if($_GET['send']): ?>
	<div id="PDFbtnDIV">
		<a id="NEWbtn" href="http://sav.tacteo.fr/INTER" target="_parent">Nouvelle Intervention</a>
	</div>
<?php elseif($_GET['pdf'] != 1): ?>
	<div id="PDFbtnDIV">
		<a id="PDFbtn" href="view_inter.php?inter=<?php echo($inter['ID']);?>&pdf=1" target="_blank">PDF</a>
		<?php if($_SESSION['rights'] > 1):?>
			<a id="EDITbtn" href="../tools/update_inter.tool.php?inter=<?=$inter['ID']?>" target="_self" onclick="edit()">Modifier</a>
			<?php if(!$inter['ARCHIVE'] || $inter['ARCHIVE'] == 2):?>
				<a id="ARCHIVEbtn" href="../tools/archive.tool.php?inter=<?=$inter['ID']?>" target="_self">Archiver</a>
			<?php else:?>
				<a id="ARCHIVEbtn" href="../tools/archive.tool.php?inter=<?=$inter['ID']?>" target="_self">Désarchiver</a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<script>
		function edit()
		{
			document.getElementsByTagName("html")[0].style.display = "none";
		}
	</script>
<?php endif; ?>

<style>
	#sign{
		width:		209px;
		height:		88px;
		border:		black solid 1px;
		display:	block;
		margin:		0 auto;
		text-align:	center;
	}

	#sign p{
		position:		relative;
		top:			50%;
		transform:		translateY(-50%);
		margin:			0;
		vertical-align:	middle;
		color:			red;
	}

	tr{
		height:	1.5em;
	}

	#billing{
		margin:	0;
		width:	100%;
	}

	#PDFbtnDIV{
		font-family: 'Cabin Condensed';
	}

	#PDFbtn {
		width:				80px;
		height:				40px;

		border-radius:		5px 5px 0 0;

		background:			rgba(0,0,0,0.9);
		position:			fixed;

		bottom:				0;
		left:				5px;

		text-align:			center;
		text-decoration:	none;
		font-weight:		bold;
		color:				white;
		font-size:			25px;

		z-index:			1;
	}

	#NEWbtn {
		width: 159px;
		height: 100px;
		border-radius: 5px 5px 0 0;
		background: rgba(0,0,0,0.9);
		position: fixed;
		bottom: 0;
		left: 50%;
		transform: translateX(-50%);
		text-align: center;
		text-decoration: none;
		font-weight: bold;
		color: white;
		font-size: 25px;
		z-index: 1;
	}

	#EDITbtn {
		width:				160px;
		height:				40px;

		border-radius:		5px 5px 0 0;

		background:			rgba(0,0,0,0.9);
		position:			fixed;

		bottom:				0;
		left:				90px;

		text-align:			center;
		text-decoration:	none;
		font-weight:		bold;
		color:				white;
		font-size:			25px;

		z-index:			1;
	}

	#ARCHIVEbtn {
		width:				160px;
		height:				40px;

		border-radius:		5px 5px 0 0;

		background:			rgba(0,0,0,0.9);
		position:			fixed;

		bottom:				0;
		right:				5px;

		text-align:			center;
		text-decoration:	none;
		font-weight:		bold;
		color:				white;
		font-size:			25px;

		z-index:			1;
	}
</style>

<?php
	$_HTML = ob_get_clean();

	require '../vendor/autoload.php';
	use Spipu\Html2Pdf\Html2Pdf;

	if($_GET['pdf'])
	{
		$name = "Intervention " . $inter['ID'] . " " . $inter['CLIENT'] . " (" . $inter['CODE_CLIENT'] . ") " . $inter['DATE'] . ".pdf";
		$pdf = new \Spipu\Html2Pdf\Html2Pdf('P','A3','fr', true, 'UTF-8', array(21, 21, 21, 21));
		$pdf->writeHTML($_HTML);

		if($_POST['SENDMAIL'])
		{
			$base64 = $pdf->output($name, 'E');
			sendMail($base64, $name, $inter);
		}
		else
		{
			$pdf->output($name);
		}
	}
	else
	{
		echo($_HTML);
	}

	function sendMail($pdf, $name, $inter){
		//=====Récupération des informations techniciens.
		$req = $GLOBALS['bdd']->prepare("SELECT * FROM USERS_INTER WHERE ID = :tech");
		$req->execute(array('tech'	=>	$inter['TECHNICIEN']));
		//==========

		$user = $req->fetch(2);
		$mailBcc .= $user['MAIL'].",";

		//=====Récupération des emails établissement.
		$req = $GLOBALS['bdd']->query("SELECT * FROM SETTINGS WHERE NAME = 'MAILING'") or die(print_r($GLOBALS['bdd']->errorInfo()));
		$req = $req->fetch();
		$mailBcc .= $req['VALUE'];
		//==========

		//=====Récupération de l'email du client.
		$mail = $inter['EMAIL'];
		//==========

		//=====On filtre les serveurs qui présentent des bogues.
		if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail))
		{
			$passage_ligne = "\r\n";
		}
		else
		{
			$passage_ligne = "\n";
		}
		//==========

		//=====Déclaration des messages au format texte et au format HTML.

		$dateInter = explode(" ", $inter['DATE']);
		$timeInter = $dateInter[1];
		$dateInter = $dateInter[0];

		$dateInter = explode("-", $dateInter);
		$dateInter = $dateInter[2]."/".$dateInter[1]."/".$dateInter[0];

		$timeInter = explode(":", $timeInter);
		$timeInter = $timeInter[0].":".$timeInter[1];

		$description = str_replace("\n","<br />",$inter['DESCRIPTION']);

		$message_txt = "
			Retrouvez en pièce jointe, votre copie du bon d'intervention N°".$inter['ID']." réalisée le ".$dateInter." à ".$timeInter.".\n
			Cette Intervention à été réalisée le ".$dateInter." à ".$timeInter." par ".$user['NAME'].".\n
			Celui-ci a réalisé :\n
			".$inter['DESCRIPTION']."\n\n
			Pour toutes demandes, merci de nous contacter par téléphone au 05 56 94 26 96 ou par mail à sav@tacteo-se.fr.\n
			Cordialement,\n\n
			L'équipe Tacteo.
		";
		$message_html = "
			<html style='width: 100%;margin: 0 auto; display: block'>
				<h1 style='text-align: center;'>
					Retrouvez en pièce jointe, votre copie du bon d'intervention N°".$inter['ID'].".
				</h1>

				<img id='ban' src='https://sav.tacteo.fr/img/logoBan.png' alt='SAV TACTEO' style='width: 50%; display: block; margin: 0 auto;'>

				<p style='width: 75%;display:block;margin:0 auto'>
					Cette Intervention à été réalisée le ".$dateInter." à ".$timeInter." par ".$user['NAME'].".<br />
					Celui-ci a réalisé :<br />
					".$description."<br /><br />
					Pour toutes demandes, merci de nous contacter par téléphone au 05 56 94 26 96 ou par mail à <a href='mailto:sav@tacteo-se.fr'>sav@tacteo-se.fr</a>.<br />
					Cordialement,<br /><br />
					<i>L'équipe Tacteo.</i>
				</p>
			</html>
		";
		//==========

		//=====Mise en forme de la pièce jointe.
		$attachement = $pdf;
		//==========

		//=====Création de la boundary.
		$boundary = "-----=".md5(rand());
		$boundary_alt = "-----=".md5(rand());
		//==========

		//=====Définition du sujet.
		$sujet = $name;
		//=========

		//=====Création du header de l'e-mail.
		$header = "From: \"Tacteo SE\"<no-reply@tacteo-se.fr>".$passage_ligne;
		$header.= "Reply-to: \"Tacteo SE\" <no-reply@tacteo-se.fr>".$passage_ligne;
		$header.= "MIME-Version: 1.0".$passage_ligne;
		$header.= "Content-Type: multipart/mixed;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
		$header.= "Bcc: ".$mailBcc.$passage_ligne;
		//==========

		//=====Création du message.
		$message = $passage_ligne."--".$boundary.$passage_ligne;
		$message.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary_alt\"".$passage_ligne;
		$message.= $passage_ligne."--".$boundary_alt.$passage_ligne;

		//=====Ajout du message au format texte.
		$message.= "Content-Type: text/plain; charset=\"UTF-8\"".$passage_ligne;
		$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
		$message.= $passage_ligne.$message_txt.$passage_ligne;
		//==========

		$message.= $passage_ligne."--".$boundary_alt.$passage_ligne;

		//=====Ajout du message au format HTML.
		$message.= "Content-Type: text/html; charset=\"UTF-8\"".$passage_ligne;
		$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
		$message.= $passage_ligne.$message_html.$passage_ligne;
		//==========

		//=====On ferme la boundary alternative.
		$message.= $passage_ligne."--".$boundary_alt."--".$passage_ligne;
		//==========

		$message.= $passage_ligne."--".$boundary.$passage_ligne;

		//=====Ajout de la pièce jointe.
		$message.= $attachement;
		$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
		//==========

		//=====Envoi de l'e-mail.
		mail($mail,$sujet,$message,$header);
		die();
		//==========
	}
?>
