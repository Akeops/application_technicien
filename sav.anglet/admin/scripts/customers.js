$("#searchButton").on("click", showSearchbar);

$( document ).ready(function() {
    if($_GET('contract') != null)
	{
		$("select[name=CONTRACT]")[0].value = $_GET("contract");
	}
	if($_GET('balance') != null)
	{
		$("input[name=BALANCE]")[0].value = $_GET("balance");
	}
	if($_GET('customer') != null)
	{
		$("input[name=CUSTOMER]")[0].value = decodeURI($_GET("customer")).toUpperCase();
	}
});

showSearchbar(window.localStorage.getItem("TOGGLE_SEARCH"));

function searchCustomer()
{
	var inputs = $("#searchbar input[name], #searchbar select[name]");
	var request = "/admin/gmao.php?page=customers";
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