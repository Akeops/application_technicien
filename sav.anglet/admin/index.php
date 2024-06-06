<?php
	require('inc/login.inc.php');
?>

<!DOCTYPE html>

<head>
	<link href="css/index.css" rel="stylesheet" />
	<link href="scripts/ui/jquery-ui.min.css" rel="stylesheet" />
	<link href="scripts/ui/jquery-ui.structure.min.css" rel="stylesheet" />
	<link href="scripts/ui/jquery-ui.theme.min.css" rel="stylesheet" />
	<link href="https://fonts.googleapis.com/css?family=Cabin Condensed" rel="stylesheet">
	<script src="scripts/jquery.js"></script>
	<script src="scripts/ui/jquery-ui.js"></script>
	<script src="scripts/paginer.js"></script>
	<script src="scripts/html2canvas.js"></script>
	<script src="scripts/jsPDF.js"></script>
</head>

<script>
	document.addEventListener("DOMContentLoaded", init);
	//document.addEventListener("onhashchange", location.reload(1));
	function init()
	{
		if(window.localStorage.getItem('NAV_WIDTH') == null)
		{
			var navWidth = (window.innerWidth / 100) * 15.6;
		}
		else
		{
			var navWidth = parseInt(window.localStorage.getItem('NAV_WIDTH'));
		}
		var navWidth = parseInt(window.localStorage.getItem('NAV_WIDTH')) || (window.innerWidth / 100) * 15.6;
		
		window.localStorage.setItem('NAV_WIDTH', navWidth);
		var navigate = $("#navigate")[0];
		var bodyContent = $("#body-content")[0];
		var bodyInfos = $("#body-infos")[0];
		var objPin = $("#nav-pin")[0];
		
		navigate.style.width = navWidth+"px";
		
		var navigate = $("#navigate")[0];
		
		if(parseInt(window.localStorage.getItem('NAV_PINNED')))
		{
			objPin.style.backgroundImage = "";
			bodyContent.style.marginLeft = navWidth+"px";
			bodyInfos.style.marginLeft = navWidth+"px";
		}
		else
		{
			objPin.style.backgroundImage = "url('images/arrowRight.png')";
			navigate.style.left = "-"+navWidth + "px";
			bodyContent.style.marginLeft = "0px";
			bodyInfos.style.marginLeft = "0px";
		}
		
		$('#loading')[0].style.transform = "translateY(0px)";
		$('#body-content')[0].style.visibility = "hidden";
		$.ajax({
			type: 			'POST',
			url: 			'gmao.php'+window.location.search,
			success: 		function (data) {
								if (typeof data !== 'undefined' && data.SUCCESS === true) {
									refreshBody(data);
									$('#loading')[0].style.transform = "";
									$('#body-content')[0].style.visibility = "";
								}
							}
		});
	}
</script>

<html>
	<body>
		<div id="loading"></div>
		<div id="navigate">
			<div id="nav-header">
				<span>Connecté en tant que <b><?=$user['NAME']?></b></span>
			</div>
			<div id="nav-inner-top">
				<a href="gmao.php?page=home" title="Accueil" class="gmao_link"><img src="images/home.png"/></a>
				<a href="javascript:void(0);" onclick="logOut()" title="Se Déconnecter" class="gmao_link"><img src="images/logout.png"/></a>
				<a href="http://tacteo-se.fr/" disin="1" target="_blank" title="À Propos"><img src="images/infos.png"/></a>
				<a href="gmao.php?page=stats" title="Statistiques" class="gmao_link"><img src="images/stats.png"/></a>
				<a href="gmao.php?page=settings" title="Paramètres" class="gmao_link"><img src="images/settings.png"/></a>
			</div>
			<div id="nav-inner">
				<ul id="nav-opts">
					<li><a href="gmao.php?page=inter"  class="gmao_link"><img src="images/intervention.png" width="20px" height="20px" /><span>INTERVENTION</span></a>
						<ul>
							<li>
								<a href="gmao.php?page=inter&archives=1"  class="gmao_link">ARCHIVES</a>
							</li>
						</ul>
					</li>
					<li><a href="gmao.php?page=customers"  class="gmao_link"><img src="images/customers.png" width="20px" height="20px" /><span>LISTING CLIENTS</span></a></li>
				</ul>
			</div>
		</div>
		<div id="body-infos">
			<div id="nav-pin" onclick="togglePin(this)"></div>
		</div>
		<div id="body-content" style="min-width: 940px;visibility:hidden"></div>
	</body>
</html>

<script>
	function togglePin(obj)
	{
		var navigate = $("#navigate")[0];
		var bodyContent = $("#body-content")[0];
		var bodyInfos = $("#body-infos")[0];
		var navWidth = parseInt(window.localStorage.getItem('NAV_WIDTH'));
		
		if(parseInt(navigate.style.left) <= 0)
		{
			navigate.style.left = "";
			obj.style.backgroundImage = "";
			bodyContent.style.marginLeft = navWidth + "px";
			bodyInfos.style.marginLeft = navWidth + "px";
			window.localStorage.setItem('NAV_PINNED', 1)
		}
		else
		{
			navigate.style.left = "-"+window.localStorage.getItem('NAV_WIDTH') + "px";
			obj.style.backgroundImage = "url('images/arrowRight.png')";
			bodyContent.style.marginLeft = "0px";
			bodyInfos.style.marginLeft = "0px";
			window.localStorage.setItem('NAV_PINNED', 0)
		}
	}
	
	function logOut()
	{
		var logout = $.post("login.php", {
			LOGOUT:	1
		});
		
		logout.done(
		function(data)
		{
			window.location.href = "login.php";
		});
	}
	
	function $_GET(param) {
		var vars = {};
		window.location.href.replace( location.hash, '' ).replace( 
			/[?&]+([^=&]+)=?([^&]*)?/gi,
			function( m, key, value ) {
				vars[key] = value !== undefined ? value : '';
			}
		);

		if ( param ) {
			return vars[param] ? vars[param] : null;	
		}
		return vars;
	}
	
	function isEmpty(obj) {
		for(var key in obj) {
			if(obj.hasOwnProperty(key))
				return false;
		}
		return true;
	}
	
	function refreshBody(data){
		$('#body-content').html(data.HTML);
		window.history.pushState("", "", data.URL);
		document.title = data.TITLE;
	}
	
	$(document).on('click', '.gmao_link', function (event) {
		if($(this).attr('disin') == "1")
		{
			return;
		}
		else if($(this)[0].className.search("gmao_link") > -1)
		{
			sendRequest($(this).attr('href'));
		}
    });
	
	function sendRequest(url)
	{
		event.preventDefault();
		$('#loading')[0].style.transform = "translateY(0px)";
		$('#body-content')[0].style.visibility = "hidden";
		$.ajax({
			type: 'POST',
			url: url,
			success: function (data) {
				if (typeof data !== 'undefined' && data.SUCCESS === true) {
					refreshBody(data);
					$('#loading')[0].style.transform = "";
					$('#body-content')[0].style.visibility = "";
				}
			}
		});
	}
</script>