<?php
	require_once('../sellsy/sellsyconnect_curl.php');
	require_once('../sellsy/sellsytools.php');
	require_once('../sellsy/sellsy.php');
?>

<form action="" method="post" enctype="multipart/form-data">
	Select image to upload:<br>
	<input type="file" name="fileToUpload" id="fileToUpload">
	<input type="submit" value="Upload " name="submit">
</form>

<?php
	$request = array(
		"method" => "Briefcases.uploadFile",
		"params" => array(
			'linkedtype' => 'third',
			'linkedid' => 26262314
		)
	);
	$file = $_FILES['fileToUpload'];
	echo var_export($_FILES, true);
	$response = sellsyconnect_curl::load()->requestApi($request, $file);
	echo '<pre>'.var_export($response, true).'</pre>';
	echo '<hr>';
