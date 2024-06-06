<?php
	$req = $bdd->query("SELECT VALUE FROM SETTINGS WHERE NAME = 'LAST_SYNCHRO'") or die(print_r($bdd->errorInfo()));
	$last_synchro = $req->fetch(2)['VALUE'];
	$req = $bdd->query("SELECT VALUE FROM SETTINGS WHERE NAME = 'INTERVAL_SYNCHRO'") or die(print_r($bdd->errorInfo()));
	$interval_synchro = $req->fetch(2)['VALUE'];
	$today = time();
	$diff = $today - $last_synchro;

	refreshBDD($bdd);

	if($diff >= $interval_synchro)
	{	
		$bdd->query("UPDATE SETTINGS SET VALUE = ".$today." WHERE NAME = 'LAST_SYNCHRO'") or die(print_r($bdd->errorInfo()));
	}
	
	function refreshBDD($bdd)
	{		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL =>				"https://axonaut.com/api/v2/companies",
			CURLOPT_CUSTOMREQUEST	=>	"GET",
			CURLOPT_RETURNTRANSFER	=>	true,
			CURLOPT_HTTPHEADER		=>	array(
				"Accept: application/json",
				"userApiKey: ".$GLOBALS['userApiKey']
			),
		));
		$output = json_decode(curl_exec($curl));
		if(isset($output->error))
		{
			// if(!isset($output->error->pages))
			// {
				// return;
			// }
			if(isset($output->error->pages))
			{
				$pages = $output->error->pages + 1;
			}
			else
			{
				$pages = 1;
			}
		}
		
		$bdd->query("TRUNCATE CUSTOMERS") or die(print_r($bdd->errorInfo()));
		
		for($x = 1; $x < $pages; $x++)
		{
			$customers = getCustomers(null, $x);
			for($y = 0; $y < count($customers); $y++)
			{
				if($customers[$y]->is_prospect == 1)
				{
					$customer_is = "NON CLIENT";
				}
				else if($customers[$y]->is_customer == 1)
				{
					$customer_is = "CLIENT";
				}
				else if($customers[$y]->is_supplier == 1)
				{
					$customer_is = "ANCIEN CLIENT";
				}
				
				$phone = null;
				$email = null;
				if(isset($customers[$y]->employees[0]))
				{
					if(!empty($customers[$y]->employees[0]->phone_number))
					{
						$phone = $customers[$y]->employees[0]->phone_number;
					}
					else if(!empty($customers[$y]->employees[0]->cellphone_number))
					{
						$phone = $customers[$y]->employees[0]->cellphone_number;
					}
					
					$email = $customers[$y]->employees[0]->email;
				}
				
				$start_countract = null;
				$end_countract = null;
				if(isset($customers[$y]->custom_fields->{'Début de contrat'}))
				{
					$start_countract = $customers[$y]->custom_fields->{'Début de contrat'};
				}
				
				if(isset($customers[$y]->custom_fields->{'Fin du contrat'}))
				{
					$end_countract = $customers[$y]->custom_fields->{'Fin du contrat'};
				}
				
				$req = $bdd->prepare("
					INSERT INTO
						`CUSTOMERS`(`CODE`, `INTITULE`, `NOM`, `SIRET_NUMBER`, `TELEPHONE1`, `EMAIL`, `ADLIVR_LIGNE1`, `ADLIVR_LIGNE2`, `ADLIVR_VILLE`, `ADLIVR_CODE_POSTAL`, `CONTRAT_DU`, `CONTRAT_AU`, `CODE_VENDEUR`, `SOLDE`)
					VALUES
					(
						:code,
						:intitule,
						:nom,
						:siret,
						:telephone,
						:email,
						:addresse,
						:addresse2,
						:ville,
						:code_postal,
						:contrat_du,
						:contrat_au,
						:vendeur,
						:solde
					)
					ON DUPLICATE KEY UPDATE
						`INTITULE`				= :intituleD,
						`NOM`					= :nomD,
						`SIRET_NUMBER`			= :siret,
						`TELEPHONE1`			= :telephoneD,
						`EMAIL`					= :emailD,
						`ADLIVR_LIGNE1`			= :addresseD,
						`ADLIVR_LIGNE2`			= :addresse2D,
						`ADLIVR_VILLE`			= :villeD,
						`ADLIVR_CODE_POSTAL`	= :code_postalD,
						`CONTRAT_DU`			= :contrat_duD,
						`CONTRAT_AU`			= :contrat_auD,
						`CODE_VENDEUR`			= :vendeurD,
						`SOLDE`					= :soldeD
					"
				);
				
				$req->execute(array(
						'code'			=>	$customers[$y]->id,
						'intitule'		=>	$customer_is,
						'nom'			=>	$customers[$y]->name,
						'siret'			=>	$customers[$y]->custom_fields->Siret ? $customers[$y]->custom_fields->Siret : "",
						'telephone'		=>	$phone,
						'email'			=>	$email,
						'addresse'		=>	$customers[$y]->address_street,
						'addresse2'		=>	"",
						'ville'			=>	$customers[$y]->address_city,
						'code_postal'	=>	$customers[$y]->address_zip_code,
						'contrat_du'	=>	$start_countract,
						'contrat_au'	=>	$end_countract,
						'vendeur'		=>	$customers[$y]->address_zip_code,
						'solde'			=>	$customers[$y]->customer_balance ? $customers[$y]->customer_balance : 0,
						//DUPLICATE
						'intituleD'		=>	$customer_is,
						'nomD'			=>	$customers[$y]->name,
						'telephoneD'	=>	$phone,
						'emailD'		=>	$email,
						'addresseD'		=>	$customers[$y]->address_street,
						'addresse2D'	=>	"",
						'villeD'		=>	$customers[$y]->address_city,
						'code_postalD'	=>	$customers[$y]->address_zip_code,
						'contrat_duD'	=>	$start_countract,
						'contrat_auD'	=>	$end_countract,
						'vendeurD'		=>	$customers[$y]->address_zip_code,
						'soldeD'		=>	$customers[$y]->customer_balance ? $customers[$y]->customer_balance : 0
				)) or die(print_r($req->errorInfo()));
			}
		}
	}
	
	function getCustomers($id = null, $page = 1)
	{
		($id == null) ? null : $id = "/".$id;
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL =>				"https://axonaut.com/api/v2/companies".$id,
			CURLOPT_CUSTOMREQUEST =>	"GET",
			CURLOPT_RETURNTRANSFER =>	true,
			CURLOPT_HTTPHEADER =>		array(
				"Accept: application/json",
				"userApiKey: ".$GLOBALS['userApiKey'],
				"page: ".$page
			),
		));

		$customers = json_decode(curl_exec($curl));
		$err = curl_error($curl);

		curl_close($curl);

		if($err)
		{
			print_r($err);
			return FALSE;
		}
		else if(isset($customers->error))
		{
			print_r($customers);
			return FALSE;
			die();
		}
		else
		{
			return $customers;
		}
	}
	
	function getProducts($id = null, $page = 1)
	{
		($id == null) ? null : $id = "/".$id;
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://axonaut.com/api/v2/products".$id,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array(
				"Accept: application/json",
				"userApiKey: ".$GLOBALS['userApiKey'],
				"page:".$page
			),
		));

		$products = json_decode(curl_exec($curl));
		$err = curl_error($curl);

		curl_close($curl);

		if($err)
		{
			return FALSE;
		}
		else if(isset($products->error))
		{
			print_r($products);
			return FALSE;
			die();
		}
		else
		{
			return $products;
		}
	}
	
	function updateCustomer($id, $updates)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://axonaut.com/api/v2/companies/".$id,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST => "PATCH",
			CURLOPT_POSTFIELDS => http_build_query($updates),
			CURLOPT_HTTPHEADER => array(
				"Accept: application/json",
				"userApiKey: ".$GLOBALS['userApiKey']
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if($err)
		{
			return FALSE;
		}
		else if(isset($response->error))
		{
			print_r($response);
			return FALSE;
			die();
		}
		else
		{
			return $response;
		}
	}
	
	function updateProduct($id, $updates)
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://axonaut.com/api/v2/products/".$id,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST => "PATCH",
			CURLOPT_POSTFIELDS => http_build_query($updates),
			CURLOPT_HTTPHEADER => array(
				"Accept: application/json",
				"userApiKey: ".$GLOBALS['userApiKey']
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if($err)
		{
			return FALSE;
		}
		else if(isset($response->error))
		{
			print_r($response);
			return FALSE;
			die();
		}
		else
		{
			return $response;
		}
	}
	
	function getInvoices($id = null, $page = 1)
	{
		($id == null) ? null : $id = "/".$id;
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL =>				"https://axonaut.com/api/v2/companies".$id."/invoices",
			CURLOPT_CUSTOMREQUEST =>	"GET",
			CURLOPT_RETURNTRANSFER =>	true,
			CURLOPT_HTTPHEADER =>		array(
				"Accept: application/json",
				"userApiKey: ".$GLOBALS['userApiKey'],
				"page: ".$page
			),
		));

		$invoices = json_decode(curl_exec($curl));
		$err = curl_error($curl);

		curl_close($curl);

		if($err)
		{
			print_r($err);
			return FALSE;
		}
		else if(isset($invoices->error))
		{
			print_r($invoices);
			return FALSE;
			die();
		}
		else
		{
			return $invoices;
		}
	}
	
	function getQuotations($id = null, $page = 1)
	{
		($id == null) ? null : $id = "/".$id;
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL =>				"https://axonaut.com/api/v2/quotations".$id,
			CURLOPT_CUSTOMREQUEST =>	"GET",
			CURLOPT_RETURNTRANSFER =>	true,
			CURLOPT_HTTPHEADER =>		array(
				"Accept: application/json",
				"userApiKey: ".$GLOBALS['userApiKey'],
				"page: ".$page
			),
		));

		$quotations = json_decode(curl_exec($curl));
		$err = curl_error($curl);

		curl_close($curl);

		if($err)
		{
			print_r($err);
			return FALSE;
		}
		else
		{
			return $quotations;
		}
	}
	
	function postDocument($name = "Document sans nom", $compagny_id, $external_url)
	{			
		$updates = "{ \"name\": \"".$name."\", \"company_id\": ".$compagny_id.", \"external_url\": \"".$external_url."\"}";
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL 			=>	"https://axonaut.com/api/v2/documents",
			CURLOPT_RETURNTRANSFER	=>	true,
			CURLOPT_POST			=>	1,
			CURLOPT_POSTFIELDS		=>	$updates,
			CURLOPT_HTTPHEADER		=>	array(
				"Accept: application/json",
				"userApiKey: ".$GLOBALS['userApiKey'],
				"Content-Type: application/json"
			)
		));

		$documents = json_decode(curl_exec($curl));
		$header  = curl_getinfo($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if($err)
		{
			print_r($err);
			return FALSE;
		}
		else
		{
			return $documents;
		}
	}
?>