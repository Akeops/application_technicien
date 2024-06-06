<?php
	require('../inc/config.inc.php');
	require('../inc/accounts.inc.php');
	
	session_start();
	if($_SESSION['rights'] < "3"){
		header('Location: widgets/home.php');
		die();
	}

	$users = $bdd->query("
		SELECT
			r.NAME 'GROUP', USERS_INTER.*, r.COLOR 'COLOR'
		FROM
			USERS_INTER
		INNER JOIN
			RIGHTS r ON (USERS_INTER.RIGHTS = r.ID)
		WHERE
			NOT(USERS_INTER.DISABLED = 1)
		ORDER BY
			USERS_INTER.RIGHTS DESC
		") or die(print_r($bdd->errorInfo()));
		
	$groups = $bdd->query("
		SELECT
			ID, NAME, COLOR
		FROM
			RIGHTS
		WHERE
			NOT(ID = -1)
		ORDER BY
			ID ASC
	") or die(print_r($bdd->errorInfo()));
	
	$agencies = $bdd->query("
		SELECT
			ID, NAME
		FROM
			AGENCES
		WHERE
			NOT(ID = -1)
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
			<h2>Gestion des Utilisateurs</h2>
		</div>
		<div class="sectionContent">
			<form method="get" action="edit_user.php" id="editUsr">
				<input type="hidden" name="userID"/>
			</form>
			<form method="get" action="stats_user.php" id="statsUsr">
				<input type="hidden" name="userID"/>
			</form>
			<h1 style="width:50%;margin:0 auto">Utilisateurs Actifs</h1>
			<table class="users">
				<tr style="display:none">
					<td>
					</td>
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
					<th>
						<span>NOM</span>
					</th>
					<th>
						<span>PSEUDO</span>
					</th>
					<th>
						<span>GROUPE</span>
					</th>
					<th>
						<span></span>
					</th>
				</tr>
				<?php
					while($user = $users->fetch()):?>
					<tr>
						<td>
							<span><?=$user['NAME']?></span>
						</td>
						<td>
							<span><?=$user['PSEUDO']?></span>
						</td>
						<td>
							<span style="text-shadow:0 0 2px black;color:<?=$user['COLOR']?>"><b><?=$user['GROUP']?></b></span>
						</td>
						<td>
							<span class="editOpt" onclick="editUser(<?=$user['ID']?>)" href="#">Editer</span> | <span class="editOpt" onclick="statsUser(<?=$user['ID']?>)" href="#">Stats</span> | <span class="editOpt" onclick="disableUser(<?=$user['ID']?>)" href="#">Désactiver</span>
						</td>
					</tr>
				<?php endwhile; ?>
					<tr style="border:none">
						<td colspan="3" style="border:none">
						</td>
						<td style="padding-top:40px;border:none">
							<input type="button" onclick="newUser(1)" value="Nouvel Utilisateur"/>
						</td>
					</tr>
			</table>
			<hr width="50%">
			
			<?php
				$usersDisabled = $bdd->query("
					SELECT
						r.NAME 'GROUP', USERS_INTER.*, r.COLOR 'COLOR'
					FROM
						USERS_INTER
					INNER JOIN
						RIGHTS r ON (USERS_INTER.RIGHTS = r.ID)
					WHERE
						USERS_INTER.DISABLED = 1
					ORDER BY
						USERS_INTER.NAME ASC
					") or die(print_r($bdd->errorInfo()));
			?>
			<h1 style="width:50%;margin:90px auto 0 auto">Utilisateurs Désactivés</h1>
			<table class="users">
				<tr style="display:none">
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
					<th>
						<span>NOM</span>
					</th>
					<th>
						<span>PSEUDO</span>
					</th>
					<th>
						<span>GROUPE</span>
					</th>
					<th>
						<span></span>
					</th>
				</tr>
				<?php
					while($userDisabled = $usersDisabled->fetch()):?>
					<tr>
						<td>
							<span><?=$userDisabled['NAME']?></span>
						</td>
						<td>
							<span><?=$userDisabled['PSEUDO']?></span>
						</td>
						<td>
							<span style="text-shadow:0 0 2px black;color:<?=$userDisabled['COLOR']?>"><b><?=$userDisabled['GROUP']?></b></span>
						</td>
						<td>
							<span class="editOpt" onclick="editUser(<?=$userDisabled['ID']?>)" href="#">Editer</span> | <span class="editOpt" onclick="statsUser(<?=$userDisabled['ID']?>)" href="#">Stats</span> | <span class="editOpt" onclick="enableUser(<?=$userDisabled['ID']?>)" href="#">Réactiver</span>
						</td>
					</tr>
				<?php endwhile; ?>
				<tr>
					<td colspan="4">
					</td>
				</tr>
			</table>
			<div id="newUsr">
				<div id="field">
					<span onclick="newUser(0)" id="btnbackuser"><p>X</p></a></span>
					<h1>Nouvel Utilisateur</h1>
					<form method="post" action="edit_user.php?create" id="formNewUsr">
						<table>
							<tr>
								<td>
									<span>Nom<span>*</span> :</span><input type="text" name="NAME" placeholder="Nom" required />
								</td>
								<td>
									<span>Identifiant<span>*</span> :</span><input type="text" name="PSEUDO" placeholder="Identifiant" required />
								</td>
							</tr>
							<tr>
								<td>
									<span>Mail<span>*</span> :</span><input type="mail" name="MAIL" placeholder="Email" required />
								</td>
								<td>
									<span>Groupe<span>*</span> :</span>
									<select name="RIGHTS" required >
										<?php
											while($group = $groups->fetch()){
												echo "<option style='color:".$group['COLOR']."' value=".$group['ID'].">".$group['NAME']."</option>";
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<span>Agence<span>*</span> :</span>
									<select name="AGENCY" required >
										<?php
											while($agency = $agencies->fetch()){
												echo "<option value=".$agency['ID'].">".$agency['NAME']."</option>";
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td>
									<span>Mot de Passe<span>*</span> :</span>
									<input type="password" name="PWD" placeholder="Mot de Passe" required />
								</td>
								<td>
								</td>
							</tr>
							<tr>
								<td>
									<span>Confirmer le Mot de Passe<span>*</span> :</span>
									<input type="password" name="PWDconfirm" placeholder="Confirmation Mot de Passe" required />
								</td>
								<td>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<span><b><span>* </span>: Champs Obligatoires.</b></span>
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
	function editUser(user){
		form = document.getElementById("editUsr");
		input = document.getElementsByName("userID")[0];
		
		input.value = user;
		form.submit();
	}
	
	function statsUser(user){
		form = document.getElementById("statsUsr");
		input = form.getElementsByTagName("input")[0];
		
		input.value = user;
		form.submit();
	}
	
	function newUser(show){
		if(show)
		{
			$("#newUsr")[0].style.display = "block";
			$("#newUsr")[0].style.opacity = "1";
			$("#newUsr")[0].style.zIndex = "1";
		}
		else
		{
			$("#newUsr")[0].removeAttribute("style");
		}
	}
	
	function sendNew(){
		var error = "Les Mots de Passes ne correspondent pas !";
		var styleError = "0px 0px 2px 1px red";
		
		if($("input[name='PWD']")[0].value == $("input[name='PWDconfirm']")[0].value)
		{
			$("input[name='PWDconfirm']")[0].setCustomValidity("");
			$("input[name='PWDconfirm']")[0].removeAttribute("style");
			$("input[name='PWD']")[0].removeAttribute("style");
		}
		else
		{
			$("input[name='PWDconfirm']")[0].setCustomValidity(error);
			$("input[name='PWDconfirm']")[0].style.boxShadow =		styleError;
			$("input[name='PWD']")[0].style.boxShadow =				styleError;
		}
		
		$('#submitBtn').click();
	}
	
	function disableUser(user){
		window.location = "edit_user.php?disable=" + user;
	}
	
	function enableUser(user){
		window.location = "edit_user.php?enable=" + user;
	}
</script>