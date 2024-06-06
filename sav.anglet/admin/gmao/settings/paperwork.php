<div id="main">
<?php
	if(isset($_GET['edit']) && !empty($_GET['edit']) && ctype_digit($_GET['edit'])):
		$req = $bdd->prepare("SELECT * FROM PAPERWORK WHERE ID = :id");
		$req->execute(array('id'=>$_GET['edit']));
		$paperwork = $req->fetch(2);
		$activities = $bdd->query("SELECT * FROM ACTIVITIES");
		$opt_activities = JSON_decode($paperwork['ACTIVITIES']);
		$opt_types = JSON_decode($paperwork['TYPES']);
?>
		<link href="css/paperwork_editor.css" rel="stylesheet" />
		<script src="scripts/ckeditor/ckeditor.js"></script>
		<form method="post" action="gmao/settings.php?paperwork&update=<?=$paperwork['ID']?>" id="paperwork_editor">
			<div id="paper_options">
				<div>
					<div>
						<table cellspacing=0 id="types">
							<tr>
								<td colspan="2" style="border-bottom: solid 1px rgba(0,0,0,0.1)">
									Document requis pendant un/une :
								</td>
							</tr>
							<tr>
								<td>
									Installation
								</td>
								<td>
									<input type="hidden" value="0" name="INSTALL" />
									<input type="checkbox" value="1" name="INSTALL" <?=$opt_types->INSTALL ? "checked" : ""?>/>
								</td>
							</tr>
							<tr>
								<td>
									Maintenance
								</td>
								<td>
									<input type="hidden" value="0" name="MAINTENANCE" />
									<input type="checkbox" value="1" name="MAINTENANCE" <?=$opt_types->MAINTENANCE ? "checked" : ""?>/>
								</td>
							</tr>
							<tr>
								<td>
									Formation
								</td>
								<td>
									<input type="hidden" value="0" name="TRAINING" />
									<input type="checkbox" value="1" name="TRAINING" <?=$opt_types->TRAINING ? "checked" : ""?>/>
								</td>
							</tr>
							<tr>
								<td>
									Récupération Matériel
								</td>
								<td>
									<input type="hidden" value="0" name="RECOVERY" />
									<input type="checkbox" value="1" name="RECOVERY" <?=$opt_types->RECOVERY ? "checked" : ""?>/>
								</td>
							</tr>
							<tr>
								<td>
									Renouvellement
								</td>
								<td>
									<input type="hidden" value="0" name="RENEWAL" />
									<input type="checkbox" value="1" name="RENEWAL" <?=$opt_types->RENEWAL ? "checked" : ""?>/>
								</td>
							</tr>
							<tr>
								<td>
									Livraison
								</td>
								<td>
									<input type="hidden" value="0" name="DELIVERY" />
									<input type="checkbox" value="1" name="DELIVERY" <?=$opt_types->DELIVERY ? "checked" : ""?>/>
								</td>
							</tr>
							<tr>
								<td>
									Pré-Visite
								</td>
								<td>
									<input type="hidden" value="0" name="NEAR_VISIT" />
									<input type="checkbox" value="1" name="NEAR_VISIT" <?=$opt_types->NEAR_VISIT ? "checked" : ""?>/>
								</td>
							</tr>
						</table>
						<table cellspacing=0 id="activities">
							<tr>
								<td colspan="2" style="border-bottom: solid 1px rgba(0,0,0,0.1)">
									Domaine d'Activité :
								</td>
							</tr>
							<?php 
								$id = Array();
								for($x = 0; $activity = $activities->fetch(2); $x++):
							?>
							<tr>
								<td>
									<?=$activity['NAME']?>
								</td>
								<td>
									<input type="hidden" value="0" name="<?=$activity['ID']?>" />
									<input type="checkbox" value="1" name="<?=$activity['ID']?>" <?=$opt_activities->{$activity['ID']} ? "checked" : ""?>/>
								</td>
							</tr>
							<?php endfor; ?>
						</table>
						<table cellspacing=0>
							<tr>
								<td colspan="2" style="border-bottom: solid 1px rgba(0,0,0,0.1)">
									Type de Contrat :
								</td>
							</tr>
							<tr>
								<td>
									LOCATION
								</td>
								<td>
									<input type="hidden" value="0" name="LEASING" />
									<input type="checkbox" value="1" name="LEASING" <?=$paperwork['LEASING'] ? "checked" : ""?>/>
								</td>
							</tr>
							<tr>
								<td>
									VENTE
								</td>
								<td>
									<input type="hidden" value="0" name="SELLING" />
									<input type="checkbox" value="1" name="SELLING" <?=$paperwork['SELLING'] ? "checked" : ""?>/>
								</td>
							</tr>
						</table>
						<table cellspacing=0>
							<tr>
								<td>
									INCLU DANS LE CONTRAT D'ASSISTANCE
								</td>
								<td>
									<input type="hidden" value="0" name="COUNTRACT" />
									<input type="checkbox" value="1" name="COUNTRACT" <?=$paperwork['COUNTRACT'] ? "checked" : ""?>/>
								</td>
							</tr>
						</table>
						<table cellspacing=0>
							<tr>
								<td>
									<textarea style="width:100%" name="DESCRIPTION" placeholder="Description"><?=$paperwork['DESCRIPTION']?></textarea>
								</td>
							</tr>
						</table>
					</div>
					<table cellspacing=0>
						<tr>
							<td>
								SIGNATURE REQUISE 
							</td>
							<td>
								<input type="hidden" value="0" name="REQUIRE_SIGN" />
								<input type="checkbox" value="1" name="REQUIRE_SIGN" <?=$paperwork['REQUIRE_SIGN'] ? "checked" : ""?>/>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<table id="editor" cellspacing=0 cellpadding=0>
				<tr>
					<td>
						<a class="gmao_link" id="return_button" href="gmao.php?page=settings&setting=paperwork"></a>
						<input id="name" name="PAPERWORK_NAME" value="<?=$paperwork['NAME']?>" placeholder="Pièce Administrative"/>
						<input id="submit_button" type="submit" value="ENREGISTRER"/>
						<input id="preview_button" type="button" value="PREVISUALISER"/>
					</td>
				</tr>
				<tr>
					<td>
						<input type="hidden" name="ID" value="<?=$paperwork['ID']?>" />
						<textarea name="BODY"><?=$paperwork['BODY']?></textarea>
					</td>
				</tr>
			</table>
		<form>
		<div id="createMenu" style="display:none">
			<h1>Nouveau Document</h1>
			<input type="text" value="" placeholder="Nom du Document" />
		</div>
<?php
	else:
		$req = $bdd->query("SELECT * FROM PAPERWORK ORDER BY NAME");
?>
		<link href="css/paperwork.css" rel="stylesheet" />
		<h1>Pièces Administratives</h1>
		<table cellspacing=0 cellpadding=0>
			<?php while($paperwork = $req->fetch(2)): ?>
				<tr>
					<td style="width:100%; padding:5px">
						<a class='gmao_link' title="<?=$paperwork['DESCRIPTION']?>" href="gmao.php?page=settings&setting=paperwork&edit=<?=$paperwork['ID']?>" id="<?=$paperwork['ID']?>" ><?=$paperwork['NAME']?></a>
					</td>
					<td>
						<img class="delete" src="images/delete.png" onclick='deleteInfo(this)' />
					</td>
				</tr>
			<?php endwhile; ?>
			<tr>
				<td colspan="2" style="padding:0">
					<input type="button" value="+" style="border: none;background: rgba(0,0,0,0.1);width: 100%;height: 100%;outline: none;color: white;font-weight: bold;font-size: 20px;cursor:pointer;" onclick="create()" />
				</td>
			</tr>
		</table>
<?php endif; ?>
	<script src="scripts/paperwork.js"></script>
</div>

<div style="display:none" id="deleteInfo" class="ui">
	<table>
		<tr>
			<td>
				<h1 style="color:black">Attention !</h1>
			</td>
		</tr>
		<tr>
			<td id="deleteTypeMsg">
				<h3></h3>
			</td>
		</tr>
		<tr>
			<td id="deleteTypeDesc">
			</td>
		</tr>
		<tr>
			<td>
				Cette action est irréversible. Continuer ?
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" value="NON" onclick="$('#deleteInfo')[0].style.display = 'none'"/>
				<input type="button" value="OUI" id="uiButtonYes" onclick=""/>
			</td>
		</tr>
	</table>
</div>