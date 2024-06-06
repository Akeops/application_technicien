<?php
	require('../inc/accounts.inc.php');
	
	$req = $bdd->prepare("
		SELECT
			u.ID, u.NAME, u.PSEUDO, u.MAIL, r.NAME RIGHTS
		FROM
			USERS_INTER u
		INNER JOIN
			RIGHTS r
		ON
			r.ID = u.RIGHTS
		WHERE
			u.ID = :id
	");
	
	$req->execute(array(
		'id'	=> $_SESSION['id']
		)
	) or die(print_r($req->errorInfo()));
	
	$account = $req->fetch();
	
	$req = $bdd->query("SELECT * FROM INTERVENTIONS");
	$inter = $req->fetchAll();
	
	$req = $bdd->prepare("SELECT * FROM INTERVENTIONS WHERE TECHNICIEN = :name AND (ATTENTE IS NULL OR ATTENTE = 0) ORDER BY DATE");
	$req->execute(array(
		'name'	=> $_SESSION['id']
		)
	);
	$my_inter = $req->fetchAll();
	
	$req = $bdd->prepare("SELECT * FROM INTERVENTIONS WHERE UPDATED_BY = :name");
	$req->execute(array(
		'name'	=> $_SESSION['id']
		)
	);
	$my_inter_updated = $req->fetchAll();
	
	$signPath = "/home/tacteose/sav/signatures/TECHS/".$account['ID'].".png";
	$type = pathinfo($signPath, PATHINFO_EXTENSION);
	$data = file_get_contents($signPath);
	$sign = 'data:image/' . $type . ';base64,' . base64_encode($data);
	
?>

<!DOCTYPE html>

<?php
	require('../inc/head.inc.php');
?>

<html>
	<header>
	</header>

	<body>
		<form method="POST" target="">
			<table id="accountPreview">
				<tr>
					<th colspan="3" style="text-align:center">
						Aperçu du Compte :
					</th>
				</tr>
				<tr>
					<td style="text-align:right">
						Nom :
					</td>
					<td id="infos">
						<?=$account['NAME']?>
					</td>
					<td>
						Signature :
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						Identifiant :
					</td>
					<td id="infos">
						<?=$account['PSEUDO']?>
					</td>
					<td rowspan="3">
						<div id="sign">
							<img src="<?=$sign?>" alt="Signature" width="100%" height="100%">
						</div>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						E-Mail :
					</td>
					<td id="infos">
						<?=$account['MAIL']?>
					</td>
				</tr>
				<tr>
					<td style="text-align:right">
						Type de Compte :
					</td>
					<td style="text-align:left" id="infos">
						<?=$account['RIGHTS']?>
					</td>
				</tr>
				<tr>
					<td>
					</td>
					<td>
					</td>
					<td style="text-align:center">
						<a href="./update_account.php">Changer mes Informations</a>
					</td>
				</tr>
			</table>
		</form>
		
		<table id="statsGlobals">
			<tr>
				<th>
					Interventions Réalisées: <span id="infos"><?php echo(count($my_inter) . "/" . count($inter)); ?></span>
				</th>
				<th>
					<?php
						$totalMake = count($my_inter);
						$total = count($inter);
					?>
					Total : <span id="infos"><?php echo($totalMake . "/" . $total); ?></span>
				</th>
			</tr>
			<tr>
				<td>
					<canvas id="piechartCreated" width="600" height="300">
						Votre navigateur ne supporte pas cette fonctionnalité.
					</canvas>
				</td>
				<td>
					<canvas id="piechartWaiting" width="600" height="300">
						Votre navigateur ne supporte pas cette fonctionnalité.
					</canvas>
				</td>
				<td>
					<canvas id="piechartTotal" width="600" height="300">
						Votre navigateur ne supporte pas cette fonctionnalité.
					</canvas>
				</td>
			</tr>
		</table>
		
		<span id="widthed"></span>
		
		<?php
			$h = 0;
			
			for($x = 0; $x < sizeOf($my_inter); $x++)
			{
				$s = strtotime($inter[$x]['HEURE_ARRIVEE']);
				$e = strtotime($inter[$x]['HEURE_DEPART']);
				
				$heures = $e - $s;
				
				$h = $h + $heures;
			}
				
			$minutes = $h	/	60;
			$heures = $minutes	/	60;
			$jours = $heures	/	24;
		
			echo round($heures,1)." Heure(s) soit ".round($jours,1)." Jour(s) (approximatif)<br>Première intervention : ".$my_inter[0]['DATE'];
		?>
		
	</body>
	
	<footer>
	</footer>
</html>

<script>
	function resize(){
		var width = ($(window).width()) / 3 - 10;
		var listCanvas = document.getElementsByTagName("canvas");
		
		for(x=0;x<listCanvas.length;x++)
		{
			listCanvas[x].setAttribute("width", width);
		}
		
		graphique();
	}
	
	resize();
	window.onresize = resize;
	function graphique(){
		var listCanvas = document.getElementsByTagName("canvas");
		var canvas = listCanvas[0];
		
		//Total des interventions.
		var inter = ((<?=$totalMake?> / <?=$total?>) * 100).toFixed(2);
		var interTotal = 100 - inter;
		var donneesInter = [interTotal,inter];
		var legendesInter = ['Total (<?=$total?>)', 'Réalisées (<?=$totalMake?>)'];
		var colorsInter = ['darkgreen', 'lime'];
		
		//Interventions Réalisées.
		var make = ((<?=count($my_inter)?> / <?=$total?>) * 100).toFixed(2);
		var makeTotal = 100 - make;
		var donneesMake = [makeTotal,make];
		var legendesMake = ['Total (<?=$total?>)', 'Réalisées (<?=count($my_inter)?>)']
		var colorsMake = ['blue', 'cyan'];
		
		var listDonnees = [donneesMake, donneesInter];
		var listLegendes = [legendesMake,legendesInter];
		var listColors = [colorsMake, colorsInter];
		
		var diametre = Math.min(canvas.height, canvas.width) / 1.5;
		var rayon = diametre / 2;

		var position_x = canvas.width / 2;
		var position_y = canvas.height / 1.75;
		
		var largeur_rect = 15;
		var angle_initial = 0;
	
		for(x=0;x<listDonnees.length;x++)
		{
			var canvas = listCanvas[x];
			var context = canvas.getContext('2d');
			var donnees = listDonnees[x];
			var nb_donnees = donnees.length;
			var legendes = listLegendes[x];
			var stylecolors = listColors[x];
			makeCanvas(donnees,diametre,rayon,position_x,position_y,largeur_rect,angle_initial,nb_donnees,canvas,context);
			for(i=0;i<legendes.length;i++) {
				DessinerRectangle(
						context,
						diametre + 30,
						(largeur_rect + 3) * (i + 1),
						largeur_rect,largeur_rect,
						stylecolors[i]
				);
				context.font = '9pt Tahoma';//legendes
				context.fillStyle = '#000';//legendes
				context.fillText(legendes[i] + ' ' + donnees[i] +' %',diametre + 55,18 * i+30);//legendes
			}
		}

		function makeCanvas(donnes,diametre,rayon,position_x,position_y,largeur_rect,angle_initial,nb_donnees,canvas,context)
		{	
			for(var i=0;i<nb_donnees; i++) {
					var angles_degre = (donnees[i] / 100) * 360;// conversion pourcentage -> angle en degré
					DessinerAngle(context,position_x,position_y,rayon,angle_initial,angles_degre,stylecolors[i]);
					angle_initial += angles_degre;
			}
		}
		
		function DessinerAngle(context,position_x,position_y,rayon,angle_initial,angles_degre,couleurs) {
			context.beginPath();
			context.fillStyle  = couleurs;
			var angle_initial_radian = angle_initial / (180 / Math.PI);// conversion angle en degré -> angle en radian
			var angles_radian = angles_degre / (180 / Math.PI);// conversion angle en degré -> angle en radian
			context.arc(position_x,position_y,rayon,angle_initial_radian,angle_initial_radian + angles_radian,0);
			context.lineTo(position_x, position_y);
			context.closePath();
			context.fill();
		}
		
		function DessinerRectangle(context,x0,y0,xl,yl,coloration) {
			context.beginPath();
			context.fillStyle = coloration;
			context.fillRect(x0,y0,xl,yl);
			context.closePath();
			context.fill();
		}
	}
	
	graphique();
</script>

<style>

	table{
		font-weight:		bold;
	}

	table td{
		padding:			2px;
	}
	
	table th{
		font-size:			2em;
		padding-bottom:			10px;
	}
	
	#accountPreview{
		border-collapse:	collapse;
		border:				1px solid black;
		width:				100%;
		background:			#8fa0a0;
		margin-bottom:		20px;
	}
	
	#accountPreview #sign{
		background:			white;
		width:				210px;
		height:				90px;
		box-shadow:			0 0 3px inset black;
		border-radius:		6px;
	}
	
	#accountPreview #sign img{
		filter:				drop-shadow(1px 1px 1px black);
	}
	
	#statsGlobals{
		border-collapse:	collapse;
		border:				1px solid black;
		background:			#8fa0a0;
		margin-top:			20px;
		text-align:			center;
		margin:				0 auto;
		width:				100%;
	}
	
	#statsGlobals td{
		padding:			0;
		border:				solid black 1px;
		background:			white;
	}
	
	#statsGlobals th{
		font-size:			unset;
		padding:			0;
		background:			#8fa0a0;
	}
	
	#statsGlobals canvas{
		background:			white;
	}
	
	#infos{
		font-weight:		normal;
	}
	
	body,html{
		//margin:0;
		//padding:0;
		background:	white;
	}
</style>