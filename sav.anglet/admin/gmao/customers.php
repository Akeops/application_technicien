<?php
	require('../inc/config.inc.php');
	include('../axonaut/axonaut.php');
	
	$customers = getCustomers();
	
	$req = $bdd->query("SELECT * FROM SETTINGS WHERE NAME = 'RESULTS_PER_PAGES'") or die(print_r($bdd->errorInfo()));
	$resultsPerPages = ($req->fetch(2))['VALUE'];
	
	$interval = date('Y-m-d H:i:s',(strtotime("-".$interval['VALUE'])));
	$date = date('Y-m-d H:i:s');
	
	$query = "SELECT * FROM CUSTOMERS WHERE INTITULE = 'CLIENT'";
	$queryCount = "SELECT COUNT(CODE) AS TOTAL FROM CUSTOMERS WHERE INTITULE = 'CLIENT'";
	$filter = "";
	$url = "";
	
	$sql_vars = array();
	
	if(isset($_GET['contract']) && $_GET['contract'] == 1)
	{
		$filter = $filter." AND STR_TO_DATE(CONTRAT_AU, '%d/%m/%Y') >= CURDATE()";
		$url = $url."&contract=1";
	}
	else if(isset($_GET['contract']) && $_GET['contract'] == 0)
	{
		$filter = $filter." AND STR_TO_DATE(CONTRAT_AU, '%d/%m/%Y') <= CURDATE()";
		$url = $url."&contract=0";
	}
	
	if(!empty($_GET['com']))
	{
		$filter = $filter." AND CODE_VENDEUR = ?";
		$sql_vars['com'] = $_GET['com'];
		$url = $url."&com=".$_GET['com'];
	}
	
	if(!empty($_GET['customer']))
	{
		$filter = $filter." AND (UPPER(NOM) LIKE UPPER(?))";
		$sql_vars['customer'] = "%".urldecode($_GET['customer'])."%";
		$url = $url."&customer=".$_GET['customer'];
	}
	
	if(!empty($_GET['balance']))
	{
		$filter = $filter." AND CONVERT(SOLDE, SIGNED INTEGER) >= CONVERT(?, SIGNED INTEGER)";
		$sql_vars['balance'] = $_GET['balance'];
		$url = $url."&balance=".$_GET['balance'];
	}
	
	if(!empty($_GET['srch']))
	{
		$url = $url."&srch=1";
	}
	
	if(!empty($_GET['sort']))
	{
		if($_GET['sort'] == "contract")
		{
			$filter = $filter." ORDER BY STR_TO_DATE(CONTRAT_AU, '%d/%m/%Y')";
		}
		else if($_GET['sort'] == "solde")
		{
			$filter = $filter." ORDER BY CONVERT(SOLDE, SIGNED INTEGER)";
		}
		else
		{
			$filter = $filter." ORDER BY ".strtoupper($_GET['sort']);
		}
		
		if($_GET['order'])
		{
			$filter = $filter." ASC";
		}
		else
		{
			$filter = $filter." DESC";
		}
	}
	else
	{
		$filter = $filter." ORDER BY CODE DESC";
	}
	
	$req = $bdd->prepare($queryCount.$filter);
	
	if(count($sql_vars) > 0)
	{
		reset($sql_vars);
		for($x = 1; $x <= count($sql_vars); $x++)
		{
			$param = $x;
			$value = current($sql_vars);
			$req->bindParam($param, $value);
			next($sql_vars);
		}
	}
	$req->execute() or die(print_r($req->errorInfo()));
	
	$count = $req->fetch(2);
	$nbpages = ceil(abs($count['TOTAL'] / 50));
	
	if(isset($_GET['pg']) && preg_match("/^[0-9]*$/",$_GET['pg'])) //vérifie bien que 'pg' est bien existant et est numérique.
	{
		$_GET['pg'] = abs($_GET['pg']);
		$pg = (($_GET['pg'] - 1) * $resultsPerPages);
		$filter = $filter." LIMIT ".$pg.",".$resultsPerPages;
	}
	else
	{
		$filter = $filter." LIMIT 0,".$resultsPerPages;
	}
	
	$req = $bdd->prepare($query.$filter);
	
	$query = $query.$filter;
	
	if(count($sql_vars) > 0)
	{
		reset($sql_vars);
		for($x = 1; $x <= count($sql_vars); $x++)
		{
			$query = preg_replace('/\?/', current($sql_vars), $query, 1);
			$param = $x;
			$value = current($sql_vars);
			$req->bindParam($param, $value);
			next($sql_vars);
		}
	}
	
	$req->execute() or die(print_r($req->errorInfo()));
?>

<link rel="stylesheet" type="text/css" href="css/customers.css" />

<div id="searchbar">
	<div>
		<div>
			<div>
				<span>LIBELLE</span>
				<input type="text" placeholder="LIBELLE CLIENT" title="Code Client ou Libellé Client" name="CUSTOMER" />
			</div>
			<div>
				<span>SOLDE</span>
				<input type="number" placeholder="SOLDE" title="Solde supérieur ou égale à ..." name="BALANCE" style="width:5em" />
			</div>
			<div>
				<span>CONTRAT</span>
				<select name="CONTRACT" >
					<option value="2" selected>TOUS</option>
					<option value="1">OUI</option>
					<option value="0">NON</option>
				</select>
			</div>
			<div>
				<span></span>
				<input type="button" value="RECHERCHER" onclick="searchCustomer()" />
			</div>
		</div>
	</div>
	<div id="searchButton"><span>RECHERCHER</span></div>
</div>

<div id="search-results">
		<div id="inter-header">
			<?php if(!isset($_GET['srch'])): ?>
			<div id="inter-type">
				<a href="gmao.php?page=customers" <?=empty($_GET['contract']) && $_GET['contract'] != "0" ? "class='select gmao_link'" : "class='gmao_link'"?>><span>TOUS</span></a>
				<a href="gmao.php?page=customers&contract=1" <?=($_GET['contract'] == "1") ? "class='select gmao_link'" : "class='gmao_link'"?>><span>SOUS CONTRAT</span></a>
				<a href="gmao.php?page=customers&contract=0" <?=($_GET['contract'] == "0") ? "class='select gmao_link'" : "class='gmao_link'"?>><span>HORS CONTRAT</span></a>
			</div>
			<?php else: ?>
			<div id="inter-type">
				<a class="gmao_link" href="" class="select"><span>RESULTAT DE LA RECHERCHE</span></a>
				<a class="gmao_link" href="gmao.php?page=customers"><span>ANNULER</span></a>
			</div>
			<?php endif; ?>
		</div>

	<div id="inter-result">
		<div id="inter-tools">
		</div>
		<table id="table-result">
			<thead>
				<tr>
					<th><input type="checkbox" onclick="checkAll(this)"/></th>
					<th class="">
						<a class="gmao_link" href="gmao.php?page=customers<?=$url?>&sort=code<?=($_GET['sort'] == 'code' && empty($_GET['order'])) ? "&order=asc" : "" ?>">
							CODE<?=($_GET['sort'] == 'code') ? ($_GET['order'] == 'asc') ? "▲" : "▼" : ""?>
						</a>
					</th>
					<th class="">
						<a class="gmao_link" href="gmao.php?page=customers<?=$url?>&sort=nom<?=($_GET['sort'] == 'nom' && empty($_GET['order'])) ? "&order=asc" : "" ?>">
							LIBELLE<?=($_GET['sort'] == 'nom') ? ($_GET['order'] == 'asc') ? "▲" : "▼" : ""?>
						</a>
					</th>
					<th class="">
						<a class="gmao_link" href="gmao.php?page=customers<?=$url?>&sort=adlivr_ligne1<?=($_GET['sort'] == 'adlivr_ligne1' && empty($_GET['order'])) ? "&order=asc" : "" ?>">
							ADRESSE<?=($_GET['sort'] == 'adlivr_ligne1') ? ($_GET['order'] == 'asc') ? "▲" : "▼" : ""?>
						</a>
					</th>
					<th class="">
						<a class="gmao_link" href="gmao.php?page=customers<?=$url?>&sort=adlivr_code_postal<?=($_GET['sort'] == 'adlivr_code_postal' && empty($_GET['order'])) ? "&order=asc" : "" ?>">
							CODE POSTAL<?=($_GET['sort'] == 'adlivr_code_postal') ? ($_GET['order'] == 'asc') ? "▲" : "▼" : ""?>
						</a>
					</th>
					<th class="">
						<a class="gmao_link" href="gmao.php?page=customers<?=$url?>&sort=adlivr_ville<?=($_GET['sort'] == 'adlivr_ville' && empty($_GET['order'])) ? "&order=asc" : "" ?>">
							VILLE<?=($_GET['sort'] == 'adlivr_ville') ? ($_GET['order'] == 'asc') ? "▲" : "▼" : ""?>
						</a>
					</th>
					<th class="">
						<a class="gmao_link" href="gmao.php?page=customers<?=$url?>&sort=solde<?=($_GET['sort'] == 'solde' && empty($_GET['order'])) ? "&order=asc" : "" ?>">
							SOLDE<?=($_GET['sort'] == 'solde') ? ($_GET['order'] == 'asc') ? "▲" : "▼" : ""?>
						</a>
					</th>
					<th class="">
						<a class="gmao_link" href="gmao.php?page=customers<?=$url?>&sort=contract<?=($_GET['sort'] == 'contract' && empty($_GET['order'])) ? "&order=asc" : "" ?>">
							CONTRAT<?=($_GET['sort'] == 'contract') ? ($_GET['order'] == 'asc') ? "▲" : "▼" : ""?>
						</a>
					</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
					if($nbpages == 0 && isset($_GET['srch'])):
				?>
					<tr>
						<td colspan="9" style="background:#014b4b"><h1 style="text-align:center">AUCUN RESULTAT</h1></td>
					</tr>					
				<?php
					endif;
					
					while($customer = $req->fetch(2)):
					if(empty($customer["CONTRAT_AU"]))
					{
						$contract = false;
					}
					else
					{
						$contract = explode("/", $customer["CONTRAT_AU"]);
						$contract = $contract[2]."-".$contract[1]."-".$contract[0];
					}
					
					$title = "Contrat : ".(!empty($customer['CONTRAT_DU']) ? $customer['CONTRAT_DU'] : "???")." | ".(!empty($customer['CONTRAT_AU']) ? $customer['CONTRAT_AU'] : "???");
				?>
					<tr title="<?=$title?>">
						<td>
							<input type="checkbox" value="<?=$customer['CODE']?>" />
						</td>
						<td>
							<?=$customer['CODE']?>
						</td>
						<td>
							<?=$customer['NOM']?>
						</td>
						<td>
							<?=$customer['ADLIVR_LIGNE1']?>
						</td>
						<td>
							<?=$customer['ADLIVR_CODE_POSTAL']?>
						</td>
						<td>
							<?=$customer['ADLIVR_VILLE']?>
						</td>
						<td>
							<?=number_format($customer['SOLDE'],2,","," ")." €"?>
						</td>
						<td>
							<?php
								$contrat = date("Y-m-d", strtotime($contract));
								if(empty($customer['CONTRAT_AU']))
								{
									echo "<span style='color:grey'>???</span>";
								}
								else if($date < $contrat)
								{
									echo "<span style='color:lime'>OUI</span>";
								}
								else if($date > $contrat)
								{
									echo "<span style='color:red'>NON</span>";
								}
							?>
						</td>
						<td class="editTools">
							<a href="" target="_blank"><img src="images/view.png" title="Visualiser"/></a>
							<!-- <a href="#" title="Editer"><img src="images/edit.png" title="Editer" /></a> -->
						</td>
					</tr>
				<?php endwhile; ?>
			</tbody>
		</table>
		<div id="inter-footer">
			<div id="inter-infos"><span> Total : <?=$count['TOTAL']?></span></div>
			<div id="inter-paginer">
				<?php
					$request = preg_replace("#&pg=".$_GET['pg']."|pg=".$_GET['pg']."&|pg=".$_GET['pg']."#", "" ,$_SERVER['QUERY_STRING'])."&pg=";
					if(isset($_GET['pg']))
					{
						if($_GET['pg'] != 1)
						{
							echo "<a class='gmao_link' href='gmao.php?".$request.abs($_GET['pg'] - 1)."'><span><</span></a>"; 
						}
						if($_GET['pg'] <= 5 && $nbpages <= 5)
						{
							for($x = 1; $x <= $nbpages; $x++)
							{
								echo "<a class='gmao_link' href='gmao.php?".$request.abs($x)."' ".(($x == $_GET['pg']) ? "class='activePage'" : "")."><span>".$x."</span></a>";
							}
						}
						else if($_GET['pg'] <= 5 && $nbpages > 5)
						{
							for($x = 1; $x <= 5; $x++)
							{
								echo "<a class='gmao_link' href='gmao.php?".$request.abs($x)."' ".(($x == $_GET['pg']) ? "class='activePage'" : "")."><span>".$x."</span></a>";
							}
							echo "<a class='gmao_link' href='#' disin='1'><span>...</span></a><a class='gmao_link' href='gmao.php?".$request.abs($nbpages)."'><span>".$nbpages."</span></a>";
						}
						else if($_GET['pg'] > $nbpages - 5)
						{
							echo "<a class='gmao_link' href='gmao.php?".$request."1' ><span>1</span></a><a class='gmao_link' href='#' disin='1'><span>...</span></a>";
							for($x = $nbpages - 5; $x <= $nbpages; $x++)
							{
								echo "<a class='gmao_link' href='gmao.php?".$request.abs($x)."' ".(($x == $_GET['pg']) ? "class='activePage'" : "")."><span>".$x."</span></a>";
							}
						}
						else if($_GET['pg'] > 5 && $nbpages > 5)
						{
							echo "<a class='gmao_link' href='gmao.php?".$request."1'><span>1</span></a><a class='gmao_link' href='#' disin='1'><span>...</span></a>";
							for($x = $_GET['pg'] - 2; $x <= $_GET['pg'] + 2; $x++)
							{
								echo "<a class='gmao_link' href='gmao.php?".$request.abs($x)."' ".(($x == $_GET['pg']) ? "class='activePage'" : "")."></span>".$x."<span></a>";
							}
							echo "<a class='gmao_link' href='#' disin='1'><span>...</span></a><a class='gmao_link' href='gmao.php?".$request.abs($nbpages)."'><span>".$nbpages."</span></a>";
						}
						if($_GET['pg'] != $nbpages)
						{
							echo "<a class='gmao_link' href='gmao.php?".$request.abs($_GET['pg'] + 1)."'><span>></span></a>";
						}
					}
					else if($nbpages != 1 && $nbpages <= 5)
					{
						if(isset($_GET['pg']) && $_GET['pg'] != 1)
						{
							echo "<a class='gmao_link' href=''><span><</span></a>";
						}
						for($x = 1; $x <= $nbpages; $x++)
						{
							echo "<a class='gmao_link' href='gmao.php?".$request.abs($x)."' ".(($x == 1) ? "class='activePage'" : "")."><span>".$x."</span></a>";
						}
						if($_GET['pg'] != $nbpages)
						{
							echo "<a class='gmao_link' href='gmao.php?".$request."2'><span>></span></a>";
						}
					}
					else if($nbpages != 1 && $nbpages > 5)
					{
						if(isset($_GET['pg']) && $_GET['pg'] != 1)
						{
							echo "<a class='gmao_link' href=''><span><</span></a>";
						}
						for($x = 1; $x <= 5; $x++)
						{
							echo "<a class='gmao_link' href='gmao.php?".$request.abs($x)."' ".(($x == 1) ? "class='activePage'" : "")."><span>".$x."</span></a>";
						}
						if($_GET['pg'] != $nbpages)
						{
							echo "<a class='gmao_link' href='#' disin='1'><span>...</span></a><a class='gmao_link' href='gmao.php?".$request.$nbpages."'><span>".$nbpages."</span></a><a href='gmao.php?".$request."2'><span>></span></a>";
						}
					}
				?>
			</div>
		</div>
	</div>
</div>

<script>
	//$("#inter-result").on('click', 'tbody tr', function(e){this.getElementsByTagName("input")[0].click();});

	//$("#inter-result").on('click', 'tbody td a', function(e){this.parentNode.parentNode.click();});

	$("#inter-result").on('click', 'td input', function(e){
		if(!this.checked)
		{
			this.parentNode.parentNode.style.background = "";
		}
		else
		{
			this.parentNode.parentNode.style.background = "rgba(0, 0, 0, 0.5)";
		}
	});
	
	function checkAll(input)
	{
		if(input.checked)
		{
			for(x = 0; x < $("td input").length; x++)
			{
				$("td input")[x].checked = true;
				$("td input")[x].parentNode.parentNode.style.background = "rgba(0, 0, 0, 0.5)";
			}
		}
		else
		{
			for(x = 0; x < $("td input").length; x++)
			{
				$("td input")[x].checked = false;
				$("td input")[x].parentNode.parentNode.style.background = "";
			}
		}
	}
</script>
<script src="scripts/customers.js" />