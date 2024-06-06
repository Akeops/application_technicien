<?php	
	require('../../inc/config.inc.php');
	require('../../inc/login.inc.php');
	
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	
	$_GET['pdf'] = 1;
	
	$interID = $_POST['ID'] ? $_POST['ID'] : $_GET['inter'];
	
	//=====Récupération des information établissement.
	$req = $bdd->query("SELECT * FROM ETABLISSEMENT");
	$establishment = $req->fetch();
	//==========
	
	//=====Récupération des informations de l'Intervention.
	$req = $bdd->prepare("SELECT * FROM INTER WHERE ID = :id");
	$req->execute(array
		(
			'id' => $interID
		)
	) or die();
	$inter = $req->fetch();
	
	if(empty($inter)){
		echo "<div style='text-align:center'><h1>L'Intervention ".$interID." est introuvable.</h1></div>";
		die();
	}
	//==========
	
	//=====Récupération des Agences.
	$req = $bdd->prepare("SELECT * FROM AGENCES WHERE ID = :id");
	$req->execute(array('id' => $inter['AGENCY']));
	$agency = $req->fetch(2);
	//==========
	
	//=====Récupération des informations de l'utilisateur.
	$req = $bdd->prepare("SELECT * FROM USERS_INTER WHERE ID = :id");
	$req->execute(array
		(
			'id' => $inter['USER_UPLOAD']
		)
	);
	$user = $req->fetch();
	//=========
	
	//=====Récupération des informations de tarifications.
	$req = $bdd->query("SELECT * FROM TARIFS");
	$tarif = $req->fetchAll(2);
	//=========
	
	ob_start();

	require('../../inc/intergen.inc.php');
	
	if($_GET['send']):
?>
	<script>
		function edit()
		{
			document.getElementsByTagName("html")[0].style.display = "none";
		}
	</script>
<?php
	endif;
	$_HTML = ob_get_clean();
	
	require '../../../vendor/autoload.php';
	use Spipu\Html2Pdf\Html2Pdf;
	
	if($_GET['pdf'])
	{
		$name = "Intervention " . $inter['ID'] . " " . $inter['CUSTOMER_LABEL'] . " (" . $inter['CUSTOMER_CODE'] . ") " . $inter['DATE_END'] . ".pdf";
		$pdf = new \Spipu\Html2Pdf\Html2Pdf('P','A3','fr', true, 'UTF-8', array(21, 21, 21, 21));
		$pdf->writeHTML($_HTML);
		$pdf->pdf->SetTitle($name);
		
		if($_GET['SENDMAIL'])
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
	
	$files = glob('tmp/*');
	foreach($files as $file){
		if(is_file($file))
		{
			unlink($file);
		}
	}
	
	function sendMail($pdf, $name, $inter){
		//=====Récupération des informations techniciens.
		$req = $GLOBALS['bdd']->prepare("SELECT * FROM USERS_INTER WHERE ID = :tech");
		$req->execute(array('tech'	=>	$inter['USER_UPLOAD']));
		//==========
		
		$user = $req->fetch(2);
		$mailBcc .= $user['MAIL'].",";
		
		// =====Récupération des emails établissement.
		$req = $GLOBALS['bdd']->query("SELECT * FROM SETTINGS WHERE NAME = 'MAILING'") or die(print_r($GLOBALS['bdd']->errorInfo()));
		$req = $req->fetch();
		$mailBcc .= $req['VALUE'];
		// ==========
		
		//=====Récupération de l'email du client.
		$mail = $inter['CUSTOMER_EMAIL'];
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
		
		$dateInter = explode(" ", $inter['DATE_END']);
		$timeInter = $dateInter[1];
		$dateInter = $dateInter[0];
		
		$dateInter = explode("-", $dateInter);
		$dateInter = $dateInter[2]."/".$dateInter[1]."/".$dateInter[0];
		
		$timeInter = explode(":", $timeInter);
		$timeInter = $timeInter[0].":".$timeInter[1];
		
		$description = str_replace("\n","<br />\n",$inter['DESCRIPTION']);
		
		$message_txt = "
			Retrouvez en pièce jointe, votre copie du bon d'intervention N°".$inter['ID']." réalisée le ".$dateInter." à ".$timeInter.".\n
			Cette Intervention à été réalisée le ".$dateInter." à ".$timeInter." par ".$user['NAME'].".\n
			Celui-ci a réalisé :\n\n
			".$inter['DESCRIPTION']."\n\n
			Pour toutes demandes, merci de nous contacter par téléphone au 05 56 94 26 96 ou par mail à sav@tacteo-se.fr.\n
			Cordialement,\n\n
			L'équipe Tacteo.
		";
		$message_html = "
			<html style='width: 100%;margin: 0 auto; display: block; background: white'>
				<h1 style='text-align: center;'>
					Retrouvez en pièce jointe, votre copie du bon d'intervention N°".$inter['ID'].".
				</h1>
				
				<img id='ban' src='https://sav.tacteo.fr/img/logoBan.png' alt='SAV TACTEO' style='width: 50%; display: block; margin: 0 auto;'>
				
				<p style='width: 75%;display:block;margin:0 auto'>
					Cette Intervention à été réalisée le ".$dateInter." à ".$timeInter." par ".$user['NAME'].".<br />
					Celui-ci a réalisé :<br /><br />
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
		if(isset($_GET['SUBJECT']))
		{
			$sujet = str_replace("_", " ", $_GET['SUBJECT']);
		}
		else
		{
			$sujet = str_replace(".pdf", "", $name);
		}
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