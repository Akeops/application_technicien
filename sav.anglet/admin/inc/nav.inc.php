<div id="navigate">
	<div id="nav-header">
		<span>Connecté en tant que <b><?=$user['NAME']?></b></span>
	</div>
	<div id="nav-inner-top">
		<a href="javascript:void(0);" title="Accueil"><img src="images/home.png"/></a>
		<a href="javascript:void(0);" onclick="logOut()" title="Se Déconnecter"><img src="images/logout.png"/></a>
		<a href="javascript:void(0);" title="À Propos"><img src="images/infos.png"/></a>
		<a href="javascript:void(0);" title="Statistiques"><img src="images/stats.png"/></a>
		<a href="javascript:void(0);" title="Paramètres"><img src="images/settings.png"/></a>
	</div>
	<div id="nav-inner">
		<ul id="nav-opts">
			<li><a href="?section=1" ><img src="images/intervention.png" width="20px" height="20px" /><span>Intervention</span></a>
				<ul>
					<li>
						<a href="gmao.php" class="ajax">Dernières Interventions</a>
					</li>
					<li>
						<a href="#" >Archives</a>
					</li>
				</ul>
			</li>
			<li><a href="?section=2" ><img src="images/sav.png" width="20px" height="20px" /><span>SAV</span></a>
				<ul>
					<li>
						<a href="#" >Traités</a>
					</li>
					<li>
						<a href="#" >En cours</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</div>