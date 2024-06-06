<?php
	require('../inc/config.inc.php');
	require('../inc/accounts.inc.php');
	
	session_start();
	
	if(!$_SESSION['rights']['ADMIN'] && !$_SESSION['rights']['AGENCY']){
		header('Location: ../errors/error_rights.php');
		die();
	}

	$agencies = $bdd->query("
		SELECT
			NAME, ID
		FROM
			AGENCES
		WHERE
			NOT(DISABLED = 1)
		ORDER BY
			ID ASC
		") or die(print_r($bdd->errorInfo()));
?>

<head>
	<link href="https://fonts.googleapis.com/css?family=Cabin Condensed" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script src="../scripts/jQuery.min.js"></script>
	<script src="conf.js"></script>
</head>

<html>
	<body>
		<div class="section">
			<h2>Gestion des Agences</h2>
		</div>
		<div class="sectionContent">
			<form method="post" action="edit_agency.php" id="editAgency">
				<input type="hidden" name="agencyID"/>
			</form>
			<h1 style="width:50%;margin:0 auto">Agences Actives</h1>
			<table class="users">
				<tr style="display:none">
					<td>
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<th>
						<span>NOM</span>
					</th>
					<th>
						<span></span>
					</th>
				</tr>
				<?php
					while($agency = $agencies->fetch()):?>
					<tr>
						<td>
							<span><?=$agency['NAME']?></span>
						</td>
						<td>
							<span class="editOpt" onclick="editAgency(<?=$agency['ID']?>)" href="#">Editer</span> | <span class="editOpt" onclick="disableAgency(<?=$agency['ID']?>)" href="#">Désactiver</span>
						</td>
					</tr>
				<?php endwhile; ?>
					<tr style="border:none">
						<td style="border:none">
						</td>
						<td style="padding-top:40px;border:none">
							<input type="button" onclick="newAgency(1)" value="Nouvelle Agence"/>
						</td>
					</tr>
			</table>
			<hr width="50%">
			
			<?php
				$agenciesDisabled = $bdd->query("
					SELECT
						NAME, ID
					FROM
						AGENCES
					WHERE
						DISABLED = 1
					ORDER BY
						ID ASC
					") or die(print_r($bdd->errorInfo()));
			?>
			<h1 style="width:50%;margin:90px auto 0 auto">Agences Désactivées</h1>
			<table class="users">
				<tr style="display:none">
					<td>
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<th>
						<span>NOM</span>
					</th>
					<th>
						<span></span>
					</th>
				</tr>
				<?php
					while($agencyDisabled = $agenciesDisabled->fetch()):?>
					<tr>
						<td>
							<span><?=$agencyDisabled['NAME']?></span>
						</td>
						<td>
							<span class="editOpt" onclick="editAgency(<?=$agencyDisabled['ID']?>)" href="#">Editer</span> | <span class="editOpt" onclick="enableAgency(<?=$agencyDisabled['ID']?>)" href="#">Réactiver</span>
						</td>
					</tr>
				<?php endwhile; ?>
				<tr>
					<td colspan="4">
					</td>
				</tr>
			</table>
			<div id="newAgency">
				<div id="field">
					<span onclick="newAgency(0)" id="btnbackuser"><p>X</p></a></span>
					<h1>Nouvelle Agence</h1>
					<form method="post" action="edit_agency.php?create" id="formNewUsr">
						<table>
							<tr>
								<td>
									<span>Nom<span>*</span> :</span><input type="text" name="NAME" placeholder="Nom de l'Agence" required />
								</td>
								<td>
									<span>Adresse<span>*</span> :</span><input type="text" name="ADDRESS_LINE1" placeholder="6 rue Adresse Exemple" required />
								</td>
							</tr>
							<tr>
								<td>
									<span>Téléphone :</span><input type="phone" name="PHONE" placeholder="09 87 65 43 21" />
								</td>
								<td>
									<span>Complément d'Adresse :</span><input type="text" name="ADDRESS_LINE2" placeholder="Zone Exemple" />
								</td>
							</tr>
							<tr>
								<td>
									<span>FAX :</span><input type="phone" name="FAX" placeholder="05 76 54 32 10" />
								</td>
								<td>
									<span>Ville<span>*</span> :</span><input type="text" name="CITY" placeholder="Ville-sur-Exemple" required />
								</td>
							</tr>
							<tr>
								<td>
									<span>Code Postal<span>*</span> :</span><input type="text" name="POSTAL_CODE" placeholder="33000" required />
								</td>
								<td>
									<span>Mail :</span><input type="MAIL" name="MAIL" placeholder="contact@exemple.com" />
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<span>Autre :</span><input type="MAIL" name="OTHER" placeholder="Informations Complémentaires etc..." />
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
								</td>
								<td>
									<input type="button" onclick="sendNew()" value="Valider" />
									<input type="submit" style="display:none" id="submitBtn" />
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>

<script>
	function editAgency(agency){
		form = document.getElementById("editAgency");
		input = document.getElementsByName("agencyID")[0];
		
		input.value = agency;
		form.submit();
	}
	
	function newAgency(show){
		if(show)
		{
			$("#newAgency")[0].style.display = "block";
			$("#newAgency")[0].style.opacity = "1";
			$("#newAgency")[0].style.zIndex = "1";
		}
		else
		{
			$("#newAgency")[0].removeAttribute("style");
		}
	}
	
	function sendNew(){
		$('#submitBtn').click();
	}
	
	function disableAgency(agency){
		window.location = "edit_agency.php?disable=" + agency;
	}
	
	function enableAgency(agency){
		window.location = "edit_agency.php?enable=" + agency;
	}
</script>