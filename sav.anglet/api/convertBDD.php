// <?php
	// require('/home/tacteose/sav/INTER/ADMIN/inc/config.inc.php');
	// require('/home/tacteose/sav/INTER/ADMIN/inc/accounts.inc.php');
	
	// $bdd->query("TRUNCATE INTER") or die(print_r($bdd->errorInfo()));
	
	// $req = $bdd->query("
		// SELECT
			// *
		// FROM
			// INTERVENTIONS
		// ORDER BY
			// ID
		// "
	// ) or die(print_r($bdd->errorInfo()));
	
	// $query = "
	// INSERT INTO INTER (
		// ID,
		// DATE_UPLOADED,
		// DATE_LAST_MODIFY,
		// USER_UPLOAD,
		// USER_MODIFY,
		// DATE_START,
		// DATE_END,
		// AGENCY,
		// INSTALL,
		// MAINTENANCE,
		// TRAINING,
		// RECOVERY,
		// RENEWAL,
		// CUSTOMER_CODE,
		// CUSTOMER_LABEL,
		// CUSTOMER_PHONE,
		// CUSTOMER_CONTRACT,
		// CUSTOMER_NAME,
		// CUSTOMER_QUALITY,
		// CUSTOMER_EMAIL,
		// CUSTOMER_TRADER,
		// DESCRIPTION,
		// SOFTWARE_VERSION,
		// IS_FUNCTIONAL,
		// IS_NF525,
		// IS_UPDATED,
		// TRAINING_BACKUP_PERFORMED,
		// TRAVEL_LABEL,
		// TRAVEL_PRICE,
		// VAT_RATE,
		// IS_ARCHIVED,
		// CUSTOMER_ADDRESS_LINE1,
		// CUSTOMER_ADDRESS_LINE2,
		// CUSTOMER_ADDRESS_CITY,
		// CUSTOMER_ADDRESS_POSTAL_CODE,
		// CUSTOMER_CIVILITY,
		// CUSTOMER_SIGN,
		// LOANS,
		// WORKFORCE_HOUR_PRICE,
		// WORKFORCE_TYPE,
		// BILLINGS_THIRD
	// )
	// VALUES (
		// :ID,
		// :DATE,
		// :LAST_UPDATE,
		// :TECHNICIEN,
		// :UPDATED_BY,
		// :HEURE_ARRIVEE,
		// :HEURE_DEPART,
		// :AGENCE,
		// :INSTALLATION,
		// :MAINTENANCE,
		// :FORMATION,
		// :RECUPERATION,
		// :RENOUVELLEMENT,
		// :CODE_CLIENT,
		// :CLIENT,
		// :TELEPHONE,
		// :CONTRAT,
		// :SIGNATAIRE,
		// :QUALITE_SIGNATAIRE,
		// :EMAIL,
		// :COMMERCIAL,
		// :DESCRIPTION,
		// :VERSION,
		// :FONCTIONNEMENT,
		// :LOI_NF,
		// :MISE_A_JOUR,
		// :FORMATION_SAUVEGARDE,
		// :DEPLACEMENT,
		// :TRAVEL_PRICE,
		// :TVA,
		// :ARCHIVE,
		// :CUSTOMER_ADDRESS_LINE1,
		// :CUSTOMER_ADDRESS_LINE2,
		// :CUSTOMER_ADDRESS_CITY,
		// :CUSTOMER_ADDRESS_POSTAL_CODE,
		// :CUSTOMER_CIVILITY,
		// :CUSTOMER_SIGN,
		// :LOANS,
		// :WORKFORCE_HOUR_PRICE,
		// :WORKFORCE_TYPE,
		// :BILLINGS_THIRD
	// )";
	// $update = $bdd->prepare($query);	
	
	// while($old = $req->fetch())
	// {
		// $old = Address($old);
		// $old = Loans($old, $bdd);
		// $old = Billings($old, $bdd);
		// $old = Signature($old, $bdd);
		// $old = Travel_Label($old, $bdd);
		// $update->execute(array(
			// 'ID' 							=> $old['ID'],
			// 'DATE'							=> $old['DATE'],
			// 'LAST_UPDATE'					=> $old['LAST_UPDATE'],
			// 'TECHNICIEN'					=> $old['TECHNICIEN'],
			// 'UPDATED_BY'					=> $old['UPDATED_BY'],
			// 'HEURE_ARRIVEE'					=> explode(" ",$old['DATE'])[0]." ".$old['HEURE_ARRIVEE'],
			// 'HEURE_DEPART'					=> explode(" ",$old['DATE'])[0]." ".$old['HEURE_DEPART'],
			// 'AGENCE'						=> $old['AGENCE'],
			// 'INSTALLATION'					=> $old['INSTALLATION'],
			// 'MAINTENANCE'					=> $old['MAINTENANCE'],
			// 'FORMATION'						=> $old['FORMATION'],
			// 'RECUPERATION'					=> $old['RECUPERATION'],
			// 'RENOUVELLEMENT'				=> $old['RENOUVELLEMENT'],
			// 'CODE_CLIENT'					=> $old['CODE_CLIENT'],
			// 'CLIENT'						=> $old['CLIENT'],
			// 'TELEPHONE'						=> $old['TELEPHONE'],
			// 'CONTRAT'						=> $old['CONTRAT'],
			// 'SIGNATAIRE'					=> $old['SIGNATAIRE'],
			// 'QUALITE_SIGNATAIRE'			=> $old['QUALITE_SIGNATAIRE'],
			// 'EMAIL'							=> $old['EMAIL'],
			// 'COMMERCIAL'					=> $old['COMMERCIAL'],
			// 'DESCRIPTION'					=> $old['DESCRIPTION'].$old['COMPLEMENTS'],
			// 'VERSION'						=> $old['VERSION'],
			// 'FONCTIONNEMENT'				=> $old['FONCTIONNEMENT'],
			// 'LOI_NF'						=> $old['LOI_NF'],
			// 'MISE_A_JOUR'					=> $old['MISE_A_JOUR'],
			// 'FORMATION_SAUVEGARDE'			=> $old['FORMATION_SAUVEGARDE'],
			// 'DEPLACEMENT'					=> $old['TRAVEL_LABEL'],
			// 'TRAVEL_PRICE'					=> $old['TRAVEL_PRICE'],
			// 'TVA'							=> $old['TVA'],
			// 'ARCHIVE'						=> $old['ARCHIVE'],
			// 'CUSTOMER_ADDRESS_LINE1'		=> $old['CUSTOMER_ADDRESS_LINE1'],
			// 'CUSTOMER_ADDRESS_LINE2'		=> $old['CUSTOMER_ADDRESS_LINE2'],
			// 'CUSTOMER_ADDRESS_CITY'			=> $old['CUSTOMER_ADDRESS_CITY'],
			// 'CUSTOMER_ADDRESS_POSTAL_CODE'	=> $old['CUSTOMER_ADDRESS_POSTAL_CODE'],
			// 'CUSTOMER_SIGN'					=> $old['CUSTOMER_SIGN'],
			// 'CUSTOMER_CIVILITY'				=> "Monsieurs/Madame",
			// 'LOANS'							=> $old['LOANS'],
			// 'WORKFORCE_HOUR_PRICE'			=> 50.00,
			// 'WORKFORCE_TYPE'				=> 0,
			// 'BILLINGS_THIRD'				=> $old['BILLINGS_THIRD']
		// )) or die(print_r($update->errorInfo()));
	// }
	
	// function Address($old)
	// {
		// $address = explode("\n",$old['ADRESSE']);
		
		// $old['CUSTOMER_ADDRESS_LINE1'] = $address[0];
		// $old['CUSTOMER_ADDRESS_LINE2'] = $address[1];
		// $old['CUSTOMER_ADDRESS_POSTAL_CODE'] = explode(" ", $address[2])[0];
		// $old['CUSTOMER_ADDRESS_CITY'] = explode(" ", $address[2], 2)[1];
		
		// return $old;
	// }
	
	// function Loans($old,$bdd)
	// {
		// $materials = explode("\n",$old['MATERIELS']);
		// $serials = explode("\n",$old['NUMEROS_SERIES']);

		// if($old['NUMEROS_SERIES'] != "")
		// {
			// for($x = 0; $x < sizeOf($serials); $x++)
			// {
				// $loans->$x['id'] = "";
				// $loans->$x['name'] = $materials[$x];
				// $loans->$x['serial'] = $serials[$x];
			// }
		// }

		// $old['LOANS'] = json_encode($loans);

		// return $old;
	// }
	
	// function Billings($old, $bdd)
	// {
		// $y = 0;
		// for($x = 1; $x != 6; $x++)
		// {
			// if(is_integer($old['FACTU'.$x]) && !empty($old['FACTU'.$x]))
			// {
				// $req = $bdd->prepare("
					// SELECT
						// *
					// FROM
						// PRODUITS
					// WHERE
						// LIBELLE = :name
					// ") or die(print_r($bdd->errorInfo()));
				// $req->execute(array('name' => $old['FACTU'.$x]));
				// $product = $req->fetch(2);
			// }
			// else if(!empty($old['FACTU'.$x]))
			// {
				// $req = $bdd->prepare("
					// SELECT
						// *
					// FROM
						// PRODUITS
					// WHERE
						// ID = :id
					// ") or die(print_r($bdd->errorInfo()));
				// $req->execute(array('id' => $old['FACTU'.$x]));
				// $product = $req->fetch(2);
			// }
			
			// if(!empty($product))
			// {
				// $billing->$y['id'] =			$product['CODE'];
				// $billing->$y['name'] =			$product['LIBELLE'];
				// $billing->$y['quantity'] =		1;
				// $billing->$y['nbDiscount'] =	1;
				// $billing->$y['puht'] =			$product['PVHT'];
				// $billing->$y['discount'] =		$old['REMISE'.$x];
				// $y++;
			// }
			// $old['BILLINGS_THIRD'] =			json_encode($billing);
		// }
		// return $old;
	// }

	// function Travel_Label($old, $bdd)
	// {
		// $req = $bdd->query("
			// SELECT
				// *
			// FROM
				// TARIFS
			// WHERE
				// ID = 1");
		// $tarif_label = $req->fetch(2);
		
		// if($old['TRAVEL_PRICE'] == 90)
		// {
			// $old['TRAVEL_LABEL'] = $tarif_label["ZONE1"];
		// }
		// else if($old['TRAVEL_PRICE'] == 150)
		// {
			// $old['TRAVEL_LABEL'] = $tarif_label["ZONE2"];
		// }
		// else
		// {
			// $old['TRAVEL_LABEL'] = "";
		// }
		
		// return $old;
	// }

	// function Signature($old, $bdd)
	// {
		// $path = $old['SIGNATURE'];
		// $type = pathinfo($path, PATHINFO_EXTENSION);
		// $data = file_get_contents($path);
		// $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		
		// $old['CUSTOMER_SIGN'] = $base64;
		
		// $path = "../signatures/TECHS/".$old['TECHNICIEN'].".png";
		// $type = pathinfo($path, PATHINFO_EXTENSION);
		// $data = file_get_contents($path);
		// $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		
		// $bdd->query("UPDATE USERS_INTER SET SIGN = '".$base64."' WHERE ID = '".$old['TECHNICIEN']."'") or die(print_r($bdd->errorInfo()));
		// return($old);
	// }

	// die();
// ?>