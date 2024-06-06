function create()
{
	var url = "gmao/settings.php?paperwork&create";
	$.ajax({
		url : url || window.location.pathname,
		type: "POST",
		data: "create=1",
		dataType:	'html',
		success: function (data) {
			var result = JSON.parse(data);
			if(result['success'] == 1)
			{
				location.href = "http://sav.tacteo.fr/admin/index.php?page=settings&setting=paperwork&edit="+result['id'];
			}
			else
			{
				alert(result['error']);
			}
		},
		error: function (jXHR, textStatus, errorThrown) {
			alert(errorThrown);
		}
	});
}

function deleteInfo(del)
{
	var paperwork = del.parentNode.previousElementSibling.firstElementChild;
	var name = paperwork.innerHTML;
	var id = paperwork.getAttribute("id");
	var ui = $("#deleteInfo")[0];
	var msg = $("#deleteTypeMsg")[0].firstElementChild;
	msg.innerHTML = "Vous êtes sur le point de supprimer \""+name+"\".";
	ui.style.display = "block";
	var button = $("#uiButtonYes")[0];
	button.setAttribute("onclick", "deletePaper("+id+")");
}

function deletePaper(id)
{
	var url = "gmao/settings.php?paperwork";
	$.ajax({
		url : url || window.location.pathname,
		type: "POST",
		data: "DELETE="+id,
		dataType:	'html',
		success: function (data) {
			var result = JSON.parse(data);
			if(result['success'] == 1)
			{
				location.reload();
			}
			else
			{
				alert(result['error']);
			}
		},
		error: function (jXHR, textStatus, errorThrown) {
			alert(errorThrown);
		}
	});
}

$(document).ready(function () {
	$('#paperwork_editor').on('submit', function(e) {
		$("#submit_button")[0].value = "...";
		$("textarea[name=BODY]")[0].value = CKEDITOR.instances.BODY.getData();
		e.preventDefault();
		
		var values = $("#types input[type='checkbox']");
		var types = new Object;
		for(x = 0; x < values.length; x++)
		{
			types[values[x].getAttribute("name")] = values[x].checked;
		}
		types = JSON.stringify(types);
		
		var values = $("#activities input[type='checkbox']");
		var activities = new Object;
		for(x = 0; x < values.length; x++)
		{
			activities[values[x].getAttribute("name")] = values[x].checked;
		}
		activities = JSON.stringify(activities);
		
		$.ajax({
			url : $(this).attr('action') || window.location.pathname,
			type: "POST",
			data: $(this).serialize()+"&TYPES="+types+"&ACTIVITIES="+activities,
			dataType:	'html',
			success: function (data) {
				var result = JSON.parse(data);
				if(result['success'] == 1)
				{
					$("#submit_button")[0].value = "Enregsitré !";
					setTimeout(function(){ $("#submit_button")[0].value = "ENREGISTRER"; }, 1000);
				}
				else
				{
					alert(result['error']);
					$("#submit_button")[0].value = "ENREGISTRER";
				}
			},
			error: function (jXHR, textStatus, errorThrown) {
				alert(errorThrown);
				$("#submit_button")[0].value = "ENREGISTRER";
			}
		});
	});
});
CKEDITOR.replace( 'BODY', {
	toolbar: [
		{ name: 'clipboard', items: [ 'Undo', 'Redo' ] },
		{ name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'CopyFormatting' ] },
		{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
		{ name: 'align', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
		{ name: 'links', items: [ 'Link', 'Unlink' ] },
		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
		{ name: 'insert', items: ['Table'] },
		{ name: 'tools', items: [ 'Maximize' ] },
		{ name: 'editing', items: [ 'Scayt' ] }
	],
	customConfig: '',
	uiColor: 'rgba(0,0,0,0.3)',
	disallowedContent: 'img{width,height,float}',
	extraAllowedContent: 'img[width,height,align]',
	extraPlugins: 'tableresize',//uploadimage,uploadfile,
	height: 700,
	contentsCss: [ 'https://cdn.ckeditor.com/4.8.0/full-all/contents.css', 'css/editor.css' ],
	bodyClass: 'document-editor',
	format_tags: 'p;h1;h2;h3;pre',
	removeDialogTabs: 'image:advanced;link:advanced',
	stylesSet: [
		{ name: 'Marker', element: 'span', attributes: { 'class': 'marker' } },
		{ name: 'Cited Work', element: 'cite' },
		{ name: 'Inline Quotation', element: 'q' },
		{
			name: 'Special Container',
			element: 'div',
			styles: {
				padding: '5px 10px',
				background: '#eee',
				border: '1px solid #ccc'
			}
		},
		{
			name: 'Compact table',
			element: 'table',
			attributes: {
				cellpadding: '5',
				cellspacing: '0',
				border: '1',
				bordercolor: '#ccc'
			},
			styles: {
				'border-collapse': 'collapse'
			}
		},
		{ name: 'Borderless Table', element: 'table', styles: { 'border-style': 'hidden', 'background-color': '#E6E6FA' } },
		{ name: 'Square Bulleted List', element: 'ul', styles: { 'list-style-type': 'square' } }
	]
} );