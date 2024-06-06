<?php
	require('/home/tacteose/sav/INTER/ADMIN/inc/accounts.inc.php');
	
?>

<!DOCTYPE html>

<html>
	<?php
		require('inc/head.inc.php');
	?>
	
	<body>
		<nav id="menu">
			<div class="panelUser">
				<div id="pin" onclick="show(this)"></div>
				<a href="inc/accounts.inc.php?logout" target="_self">Deconnexion</a>
				<br><span>Bienvenue, <?php echo($_SESSION['name']); ?> (<?php echo($_SESSION['user']); ?>).</span>
			</div>
			<ul class="dropdown">
				<li>
					<a class="panelOption" href="widgets/home.php" target="content" style="background-color:#F28C0F;color:black;box-shadow:0 0 7px black inset;">
						<img width="28.5px" src="styles/images/home.png" /><span>Accueil</span>
					</a>
				</li>
				<li>
					<a class="panelOption">
						<img width="28.5px" src="styles/images/intervention.png" /><span>Interventions</span>
					</a>
					<ul>
						<li>
							<a href="widgets/list_inter.php?inter=<?=$_GET['_inter']?>" target="content">
								<span>Dernières Interventions</span>
							</a>
						</li>
						<?php if($_SESSION['rights'] >= "2"):?>
						<li>
							<a href="widgets/list_inter.php?_action=archives&inter=<?=$_GET['_inter']?>" target="content">
								<span>Archives</span>
							</a>
						</li>
						<?php endif;?>
					</ul>
				</li>
			</ul>
			
	<!-- Paramètres -->	
			<div class="menudown">
				<ul class="dropdown">
					<li>
						<a class="panelOption account" href="#" target=""><img width="28.5px" src="styles/images/account.png" /><span>Mon Compte</span></a>
						<ul>
							<li>
								<a href="account/account.php" target="content"><span>Mes Informations</span></a>
							</li>
							<li>
								<a href="account/stats.php" target="content"><span>Mes Statistiques</span></a>
							</li>
							<li>
								<a href="account/stats.php" target="content"><span>Mes Paramètres</span></a>
							</li>
						</ul>
					</li>
					<?php if(
						$_SESSION['rights']['ADMIN'] ||
						$_SESSION['rights']['ADMIN_EDIT'] ||
						$_SESSION['rights']['USER'] || 
						$_SESSION['rights']['GROUP'] ||
						$_SESSION['rights']['AGENCIES'] ||
						$_SESSION['rights']['ADMIN_EDIT_ESTABLISHMENT'] ||
						$_SESSION['rights']['ADMIN_EDIT_PRICING'] ||
						$_SESSION['rights']['ADMIN_EDIT_INTER'] ||
						$_SESSION['rights']['ADMIN_TOOLSDEV']
						):?>
					<li>
						<a class="panelOption"><img width="28.5px" src="styles/images/settings.png" /><span>Paramètres</span></a>
						<ul>
							<?php if($_SESSION['rights']['ADMIN'] || $_SESSION['rights']['ADMIN_EDIT'] || $_SESSION['rights']['ADMIN_EDIT_ESTABLISHMENT']): ?>
								<li>
									<a href="conf/etablishment.php" target="content"><span>Etablissement</span></a>
								</li>
							<?php endif;?>
							<?php if($_SESSION['rights']['ADMIN'] || $_SESSION['rights']['ADMIN_EDIT'] || $_SESSION['rights']['ADMIN_EDIT_PRICING']): ?>
								<li>
									<a href="conf/pricing.php" target="content"><span>Tarifications</span></a>
								</li>
							<?php endif;?>
							<?php if($_SESSION['rights']['ADMIN'] || $_SESSION['rights']['ADMIN_EDIT'] || $_SESSION['rights']['USER']): ?>
								<li>
									<a href="conf/user_manager.php" target="content"><span>Gestion des Utilisateurs</span></a>
								</li>
							<?php endif;?>
							<?php if($_SESSION['rights']['ADMIN'] || $_SESSION['rights']['ADMIN_EDIT'] || $_SESSION['rights']['GROUP']): ?>
								<li>
									<a href="conf/group_manager.php" target="content"><span>Gestion des Groupes</span></a>
								</li>
							<?php endif;?>
							<?php if($_SESSION['rights']['ADMIN'] || $_SESSION['rights']['ADMIN_EDIT'] || $_SESSION['rights']['AGENCY']): ?>
								<li>
									<a href="conf/agency_manager.php" target="content"><span>Gestion des Agences</span></a>
								</li>
							<?php endif;?>
							<?php if($_SESSION['rights']['ADMIN'] || $_SESSION['rights']['ADMIN_EDIT'] || $_SESSION['rights']['ADMIN_EDIT_INTER']): ?>
								<li>
									<a href="conf/intervention.php" target="content"><span>Interventions</span></a>
								</li>
							<?php endif;?>
							<?php if($_SESSION['rights']['ADMIN'] || $_SESSION['rights']['ADMIN_EDIT'] || $_SESSION['rights']['ADMIN_TOOLSDEV']): ?>
								<li>
									<a href="conf/tools.php" target="content"><span>Outils</span></a>
								</li>
							<?php endif;?>
						</ul>
					</li>
				</ul>
			<?php endif;?>
			</div>
		</nav>

		<iframe onmouseover="show(this)" src="widgets/home.php" style="visibility:hidden;z-index:-1" onload="this.style.visibility = '';" name="content" id="content"></iframe>
		<div id="loading"></div>
		<div id="menu_vanish" onmouseover="show(this)"></div>
	</body>
	
	<script>
		var select = "background-color:#F28C0F;color:black;box-shadow:0 0 7px black inset;";
		var subSelect = "color: #F28C0F;font-weight: bold;";
		
		var menu = document.getElementById('menu');
		
		var panelOptions = menu.getElementsByClassName('panelOption');
		
		var lock = true;
		
		for(var x = 0; x < panelOptions.length; x++){
			panelOptions[x].setAttribute("onclick", "selectSection(this);" + panelOptions[x].getAttribute("onclick"));
			var subSection = panelOptions[x].parentNode;
			if(subSection.tagName == "LI")
			{
				subSection = subSection.getElementsByTagName("a");
				for(var y = 1; y < subSection.length; y++)
				{
					subSection[y].setAttribute("onclick", "selectSubSection(this);" + subSection[y].getAttribute("onclick"));
				}
			}
		}
		
		function selectSection(option)
		{
			for(var x = 0; x < panelOptions.length; x++)
			{
				panelOptions[x].setAttribute("style", "");
				if(panelOptions[x].parentNode.getElementsByTagName("ul").length != 0)
				{
					panelOptions[x].parentNode.getElementsByTagName("ul")[0].style.display = "";
				}
			}
			
			option.setAttribute("style", select);
			option.parentNode.getElementsByTagName("ul")[0].style.display = "block";
		}
		
		function selectSubSection(option)
		{
			var parentSection = option.parentNode.parentNode;
			var subSections = parentSection.getElementsByTagName("a");
			
			for(var x = 0; x < subSections.length; x++){
				subSections[x].setAttribute("style", "");
			}
			
			option.setAttribute("style", subSelect);
		}
	
		function show(obj)
		{
			var menu = document.getElementById('menu');
			var content = document.getElementsByName('content')[0];
			
			if(obj.getAttribute("id") == "pin")
			{
				if(content.style.width == "")
				{
					content.style.width = "100vw";
					menu.style.left = "-15vw";
				}
				else
				{
					content.style.width = "";
					menu.style.left = "0";
				}
			}
			else if(obj.getAttribute("id") == "menu_vanish")
			{
				if(menu.style.left == "-15vw")
				{
					menu.style.left = "0vw";
				}
			}
			else
			{
				if(content.style.width == "100vw" && menu.style.left == "0vw")
				{
					setTimeout(function(){menu.style.left = "-15vw";}, 300);
				}
			}
		}
	</script>
</html>