function reset(content)
{
	switch(content){
		case "1":
			var section = document.getElementById("sectionEtablishment");
			var inputs = section.getElementsByTagName("input");
			var textarea = section.getElementsByTagName("textarea");
			
			for(var x = 0; x < inputs.length; x++)
			{
				inputs[x].value = inputs[x].defaultValue;
			}
			
			for(var x = 0; x < textarea.length; x++)
			{
				textarea[x].value = textarea[x].defaultValue;
			}
			
			break;
	}
}

function show(section)
{
	var sections = document.getElementsByClassName("sectionContent");
	var sectionContent = section.parentNode.getElementsByClassName("sectionContent")[0];
	var h2 = sectionContent.parentNode.getElementsByTagName('h2')[0];
	
	console.log(sectionContent);
	
	if(sectionContent.style.display == "")
	{
		sectionContent.style.display = "none";
		h2.style.background = "";
		h2.style.boxShadow = "";
	}
	else
	{
		for(var x = 0; x < sections.length; x++)
		{
			sections[x].style.display = "none";
			sections[x].parentNode.getElementsByTagName("h2")[0].style.background = "";
			sections[x].parentNode.getElementsByTagName("h2")[0].style.boxShadow = "";
		}
		
		sectionContent.style.display = "";
		h2.style.background = "#F28C0F";
		h2.style.boxShadow = "0 0 7px black";
		h2.style.textShadow = "0px 0px 7px black";
	}
}

function refresh(button)
{
	button.value = "Actualisation...";
	button.style.color = "";
	button.disabled = "true";
	
	$.post({
		url:	"../tools/import_export.tool.php",
		data:	{
			import:	1
		},
		success: function(html, statut)
		{
			var errorOutput = document.getElementById("error");
			console.log(html);
			if(html === "1"){
				errorOutput.innerHTML = "";
				button.value = "OK !";
				button.style.color = "green";
			}
			else
			{
				errorOutput.innerHTML = html;
				button.value = "FAIL";
				button.style.color = "red";
			}
		},
		error: function(result, statut, error)
		{
			var errorOutput = document.getElementById("error");
			errorOutput.innerHTML = error;
			
			button.value = "FAIL";
			button.style.color = "red";
		},
		complete: function(result, statut)
		{
			button.removeAttribute("disabled");
		}
	});
}