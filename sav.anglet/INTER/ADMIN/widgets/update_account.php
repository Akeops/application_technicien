<?php
	require('../inc/accounts.inc.php');
	
	$req = $bdd->prepare("
		SELECT
			u.ID, u.NAME, u.PSEUDO, u.MAIL, u.TOKEN_MAIL, u.PWD, r.NAME RIGHTS
		FROM
			USERS_INTER u
		INNER JOIN
			RIGHTS r
		ON
			r.ID = u.RIGHTS
		WHERE
			u.ID = :id
	");
	
	$req->execute(array(
		'id'	=> $_SESSION['id']
		)
	) or die(print_r($req->errorInSfo()));
	
	$account = $req->fetch();
	
	if(!empty($_POST) && update($account))
	{
		echo "<script>window.parent.location = '/INTER/ADMIN/inc/accounts.inc.php?logout&recon';</script>";
	}
	else if(isset($_GET['token']))
	{
		if(($_GET['token'] === $account['TOKEN_MAIL']))
		{
			$mail = explode(",", base64_decode($_GET['token']));
			$req = $bdd->prepare("UPDATE USERS_INTER SET MAIL = :mail, TOKEN_MAIL = NULL WHERE ID = :id");
			$req->execute(
				array
				(
					'mail'	=>	base64_decode($mail[1]),
					'id'	=>	$mail[0]
				)
			) or die(print_r($req->errorInfo()));
			
			header('Location:	/INTER/ADMIN');
		}
		else
		{
			header('Location:	/INTER/ADMIN');
		}
	}
	
	function update($account)
	{
		$bdd = $GLOBALS['bdd'];
		if(password_verify($_POST['passCheck'], $account['PWD']) && ($_POST['newpassword'] === $_POST['newpasswordCheck']) && (!empty($_POST['newpassword'])))
		{
			$req = $bdd->prepare("UPDATE USERS_INTER SET PSEUDO = :pseudo, PWD = :pwd WHERE ID = :id");
			$req->execute(
				array
				(
					'pseudo'	=>	$_POST['identifiant'],
					'pwd'		=>	password_hash($_POST['newpassword'], PASSWORD_DEFAULT),
					'id'		=>	$_SESSION['id']
				)
			) or die(print_r($req->errorInfo()));
			$_SESSION['user']	= $_POST['identifiant'];
		}
		else if(password_verify($_POST['passCheck'], $account['PWD']) && empty($_POST['newpassword']))
		{
			$req = $bdd->prepare("UPDATE USERS_INTER SET PSEUDO = :pseudo WHERE ID = :id");
			$req->execute(
				array
				(
					'pseudo'	=>	$_POST['identifiant'],
					'id'		=>	$_SESSION['id']
				)
			) or die(print_r($req->errorInfo()));
			$_SESSION['user']	= $_POST['identifiant'];
		}
		else
		{
			header('Location:	./update_account.php?deny');
		}
		
		if($_POST['mail'] != $account['MAIL'])
		{
			$req = $bdd->prepare("UPDATE USERS_INTER SET TOKEN_MAIL = :token WHERE ID = :id");
			$req->execute(
				array
				(
					'token'	=>	sendMail("Test", $_POST['mail']),
					'id'	=>	$account['ID']
				)
			);
		}
		
		return true;
	}
?>
<html>
	<header>
	</header>
	
	<body>
		<form method="post">
			<?php if(isset($_GET['deny'])):?>
				Une erreur s'est produite :<br>
				<i style="color:red">- Vérifiez vos mots de passes.</i><br><br>
			<?php endif;?>
			<table>
				<tr>
					<td>
						Nouvel Identifiant :<br>
						<input type="text" name="identifiant" value="<?=$account['PSEUDO']?>" placeholder="Identifiant" required>
					</td>
					<td>
						Changer l'Email de Contact :<br>
						<input type="email" name="mail" value="<?=$account['MAIL']?>" placeholder="Email" required>
					</td>
				</tr>
				<tr>
					<td>
						Changer de Mot de passe :<br>
						<input id="newPass" type="password" name="newpassword" placeholder="Nouveau mot de passe" onchange="passCheckFunc()"><br>
						<input id="newPassCheck" type="password" name="newpasswordCheck" placeholder="Confirmation">
					</td>
				</tr>
				<tr>
					<td>
						Validation :<br>
						<input type="password" name="passCheck" placeholder="Mot de passe actuel" required>
					</td>
				</tr>
				<tr>
					<td>
						<button type="submit">Valider Changements</button><button type="reset">Réinitialiser</button>
					</td>
				</tr>
			</table>
		</form>
	</body>
	
	<footer>
	</footer>
</html>

<style>
	*{
		font-size:		20px;
		font-weight:	bold;
		font-family:	arial;
		background:		white;
	}
	
	input{
		font-weight:	normal;
	}
</style>

<script src="/INTER/ADMIN/inc/jQuery.js"></script>

<script>
	function passCheckFunc()
	{
		if($("#newPass").val() != "")
		{
			$("#newPassCheck").attr("require","");
		}
		else
		{
			$("#newPassCheck").removeAttr("require");
		}
	}
</script>

<?php
	function sendMail($sujet, $mail){
		
		$account = $GLOBALS['account'];
		$token = base64_encode($mail);
		$str = $account['ID'].",".$token;
		$hash = base64_encode($str);
		
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
		$message_txt = "Pour valider votre changement d'Email de contact, merci de cliquer sur ce lien : http://sav.tacteo.fr/INTER/ADMIN/widgets/update_account.php?token=".$hash;
		$message_html = "
			<html>
				Pour valider votre changement d'Email de contact, merci de cliquer sur <a href='http://sav.tacteo.fr/INTER/ADMIN/widgets/update_account.php?token=".$hash."'>ce lien</a>
			</html>
		";
		//==========
		 
		//=====Création de la boundary.
		$boundary = "-----=".md5(rand());
		$boundary_alt = "-----=".md5(rand());
		//==========
		 
		//=====Création du header de l'e-mail.
		$header = "From: \"Tacteo SE\"<no-reply@tacteo-se.fr>".$passage_ligne;
		$header.= "Reply-to: \"Tacteo SE\" <no-reply@tacteo-se.fr>".$passage_ligne;
		$header.= "MIME-Version: 1.0".$passage_ligne;
		$header.= "Content-Type: multipart/mixed;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;
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
		
		//=====Envoi de l'e-mail.
		mail($mail,$sujet,$message,$header);
		return $hash;
	}
?>