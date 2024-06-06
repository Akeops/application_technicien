<?php
include("xmlrpc.inc");
include("xmlrpcs.inc");
include("xmlrpc_wrappers.inc");

//GetListeCategories()
//AddOrUpadteProduit()
//UpdateStock();
//RecupereCommande();


define('__DEBUG_SMAIN', true);

//Recuperation d'un client.
$GetListeCategories_sig=array(array($xmlrpcStruct));
$GetListeCategories_doc='Retourne liste catégories';

function GetListeCategories($m)
{
	global $xmlrpcerruser;

	$n = php_xmlrpc_decode($m);


	$ValeurRetour=array();
	$Categorie=array();
	try {
		$CompteurCategorie=5;
		if ($CompteurCategorie > 0) {
			For ($i=1;$i<=$CompteurCategorie;$i++) {
				$Categorie['ID']='1'.$i;
				$Categorie['LIBELLE']='ESSAI '.$i;
				$Categorie['PARENT_ID']='1'.$i.'1';
				$ValeurRetour[]=$Categorie;
			}
		}
		else {
			$ValeurRetour['AUCUNE_CATEGORIE']='1';
		}
		if (__DEBUG_SMAIN) error_log("Essai RetourListeCategories : ".print_r($ValeurRetour,true),3,"log.txt");
		return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));

	}
	catch (Exception $Ex){
		$ValeurRetour['EXCEPTION']=$Ex->message();
		if (__DEBUG_SMAIN) error_log("EXCEPTION : ".print_r($ValeurRetour,true),3,"log.txt");
		return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));
	}
}

$AddOrUpdateAttribut_sig=array(array($xmlrpcStruct, $xmlrpcStruct));
$AddOrUpdateAttribut_doc="Mise à jour d'un attribut.";

function AddOrUpdateAttribut($m)
{
	global $xmlrpcerruser;
	$TabParametre=array();

	$n = php_xmlrpc_decode($m);
	$TabParametre=$n[0];

	//$TabParametre=utf8_encode_array($TabParametre);
	$ValeurRetour=array();


	if (!isset($TabParametre['ATTRIBUT'])) {
		$ValeurRetour['ATTRIBUT_NON_RENSEIGNE']='1';
		return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));
	}
	if (!isset($TabParametre['CODE'])) {
		$ValeurRetour['CODE_NON_RENSEIGNE']='1';
		return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));
	}
	if (__DEBUG_SMAIN) error_log("TabParametre : ".print_r($TabParametre,true),3,"log.txt");

	try {
		$attribut=$TabParametre['ATTRIBUT'];
		$code=$TabParametre['CODE'];
		$libelle='';
		if (isset($TabParametre['LIBELLE'])) $libelle=$TabParametre['LIBELLE'];

		$ValeurRetour['MISE_A_JOUR_OK']=$code;
        return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));

	}
	catch (Exception $Ex){
	 $ValeurRetour['EXCEPTION']=$Ex->message();
	 if (__DEBUG_SMAIN) error_log("EXCEPTION : ".print_r($ValeurRetour,true),3,"log.txt");
	 return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));
	}
}


$AddOrUpdateProduit_sig=array(array($xmlrpcStruct, $xmlrpcStruct));
$AddOrUpdateProduit_doc="Ajout ou mise à jour d'un produit.";
/* Format du fichier produit */
/*
CODE (Char[13])													-> Code du produit.(Maximum 13 caractères).
LIBELLE (char[100])												-> Désignation du produit.
DESCRIPTION (blob)                                              -> Description longue du produit
DESCRIPTION_COURTE(blob)                                        -> Description courte du produit
CODE_RAYON (entier)												-> Code du rayon auquel est affecté le produit.
LIBELLE_RAYON (char(40))                                        -> Libellé du rayon
CODE_FAMILLE	(entier)										-> Code de la famille auquel le produit est affecté.
LIBELLE_FAMILLE (char(40))                                      -> Libellé de la famille
CODE_LIGNE	(entier)											-> Code de la ligne auquel le produit est affecté.
LIBELLE_LIGNE (char(40))                                        -> Libellé de la ligne
CODE_MARQUE (char[8])											-> Code de la marque auquel le produit est affecté.
LIBELLE_MARQUE (char(40))                                       -> Libellé de la marque
CATEGORIES (Tableau d’entier court)								-> Catégories supplémentaires (Affectation des catégories déclarées sur le site).
PAHTNET (Numérique)												-> Prix d’achat du produit.
PVHT (Numérique)                                                -> Prix de vente Hors taxe du produit
CODE_TVA (entier)                                               -> Code de la TVA affecté au produit
NOM_TVA  (char(40))                                             -> Libellé de la TVA affecté au produit
TAUX_TVA (Numérique)                                            -> Taux de la TVA affecté au produit
PVTTC (Numérique)												-> Prix de vente du produit.
TARIF1HT (Numérique)                                            -> Prix de vente HT du produit pour le tarif 1
TARIF1CODE_TVA (entier)                                         -> Code TVA affecté au produit pour le tarif 1
TARIF1_NOM_TVA (char(40))                                       -> Nom de la TVA affecté au produit pour le tarif 1
TARIF1_TAUX_TVA (Numérique)                                     -> Taux de TVA affecté au produit pour le tarif 1
TARIF1TTC (Numérique)                                           -> Prix de vente TTC du produit pour le tarif 1
TARIF2HT (Numérique)                                            -> Prix de vente HT du produit pour le tarif 2
TARIF2CODE_TVA (entier)                                         -> Code TVA affecté au produit pour le tarif 2
TARIF2_NOM_TVA (char(40))                                       -> Nom de la TVA affecté au produit pour le tarif 2
TARIF2_TAUX_TVA (Numérique)                                     -> Taux de TVA affecté au produit pour le tarif 2
TARIF2TTC (Numérique)                                           -> Prix de vente TTC du produit pour le tarif 2
TARIF3HT (Numérique)                                            -> Prix de vente HT du produit pour le tarif 3
TARIF3CODE_TVA (entier)                                         -> Code TVA affecté au produit pour le tarif 3
TARIF3_NOM_TVA (char(40))                                       -> Nom de la TVA affecté au produit pour le tarif 3
TARIF3_TAUX_TVA (Numérique)                                     -> Taux de TVA affecté au produit pour le tarif 3
TARIF3TTC (Numérique)                                           -> Prix de vente TTC du produit pour le tarif 3
TARIF4HT (Numérique)                                            -> Prix de vente HT du produit pour le tarif 4
TARIF4CODE_TVA (entier)                                         -> Code TVA affecté au produit pour le tarif 4
TARIF4_NOM_TVA (char(40))                                       -> Nom de la TVA affecté au produit pour le tarif 4
TARIF4_TAUX_TVA (Numérique)                                     -> Taux de TVA affecté au produit pour le tarif 4
TARIF4TTC (Numérique)                                           -> Prix de vente TTC du produit pour le tarif 4
PROMODU (Date)													-> Date de début promotion du produit.
PROMOAU (Date)													-> Date de fin promotion du produit.
PROMOHT (Numérique)                                             -> Prix promotionnel HT du produit
PROMOTTC (Numérique)											-> Prix de vente promotionnel TTC du produit.
POIDS (Numérique)												-> Poids du produit.
UNITE (char[10])												-> Unité du produit (UNITE, kg, l , etc.).
COEFF_UNITE (Numérique)											-> Coefficient de l’unité du produit. (Exemple : 0.250 kg).
PRIX_UNITE (Numérique)                                          -> calcule automatique de cette zone , en fonction du prix de vente TTC issu du tarif standard et du COEFF_UNITE.
ACTIF (1 -> Actif 0 -> Inactif)                                 -> Statut du produit
STOCKABLE (1 -> Stockable et 0 pour stock non géré)             -> Gestion du stock (Oui/Non)
STOCK (Numérique)												-> Quantité en stock du produit.
COMMANDE_CLIENT (Numérique)                                     -> Quantité en cours des commandes clients
COMMANDE_FOURNISSEUR (Numérique)                                -> Quantité en cours des commandes fournisseurs
IMAGE01 (1->presente 0->absente)   								->Présence de l’image principale, nom de l’image « CODEPRODUIT_1.jpg ».
IMAGE02 (1->presente 0->absente)								-> Présence de l’image supp1, nom de l’image « CODEPRODUIT_2.jpg ».
IMAGE03 (1->presente 0->absente)								-> Présence de l’image supp2, nom de l’image « CODEPRODUIT_3.jpg ».
IMAGESUP01 (1->presente 0->absente)								-> Présence de l’image supp3, nom de l’image « CODEPRODUIT_11.jpg ».
IMAGESUP02 (1->presente 0->absente)								-> Présence de l’image supp3, nom de l’image « CODEPRODUIT_12.jpg ».
IMAGESUP03 (1->presente 0->absente)								-> Présence de l’image supp3, nom de l’image « CODEPRODUIT_13.jpg ».
IMAGESUP04 (1->presente 0->absente)								-> Présence de l’image supp3, nom de l’image « CODEPRODUIT_14.jpg ».
IMAGESUP05 (1->presente 0->absente)								-> Présence de l’image supp3, nom de l’image « CODEPRODUIT_15.jpg ».
IMAGESUP06 (1->presente 0->absente)								-> Présence de l’image supp3, nom de l’image « CODEPRODUIT_16.jpg ».
IMAGESUP07 (1->presente 0->absente)								-> Présence de l’image supp3, nom de l’image « CODEPRODUIT_17.jpg ».
IMAGESUP08 (1->presente 0->absente)								-> Présence de l’image supp3, nom de l’image « CODEPRODUIT_18.jpg ».
IMAGESUP09 (1->presente 0->absente)								-> Présence de l’image supp3, nom de l’image « CODEPRODUIT_19.jpg ».
IMAGESUP10 (1->presente 0->absente)								-> Présence de l’image supp3, nom de l’image « CODEPRODUIT_20.jpg ».
(Les images seront transférées par FTP dans le répertoire renseigné dans le paramétrage de l’interface Kwisatz).
TEXTE01 (char(80))                                              -> Texte supplémentaire
TEXTE02 (char(80))                                              -> Texte supplémentaire
TEXTE03 (char(80))                                              -> Texte supplémentaire
TEXTE04 (char(80))                                              -> Texte supplémentaire
TEXTE05 (char(80))                                              -> Texte supplémentaire
MEMO01 (blob)                                                   -> Texte supplémentaire
MEMO02 (blob)                                                   -> Texte supplémentaire
MEMO03 (blob)                                                   -> Texte supplémentaire
MEMO04 (blob)                                                   -> Texte supplémentaire
MEMO05 (blob)                                                   -> Texte supplémentaire
TAUX_ALCOOL (Numérique)                                         -> Taux d’alcool du produit
LIBRE_01 (Booléen)                                              -> Zone supplémentaire à définir
LIBRE_02 (Booléen)                                              -> Zone supplémentaire à définir
LIBRE_03 (Booléen)                                              -> Zone supplémentaire à définir
SITE_INTERNET (char(100))                                       -> Adresse internet du produit
FACING1 (char(3))                                               -> Zone supplémentaire à définir
FACING2 (char(3))                                               -> Zone supplémentaire à définir
FACING3 (char(3))                                               -> Zone supplémentaire à définir
EN_SOMMEIL (Booléen)                                            -> Mise en sommeil du produit
VENTE_INTERDITE (Booléen)                                       -> Produit interdit à la vente
*/

function AddOrUpdateProduit($m)
{
	global $xmlrpcerruser;
	$TabParametre=array();

	$n = php_xmlrpc_decode($m);
	$TabParametre=$n[0];

	//$TabParametre=utf8_encode_array($TabParametre);
	$ValeurRetour=array();

	//Vérification si le code produit renseigné
	if (!isset($TabParametre['CODE'])) {
		$ValeurRetour['CODE_NON_RENSEIGNE']='1';
		return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));
	}
	$Code=$TabParametre['CODE'];
	if (__DEBUG_SMAIN) error_log("TabParametre : ".print_r($TabParametre,true),3,"log.txt");

	try {
		//Recherche si le produit existe
		//Si le produit existe ModeMiseAJour=true sinon ModeMiseAJour=false;
		$code=$TabParametre['CODE'];
		$ModeMiseAJour=false;
		//if (Produit.Find($code)) {
     	// $produit=ChargeFicheProduit($code);
     	// $ModeMiseAJour=true;
  	   // }
  	   //else {
  	   // $ModeMiseAJour=false;
  	   // Creation Objet Produit;
  	   // $produit = new Produit();
	   //}
	   //if (isset($TabParametre['CODE'])) $produit.CODE=$TabParametre['CODE'];
	   //if (isset($TabParametre['LIBELLE '])) $produit.LIBELLE =$TabParametre['LIBELLE '];
	   //if (isset($TabParametre['CODE_RAYON'])) $produit.CODE_RAYON=$TabParametre['CODE_RAYON'];
	   //if (isset($TabParametre['CODE_FAMILLE'])) $produit.CODE_FAMILLE=$TabParametre['CODE_FAMILLE'];
	   //if (isset($TabParametre['CODE_LIGNE'])) $produit.CODE_LIGNE=$TabParametre['CODE_LIGNE'];
	   //if (isset($TabParametre['CODE_MARQUE'])) $produit.CODE_MARQUE=$TabParametre['CODE_MARQUE'];
	   //if (isset($TabParametre['PAHTNET'])) $produit.PAHNET=$TabParametre['PAHNET'];
	   //if (isset($TabParametre['PVTTC'])) $produit.PVTTC=$TabParametre['PVTTC'];
	   //if (isset($TabParametre['PROMODU'])) $produit.PROMODU=$TabParametre['PROMODU'];
	   //if (isset($TabParametre['PROMOAU'])) $produit.PROMOAU=$TabParametre['PROMOAU'];
	   //if (isset($TabParametre['PROMOTTC'])) $produit.PROMOTTC=$TabParametre['PROMOTTC'];
	   //if (isset($TabParametre['STOCK'])) $produit.STOCK=$TabParametre['STOCK'];
	   //if (isset($TabParametre['UNITE'])) $produit.UNITE=$TabParametre['UNITE'];
	   //if (isset($TabParametre['COEFF_UNITE'])) $produit.COEFF_UNITE=$TabParametre['COEFF_UNITE'];
	   //if (isset($TabParametre['POIDS'])) $produit.POIDS=$TabParametre['POIDS'];
	   //if (isset($TabParametre['CATEGORIES'])) $produit.CATEGORIES=$TabParametre['CATEGORIES'];
	   //if (isset($dataProduit['CATEGORIES']) && is_array($dataProduit['CATEGORIES'])) {
       //     foreach ($dataProduit['CATEGORIES'] as $id_category) {
	   //       Affecter la catégorie à $produit->categorie;
	   //	  }
       //
       //     }
	   //if (isset($TabParametre['DESCRIPTION'])) $produit.DESCRIPTION=$TabParametre['DESCRIPTION'];
	   //if (isset($TabParametre['DESCRIPTION_COURTE])) $produit.DESCRIPTION_COURTE=$TabParametre['DESCRIPTION_COURTE'];
	   //if (isset($TabParametre['IMAGE01'])) Traitement de l'image;
	   //if (isset($TabParametre['IMAGE02'])) Traitement de l'image;
	   //if (isset($TabParametre['IMAGE03'])) Traitement de l'image;
	   //if (isset($TabParametre['IMAGESUP01'])) Traitement de l'image;
	   //if (isset($TabParametre['IMAGESUP02'])) Traitement de l'image;
	   //if (isset($TabParametre['IMAGESUP03'])) Traitement de l'image;
	   //if (isset($TabParametre['IMAGESUP04'])) Traitement de l'image;
	   //if (isset($TabParametre['IMAGESUP05'])) Traitement de l'image;
	   //if (isset($TabParametre['IMAGESUP06'])) Traitement de l'image;
	   //if (isset($TabParametre['IMAGESUP07'])) Traitement de l'image;
	   //if (isset($TabParametre['IMAGESUP08'])) Traitement de l'image;
	   //if (isset($TabParametre['IMAGESUP09'])) Traitement de l'image;
	   //if (isset($TabParametre['IMAGESUP10'])) Traitement de l'image;



  	   if ($ModeMiseAJour) {
  	   	//$produit.Update();
  	   	//si Erreur Update $ValeurRetour['ERREUR_MISE_A_JOUR']=$code;
  	   	$ValeurRetour['MISE_A_JOUR_OK']=$code;
  	   }
  	   else {
  	   	//$produit.Add();
  	   	//si Erreur Add $ValeurRetour['ERREUR_AJOUT']=$code;
  	   	$ValeurRetour['AJOUT_OK']=$code;
  	   }
	    return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));

	}
	catch (Exception $Ex){
		$ValeurRetour['EXCEPTION']=$Ex->message();
		if (__DEBUG_SMAIN) error_log("EXCEPTION : ".print_r($ValeurRetour,true),3,"log.txt");
		return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));
	}
}


$UpdateStock_sig=array(array($xmlrpcStruct, $xmlrpcStruct));
$UpdateStock_doc="Mise à jour du stock.";

function UpdateStock($m)
{
	global $xmlrpcerruser;
	$TabParametre=array();

	$n = php_xmlrpc_decode($m);
	$TabParametre=$n[0];

	//$TabParametre=utf8_encode_array($TabParametre);
	$ValeurRetour=array();

	//Vérification si le code produit renseigné
	if (!isset($TabParametre['CODE'])) {
		$ValeurRetour['CODE_NON_RENSEIGNE']='1';
		return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));
	}
	$Code=$TabParametre['CODE'];
	if (__DEBUG_SMAIN) error_log("TabParametre : ".print_r($TabParametre,true),3,"log.txt");

	try {
		//Recherche si le produit existe
		$code=$TabParametre['CODE'];

		//if (Produit.Find($code)) {
		// $produit=ChargeFicheProduit($code);
		// }
		//else {
		// $ValeurRetour['PRODUIT_INTROUVABLE']='1';
		// return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));
		//}

		//if (isset($TabParametre['STOCK'])) $produit.STOCK=$TabParametre['STOCK'];

		//$produit.Update();
		//si Erreur Update $ValeurRetour['ERREUR_MISE_A_JOUR']=$code;
		$ValeurRetour['MISE_A_JOUR_OK']=$code;
        return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));

	}
	catch (Exception $Ex){
	 $ValeurRetour['EXCEPTION']=$Ex->message();
	 if (__DEBUG_SMAIN) error_log("EXCEPTION : ".print_r($ValeurRetour,true),3,"log.txt");
	 return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));
	}
}

$AddClient_sig=array(array($xmlrpcStruct, $xmlrpcStruct));
$AddClient_doc="Ajout ou mise à jour d'un client.";
/* Format de le structure d'entrée

CODE (char(8))                          -> Identifiant du client
NOM	 (char(40))                         -> Nom du client
ADRESSE_FACT_LIGNE1 (char(80))          -> Adresse de facturation : ligne 1
ADRESSE_FACT_LIGNE2	(char((80))         -> Adresse de facturation : ligne 2
ADRESSE_FACT_CP (char(10))              -> Adresse de facturation : code postal
ADRESSE_FACT_VILLE (char(40))           -> Adresse de facturation : ville
ADRESSE_FACT_PAYS  (char(20))           -> Adresse de facturation : pays
ADRESSE_LIVR_LIGNE1 (char(80))          -> Adresse de livraison : ligne 1
ADRESSE_LIVR_LIGNE2 (char(80))          -> Adresse de livraison : ligne 2
ADRESSE_LIVR_CP (char(10))              -> Adresse de livraison : code postal
ADRESSE_LIVR_VILLE (char(40))           -> Adresse de livraison : ville
ADRESSE_LIVR_PAYS 'char(20))            -> Adresse de livraison : pays
CODE_FAMILLE (Entier)                   -> Code de la famille auquel appartient le client
CODE_CATEGORIE (Entier)                 -> Code la catégorie auquel appartient le client
INTITULE (char(40))                     -> Intitulé du client « Monsieur, Madame, etc. »
ACTIVITE (char(40))                     -> Activité du client
RESPONSABLE (char(40))                  -> Nom du responsable
CONTACT (char(40))                      -> Nom du contact
TELEPHONE1 (char(20))                   -> Téléphone 1
TELEPHONE2 (char(20))                   -> Téléphone 2
TELEPHONE3 (char(20))                   -> Téléphone 3
FAX1 (char(20))                         -> Fax 1
FAX2 (char(20))                         -> Fax 2
FAX3 (char(20))                         -> Fax 3
EMAIL1 (char(100))                      -> Email 1
EMAIL2 (char(100))                      -> Email 2
EMAIL3 (char(100))                      -> Email 3
NUMERO_INTRACOMM (char(40))             -> N° de TVA Intracommunautaire
CODE_VENDEUR (char(4))                  -> Code du vendeur affecté à ce client
CODE_REGLEMENT (Entier)                 -> Code règlement affecté à ce client
CODE_DELAIS (Entier)                    -> Code délais de paiement affecté à ce client
CODE_TARIF (Entier)                     ->Code tarif affecté à ce client
*/

function AddClient($m)
{
    global $xmlrpcerruser;
    $TabParametre=array();

    $n = php_xmlrpc_decode($m);
    $TabParametre=$n[0];

    //$TabParametre=utf8_encode_array($TabParametre);
    $ValeurRetour=array();

    //Vérification si le code produit renseigné
    if (!isset($TabParametre['CODE'])) {
        $ValeurRetour['CODE_NON_RENSEIGNE']='1';
        return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));
    }
    $Code=$TabParametre['CODE'];
    if (__DEBUG_SMAIN) error_log("TabParametre : ".print_r($TabParametre,true),3,"log.txt");

    try {
        //Recherche si le client existe
        //Si le client existe ModeMiseAJour=true sinon ModeMiseAJour=false;
        $code=$TabParametre['CODE'];
        $ModeMiseAJour=false;
        //if (client.Find($code)) {
        // $client=ChargeFicheClient($code);
        // $ModeMiseAJour=true;
        // }
        //else {
        // $ModeMiseAJour=false;
        // Creation Objet Client;
        // $client = new Client();
        //}
        //if (isset($TabParametre['CODE'])) $client.CODE=$TabParametre['CODE'];
        //if (isset($TabParametre['NOM"])) $client.LIBELLE =$TabParametre['NOM '];
        //etc


        if ($ModeMiseAJour) {
            //$client.Update();
            //si Erreur Update $ValeurRetour['ERREUR_MISE_A_JOUR']=$code;
            $ValeurRetour['MISE_A_JOUR_OK']=$code;
        }
        else {
            //$client.Add();
            //si Erreur Add $ValeurRetour['ERREUR_AJOUT']=$code;
            $ValeurRetour['AJOUT_OK']=$code;
        }
        return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));

    }
    catch (Exception $Ex){
        $ValeurRetour['EXCEPTION']=$Ex->message();
        if (__DEBUG_SMAIN) error_log("EXCEPTION : ".print_r($ValeurRetour,true),3,"log.txt");
        return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));
    }
}


function RecupereEntete($Compteur) {
	$Entete=array();
	$Entete['REFERENCE1']='REFERENCE 1';
	$Entete['REFERENCE2']='REFERENCE 2';
	//$Entete['CODE_CLIENT']='1000000'.$Compteur;
	$Entete['DATE']='01/08/2018';
	//$ENTETE['CODE_MAGASIN']=
	//$Entete['CODE_AFFAIRE']=
	$Entete['NUMERO']=$Compteur;
    //$Entete['PORT_SOUMIS']='10';
    //$Entete['PORT_TVA_CODE']='1';
    //$Entete['PORT_TVA_TAUX']='20.00';
    //$Entete['PORT_TVA_MONTANT']='2.00';
    //$Entete['FRAIS']=;
    //$Entete['PORT_NON_SOUMIS']=;
    //$Entete['FRAIS']=;
    //$Entete['TAUX_REMISE']=;
	return $Entete;
}

function RecupereClient($Compteur) {
	// Si un champ ne contient pas de valeur ne pas renvoyer le champ.
	$Client=array();
	$Client['CODE']='1000000'.$Compteur; //Code du client sur 8 digit
	$Client['NOM']='ESSAI '.$Compteur;
	$Client['ADRESSE_FACT_LIGNE1']=$Compteur.' AVENUE DES PLATANES';
	//$Client['ADRESSE_FACT_LIGNE2']=
	$Client['ADRESSE_FACT_CP']='1300'.$Compteur;
	$Client['ADRESSE_FACT_VILLE']='MARSEILLE';
	//$Client['ADRESSE_FACT_PAYS']=
	$Client['ADRESSE_LIVR_LIGNE1']=$Compteur.' AVENUE DES PLATANES';
	//$Client['ADRESSE_LIVR_LIGNE2']=
	$Client['ADRESSE_LIVR_CP']='1300'.$Compteur;
	$Client['ADRESSE_LIVR_VILLE']='MARSEILLE';
	//$Client['ADRESSE_LIVR_PAYS']=
	$Client['CODE_FAMILLE']='1';
	$Client['CODE_CATEGORIE']='1';
	$Client['INTITULE']='MONSIEUR';
	//$Client['ACTIVITE']=
	//$Client['RESPONSABLE']=
	//$Client['CONTACT']=
	$Client['TELEPHONE1']='04.96.13.02.91';
	//$Client['TELEPHONE2']=
	//$Client['TELEPHONE3']=
	//$Client['FAX1']=
	//$Client['FAX2']=
	//$Client['FAX3']=
	$Client['EMAIL1']='kwisatz.hotline@free.fr';
	//$Client['EMAIL2']=
	//$Client['EMAIL3']=
    //$Client['NUMERO_INTRACOMM']=
    //$Client['CODE_VENDEUR']=
    //$Client['CODE_REGLEMENT']=
    //$Client['CODE_DELAIS']=
    //$Client['CODE_TARIF']=
	return $Client;
}

function RecupereLigneCommande($Compteur) {
	$Ligne=array();

	$Ligne['CODE_ARTICLE']=$Compteur;
	//$Ligne['REF_FOURNISSEUR']=;
	$Ligne['LIBELLE']='ESSAI PRODUIT '.$Compteur;
	$Ligne['QUANTITE']='1';
	$Ligne['PUHT']='25.08361'; // Prix unitaire HT
	//$Ligne['TAUX_REMISE']='10.00';
	$Ligne['CODE_TVA']='1';
    $Ligne['TAUX_TVA']='20.00';
	$Ligne['PUTTC']='30.00';
	return $Ligne;
}

function RecupereLigneReglement(){
	$Reglement=array();

	$Reglement['MODE_PAIEMENT']='12';//CORRESPOND A CHEQUE;
	$Reglement['MONTANT']='150.00';

	return $Reglement;
}

$GetListeCommandes_sig=array(array($xmlrpcStruct, $xmlrpcStruct));
$GetListeCommandes_doc="Retourne la liste des commandes.";

function GetListeCommandes($m)
{
	$n = php_xmlrpc_decode($m);
	$TabParametre=$n[0];

	if (__DEBUG_SMAIN) error_log("PARAMETRES : ".print_r($TabParametre,true),3,"log.txt");

	$ValeurRetour=array();
	try {
		//Verifier la pr�sence de TypeFiltre
		if (isset($TabParametre['TYPEFILTRE]'])) $TypeFiltre=$TabParametre['TYPEFILTRE'];
		else $TypeFiltre=2;

		switch ($TypeFiltre) {
			case 1:if (isset($TabParametre['DATEDEDEBUT'])) $DateDeDebut=$TabParametre['DATEDEDEBUT'];
			        else $DateDeDebut="2012-01-18"; //date du jour;
			       if (isset($TabParametre['DATEDEFIN'])) $DateDeFin=$TabParametre['DATEDEFIN'];
			       else $DateDeFin="201-01-18"; //date du jour;
			       $DateDeDebut=$DateDeDebut+" 00:00:00";
			       $DateDeFin=$DateDeFin+" 23:59:59";
			       //Recuperation de la collection des commandes.
				break;
			case 2:if (isset($TabParametre['NBJOURS'])) $NbJours=$TabParametre['NBJOURS'];
			        else $NbJours=1;
				   // Calcul de la date de debut et la date de fin par rapport au NbJours demand�.
				   // Recuperation de la collection des commandes.
				break;

		}

		$CompteurCommande=1;

		//Si la collection des commandes contient des commandes renvoyer les commmandes sinon renvoyer 'AUCUNE_COMMANDE'
		if ($CompteurCommande > 0) { // Verification si des commandes pr�sentes.

			//CLIENT CONCERNE PAR LE BON DE COMMANDE
			//Si un champ est vide ne pas le renvoyer, le champ CLI_CODE est obligatoire.
			for ($i=1;$i<=$CompteurCommande;$i++) {
				$UneCommande=array();

				$UneCommande['ENTETE']=RecupereEntete($i);
				//$UneCommande['CLIENT']=RecupereClient($i);

				$NbLigneDocuments=5;
				for ($j=1;$j<=$NbLigneDocuments;$j++) {
				 $UneCommande['LIGNE'][]=RecupereLigneCommande($j);
				}

				$NbLignesReglements=1;
				for ($j=1;$j<=$NbLignesReglements;$j++){
				 $UneCommande['REGLEMENT'][]=RecupereLigneReglement();
				}
				$ValeurRetour[]=$UneCommande;
			}


		}
		else $ValeurRetour['AUCUNE_COMMANDE']='1'; // Message de retour : Il n'y a aucune commande � renvoyer;

		if (__DEBUG_SMAIN) error_log("VALEUR RETOUR : ".print_r($ValeurRetour,true),3,"log.txt");

		return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));
	}
	catch (Exception $Ex) {
		$ValeurRetour['EXCEPTION']=$Ex->message(); // Message de retour : Exception sur la fonction;
		return new xmlrpcresp(php_xmlrpc_encode($ValeurRetour));
	}

}


$s = new xmlrpc_server(array(
		"kwisatz.getlistecategories" => array(
				"function" => "GetListeCategories",
				"signature" => $GetListeCategories_sig,
				"docstring" => $GetListeCategories_doc
		),
		"kwisatz.addorupdateattribut" => array(
			"function" => "AddOrUpdateAttribut",
			"signature" => $AddOrUpdateAttribut_sig,
			"docstring" => $AddOrUpdateAttribut_doc
	   ),
	   "kwisatz.addorupdateproduit" => array(
			"function" => "AddOrUpdateProduit",
			"signature" => $AddOrUpdateProduit_sig,
			"docstring" => $AddOrUpdateProduit_doc
	   ),
	   "kwisatz.updatestock" => array(
			"function" => "UpdateStock",
			"signature" => $UpdateStock_sig,
			"docstring" => $UpdateStock_doc
	   ),
    "kwisatz.addunclient" => array(
        "function" => "AddClient",
        "signature" => $AddClient_sig,
        "docstring" => $AddClient_doc
        ),
    "kwisatz.getlistecommandes" => array(
        "function" => "GetListeCommandes",
        "signature" => $GetListeCommandes_sig,
        "docstring" => $GetListeCommandes_doc
        )
	   ));
?>
