<?php
	$GLOBALS['userApiKey'] = "ae408ce23e62e798964737f720486e93";
	
	$documents = postDocument("Document Test", 2446987, "http://sav.tacteo.fr/admin/gmao/intervention/view_inter.php?inter=566&pdf=1");
	echo "<pre>";
		print_r($customers);
	echo "</pre>";
	
	
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
		else if(isset($quotations->error))
		{
			print_r($quotations);
			return FALSE;
			die();
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