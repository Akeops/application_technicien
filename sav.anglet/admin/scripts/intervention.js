$("#searchButton").on("click", showSearchbar);

$( document ).ready(function() {
    if($_GET('id') != null)
	{
		$("input[name=ID]")[0].value = $_GET("id");
	}
	if($_GET('type') != null)
	{
		$("select[name=TYPE]")[0].value = $_GET("type").toUpperCase();
	}
	if($_GET('date_start') != null)
	{
		$("input[name=DATE_START]")[0].value = $_GET("date_start");
	}
	if($_GET('date_end') != null)
	{
		$("input[name=DATE_END]")[0].value = $_GET("date_end");
	}
	if($_GET('user') != null)
	{
		$("select[name=USER]")[0].value = $_GET("user");
	}
	if($_GET('customer') != null)
	{
		$("input[name=CUSTOMER]")[0].value = decodeURI($_GET("customer")).toUpperCase();
	}
	if($_GET('cost') != null)
	{
		$("input[name=COST]")[0].value = decodeURI($_GET("cost"));
	}
	if($_GET('description') != null)
	{
		$("input[name=DESCRIPTION]")[0].value = decodeURI($_GET("description")).toUpperCase();
	}
	if($_GET('agency') != null)
	{
		$("select[name=AGENCY]")[0].value = $_GET("agency");
	}
	if($_GET('archived') != null)
	{
		$("select[name=ARCHIVED]")[0].value = $_GET("archived");
	}
});

showSearchbar(window.localStorage.getItem("TOGGLE_SEARCH"));

function searchInter()
{
	var inputs = $("#searchbar input[name], #searchbar select[name]");
	var request = "/admin/gmao.php?page=inter";
	for(x = 0; x < inputs.length; x++)
	{
		if(inputs[x].value != "")
		{
			request = request + "&" + inputs[x].name.toLowerCase() + "=" + inputs[x].value.toLowerCase();
		}
	}
	
	request = request + "&srch=1";
	sendRequest(request);
}

function showSearchbar(toggle)
{
	var searchbar = $("#searchbar")[0];
	var table = $("#search-results")[0];
	if(typeof(toggle) == "string")
	{
		if(toggle == true)
		{
			searchbar.style.top = "0";
			table.style.top = "0";
		}
		else
		{
			searchbar.style.top = "-100px";
			table.style.top = "-100px";
		}
	}
	else if(searchbar.style.top == "-100px" || searchbar.style.top == "")
	{
		searchbar.style.top = "0";
		table.style.top = "0";
		window.localStorage.setItem("TOGGLE_SEARCH", 1);
	}
	else
	{
		searchbar.style.top = "-100px";
		table.style.top = "-100px";
		window.localStorage.setItem("TOGGLE_SEARCH", 0);
	}
}