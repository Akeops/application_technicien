function addType()
{
	$("#uiAddType")[0].style.display = "block";
}

function closeType()
{
	$("#uiAddType")[0].style.display = "none";
	for(x = 0; x < $("#uiAddType [name]").length; x++)
	{
	$("#uiAddType [name]")[x].value = "";
	}
}

function createType()
{
	var data = $("#uiAddType form");
	
	$.post(
		'gmao/settings/intervention_settings.php',
		{
			create:			1,
			page:			"type",
			NAME:			$("#uiAddType [name=NAME]")[0].value,
			DESCRIPTION:	$("#uiAddType [name=DESCRIPTION]")[0].value
		},
		function(data)
		{
			location.reload();
		}
	);
}

function openDeleteType(id, type)
{
	var ui = $("#uiDeleteType")[0];
	ui.style.display = "block";
	
	var button = $("#uiButtonYes")[0];
	var name = type.parentNode.previousElementSibling.childNodes[1].innerHTML;
	var msgDesc = type.parentNode.previousElementSibling.getAttribute("title");;
	var msg = $("#uiDeleteType #deleteTypeMsg h3")[0];
	var desc = $("#uiDeleteType #deleteTypeDesc")[0];
	
	msg.innerHTML = "Vous êtes sur le point de supprimer " + name;
	desc.innerHTML = msgDesc;
	button.setAttribute("onclick","deleteType(" + id + ")");
}

function deleteType(id)
{	
	$.post(
		'gmao/settings/intervention_settings.php',
		{
			delete:			1,
			page:			"type",
			ID:				id
		},
		function(data)
		{
			var json = JSON.parse(data);
			if(json['success'] == true)
			{
				location.reload();
			}
		}
	);
}