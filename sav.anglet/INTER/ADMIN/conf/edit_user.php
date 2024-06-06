<?php

	require('/home/tacteose/sav.anglet/INTER/ADMIN/inc/accounts.inc.php');
	
	if(!(!$_SESSION['rights']['ADMIN'] || !$_SESSION['rights']['ACCOUNTS_EDIT']) || empty($_GET))
	{
		header('Location:	../errors/error_rights.php');
		die();
	}
	elseif(isset($_GET['create']))
	{
		$req = $bdd->prepare("
			INSERT INTO
				USERS_INTER (NAME,PSEUDO,PWD,MAIL,RIGHTS,IS_COMMERCIAL,CODE_COMMERCIAL,AGENCY)
			VALUES
				(:name,:pseudo,:pwd,:mail,:rights,:iscommercial,:codecommercial,:agency)
		");
		
		$req->execute(array(
			'name'			=>	$_POST['NAME'],
			'pseudo'		=>	$_POST['PSEUDO'],
			'pwd'			=>	password_hash($_POST['PWD'],PASSWORD_DEFAULT),
			'mail'			=>	$_POST['MAIL'],
			'rights'		=>	$_POST['RIGHTS'],
			'iscommercial'	=>	$_POST['IS_COMMERCIAL'] ? $_POST['IS_COMMERCIAL'] : "0",
			'codecommercial'=>	$_POST['CODE_COMMERCIAL'] ? $_POST['CODE_COMMERCIAL'] : NULL,
			'agency'		=>	$_POST['AGENCY']
		)) or die(print_r($req->errorInfo()));
		
		$req = $bdd->query("
			SELECT
				ID
			FROM
				USERS_INTER
			WHERE
				NAME = '".$_POST['NAME']."'
		") or die(print_r($req->errorInfo()));
		
		$user = $req->fetch(2);
		
		header('Location:	edit_user.php?userID='.$user['ID']);
		die();
	}
	elseif(!empty($_POST))
	{
		updateUser($bdd);
		header('Location:	user_manager.php');
		die();
	}
	elseif(isset($_GET['disable']))
	{
		$req = $bdd->prepare("
			UPDATE
				USERS_INTER
			SET
				DISABLED = 1
			WHERE
				ID = :userID
		");

		$req->execute(array(
			'userID'	=>	$_GET['disable']
		)) or die(print_r($bdd->errorInfo()));
		
		header('Location:	user_manager.php');
		die();
	}
	elseif(isset($_GET['enable']))
	{
		$req = $bdd->prepare("
			UPDATE
				USERS_INTER
			SET
				DISABLED = 0
			WHERE
				ID = :userID
		");

		$req->execute(array(
			'userID'	=>	$_GET['enable']
		)) or die(print_r($bdd->errorInfo()));
		
		header('Location:	user_manager.php');
		die();
	}
	
	$_SESSION['userID'] = $_GET['userID'];
	
	$req = $bdd->prepare("
		SELECT
			*
		FROM
			USERS_INTER
		WHERE
			ID = :userId"
		) or die($req->errorInfo());
	
	$req->execute(array(
		'userId'	=>	$_SESSION['userID']
		)) or die($req->errorInfo());
	
	$user = $req->fetch(2);
	
	$groups = $bdd->query("
		SELECT
			*
		FROM
			RIGHTS
		WHERE
			NOT(ID = 4)
		ORDER BY
			ID ASC
		"
	) or die($req->errorInfo());
	
	$agencies = $bdd->query("
		SELECT
			ID, NAME
		FROM
			AGENCES
		WHERE
			NOT(ID = -1)
		ORDER BY
			ID ASC
		"
	) or die($req->errorInfo());
	
	function updateUser($bdd)
	{	
		$req = $bdd->prepare("
			SELECT
				*
			FROM
				USERS_INTER
			WHERE
				ID = :user"
			) or die($req->errorInfo());
			
		$req->execute(array(
			'user'	=>	$_SESSION['id']
			)) or die($req->errorInfo());
			
		$infoUser = $req->fetch(2);
		
		if(!password_verify($_POST['verify'],$infoUser['PWD']) || ($_POST['pwd'] != $_POST['newpwd']))
		{
			header('Location:	edit_user.php?userID='.$_SESSION['userID'].'&error');
			die();
		}
		else
		{
			unset($_GET['error']);
		}
		
		$req = $bdd->prepare("
			UPDATE
				USERS_INTER
			SET
				NAME	= :name,
				PSEUDO	= :pseudo,
				RIGHTS	= :rights,
				MAIL	= :mail,
				TOKEN	= NULL,
				IS_COMMERCIAL = :iscommercial,
				CODE_COMMERCIAL = :codecommercial,
				MAIL_COMMERCIAL = :mailcommercial,
				AGENCY = :agency
			WHERE
				ID		= :userId"
			);
				
		$req->execute(array(
			'name'		=> $_POST['NAME'],
			'pseudo'	=> $_POST['PSEUDO'],
			'rights'	=> $_POST['rights'],
			'mail'		=> $_POST['MAIL'],
			'userId'	=> $_SESSION['userID'],
			'iscommercial'	=> $_POST['IS_COMMERCIAL'] ? $_POST['IS_COMMERCIAL'] : "0",
			'codecommercial'=> $_POST['CODE_COMMERCIAL'] ? $_POST['CODE_COMMERCIAL'] : NULL,
			'mailcommercial'=> $_POST['MAIL_COMMERCIAL'] ? $_POST['MAIL_COMMERCIAL'] : NULL,
			'agency'	=> $_POST['AGENCY']
		)) or die($req->errorInfo());
		
		if(!empty($_POST['pwd']))
		{
			$req = $bdd->prepare("
				UPDATE
					USERS_INTER
				SET
					PWD = :pwd
				WHERE
					ID = :userID"
				) or die($req->errorInfo());
			
			$req->execute(array(
				'pwd'		=> password_hash($_POST['pwd'], PASSWORD_DEFAULT),
				'userID'	=> $_SESSION['userID']
			)) or die($req->errorInfo());
		}
		
		//unset($_SESSION['userId']);
		$_GET['success'] = true;
	}
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
			<a href="user_manager.php" id="btnback"><p>X</p></a>
		</div>
		<div class="section">
			<h2>EDITER L'UTILISATEUR : <i><?=$user['NAME']?></i></h2>
		</div>
		<div class="sectionContent">
			<form method="post" action="" onreset="setTimeout(function(){setValidate();newPasswd();},1)" id="userForm">
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
							<span>Nom<span>*</span></span><input onkeyup="setValidate()" name="NAME" type="text" value="<?=$user['NAME']?>" required />
						</td>
						<td>
							<span>Identifiant<span>*</span></span><input onkeyup="setValidate()" autocomplete="username" name="PSEUDO" type="text" value="<?=$user['PSEUDO']?>"  required />
						</td>
					</tr>
					<tr>
						<td>
							<span>Mail<span>*</span></span><input onkeyup="setValidate()" autocomplete="mail" name="MAIL" type="text" value="<?=$user['MAIL']?>"  required />
						</td>
						<td colspan="2">
							<span>Groupe<span>*</span></span>
							<select onchange="setValidate()" name="rights"  required >
								<?php
									while($group = $groups->fetch()){
										echo "<option style='color:".$group['COLOR']."' value=".$group['ID'];
										
										if($user['RIGHTS'] == $group['ID']){
											echo " selected";
										}
										
										echo ">".$group['NAME']."</option>";
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span>Agence<span>*</span></span>
							<select onchange="setValidate()" name="AGENCY"  required >
								<?php
									while($agency = $agencies->fetch()){
										echo "<option value=".$agency['ID'];
										
										if($user['AGENCY'] == $agency['ID']){
											echo " selected";
										}
										
										echo ">".$agency['NAME']."</option>";
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;padding-top:20px">
						</td>
					</tr>
					<tr>
						<td>
						<?php if(!isset($_GET['create'])):?>
							<span>Changer Mot de Passe<input onchange="newPasswd()" autocomplete="new-password" type="password" placeholder="Nouveau mot de passe" name="pwd">
						<?php else :?>
							<span>Mot de Passe<input onchange="newPasswd()" autocomplete="new-password" type="password" placeholder="Nouveau mot de passe" name="pwd" required>
						<?php endif;?>
						</td>
						<td style="text-align: right">
							<span style="height:1em;display:block"></span>
							<div class="button" onclick="check(this)" style="display: inline;float:right" id="com">
								<div>
									<input type="checkbox" value="1" <?=$user['IS_COMMERCIAL'] ? "checked" : ""?> name="IS_COMMERCIAL">
								</div>
							</div>
							<span style="float:right;display:inline-block;transform:translateY(50%)">Commercial</span>
						</td>
					</tr>
					<tr>
						<td>
							<span>Confirmation</span>
							<input onchange="newPasswd()" autocomplete="new-password" type="password" placeholder="Confirmation" name="newpwd">
						</td>
						<td style="text-align: right">
							<com class="commercial">
								<span  class="commercial" style="height:1em;display:block"></span>
								<div class="button" onclick="check(this)" id="mail_com" style="display: inline;float:right">
									<div>
										<input type="checkbox" value="1" <?=$user['MAIL_COMMERCIAL'] ? "checked" : ""?> name="MAIL_COMMERCIAL">
									</div>
								</div>
								<span style="float:right;display:inline-block;transform:translateY(50%)">Reçois les Interventions</span>
							</com>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;padding-top:20px">
						</td>
					</tr>
					<tr>
						<td>
							<span>Votre Mot de Passe<span>*</span> <i>(sécurité)</i><input <?php if(isset($_GET['error'])){echo "style='box-shadow:0px 0px 2px 1px red'";} ?> onchange="setValidate()" onkeyup="setValidate()" autocomplete="current-password" type="password" placeholder="Votre Mot de Passe" name="verify" required>
						</td>
						<td>
							<com class="commercial">
								<span>Code Commercial</span>
								<input type="number" name="CODE_COMMERCIAL" placeholder="Code Numérique" value="<?=$user['CODE_COMMERCIAL']?>" />
							</com>
						</td>
					</tr>
					<tr>
						<td>
						</td>
						<td>
							<?php if(isset($_GET['error'])):?>
								<span><span>Votre mot de passe est peut-être erroné.</span></span>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="padding-top:20px">
							<span><b><span>*</span> : Champs Oligatoires.</b></span>
						</td>
					</tr>
					<tr>
						<td>
							<input type="button" onclick="reset();check('reset')" value="Réinitialiser">
						</td>
						<td>
							<input style="color:grey" type="submit" value="Valider les Changements" id="validate" disabled="true">
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>

<script>

	check('reset');

	function setValidate()
	{
		var verify = document.getElementsByName("verify")[0];
		var button = document.getElementById("validate");
		<?php if(isset($_GET['create'])):?>
			button.value = "Valider";
		<?php else:?>
			button.value = "Valider les Changements";
		<?php endif; ?>
		if(verify.value != "")
		{
			button.style.color = "";
			button.removeAttribute("disabled");
		}
		else
		{
			button.setAttribute("disabled","true");
			button.style.color = "grey";
		}
	}
	
	function newPasswd()
	{
		var pass = document.getElementsByName("pwd")[0];
		var newPass = document.getElementsByName("newpwd")[0];
		
		if(pass.value != "" || newPass != "")
		{
			pass.setAttribute("required","");
			newPass.setAttribute("required","");
		}
		else
		{
			pass.removeAttribute("required");
			newPass.removeAttribute("required");
		}
		
		if(pass.value != newPass.value)
		{
			var error = "Les Mots de Passes ne correspondent pas !";
			var styleError = "0px 0px 2px 1px red";
			newPass.setCustomValidity(error);
			newPass.style.boxShadow =		styleError;
			pass.style.boxShadow =			styleError;
		}
		else
		{
			newPass.setCustomValidity("");
			pass.style.boxShadow = 		"";
			newPass.style.boxShadow = 	"";
		}
	}
	
	function check(button){
		if(button == "reset")
		{
			var inputs = $('input[type="checkbox"]');
			for(var x = 0; x < inputs.length; x++)
			{
				var slide = inputs[x].parentNode;
				var button = slide.parentNode;
				var id = button.getAttribute("id");
				console.log(id);
				if(!inputs[x].checked)
				{
					slide.removeAttribute("style");
					button.removeAttribute("style");
					if(id == "com")
					{
						$(".commercial").css("display","none");
						
						$("input[name='CODE_COMMERCIAL']")[0].value = "";
						$("#mail_com input")[0].checked = "";
						$("#mail_com div")[0].removeAttribute("style");
						$("#mail_com input")[0].removeAttribute("style");
						$("#mail_com")[0].removeAttribute("style");
						$("input[name='CODE_COMMERCIAL']")[0].removeAttribute("required");
					}
				}
				else
				{
					slide.style.marginLeft = "calc(100% - 28px)";
					button.style.background = "lightgreen";
					if(id == "com")
					{
						$(".commercial").css("display","unset");
						$("input[name='CODE_COMMERCIAL']")[0].setAttribute("required","");
					}
				}
			}
		}
		else
		{
			var id = button.getAttribute("id");
			var slide = button.getElementsByTagName("div")[0];
			var input = button.getElementsByTagName("input")[0];
			
			if(input.checked)
			{
				slide.removeAttribute("style");
				input.checked = "";
				button.removeAttribute("style");
				if(id == "com")
				{
					$(".commercial").css("display","none");
					
					$("input[name='CODE_COMMERCIAL']")[0].value = "";
					$("#mail_com input")[0].checked = "";
					$("#mail_com div")[0].removeAttribute("style");
					$("#mail_com input")[0].removeAttribute("style");
					$("#mail_com")[0].removeAttribute("style");
					$("input[name='CODE_COMMERCIAL']")[0].removeAttribute("required");
				}
			}
			else
			{
				slide.style.marginLeft = "calc(100% - 28px)";
				input.checked = "true";
				button.style.background = "lightgreen";
				if(id == "com")
				{
					$(".commercial").css("display","unset");
					$("input[name='CODE_COMMERCIAL']")[0].setAttribute("required","");
				}
			}
		}
	}
</script>