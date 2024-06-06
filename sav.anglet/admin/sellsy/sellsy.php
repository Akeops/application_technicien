<?php
	error_reporting(0);
	require_once('sellsyconnect_curl.php');
	require_once('sellsytools.php');


	function refreshBDD($bdd) // Refresh the MySQL database
	{
		refreshCustomers($bdd);
		refreshProducts($bdd);
		return true;
	}

	function refreshCustomers($bdd)
	{
		$customers = getCustomers();
		if($customers->status == 'success')
		{
			$query = "INSERT INTO `CUSTOMERS`(`CUSTOMER_ID`, `CODE`, `INTITULE`, `NOM`, `SIRET_NUMBER`, `TELEPHONE1`, `EMAIL`, `ADLIVR_LIGNE1`, `ADLIVR_LIGNE2`, `ADLIVR_VILLE`, `ADLIVR_CODE_POSTAL`, `CONTRAT_DU`, `CONTRAT_AU`, `CODE_VENDEUR`, `SOLDE`) VALUES";
			$ctm = array();
			$customers = (array) $customers->response->result;
			foreach($customers as $customer)
			{
				$find = array("'", "\"", "\\");
				$replace = array(" ", "", "");
				!empty($ctm) ? $query .= ',' : null;
				$contacts = (array) $customer->contacts;
				$contact = $contacts[array_keys($contacts)[0]];
				$ctm['EMAIL']				= str_replace($find, $replace, $contact->email);
				$ctm['TELEPHONE1']			= str_replace($find, $replace, $contact->tel);

				$customFields = (array) $customer->customfields;
				foreach($customFields as $field)
				{
					if($field->code == 'countractend')
					{
						$ctm['CONTRAT_AU']	= str_replace($find, $replace, $field->formatted_value);
					}
					elseif($field->code == 'countractstart')
					{
						$ctm['CONTRAT_DU']	= str_replace($find, $replace, $field->formatted_value);
					}
				}

				$ctm['CUSTOMER_ID']			= str_replace($find, $replace, $customer->thirdid);
				$ctm['ADLIVR_CODE_POSTAL']	= str_replace($find, $replace, $customer->addr_zip);
				$ctm['ADLIVR_LIGNE1']		= str_replace($find, $replace, $customer->addr_part1);
				$ctm['ADLIVR_LIGNE2']		= str_replace($find, $replace, $customer->addr_part2);
				$ctm['ADLIVR_VILLE']		= str_replace($find, $replace, $customer->addr_town);
				$ctm['CODE']				= str_replace($find, $replace, $customer->auxCode);
				$ctm['CODE_VENDEUR']		= str_replace($find, $replace, $customer->ownerid);
				$ctm['INTITULE']			= 'CLIENT';
				$ctm['NOM']					= str_replace($find, $replace, $customer->name);
				$ctm['SIRET_NUMBER']		= str_replace($find, $replace, $customer->siret);
				$ctm['SOLDE']				= 0;

				//if(!$ctm['CUSTOMER_ID']) continue;

				$query .= "
						(
							'".$ctm['CUSTOMER_ID']."',
							'".$ctm['CODE']."',
							'".$ctm['INTITULE']."',
							'".$ctm['NOM']."',
							'".$ctm['SIRET_NUMBER']."',
							'".$ctm['TELEPHONE1']."',
							'".$ctm['EMAIL']."',
							'".$ctm['ADLIVR_LIGNE1']."',
							'".$ctm['ADLIVR_LIGNE2']."',
							'".$ctm['ADLIVR_VILLE']."',
							'".$ctm['ADLIVR_CODE_POSTAL']."',
							'".$ctm['CONTRAT_DU']."',
							'".$ctm['CONTRAT_AU']."',
							'".$ctm['CODE_VENDEUR']."',
							'".$ctm['SOLDE']."'
						)";
			}
			$query .= "
				ON DUPLICATE KEY UPDATE
					`INTITULE`				= VALUES(`INTITULE`),
					`SIRET_NUMBER`			= VALUES(`SIRET_NUMBER`),
					`TELEPHONE1`			= VALUES(`TELEPHONE1`),
					`EMAIL`					= VALUES(`EMAIL`),
					`ADLIVR_LIGNE1`			= VALUES(`ADLIVR_LIGNE1`),
					`ADLIVR_LIGNE2`			= VALUES(`ADLIVR_LIGNE2`),
					`ADLIVR_VILLE`			= VALUES(`ADLIVR_VILLE`),
					`ADLIVR_CODE_POSTAL`	= VALUES(`ADLIVR_CODE_POSTAL`),
					`CONTRAT_DU`			= VALUES(`CONTRAT_DU`),
					`CONTRAT_AU`			= VALUES(`CONTRAT_AU`),
					`CODE_VENDEUR`			= VALUES(`CODE_VENDEUR`),
					`SOLDE`					= VALUES(`SOLDE`)
				";
			//$bdd->query("TRUNCATE `CUSTOMERS`") or die(print_r($bdd->errorInfo()));
			$bdd->query($query) or die(print_r($bdd->errorInfo()));
		}
	}

	function refreshProducts($bdd)
	{
		$products = getProducts();
		if($products->status == 'success')
		{
			$query = "INSERT INTO `PRODUITS`(`CODE`, `LIBELLE`, `PVHT`) VALUES";
			$pdt = array();
			$products = (array) $products->response->result;
			foreach($products as $product)
			{
				$find = array("'", "\"", "\\");
				$replace = array(" ", "", "");
				!empty($pdt) ? $query .= ',' : null;
				$contacts = (array) $product->contacts;
				$contact = $contacts[array_keys($contacts)[0]];

				$pdt['CODE'] = 		str_replace($find, $replace, $product->id);
				$pdt['LIBELLE'] = 	str_replace($find, $replace, $product->tradename);
				$pdt['PVHT'] = 		str_replace($find, $replace, $product->unitAmount);

				$query .= "
						(
							".$pdt['CODE'].",
							'".$pdt['LIBELLE']."',
							".$pdt['PVHT']."
						)";
			}
			$query .= "
				ON DUPLICATE KEY UPDATE
					`CODE`				= 	VALUES(`CODE`),
					`LIBELLE`			= 	VALUES(`LIBELLE`),
					`PVHT`				=	VALUES(`PVHT`)
				";
			$bdd->query("TRUNCATE `PRODUITS`") or die(print_r($bdd->errorInfo()));
			$bdd->query($query) or die(print_r($bdd->errorInfo()));
		}
	}

	function getCustomers($id=null, $search=array("type" => "corporation"), $nbperpage=5000, $pagenum=1) // Get the Customers list or one
	{
		if($id)
		{
			$request = array(
				'method' => 'Client.getOne',
				'params' => array(
					'clientid'  =>  $id
				)
			);
		}
		else
		{
			$request = array(
				'method' => 'Client.getList',
				'params' => array(
					'order' => array(
						'direction'		=> 'ASC',
						'order'			=> 'fullName'
					),
					'pagination' => array(
						'nbperpage' => $nbperpage,
						'pagenum'   => $pagenum
					),
					'search' => $search
				)
			);
		}

		return sellsyConnect_curl::load()->requestApi($request);
	}

	function getProducts($id=null, $search=array(), $nbperpage=5000, $pagenum=1) // Get products list or one
	{
		if($id)
		{
			$request =  array(
				'method' => 'Catalogue.getOne',
				'params' => array (
					'type'                   => 'item',
					'id'                     => $id
				)
			);
			$items = sellsyConnect_curl::load()->requestApi($request);
			$request =  array(
				'method' => 'Catalogue.getOne',
				'params' => array (
					'type'                   => 'service',
					'id'                     => $id
				)
			);
			$products = sellsyConnect_curl::load()->requestApi($request);
		}
		else
		{
			$request = array(
				'method' => 'Catalogue.getList',
				'params' => array (
					'type'          => 'item',
					'order' => array(
						'direction' => 'ASC',
						'order'     => 'item_name'
					),
					'pagination' => array (
						'pagenum'   => $pagenum,
						'nbperpage' => $nbperpage
					),
					'search' => $search
				)
			);
			$items = sellsyConnect_curl::load()->requestApi($request);
			$request = array(
				'method' => 'Catalogue.getList',
				'params' => array (
					'type'          => 'service',
					'order' => array(
						'direction' => 'ASC',
						'order'     => 'item_name'
					),
					'pagination' => array (
						'pagenum'   => $pagenum,
						'nbperpage' => $nbperpage
					),
					'search' => $search
				)
			);
			$products = sellsyConnect_curl::load()->requestApi($request);
		}

		$products->response->result = (object)array_merge((array)$items->response->result, (array)$products->response->result);
		$products->response->infos->nbtotal = $products->response->infos->nbtotal + $items->response->infos->nbtotal;

		return $products;
	}

	function getQuotation($ident) // Get a quotation
	{
		$request =  array(
			'method' => 'Document.getList',
			'params' => array (
				'doctype'       => 'estimate',
				'order' => array(
					'direction' => 'ASC',
					'order'     => 'doc_ident'
				),
				'pagination' => array (
					'nbperpage' => 5000,
					'pagenum'   => 1
				),
				'search' => array(
					'ident'         => $ident
				)
			)
		);

		$res = sellsyConnect_curl::load()->requestApi($request);

		if($res->status == 'success' && !empty($res->response->result))
		{
			$res = (array) $res->response->result;
			$estimate = $res[array_keys($res)[0]];
			$request = array(
				'method' => 'Document.getOne',
				'params' => array(
					'doctype'   => 'estimate',
					'docid'     => $estimate->id
				)
			);
			$quotation = sellsyConnect_curl::load()->requestApi($request);
			$customer = getCustomers($quotation->response->thirdid);
			$quotation->response->customer = $customer;
			return $quotation;
		}
		else
		{
			$res->status = 404;
			return $res;
		}
	}

	function postDocument($name = "Document sans nom", $compagny_id, $file) // Post a document to sellsy
	{

	}
