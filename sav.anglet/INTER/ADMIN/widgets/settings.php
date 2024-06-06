<?php
	require('../inc/config.inc.php');
	require('../inc/accounts.inc.php');
	
	session_start();
	if($_SESSION['rights'] < "3"){
		header('Location: widgets/home.php');
	}
	
	$req = $bdd->query("SELECT * FROM ETABLISSEMENT") or die(print_r($req->errorInfo()));
	$etablishment = $req->fetch(2);
	
	$req = $bdd->query("SELECT * FROM TARIFS") or die(print_r($req->errorInfo()));
	$tarifs = $req->fetchAll(2);
	
	$users = $bdd->query("
		SELECT
			r.NAME 'GROUP', USERS_INTER.*
		FROM
			USERS_INTER
		INNER JOIN
			RIGHTS r ON (USERS_INTER.RIGHTS = r.ID)
		WHERE
			NOT(RIGHTS = 0)
		
		") or die(print_r($bdd->errorInfo()));
		
	$groups = $bdd->query("SELECT * FROM RIGHTS") or die(print_r($bdd->errorInfo()));
?>

<script src="../scripts/jQuery.min.js"></script>

<head>
	<link href="https://fonts.googleapis.com/css?family=Cabin Condensed" rel="stylesheet">
</head>

<html style="display:none">
	<body onload="document.getElementsByTagName('html')[0].style.display = ''">
		<div id="content">
		</div>
	</body>
</html>

<script>

</script>

<style>
	html,body{
		background:			white;
		margin:				0;
		font-family: 		'Cabin Condensed';
	}
	
	#content{
		border-top:			none;
		width:				100%;
		margin:				0 auto;
		z-index:			1;
	}
	
	#content .section{
		margin-top:			5px;
		display:			inline-block;
		text-align:			center;
	}
	
	#content .section h2{
		user-select:		none;
		background:			#014040;
		color:				white;
		cursor:				pointer;
		margin:				0;
		padding:			10px;
	}
	
	#content .section form{
		padding:			50px calc(25% / 4)
	}
	
	#content .section h2:hover{
		background:			#F28C0F;
		text-shadow:		0px 0px 7px black;
	}
	
	.sectionContent{
		position:			absolute;
		top:				49px;
		left:				0px;
		
		width:				100%;
	}
	
	.sectionContent table{
		margin:				0 auto;
		width:				100%;
		border-collapse:	collapse;
	}
	
	.sectionContent span span{
		color:				red !important;
	}
	
	.sectionContent table span{
		color:				black;
	}
	
	.sectionContent table td{
		width:				25%;
		padding:			0;
		padding-top:		5px;
	}
	
	.sectionContent table input,textarea{
		width:				100%;
		height:				40px;
		color:				blue;
	}
	
	.sectionContent #users{
		width:				75%;
		vertical-align:		middle;
	}
	
	.sectionContent #users th{
		border:				solid 1px black;
		background:			grey;
		height:				2em;
	}
	
	.sectionContent #users span{
		vertical-align:		middle;
		display:			inline;
	}
	
	.sectionContent #users tr:hover{
		background:			linear-gradient(hsla(33, 89%, 25%, 1) 0%, #F28C0F 20%, #F28C0F 80%, hsla(33, 89%, 25%, 1) 100%);
	}
	
	.sectionContent #users tr:nth-child(2n):hover{
		background:			linear-gradient(hsla(33, 89%, 25%, 1) 0%, #F28C0F 20%, #F28C0F 80%, hsla(33, 89%, 25%, 1) 100%);
	}
	
	.sectionContent #users tr td{
		width:				auto !important;
		border:				solid black 1px;
		padding:			5px;
	}
	
	.sectionContent #users tr td:first-child{
		width:				5% !important;
		text-align:			center;
	}
	
	.sectionContent #users tr:nth-child(2n){
		background:			darkgrey;
	}
	
	.sectionContent #users tr:nth-last-child(1):hover{
		background:			white;
		box-shadow:			none;
	}
	
	.sectionContent #users tr:nth-last-child(1){
		background:			white;
	}
	
	.sectionContent #users tr :nth-last-child(1){
		text-align:			center;
		width:				auto !important;
	}
	
	.sectionContent #users tr:nth-last-child(1) td:nth-last-child(1){
		text-align:			right;
	}
	
</style>