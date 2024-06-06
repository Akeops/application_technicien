<?php
	require('../inc/config.inc.php');
	require('../inc/accounts.inc.php');
	
	session_start();
	if(!$_SESSION['rights']['ADMIN'] && !$_SESSION['rights']['ADMIN_EDIT'] && !$_SESSION['rights']['GROUP']){
		include('../errors/error_rights.php');
		die();
	}

	$groups = $bdd->query("SELECT * FROM RIGHTS WHERE ID > -1 ORDER BY ID") or die(print_r($bdd->errorInfo()));
?>

<head>
	<link href="https://fonts.googleapis.com/css?family=Cabin Condensed" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="../scripts/jQuery.min.js"></script>
	<script src="conf.js"></script>
</head>

<html>
	<body>
		<div class="section">
			<h2>GESTION DES GROUPES</h2>
		</div>
		<div class="sectionContent">
			<form method="get" action="edit_group.php" id="editGrp">
				<input type="hidden" name="groupID"> 
			</form>
			<table class="users">
				<tr style="display:none">
					<td>
					</td>
					<td>
					</td>
					<td>
					</td>
					<td>
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<th>
						<span>NOM</span>
					</th>
					<th>
						<span>EDITIONS</span>
					</th>
				</tr>
				<?php
					while($group = $groups->fetch()):?>
					<tr>
						<td title="<?=$group['DESCRIPTION']?>" class="groupName">
							<span style="cursor:help;color:<?=$group['COLOR']?>;font-weight:bold;text-shadow:0 0 2px black"><?php
								echo $group['NAME'];
								if($group['ID'] <= "0")
								{
									echo " <i>(Par défaut)</i>";
								}
							?></span>
						</td>
						<td>
							<span class="editOpt" onclick="editGroup(<?=$group['ID']?>)">Editer</span>
							<?php if($group['ID'] > "0"):?>
							 | <span onclick="delGroup(<?=$group['ID']?>)" class="editOpt">Supprimer</span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endwhile; ?>
					<tr style="border:none">
						<td style="border:none">
						</td>
						<td style="padding-top:40px;border:none">
							<input onclick="addGroup()" type="button" value="Nouveau Groupe"/>
						</td>
					</tr>
			</table>
			<hr width="50%">
		</div>
	</body>
</html>

<script>
	function editGroup(group){
		form = document.getElementById("editGrp");
		input = document.getElementsByName("groupID")[0];
		
		input.value = group;
		form.submit();
	}
	
	function addGroup(){
		window.location = "edit_group.php?create";
	}
	
	function delGroup(group){
		window.location = "edit_group.php?delete&groupID=" + group;
	}
</script>