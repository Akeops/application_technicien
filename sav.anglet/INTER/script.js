	window.onbeforeunload = function() {
			alert("ATTENTION !");
			return false;
    }
	
	/*----------------*/
	/* INITIALISATION */
	/*----------------*/
	var CSV_CLIENTS = "BDD/CLIENTS.csv";
	var CSV_INSTALL = "BDD/DESC_INSTALLATION.csv";
	var CSV_FORMATION = "BDD/DESC_FORMATION.csv";
	var CSV_MAINTENANCE = "BDD/DESC_MAINTENANCE.csv";
	var CSV_FACTURATIONS = "BDD/FACTURATIONS.csv";
	
	var suite = [	"LOADING",		//0
					"AGENCE",		//1
					"OBJECT",		//2
					"HOUR_START",	//3
					"SEL_CLIENT",	//4
					"INFOS",		//5
					"",				//6
					"",				//7
					"",				//8
					"CONTRAT",		//9
					"DESC",			//10
					"ZONE",			//11
					"PRET",			//12
					"MAT",			//13
					"TEST",			//14
					"SAVE",			//15
					"LOI",			//16
					"MAJ",			//17
					"VERSION",		//18
					"COMPLEMENTS",	//19
					"HOUR_END",		//20
					"FACTURATION_TIERCE",				//21
					"NAME",			//22
					"QUALITY",		//23
					"TAB",			//24
					"SIGN",			//25
					"CONFIRM"		//26 
				];
				
	var list = [];
	list.length = 0;
	var question = 0;
	var fieldset = document.getElementsByTagName("fieldset");
	
	fieldset[0].style.display = "block";
	
	var clientsList = listing([true, false, false, false, false]);
	var factuList;
	
	var clickX = new Array();
	var clickY = new Array();
	var clickDrag = new Array();
	var paint;
	
	var clientID = 0;
	
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1;
	var yyyy = today.getFullYear();
	
	var techSign = "";
	
	setTimeout(function() {
		refresh();
		document.getElementById("client").value = "";
		techsSign();
		next();
		calcCharges();
		document.getElementById("btnSom").style.display = "block";
	}, 2000);
	
	var showMenu = false;
	
	function showSom(){
		var som = document.getElementById("sommaire");
		var btn = document.getElementById("btnSom");
		if(showMenu){
			som.style.transform = "";
			btn.style.backgroundImage = "";
			document.getElementsByTagName("html")[0].style.overflow = "";
			showMenu = false;
		}else{
			som.style.transform = "translateX(0px)";
			btn.style.backgroundImage = "url('images/shape close.png')";
			document.getElementsByTagName("html")[0].style.overflow = "hidden";
			showMenu = true;
		}
	}
	
	/*----------------------------------*/
	/* GESTION DE LA DATE D'AUJOURD'HUI */
	/*----------------------------------*/
	function toDay(){
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear();

		if(dd<10) {
			dd = '0'+ dd
		} 

		if(mm<10) {
			mm = '0'+ mm
		}
		
		today = mm + '-' + dd + '-' + yyyy;
		
		return today;
	}
	
	/*------------------------------------*/
	/* TRAITEMENT DE LA LISTE DES CLIENTS */
	/*------------------------------------*/
	
	function parse(filePath){
		var fileContent = "";
		var csv = new XMLHttpRequest();
		csv.open("GET",filePath + "?v=" + Math.random(),false);
		csv.send();
		fileContent = csv.responseText;
		
		csv.abort();
		
		return Papa.parse(fileContent);
	}
	
	function listing(type){
	
		var list = "";
		var length = 0;
		
		var DESC_MAINTENANCE = [];
		var DESC_INSTALL = [];
		var DESC_FORMATION = [];
		var FACTURATIONS = [];
		
		if(type[0] || type[1] || type[2] || type[3] || type[4] || type[5]){
			if(type[0]){
				var parseList = parse(CSV_CLIENTS);
				for(var x = 1; x < parseList.data.length; x++){
					if ((parseList.data[x][2] != "") && (parseList.data[x][1] == "CLIENT" || parseList.data[x][1] == "")){
						list = list + "<tr style='display: none'><td type='button' onclick='clientID = " + x + "; refresh(); setTimeout(function(){next();}, 200)' name='optClient'  value=\"" + x + "\">" + parseList.data[x][2] + '</td></tr>';
					}
				}
				
				document.getElementById("clients").innerHTML = list;
				
				return parseList;
			}
			if(type[1] || type[4]){
				var parseList = parse(CSV_INSTALL);
				for(var x = 1; x < parseList.data.length - 1; x++){
					list = list + "<option value=\"" + length + "\">" + parseList.data[x][0];
					DESC_INSTALL[x-1] = [parseList.data[x][0], parseList.data[x][1]];
					length++;
				}
				
				document.getElementById("desc_list").innerHTML = list;
			}
			if(type[2]){
				var parseList = parse(CSV_FORMATION);
				for(var x = 1; x < parseList.data.length - 1; x++){
					list = list + "<option value=\"" + length + "\">" + parseList.data[x][0];
					DESC_FORMATION[x-1] =  [parseList.data[x][0], parseList.data[x][1]];
					length++;
				}
				
				document.getElementById("desc_list").innerHTML = list;
			}
			if(type[3]){
				var parseList = parse(CSV_MAINTENANCE);
				for(var x = 1; x < parseList.data.length - 1; x++){
					list = list + "<option value=\"" + length + "\">" + parseList.data[x][0];
					DESC_MAINTENANCE[x-1] =  [parseList.data[x][0], parseList.data[x][1]];
					length++;
				}
				
				document.getElementById("desc_list").innerHTML = list;
			}
			if(type[5]){
				var parseList = parse(CSV_FACTURATIONS);
				var factu = document.getElementsByClassName('facturations');
				var articles = "";
				
				for(var x = 1; x < parseList.data.length - 1; x++)
				{
					if(parseList.data[x][1] != "" || parseList.data[x][1] != 0 || parseList.data[x][1] != "0")
					{
						articles = articles + "<option value=\"" + parseList.data[x][0] + "\">" + parseList.data[x][0] + "</option>";
					}
				}
				
				for(var y = 0; y < factu.length; y++)
				{
					factu[y].innerHTML = articles;
				}
				
				return parseList;
			}
			
			var desc = [];
			desc.length = 0;
			
			var x = 0;
			var y = 0;
			var z = 0;

			if(DESC_INSTALL.length > 0) {
				for(x; x < DESC_INSTALL.length; x++){
					desc[x] = [DESC_INSTALL[x][0], DESC_INSTALL[x][1]];
				}
			}
			if(DESC_FORMATION.length > 0){
				for(x; x < (DESC_FORMATION.length + DESC_INSTALL.length); x++){
					desc[x] = [DESC_FORMATION[z][0], DESC_FORMATION[z][1]];
					z++;
				}
			}
			if(DESC_MAINTENANCE.length > 0){
				for(x; x < (DESC_FORMATION.length + DESC_INSTALL.length + DESC_MAINTENANCE.length); x++){
					desc[x] = [DESC_MAINTENANCE[y][0], DESC_MAINTENANCE[y][1]];
					y++;
				}
			}
			
			return desc;
		}
	}
	
	function refresh(){
		var dateStart =	clientsList.data[clientID][9];
		var dateEnd =	clientsList.data[clientID][10];
		var solde = clientsList.data[clientID][12];
		var output =	document.getElementById("dateContrat");
		
		if((dateStart != "") && (dateStart != "CONTRAT_DU")){
			output.innerHTML =	"Du <b>" + dateStart;
		}
		else{
			output.innerHTML =	"Du <b>??/??/????</b> ";
		}
		
		if((dateEnd != "") && (dateEnd != "CONTRAT_DU")){
			output.innerHTML =	output.innerHTML + " au <b>" + dateEnd + "</b>.";
		}
		else{
			output.innerHTML =	output.innerHTML + " au <b>??/??/????</b>.";
		}
		
		output.innerHTML =	output.innerHTML + "<br />Solde : ";
		
		if(solde >= 0)
		{
			output.innerHTML =	output.innerHTML + "\n<span style='color: green'><b>" + solde + " €</b></span>";
		}
		else
		{
			output.innerHTML =	output.innerHTML + "\n<span style='color: red'><b>" + solde + " €<br />(Le client a potentiellement des factures impayées).</b></span>";
		}
		
		document.getElementById("client").value = clientsList.data[clientID][2];
		if(document.getElementsByName("entry.532586694")[0].value != "" && clientID != 0){
			document.getElementById("code").value = clientsList.data[clientID][0];
			document.getElementById("phone").value = clientsList.data[clientID][3];
			document.getElementById("address").value = clientsList.data[clientID][5] + "\n" + clientsList.data[clientID][6] + "\n" + clientsList.data[clientID][8] + " " + clientsList.data[clientID][7];
			document.getElementById("mail").value = clientsList.data[clientID][4];
			document.getElementById("code_com").value = clientsList.data[clientID][11];
		}
	}
	
	function searchClient() {
		var input, filter, select, option, a, i, txtValue, found;
		input = document.getElementById('searchingClient');
		document.getElementById('client').value = input.value;
		filter = input.value.toUpperCase();
		select = document.getElementById("clients");
		option = select.getElementsByTagName('tr');
		
		found = document.getElementById('clientFound');
		found.innerHTML = "AUCUN RESULTAT";
		
		for (i = 0; i < option.length; i++) {
			if(input.value != ""){
				a = option[i];
				txtValue = a.textContent || a.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					option[i].style.display = "";
					found.innerHTML = "";
				} else {
					option[i].style.display = "none";
				}
			}
			else{
				option[i].style.display = "none";
				found.innerHTML = "";
			}
		}
	}

	function searchArticle(z) {
		var input, filter, select, option, a, i, txtValue, found;
		
		input = document.getElementsByClassName('factu_search')[z];
		filter = input.value.toUpperCase();
		
		select = document.getElementsByClassName("facturations")[z];
		option = select.getElementsByTagName('option');
		
		for (i = 0; i < option.length; i++) {
			if(input.value != ""){
				a = option[i];
				txtValue = a.textContent || a.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					option[i].style.display = "";
				} else {
					option[i].style.display = "none";
				}
			}
			else{
				option[i].style.display = "";
			}
		}
	}
	
	function newClient(){
		document.getElementsByName("entry.532586694")[0].value = document.getElementById("searchingClient").value;
		document.getElementsByName("entry.845891913")[0].value = "NEW"; // CODE CLIENT
		document.getElementsByName("entry.1229884392")[0].value = "";
		document.getElementsByName("entry.1886220397")[0].value = "";
		document.getElementsByName("entry.19991430")[0].value = "";
		document.getElementById("dateContrat").innerHTML = ""; 
		next();
	}
	
	function areaDesc(){
		var SELECTED = document.getElementById('desc_list');
		var textArea = document.getElementById('descArea');
		var DESC = document.getElementById('descArea').value;
		var resp = "";
		
		
		for(var x = 0; x < list.length; x++){
			DESC = DESC.replace("- " + list[x][1] + "\n", "");
		}
		
		for(var x = 0; x < SELECTED.length; x++){
			if(document.getElementById('desc_list')[x].selected){
				resp = resp + "- " + list[x][1] + "\n";
			}
		}
		
		textArea.value = resp + DESC;
		refreshDescTab();
	}
	
	function refreshDescTab(){
		var temp = document.getElementsByName('entry.2041036538')[0].value;
		var count = (temp.match(/\n/g) || []).length;
		document.getElementsByName('descTab')[0].rows = count + 1;
		document.getElementsByName('entry.2041036538')[0].rows = count + 1;
		
		document.getElementsByName('descTab')[0].value = document.getElementsByName('entry.2041036538')[0].value;
	}
	
	function refreshDesc(){
		var temp = document.getElementsByName('descTab')[0].value;
		var count = (temp.match(/\n/g) || []).length;
		document.getElementsByName('descTab')[0].rows = count + 1;
		document.getElementsByName('entry.2041036538')[0].rows = count + 1;
		
		document.getElementsByName('entry.2041036538')[0].value = document.getElementsByName('descTab')[0].value;
	}
	
	function refreshComplementsTab(){
		var temp = document.getElementsByName('entry.1250423481')[0].value;
		var count = (temp.match(/\n/g) || []).length;
		document.getElementsByName('complementsTab')[1].rows = count + 1;
		
		document.getElementsByName('complementsTab')[1].value = document.getElementsByName('entry.1250423481')[0].value;
	}
	
	function refreshComplements(){
		var temp = document.getElementsByName('complementsTab')[1].value;
		var count = (temp.match(/\n/g) || []).length;
		document.getElementsByName('complementsTab')[1].rows = count + 1;
		document.getElementsByName('entry.1250423481')[0].rows = count + 1;
		
		document.getElementsByName('entry.1250423481')[0].value = document.getElementsByName('complementsTab')[1].value;
		document.getElementById("pendingReason").value = document.getElementsByName('entry.1250423481')[0].value;
	}
	
	/*-------------------------*/
	/* TRAITEMENT DES FIELDSET */
	/*-------------------------*/
	function show(id){
		var field = document.getElementById(id);
		var fieldList = document.getElementsByTagName("fieldset");
		
		if(field == null){
			alert(id + " n'éxiste pas ! Contactez un administrateur.");
			return false;
		}
		
		for(var x = 0; x < fieldList.length; x++){
			fieldList[x].style.display = "none";
		}
		
		field.style.display = "block";
		document.getElementById("question").value = question;
	}
	
	function next(){
		var nextField, currentField;
		currentField = suite[question];
		question++;
		nextField = suite[question];
		
		while(nextField == ""){
			question++;
			nextField = suite[question];
		}
		
		document.getElementById(currentField).style.display = "none";
		document.getElementById(nextField).style.display = "block";
		
		document.getElementById("question").value = question;
	}
	
	function prev(){
		var prevField, currentField;
		currentField = suite[question];
		question--;
		prevField = suite[question];
		
		while(prevField == ""){
			question--;
			prevField = suite[question];
		}
		
		document.getElementById(currentField).style.display = "none";
		document.getElementById(prevField).style.display = "block";
		
		document.getElementById("question").value = question;
	}
	
	/*-----------------------*/
	/* TRAITEMENT DES SWITCH */
	/*-----------------------*/
	function materiel(state){
		var input = document.getElementsByName("pret");
		var output = document.getElementsByName("entry.2094846173");
		var obj = document.getElementsByName("entry.912488796");
		
		if(state){
			if(input[0].checked && obj[0].checked){
				show("MAT");
				output[0].value = "Oui";
			}
			else if (input[0].checked && !(obj[0].checked)){
				show("MAT");
				output[0].value = "Oui";
			}
			else if (!(input[0].checked) && !(obj[0].checked)){
				show("LOI");
				output[0].value = "Non";
			}
			else{
				show("TEST");
				output[0].value = "Non";
			}
		}
		else {
			if(input[0].checked){
				show("MAT");
			}
			else{
				show("PRET");
			}
		}
			
	}
	
	function agence(entry){
		var sw = document.getElementsByClassName("toggle")[0].checked;
		if(sw){
			document.getElementsByName(entry)[0].value = "1";
		}
		else{
			document.getElementsByName(entry)[0].value = "0";
		}
	}
	
	function contrat(entry){
		var sw = document.getElementsByClassName("toggle")[1].checked;
		if(sw){
			document.getElementsByName(entry)[0].value = "Non";
		}
		else{
			document.getElementsByName(entry)[0].value = "Oui";
		}
	}
	
	function zone(entry){
		var sw = document.getElementsByClassName("toggle")[2].checked;
		if(sw){
			document.getElementsByName(entry)[0].value = "ZONE2";
		}
		else{
			document.getElementsByName(entry)[0].value = "ZONE1";
		}
		
		sw = document.getElementsByClassName("toggleZone")[0].checked;
		
		if(sw){
			document.getElementsByName(entry)[0].value = "AUCUN";
		}
	}
	
	function test_fonc(entry, previous){
		var sw = document.getElementsByClassName("toggle")[4].checked;
		
		if(sw){
			document.getElementsByName(entry)[0].value = "Négatif";
		}
		else{
			document.getElementsByName(entry)[0].value = "Positif";
		}
		
		sw = document.getElementsByClassName("toggle")[5].checked;
		
		if(sw){
			document.getElementsByName(entry)[0].value = "Non Concerné";
		}
		
		if((document.getElementsByClassName("toggle")[1].checked == false) && previous){
			fieldset[question].style.position = "absolute";
			question = question - 2;
			fieldset[question].style.position = "initial";
		}
		else if((document.getElementsByClassName("toggle")[1].checked == true) && previous){
			previous();
		}
	}
	
	function form_save(entry){
		var sw = document.getElementsByClassName("toggle")[6].checked;
		if(sw){
			document.getElementsByName(entry)[0].value = "Non";
		}
		else{
			document.getElementsByName(entry)[0].value = "Oui";
		}
		
		sw = document.getElementsByClassName("toggle")[7].checked;
		
		if(sw){
			document.getElementsByName(entry)[0].value = "Non Concerné";
		}
	}
	
	function loi_2013(entry){
		var sw = document.getElementsByClassName("toggle")[8].checked;
		if(sw){
			document.getElementsByName(entry)[0].value = "Non";
		}
		else{
			document.getElementsByName(entry)[0].value = "Oui";
		}
	}
	
	function upgrade(entry){
		var sw = document.getElementsByClassName("toggle")[9].checked;
		if(sw){
			document.getElementsByName(entry)[0].value = "Réalisée";
		}
		else{
			document.getElementsByName(entry)[0].value = "";
		}
		
		sw = document.getElementsByClassName("toggle")[10].checked;
		
		if(sw){
			document.getElementsByName(entry)[0].value = "Non Concerné";
		}
	}
	
	function object(entry){
		var descList = ["", "", ""];
		var repl = [false, document.getElementsByName(entry)[0].checked, document.getElementsByName(entry)[1].checked, document.getElementsByName(entry)[2].checked, document.getElementsByName(entry)[3].checked];
		var recapComplements = document.getElementsByName("complementsTab");
		
		if(repl[1] == true || repl[5] == true){
			suite[14] = "TEST";
			suite[15] = "SAVE";
			suite[19] = "COMPLEMENTS";
			for(var x = 0; x < recapComplements.length; x++){
					recapComplements[x].style.display = "";
			}
		}
		else{
			suite[14] = "";
			suite[15] = "";
			suite[19] = "";
			for(var x = 0; x < recapComplements.length; x++){
					recapComplements[x].style.display = "none";
			}
		}
		
		return listing(repl);
	}
	
	function techsSign(){
		var techList = document.getElementsByName("entry.1981179687")[0];
		var techPNG = document.getElementsByName("entry.117482882")[0];

		if((techList.value == "Anne HUCHET")){
			techPNG.value = "http://sav.tacteo.fr/signatures/TECHS/anne.png";
		}
		else if((techList.value == "Léo SZAFARZ")){
			techPNG.value = "http://sav.tacteo.fr/signatures/TECHS/leo.png";
		}
		else if((techList.value == "Romain JOLY")){
			techPNG.value = "http://sav.tacteo.fr/signatures/TECHS/romain.png";
		}
		else if((techList.value == "Dylan PELLETIER")){
			techPNG.value = "http://sav.tacteo.fr/signatures/TECHS/dylan.png";
		}
		else if((techList.value == "Esteban BORSATO")){
			techPNG.value = "http://sav.tacteo.fr/signatures/TECHS/esteban.png";
		}
		else if((techList.value == "Arnaud PRIGENT")){
			techPNG.value = "http://sav.tacteo.fr/signatures/TECHS/arnaud.png";
		}
		else if((techList.value == "Jordane MEILLAC")){
			techPNG.value = "http://sav.tacteo.fr/signatures/TECHS/jordane.png";
		}
		else if((techList.value == "Thierry BORSATO")){
			techPNG.value = "http://sav.tacteo.fr/signatures/TECHS/thierry.png";
		}
		else if((techList.value == "Bruno RODRIGUEZ")){
			techPNG.value = "http://sav.tacteo.fr/signatures/TECHS/bruno.png";
			}
	}
	
	/*--------------------*/
	/* CALCUL DES CHARGES */
	/*--------------------*/
	function calcCharges(){
		
		/*---------------------------------*/
		/* TRAITEMENT DE L'HEURE D'ARRIVEE */
		/*---------------------------------*/
		var hours = document.getElementById("hourArrived").value; //récupération des heures de l'heure d'arrivée.
		var minutes = document.getElementById("minuteArrived").value; //récupération des minutes de l'heure d'arrivée.
		var arrivedDEC = ((parseInt(hours)*60) + parseInt(minutes)) / 60; //Conversion en décimal de l'heure d'arrivée.
		var arrivedHOUR = Math.round(arrivedDEC - (arrivedDEC % 1)) + ":" + Math.round((arrivedDEC % 1) * 60);
		document.getElementById("arrived").value = hours + ":" + minutes; //Envois de l'heure d'arrivée dans le tableau.
		
		/*--------------------------------*/
		/*TRAITEMENT DE L'HEURE DE DEPART */
		/*--------------------------------*/
		hours = document.getElementById("hourLeaved").value;	//récupération des heures de départ.
		minutes = document.getElementById("minuteLeaved").value; //récupération des minutes de départ.
		var leavedDEC = ((parseInt(hours)*60) + parseInt(minutes)) / 60; //conversion en décimal de l'heure de départ.
		document.getElementById("leaved").value = hours + ":" + minutes; //affichage dans le tableau de l'heure de départ.
		
		/*--------------------------------------*/
		/* TRAITEMENT DU TEMPS DE MAIN D'OEUVRE */
		/*--------------------------------------*/
		var workedDEC = Math.round((leavedDEC - arrivedDEC)*100)/100;//Coefficient decimal du temps de main d'oeuvre.
		var workedHOUR = Math.round(workedDEC - (workedDEC % 1)) + " H " + Math.round((workedDEC % 1) * 60) + " Min (" + workedDEC + " H)."; //Création de la formule affiché dans le tableau.
		document.getElementById("worked").value = workedHOUR; //Affichage dans le tableau.

		var workforce = (Math.round((workedDEC * 50)*100)/100); //Calcul du prix total HT de main d'oeuvre.
		document.getElementById("workforce").value = workforce + " \u20AC"; //Affichage du prix arrondie au centième dans le tableau.
		document.getElementById("workedUnit").value = 50 + " \u20AC"; //Affichage dans le tableau du prix unitaire à l'heure HT de la main d'oeuvre.
		
		/*--------------------------------------*/
		/* TRAITEMENT DE LA ZONE DE DEPLACEMENT */
		/*--------------------------------------*/
		var zone = document.getElementById("travel").value; //Stockage de la valeur de ZONE.
		var travel;
		if (zone == "ZONE1"){ //Si la valeur est ZONE1 :
			document.getElementById("travelType").value = "ZONE1"; //Affichage de la valeur "ZONE1" dans le tableau.
			document.getElementById("travelUnit").value = 90.00 + " \u20AC"; //Affichage du prix unitaire pour la ZONE1 (90€).
			document.getElementById("travelTot").value = 90.00 + " \u20AC"; //Affichage du total HT pour le déplacement en ZONE1.
			travel = 90;
		}
		else if(zone == "ZONE2"){ //Si la valeur est ZONE1 :
			document.getElementById("travelType").value = "ZONE2"; //Affichage de la valeur "ZONE2" dans le tableau.
			document.getElementById("travelUnit").value = 150.00 + " \u20AC"; //Affichage du prix unitaire pour la ZONE2 (150€).
			document.getElementById("travelTot").value = 150.00 + " \u20AC"; //Affichage du total HT pour le déplacement en ZONE2.
			travel = 150;
		}
		else if(zone == "AUCUN"){ //Si la valeur est AUCUN :
			document.getElementById("travelType").value = "AUCUN"; //Affichage de la valeur "ZONE2" dans le tableau.
			document.getElementById("travelUnit").value = 0 + " \u20AC"; //Affichage du prix unitaire pour la ZONE2 (150€).
			document.getElementById("travelTot").value = 0 + " \u20AC"; //Affichage du total HT pour le déplacement en ZONE2.
			travel = 0;
		}
		else{ //Si la valeur ne correspond à aucunes des valeurs attendue, afficher une erreur :
			document.getElementById("travelType").value = "ERROR";
			document.getElementById("travelUnit").value = "ERROR";
			document.getElementById("travelTot").value = "ERROR";
		}
		
		/*-----------------------*/
		/* TRAITEMENT DES TOTAUX */
		/*-----------------------*/
		var factuName = document.getElementsByClassName('facturations');
		var factuPrix = document.getElementsByClassName('facturations_prix');
		var factuRemise = document.getElementsByClassName('remise');
		var factuTotHT = document.getElementById('factuTotHT');
		
		var factuNameTab = document.getElementsByClassName('designation_facturation');
		var factuPrixTab = document.getElementsByClassName('facturation_prix_tab');
		var factuRemiseTab = document.getElementsByClassName('facturation_remise_tab');
		var factuTotHTTab = document.getElementById('facturation_prix_total_ht');
		var factuTotTTCTab = document.getElementById('facturation_prix_total_ttc');
		
		var factuTotTVA = document.getElementById('facturation_prix_total_tva');
		
		for(var x = 0; x < factuName.length; x++)
		{
			if(factuName[x].value != "")
			{
				factuNameTab[x].value = factuName[x].value;
				factuPrixTab[x].value = factuPrix[x].value + " €";
				if(factuRemise[x].value != "")
				{
					factuRemiseTab[x].value = factuRemise[x].value + " %";
				}
				else
				{
					factuRemiseTab[x].value = "";
				}
			}
			else
			{
				factuNameTab[x].value = "";
				factuPrixTab[x].value = "";
				factuRemiseTab[x].value = "";
			}
		}
		
		if(factuTotHT.value == "")
		{
			factuTotHTTab.value = "0.00 €";
			factuTotTVA.value = "0.00 €";
			factuTotTTCTab.value = "0.00 €";
			var factuTTC = 0;
		}
		else
		{
			factuTotHTTab.value = factuTotHT.value + " €";
			var TVA = Math.round(((parseFloat(factuTotHT.value) / 100) * 20)*100)/100
			factuTotTVA.value = TVA + " €";
			var factuTTC = parseFloat(factuTotHT.value) + parseFloat(TVA);
			factuTotTTCTab.value = Math.round(factuTTC*100)/100 + " €";
		}
		
		var totalUniqueHT = travel + 50;
		document.getElementById("totalUniqueHT").value = totalUniqueHT + " \u20AC"; //Calcul et affichage du total HT unique.
		
		var totalUniqueTva = (totalUniqueHT / 100) * 20; //Calcul du coût TVA à l'unité.
		document.getElementById("totalUniqueTVA").value = totalUniqueTva + " \u20AC"; //Afficahnge du coût TVA à l'unité.
		
		var ttcUnique = totalUniqueTva + totalUniqueHT; //Calcul du TTC à l'unité.
		document.getElementById("ttcUnique").value = ttcUnique + " \u20AC"; //Affichage du TTC à l'unité.
		
		var totalHT = travel + workforce; //Calcul du total HT.
		document.getElementById("totalHT").value = totalHT + " \u20AC"; //Affichage du total HT.
		
		var tva = Math.round(((totalHT / 100) * 20)*100)/100; //Calcul du Total TVA;
		document.getElementById("totalTVA").value = tva + " \u20AC"; //Affichage du total TVA.
		
		var ttc = tva + totalHT; //Calcul du total TTC.
		document.getElementById("ttc").value = ttc + " \u20AC"; //Affichage du TTC.
		
		/*--------------------------------------*/
		/* TRAITEMENT DU CONTRAT DE MAINTENANCE */
		/*--------------------------------------*/
		var contrat = document.getElementById("cont").value;
		if(contrat == "Oui"){
			document.getElementById("contratUnique").value = "INCLUS";
			document.getElementById("contratTotal").value = "INCLUS";
			document.getElementById("total").value = 0.00 + (Math.round(factuTTC*100)/100) + "\u20AC";
		}
		else{
			document.getElementById("contratUnique").value = "Aucun";
			document.getElementById("contratTotal").value = "Aucun";
			console.log(document.getElementById("contratTotal").value);
			document.getElementById("total").value = ttc + (Math.round(factuTTC*100)/100) + " \u20AC";
		}
	}
	
	/*-------------------------*/
	/* GESTION DE LA SIGNATURE */
	/*-------------------------*/
	var canvas = document.getElementById("myCanvas");

	var signaturePad = new SignaturePad(canvas, {
		minWidth: 2,
		maxWidth: 10,
		penColor: "black",
		throttle: 0
	});
	
	function formationDist(){
		document.getElementsByName("entry.1142705552")[0].value = "http://sav.tacteo.fr/signatures/.formation_distance/formationDist.png";
	}
	
	/*-------------------------------*/
	/* EXPORT EN PNG DE LA SIGNATURE */
	/*-------------------------------*/
	function saved(){
		var dataURL = canvas.toDataURL();
		var nomClient = document.getElementsByName("entry.532586694")[0].value;
		
		nomClient = nomClient.replace(/>|<|\\|\?|\'|\/|\"|\*|:|\||\;/gi, function(x){
			return x = "";
		});
		
		nomClient = nomClient.toUpperCase();
		
		var fileDirectory = "../signatures/" + nomClient;
		var date = toDay();
		var nameFile = "SIGNATURE_" + nomClient + "_" + date + "_" + Math.trunc(Math.random() * 1000) + ".png";
		
		$.ajax({
			type: "POST",
			url: "upload.php",
			data: { 
				imgBase64:		dataURL,
				fileDirectory:	fileDirectory,
				nameFile:		nameFile,
				nomClient:		nomClient,
			},
		}).done(function() {
			var result = document.getElementsByName("entry.1142705552")[0];
			var url = "http://sav.tacteo.fr/signatures/" + nomClient + "/" + nameFile;
			
			url = url.replace(/ /gi, function(x){
				return x = "%20";
			});
			
			result.value = url;
		});
	}
	
	function clean(){
		signaturePad.clear();
	}
	
	function submited(valid){
		if (valid){
			var error = checking("*");
			if (error){
				return false;
			}
		}
		else {
			prev();
			return false;
		}
		
		document.getElementById("CONFIRM").style.display = "none";
		show("LOADING");

		var test = document.getElementById("form").submit();
	}
	
	function checking(state){
		var LOADING, AGENCE, OBJECT, HOUR_START, SEL_CLIENT, CODE, ADDRESS, TELEPHONE, MAIL, CONTRAT, DESC, ZONE, PRET, MAT, TEST, SAVE, LOI, MAJ, VERSION, COMPLEMENTS, HOUR_END, NAME, QUALITY, TECH, TAB, SIGN;
		AGENCE =	 		document.getElementsByName("entry.433474814")[0]; 	//01
		OBJECT = 			document.getElementsByName("entry.912488796")[4]; 	//02
		HOUR_START =	 	[document.getElementsByName("entry.1667284575_hour")[0], document.getElementsByName("entry.1667284575_minute")[0]]; 	//03
		SEL_CLIENT = 		document.getElementsByName("entry.532586694")[0]; 	//04
		CODE =	 			document.getElementsByName("entry.845891913")[0]; 	//05
		ADDRESS =	 		document.getElementsByName("entry.1229884392")[0];	//06
		TELEPHONE = 		document.getElementsByName("entry.1886220397")[0]; //07
		MAIL = 				document.getElementsByName("entry.19991430")[0]; 	//08
		CONTRAT = 			document.getElementsByName("entry.2021019270")[0]; //09
		DESC = 				document.getElementsByName("entry.2041036538")[0]; //10
		ZONE = 				document.getElementsByName("entry.214594423")[0]; 	//11
		PRET = 				document.getElementsByName("entry.2094846173")[0]; //12
		MAT = 				[document.getElementsByName("entry.2082384825")[0], document.getElementsByName("entry.2047563043")[0]]; //13
		TEST = 				document.getElementsByName("entry.246656631")[0]; 	//14
		SAVE = 				document.getElementsByName("entry.1885583728")[0]; //15
		LOI = 				document.getElementsByName("entry.1676268250")[0]; //16
		MAJ = 				document.getElementsByName("entry.509496568")[0]; 	//17
		VERSION = 			document.getElementsByName("entry.1389074181")[0]; //18
		COMPLEMENTS = 		document.getElementsByName("entry.1250423481")[0];	//19
		HOUR_END = 			[document.getElementsByName("entry.1131156718_hour")[0], document.getElementsByName("entry.1131156718_minute")[0]]; //20
		NAME = 				document.getElementsByName("entry.1445723676")[0];	//21
		QUALITY = 			document.getElementsByName("entry.184632626")[0]; 	//22
		TECH = 				[document.getElementsByName("entry.1981179687")[0], document.getElementsByName("entry.117482882")[0]]; //23
		SIGN = 				document.getElementsByName("entry.1142705552")[0];	//24
		CODECOM =			document.getElementsByName("entry.1384966339")[0];
		
		var MINUTES_START	=	(parseInt(HOUR_START[0].value) * 60) + parseInt(HOUR_START[1].value);
		var MINUTES_END		=	(parseInt(HOUR_END[0].value) * 60) + parseInt(HOUR_END[1].value);
		
		console.log(MINUTES_START);
		console.log(MINUTES_END);
		
		if(AGENCE.value == "" 							&& (state == "AGENCE" 		|| state == "*")){
			if(confirm("ATTENTION : Aucune agence renseignée. Continuer ?")){
			}
			else {
				show("AGENCE");
				question = 1;
				return true;
			}
		}
		if(OBJECT.value == "" 							&& (state == "OBJECT"		|| state == "*")){
			alert("ERREUR : Aucun objet renseigné !");
			show("OBJECT");
			question = 2;
			return true;
		}
		if(MINUTES_START >= MINUTES_END 				&& (state == "HOUR"			|| state == "*")){
			alert("ERREUR : Les heures d'arrivée/départ sont mal renseignées !\n\nHeure d'arrivée : " + HOUR_START[0].value + ":" + HOUR_START[1].value + "\nHeure de départ : " + HOUR_END[0].value + ":" + HOUR_END[1].value + "\n\nMerci de corriger les heures d'arrivée/départ.")
			show("HOUR_END");
			question = 20;
			return true;
		}
		if(SEL_CLIENT.value == "" 						&& (state == "SEL_CLIENT"	|| state == "*")){
			alert("ERREUR : Aucun client renseigné !");
			show("SEL_CLIENT");
			question = 4;
			return true;
		}
		if(CODE.value == "" 							&& (state == "INFOS"		|| state == "*")){
			alert("ERREUR : Aucun code client renseigné !");
			show("INFOS");
			question = 5;
			return true;
		}
		if(ADDRESS.value == "" 							&& (state == "INFOS"		|| state == "*")){
			alert("ERREUR : Aucune adresse client renseignée !");
			show("INFOS");
			question = 5;
			return true;
		}
		if(TELEPHONE.value == "" 						&& (state == "INFOS"		|| state == "*")){
			if(confirm("ATTENTION : Aucun téléphone renseigné. Continuer ?")){
			}
			else {
				show("INFOS");
				question = 5;
				return true;
			}
		}
		if(MAIL.value == "" 							&& (state == "INFOS"		|| state == "*")){
			alert("ERREUR : Aucun mail client renseigné ! Il est nécessaire pour l'envois d'une copie.");
			show("INFOS");
			question = 5;
			return true;
		}
		if(CONTRAT.value == "" 							&& (state == "CONTRAT"		|| state == "*")){
			alert("ERREUR : Le client est-il sous contrat ?");
			show("CONTRAT");
			question = 9;
			return true;
		}
		if(DESC.value == "" 							&& (state == "DESC"			|| state == "*")){
			if(confirm("ATTENTION : Aucune description renseignée. Continuer ?")){
			}
			else {
				show("DESC");
				question = 10;
				return true;
			}
		}
		if(ZONE.value == "" 							&& (state == "ZONE"			|| state == "*")){
			alert("ERREUR : Aucune zone de déplacement renseignée ! Appelez un administrateur.");
			show("ZONE");
			question = 11;
			return true;
		}
		if(LOI.value == "" 								&& (state == "LOI"			|| state == "*")){
			alert("ERREUR : Aucune loi indiquée. Appelez un administrateur.");
			show("LOI");
			question = 16;
			return true;
		}
		// if(VERSION.value == "" 							&& (state == "VERSION"		|| state == "*")){
			// alert("ERREUR : Aucune version renseignée.");
			// show("VERSION");
			// question = 18;
			// return true;
		// }
		if(NAME.value == "" 							&& (state == "NAME"			|| state == "*")){
			alert("ERREUR : Aucun nom renseigné pour le signataire !");
			show("NAME");
			question = 22;
			return true;
		}
		if(SIGN.value == "" 							&& (state == "SIGN"			|| state == "*")){
			alert("ERREUR : Aucune signature !");
			show("SIGN");
			question = 25;
			return true;
		}
		
		return false;
	}