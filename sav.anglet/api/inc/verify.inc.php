<?php

	// L'API a besoin d'informations primordiale pour pouvoir fonctionner correctement :
	// - UUID 
	// - USERNAME
	// - PASSWORD
	// - MODEL
	// - PLATFORM
	// - MANUFACTURER

	require('../admin/inc/config.inc.php');
	$json = array();

	$req = $bdd->query("
		SELECT
			*
		FROM
			SETTINGS
		WHERE
			NAME = 'API'
	") or die(print_r($bdd->errorInfo()));
	$api = $req->fetch(2);
	
	if(intval($api['VALUE']))
	{
		header("Access-Control-Allow-Origin: *");
	}
	else
	{
		$json['SUCCESS'] = 0;
		$json['ERROR'] = "L'API n'est pas activée.\nContactez un administrateur.";
		echo json_encode($json);
		die();
	}

	if(empty($_POST))
	{
		header('Location:	errors/error_rights.php');
		die();
	}
	else
	{
		$req = $bdd->prepare("
			SELECT
				*
			FROM
				USERS_INTER
			WHERE
				PSEUDO = :user
		");
		$req->execute(array(
			'user' =>	$_POST['username']
		)) or die(print_r($req->errorInfo()));
		$user = $req->fetch(2);
		
		if(empty($user))
		{
			header('Location:	errors/error_rights.php');
			die();
		}
		else if($user['DISABLED'])
		{
			$json['SUCCESS'] = 0;
			$json['ERROR'] = "Connexion impossible. Votre compte à été désactivé.";
			echo json_encode($json);
			die();
		}
		else if(!password_verify($_POST['password'], $user['PWD']))
		{
			$json['SUCCESS'] = 0;
			$json['ERROR'] = "Identifiant ou mot de passe invalide.";
			echo json_encode($json);
			die();
		}
		
		$req = $bdd->query("
			SELECT
				*
			FROM
				RIGHTS
			WHERE
				ID = ".$user['RIGHTS']
		) or die(print_r($bdd->errorInfo()));
		$rights = $req->fetch(2);
		
		if(empty($rights))
		{
			$json['SUCCESS'] = 0;
			$json['ERROR'] = "Connexion impossible. Votre compte à été désactivé.";
			echo json_encode($json);
			die();
		}
		else if(!$rights['API_USE'])
		{
			$json['SUCCESS'] = 0;
			$json['ERROR'] = "Vous n'êtes pas autorisé à vous connecter.";
			echo json_encode($json);
			die();
		}
	}

	$req = $bdd->prepare("
		SELECT
			*
		FROM
			DEVICES
		WHERE
			UUID = :uuid
	");
	$req->execute(array(
		'uuid' =>	$_POST['UUID']
	)) or die(print_r($req->errorInfo()));
	$phone = $req->fetch(2);

	if(empty($phone))
	{
		$req = $bdd->prepare("
			INSERT INTO
				DEVICES (UUID, TYPE, USER, MODEL, PLATFORM, MANUFACTURER)
			VALUES
				(:uuid, :type, :user, :model, :platform, :manufacturer)
		");
		$req->execute(array(
			'uuid'			=> $_POST['UUID'],
			'type'			=> $_POST['type'],
			'user'			=> $user['ID'],
			'model'			=> $_POST['model'],
			'platform'		=> $_POST['platform'],
			'manufacturer'	=> $_POST['manufacturer']
		)) or die(print_r($req->errorInfo()));
		
		$json['SUCCESS'] = 0;
		$json['ERROR'] = "Première connexion. L'appareil a été inscrit sur le serveur.\r\rUne demande de validation à été envoyée.";
		echo json_encode($json);
		sendNotif($user);
		die();
	}
	else if($phone['WAIT_VALIDITY'])
	{
		$json['SUCCESS'] = 0;
		$json['ERROR'] = "Demande de validation en cours de traitement.\r\rRéessayez plus tard ou contactez un administrateur.";
		echo json_encode($json);
		die();
	}
	else if(!$phone['VALIDITY'])
	{
		$json['SUCCESS'] = 0;
		$json['ERROR'] = "L'appareil n'est pas autorisé à se connecter au serveur.";
		echo json_encode($json);
		die();
	}
	
	function sendNotif($user)
	{	
		$mail = "leo@tacteo-se.fr";
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
		$message_txt = "
			Un nouveau périphérique est en attente de validation :".$passage_ligne.$passage_ligne."
			UTILISATEUR\tUUID\tTYPE\tMODELE\tPLATEFORME\tFABRICANT".$passage_ligne.
			$user['NAME']."\t".$_POST['UUID']."\t".$_POST['type']."\t".$_POST['model']."\t".$_POST['platform']."\t".$_POST['manufacturer'];
		$message_html = "
			<html style='width: 100%;margin: 0 auto; display: block; background: white'>
				<h1 style='text-align: center;'>
					Un nouveau périphérique est en attente de validation.
				</h1>
				
				<img id='ban' src='https://sav.tacteo.fr/img/logoBan.png' alt='SAV TACTEO' style='width: 50%; display: block; margin: 0 auto;'>

				<table style='width: 75%;margin:0 auto'>
					<tr>
						<td>
							UTILISATEUR
						</td>
						<td>
							UUID
						</td>
						<td>
							TYPE
						</td>
						<td>
							MODEL
						</td>
						<td>
							PLATEFORME
						</td>
						<td>
							FABRICANT
						</td>
					</tr>
					<tr>
						<td>
							".$user['NAME']."
						</td>
						<td>
							".$_POST['UUID']."
						</td>
						<td>
							".$_POST['type']."
						</td>
						<td>
							".$_POST['model']."
						</td>
						<td>
							".$_POST['platform']."
						</td>
						<td>
							".$_POST['manufacturer']."
						</td>
					</tr>
				</table>
			</html>
		";
		//==========
		 
		//=====Création de la boundary.
		$boundary = "-----=".md5(rand());
		$boundary_alt = "-----=".md5(rand());
		//==========
		 
		//=====Définition du sujet.
		$sujet = $user['NAME']." : Nouvel Appareil inscrit sur le Serveur !";
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
		//==========
	}
?>