<?php
	//require_once('inc/login.inc.php');
	require_once(__DIR__ . '/intergen.inc.php');
	require_once(__DIR__ . '/../sellsy/sellsy.php');
	require_once (__DIR__ . '/../../vendor/autoload.php');

	$mpdf = new \Mpdf\Mpdf([
		'default_font_size' => 6,
		'margin_top' => 30,
		'margin_bottom' => 30
		]
	);

	// Get the types of the voucher for the subject
	$interTypes = array();

	if($_INTER['INSTALL'])
	{
		$interTypes[] = 'INSTALLATION';
	}
	if($_INTER['MAINTENANCE'])
	{
		$interTypes[] = 'MAINTENANCE';
	}
	if($_INTER['TRAINING'])
	{
		$interTypes[] = 'FORMATION';
	}
	if($_INTER['RENEWAL'])
	{
		$interTypes[] = 'RENOUVELLEMENT';
	}
	if($_INTER['RECOVERY'])
	{
		$interTypes[] = 'RECUPERATION';
	}
	if($_INTER['DELIVERY'])
	{
		$interTypes[] = 'LIVRAISON';
	}
	if($_INTER['NEAR_VISIT'])
	{
		$interTypes[] = 'PRE-VISITE';
	}
	if(count($interTypes) > 1)
	{
		$interType = $interTypes[0];
		for($x = 1; $x < count($interTypes); $x++)
		{
			$interType .= ', '.$interTypes[$x];
		}
	}
	else
	{
		$interType = $interTypes[0];
	}
	$name = "[".$interType."] Intervention " . $_INTER['ID'] . " " . $_INTER['CUSTOMER']['LABEL'] . " (" . $_INTER['CUSTOMER']['CODE'] . ") " . $_INTER['DATE_END'];

	$mpdf->showImageErrors = true;
	$mpdf->WriteHTML($html['VOUCHER']);
	$content['Bon d\'Intervention '.$name] = $mpdf->Output('', 'S');
	if(!empty($html['BILLINGS_THIRD']))
	{
		$mpdf = new \Mpdf\Mpdf([
			'default_font_size' => 6,
			'margin_top' => 30,
			'margin_bottom' => 30
			]
		);
		$mpdf->showImageErrors = true;
		$mpdf->WriteHTML($html['BILLINGS_THIRD']);
		$content['Proforma'] = $mpdf->Output('', 'S');
	}
	if(!empty($html['ATTACHMENTS']))
	{
		$mpdf = new \Mpdf\Mpdf([
			'default_font_size' => 6,
			'margin_top' => 30,
			'margin_bottom' => 30
			]
		);
		$mpdf->showImageErrors = true;
		$mpdf->WriteHTML($html['ATTACHMENTS']);
		$content['Pièce(s) Jointe(s)'] = $mpdf->Output('', 'S');
	}
	if(!empty($_INTER['PAPERWORKS']) && $_INTER['PAPERWORKS'] != '{}')
	{
		$paperworks = JSON_decode($_INTER['PAPERWORKS']);
		for($x = 0; $x < count($paperworks); $x++)
		{
			$mpdf = new \Mpdf\Mpdf([
				'default_font_size' => 6,
				'margin_top' => 30,
				'margin_bottom' => 30
				]
			);
			$mpdf->showImageErrors = true;
			$mpdf->WriteHTML('<!--mpdf<sethtmlpageheader name="myheader" value="off" show-this-page="1" />mpdf-->');
			$mpdf->WriteHTML('<!--mpdf<htmlpagefooter name="myfooter">
					<div style="border-top: 0.5mm solid #000000; font-size: 5pt; text-align: center; padding-top: 3mm; ">
						'.$_ESTABLISHMENT['END_TEXT'].'<br /><br />
						<span style="font-size:8pt">'.$paperworks[$x][0].'</span><br />
						<span style="font-size:8pt">{PAGENO} / {nbpg}</span>
					</div>
				</htmlpagefooter>
				<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
				<sethtmlpagefooter name="myfooter" value="on" />
			mpdf-->');
			$mpdf->WriteHTML($paperworks[$x][1]);
			$content[$paperworks[$x][0]] = $mpdf->Output('', 'S');
		}
	}

	// Preparing the email

	// =====Récupération des emails établissement.
	$req = $GLOBALS['bdd']->query("SELECT * FROM SETTINGS WHERE NAME = 'MAILING'") or die(print_r($GLOBALS['bdd']->errorInfo()));
	$req = $req->fetch();
	$res = $req['VALUE'].','.$_INTER['TECHNICIAN']['MAIL'];

	$mailBcc = explode(',',$res);

	// =====Récupération de l'email du client.
	$mail = $_INTER['CUSTOMER']['EMAIL'];

	//define mail subject
	$subject = "[".$interType."] Intervention " . $_INTER['ID'] . " " . $_INTER['CUSTOMER']['LABEL'] . " (" . $_INTER['CUSTOMER']['CODE'] . ") " . $_INTER['DATE_END'];
	//define mail body text
	$bodyText =
"Retrouvez en pièce jointe, votre copie du bon d'intervention N°".$_INTER['ID'].".
Cette Intervention à été réalisée le ".explode(' ',$_INTER['DATE_START'])[0]." à ".explode(' ',$_INTER['DATE_START'])[1]." par ".$_INTER['TECHNICIAN']['NAME'].".
Celui-ci a réalisé :
".$_INTER['DESCRIPTION']."
Pour toutes demandes, merci de nous contacter par téléphone au 05 64 11 58 18 ou par mail à tacteo-anglet@tacteo-se.fr.

Cordialement,
L'équipe Tacteo.";
	//define mail body html
	$bodyHtml = "<html style='width: 100%;margin: 0 auto; display: block; background: white'>
				<h1 style='text-align: center;'>
					Retrouvez en pièce jointe, votre copie du bon d'intervention N°".$_INTER['ID'].".
				</h1>

				<img id='ban' src='https://sav.tacteo.fr/img/logoBan.png' alt='SAV TACTEO' style='width: 50%; display: block; margin: 0 auto;'>

				<p style='width: 75%;display:block;margin:0 auto'>
					Cette Intervention à été réalisée le ".explode(' ',$_INTER['DATE_START'])[0]." à ".explode(' ',$_INTER['DATE_START'])[1]." par ".$_INTER['TECHNICIAN']['NAME'].".<br />
					Celui-ci a réalisé :<br /><br />
					".str_replace("\n","<br />\n",$_INTER['DESCRIPTION'])."<br /><br />
					Pour toutes demandes, merci de nous contacter par téléphone au 05 64 11 58 18 ou par mail à <a href='mailto:tacteo-anglet@tacteo-se.fr'>tacteo-anglet@tacteo-se.fr</a>.<br />
					Cordialement,<br /><br />
					<i>L'équipe Tacteo.</i>
				</p>
			</html>";

	//send mail
	sendMail($content, $subject, $mail, $mailBcc, $bodyHtml, $bodyText);

	function sendMail($content, $subject, $mail, $bcc = NULL, $bodyHtml = '', $bodyText = '')
	{

		$message = (new Swift_Message($subject))
			->setFrom(array('no-reply@tacteo-se.fr' => 'Tacteo SE Anglet'))
			->setTo(array($mail))
			->setBcc($bcc)
			->setBody($bodyText)
			->addPart($bodyHtml, 'text/html');
			foreach($content as $key => $attachment) {
				$message->attach(new Swift_Attachment($attachment, $key.'.pdf', 'application/pdf'));
			}

		// Create the transport
		$transport = new Swift_SendmailTransport('/usr/sbin/sendmail -bs');
		// Create the Mailer using created Transport
		$mailer = new Swift_Mailer($transport);
		// Send the created message
		$mailer->send($message);
	}
?>
