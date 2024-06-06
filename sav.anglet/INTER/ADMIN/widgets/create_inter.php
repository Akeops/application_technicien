<?php
	require('../inc/accounts.inc.php');
	$req = $bdd->query("SELECT * FROM CLIENTS");
?>

<!DOCTYPE html>

<html>
	<?php
		require('../inc/head.inc.php');
	?>

	<body>
		<?php
			if(empty($_POST['CODE'])):
		?>
		<form method="post" action="./create_inter.php" id="selection_client">
			<table style="border:solid black 1px;width:100%">
				<thead>
					<tr style="background:yellow">
						<th colspan="6">
							NOUVELLE INTERVENTION : SELECTIONNEZ CLIENT
						</th>
					</tr>
				</thead>
				<tbody id="clients">
					<tr style="background:yellow">
						<td colspan="6">
							<input type="search" value="" placeholder="Rechercher..." id="searching" onkeyup="SEARCH()" onkeydown="SEARCH()">
						</td>
					</tr>
					<tr style="background:yellow; font-weight:bold; text-align:center;">
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
					</tr>
					<?php
						while($client = $req->fetch()):
					?>

						<tr class="CLIENT" onclick="SELECTED(<?php echo $client['CODE']; ?>)">
							<td class="code">
								<?php echo $client['CODE']; ?>
							</td>
							<td class="name">
								<?php echo $client['Nom']; ?>
							</td>
							<td class="telephone">
								<?php echo $client['TELEPHONE1']; ?>
							</td>
							<td class="mail">
								<?php echo $client['EMAIL']; ?>
							</td>
							<td>
								<?php echo $client['CONTRAT_DU']; ?>
							</td>
							<td>
								<?php echo $client['CONTRAT_AU']; ?>
							</td>
						</tr>

					<?php
						endwhile;
					?>
				</tbody>
			</table>
			<input type="hidden" name="CODE">
		</form>

		<script>
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
					if(input.value != ""){
						a = search[i];
						txtValue = a.textContent + code[i].textContent.toUpperCase() + telephone[i].textContent.toUpperCase() + mail[i].textContent.toUpperCase();
						if (txtValue.toUpperCase().indexOf(filter) > -1) {
							option[i].style.display = "";
						} else {
							option[i].style.display = "none";
						}
					}
					else{
						option[i].style.display = "none";
					}
				}
			}

			function SELECTED(code){

				var input = document.getElementsByName("CODE")[0].value = code;
				var form = document.getElementById("selection_client");

				form.submit();
			}
		</script>
		<style>
			body{
				margin:				0;
				overflow:			auto;
			}

			table{
				border-collapse:	collapse;
			}

			th{
				height:	5em;
			}

			td{
				border:	solid black 1px;
				padding:	0.2em;
			}

			.CLIENT{
				cursor:	pointer;
				background:	lightyellow;
			}

			.CLIENT:hover{
				background:	moccasin;
			}
		</style>
		<?php
			else:
				$req = $bdd->prepare("SELECT * FROM CLIENTS WHERE CODE = :CODE");
				$req->execute(array
					(
						'CODE'	=> $_POST['CODE']
					)
				);
				$client = $req->fetch(1);
				// echo "<pre>";
					// print_r($client);
				// echo "</pre>";
		?>
		<form method="post" action="../tools/create_inter.tool.php" name="new_inter">
			<table id="infos_client">
				<thead>
					<th colspan="4">
						INFORMATIONS CLIENT N°<span class="INTITULE INFORM"><?php echo $client['CODE']?></span>
					</th>
				</thead>
				<tbody>
					<tr>
						<td>
							<span class="INTITULE">CLIENT (<a href="#" style="text-decoration:none;" class="INTITULE INFORM">Changer Client</a>):</span><br>
							<textarea name="ADRESSE" rows="5" style="width:calc(100% - 0.4em); height:100%; resize:none; overflow:hidden"><?php
									echo $client['Nom'] . "\n";
									echo $client['ADLIVR_LIGNE1'] . "\n";
									echo $client['ADLIVR_LIGNE2'] . "\n";
									echo $client['ADLIVR_CODE_POSTAL'] . " " . $client['ADLIVR_VILLE'];
								?></textarea>
						</td>
						<td>
							<span class="INTITULE">TELEPHONE :</span><br>	<input type="text" value="<?php echo $client['TELEPHONE1']; ?>" name="TELEPHONE"><br><br>
							<span class="INTITULE">EMAIL :</span><br>		<input type="text" value="<?php echo $client['EMAIL']; ?>" name="EMAIL">
						</td>
						<td>
							<span class="INTITULE">CONTRAT DE SERVICE :</span> <br>
							<span class="INTITULE">du</span> <?php echo $client['CONTRAT_DU']; ?><br>
							<span class="INTITULE">au</span> <?php echo $client['CONTRAT_AU']; ?><br><br>
							<input type="checkbox" value="1" name="CONTRAT"> <span class="INTITULE INFORM">SOUS CONTRAT</span>
						</td>
						<td>
							<span class="INTITULE">OBJET DE L'INTERVENTION :</span><br>
								<input type="checkbox" name="INSTALLATION" value="1">	<span class="INFORM">INSTALLATION</span><br>
								<input type="checkbox" name="MAINTENANCE" value="1">	<span class="INFORM">MAINTENANCE</span><br>
								<input type="checkbox" name="FORMATION" value="1">		<span class="INFORM">FORMATION</span><br>
								<input type="checkbox" name="RENOUVELLEMENT" value="1">	<span class="INFORM">RENOUVELLEMENT</span>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="border:none;width:0.5em;">
						</td>
					</tr>
					<tr>
						<td>
							<span class="INTITULE INFORM">AGENCE :</span> <br>
							<select name="AGENCE">
								<option value="1">FLOIRAC</option>
								<option value="0">BAYONNE</option>
							</select>
						</td>
						<td>
							<span class="INTITULE INFORM">HEURE D'ARRIVEE :</span> <br>
							<select name="HEURES_ARRIVEE">
								<?php
									for($x = 0; $x < 24; $x++)
									{
										echo "<option value=" . $x . ">" . $x . "</option>";
									}
								?>
							</select>
							:
							<select name="MINUTES_ARRIVEE">
								<?php
									for($x = 0; $x < 60; $x = $x + 5)
									{
										echo "<option value=" . $x . ">" . $x . "</option>";
									}
								?>
							</select>
						</td>
						<td>
							<span class="INTITULE INFORM">HEURE DE DEPART :</span> <br>
							<select name="HEURES_DEPART">
								<?php
									for($x = 0; $x < 24; $x++)
									{
										echo "<option value=" . $x . ">" . $x . "</option>";
									}
								?>
							</select>
							:
							<select name="MINUTES_DEPART">
								<?php
									for($x = 0; $x < 60; $x = $x + 5)
									{
										echo "<option value=" . $x . ">" . $x . "</option>";
									}
								?>
							</select>
						</td>
						<td>
							<span class="INTITULE INFORM">DEPLACEMENT :</span> <br>
							<select name="DEPLACEMENT">
								<option value="1">ZONE1</option>
								<option value="0">ZONE2</option>
								<option value="-1">AUCUNS</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="height:2em;border:none">
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<span class="INTITULE">DESCRIPTION :</span><br>
							<textarea name="DESCRIPTION" rows="15" style="width:calc(100% - 0.4em);resize:none; overflow:hidden"></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span class="INTITULE">PRÊT DE MATERIELS :</span><br>
							<textarea name="MATERIELS" rows="5" style="width:calc(100% - 0.4em);resize:none; overflow:hidden;text-align:right;"></textarea>
						</td>
						<td colspan="2">
							<span class="INTITULE">NUMEROS DE SERIES :</span><br>
							<textarea name="NUMEROS_SERIES" rows="5" style="width:calc(100% - 0.4em);resize:none; overflow:hidden"></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="height:2em;border:none">
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span class="INTITULE INFORM">Test de bon fonctionnement :</span> <input type="checkbox" value="1" name="FONCTIONNEMENT">
						</td>
						<td colspan="2">
							<span class="INTITULE INFORM">Formation à la sauvegarde :</span> <input type="checkbox" value="1" name="FORMATION_SAUVEGARDE">
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span class="INTITULE INFORM">Le matériel est NF :</span> <input type="checkbox" value="1" name="LOI_NF">
						</td>
						<td colspan="2">
							<span class="INTITULE INFORM">Numéro de Version :</span> <input type="text" name="VERSION">
						</td>
					</tr>
					<tr>
						<td colspan="4" style="height:2em;border:none">
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<span class="INTITULE INFORM">COMPLEMENTS</span><br>
							<textarea name="COMPLEMENTS" rows="5" style="width:calc(100% - 0.4em);resize:none; overflow:hidden"></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" value="1" name="ATTENTE">
			<input type="hidden" value="<?php echo $client['CODE']?>" name="CODE_CLIENT">
			<input type="hidden" value="<?php echo $client['Nom']?>" name="CLIENT">
			<input type="hidden" value="<?php echo $client['CODE_VENDEUR']?>" name="COMMERCIAL">
			<input type="hidden" value="" name="DATE">
			<div onclick="send()" id="SUBMIT">Envoyer</div>
		</form>

			<script>
				function send(){
					var form = document.getElementsByName('new_inter')[0];
					var date = new Date();

					date = date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
					document.getElementsByName('DATE')[0].value = date;

					form.submit();
				}
			</script>

			<style>
				body{
					overflow:			auto;
					margin:				0;
				}

				#infos_client{
					border-collapse:	collapse;
					margin:				5% auto;
					width:				90%;
				}

				#infos_client th{
					border:				solid black 1px;
					height:				5em;
				}

				#infos_client td{
					border:				solid black 1px;
					padding:			0.2em;
					width:				25%;
				}

				.CLIENT{
					cursor:				pointer;
					background:			lightyellow;
				}

				.CLIENT:hover{
					background:			moccasin;
				}

				.INTITULE{
					font-weight:		bold;
				}

				.INFORM{
					color:				blue;
				}

				#SUBMIT{
					width:				160px;
					height:				80px;
					border:				solid black 1px;
					border-radius:		5px;
					background:			grey;
					cursor:				pointer;
				}
			</style>
		<?php
			endif;
		?>
	</body>
</html>

<?php
	if($debug_mode)
	{
		echo("<pre style='color:white; text-align:left;'>");
		print_r($_SERVER);
		echo("</pre>");
	}
?>
