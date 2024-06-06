<?php
	require('../../inc/config.inc.php');
	
	$agencies = $bdd->query("SELECT * FROM AGENCES ORDER BY NAME") or die(print_r($bdd->errorInfo()));
	
	$users = $bdd->query("SELECT * FROM USERS_INTER ORDER BY NAME") or die(print_r($bdd->errorInfo()));
	
	$req = $bdd->query("SELECT * FROM SETTINGS WHERE NAME = 'AUTO_ARCHIVE_INTERVAL'") or die(print_r($bdd->errorInfo()));
	$interval = $req->fetch(2);
	
	$req = $bdd->query("SELECT * FROM SETTINGS WHERE NAME = 'RESULTS_PER_PAGES'") or die(print_r($bdd->errorInfo()));
	$resultsPerPages = ($req->fetch(2))['VALUE'];
	
	$interval = date('Y-m-d H:i:s',(strtotime("-".$interval['VALUE'])));
	$date = date('Y-m-d H:i:s');
	
	$query = "SELECT INTER.*, USERS_INTER.NAME AS TECHNICIAN_NAME FROM INTER LEFT JOIN USERS_INTER ON INTER.USER_UPLOAD = USERS_INTER.ID";
	$queryCount = "SELECT COUNT(ID) AS TOTAL FROM INTER";
	$filter = "";
	$url = "";
	
	$sql_vars = array();
	
	if(isset($_GET['archives']))
	{
		$filter = $filter." WHERE IS_ARCHIVED = '1'";
	}
	else if(!isset($_GET['srch']))
	{
		$filter = $filter." WHERE DATE_END BETWEEN '".$interval."' AND '".$date."' AND IS_ARCHIVED = '0'";
	}
	else
	{
		switch ($_GET['archived'])
		{
			case '0':
				$filter = $filter." WHERE IS_ARCHIVED = '0'";
				$url = $url."&archived=0";
				break;
			case '1':
				$filter = $filter." WHERE IS_ARCHIVED = '1'";
				$url = $url."&archived=1";
				break;
			default:
				$filter = $filter." WHERE IS_ARCHIVED >= '-1'";
				$url = $url."&archived=2";
				break;
		}
	}
	
	switch ($_GET['type'])
	{
		case 'install':
			$filter = $filter." AND INSTALL = '1'";
			$url = "&type=install";
		break;
		case 'maintenance':
			$filter = $filter." AND MAINTENANCE = '1'";
			$url = "&type=maintenance";
		break;
		case 'training':
			$filter = $filter." AND TRAINING = '1'";
			$url = "&type=training";
		break;
		case 'renewal':
			$filter = $filter." AND RENEWAL = '1'";
			$url = "&type=renewal";
		break;
		case 'recovery':
			$filter = $filter." AND RECOVERY = '1'";
			$url = "&type=recovery";
		break;
		default:
			$url = isset($_GET['archives']) ? "&archives=1" : "";
		break;
	}
	
	if(!empty($_GET['id']))
	{
		$filter = $filter." AND INTER.ID = ?";
		$sql_vars['id'] = $_GET['id'];
		$url = $url."&id=".$_GET['id'];
	}
	
	if(!empty($_GET['date_start']))
	{
		$filter = $filter." AND (DATE_UPLOADED BETWEEN ?";
		$sql_vars['date_start'] = $_GET['date_start'];
		$url = $url."&date_start=".$_GET['date_start'];
	}
	
	if(!empty($_GET['date_start']) && !empty($_GET['date_end']))
	{
		$filter = $filter." AND ?)";
		$sql_vars['date_end'] = $_GET['date_end'];
		$url = $url."&date_end=".$_GET['date_end'];
	}
	else if(!empty($_GET['date_start']))
	{
		$filter = $filter." AND '".date("Y-m-d")."')";
		$url = $url."&date_end=".$_GET['date_end'];
	}
	
	if(!empty($_GET['user']))
	{
		$filter = $filter." AND USER_UPLOAD = ?";
		$sql_vars['user'] = $_GET['user'];
		$url = $url."&user=".$_GET['user'];
	}
	
	if(!empty($_GET['customer']))
	{
		$filter = $filter." AND (UPPER(CUSTOMER_LABEL) LIKE UPPER(?)) OR (UPPER(CUSTOMER_CODE) LIKE UPPER(?))";
		$sql_vars['customer'] = "%".urldecode($_GET['customer'])."%";
		$sql_vars['customercode'] = "%".urldecode($_GET['customer'])."%";
		$url = $url."&customer=".$_GET['customer'];
	}
	
	if(!empty($_GET['cost']))
	{
		$filter = $filter." AND COST >= ?";
		$sql_vars['cost'] = $_GET['cost'];
		$url = $url."&cost=".$_GET['cost'];
	}
	
	if(!empty($_GET['description']))
	{
		$filter = $filter." AND (UPPER(DESCRIPTION) LIKE UPPER(?))";
		$sql_vars['description'] = "%".urldecode($_GET['description'])."%";
		$url = $url."&description=".$_GET['description'];
	}
	
	if(isset($_GET['agency']))
	{
		$filter = $filter." AND INTER.AGENCY = ?";
		$sql_vars['agency'] = $_GET['agency'];
		$url = $url."&agency=".$_GET['agency'];
	}
	
	if(!empty($_GET['srch']))
	{
		$url = $url."&srch=1";
	}
	
	if(!empty($_GET['sort']))
	{
		$filter = $filter." ORDER BY ".strtoupper($_GET['sort']);
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
		$filter = $filter." ORDER BY DATE_END DESC";
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

<link rel="stylesheet" type="text/css" href="css/intervention.css" />

<div id="searchbar">
	<div>
		<div>
			<div>
				<span>ID</span>
				<input type="text" placeholder="ID" title="Numéro de l'intervention" name="ID" style="width:4em" />
			</div>
			<div>
				<span>TYPE</span>
				<select name="TYPE">
					<option value="">TOUTES</option>
					<option value="INSTALL">INSTALLATIONS</option>
					<option value="RECOVERY">RECUPERATIONS</option>
					<option value="RENEWAL">RENOUVELLEMENTS</option>
					<option value="MAINTENANCE">MAINTENANCES</option>
					<option value="TRAINING">FORMATIONS</option>
				</select>
			</div>
			<div>
				<span>DATE DE DEBUT</span>
				<input type="date" placeholder="DATE DE DEBUT" name="DATE_START" />
			</div>
			<div>
				<span>DATE DE FIN</span>
				<input type="date" placeholder="DATE DE FIN" name="DATE_END" />
			</div>
			<div>
				<span>UTILISATEUR</span>
				<select name="USER" >
					<option value="" selected>TOUS</option>
					<?php
						while($user = $users->fetch(2)):
							if(!empty($user['NAME'])):
					?>
								<option value="<?=$user['ID']?>"><?=strtoupper($user['NAME'])?></option>
					<?php
							endif;
						endwhile;
					?>
				</select>
			</div>
			<div>
				<span>CLIENT</span>
				<input type="text" placeholder="CLIENT" title="Code Client ou Libellé" name="CUSTOMER" />
			</div>
			<div>
				<span>COUT</span>
				<input type="number" placeholder="COUT" title="Cout supérieur ou égale à ..." name="COST" style="width:5em" />
			</div>
			<div>
				<span>DESCRIPTION</span>
				<input type="text" placeholder="DESCRIPTION" title="Mots clefs renseigné dans la description" name="DESCRIPTION" />
			</div>
			<div>
				<span>AGENCE</span>
				<select name="AGENCY" >
					<option value="" selected>TOUTES</option>
					<?php
						while($agency = $agencies->fetch(2)):
							if(!empty($agency['NAME'])):
					?>
								<option value="<?=$agency['ID']?>"><?=$agency['NAME']?></option>
					<?php
							endif;
						endwhile;
					?>
				</select>
			</div>
			<div>
				<span>ARCHIVES</span>
				<select name="ARCHIVED" >
					<option value="2" selected>TOUS</option>
					<option value="1">ARCHIVE</option>
					<option value="0">NON ARCHIVE</option>
				</select>
			</div>
			<div>
				<span></span>
				<input type="button" value="RECHERCHER" onclick="searchInter()" />
			</div>
		</div>
	</div>
	<div id="searchButton"><span>RECHERCHER</span></div>
</div>

<div id="search-results">
		<div id="inter-header">
			<?php if(!isset($_GET['srch'])): ?>
			<div id="inter-type">
				<a href="gmao.php?page=inter<?=isset($_GET['archives']) ? "&archives=1" : ""?>" <?=empty($_GET['type']) ? "class='select gmao_link'" : "class='gmao_link'"?>><span>TOUTES</span></a>
				<a href="gmao.php?page=inter&type=install<?=isset($_GET['archives']) ? "&archives=1" : ""?>" <?=($_GET['type'] == "install") ? "class='select gmao_link'" : "class='gmao_link'"?>><span>INSTALLATIONS</span></a>
				<a href="gmao.php?page=inter&type=recovery<?=isset($_GET['archives']) ? "&archives=1" : ""?>" <?=($_GET['type'] == "recovery") ? "class='select gmao_link'" : "class='gmao_link'"?>><span>RECUPERATIONS</span></a>
				<a href="gmao.php?page=inter&type=renewal<?=isset($_GET['archives']) ? "&archives=1" : ""?>" <?=($_GET['type'] == "renewal") ? "class='select gmao_link'" : "class='gmao_link'"?>><span>RENOUVELLEMENTS</span></a>
				<a href="gmao.php?page=inter&type=maintenance<?=isset($_GET['archives']) ? "&archives=1" : ""?>" <?=($_GET['type'] == "maintenance") ? "class='select gmao_link'" : "class='gmao_link'"?>><span>MAINTENANCE</span></a>
				<a href="gmao.php?page=inter&type=training<?=isset($_GET['archives']) ? "&archives=1" : ""?>" <?=($_GET['type'] == "training") ? "class='select gmao_link'" : "class='gmao_link'"?>><span>FORMATIONS</span></a>
			</div>
			<?php else: ?>
			<div id="inter-type">
				<a class="gmao_link" href="" class="select"><span>RESULTAT DE LA RECHERCHE</span></a>
				<a class="gmao_link" href="gmao.php?page=inter<?=isset($_GET['archives']) ? "&archives=1" : ""?>"><span>ANNULER</span></a>
			</div>
			<?php endif; ?>
		</div>

	<div id="inter-result">
		<div id="inter-tools">
			<span>Sélection : </span><a class="gmao_link" href=""><img src="images/icon_archive.png" width="25px" /><span>Archiver</span></a><a class="gmao_link" href=""><img src="images/resend.png" width="25px" /><span>Renvoyer</span></a>
		</div>
		<table id="table-result">
			<thead>
				<tr>
					<th><input type="checkbox" onclick="checkAll(this)"/></th>
					<th class="">
						<a class="gmao_link" href="gmao.php?page=inter<?=$url?>&sort=id<?=($_GET['sort'] == 'id' && empty($_GET['order'])) ? "&order=asc" : "" ?>">
							ID<?=($_GET['sort'] == 'id') ? ($_GET['order'] == 'asc') ? "▲" : "▼" : ""?>
						</a>
					</th>
					<th class="">
						<a class="gmao_link" href="gmao.php?page=inter<?=$url?>&sort=date_end<?=($_GET['sort'] == 'date_end' && empty($_GET['order'])) ? "&order=asc" : "" ?>">
							DATE<?=($_GET['sort'] == 'date_end') ? ($_GET['order'] == 'asc') ? "▲" : "▼" : ""?>
						</a>
					</th>
					<th class="">
						<a class="gmao_link" href="gmao.php?page=inter<?=$url?>&sort=user_upload<?=($_GET['sort'] == 'user_upload' && empty($_GET['order'])) ? "&order=asc" : "" ?>">
							TECHNICIEN<?=($_GET['sort'] == 'user_upload') ? ($_GET['order'] == 'asc') ? "▲" : "▼" : ""?>
						</a>
					</th>
					<th class="">
						<a class="gmao_link" href="gmao.php?page=inter<?=$url?>&sort=customer_code<?=($_GET['sort'] == 'customer_code' && empty($_GET['order'])) ? "&order=asc" : "" ?>">
							CODE CLIENT<?=($_GET['sort'] == 'customer_code') ? ($_GET['order'] == 'asc') ? "▲" : "▼" : ""?>
						</a>
					</th>
					<th class="">
						<a class="gmao_link" href="gmao.php?page=inter<?=$url?>&sort=customer_label<?=($_GET['sort'] == 'customer_label' && empty($_GET['order'])) ? "&order=asc" : "" ?>">
							DESIGNATION CLIENT<?=($_GET['sort'] == 'customer_label') ? ($_GET['order'] == 'asc') ? "▲" : "▼" : ""?>
						</a>
					</th>
					<th class="">
						<a class="gmao_link" href="gmao.php?page=inter<?=$url?>&sort=cost<?=($_GET['sort'] == 'cost' && empty($_GET['order'])) ? "&order=asc" : "" ?>">
							COUT<?=($_GET['sort'] == 'cost') ? ($_GET['order'] == 'asc') ? "▲" : "▼" : ""?>
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
						<td colspan="8" style="background:#014b4b"><h1 style="text-align:center">AUCUN RESULTAT</h1></td>
					</tr>					
				<?php
					endif;
					
					while($intervention = $req->fetch(2)):
					$title = $intervention['DESCRIPTION'];
					$type = "";
					if($intervention["INSTALL"])
					{
						$type = "INSTALLATION ";
					}
					if($intervention["MAINTENANCE"])
					{
						$type = $type."MAINTENANCE ";
					}
					if($intervention["RECOVERY"])
					{
						$type = $type."RECUPERATION ";
					}
					if($intervention["TRAINING"])
					{
						$type = $type."FORMATION ";
					}
					if($intervention["RENEWAL"])
					{
						$type = $type."RENOUVELLEMENT ";
					}
					
					$title = $type."\n\n".$title;
				?>
					<tr title="<?=$title?>">
						<td>
							<input type="checkbox" value="<?=$intervention['ID']?>" />
						</td>
						<td>
							<?=$intervention['ID']?>
						</td>
						<td>
							<?=date('d/m/Y',strtotime($intervention['DATE_END']))?>
						</td>
						<td>
							<?=$intervention['TECHNICIAN_NAME']?>
						</td>
						<td <?=($intervention['CUSTOMER_CODE'] == "NEW") ? "style='color:lime';text-shadow:0 0 2px black" : ""?>>
							<?=$intervention['CUSTOMER_CODE']?>
						</td>
						<td>
							<?=$intervention['CUSTOMER_LABEL']?>
						</td>
						<td>
							<?=($intervention['COST'] > 0) ? number_format($intervention['COST'],2,","," ")." €" : ""?>
						</td>
						<td class="editTools">
							<a disin="1" href="gmao/intervention/view_inter.php?inter=<?=$intervention['ID']?>&pdf=1" target="_blank"><img src="images/view.png" title="Visualiser"/></a>
							<a href="#" title="Editer"><img src="images/edit.png" title="Editer" /></a>
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
							echo "<a href='index.php?".$request.abs($_GET['pg'] - 1)."'><span><</span></a>"; 
						}
						if($_GET['pg'] <= 5 && $nbpages <= 5)
						{
							for($x = 1; $x <= $nbpages; $x++)
							{
								echo "<a href='index.php?".$request.abs($x)."' ".(($x == $_GET['pg']) ? "class='activePage'" : "")."><span>".$x."</span></a>";
							}
						}
						else if($_GET['pg'] <= 5 && $nbpages > 5)
						{
							for($x = 1; $x <= 5; $x++)
							{
								echo "<a href='index.php?".$request.abs($x)."' ".(($x == $_GET['pg']) ? "class='activePage'" : "")."><span>".$x."</span></a>";
							}
							echo "<a href='#' ><span>...</span></a><a href='index.php?".$request.abs($nbpages)."'><span>".$nbpages."</span></a>";
						}
						else if($_GET['pg'] > $nbpages - 5)
						{
							echo "<a href='index.php?".$request."1' ><span>1</span></a><a href='#' ><span>...</span></a>";
							for($x = $nbpages - 5; $x <= $nbpages; $x++)
							{
								echo "<a href='index.php?".$request.abs($x)."' ".(($x == $_GET['pg']) ? "class='activePage'" : "")."><span>".$x."</span></a>";
							}
						}
						else if($_GET['pg'] > 5 && $nbpages > 5)
						{
							echo "<a href='index.php?".$request."1'><span>1</span></a><a href='#' ><span>...</span></a>";
							for($x = $_GET['pg'] - 2; $x <= $_GET['pg'] + 2; $x++)
							{
								echo "<a href='index.php?".$request.abs($x)."' ".(($x == $_GET['pg']) ? "class='activePage'" : "")."></span>".$x."<span></a>";
							}
							echo "<a href='#'><span>...</span></a><a href='index.php?".$request.abs($nbpages)."'><span>".$nbpages."</span></a>";
						}
						if($_GET['pg'] != $nbpages)
						{
							echo "<a href='index.php?".$request.abs($_GET['pg'] + 1)."'><span>></span></a>";
						}
					}
					else if($nbpages != 1 && $nbpages <= 5)
					{
						if(isset($_GET['pg']) && $_GET['pg'] != 1)
						{
							echo "<a href=''><span><</span></a>";
						}
						for($x = 1; $x <= $nbpages; $x++)
						{
							echo "<a href='index.php?".$request.abs($x)."' ".(($x == 1) ? "class='activePage'" : "")."><span>".$x."</span></a>";
						}
						if($_GET['pg'] != $nbpages)
						{
							echo "<a href='index.php?".$request."2'><span>></span></a>";
						}
					}
					else if($nbpages != 1 && $nbpages > 5)
					{
						if(isset($_GET['pg']) && $_GET['pg'] != 1)
						{
							echo "<a href=''><span><</span></a>";
						}
						for($x = 1; $x <= 5; $x++)
						{
							echo "<a href='index.php?".$request.abs($x)."' ".(($x == 1) ? "class='activePage'" : "")."><span>".$x."</span></a>";
						}
						if($_GET['pg'] != $nbpages)
						{
							echo "<a href='#'><span>...</span></a><a href='index.php?".$request.$nbpages."'><span>".$nbpages."</span></a><a href='index.php?".$request."2'><span>></span></a>";
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
<script src="scripts/intervention.js" />