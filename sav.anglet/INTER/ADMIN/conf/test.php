<div class="button" onclick="check(this)">
	<div>
		<input type="checkbox" name="test">
	</div>
</div>


<style>
	.button{
		display:		block;
		width:			65px;
		height:			30px;
		box-shadow:		inset 0 0 5px black;
		border-radius:	100px;
		background:		lightgrey;
		text-align:		right;
		transition:		all 0.3s ease;
	}
	
	.button div{
		display:		block;
		width:			30px;
		height:			30px;
		border-radius:	100%;
		background:		white;
		box-shadow:		0 0 5px black;
		transition:		all 0.3s ease;
	}
	
	.button input{
		display:		none;
	}
</style>

<script>
	function check(button){
		var slide = button.getElementsByTagName("div")[0];
		var input = button.getElementsByTagName("input")[0];

		if(input.checked)
		{
			slide.removeAttribute("style");
			input.checked = "";
			button.removeAttribute("style");
		}
		else
		{
			slide.style.marginLeft = "calc(100% - 28px)";
			
			input.checked = "true";
			button.style.background = "lightgreen";
		}
	}
</script>