<?php
	require('../inc/accounts.inc.php');
?>

<html style="display:none;">
	<body onload="load()">
		<?php
			require('../inc/table.inc.php');
		?>
	</body>
</html>

<script>
	function load(){
		document.getElementsByTagName('html')[0].style.display = '';
	}
</script>