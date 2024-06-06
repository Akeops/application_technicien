<?php
	require('./inc/accounts.inc.php');
	require('./ADMIN/inc/config.inc.php');
	session_start();
	
	$req = $bdd->prepare("SELECT PSEUDO, NAME FROM USERS_INTER WHERE NOT(ID = 0) ORDER BY ID");
	$req->execute();
	
	while($user = $req->fetch(2))
	{
		$users[$user['PSEUDO']] = $user['NAME'];
	}
	
	$session = $_SESSION['user'];
	
	?>

	<?php
	
	//die();
	
	if(isset($_GET['debug']))
	{
		$debug_mode = true;
		$hide = "";
		echo("<pre style='color:white;text-align:left;'>Connecté en tant que : " . $session . ". DEBUG ON.");
	}
	else
	{
		$debug_mode = false;
		$hide = "hidden";
		echo("<pre style='color:white;text-align:left;'>Connecté en tant que : " . $session . ".");
	}

	echo(" (<a href='inc/accounts.inc.php?logout' target='_self'>Deconnexion</a>");
	
	if($session == "leo"){
		echo (" | <a href='ADMIN' target='_self'>ADMIN</a>)</pre>");
	}
	else{
		echo (")</pre>");
	}
?>

<!DOCTYPE html>

<html>

	<head>
		<title>Bon d'intervention</title>
		<link rel="icon" href="images/icon.png">
		<link rel="stylesheet" type="text/css" href="style.css">
		<link href='https://fonts.googleapis.com/css?family=Armata' rel='stylesheet'>
		
		<link rel="apple-touch-icon" href="images/icon.png">
		<link rel="apple-touch-startup-image" href="images/icon.png">
		<meta name="apple-mobile-web-app-title" content="Intervention">
		<meta name="apple-mobile-web-app-capable" content="yes">
		
		<script src="js/PapaParse-4.6.0/papaparse.js"></script>
		<script src="js/common.js"></script>
		<script src="js/jquery.min.js"></script>
		<script src="js/sign.js"></script>
		<script src="js/apiGoogle.js"></script>
		<script src="js/jquery.min.js"></script>
		
		<meta charset="UTF-8">
	</head>
	
	<body>
		
		
		
		<iframe name="response" style="display:	none"></iframe>
		
		<input type="<?php echo($hide); ?>" id="question" style="position: absolute; left: 0; border:none;">
		
		<form id="form" action="tools/new_inter.php" target="response" method="POST">
		
		<fieldset id="LOADING">		<!-- 00 -->
			<img src="images/loading.gif" style="width: auto; margin: 20px auto;" alt="Chargement...">
			<h1>Chargement...</h1>
		</fieldset>
		
		<fieldset id="AGENCE">		<!-- 01 -->
			<h3>AGENCE</h3>
			
			<table style="font-size: 1.2em;margin:0 auto;text-align:center;">
				<tr>
					<td>
						<p style="display:	inline">FLOIRAC </p>
					</td>
					<td>
						<label class="switch">
							<input type="checkbox" class="toggle" onchange="agence('entry.433474814'); setTimeout(function(){next();}, 200);">
							<span class="slider"></span>
						</label>
					</td>
					<td>
						<p style="display:	inline"> BAYONNE</p>
					</td>
				</tr>
			</table>
			
			<div class="navigation">
				<div id="buttonNext" type="button" style="" onclick="next()"><a href="#">NEXT</a></div>
			</div>
			
			<input type="<?php echo($hide); ?>" name="entry.433474814" value="0" required>
		</fieldset>

		<fieldset id="OBJECT">		<!-- 02 -->
			<h3>OBJET</h3>
			
			<table style="font-size: 1.2em;margin:0 auto;">
				<tr>
					<td>
						<label class="switch">
							<input type="checkbox" name="entry.912488796" onchange="objrefresh()" value="INSTALLATION" required>
							<span class="sliderG"></span>
						</label>
					</td>
					<td>
						<p style="display:	inline"> INSTALLATION</p>
					</td>
				</tr>
				<tr>
					<td>
						<label class="switch">
							<input type="checkbox" name="entry.912488796" onchange="objrefresh()" value="FORMATION" required>
							<span class="sliderG"></span>
						</label>
					</td>
					<td>
						<p style="display:	inline"> FORMATION</p>
					</td>
				</tr>
				<tr>
					<td>
						<label class="switch">
							<input type="checkbox" name="entry.912488796" onchange="objrefresh()" value="MAINTENANCE" required>
							<span class="sliderG"></span>
						</label>
					</td>
					<td>
						<p style="display:	inline"> MAINTENANCE</p>
					</td>
				</tr>
				<tr>
					<td>
						<label class="switch">
							<input type="checkbox" name="entry.912488796" onchange="objrefresh()" value="RENOUVELLEMENT" required>
							<span class="sliderG"></span>
						</label>
					</td>
					<td>
						<p style="display:	inline"> RENOUVELLEMENT</p>
					</td>
				</tr>
				<tr>
					<td>
						<label class="switch">
							<input type="checkbox" name="entry.912488796" onchange="objrefresh()" value="RECUPERATION MATERIELS" required>
							<span class="sliderG"></span>
						</label>
					</td>
					<td>
						<p style="display:	inline"> RECUPERATION MATERIELS</p>
					</td>
				</tr>
			</table>
			<input name="entry.912488796" type="<?php echo($hide); ?>">
			
			<script>
				function objrefresh()
				{
					var value = document.getElementsByName("entry.912488796");
					var output = "";
					
					value[value.length-1].value = "";
					
					for(var x = 0; x < value.length; x++)
					{
						if(value[x].checked)
						{
							output = output + value[x].value + ",";
						}					
					}
					
					output = output.substring(0, output.length - 1);
					value[value.length-1].value = output;
				}
			</script>
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="list = object('entry.912488796'); next();checking('OBJECT');"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="HOUR_START">	<!-- 03 -->
			<h3>HEURE D'ARRIVEE</h3>
			
			<SELECT style="border:	none; font-size:	46px; height:	80px;	border-bottom:	3px rgb(35, 35, 35) solid;	background:	none;" id="hourArrived" name="entry.1667284575_hour" size="1" onchange="calcCharges()" required>
				<OPTION>00</OPTION><OPTION>01</OPTION><OPTION>02</OPTION><OPTION>03</OPTION><OPTION>04</OPTION><OPTION>05</OPTION><OPTION>06</OPTION><OPTION>07</OPTION><OPTION>08</OPTION><OPTION>09</OPTION><OPTION>10</OPTION><OPTION>11</OPTION><OPTION>12</OPTION><OPTION>13</OPTION><OPTION>14</OPTION><OPTION>15</OPTION><OPTION>16</OPTION><OPTION>17</OPTION><OPTION>18</OPTION><OPTION>19</OPTION><OPTION>20</OPTION><OPTION>21</OPTION><OPTION>22</OPTION><OPTION>23</OPTION>
			</SELECT>
			:
			<SELECT style="border:	none; font-size:	46px; height:	80px;	border-bottom:	3px rgb(35, 35, 35) solid;	background:	none;" id="minuteArrived" name="entry.1667284575_minute" size="1" onchange="calcCharges(); setTimeout(function(){next();}, 200);" required>
				<OPTION>00</OPTION><OPTION>05</OPTION><OPTION>10</OPTION><OPTION>15</OPTION><OPTION>20</OPTION><OPTION>25</OPTION><OPTION>30</OPTION><OPTION>35</OPTION><OPTION>40</OPTION><OPTION>45</OPTION><OPTION>50</OPTION><OPTION>55</OPTION>
			</SELECT>
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="next()"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="SEL_CLIENT">	<!-- 04 -->
			<h3>CLIENT</h3>
			
			<table align="center">
				<tr>
					<td>
						<input style="float: right;" type="search" id="searchingClient" onchange="searchClient()">
					</td>
					<td>
						<div style="box-shadow: 0px 0px 32px rgb(35, 35, 35);
									color: white;
									width: 4em;
									height: 4em;
									background: rgb(35, 35, 35);
									padding: 5px;
									vertical-align: middle;
									display: inherit;
									font-family: Armata;
									cursor: pointer;
									border-radius: 46px;
									text-align:	center;
								}" type="button" onclick="if(checking('SEL_CLIENT')){}else{newClient();}">NEW</div>
					</td>
				</tr>
			</table>

			<p id="clientFound"></p>
			
			<table align="center" style="width:	75%">
				<tbody id="clients"></tbody>
			</table>

			<input type="<?php echo($hide); ?>" id="client"  name="entry.532586694" value=""  required>
			<input type="<?php echo($hide); ?>" id="code_com"  name="entry.1384966339" value="">
			<div class="navigation">
				<div id="buttonPrev" style="float:	left;" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="INFOS">		<!-- 05 -->
			<h3>INFORMATIONS CLIENT</h3>
		
			<table style="margin: 0 auto;    text-align: left;">
				<tr>
					<td>
						<p>CODE :</p>
					</td>
					<td>
						<input id="code" type="text" name="entry.845891913" onchange="" required>
					</td>
				</tr>	
				<tr>
					<td>
						<p>EMAIL :</p>
					</td>
					<td>
						<input id="mail" type="email" name="entry.19991430" onchange=""  required>
					</td>
				</tr>
				<tr>
					<td>
						<p>TELEPHONE :</p>
					</td>
					<td>
						<input id="phone" type="text" name="entry.1886220397" onchange=""  required>
					</td>
				</tr>
			</table>
			
			<p>ADRESSE :</p>
			<textarea rows="4" cols="50" id="address" style="text-align:	center" type="text" name="entry.1229884392" onchange="" required></textarea>

			<div class="navigation">
				<div id="buttonNext" type="button" onclick="if(checking('INFOS')){}else{next();}"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="CONTRAT">		<!-- 06 -->
			<h3>CONTRAT DE MAINTENANCE</h3>
			<h4>Le client est-il sous contrat de maintenance ?</h4>
			
			<p id="dateContrat"></p>
			
			<table>
				<tr>
					<td>
						<p style="display:	inline">OUI </p>
					</td>
					<td>
						<label class="switch">
							<input type="checkbox" class="toggle" onchange="contrat('entry.2021019270');calcCharges(); setTimeout(function(){next();}, 200);">
							<span class="slider"></span>
						</label>
					</td>
					<td>
						<p style="display:	inline"> NON</p>
					</td>
				</tr>
			</table>
			
			<input id="cont" type="<?php echo($hide); ?>" name="entry.2021019270" value="Oui"  required>
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="next()"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="DESC">		<!-- 07 -->
			<h3>DESCRIPTION</h3>

			<select id="desc_list" multiple style="height:	4em; width:	50%; background: rgb(109, 109, 109);" onchange="areaDesc()"></select>
			<textarea id="descArea" rows="5" name="entry.2041036538" onchange="refreshDescTab()"
			></textarea>
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="if(checking('DESC')){}else{refreshDescTab(); next();}"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="ZONE">		<!-- 08 -->
			<h3>ZONE DE DEPLACEMENT</h3>
			<h4>- ZONE1 : -100KM<br>
			- ZONE2 : +100KM</h4>
			
			<p style="display:	inline">ZONE1 </p><label class="switch">
				<input type="checkbox" class="toggle" onchange="zone('entry.214594423'); calcCharges(); setTimeout(function(){next();}, 200);">
				<span class="slider"></span>
			</label><p style="display:	inline"> ZONE2</p>
			
			<label class="switch">
				<input type="checkbox" class="toggleZone" onchange="zone('entry.214594423'); calcCharges(); setTimeout(function(){next();}, 200);">
				<span class="sliderG"></span>
			</label><p style="display:	inline"> AUCUN</p>
			
			<input id="travel" type="<?php echo($hide); ?>" name="entry.214594423" value="ZONE1"  required>
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="next()"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="PRET">		<!-- 09 --> <!-- CHANGEMENT CONDITIONNEL -->--
		
			<h3>PRET DE MATERIEL</h3>
			<p style="display:	inline">NON </p><label class="switch">
				<input type="checkbox" class="toggle" name="isPret" onchange="setTimeout(function() {pret();}, 200);">
				<span class="slider"></span>
			</label><p style="display:	inline"> OUI</p>
			
			<input id="mat" type="<?php echo($hide); ?>" name="entry.2094846173" value="Non" onchange=""  required>
			
			<script>
				function pret(){
					var input = document.getElementsByName("isPret")[0].checked;
					var output = document.getElementsByName("entry.2094846173")[0];
					
					if(input){
						output.value = "Oui";
						suite[13] = "MAT";
					}
					else{
						output.value = "Non";
						suite[13] = "";
					}

					next();
				}
			</script>
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="pret()"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="MAT">			<!-- 10 -->
			<h3>MATERIEL(S) PRETE(S)</h3>
			<textarea style="width:	49%; float:	right; border-radius:	0; margin:	0;" placeholder="Numéro(s) de Série" rows="5" name="entry.2047563043" onchange=""></textarea>
			<textarea style="width:	49%; border-radius:	0; text-align:	right; margin:	0;" placeholder="Nom du/des Matériel(s)" rows="5" name="entry.2082384825" onchange=""></textarea>
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="next()"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="TEST">		<!-- 11 -->
			<h3>TEST DE BON FONCTIONNEMENT</h3>
			
			<p style="display:	inline">POSITIF </p><label class="switch">
				<input type="checkbox" class="toggle" onchange="test_fonc('entry.246656631'); setTimeout(function(){next();}, 200);">
				<span class="slider"></span>
			</label><p style="display:	inline"> NEGATIF</p><br><br>
			
			<label class="switch">
				<input type="checkbox" class="toggle" onchange="test_fonc('entry.246656631'); setTimeout(function(){next();}, 200);">
				<span class="sliderG"></span>
			</label><p style="display:	inline"> NON CONCERNE</p>
			
			<input id="test" type="<?php echo($hide); ?>" name="entry.246656631" value="Positif" onchange="">
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="next()"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="SAVE">		<!-- 12 -->
			<h3>FORMATION SAUVEGARDE</h3>
			
			<p style="display:	inline">POSITIF </p><label class="switch">
				<input type="checkbox" class="toggle" onchange="form_save('entry.1885583728'); setTimeout(function(){next();}, 200);">
				<span class="slider"></span>
			</label><p style="display:	inline"> NEGATIF</p><br><br>
			
			<label class="switch">
				<input type="checkbox" class="toggle" onchange="form_save('entry.1885583728'); setTimeout(function(){next();}, 200);">
				<span class="sliderG"></span>
			</label><p style="display:	inline"> NON CONCERNE</p>
			
			<input id="save" type="<?php echo($hide); ?>" name="entry.1885583728" value="Oui" onchange="">
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="next()"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="LOI">			<!-- 13 --> <!-- CHANGEMENT CONDITIONNEL -->
			<h3>LOI 2018-1317 du 28 DECEMBRE 2018</h3>
			<h4>Le matériel et/ou logiciel sur lequel nous intervenons est-il mis à jour conformément à la loi 2018-1317 du 28 décembre 2018 ?</h4>
			
			<p style="display:	inline">OUI </p><label class="switch">
				<input type="checkbox" class="toggle" name="isLoi" onchange="setTimeout(function(){loiNF();}, 200);">
				<span class="slider"></span>
			</label><p style="display:	inline"> NON</p><br><br>
			
			<input id="loi" type="<?php echo($hide); ?>" name="entry.1676268250" value="Oui" onchange="">
			
			<script>
				function loiNF(){
					var input = document.getElementsByName("isLoi")[0].checked;
					var output = document.getElementsByName("entry.1676268250")[0];
					
					if(input){
						output.value = "Non";
						suite[17] = "MAJ";
					}
					else{
						output.value = "Oui";
						suite[17] = "";
					}

					next();
				}
			</script>
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="loiNF()"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="MAJ">			<!-- 14 -->
			<h3>MISE A JOUR</h3>
			
			<p style="display:	inline">NEGATIF </p><label class="switch">
				<input type="checkbox" class="toggle" onchange="upgrade('entry.509496568'); setTimeout(function(){next();}, 200);">
				<span class="slider"></span>
			</label><p style="display:	inline"> POSITIF</p><br><br>
			
			<label class="switch">
				<input type="checkbox" class="toggle" onchange="upgrade('entry.509496568'); setTimeout(function(){next();}, 200);">
				<span class="sliderG"></span>
			</label><p style="display:	inline"> NON CONCERNE</p>
			
			<input id="maj" type="<?php echo($hide); ?>" name="entry.509496568" value="" onchange="">
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="next()"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="VERSION">		<!-- 15 -->
			<h3>NUMERO DE VERSION</h3>
			<h4>Numéro de la Version actuellement installée ou mise à jour.</h4>
			
			<input type="text" name="entry.1389074181" onchange=""  required>
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="if(checking('VERSION')){}else{next();}"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="COMPLEMENTS">	<!-- 16 -->
			<h3>COMPLEMENTS</h3>
			
			<div style="text-align: left; width:	auto; display:	block;	margin:	0 auto;">
				<label class="switch">
					<input type="checkbox" name="complement" value="Remise du certificat NF / LNE.">
					<span class="sliderG"></span>
				</label><p style="display:	inline"> Remise du certificat NF / LNE.</p><br><br>
				
				<label class="switch">
					<input type="checkbox" name="complement" value="Explication de la procédure de sauvegarde sur support externe et clôture en fin de journée (Ticket Z).">
					<span class="sliderG"></span>
				</label><p style="display:	inline"> Explication de la procédure de sauvegarde sur support externe et clôture en fin de journée (Ticket Z).</p><br><br>
				
				<label class="switch">
					<input type="checkbox" name="complement" value="Clé de sauvegarde installée et paramétrée.">
					<span class="sliderG"></span>
				</label><p style="display:	inline"> Clef de sauvegarde installée et paramétrée.</p><br><br>
				
				<label class="switch">
					<input type="checkbox" name="complement" value="Remise à zéro du système avant mise en service.">
					<span class="sliderG"></span>
				</label><p style="display:	inline"> Remise à zéro du système avant mise en service.</p><br><br>
				
				<label class="switch">
					<input type="checkbox" name="complement" value="Installation en Cours :" onchange="pending()">
					<span class="sliderG"></span>
				</label><p style="display:	inline"> Installation en Cours.</p><br><br>
				
				<textarea id="pendingReason" name="complement" style="display:	none;" rows="10" col="25" placeholder="Motif(s)"></textarea>
				
				<script>
					function complements(){
						var input = document.getElementsByName("complement");
						var output = document.getElementsByName("entry.1250423481")[0];
						output.value = "";
						
						for(var x = 0; x <= 4; x++){
							if(input[x].checked){
								output.value = output.value + input[x].value + "\n";
							}
						}
						
						if(input[4]){
							output.value = output.value + input[5].value;
						}
						next();
					}
					
					function pending(){
						var input = document.getElementsByName("complement")[4].checked;
						var output = document.getElementById("pendingReason");
						
						if(input){
							output.style.display = "";
						}
						else {
							output.style.display = "none";
						}
					}
					
				</script>
			</div>
			
			<textarea name="entry.1250423481" style="display:	none;"></textarea>
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="complements(); refreshComplementsTab();"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="HOUR_END">	<!-- 17 -->
			<h3>HEURE DE DEPART</h3>
			
			<SELECT style="border:	none; font-size:	46px; height:	80px;	border-bottom:	3px rgb(35, 35, 35) solid;	background:	none;" id="hourLeaved" name="entry.1131156718_hour" size="1" onchange="calcCharges()" required>
				<OPTION>00</OPTION><OPTION>01</OPTION><OPTION>02</OPTION><OPTION>03</OPTION><OPTION>04</OPTION><OPTION>05</OPTION><OPTION>06</OPTION><OPTION>07</OPTION><OPTION>08</OPTION><OPTION>09</OPTION><OPTION>10</OPTION><OPTION>11</OPTION><OPTION>12</OPTION><OPTION>13</OPTION><OPTION>14</OPTION><OPTION>15</OPTION><OPTION>16</OPTION><OPTION>17</OPTION><OPTION>18</OPTION><OPTION>19</OPTION><OPTION>20</OPTION><OPTION>21</OPTION><OPTION>22</OPTION><OPTION>23</OPTION>
			</SELECT>
			:
			<SELECT style="border:	none; font-size:	46px; height:	80px;	border-bottom:	3px rgb(35, 35, 35) solid;	background:	none;" id="minuteLeaved" name="entry.1131156718_minute" size="1" onchange="if(checking('HOUR')){}else{calcCharges(); setTimeout(function(){next();}, 200);}" required>
				<OPTION>00</OPTION><OPTION>05</OPTION><OPTION>10</OPTION><OPTION>15</OPTION><OPTION>20</OPTION><OPTION>25</OPTION><OPTION>30</OPTION><OPTION>35</OPTION><OPTION>40</OPTION><OPTION>45</OPTION><OPTION>50</OPTION><OPTION>55</OPTION>
			</SELECT>
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="if(checking('HOUR')){}else{next();}"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>

		<fieldset id="FACTURATION_TIERCE">
			<h3>FACTURATION(S) TIERCE(S)</h3>
			
			<p style="display:	inline">NON </p><label class="switch">
				<input type="checkbox" class="toggle" onchange="showFactu()">
				<span class="slider"></span>
			</label><p style="display:	inline"> OUI</p><br><br>
			
			<div style="display:none" id="factu">
				<p>Numéro de devis :</p>
				<table>
					<tr>
						<td>
							Devis : 
						</td>
						<td>
							<input style="width: 5em" type="text" id="devis" placeholder="N°" onchange="facturation()">
						</td>
					</tr>
					<tr>
						<td>
							Devis : 
						</td>
						<td>
							<input style="width: 5em" type="text" id="devis1" placeholder="N°" onchange="facturation()">
						</td>
					</tr>
					<tr>
						<td>
							Devis : 
						</td>
						<td>
							<input style="width: 5em" type="text" id="devis2" placeholder="N°" onchange="facturation()">
						</td>
					</tr>
				</table>
				<p>Facturation Tierce : </p>
				<table style="width:100%" id="factu">
					<tr>
						<td>
						</td>
						<td style="font-size:50%;text-align:center;">
							<b>DESIGNATION</b>
						</td>
						<td style="font-size:50%;text-align:center;">
							<b>REMISE</b>
						</td>
						<td style="font-size:50%;text-align:center;">
							<b>PRIX (HT)</b>
						</td>
					</tr>
					<tr>
						<td>
							<input placeholder="Rechercher..." type="search" class="factu_search" style="font-size:50%;border-width: 2px;width:100%" onchange="searchArticle(0)">
						</td>
						<td style="text-align:right">
							<select class="facturations" id="facturation_tierse_name" name="entry.414412799" onchange="facturation()">
								<option value="">
								</option>
							</select>
						</td>
						<td>
							<select name="entry.1180815520" class="remise" style="font-size:50%" onchange="facturation()">
								<option value="">
								</option>
								<option value="10">
									10.00%
								</option>
								<option value="20">
									20.00%
								</option>
								<option value="30">
									30.00%
								</option>
							</select>
						</td>
						<td>
							<input name="entry.1896435851" class="facturations_prix" style="width: 5em" type="text" id="facturation_tierse" placeholder="Prix" onchange="facturation()" readonly>
						</td>
					</tr>
					<tr>
						<td>
							<input placeholder="Rechercher..." type="search" class="factu_search" style="font-size:50%;border-width: 2px;width:100%" onchange="searchArticle(1)">
						</td>
						<td style="text-align:right">
							<select class="facturations" id="facturation_tierse1_name" name="entry.1798627640" onchange="facturation()">
								<option value="">
								</option>
							</select>
						</td>
						<td>
							<select name="entry.622528202" class="remise" style="font-size:50%" onchange="facturation()">
								<option value="">
								</option>
								<option value="10">
									10.00%
								</option>
								<option value="20">
									20.00%
								</option>
								<option value="30">
									30.00%
								</option>
							</select>
						</td>
						<td>
							<input name="entry.210623750" class="facturations_prix" style="width: 5em" type="text" id="facturation_tierse1" placeholder="Prix" onchange="facturation()" readonly>
						</td>
					</tr>
					<tr>
						<td>
							<input placeholder="Rechercher..." type="search" class="factu_search" style="font-size:50%;border-width: 2px;width:100%" onchange="searchArticle(2)">
						</td>
						<td style="text-align:right">
							<select class="facturations" id="facturation_tierse2_name" name="entry.468083007" onchange="facturation()">
								<option value="">
								</option>
							</select>
						</td>
						<td>
							<select name="entry.166947705" class="remise" style="font-size:50%" onchange="facturation()">
								<option value="">
								</option>
								<option value="10">
									10.00%
								</option>
								<option value="20">
									20.00%
								</option>
								<option value="30">
									30.00%
								</option>
							</select>
						</td>
						<td>
							<input name="entry.1185482882" class="facturations_prix" style="width: 5em" type="text" id="facturation_tierse2" placeholder="Prix" onchange="facturation()" readonly>
						</td>
					</tr>
					<tr>
						<td>
							<input placeholder="Rechercher..." type="search" class="factu_search" style="font-size:50%;border-width: 2px;width:100%" onchange="searchArticle(3)">
						</td>
						<td style="text-align:right">
							<select class="facturations" id="facturation_tierse3_name" name="entry.1018844855" onchange="facturation()">
								<option value="">
								</option>
							</select>
						</td>
						<td>
							<select name="entry.1756883059" class="remise" style="font-size:50%" onchange="facturation()">
								<option value="">
								</option>
								<option value="10">
									10.00%
								</option>
								<option value="20">
									20.00%
								</option>
								<option value="30">
									30.00%
								</option>
							</select>
						</td>
						<td>
							<input name="entry.740985120" class="facturations_prix" style="width: 5em" type="text" id="facturation_tierse3" placeholder="Prix" onchange="facturation()" readonly>
						</td>
					</tr>
					<tr>
						<td>
							<input placeholder="Rechercher..." type="search" class="factu_search" style="font-size:50%;border-width: 2px;width:100%" onchange="searchArticle(4)">
						</td>
						<td style="text-align:right">
							<select class="facturations" id="facturation_tierse4_name" name="entry.2001038834" onchange="facturation()">
								<option value="">
								</option>
							</select>
						</td>
						<td>
							<select name="entry.1202409190" class="remise" style="font-size:50%" onchange="facturation()">
								<option value="">
								</option>
								<option value="10">
									10.00%
								</option>
								<option value="20">
									20.00%
								</option>
								<option value="30">
									30.00%
								</option>
							</select>
						</td>
						<td>
							<input name="entry.1586073318" class="facturations_prix" style="width: 5em" type="text" id="facturation_tierse4" placeholder="Prix" onchange="facturation()" readonly>
						</td>
					</tr>
					<tr>
						<td>
							<input placeholder="Rechercher..." type="search" class="factu_search" style="font-size:50%;border-width: 2px;width:100%" onchange="searchArticle(5)">
						</td>
						<td style="text-align:right">
							<select class="facturations" id="facturation_tierse5_name" name="entry.1125778919" onchange="facturation()">
								<option value="">
								</option>
							</select>
						</td>
						<td>
							<select name="entry.839151629" class="remise" style="font-size:50%" onchange="facturation()">
								<option value="">
								</option>
								<option value="10">
									10.00%
								</option>
								<option value="20">
									20.00%
								</option>
								<option value="30">
									30.00%
								</option>
							</select>
						</td>
						<td>
							<input name="entry.1040022259" class="facturations_prix" style="width: 5em" type="text" id="facturation_tierse5" placeholder="Prix" onchange="facturation()" readonly>
						</td>
					</tr>
					<tr>
						<td style="text-align:right" colspan="3">
							<span style="font-size:50%">TOTAL :</span>
						</td>
						<td>
							<input id="factuTotHT" type="text" name="totalFactu" style="width: 5em;font-size:50%" readonly>
						</td>
					</tr>
				</table>
			</div>
			
			<div class="navigation">
					<div id="buttonNext" type="button" onclick="next(); calcCharges()"><a href="#">NEXT</a></div>
					<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
			
			<style>
				.facturations{
					font-size:	50%;
					width:		100%;
				}
				
				#factu .facturations_prix{
					font-size:		50%;
				}
			</style>
			
			<script>
				var listFactu = "";
				function showFactu()
				{
					if(listFactu == "")
					{
						factuList = listing([false, false, false, false, false, true]);
						listFactu = 1;
					}
					
					var factuField = document.getElementById('factu');
					
					var factu = document.getElementsByClassName('facturations');
					var factuPrix = document.getElementsByClassName('facturations_prix');
					
					if(factuField.style.display == "none")
					{
						factuField.style.display = "block";
					}
					else
					{
						factuField.style.display = "none";
						
						for(var x = 0; x < factu.length; x++)
						{
							factu[x].value = "";
						}
						
						for(var x = 0; x < factuPrix.length; x++)
						{
							factuPrix[x].value = "";
						}
						facturation();
					}
				}
			
				var oldDEVIS = "";
				var factuTot = 0;
				function facturation()
				{
					var devis = document.getElementById('devis').value;
					var devis1 = document.getElementById('devis1').value;
					var devis2 = document.getElementById('devis2').value;
					
					var factu = document.getElementsByClassName('facturations');
					var factuPrix = document.getElementsByClassName('facturations_prix');
					var remise = document.getElementsByClassName('remise');
					
					for(var x = 0; x < factu.length; x++)
					{
						if(factu[x].value != "")
						{
							for(var y = 0; y < factuList.data.length; y++)
							{
								if(factu[x].value == factuList.data[y][0])
								{
									if(remise[x].value != "")
									{
										factuPrix[x].value = ((Math.round(parseFloat(factuList.data[y][1]))*100)/100) - (Math.round(((parseFloat(factuList.data[y][1])/100)*parseInt(remise[x].value))*100)/100);
									}
									else
									{
										factuPrix[x].value = Math.round(parseFloat(factuList.data[y][1])*100)/100;
									}
								}
							}
						}
						else
						{
							factuPrix[x].value = "";
						}
					}
					
					var facture = [document.getElementById('facturation_tierse_name').value, document.getElementById('facturation_tierse').value];
					var facture1 = [document.getElementById('facturation_tierse1_name').value, document.getElementById('facturation_tierse1').value];
					var facture2 = [document.getElementById('facturation_tierse2_name').value, document.getElementById('facturation_tierse2').value];
					var facture3 = [document.getElementById('facturation_tierse3_name').value, document.getElementById('facturation_tierse3').value];
					var facture4 = [document.getElementById('facturation_tierse4_name').value, document.getElementById('facturation_tierse4').value];
					var facture5 = [document.getElementById('facturation_tierse5_name').value, document.getElementById('facturation_tierse5').value];
					
					var output = document.getElementsByName('totalFactu')[0];
					var desc = document.getElementById('descArea');
					
					if((devis != "") || (devis1 != "") || (devis2 != ""))
					{
						var devisTot = "\nFACTURATION :\nSe référer au(x) devis :";
						if(devis != "")
						{
							devisTot = devisTot + "\n    - N°" + devis;
						}
						if(devis1 != "")
						{
							devisTot = devisTot + "\n    - N°" + devis1;
						}
						if(devis2 != "")
						{
							devisTot = devisTot + "\n    - N°" + devis2;
						}
					}
					else
					{
						var devisTot = "";
					}
					
					if(facture[1] === "")
					{
						facture[1] = 0;
					}
					if(facture1[1] === "")
					{
						facture1[1] = 0;
					}
					if(facture2[1] === "")
					{
						facture2[1] = 0;
					}
					if(facture3[1] === "")
					{
						facture3[1] = 0;
					}
					if(facture4[1] === "")
					{
						facture4[1] = 0;
					}
					if(facture5[1] === "")
					{
						facture5[1] = 0;
					}
					
					if((facture[0] != "") || (facture1[0] != "") || (facture2[0] != "") || (facture3[0] != "") || (facture4[0] != "") || (facture5[0] != ""))
					{
						devisTot = devisTot + "\nFacturation(s) Tierce(s) :\n";
						if(facture[0] != "")
						{
							devisTot = devisTot + "    " + facture[0] + " : " + facture[1] +"€\n";
						}
						if(facture1[0] != "")
						{
							devisTot = devisTot + "    " + facture1[0] + " : " + facture1[1] +"€\n";
						}
						if(facture2[0] != "")
						{
							devisTot = devisTot + "    " + facture2[0] + " : " + facture2[1] +"€\n";
						}
						if(facture3[0] != "")
						{
							devisTot = devisTot + "    " + facture3[0] + " : " + facture3[1] +"€\n";
						}
						if(facture4[0] != "")
						{
							devisTot = devisTot + "    " + facture4[0] + " : " + facture4[1] +"€\n";
						}
						if(facture5[0] != "")
						{
							devisTot = devisTot + "    " + facture5[0] + " : " + facture5[1] +"€\n";
						}
					}
					else
					{
						factuTot = 0;
					}
					
					factuTot = Math.round((parseFloat(facture[1]) + parseFloat(facture1[1]) + parseFloat(facture2[1]) + parseFloat(facture3[1]) + parseFloat(facture4[1]) + parseFloat(facture5[1]))*100)/100;
					
					desc.value = desc.value.replace(oldDEVIS, "");
					
					desc.value = desc.value + devisTot;
					
					oldDEVIS = devisTot;
					
					output.value = factuTot;
					
					refreshDescTab();
				}
			</script>
		</fieldset>
		
		<fieldset id="NAME">		<!-- 18 -->
			<h3>NOM DU SIGNATAIRE</h3>
			
			<input type="text" name="entry.1445723676" onchange="next()" required>
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="next()"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="QUALITY">		<!-- 19 -->
			<h3>QUALITE DU SIGNATAIRE</h3>
			
			<select style="width: 50%; height:	2em; font-size: 1em; background: none;" id="quality" size="5" onchange="qualite('entry.184632626')">
				<option value="Responsable">Responsable</option>
				<option value="Gérant(e)">Gérant(e)</option>
				<option value="Directeur(ice)">Directeur(ice)</option>
				<option value="Salarié(e)">Salarié(e)</option>
				<option value="Technicien(ne)">Technicien(ne)</option>
			</select><br>
			
			Autre : <input id="otherQuality" type="text" onchange="qualite('entry.184632626')">
			
			<input type="<?php echo($hide); ?>" name="entry.184632626" value="Responsable" onchange="" required>
			
			<script>
				function qualite(entry){
					var quality = document.getElementById("quality").value;
					var otherQuality = document.getElementById("otherQuality").value;
					
					if(otherQuality != ""){
						document.getElementsByName(entry)[0].value = otherQuality;
					}
					else if(quality != ""){
						document.getElementsByName(entry)[0].value = quality;
					}
					else{
						document.getElementsByName(entry)[0].value = "";
					}
				}
			</script>
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="next()"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>
		
		<fieldset id="TECH">		<!-- 20 -->
			<h3>NOM DU TECHNICIEN REFERANT</h3>
			
			<?php
				echo("ID :" . $session . "<br>");
				
				echo("<input type='text' name='entry.1981179687' value='");
					if(array_key_exists($session, $users))
					{
						echo($users[$session]);
					}
					else
					{
						echo("error");
					}
				echo("'>");
			?>
			
			<input type="text" name="entry.117482882">
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="techsSign()"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>

		<fieldset id="TAB">			<!-- 21 -->
			<h2 style="text-align:	left;">Récapitulatif de l'intervention :</h2>
		
			<h3>A FACTURER :</h3>
		
			<div class="divTable">
				<div class="divTableBody">
					<div class="divTableRow" id="top">
						<div class="divTableCell">
							<b>DESIGNATION</b>
						</div>
						<div class="divTableCell">
							<b>REMISE</b>
						</div>
						<div class="divTableCell">
							<b>PRIX REMISE</b>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">
							<input type="text" class="charges designation_facturation" disabled>
						</div>
						<div class="divTableCell">
							<input type="text" class="charges facturation_remise_tab" disabled>
						</div>
						<div class="divTableCell">
							<input type="text" class="charges facturation_prix_tab" disabled>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">
							<input type="text" class="charges designation_facturation" disabled>
						</div>
						<div class="divTableCell">
							<input type="text" class="charges facturation_remise_tab" disabled>
						</div>
						<div class="divTableCell">
							<input type="text" class="charges facturation_prix_tab" disabled>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">
							<input type="text" class="charges designation_facturation" disabled>
						</div>
						<div class="divTableCell">
							<input type="text" class="charges facturation_remise_tab" disabled>
						</div>
						<div class="divTableCell">
							<input type="text" class="charges facturation_prix_tab" disabled>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">
							<input type="text" class="charges designation_facturation" disabled>
						</div>
						<div class="divTableCell">
							<input type="text" class="charges facturation_remise_tab" disabled>
						</div>
						<div class="divTableCell">
							<input type="text" class="charges facturation_prix_tab" disabled>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">
							<input type="text" class="charges designation_facturation" disabled>
						</div>
						<div class="divTableCell">
							<input type="text" class="charges facturation_remise_tab" disabled>
						</div>
						<div class="divTableCell">
							<input type="text" class="charges facturation_prix_tab" disabled>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">
							<input type="text" class="charges designation_facturation" disabled>
						</div>
						<div class="divTableCell">
							<input type="text" class="charges facturation_remise_tab" disabled>
						</div>
						<div class="divTableCell">
							<input type="text" class="charges facturation_prix_tab" disabled>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">
						</div>
						<div class="divTableCell" style="text-align:right;border-top:black 2px solid">
							<b>TOTAL HT:</b>
						</div>
						<div class="divTableCell" style="border-top:black 2px solid">
							<input type="text" class="charges" id="facturation_prix_total_ht" disabled>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">
						</div>
						<div class="divTableCell" style="text-align:right">
							<b>TAUX TVA :</b>
						</div>
						<div class="divTableCell">
							20%
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">
						</div>
						<div class="divTableCell" style="text-align:right">
							<b>TOTAL TVA :</b>
						</div>
						<div class="divTableCell">
							<input type="text" class="charges" id="facturation_prix_total_tva" disabled>
						</div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">
						</div>
						<div class="divTableCell" style="text-align:right">
							<b>TOTAL TTC :</b>
						</div>
						<div class="divTableCell">
							<input type="text" class="charges" id="facturation_prix_total_ttc" disabled>
						</div>
					</div>
				</div>
			</div>
			
			<div class="divTable">
				<div class="divTableBody">
					<div class="divTableRow" id="top">
						<div class="divTableCell" style="background: white;">&nbsp;Heure d'arrivée : <input style="max-width:	5em;" class="charges" id="arrived" type="text" value="" onchange="calcCharges()" disabled><br>
						Heure de départ : <input style="max-width:	5em;" class="charges" id="leaved" type="text" value="" onchange="calcCharges()" disabled></div>
						<div class="divTableCell">&nbsp;<b>QUANTITE</b></div>
						<div class="divTableCell">&nbsp;<b>PRIX UNITAIRE</b></div>
						<div class="divTableCell">&nbsp;<b>TOTAL</b></div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell" style="text-align: right;">&nbsp;<b>MAIN D'OEUVRE :</b></div>
						<div class="divTableCell">&nbsp;<input class="charges" id="worked" type="text" value="" onchange="calcCharges()" disabled></div>
						<div class="divTableCell">&nbsp;<input class="charges" id="workedUnit" type="text" value="" onchange="calcCharges()" disabled></div>
						<div class="divTableCell">&nbsp;<input class="charges" id="workforce" type="text" value="" onchange="calcCharges()" disabled></div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell" style="text-align: right;">&nbsp;<b>DEPLACEMENT :</b></div>
						<div class="divTableCell">&nbsp;<input class="charges" id="travelType" type="text" value="" onchange="calcCharges()" disabled></div>
						<div class="divTableCell">&nbsp;<input class="charges" id="travelUnit" type="text" value="" onchange="calcCharges()" disabled></div>
						<div class="divTableCell">&nbsp;<input class="charges" id="travelTot" type="text" value="" onchange="calcCharges()" disabled></div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">&nbsp;</div>
						<div class="divTableCell" style="text-align: right;">&nbsp;<b>TOTAL HT :</b></div>
						<div class="divTableCell">&nbsp;<input class="charges" id="totalUniqueHT" type="text" value="" onchange="calcCharges()" disabled></div>
						<div class="divTableCell">&nbsp;<input class="charges" id="totalHT" type="text" value="" onchange="calcCharges()" disabled></div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">&nbsp;</div>
						<div class="divTableCell" style="text-align: right;" disabled>&nbsp;<b>TAUX TVA :</b></div>
						<div class="divTableCell">&nbsp;20%</div>
						<div class="divTableCell">&nbsp;20%</div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">&nbsp;</div>
						<div class="divTableCell" style="text-align: right;">&nbsp;<b>TOTAL TVA :</b></div>
						<div class="divTableCell">&nbsp;<input class="charges" id="totalUniqueTVA" type="text" value="" onchange="calcCharges()" disabled></div>
						<div class="divTableCell">&nbsp;<input class="charges" id="totalTVA" type="text" value="" onchange="calcCharges()" disabled></div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">&nbsp;</div>
						<div class="divTableCell" style="text-align: right;">&nbsp;<b>TOTAL TTC :</b></div>
						<div class="divTableCell">&nbsp;<input class="charges" id="ttcUnique" type="text" value="" onchange="calcCharges()" disabled></div>
						<div class="divTableCell">&nbsp;<input class="charges" id="ttc" type="text" value="" onchange="calcCharges()" disabled></div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">&nbsp;</div>
						<div class="divTableCell" style="text-align: right;">&nbsp;<b>CONTRAT DE MAINTENANCE :</b></div>
						<div class="divTableCell">&nbsp;<input class="charges" id="contratUnique" type="text" value="" onchange="calcCharges()" disabled></div>
						<div class="divTableCell">&nbsp;<input class="charges" id="contratTotal" type="text" value="" onchange="calcCharges()" disabled></div>
					</div>
					<div class="divTableRow">
						<div class="divTableCell">&nbsp;</div>
						<div class="divTableCell">&nbsp;</div>
						<div class="divTableCell" style="text-align: right;border-top:	solid black 2px;">&nbsp;<b>TOTAL* :</b></div>
						<div class="divTableCell" style="border-top:	solid black 2px;">&nbsp;<input class="charges" id="total" type="text" value="" onchange="calcCharges()" disabled></div>
					</div>
				</div>
			</div>
			<p style="text-align: right; margin: 0; font-size:	20px;">*Hors facturation soumisent à devis. Sous réserve de modifications.</p>
			
			<div> <!-- Récapitulatif -->
				<p style="text-align:	left;">Description de l'intervention :</p>
				<textarea rows="5" name="descTab" onchange="refreshDesc()" style="margin-top:	20px; width:	100%; border:	none; color:	black;"></textarea>
				
				<p name="complementsTab" style="text-align:	left;">Complément(s) d'installation :</p>
				<textarea rows="5" name="complementsTab" onchange="refreshComplements()" style="margin-top:	20px; width:	100%; border:	none; color:	black;"></textarea>
			</div>
			
			<div class="navigation">
				<div id="buttonNext" type="button" onclick="next()"><a href="#">NEXT</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
			
		</fieldset>
		
		<fieldset id="SIGN">		<!-- 22 -->
			<p>Signature :</p>
			
			<div id="signature">
				<div style="text-align:	left; font-family: monospace;">Lu et approuvé,</div>
				<canvas id="myCanvas" width="758px" height="384px" style="	border-radius: 5px;	box-shadow: 0px 0px 10px black;	display: block;	margin: 0px auto;background: white;	touch-action: none;"></canvas>

				<div style="display:	block; margin: 0 auto; text-align:	center;">
					<div style="font-family: monospace;
								box-shadow: 0px 0px 10px black;
								background: white;
								float: right;
								border-radius: 0 0 10px 10px;
								width: 135px;margin-right: 10px;cursor:pointer"
								onclick="clean()">RESET</div>
					<div style="font-family: monospace;
								box-shadow: 0px 0px 10px black;
								background: white;
								float: right;
								border-radius: 0 0 10px 10px;
								width: 200px;
								margin-right: 10px;
								cursor:	pointer;"
								onclick="formationDist(); next()">Formation à Distance</div>
					<?php if(isset($_GET['debug'])):?>
						<div style="font-family: monospace;
									box-shadow: 0px 0px 10px black;
									background: white;
									float: left;
									border-radius: 0 0 10px 10px;
									width: 200px;
									margin-left: 10px;"
									onclick="saved(); alert('sauvegardé !')">SAUVEGARDER SIGNATURE</div>
					<?php endif;?>
				</div>
			</div>
			
			<input name="entry.1142705552" type="<?php echo($hide); ?>" value="" required>
			
			<div class="navigation">
				<div id="buttonNext" type="submit" onclick="saved(); next();"><a style="background:	rgba(150,255,150,0.5)" href="#">SEND</a></div>
				<div id="buttonPrev" type="button" onclick="prev()"><a href="#">PREV</a></div>
			</div>
		</fieldset>

		<fieldset id="CONFIRM">		<!-- 23 -->
			<h3>Envoyer le formulaire ?</h3>
			<p>Il ne sera plus possible de le modifier.</p>
			<table style="margin: 0 auto;">
				<tr id="confirmation">
					<th>
						<div type="button" onclick="submited(true)" id="validate">OUI</div>
					</th>
					<th>
						<div type="button" onclick="submited(false)" id="novalidate">NON</div>
					</th>
				</tr>
			</table>
		</fieldset>
		
		<fieldset id="SENDED">		<!-- 24 -->
			<img src="images/checked.png" style="width: auto; margin: 0 auto;" alt="Envoyé !">
			<h1>Envoyé !</h1>
		</fieldset>
	
		<div id="sommaire">
			<div id="intraSom">
				<div type="button" name="subOpt" onclick="question=1;	show('AGENCE');				showSom()"><p>AGENCE</p></div>
				<div type="button" name="subOpt" onclick="question=2;	show('OBJECT');				showSom()"><p>OBJET</p></div>
				<div type="button" name="subOpt" onclick="question=3;	show('HOUR_START');			showSom()"><p>HEURE D'ARRIVEE</p></div>
				<div type="button" name="subOpt" onclick="question=4;	show('SEL_CLIENT');			showSom()"><p>SELECTION CLIENT</p></div>
				<div type="button" name="subOpt" onclick="question=5;	show('INFOS');				showSom()"><p>INFOS</p></div>
				<div type="button" name="subOpt" onclick="question=9;	show('CONTRAT');			showSom()"><p>CONTRAT</p></div>
				<div type="button" name="subOpt" onclick="question=10;	show('DESC');				showSom()"><p>DESCRIPTION</p></div>
				<div type="button" name="subOpt" onclick="question=11;	show('ZONE');				showSom()"><p>ZONE DE DEPLACEMENT</p></div>
				<div type="button" name="subOpt" onclick="question=12;	show('PRET');				showSom()"><p>PRET DE MATERIEL</p></div>
				<div type="button" name="subOpt" onclick="question=13;	show('MAT');				showSom()"><p>MATERIEL(S) PRETE(S)</p></div>
				<div type="button" name="subOpt" onclick="question=14;	show('TEST');				showSom()"><p>TEST DE BON FONCTIONNEMENT</p></div>
				<div type="button" name="subOpt" onclick="question=15;	show('SAVE');				showSom()"><p>SAUVEGARDE</p></div>
				<div type="button" name="subOpt" onclick="question=16;	show('LOI');				showSom()"><p>LOI NF/LNE</p></div>
				<div type="button" name="subOpt" onclick="question=17;	show('MAJ');				showSom()"><p>MISE A JOUR</p></div>
				<div type="button" name="subOpt" onclick="question=18;	show('VERSION');			showSom()"><p>VERSION LOGICIEL</p></div>
				<div type="button" name="subOpt" onclick="question=19;	show('COMPLEMENTS');		showSom()"><p>COMPLEMENT(S)</p></div>
				<div type="button" name="subOpt" onclick="question=20;	show('HOUR_END');			showSom()"><p>HEURE DE DEPART</p></div>
				<div type="button" name="subOpt" onclick="question=21;	show('FACTURATION_TIERCE');	showSom()"><p>FACTURATION</p></div>
				<div type="button" name="subOpt" onclick="question=22;	show('NAME');				showSom()"><p>NOM DU SIGNATAIRE</p></div>
				<div type="button" name="subOpt" onclick="question=23;	show('QUALITY');			showSom()"><p>QUALITE DU SIGNATAIRE</p></div>
				<div type="button" name="subOpt" onclick="calcCharges(); question=24;	show('TAB');showSom()"><p>TABLEAU RECAPITULATIF</p></div>
				<div type="button" name="subOpt" onclick="question=25;	show('SIGN');				showSom()"><p>SIGNATURE</p></div>
			</div>
		</div>
		
		<div id="btnSom" type="button" onclick="showSom()"></div>
	
	</body>
	
	<footer>
	</footer>
	
</html>

<script src="script.js"></script>

<?php
	if($debug_mode)
	{
		echo("<pre style='color:white; text-align:left;'>");
		print_r($_SESSION);
		print_r($_SERVER);
		echo("</pre>");
	}
?>