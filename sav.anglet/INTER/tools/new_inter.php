<?php
	require('../inc/accounts.inc.php');
	
	$upload = 'https://docs.google.com/forms/d/e/1FAIpQLScZSmU4vlVtPDONliKSud6PG41AvvGqiq7u_KCVrKLWiahxIg/formResponse';
	
	$req = $bdd->query("SELECT * FROM TARIFS");
	$tarif = $req->fetchAll(2) or die(print_r($req->errorInfo()));
	
	if(empty($_POST))
	{
		header('Location: /');
		exit;
	}
	
	$TVA							=	$tarif[0]['TVA1'];
	
	$WORKFORCE						=	$tarif[0]['WORKFORCE'];
	
	$DATE							=	date("Y-m-d H:i:s");
	
	$AGENCE							=	$_POST['entry_433474814'];

	$OBJET							=	$_POST['entry_912488796'];
	
	$OBJETS							=	explode(',', $OBJET);
	
	$INSTALLATION					=	is_int(array_search('INSTALLATION', $OBJETS));
	$MAINTENANCE					=	is_int(array_search('MAINTENANCE', $OBJETS));
	$FORMATION						=	is_int(array_search('FORMATION', $OBJETS));
	$RENOUVELLEMENT					=	is_int(array_search('RENOUVELLEMENT', $OBJETS));
	$RECUPERATION					=	is_int(array_search('RECUPERATION MATERIELS', $OBJETS));
	
	$DESCRIPTION					=	$_POST['entry_2041036538'];
	$COMPLEMENTS					=	$_POST['entry_1250423481'];

	$PRET_MATERIEL					=	$_POST['entry_2094846173'];
	$MATERIELS						=	$_POST['entry_2047563043'];
	$NUMEROS_SERIES					=	$_POST['entry_2082384825'];

	$MISE_A_JOUR					=	$_POST['entry_509496568'];
	$VERSION						=	$_POST['entry_1389074181'];

	$NOM_SIGNATAIRE					=	$_POST['entry_1445723676'];
	$QUALITE_SIGNATAIRE				=	$_POST['entry_184632626'];
	$SIGNATURE_CLIENT				=	$_POST['entry_1142705552'];

	$FACTU1							=	$_POST['entry_414412799'];
	$FACTU2							=	$_POST['entry_1798627640'];
	$FACTU3							=	$_POST['entry_468083007'];
	$FACTU4							=	$_POST['entry_1018844855'];
	$FACTU5							=	$_POST['entry_2001038834'];
	$FACTU6							=	$_POST['entry_1125778919'];

	$CLIENT							=	$_POST['entry_532586694'];
	
	$CODE_CLIENT					=	$_POST['entry_845891913'];
	$ADRESSE						=	$_POST['entry_1229884392'];
	$TELEPHONE						=	$_POST['entry_1886220397'];
	$EMAIL							=	$_POST['entry_19991430'];
	$CONTRAT_DE_MAINTENANCE			=	$_POST['entry_2021019270'];
	$CODE_COMMERCIAL				=	$_POST['entry_1384966339'];

	$TEST_FONCTIONNEMENT			=	$_POST['entry_246656631'];
	$FORMATION_SAUVEGARDE			=	$_POST['entry_1885583728'];
	$LOI							=	$_POST['entry_1676268250'];
	$DEPLACEMENT					=	$_POST['entry_214594423'];

	$NOM_DU_TECHNICIEN				=	$_POST['entry_1981179687'];
	$SIGNATURE_TECHNICIEN			=	$_POST['entry_117482882'];

	$INTER_NUMERO					=	$_POST['entry_1203314954'];

	$FACTU1_PRIX					=	$_POST['entry_1896435851'];
	$FACTU2_PRIX					=	$_POST['entry_210623750'];
	$FACTU3_PRIX					=	$_POST['entry_1185482882'];
	$FACTU4_PRIX					=	$_POST['entry_740985120'];
	$FACTU5_PRIX					=	$_POST['entry_1586073318'];
	$FACTU6_PRIX					=	$_POST['entry_1040022259'];

	$FACTU_TOTAL					=	floatval($FACTU1_PRIX) + floatval($FACTU2_PRIX) + floatval($FACTU3_PRIX) + floatval($FACTU4_PRIX) + floatval($FACTU5_PRIX) + floatval($FACTU6_PRIX);
	
	$HEURE_ARRIVEE_hour				=	$_POST['entry_1667284575_hour'];
	$HEURE_ARRIVEE_minute			=	$_POST['entry_1667284575_minute'];
	$HEURE_DEPART_hour				=	$_POST['entry_1131156718_hour'];
	$HEURE_DEPART_minute			=	$_POST['entry_1131156718_minute'];

	$HEURE_ARRIVEE					=	$HEURE_ARRIVEE_hour . ":" . $HEURE_ARRIVEE_minute . ":00";
	$HEURE_DEPART					=	$HEURE_DEPART_hour . ":" . $HEURE_DEPART_minute . ":00";
	
	$REMISE1						=	$_POST['entry_1180815520'];
	$REMISE2						=	$_POST['entry_622528202'];
	$REMISE3						=	$_POST['entry_166947705'];
	$REMISE4						=	$_POST['entry_1756883059'];
	$REMISE5						=	$_POST['entry_1202409190'];
	$REMISE6						=	$_POST['entry_839151629'];
	
	$content = http_build_query (array (
		'entry.433474814'			=>	$AGENCE,

		'entry.912488796'			=>	$OBJET,

		'entry.2041036538'			=>	$DESCRIPTION,
		'entry.1250423481'			=>	$COMPLEMENTS,

		'entry.2094846173'			=>	$PRET_MATERIEL,
		'entry.2047563043'			=>	$MATERIELS,
		'entry.2082384825'			=>	$NUMEROS_SERIES,

		'entry.509496568'			=>	$MISE_A_JOUR,
		'entry.1389074181'			=>	$VERSION,

		'entry.1445723676'			=>	$NOM_SIGNATAIRE,
		'entry.184632626'			=>	$QUALITE_SIGNATAIRE,
		'entry.1142705552'			=>	$SIGNATURE_CLIENT,
		
		'entry.414412799'			=>	$FACTU1,
		'entry.1798627640'			=>	$FACTU2,
		'entry.468083007'			=>	$FACTU3,
		'entry.1018844855'			=>	$FACTU4,
		'entry.2001038834'			=>	$FACTU5,
		'entry.1125778919'			=>	$FACTU6,

		'entry.532586694'			=>	$CLIENT,
		'entry.845891913'			=>	$CODE_CLIENT,
		'entry.1229884392'			=>	$ADRESSE,
		'entry.1886220397'			=>	$TELEPHONE,
		'entry.19991430'			=>	$EMAIL,
		'entry.2021019270'			=>	$CONTRAT_DE_MAINTENANCE,
		'entry.1384966339'			=>	$CODE_COMMERCIAL,

		'entry.246656631'			=>	$TEST_FONCTIONNEMENT,
		'entry.1885583728'			=>	$FORMATION_SAUVEGARDE,
		'entry.1676268250'			=>	$LOI,
		'entry.214594423'			=>	$DEPLACEMENT,

		'entry.1981179687'			=>	$NOM_DU_TECHNICIEN,
		'entry.117482882'			=>	$SIGNATURE_TECHNICIEN,
		
		'entry.1203314954'			=>	$INTER_NUMERO,
		
		'entry.1896435851'			=>	$FACTU1_PRIX,
		'entry.210623750'			=>	$FACTU2_PRIX,
		'entry.1185482882'			=>	$FACTU3_PRIX,
		'entry.740985120'			=>	$FACTU4_PRIX,
		'entry.1586073318'			=>	$FACTU5_PRIX,
		'entry.1040022259'			=>	$FACTU6_PRIX,
		
		'entry.1667284575_hour'		=>	$HEURE_ARRIVEE_hour,
		'entry.1667284575_minute'	=>	$HEURE_ARRIVEE_minute,
		'entry.1131156718_hour'		=>	$HEURE_DEPART_hour,
		'entry.1131156718_minute'	=>	$HEURE_DEPART_minute,

		'entry.1180815520'			=>	$REMISE1,
		'entry.622528202'			=>	$REMISE2,
		'entry.166947705'			=>	$REMISE3,
		'entry.1756883059'			=>	$REMISE4,
		'entry.1202409190'			=>	$REMISE5,
		'entry.839151629'			=>	$REMISE6
	));
	
	$req = $bdd->prepare("INSERT INTO INTERVENTIONS (
			UPDATED_BY,
			DATE,
			INSTALLATION,
			MAINTENANCE,
			FORMATION,
			RENOUVELLEMENT,
			RECUPERATION,
			TECHNICIEN,
			CLIENT,
			ADRESSE,
			TELEPHONE,
			EMAIL,
			CONTRAT,
			BILLING,
			TVA,
			WORKFORCE,
			AGENCE,
			HEURE_ARRIVEE,
			HEURE_DEPART,
			DESCRIPTION,
			DEPLACEMENT,
			TRAVEL_PRICE,
			PRET,
			MATERIELS,
			NUMEROS_SERIES,
			FONCTIONNEMENT,
			FORMATION_SAUVEGARDE,
			LOI_NF,
			VERSION,
			MISE_A_JOUR,
			COMPLEMENTS,
			SIGNATAIRE,
			QUALITE_SIGNATAIRE,
			CODE_CLIENT,
			SIGNATURE,
			COMMERCIAL,
			ATTENTE,
			FACTU1,
			FACTU2,
			FACTU3,
			FACTU4,
			FACTU5,
			FACTU6,
			FACTU_PRIX1,
			FACTU_PRIX2,
			FACTU_PRIX3,
			FACTU_PRIX4,
			FACTU_PRIX5,
			FACTU_PRIX6,
			REMISE1,
			REMISE2,
			REMISE3,
			REMISE4,
			REMISE5,
			REMISE6
		)
		
		VALUES (
			:UPDATED_BY,
			:DATE,
			:INSTALLATION,
			:MAINTENANCE,
			:FORMATION,
			:RENOUVELLEMENT,
			:RECUPERATION,
			:TECHNICIEN,
			:CLIENT,
			:ADRESSE,
			:TELEPHONE,
			:EMAIL,
			:CONTRAT,
			:BILLING,
			:TVA,
			:WORKFORCE,
			:AGENCE,
			:HEURE_ARRIVEE,
			:HEURE_DEPART,
			:DESCRIPTION,
			:DEPLACEMENT,
			:TRAVEL_PRICE,
			:PRET,
			:MATERIELS,
			:NUMEROS_SERIES,
			:FONCTIONNEMENT,
			:FORMATION_SAUVEGARDE,
			:LOI_NF,
			:VERSION,
			:MISE_A_JOUR,
			:COMPLEMENTS,
			:SIGNATAIRE,
			:QUALITE_SIGNATAIRE,
			:CODE_CLIENT,
			:SIGNATURE,
			:COMMERCIAL,
			:ATTENTE,
			:FACTU1,
			:FACTU2,
			:FACTU3,
			:FACTU4,
			:FACTU5,
			:FACTU6,
			:FACTU1_PRIX,
			:FACTU2_PRIX,
			:FACTU3_PRIX,
			:FACTU4_PRIX,
			:FACTU5_PRIX,
			:FACTU6_PRIX,
			:REMISE1,
			:REMISE2,
			:REMISE3,
			:REMISE4,
			:REMISE5,
			:REMISE6
		)"
	); //BDD PREPARE END
	
	if($TEST_FONCTIONNEMENT == "Positif")
	{
		$TEST_FONCTIONNEMENT = 1;
	}
	else if($TEST_FONCTIONNEMENT == "Négatif")
	{
		$TEST_FONCTIONNEMENT = 0;
	}
	else
	{
		$TEST_FONCTIONNEMENT = -1;
	}
	
	if($FORMATION_SAUVEGARDE == "Oui")
	{
		$FORMATION_SAUVEGARDE = 1;
	}
	else if($FORMATION_SAUVEGARDE == "Non")
	{
		$FORMATION_SAUVEGARDE = 0;
	}
	else
	{
		$FORMATION_SAUVEGARDE = -1;
	}
	
	if($DEPLACEMENT == "ZONE1")
	{
		$DEPLACEMENT = 0;
	}
	else if($DEPLACEMENT == "ZONE2")
	{
		$DEPLACEMENT = 1;
	}
	else
	{
		$DEPLACEMENT = -1;
	}
	
	switch($DEPLACEMENT){
	case 0:
		$TRAVEL_PRICE			=	$tarif[0]['TRAVEL_PRICE'];
		break;
	case 1:
		$TRAVEL_PRICE			=	$tarif[1]['TRAVEL_PRICE'];
		break;
	default:
		$TRAVEL_PRICE			=	"0";
		break;
	}
	
	if($PRET_MATERIEL == "Oui")
	{
		$PRET_MATERIEL = 1;
	}
	else
	{
		$PRET_MATERIEL = 0;
	}
	
	if($LOI == "Oui")
	{
		$LOI = 1;
	}
	else
	{
		$LOI = 0;
	}
	
	if($CONTRAT_DE_MAINTENANCE == "Oui")
	{
		$CONTRAT_DE_MAINTENANCE = 1;
	}
	else
	{
		$CONTRAT_DE_MAINTENANCE = 0;
	}
	
	if($MISE_A_JOUR == "Réalisée")
	{
		$MISE_A_JOUR = 1;
	}
	else
	{
		$MISE_A_JOUR = 0;
	}
	
	switch($CONTRAT_DE_MAINTENANCE){
		case "1":
			$BILLING = round($FACTU_TOTAL,2);
			break;
		default:
			$BILLING = round((calcHour() + $TRAVEL_PRICE + $FACTU_TOTAL),2);
	}
	
	function calcHour(){
		$a[0] = $HEURE_ARRIVEE_hour;
		$a[1] = $HEURE_ARRIVEE_minute;
		
		$d[0] = $HEURE_DEPART_hour;
		$d[1] = $HEURE_DEPART_minute;
	
		$ma = $a[1] + ($a[0]*60); //minutes arrivée
		$md = $d[1] + ($d[0]*60); //minutes départ
		
		$tm = $md - $ma; //temps minutes
		$c = round(($tm/60),2); //coefficent de calcul
		return($c);
	}
	
	$send = array(
			'UPDATED_BY'			=>	"0",
			'DATE'					=>	$DATE,
			'INSTALLATION'			=>	$INSTALLATION,
			'MAINTENANCE'			=>	$MAINTENANCE,
			'FORMATION'				=>	$FORMATION,
			'RENOUVELLEMENT'		=>	$RENOUVELLEMENT,
			'RECUPERATION'			=>	$RECUPERATION,
			'TECHNICIEN'			=>	intval($_SESSION['id']),
			'CLIENT'				=>	$CLIENT,
			'ADRESSE'				=>	$ADRESSE,
			'TELEPHONE'				=>	$TELEPHONE,
			'EMAIL'					=>	$EMAIL,
			'CONTRAT'				=>	$CONTRAT_DE_MAINTENANCE,
			'BILLING'				=>	$BILLING,
			'TVA'					=>	$TVA,
			'WORKFORCE'				=>	$WORKFORCE,
			'AGENCE'				=>	$AGENCE,
			'HEURE_ARRIVEE'			=>	$HEURE_ARRIVEE,
			'HEURE_DEPART'			=>	$HEURE_DEPART,
			'DESCRIPTION'			=>	$DESCRIPTION,
			'DEPLACEMENT'			=>	$DEPLACEMENT,
			'TRAVEL_PRICE'			=>	$TRAVEL_PRICE,
			'PRET'					=>	$PRET_MATERIEL,
			'MATERIELS'				=>	$MATERIELS,
			'NUMEROS_SERIES'		=>	$NUMEROS_SERIES,
			'FONCTIONNEMENT'		=>	$TEST_FONCTIONNEMENT,
			'FORMATION_SAUVEGARDE'	=>	$FORMATION_SAUVEGARDE,
			'LOI_NF'				=>	$LOI,
			'VERSION'				=>	$VERSION,
			'MISE_A_JOUR'			=>	$MISE_A_JOUR,
			'COMPLEMENTS'			=>	$COMPLEMENTS,
			'SIGNATAIRE'			=>	$NOM_SIGNATAIRE,
			'QUALITE_SIGNATAIRE'	=>	$QUALITE_SIGNATAIRE,
			'CODE_CLIENT'			=>	$CODE_CLIENT,
			'SIGNATURE'				=>	$SIGNATURE_CLIENT,
			'COMMERCIAL'			=>	$CODE_COMMERCIAL,
			'ATTENTE'				=>	$ATTENTE,
			'FACTU1'				=>	$FACTU1,
			'FACTU2'				=>	$FACTU2,
			'FACTU3'				=>	$FACTU3,
			'FACTU4'				=>	$FACTU4,
			'FACTU5'				=>	$FACTU5,
			'FACTU6'				=>	$FACTU6,
			'FACTU1_PRIX'			=>	$FACTU1_PRIX,
			'FACTU2_PRIX'			=>	$FACTU2_PRIX,
			'FACTU3_PRIX'			=>	$FACTU3_PRIX,
			'FACTU4_PRIX'			=>	$FACTU4_PRIX,
			'FACTU5_PRIX'			=>	$FACTU5_PRIX,
			'FACTU6_PRIX'			=>	$FACTU6_PRIX,
			'REMISE1'				=>	$REMISE1,
			'REMISE2'				=>	$REMISE2,
			'REMISE3'				=>	$REMISE3,
			'REMISE4'				=>	$REMISE4,
			'REMISE5'				=>	$REMISE5,
			'REMISE6'				=>	$REMISE6
		);
	$req->execute($send);
	
	$req = $bdd->query("SELECT * FROM INTERVENTIONS ORDER BY ID DESC");
	$inter = $req->fetch();
?>

<script src="/INTER/ADMIN/scripts/jQuery.min.js"></script>
<script>
	$.post({
		url: "../ADMIN/widgets/view_inter.php?pdf=1",
		data: {
			ID: "<?=$inter['ID']?>",
			SENDMAIL: true
		}
	});
	
	window.parent.show('SENDED');
	// window.parent.location.href = '../ADMIN/widgets/view_inter.php?inter=<?=$inter['ID']?>&send=1';
</script>