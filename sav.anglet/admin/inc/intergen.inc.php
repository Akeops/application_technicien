<?php
	require_once('config.inc.php');
	ini_set("pcre.backtrack_limit", "5000000");
	if(is_null($bdd))
	{
		header('Location: /');
		die();
	}
	if(!empty($_GET['inter']) && is_numeric($_GET['inter']))
	{
		$req = $bdd->prepare("SELECT * FROM INTER WHERE ID = :id");
		$req->execute(array('id' => $_GET['inter'])) or die(print_r($req->errorInfo()));
		$result = $req->fetch(2);
		$_INTER = array();

		$req = $bdd->prepare("SELECT * FROM ETABLISSEMENT");
		$req->execute() or die(print_r($req->errorInfo()));
		$_ESTABLISHMENT = $req->fetch(2);

		$req = $bdd->prepare("SELECT * FROM AGENCES WHERE ID = :id");
		$req->execute(array('id' => $result['AGENCY'])) or die(print_r($req->errorInfo()));
		$_AGENCY = $req->fetch(2);
		$result['AGENCY'] = $_AGENCY;

		$req = $bdd->prepare("SELECT * FROM USERS_INTER WHERE ID = :id");
		$req->execute(array('id' => $result['USER_UPLOAD'])) or die(print_r($req->errorInfo()));
		$_TECHNICIAN = $req->fetch(2);
		$result['TECHNICIAN'] = $_TECHNICIAN;
	}
	else
	{
		die();
	}

	$keys = array_keys($result);
	foreach($keys as $value)
	{
		if(strpos($value, 'CUSTOMER_') !== false)
		{
			$key = str_replace('CUSTOMER_', '', $value);
			$_INTER['CUSTOMER'][$key] = $result[$value];
			continue;
		}
		else if(strpos($value, 'WORKFORCE_') !== false)
		{
			$key = str_replace('WORKFORCE_', '', $value);
			$_INTER['WORKFORCE'][$key] = $result[$value];
			continue;
		}
		else if(strpos($value, 'TRAVEL_') !== false)
		{
			$key = str_replace('TRAVEL_', '', $value);
			$_INTER['TRAVEL'][$key] = $result[$value];
			continue;
		}
		else if(strpos($value, 'BILLINGS_THIRD') !== false)
		{
			$_INTER['BILLINGS_THIRD'] = JSON_decode($result[$value], true);
		}
		else
		{
			$_INTER[$value] = $result[$value];
		}
	}

	ob_start();
?>

<html>
	<head>
		<style>
			table{
				border-collapse:	collapse
				width:				100%;
				margin:				0 auto;
				vertical-align:		middle;
			}

			table td{
				height:				1em;
				width:				25%;
				padding:			0 2px;
			}
		</style>
	</head>
	<body>
		<!--mpdf
			<htmlpageheader name="myheader">
				<table cellspacing=0 cellpadding=0 border=0 style="width:100%">
					<tr>
						<td>
							<img src="<?=$_ESTABLISHMENT['LOGO']?>" width="209px" height="88px" alt="logo_tacteo">
						</td>
						<td style="font-weight:bold">
							<?=$_ESTABLISHMENT['SOCIAL_REASON']?><br />
							<?=$_INTER['AGENCY']['ADDRESS_LINE1']?><br />
							<?=$_INTER['AGENCY']['ADDRESS_LINE2'] ? $_INTER['AGENCY']['ADDRESS_LINE2']."<br />" : ""?>
							<?=$_INTER['AGENCY']['POSTAL_CODE']." ".$_INTER['AGENCY']['CITY']?><br />
							Tél <?=$_INTER['AGENCY']['PHONE']?><br />
						</td>
						<td style="text-align:right;font-weight:bold">
							Agence de :<br />
							Date d'intervention :<br/>
							BON D'INTERVENTION N°<br/>
							CODE CLIENT :
						</td>
						<td>
							<?=$_INTER['AGENCY']['CITY']?><br />
							<?=strftime("%d/%m/%Y à %H:%M:%S",strtotime($_INTER['DATE_END']))?><br />
							<?=$_INTER['ID']?><br />
							<?=$_INTER['CUSTOMER']['CODE']?><br />
						</td>
					</tr>
				</table>
			</htmlpageheader>
			<htmlpagefooter name="myfooter">
				<div style="border-top: 0.25mm solid #000000; font-size: 5pt; text-align: center; padding-top: 3mm; ">
					<?=$_ESTABLISHMENT['END_TEXT']?><br /><br />
					<span style="font-size:8pt">Bon d'intervention</span><br />
					<span style="font-size:8pt">{PAGENO} / {nbpg}</span>
				</div>
			</htmlpagefooter>
			<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
			<sethtmlpagefooter name="myfooter" value="on" />
		mpdf-->

		<!-- HEADER -->
		<table cellspacing=0 cellpadding=0 style="border:0.25mm solid black;width:100%">
			<tr>
				<td style="border-right:0.25mm solid black;font-weight:bold">
					CLIENT :
				</td>
				<td style="font-weight:bold">
					COORDONNEES :
				</td>
				<td style="border-right:0.25mm solid black;font-weight:bold">
				</td>
				<td style="font-weight:bold">
					OBJET DE L'INTERVENTION :
				</td>
			</tr>
			<tr>
				<td rowspan="5" style="border-right:0.25mm solid black">
					<?=$_INTER['CUSTOMER']['LABEL']?><br />
					<?=$_INTER['CUSTOMER']['ADDRESS_LINE1']?><br />
					<?=$_INTER['CUSTOMER']['ADDRESS_LINE2'] ? $_INTER['CUSTOMER']['ADDRESS_LINE2']."<br/>" : "" ?>
					<?=$_INTER['CUSTOMER']['ADDRESS_POSTAL_CODE']?> <?=$_INTER['CUSTOMER']['ADDRESS_CITY']?>
				</td>
				<td style="border-bottom:0.25mm solid black;text-align:right">
					<?=$_INTER['CUSTOMER']['PHONE']?>
				</td>
				<td style="border-right:0.25mm solid black; border-bottom:0.25mm solid black">
					<?=$_INTER['CUSTOMER']['EMAIL']?>
				</td>
				<td rowspan="5" style="border-right:0.25mm solid black;text-align:right;vertical-align:middle">
					<?php
						if($_INTER['INSTALL'])
						{
							echo("INSTALLATION<br>");
						}
						if($_INTER['MAINTENANCE'])
						{
							echo("MAINTENANCE<br>");
						}
						if($_INTER['TRAINING'])
						{
							echo("FORMATION<br>");
						}
						if($_INTER['RENEWAL'])
						{
							echo("RENOUVELLEMENT<br>");
						}
						if($_INTER['RECOVERY'])
						{
							echo("RECUPERATION MATERIELS<br>");
						}
						if($_INTER['DELIVERY'])
						{
							echo("LIVRAISON");
						}
						if($_INTER['NEAR_VISIT'])
						{
							echo("PRE-VISITE");
						}
					?>
				</td>
			</tr>
			<tr>
				<td style="border-right:0.25mm solid black; border-bottom:0.25mm solid black;font-weight:bold;text-align:right">
					HEURE D'ARRIVEE :
				</td>
				<td style="border-right:0.25mm solid black; border-bottom:0.25mm solid black">
					<?=strftime("%d/%m/%y %H:%M:%S",strtotime($_INTER['DATE_START']))?>
				</td>
			</tr>
			<tr>
				<td style="border-right:0.25mm solid black; border-bottom:0.25mm solid black;font-weight:bold;text-align:right">
					HEURE DE DEPART :
				</td>
				<td style="border-right:0.25mm solid black; border-bottom:0.25mm solid black">
					<?=strftime("%d/%m/%y %H:%M:%S",strtotime($_INTER['DATE_END']))?>
				</td>
			</tr>
			<tr>
				<td style="border-right:0.25mm solid black; border-bottom:0.25mm solid black;font-weight:bold;text-align:right">
					TOTAL D'HEURES :
				</td>
				<td style="border-right:0.25mm solid black; border-bottom:0.25mm solid black">
					<?php
						$dateStart = new DateTime($_INTER['DATE_START']);
						$dateEnd = new DateTime($_INTER['DATE_END']);
						$dateStart = $dateStart->getTimeStamp();
						$dateEnd = $dateEnd->getTimeStamp();
						$interval = $dateEnd - $dateStart;
						$hours = floor($interval / 3600);
						$minutes = round(($interval - ($hours * 3600)) / 60);
						$fh = substr('0'.$hours, -2)."h ".substr('0'.$minutes, -2)."mn";
						echo $fh;
					?>
				</td>
			</tr>
			<tr>
				<td style="border-right:0.25mm solid black;font-weight:bold;text-align:right">
					SOIT :
				</td>
				<td style="border-right:0.25mm solid black">
					<?php
						echo round(($interval / 3600), 2)." Heure(s)";
					?>
				</td>
			</tr>
		</table>
		<table cellspacing=0 cellpadding=0>
			<tbody>
				<tr>
					<td colspan="4">
					</td>
				</tr>
				<?php
					$descSize = "font-size:10px";
					$temp = explode("\n", $_INTER['DESCRIPTION']);
					$output = array();
					$y = 0;
					$maxChar = 70;
					$maxLines = 20;
					for($x = 0; $x < sizeOf($temp); $x++)
					{
						if(strlen($temp[$x]) < $maxChar)
						{
							$output[$y] = $temp[$x];
							$y++;
						}
						else
						{
							$z = 0;
							while($z < strlen($temp[$x]))
							{
								$w = 0;
								while($temp[$x][$w + $maxChar] != " ")
								{
									$w--;
								}
								$output[$y] = mb_strcut($temp[$x], $z, $maxChar - abs($w), 'UTF-8');
								$y++;
								$z = $z + $maxChar - abs($w);
							}
						}
					}

					$desc = $output;
				?>
			</tbody>
		</table>
		<table cellspacing=0 cellpadding=0 style="border:0.25mm solid black;width:90%">
			<tr>
				<td style="width:50%">
					<b><span>DESCRIPTION DE L'INTERVENTION :</span></b>
				</td>
				<td style="border-left: 1px solid #d3d3d3;width:50%">
				</td>
			</tr>
		<?php
			for($x = 0; $x < ($maxLines - 1) && $x < sizeOf($desc); $x++):
		?>
			<tr>
				<td style="width:50%">
					<span>
						<?=$desc[$x]?>
					</span>
				</td>
				<td style="border-left: 1px solid #d3d3d3;width:50%">
					<span>
						<?=$desc[$x+$maxLines]?>
					</span>
				</td>
			</tr>
			<?php endfor; ?>
		</table>
		<table cellspacing=0 cellpadding=0>
			<tbody>
				<tr>
					<td colspan="4">
					</td>
				</tr>
			</tbody>
		</table>
		<?php
			if(!empty($_INTER['LOANS'])):
				$loans = JSON_decode($_INTER['LOANS']);
		?>
				<table cellspacing=0 cellpadding=0 style="border:0.25mm solid black;width:90%" id="loans">
					<tr>
						<td colspan="4" style="border-bottom:0.25mm solid black;">
							<b>Matériel(s) de prêt :</b>
						</td>
					</tr>
				<?php
					for($x = 0; $x < count((array)$loans); $x++):
				?>
						<tr style="border-bottom:0.25mm solid black;">
							<td style="border-right:1px dotted black;text-align:right">
								<?=$loans->{$x}->name?>
							</td>
							<td style="border-right:0.25mm solid black">
								<?=$loans->{$x}->serial?>
							</td>
							<?php $x++?>
							<td style="text-align:right;border-right:1px dotted black">
								<?=$loans->{$x} ? $loans->{$x}->name : ""?>
							</td>
							<td>
								<?=$loans->{$x} ? $loans->{$x}->serial : ""?>
							</td>
						</tr>
				<?php
					endfor;
				?>
				</table>
				<table cellspacing=0 cellpadding=0>
					<tbody>
						<tr>
							<td colspan="4">
							</td>
						</tr>
					</tbody>
				</table>
		<?php
			endif;
		?>
		<!-- TABLEAU TARIFS -->
		<style>
			#costs td{
				border: 0.25mm solid black;
				text-align:	center;
			}
		</style>
		<table style="width:90%" id="costs">
			<tr style="text-align:center">
				<td style="width:25%;border:none">
				</td>
				<td style="width:25%;text-align:center">
					<b>QUANTITE</b>
				</td>
				<td style="width:25%;text-align:center">
					<b>PRIX UNITAIRE</b>
				</td>
				<td style="width:25%;text-align:center">
					<b>TOTAL</b>
				</td>
			</tr>
			<tr>
				<td style="text-align:right">
					<b>MAIN D'OEUVRE (en heure) :</b>
				</td>
				<td>
					<?=$fh?>
				</td>
				<td>
					<?=number_format($_INTER['WORKFORCE']['HOUR_PRICE'], 2, ',', ' ')."€"?>
				</td>
				<td>
					<?=number_format(round(($interval / 3600), 2) * $_INTER['WORKFORCE']['HOUR_PRICE'], 2, ',', ' ')."€"?>
				</td>
			</tr>
			<tr>
				<td style="text-align:right">
					<b>DEPLACEMENT :</b>
				</td>
				<td>
					<?=$_INTER['TRAVEL']['LABEL']?>
				</td>
				<td>
					<?=number_format($_INTER['TRAVEL']['PRICE'], 2, ',', ' ')."€"?>
				</td>
				<td>
					<?=number_format($_INTER['TRAVEL']['PRICE'], 2, ',', ' ')."€"?>
				</td>
			</tr>
			<tr style="background:black">
				<td rowspan="9" style="background:white;border:none">
				</td>
				<td>
				</td>
				<td>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td style="text-align:right">
					<b>TOTAL HT :</b>
				</td>
				<td>
					<?=number_format($_INTER['TRAVEL']['PRICE'] + $_INTER['WORKFORCE']['HOUR_PRICE'], 2, ',', ' ')."€"?>
				</td>
				<td>
					<?=number_format($_INTER['TRAVEL']['PRICE'] + (round(($interval / 3600), 2) * $_INTER['WORKFORCE']['HOUR_PRICE']), 2, ',', ' ')."€"?>
				</td>
			</tr>
			<tr>
				<td style="text-align:right">
					<b>TAUX TVA :</b>
				</td>
				<td>
					<?=number_format($_INTER['VAT_RATE'], 2, ',', ' ')."%"?>
				</td>
				<td>
					<?=number_format($_INTER['VAT_RATE'], 2, ',', ' ')."%"?>
				</td>
			</tr>
			<tr>
				<td style="text-align:right">
					<b>TOTAL TVA :</b>
				</td>
				<td>
					<?=number_format(($_INTER['TRAVEL']['PRICE'] + $_INTER['WORKFORCE']['HOUR_PRICE']) * ($_INTER['VAT_RATE'] / 100), 2, ',', ' ')."€"?>
				</td>
				<td>
					<?=number_format(($_INTER['TRAVEL']['PRICE'] + (round(($interval / 3600), 2) * $_INTER['WORKFORCE']['HOUR_PRICE'])) * ($_INTER['VAT_RATE'] / 100), 2, ',', ' ')."€"?>
				</td>
			</tr>
			<tr>
				<td style="text-align:right">
					<b>TOTAL TTC :</b>
				</td>
				<td>
					<?=number_format($_INTER['TRAVEL']['PRICE'] + $_INTER['WORKFORCE']['HOUR_PRICE'] + (($_INTER['TRAVEL']['PRICE'] + $_INTER['WORKFORCE']['HOUR_PRICE']) * ($_INTER['VAT_RATE'] / 100)), 2, ',', ' ')."€"?>
				</td>
				<td>
					<?=number_format(($_INTER['TRAVEL']['PRICE'] + (round(($interval / 3600), 2) * $_INTER['WORKFORCE']['HOUR_PRICE'])) + (($_INTER['TRAVEL']['PRICE'] + (round(($interval / 3600), 2) * $_INTER['WORKFORCE']['HOUR_PRICE'])) * ($_INTER['VAT_RATE'] / 100)), 2, ',', ' ')."€"?>
				</td>
			</tr>
			<tr>
				<td style="text-align:right">
					<b>CONTRAT DE MAINTENANCE :</b>
				</td>
				<td>
					<?=$_INTER['CUSTOMER']['COUNTRACT'] ? "SOUS CONTRAT" : "HORS CONTRAT"?>
				</td>
				<td>
					<?=$_INTER['CUSTOMER']['COUNTRACT'] ? "SOUS CONTRAT" : "HORS CONTRAT"?>
				</td>
			</tr>
			<?php if(!empty($_INTER['BILLINGS_THIRD'])): ?>
				<tr>
					<td style="border:none">
					</td>
					<td style="text-align:right">
						<b>PROFORMA :</b>
					</td>
					<td>
						<?php
							$total_ht = 0;
							foreach($_INTER['BILLINGS_THIRD'] as $billing)
							{
								$billing['ptht'] = $billing['puht'] * $billing['quantity'];
								$billing['total_discount'] = (100 / $billing['ptht']) * (($billing['nbDiscount'] * $billing['puht']) -(($billing['nbDiscount'] * $billing['puht']) * ($billing['discount'] / 100)) + ($billing['quantity'] - $billing['nbDiscount']) * $billing['puht']);
								$billing['total_ht'] = $billing['ptht'] * ($billing['total_discount'] / 100);
								$total_ht += $billing['total_ht'];
							}
							$total_ttc = $total_ht+($total_ht*($_INTER['VAT_RATE']/100));
							echo number_format($total_ttc, 2, ',', ' ')."€";
						?>
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<td style="border:none">
				</td>
				<td style="text-align:right">
					<b>TOTAL* :</b>
				</td>
				<td>
					<?php
						if($_INTER['CUSTOMER']['COUNTRACT'])
						{
							echo number_format($total_ttc ? $total_ttc : 0, 2, ',', ' ')."€";
						}
						else
						{
							echo $total_ttc ? number_format($total_ttc + (($_INTER['TRAVEL']['PRICE'] + (round(($interval / 3600), 2) * $_INTER['WORKFORCE']['HOUR_PRICE'])) + (($_INTER['TRAVEL']['PRICE'] + (round(($interval / 3600), 2) * $_INTER['WORKFORCE']['HOUR_PRICE'])) * ($_INTER['VAT_RATE'] / 100))), 2, ',', ' ')."€" : number_format(($_INTER['TRAVEL']['PRICE'] + (round(($interval / 3600), 2) * $_INTER['WORKFORCE']['HOUR_PRICE'])) + (($_INTER['TRAVEL']['PRICE'] + (round(($interval / 3600), 2) * $_INTER['WORKFORCE']['HOUR_PRICE'])) * ($_INTER['VAT_RATE'] / 100)), 2, ',', ' ')."€";
						}
					?>
				</td>
			</tr>
			<tr>
				<td style="border:none">
				</td>
				<td colspan="2" style="text-align:right;border:none">
					<i>*Hors facturation tierses. Sous réserve de modifications.</i>
				</td>
			</tr>
		</table>
		<table cellspacing=0 cellpadding=0>
			<tbody>
				<tr>
					<td colspan="4">
					</td>
				</tr>
			</tbody>
		</table>
		<style>
			#software{
				width:	90%;
			}

			#software td{
				border-bottom:0.25mm solid black;
			}
		</style>
		<table cellspacing=0 cellpadding=0 style="border:0.25mm solid black" id="software">
			<tr>
				<td style="width:25%;text-align:right">
					<b>Test de bon fonctionnement :</b>
				</td>
				<td style="border-right:0.25mm solid black;width:25%;text-align:left">
					<?=$_INTER['IS_FUNCTIONAL'] == "-1" ? "Non Concerné" : $_INTER['IS_FUNCTIONAL'] ? "Oui" : "Non"?>
				</td>
				<td style="width:25%;text-align:right">
					<b>Formation à la sauvegarde :</b>
				</td>
				<td style="width:25%;text-align:left">
					<?=$_INTER['TRAINING_BACKUP_PERFORMED'] == "-1" ? "Non Concerné" : $_INTER['TRAINING_BACKUP_PERFORMED'] ? "Oui" : "Non"?>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="border-right:0.25mm solid black;text-align:center">
					Matériel et/ou Logiciel <?=$_INTER['IS_NF525'] ? "conforme" : "non conforme"?> à la loi 2018-1317 du 28 décembre 2018.
				</td>
				<td style="text-align:right">
					<b>Mise à jour réalisée :</b>
				</td>
				<td style="text-align:left">
					<?=$_INTER['IS_UPDATED'] ? "Oui" : "Non"?>
				</td>
			</tr>
		</table>
		<style>
			.signs
			{
				width:	90%;
				margin:	0 auto;
				text-align: center;
			}

			.signs td
			{
				width:	50%;
			}

		</style>
		<div style="position:fixed;bottom:0">
			<table cellspacing=0 cellpadding=0 class="signs">
				<tr>
					<td>
						<p>Nom et signature du technicien</p>
					</td>
					<td>
						<p>Nom, Qualité et Signature précédée de la mention "Lu et pprouvé"</p>
					</td>
				</tr>
				<tr>
					<td>
						<p><?=$_INTER['TECHNICIAN']['NAME']?></p>
					</td>
					<td>
						<p>Lu et approuvé <?=$_INTER['CUSTOMER']['CIVILITY']." ".$_INTER['CUSTOMER']['NAME'].", ".$_INTER['CUSTOMER']['QUALITY']?></p>
					</td>
				</tr>
			</table>
			<table cellspacing=0 cellpadding=0 class="signs">
				<tr>
					<td>
						<img src="<?=$_INTER['TECHNICIAN']['SIGN']?>" />
					</td>
					<td>
						<img src="<?=$_INTER['CUSTOMER']['SIGN']?>" />
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>

<?php
	$html['VOUCHER'] = ob_get_clean();
	if(!empty($_INTER['BILLINGS_THIRD']) && $_INTER['BILLINGS_THIRD'] != '{}'):
		ob_start();
?>
		<html>
			<body>
				<!--mpdf
					<htmlpageheader name="myheader">
						<table cellspacing=0 cellpadding=0 border=0 style="width:100%">
							<tr>
								<td>
									<img src="<?=$_ESTABLISHMENT['LOGO']?>" width="209px" height="88px" alt="logo_tacteo">
								</td>
								<td style="font-weight:bold">
									<?=$_ESTABLISHMENT['SOCIAL_REASON']?><br />
									<?=$_INTER['AGENCY']['ADDRESS_LINE1']?><br />
									<?=$_INTER['AGENCY']['ADDRESS_LINE2'] ? $_INTER['AGENCY']['ADDRESS_LINE2']."<br />" : ""?>
									<?=$_INTER['AGENCY']['POSTAL_CODE']." ".$_INTER['AGENCY']['CITY']?><br />
									Tél <?=$_INTER['AGENCY']['PHONE']?><br />
								</td>
								<td style="text-align:right;font-weight:bold">
									Agence de :<br />
									Date d'intervention :<br/>
									BON D'INTERVENTION N°<br/>
									CODE CLIENT :
								</td>
								<td>
									<?=$_INTER['AGENCY']['CITY']?><br />
									<?=strftime("%d/%m/%Y à %H:%M:%S",strtotime($_INTER['DATE_END']))?><br />
									<?=$_INTER['ID']?><br />
									<?=$_INTER['CUSTOMER']['CODE']?><br />
								</td>
							</tr>
						</table>
					</htmlpageheader>
					<htmlpagefooter name="myfooter">
						<div style="border-top: 0.25mm solid #000000; font-size: 5pt; text-align: center; padding-top: 3mm; ">
							<?=$_ESTABLISHMENT['END_TEXT']?><br /><br />
							<span style="font-size:8pt">Proforma</span><br />
							<span style="font-size:8pt">{PAGENO} / {nbpg}</span>
						</div>
					</htmlpagefooter>
					<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
					<sethtmlpagefooter name="myfooter" value="on" />
				mpdf-->
				<style>
					#billing_third{
						table-layout:		fixed;
						border-collapse:	collapse;
						width:				100%;
						margin:				0 auto;
						vertical-align:		middle;
						height:				100%;
						font-size:			16px;
					}

					#billing_third td, #billing_third th{
						padding:			0 2px;
						border:				1PX solid black;
						width:				auto;
						height:				16px;
						white-space:		nowrap;
						padding:			5px;
					}
				</style>
				<table cellpadding=0 cellspacing=0 border="1" width="100%" id="billing_third">
					<thead>
						<tr>
							<th colspan="7" style="height:32px;width:100%">
								<h1>PROFORMA N°<?=$_INTER['ID'].'-'.date('Y')?></h1>
							</th>
						</tr>
						<tr>
							<th><h3>QTY</h3></th>
							<th><h3>CODE</h3></th>
							<th><h3>LIBELE</h3></th>
							<th><h3>PUHT</h3></th>
							<th><h3>PTHT</h3></th>
							<th><h3>REMISE</h3></th>
							<th><h3>TOTAL HT</h3></th>
						</tr>
					</thead>
					<tbody>
					<?php
						$total_ht = 0;
						foreach($_INTER['BILLINGS_THIRD'] as $billing):
							$billing['ptht'] = $billing['puht'] * $billing['quantity'];
							$billing['total_discount'] = (100 / $billing['ptht']) * (($billing['nbDiscount'] * $billing['puht']) -(($billing['nbDiscount'] * $billing['puht']) * ($billing['discount'] / 100)) + ($billing['quantity'] - $billing['nbDiscount']) * $billing['puht']);
							$billing['total_ht'] = $billing['ptht'] * ($billing['total_discount'] / 100);
							$total_ht += $billing['total_ht'];
					?>
							<tr>
								<td style="text-align:center"><p><?=$billing['quantity']?></p></td>
								<td style="text-align:center"><p><?=$billing['id']?></p></td>
								<td><p><?=$billing['name']?></p></td>
								<td style="text-align:right"><p><?=number_format($billing['puht'],2,',',' ').' €'?></p></td>
								<td style="text-align:right"><p><?=number_format($billing['ptht'],2,',',' ').' €'?></p></td>
								<td style="text-align:right"><p><?=(100 - $billing['total_discount']) == 100 ? 'OFFERT' : number_format ((100 - $billing['total_discount']),2,',',' ').' %'?></p></td>
								<td style="text-align:right"><p><?=number_format($billing['total_ht'],2,',',' ').' €'?></p></td>
							</tr>
					<?php
						endforeach;
						$total_vat = $total_ht*($_INTER['VAT_RATE']/100);
						$total_ttc = $total_vat + $total_ht;
						for($x; $x < 40; $x++):
					?>
						<tr>
							<td style="height:26px;"></td><td></td><td></td><td></td><td></td><td></td><td></td>
						</tr>
					<?php
						endfor;
					?>
						<tr>
							<td colspan="5" style="border:none"></td>
							<td style="text-align:right"><b>TOTAL HT</b></td>
							<td style="text-align:right"><?=number_format($total_ht,2,',',' ').' €'?></td>
						</tr>
						<tr>
							<td colspan="5" style="border:none"></td>
							<td style="text-align:right"><b>TVA (<?=number_format($_INTER['VAT_RATE'],2,',',' ').' %'?>)</b></td>
							<td style="text-align:right"><?=number_format($total_vat,2,',',' ').' €'?></td>
						</tr>
						<tr>
							<td colspan="5" style="border:none"></td>
							<td style="text-align:right"><b>TOTAL TTC</b></td>
							<td style="text-align:right"><?=number_format($total_ttc,2,',',' ').' €'?></td>
						</tr>
					</tbody>
				</table>
			</body>
		</html>
<?php
		$html['BILLINGS_THIRD'] = ob_get_clean();
	endif;
	if(!empty($_INTER['ATTACHMENTS']) && $_INTER['ATTACHMENTS'] != '{}'):
		ob_start();
?>
	<html>
		<body>
			<!--mpdf
				<htmlpageheader name="myheader">
					<table cellspacing=0 cellpadding=0 border=0 style="width:100%">
						<tr>
							<td>
								<img src="<?=$_ESTABLISHMENT['LOGO']?>" width="209px" height="88px" alt="logo_tacteo">
							</td>
							<td style="font-weight:bold">
								<?=$_ESTABLISHMENT['SOCIAL_REASON']?><br />
								<?=$_INTER['AGENCY']['ADDRESS_LINE1']?><br />
								<?=$_INTER['AGENCY']['ADDRESS_LINE2'] ? $_INTER['AGENCY']['ADDRESS_LINE2']."<br />" : ""?>
								<?=$_INTER['AGENCY']['POSTAL_CODE']." ".$_INTER['AGENCY']['CITY']?><br />
								Tél <?=$_INTER['AGENCY']['PHONE']?><br />
							</td>
							<td style="text-align:right;font-weight:bold">
								Agence de :<br />
								Date d'intervention :<br/>
								BON D'INTERVENTION N°<br/>
								CODE CLIENT :
							</td>
							<td>
								<?=$_INTER['AGENCY']['CITY']?><br />
								<?=strftime("%d/%m/%Y à %H:%M:%S",strtotime($_INTER['DATE_END']))?><br />
								<?=$_INTER['ID']?><br />
								<?=$_INTER['CUSTOMER']['CODE']?><br />
							</td>
						</tr>
					</table>
				</htmlpageheader>
				<htmlpagefooter name="myfooter">
					<div style="border-top: 0.25mm solid #000000; font-size: 5pt; text-align: center; padding-top: 3mm; ">
						<?=$_ESTABLISHMENT['END_TEXT']?><br /><br />
						<span style="font-size:8pt">Pièce(s) Jointe(s)</span><br />
						<span style="font-size:8pt">{PAGENO} / {nbpg}</span>
					</div>
				</htmlpagefooter>
				<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
				<sethtmlpagefooter name="myfooter" value="on" />
			mpdf-->

			<style>
				.attachment
				{
					width:		100%;
					text-align:	center;
					font-size:	14px;
					margin:		0 auto;
				}

				.attachment td
				{
					padding:	5px;
				}
			</style>
			<?php
				$attachments = JSON_decode($_INTER['ATTACHMENTS']);
				$total_attachments = count((array) $attachments);
				$x = 0;
				foreach($attachments as $attachment):
					$x++;
			?>
					<table class="attachment">
						<tr>
							<td>
								<img src="<?=$attachment->attachment?>" width="50%" />
							</td>
						</tr>
						<tr>
							<td>
								<h1>Intervention n°<?=$_INTER['ID'].' | '.$_INTER['CUSTOMER']['CODE'].' '.$_INTER['CUSTOMER']['LABEL']?></h1>
							</td>
						</tr>
						<tr>
							<td style="">
								<?=$attachment->description?>
							</td>
						</tr>
					</table>
			<?php
				if($x != $total_attachments)
				{
					echo '<pagebreak>';
				}
				endforeach;
			?>
		</body>
	</html>
<?php
		$html['ATTACHMENTS'] = ob_get_clean();
	endif;
?>
