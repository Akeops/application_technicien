<?php
	//require('/home/tacteose/sav/INTER/ADMIN/inc/accounts.inc.php');

	session_start();
	
	if(!isset($_SESSION['name']))
	{
		header('Location: ../');
	}
	
	require('head.inc.php');
	
	$today = date("Y-m-d");
	
	$todayY = date("Y");
	$todayM = date("m");
	$todayD = date("d");
	
	$limitDate = "prout";
	
	$req = $bdd->prepare("UPDATE INTERVENTIONS SET ARCHIVE = '1' WHERE DATE < :date AND (ARCHIVE = 0 OR ARCHIVE IS NULL)");
	$req->execute(array('date' => date('Y-m-d H:m:s', strtotime('-2 month')))) or die(print_r($req->errorInfo()));
	
	// $req = $bdd->prepare("UPDATE INTERVENTIONS SET ARCHIVE = '0' WHERE DATE > :date AND ARCHIVE = 1");
	// $req->execute(array('date' => date('Y-m-d H:m:s', strtotime('-2 month')))) or die(print_r($req->errorInfo()));
	
	$sql_query =
	"SELECT
		OWNER.NAME OWNER, UPDATED_BY.NAME UPDATED, INTERVENTIONS.*
	FROM
		INTERVENTIONS
	INNER JOIN
		USERS_INTER OWNER ON (INTERVENTIONS.TECHNICIEN = OWNER.ID)
	INNER JOIN
		USERS_INTER UPDATED_BY ON (INTERVENTIONS.UPDATED_BY = UPDATED_BY.ID)
	WHERE
		INTERVENTIONS.ID IS NOT NULL";
	
	switch($_GET['_action'])
	{
		case 'waiting':
			$sql_query = $sql_query." AND ATTENTE = 1 AND NOT(ARCHIVE = 1)";
			break;
		case 'self':
			switch($_GET['_type'])
			{
				case 'waiting':
					$sql_query = $sql_query." AND (UPDATED_BY = '" . $_SESSION['id'] . "' OR TECHNICIEN = '" . $_SESSION['id'] . "') AND ATTENTE = 1  AND NOT(ARCHIVE = 1)";
					break;
				default:
					$sql_query = $sql_query." AND (UPDATED_BY = '" . $_SESSION['id'] . "' OR TECHNICIEN = '" . $_SESSION['id'] . "')  AND ARCHIVE = 0";
					break;
			}
			break;
		case 'archives':
			$sql_query = $sql_query." AND ARCHIVE = 1";
			break;
		default:
			$sql_query = $sql_query." AND NOT(ARCHIVE = 1)";
			break;
	}

	$req = $bdd->query($sql_query." GROUP BY CLIENT ORDER BY CLIENT") or die(print_r($bdd->errorInfo()));
	$clients = $req->fetchAll(2);
	
	if(!empty($_POST))
	{
		$search = array();
		
		if($_POST['id'] != "")
		{
			$sql_query = $sql_query." AND INTERVENTIONS.ID = :id";
			$search['id'] = $_POST['id'];
		}
		
		if($_POST['last_update'] != "")
		{
			$sql_query = $sql_query." AND INTERVENTIONS.LAST_UPDATE <= :last_update";
			$search['last_update'] = $_POST['last_update']." 23:59:59";
		}
		
		if($_POST['date_created'] != "")
		{
			$sql_query = $sql_query." AND INTERVENTIONS.DATE <= :date";
			$search['date'] = $_POST['date_created']." 23:59:59";
		}
		
		if($_POST['updated_by'] != "")
		{
			$sql_query = $sql_query." AND INTERVENTIONS.UPDATED_BY = :updated_by";
			$search['updated_by'] = $_POST['updated_by'];
		}
		
		if($_POST['created_by'] != "")
		{
			$sql_query = $sql_query." AND INTERVENTIONS.TECHNICIEN = :created_by";
			$search['created_by'] = $_POST['created_by'];
		}
		
		if($_POST['client'] != "")
		{
			$sql_query = $sql_query." AND INTERVENTIONS.CLIENT LIKE :client";
			$search['client'] = $_POST['client'];
		}
	}
	
	switch($_POST['range'])
	{
		case 'id':
			$sql_query = $sql_query." ORDER BY ID DESC";
			break;
		case 'last_update':
			$sql_query = $sql_query." ORDER BY LAST_UPDATE DESC";
			break;
		case 'updated_by':
			$sql_query = $sql_query." ORDER BY UPDATED ASC";
			break;
		case 'date':
			$sql_query = $sql_query." ORDER BY DATE DESC";
			break;
		case 'client':
			$sql_query = $sql_query." ORDER BY CLIENT ASC";
			break;
		case 'contrat':
			$sql_query = $sql_query." ORDER BY CONTRAT DESC";
			break;
		case 'billing':
			$sql_query = $sql_query." ORDER BY BILLING DESC";
			break;
		case 'technicien':
			$sql_query = $sql_query." ORDER BY OWNER ASC";
			break;
		default:
			$sql_query = $sql_query." ORDER BY INTERVENTIONS.ID DESC";
			break;
	}
	
	$req = $bdd->query("SELECT NAME FROM USERS_INTER") or die(print_r($bdd->errorInfo()));
	$users = $req->fetchAll(2);
	
	if(empty($_POST))
	{
		$req = $bdd->query($sql_query) or die(print_r($bdd->errorInfo()));
	}
	else
	{
		$req = $bdd->prepare($sql_query) or die(print_r($bdd->errorInfo()));
		$req->execute($search) or die(print_r($req->errorInfo()));;
	}
?>

<head>
	<link href='https://fonts.googleapis.com/css?family=Buda:300' rel='stylesheet'>
</head>

<div id="loading"></div>
<iframe name='open_inter' id='display_inter'></iframe>
<div id="list_inter">
	<span id="header">
		<div id="search_div">
			<h2><span>INTERVENTIONS</span></h2>
			<form style="margin-block-end: 0;" method="post" id="search">
				<table>
					<tr>
						<td class="ID src">
							<input placeholder="ID" type="number" name="id">
						</td>
						<td class="LAST_UPDATE src">
							<input placeholder="DERNIERE MODIFICTION" type="date" value="" name="last_update">
						</td>
						<td class="UPDATED_BY src">
							<select placeholder="MODIFIÉE PAR" name="updated_by">
								<option disabled selected value>Modifié par...</option>
								<?php for($x = 1; $x < count($users); $x++): ?>
									<option value="<?=$x?>"><?=$users[$x]['NAME']?></option>
								<?php endfor; ?>
							</select>
						</td>
						<td class="TECHNICIEN src">
							<select placeholder="RÉALISÉE PAR" name="created_by">
								<option disabled selected value>Réalisée par...</option>
								<?php for($x = 1; $x < count($users); $x++): ?>
									<option value="<?=$x?>"><?=$users[$x]['NAME']?></option>
								<?php endfor; ?>
							</select>
						</td>
						<td class="DATE src">
							<input placeholder="DATE DE CRÉATION" type="date" value="" name="date_created">
						</td>
						<td class="CLIENT src">
							<input placeholder="Recherche Client" type="text" style="width:49%" onchange="SEARCH(this.value)">
							<select placeholder="Sélection Client" name="client" style="width:50%">
								<option disabled selected value>Sélection client</option>
								<?php for($x = 0; $x < count($clients); $x++): ?>
									<option value="<?=$clients[$x]['CLIENT']?>"><?=$clients[$x]['CODE_CLIENT']." - "?><?=$clients[$x]['CLIENT']?></option>
								<?php endfor; ?>
							</select>
						</td>
						<td class="CONTRAT">
							<button type="submit">Rechercher</button><button type="reset">Reset</button>
						</td>
						<td class="BILLING src">
							<img src="http://sav.tacteo.fr/INTER/ADMIN/conf/img/logo_alpha_white.png" alt="TACTEO SE" height="40px">
						</td>
					</tr>
					<tr>
						<td colspan="8" id="result">
						</td>
					</tr>
					<tr id="head">
						<td onclick="range('id')">
							<b>ID</b>
						</td>
						<td onclick="range('last_update')">
							<b>DERNIERE MODIFICATION</b>
						</td>
						<td onclick="range('updated_by')">
							<b>MODIFIÉE PAR</b>
						</td>
						<td onclick="range('technicien')">
							<b>RÉALISÉE PAR</b>
						</td>
						<td onclick="range('date')">
							<b>DATE DE CREATION</b>
						</td>
						<td onclick="range('client')">
							<b>CLIENT</b>
						</td>
						<td onclick="range('contrat')">
							<b>SOUS CONTRAT</b>
						</td>
						<td onclick="range('billing')" style="border-right:none">
							<b>A FACTURER<br></b>(HT)
						</td>
					</tr>
				</table>
				<script>
					function SEARCH(search)
					{
						var result = document.getElementsByName('client')[0].getElementsByTagName('option');
						search = search.toUpperCase();
						
						for(var x = 0; x < result.length; x++)
						{
							string = result[x].innerText.toUpperCase();
							
							if((string.search(search)) > -1)
							{
								result[x].style.display = "";
							}
							else
							{
								result[x].style.display = "none";
							}
						}
					}
				</script>
			</form>
		</div>
		<form method="post" action="view_inter.php" target="<?php echo !isset($_HOME) ? "open_inter" : "_self";?>" id="select_inter">
	</span>
		<table id="list">
			<?php
				$count = $req->rowCount() + 1;
			
				while($inter = $req->fetch()):
					$count--;
					
					if($inter['ATTENTE'])
					{
						$class = "ATTENTE";
					}
					else
					{
						$class = "INTER";
					}
					
					if($inter['INSTALLATION'])
					{
						$install = "INSTALL";
					}
					else{
						$install = "";
					}
					
					$BILLING = floatval($inter['BILLING']);
					
					if($BILLING != 0)
					{
						$facture = "color:red;font-weight:bold;";
					}
					else
					{
						$facture = "";
					}
					
					if(strval($today) == strval(explode(" ", $inter['DATE'])[0]))
					{
						$dayInter = "dayInter";
					}
					else
					{
						$dayInter = "";
					}
					
					if($BILLING != $inter['BILLING'])
					{
						$bdd->query("UPDATE INTERVENTIONS SET BILLING = ".$BILLING." WHERE ID = ".$inter['ID']);
					}
			?>
				<tr class="<?php echo($class." "." ".$dayInter." ");?>" name="<?php echo $inter['ID'];?>" id="<?php echo $count;?>" onclick="SELECTED(<?php echo $count;?>)">
					<td class="ID">
						<?php echo $inter['ID'];?>
					</td>
					<td class="LAST_UPDATE">
						<?php
							list($date, $hour) = explode(" ", $inter['LAST_UPDATE']);
							$date = explode("-", $date);
							echo $date[2]."/".$date[1]."/".$date[0]." à ".$hour;
						?>
					</td>
					<td class="UPDATED_BY">
						<?php echo $inter['UPDATED'];?>
					</td>
					<td class="TECHNICIEN">
						<?php echo $inter['OWNER'];?>
					</td>
					<td class="DATE">
						<?php
							list($date, $hour) = explode(" ", $inter['DATE']);
							$date = explode("-", $date);
							echo $date[2]."/".$date[1]."/".$date[0]." à ".$hour;
						?>
					</td>
					<td class="CLIENT">
						<?php echo $inter['CLIENT'];?>
					</td>
					<?php
						if($inter['CONTRAT']):
					?>
						<td style='color:green' class='CONTRAT'>OUI</td>
					<?php
						else:
					?>
						<td style='color:red;font-weight:bold' class='CONTRAT'>NON</td>
					<?php
						endif;
					?>
					<td class="BILLING  <?=$install?>" style="<?php echo $facture;?>">
						<?php echo number_format($BILLING,2). " €";?>
					</td>
				</tr>
			<?php
				endwhile;
			?>
		</table>
		<input type="hidden" name="ID">
	</form>
	<form  id="ranging" method="post">
		<input type="hidden" name="range">
	</form>
</div>
<div type="button" class="vanish" id="mask_inter" onclick="show(this)"><span>^</span></div>

<style>
	body{
		background:			lightgrey;
		margin:				0;
		overflow:			auto;
	}
	
	table{
		white-space:	nowrap;
		font-family: 'Cabin Condensed';
		color: black;
		border-collapse: collapse;
		width: 100%;
		-webkit-touch-callout: none;
		-webkit-user-select: none;
		-khtml-user-select: none;
		-moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
	}
	
	tr:nth-child(even) {
		background-color: #989898;
	}
	
	td{
		border-left:		solid grey 1px;
		padding:	0.2em;
	}
	
	.INTER{
		background:	#adadad;
		cursor:		pointer;
		height:		25px;
		transition: all 0.1s linear;
	}
	
	.INTER:hover, .dayInter:nth-child(even):hover{
		background:	grey;
		transition: all 0s;
		color:		white;
	}
	
	.INTER:active{
		background-color: lightgrey;
	}
	
	.dayInter{
		background-color:	cadetblue;
	}
	
	.dayInter:nth-child(even){
		background-color:	#005858;
	}
	
	.ATTENTE{
		background:	antiquewhite;
		cursor:		pointer;
	}
	
	.INSTALL{
		background:	#76d8ff;
		text-shadow:	none;
	}
	
	#list_inter{
		position:		absolute;
		top:			0;
		width:			100%;
		height:			50vh;
		overflow:		auto;
		z-index:		0;
	}
	
	#display_inter{
		position:		absolute;
		bottom:			0;
		width:			100%;
		height:			50vh;
		border:			none;
		background:		url(../styles/images/loading.gif), white;
		background-repeat:		no-repeat;
		background-position:	center;
		border-top:		2px solid grey;
		z-index:		1;
		box-shadow:		0 17px 20px 20px rgba(0, 0, 0, 0.5);
		transition:		height 0.3s cubic-bezier(1, 0, 0, 1);
	}
	
	.vanish{
		background:		rgba(0,0,0,0.5);
		width:			160px;
		height:			20px;
		z-index:		2;
		border-radius:	0px 0px 20px 20px;
		cursor:			pointer;
		position:		absolute;
		left:			calc(50% - 80px);
	}
	
	#mask_inter{
		top:			50vh;
		transition:		top .3s cubic-bezier(1, 0, 0, 1);
		text-align:		center;
	}
	
	#mask_inter span{
		display:		block;
		color:			#ffffff70;
		font-weight:	bold;
		font-size:		25px;
		transform:		scaleX(2) rotateX(0deg) translateY(0%);
		transition:		all .3s linear;
	}
	
	#search_div{
		color:			white;
		border-bottom:	2px solid grey;
		top:			0;
		position:		sticky;
		box-shadow: 	0 0 10px black;
	}
	
	#search_div td{
		border-left:		1px solid rgba(0,0,0,0);
		background:			#014040;
	}
	
	#search_div h2{
		margin:			0;
		height:			45px;
		background:		#02735E;
		box-shadow:		inset 0 -8px 6px -6px black;
		font-family:	'Cabin Condensed';
		text-align:		center;
	}
	
	#search_div h2 span
	{
		display:		block;
		transform:		translateY(25%);
	}
	
	#search_div input{
		width:	100%;
		font-size:	18px;
	}
	
	#search_div select{
		width:	100%;
		font-size:	18px;
	}
	
	#header{
		position:	sticky;
		top:		0;
	}
	
	.ID{
		width:			40px;
		text-align:		center;
	}
	
	.BILLING{
		width:			130px;
		text-align:		center;
	}
	
	.LAST_UPDATE{
		width:			255px;
		text-align:		center;
	}
	
	.UPDATED_BY{
		width:			175px;
		text-align:		center;
	}
	
	.TECHNICIEN{
		width:			175px;
		text-align:		center;
	}
	
	.CONTRAT{
		text-align:		center;
		color:			white;
		width:			156px;
	}
	
	.DATE{
		width:					200px;
		text-align:				center;
	}
	
	.CLIENT{
		max-width:				434px;
	}
	
	#head td{
		color:					white;
		height:					2em;
		background:				#014040;
		cursor:					pointer;
		text-align:				center;
	}
	
	#head td:hover{
		text-decoration:		underline;
	}
	
</style>

<script>
	
	var dayInter = document.getElementsByClassName('dayInter');
	if(dayInter.length > 0){
		document.getElementsByClassName('dayInter')[dayInter.length - 1].style.borderBottom = "solid black 3px";
	}
	
	window.addEventListener("keydown", function(e) {
		// space and arrow keys
		if([32, 37, 38, 39, 40].indexOf(e.keyCode) > -1) {
			e.preventDefault();
		}
	}, false);

	document.onkeydown = checkKey;
	var list = document.getElementById('select_inter');
	var nb = list.getElementsByTagName('tr');
	
	//document.getElementById(nb.length+1).focus();
	
	var selectId = nb.length;
	
	<?php
		if($_GET['inter'] != null):
	?>
		var selectedInter = <?=$_GET['inter']?>;
		
		var interObj = document.getElementsByName(selectedInter)[0];
		
		var idInter = interObj.getAttribute("id");
		
		SELECTED(idInter);
	<?php else: ?>
		SELECTED(nb.length);
	<?php endif;?>
		
	
	function checkKey(e)
	{

		e = e || window.event;

		if (e.keyCode == '38')
		{
			if(selectId != nb.length + 1)
			{
				selectId++;
				SELECTED(selectId);
			}
		}
		else if (e.keyCode == '40')
		{
			if(selectId != 1)
			{
				selectId--;
				SELECTED(selectId);
			}
		}
		else if (e.keyCode == '37')
		{
		   // left arrow
		}
		else if (e.keyCode == '39')
		{
		   // right arrow
		}
	}

	function range(range)
	{
		var input = document.getElementsByName('range')[0]
		var form = document.getElementById('ranging');
		
		switch(range)
		{
			case 'id':
				input.value = 'id';
				form.submit();
				break;
			case 'last_update':
				input.value = 'last_update';
				form.submit();
				break;
			case 'updated_by':
				input.value = 'updated_by';
				form.submit();
				break;
			case 'date':
				input.value = 'date';
				form.submit();
				break;
			case 'client':
				input.value = 'client';
				form.submit();
				break;
			case 'contrat':
				input.value = 'contrat';
				form.submit();
				break;
			case 'billing':
				input.value = 'billing';
				form.submit();
				break;
			case 'technicien':
				input.value = 'technicien';
				form.submit();
				break;
		}
	}

	function show(button)
	{
		var iframe = document.getElementById('display_inter');
		var arrow = button.getElementsByTagName("span")[0];
		
		if(button.style.top == "50vh" || button.style.top == "")
		{
			button.style.top = "calc(0vh + 2px)";
			iframe.style.height = "calc(100vh - 2px)";
			arrow.style.transform = "scaleX(2) rotateX(180deg) translateY(30%)";
		}
		else
		{
			button.style.top = "50vh";
			iframe.style.height = "";
			arrow.style.transform = "";
		}
	}
	
	function SELECTED(id, search)
	{
		selectId = id;
		
		if(search)
		{
			var tr = document.getElementsByName(id)[0];
		}
		else
		{
			var tr = document.getElementById(id);
		}
	
		if(document.getElementsByName('ID')[0].value != "")
		{
			var old = document.getElementById(document.getElementsByName('ID')[0].value);
			old.style.textShadow = "";
			old.style.boxShadow = "";
			old.style.backgroundColor = "";
			old.style.height = "";
			old.style.color = "";
			old.style.transform = "";
		}

		tr.style.textShadow = "0px 0px 3px black";
		tr.style.boxShadow = "inset 0px 0px 8px 2px black";
		tr.style.height = "30px";
		tr.style.color = "white";
		tr.style.backgroundColor = "#404040";
		
		tr.scrollIntoView({behavior: "smooth", block: "center", inline: "nearest"});
		
		document.getElementsByName('ID')[0].value = tr.getAttribute("name");
		
		document.getElementById('select_inter').submit();
		
		if(search)
		{
			document.getElementsByName('ID')[0].value = tr.getAttribute('id');
		}
		else
		{
			document.getElementsByName('ID')[0].value = id;
		}
	}
</script>