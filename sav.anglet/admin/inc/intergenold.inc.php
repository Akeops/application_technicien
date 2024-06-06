<style>
	<?php
		require_once('config.inc.php');

		if(!isset($inter))
		{
			die();
		}
		
		$req = $bdd->query("SELECT * FROM ETABLISSEMENT");
		$establishment = $req->fetch();
		
		$req = $bdd->prepare("SELECT * FROM AGENCES WHERE ID = :id");
		$req->execute(array('id' => $inter['AGENCY']));
		$agency = $req->fetch(2);
		
		$req = $bdd->prepare("SELECT * FROM USERS_INTER WHERE ID = :id");
		$req->execute(array
			(
				'id' => $inter['USER_UPLOAD']
			)
		);
		$user = $req->fetch();
		
		$req = $bdd->query("SELECT * FROM TARIFS");
		$tarif = $req->fetchAll(2);
		
		if($_GET['pdf'])
		{
			$path = "tmp/";

			$image_parts = explode(";base64,", $inter['CUSTOMER_SIGN']);
			$image_type_aux = explode("image/", $image_parts[0]);
			$image_type = $image_type_aux[1];
			$image_en_base64 = base64_decode($image_parts[1]);
			$customer_sign = $path . uniqid() . '.png';

			file_put_contents($customer_sign, $image_en_base64);
			
			$image_parts = explode(";base64,", $user['SIGN']);
			$image_type_aux = explode("image/", $image_parts[0]);
			$image_type = $image_type_aux[1];
			$image_en_base64 = base64_decode($image_parts[1]);
			$user_upload_sign = $path . uniqid() . '.png';
			
			file_put_contents($user_upload_sign, $image_en_base64);
			
			$image_parts = explode(";base64,", $establishment['LOGO']);
			$image_type_aux = explode("image/", $image_parts[0]);
			$image_type = $image_type_aux[1];
			$image_en_base64 = base64_decode($image_parts[1]);
			$establishmentLogo = $path . uniqid() . '.png';
			
			file_put_contents($establishmentLogo, $image_en_base64);
		}
		else
		{
			$customer_sign =		$inter['CUSTOMER_SIGN'];
			$user_upload_sign =		$user['SIGN'];
			$establishmentLogo =	$establishment['LOGO'];
			echo "
				.hightlight{
					color:			blue;
					font-weight:	bold;
				}
			";
		}
	?>

	:root{
		--td-height:	24px;
	}

	body{
		margin:				0;
		background:			white;
		font-family:		Cabin Condensed;
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

<page backtop="57.5mm" backbottom="57.5mm" backleft="10mm" backright="10mm">
	<page_header>
		<table cellspacing="0" style="margin: 10vw auto 0;">
			<tr>
				<td rowspan="6" class="acenter" style="height:calc(24px*6)">
					<img src="<?=$establishmentLogo?>" width="209px" height="88px" alt="logo_tacteo">
				</td>
				<td rowspan="6">
					<b><?=$establishment['SOCIAL_REASON']?><br>
					<?=$agency['ADDRESS_LINE1']?><br>
					<?=$agency['ADDRESS_LINE2'] ? $agency['ADDRESS_LINE2']."<br>" : ""?>
					<?=$agency['POSTAL_CODE']." ".$agency['CITY']?><br>
					Tél <?=$agency['PHONE']?></b>
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
					<?=$agency['NAME']?>
				</td>
			</tr>
			<tr>
				<td class="aright">
					<b>Date d'intervention :</b>
				</td>
				<td class="hightlight">
					<?php
						list($date, $hour) = explode(" ", $inter['DATE_END']);
						$date = explode("-", $date);
						echo $date[2]."/".$date[1]."/".$date[0]." à ".$hour;
					?>
				</td>
			</tr>
			<tr>
				<td class="aright">
					<b>BON D'INTERVENTION N°</b>
				</td>
				<td class="hightlight">
					<?php echo($inter['ID']);?>
				</td>
			</tr>
			<tr>
				<td class="aright">
					<b><span >CODE CLIENT :</span></b>
				</td>
				<td class="hightlight">
					<?php echo($inter['CUSTOMER_CODE']);?>
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
					<?php echo($inter['CUSTOMER_LABEL']);?>
				</td>
				<td class="hightlight bleft bbottom aright">
					<?php echo($inter['CUSTOMER_PHONE']."&nbsp;");?>
				</td>
				<td class="hightlight bright bbottom">
					<?php echo("&nbsp;".$inter['CUSTOMER_EMAIL']);?>
				</td>
				<td rowspan="5" class="hightlight bright bbottom aright">
					<?php
						if($inter['INSTALL'])
						{
							echo("INSTALLATION<br>");
						}
						if($inter['MAINTENANCE'])
						{
							echo("MAINTENANCE<br>");
						}
						if($inter['TRAINING'])
						{
							echo("FORMATION<br>");
						}
						if($inter['RENEWAL'])
						{
							echo("RENOUVELLEMENT<br>");
						}
						if($inter['RECOVERY'])
						{
							echo("RECUPERATION MATERIELS<br>");
						}
						if($inter['DELIVERY'])
						{
							echo("LIVRAISON");
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
						list($date, $hour) = explode(" ", $inter['DATE_START']);
						$date = explode("-", $date);
						echo "&nbsp;".$date[2]."/".$date[1]."/".$date[0]." à ".$hour;
					?>
				</td>
			</tr>
			<tr>
				<td rowspan="3" class="hightlight bleft bbottom">
					<?=$inter['CUSTOMER_ADDRESS_LINE1']?><br>
					<?=(!empty($inter['CUSTOMER_ADDRESS_LINE2'])) ? $inter['CUSTOMER_ADDRESS_LINE2']."<br>" : ""?>
					<?=$inter['CUSTOMER_ADDRESS_POSTAL_CODE']?> <?=$inter['CUSTOMER_ADDRESS_CITY']?><br>
				</td>
				<td class="bleft bbottom aright">
					<b><span >HEURE DE DEPART :</span></b>
				</td>
				<td class="hightlight bleft bbottom bright">
					<?php
						list($date, $hour) = explode(" ", $inter['DATE_END']);
						$date = explode("-", $date);
						echo "&nbsp;".$date[2]."/".$date[1]."/".$date[0]." à ".$hour;
					?>
				</td>
			</tr>
			<tr>
				<td class="bleft bbottom aright">
					<b>TOTAL D'HEURES :</b>
				</td>
				<td class="hightlight bleft bbottom bright">
					<?php
						$dateStart = new DateTime($inter['DATE_START']);
						$dateEnd = new DateTime($inter['DATE_END']);
						$dateStart = $dateStart->getTimeStamp();
						$dateEnd = $dateEnd->getTimeStamp();
						$interval = $dateEnd - $dateStart;
						$hours = floor($interval / 3600);
						$minutes = round(($interval - ($hours * 3600)) / 60);
						$fh = "&nbsp;".substr('0'.$hours, -2)."h ".substr('0'.$minutes, -2)."mn";
						echo $fh;
					?>
				</td>
			</tr>
			<tr>
				<td class="bleft bbottom aright">
					<b>SOIT :</b>
				</td>
				<td class="hightlight bleft bbottom bright">
					<?php
						$c = $interval / 3600;
						echo "&nbsp;".round($c,2)." heure(s)";
					?>
				</td>
			</tr>
		</table>
	</page_header>
	<table cellspacing="0" style="margin: 0 auto;">
		<tbody>
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
				
				$temp = explode("\n", $inter['DESCRIPTION']);
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
	
	<style>
		#desc{
			margin: 10vw auto;
			width:	100%;
			border:	solid 2px black;
		}
		
		#desc tr{
			height:	20px;
		}
		
		#desc td{
			width:	50%;
		}
	</style>

	<table id="desc" cellspacing="0">
		<tr>
			<td>
				<b><span>DESCRIPTION DE L'INTERVENTION :</span></b>
			</td>
			<td style="border-left: 1px lightgrey">
			</td>
		</tr>
	<?php
		for($x = 0; $x < ($maxLines - 1) && $x < sizeOf($desc); $x++):
	?>
		<tr>
			<td>
				<span class="hightlight">
					<?=$desc[$x]?>
				</span>
			</td>
			<td style="border-left: 1px lightgrey">
				<span class="hightlight">
					<?=$desc[$x+$maxLines]?>
				</span>
			</td>
		</tr>
		<?php endfor; ?>
	</table>
	
	<table cellspacing="0" style="margin: 0 auto;">
		<tbody>
			<?php
				if(!empty($inter['LOANS']) && $inter['LOANS'] != "null"):
				$loans = json_decode($inter['LOANS']);
			?>
				<tr>
					<td colspan="4">
					</td>
				</tr>
				<tr>
					<td class="bleft btop bbottom bright" colspan="4">
						<b>Matériel(s) de prêt :</b>
					</td>
				</tr>
				<?php
					for($x = 0; $x < count((array)$loans); $x++):
					$maxChar = 20;
				?>
					<tr>
						<td class="hightlight bbottom bleft aright" style="padding-right:5px; border-right:1px dashed;white-space:nowrap">
							<span>
								<?=(strlen($loans->$x->name) > $maxChar) ? substr($loans->$x->name, 0, $maxChar)." ..." : substr($loans->$x->name, 0, $maxChar)?>
							</span>
						</td>
						<td class="hightlight bbottom bright" style="white-space:nowrap">
							<span>
								<?=(strlen($loans->$x->serial) > $maxChar) ? substr($loans->$x->serial, 0, $maxChar)." ..." : substr($loans->$x->serial, 0, $maxChar);$x++;?>
							</span>
						</td>
						<td class="hightlight bbottom bleft aright" style="padding-right:5px; border-right:1px dashed;white-space:nowrap">
							<span>
								<?=(strlen($loans->$x->name) > $maxChar) ? substr($loans->$x->name, 0, $maxChar)." ..." : substr($loans->$x->name, 0, $maxChar)?>
							</span>
						</td>
						<td class="hightlight bbottom bright">
							<span>
								<?=(strlen($loans->$x->serial) > $maxChar) ? substr($loans->$x->serial, 0, $maxChar)." ..." : substr($loans->$x->serial, 0, $maxChar)?>
							</span>
						</td>
					</tr>
				<?php endfor; ?>
			<?php endif; ?>
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
					<?php echo number_format($inter['WORKFORCE_HOUR_PRICE'],2,","," ") . "€"?>
				</td>
				<td class="hightlight bleft btop bright acenter">
					<?php
						$mo = $c * $inter['WORKFORCE_HOUR_PRICE'];
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
						echo $inter['TRAVEL_LABEL'];
						$depl = $inter['TRAVEL_PRICE'];
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
					<?php echo number_format($inter['VAT_RATE'],2,","," ")."%"?>
				</td>
				<td class="hightlight bleft btop bright acenter">
					<?php echo number_format($inter['VAT_RATE'],2,","," ")."%"?>
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
						$tvau = ($htu / 100) * $inter['VAT_RATE'];
						echo(number_format(round($tvau, 2),2,","," ") . "€");
					?>
				</td>
				<td class="hightlight bleft btop bright acenter">
					<?php
						$tvat = ($htt / 100) * $inter['VAT_RATE'];
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
					<span class='updated'>
					<?php
						if($inter['CUSTOMER_COUNTRACT'])
						{
							$contract = "INCLUS";
						}
						else
						{
							$contract = "NON INCLUS";
						}
						echo $contract;
					?>
					</span>
				</td>
				<td class="hightlight bleft bbottom bright acenter">
					<span class='updated'>
					<?php
						echo $contract;
					?>
					</span>
				</td>
			</tr>
			<?php
				$totalHT = 0;
				$billings = json_decode($inter['BILLINGS_THIRD']);
				if(!empty($billings->{'0'})):
			?>
			<tr>
				<td colspan="2">
				</td>
				<td class="bleft aright bbottom">
					<b>PROFORMA :</b>
				</td>
				<td class="hightlight bleft bbottom bright acenter">
					<?php
						$billings = json_decode($inter['BILLINGS_THIRD']);
						for($x = 0; $x < count((array)$billings); $x++)
						{
							$discount = (($billings->$x->nbDiscount * $billings->$x->puht) / 100) * $billings->$x->discount;
							$totalHT = $totalHT + $billings->$x->quantity * $billings->$x->puht - $discount;
						}
						$proformaTTC = $totalHT/100 * $inter['VAT_RATE'] + $totalHT;
						echo number_format($proformaTTC,2,","," ")."€";
					?>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<td colspan="2">
				</td>
				<td class="bleft aright">
					<b>TOTAL* :</b>
				</td>
				<td class="hightlight bleft bright acenter">
					<?php
						if($inter['CUSTOMER_COUNTRACT'])
						{
							echo number_format($proformaTTC, 2,","," ")."€";
						}
						else
						{
							echo number_format($proformaTTC + $ttct, 2,","," ")."€";
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
				<td	colspan="4">
				</td>
			</tr>
			<tr>
				<td class="btop bleft aright">
					<b>Test de bon fonctionnement :</b>
				</td>
				<td class="hightlight btop">
					<span ><?php
						if($inter['IS_FUNCTIONAL'])
						{
							echo("Oui");
						}
						else if($inter['IS_FUNCTIONAL'] == 0)
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
						if($inter['TRAINING_BACKUP_PERFORMED'])
						{
							echo("Oui");
						}
						else if($inter['TRAINING_BACKUP_PERFORMED'] == 0)
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
				<td colspan="2" class="hightlight acenter btop bbottom bleft vmiddle">
					<span ><?php
						if($inter['IS_NF525'])
						{
							echo("Matériel et/ou Logiciel conforme à la loi 2018-1317 du 28 décembre 2018.");
						}
						else
						{
							echo("Matériel et/ou Logiciel non conforme à la loi 2018-1317 du 28 décembre 2018.");
						}	
					?></span>
				</td>
				<td class="hightlight bleft aright btop bbottom">
					<?php
						if($inter['IS_NF525'] && !empty($inter['SOFTWARE_VERSION']))
						{
							echo("<b>Version :</b>");
						}
						else
						{
							echo("<b>Mise à jour réalisée :</b>");
						}
					?>
				</td>
				<td class="hightlight bright btop bbottom">
					<?php
						if($inter['IS_NF525'] && !empty($inter['SOFTWARE_VERSION']))
						{
							echo($inter['SOFTWARE_VERSION']);
						}
						else if($inter['IS_UPDATED'])
						{						
							echo("Oui");
						}
						else if($inter['IS_UPDATED'] == "-1")
						{
							echo("Non Concerné");
						}
						else
						{
							echo("Non");
						}
					?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
				</td>
				<?php if($inter['IS_UPDATED'] && !empty($inter['SOFTWARE_VERSION'])): ?>
					<td class="hightlight bleft bbottom aright">
						<b>Mise à jour réalisée :</b>
					</td>
				<?php endif; ?>
				<?php if($inter['IS_UPDATED'] && !empty($inter['SOFTWARE_VERSION'])): ?>
					<td class="hightlight bright bbottom">
						Oui
					</td>
				<?php endif; ?>
			</tr>
			<tr>
				<td colspan="4">
				</td>
			</tr>
		</tbody>
	</table>
	<page_footer>
		<table cellspacing="0" id="table-signs">
			<tr>
				<td class="acenter vmiddle">
					<span>Nom et signature du technicien</span>
				</td>
				<td class="acenter vmiddle">
					<span>Nom, Qualité et Signature précédée de la mention "Lu et pprouvé"</span>
				</td>
			</tr>
			<tr>
				<td class="">
				</td>
				<td class="">
					Lu et approuvé,
				</td>
			</tr>
			<tr>
				<td class="hightlight acenter">
					<span><?=$user['NAME']?></span>
				</td>
				<td class="hightlight acenter">
					<span><?=$inter['CUSTOMER_CIVILITY']." ".$inter['CUSTOMER_NAME'].", ".$inter['CUSTOMER_QUALITY']?></span>
				</td>
			</tr>
			<tr style="height:calc(24px*8)">
			<?php if($_GET['pdf']): ?>
				<td class="acenter">
					<img src="<?=$user_upload_sign?>" alt="SIGNATURE TECHNICIEN" style="width:375px;height:158px;">
				</td>
				<td class="acenter">
					<img src="<?=$customer_sign?>" alt="SIGNATURE CLIENT" style="width:375px;height:158px;">
				</td>
			<?php else: ?>
				<td class="acenter">
					<span style="color:red">Signature Active à la génration du pdf.</span>
				</td>
				<td class="acenter">
					<span style="color:red">Signature Active à la génration du pdf.</span>
				</td>
			<?php endif; ?>
			</tr>
			<tr>
				<td>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="font-size:10px;" class="acenter">
					<?=str_replace("\n", "<br />", $establishment['END_TEXT'])?>
				</td>
			</tr>
		</table>
	</page_footer>
</page>

<?php
	$billings = json_decode($inter['BILLINGS_THIRD']);
	
	if(!empty($billings->{'0'})):
?>

<page backtop="57.5mm" backbottom="5mm" backleft="10mm" backright="10mm">
	<page_header>
		<table cellspacing="0" style="margin: 10vw auto 0;">
			<tr>
				<td rowspan="6" class="acenter" style="height:calc(24px*6)">
					<img src="<?=$establishmentLogo?>" width="209px" height="88px" alt="logo_tacteo">
				</td>
				<td rowspan="6">
					<b><?=$establishment['SOCIAL_REASON']?><br>
					<?=$agency['ADDRESS_LINE1']?><br>
					<?=$agency['ADDRESS_LINE2'] ? $agency['ADDRESS_LINE2']."<br>" : ""?>
					<?=$agency['POSTAL_CODE']." ".$agency['CITY']?><br>
					Tél <?=$agency['PHONE']?></b>
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
					<?=$agency['NAME']?>
				</td>
			</tr>
			<tr>
				<td class="aright">
					<b>Date d'intervention :</b>
				</td>
				<td class="highlight">
					<?php
						list($date, $hour) = explode(" ", $inter['DATE_END']);
						$date = explode("-", $date);
						echo $date[2]."/".$date[1]."/".$date[0]." à ".$hour;
						
					?>
				</td>
			</tr>
			<tr>
				<td class="aright">
					<b>PROFORMA N°</b>
				</td>
				<td class="highlight">
					<?=$inter['ID']."-".explode("-",explode(" ",$inter['DATE_END'])[0])[0]?>
				</td>
			</tr>
			<tr>
				<td class="aright">
					<b><span >CODE CLIENT :</span></b>
				</td>
				<td class="highlight">
					<?php echo($inter['CUSTOMER_CODE']);?>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
					<b><?=$inter['CUSTOMER_LABEL']?></b>
				</td>
				<td class="aright">
					<b><span >TELEPHONE :</span></b>
				</td>
				<td>
					<?php echo("&nbsp;".$inter['CUSTOMER_PHONE']);?>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
					<b><?=$inter['CUSTOMER_ADDRESS_LINE1']?></b>
				</td>
				<td class="aright">
					<b><span>EMAIL :</span></b>
				</td>
				<td>
					<?php echo("&nbsp;".$inter['CUSTOMER_EMAIL']);?>
				</td>
			</tr>
			<?php if($inter['CUSTOMER_ADDRESS_LINE2'] != "" && $inter['CUSTOMER_ADDRESS_LINE2'] != "null"):?>
				<tr>
					<td>
					</td>
					<td>
						<b><?=$inter['CUSTOMER_ADDRESS_LINE2']?></b>
					</td>
					<td>
					</td>
					<td>
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<td>
				</td>
				<td>
					<b><?=$inter['CUSTOMER_ADDRESS_POSTAL_CODE']?> <?=$inter['CUSTOMER_ADDRESS_CITY']?></b>
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
		</table>
	</page_header>
	<table cellspacing="0" id="proforma-table" style="margin: 0 auto">
		<tr>
			<td colspan="7" class="btop bleft bright" style="width:100%">
				<h1>PROFORMA N°<?=$inter['ID']."-".explode("-",explode(" ",$inter['DATE_END'])[0])[0]?></h1>
			</td>
		</tr>
		<tr>
			<td class="btop bleft acenter">
				<b>QTY</b>
			</td>
			<td class="btop bleft acenter">
				<b>CODE</b>
			</td>
			<td class="btop bleft acenter">
				<b>LIBELLE</b>
			</td>
			<td class="btop bleft acenter">
				<b>PUHT</b>
			</td>
			<td class="btop bleft acenter">
				<b>PTHT</b>
			</td>
			<td class="btop bleft acenter">
				<b>REMSE</b>
			</td>
			<td class="btop bleft bright acenter">
				<b>TOTAL HT</b>
			</td>
		</tr>
		<?php
			$billings = json_decode($inter['BILLINGS_THIRD']);
			$discount = 0;
			$totalHT = 0;
			for($x = 0; $x < count((array)$billings); $x++):
		?>
			<tr>
				<td class="btop bleft aright">
					<?=$billings->$x->quantity?>
				</td>
				<td class="btop bleft aright">
					<?=$billings->$x->id?>
				</td>
				<td class="btop bleft">
					<?=$billings->$x->name?>
				</td>
				<td class="btop bleft aright">
					<?=number_format($billings->$x->puht,2,","," ")."€"?>
				</td>
				<td class="btop bleft aright">
					<?=number_format($billings->$x->quantity*$billings->$x->puht,2,","," ")."€"?>
				</td>
				<td class="btop bleft aright">
					<?php
						if($billings->$x->puht > 0)
						{
							$discount = (($billings->$x->nbDiscount * $billings->$x->puht) / 100) * $billings->$x->discount;
							$percentage = (100 / ($billings->$x->quantity * $billings->$x->puht)) * $discount;
						}
						else
						{
							$discount = 0;
							$percentage = 0;
						}
						
						if($percentage == 100)
						{
							echo "OFFERT";
						}
						else
						{
							echo number_format($percentage, 2, ","," ")."%";
						}
					?>
				</td>
				<td class="btop bleft bright aright">
					<?=number_format($billings->$x->quantity * $billings->$x->puht - $discount,2,","," ")."€"?>
				</td>
			</tr>
		<?php
			$totalHT = $totalHT + $billings->$x->quantity * $billings->$x->puht - $discount;
			endfor;
			for($x = 30 - count((array)$billings); $x > 0; $x--):
		?>
			<tr>
				<td class="btop bleft aright">
				</td>
				<td class="btop bleft aright">
				</td>
				<td class="btop bleft">
				</td>
				<td class="btop bleft aright">
				</td>
				<td class="btop bleft aright">
				</td>
				<td class="btop bleft aright">
				</td>
				<td class="btop bleft bright aright">
				</td>
			</tr>
		<?php
			endfor;
		?>
		<tr>
			<td colspan="5" class="btop">
			</td>
			<td class="btop bleft aright">
				<b>TOTAL HT</b>
			</td>
			<td class="btop bleft bright aright">
				<?=number_format($totalHT,2,","," ")."€"?>
			</td>
		</tr>
		<tr>
			<td colspan="5">
			</td>
			<td class="btop bleft aright">
				<b>TVA (<?=$inter['VAT_RATE']?>%)</b>
			</td>
			<td class="btop bleft bright aright">
				<?=number_format($totalHT / 100 * $inter['VAT_RATE'],2,","," ")."€"?>
			</td>
		</tr>
		<tr>
			<td colspan="5">
			</td>
			<td class="btop bleft aright bbottom">
				<b>TOTAL TTC</b>
			</td>
			<td class="btop bleft bright aright bbottom">
				<?=number_format($totalHT / 100 * $inter['VAT_RATE'] + $totalHT,2,","," ")."€"?>
			</td>
		</tr>
	</table>
	<page_footer>
		<span style="display: block;text-align:center;font-size:10px;"><p><?=str_replace("\n", "<br />", $establishment['END_TEXT'])?></p></span>
	</page_footer>
</page>

<?php
	endif;
	if(!empty($inter['ATTACHMENTS']) && $inter['ATTACHMENTS'] != "null"):
		$attachments = json_decode(str_replace("\\\\\\\\n","<br />",$inter['ATTACHMENTS']));
		for($x = 0; $x < count((array)$attachments); $x++):
			if($_GET['pdf'])
			{
				$path = "tmp/";

				$image_parts = explode(";base64,", $attachments->$x->attachment);
				$image_type_aux = explode("image/", $image_parts[0]);
				$image_type = $image_type_aux[1];
				$image_en_base64 = base64_decode($image_parts[1]);
				$attachment = $path . uniqid() . '.png';
				
				file_put_contents($attachment, $image_en_base64);
			}
			else
			{
				$attachment = $attachments->$x->attachment;
			}
?>

<!-- PIECES JOINTES -->
<page backtop="30mm" backbottom="5mm" backleft="10mm" backright="10mm">
	<page_header>
		<h1>Pièce Jointe N°<?=$x+1?></h1>
	</page_header>
	<table class="table-attachment" cellspacing="0">
		<tr>
			<td style="height:50%;text-align:center;">
				<img src="<?=$attachment?>" style="max-width:100%;max-height:100%;">
			</td>
		</tr>
		<tr>
			<td>
				<h1 style="text-align:center">Intervention n°<?=$inter['ID']?> | <?=$inter['CUSTOMER_CODE']." ".$inter['CUSTOMER_LABEL']?></h1>
				<h2 style="font-weight:normal"><?=$attachments->$x->description?></h2>
			</td>
		</tr>
	</table>
</page>

<?php
		endfor;
	endif;
?>

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
	
	.table-attachment{
		width:			100%;
	}
	
	.table-attachment td{
		width:			100%;
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
	
	#proforma-table{
		width:				80vw;
	}
	
	#proforma-table td{
		width:				unset;
		height:				22px;
	}
	
	#table-signs{
		width:				100%;
	}
	
	#table-signs td{
		width:				50%;
	}
</style>