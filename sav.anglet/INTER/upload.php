<?php
	$fileDirectory =	$_POST['fileDirectory'];
	$fileName =			$_POST['nameFile'];
	$clientName =		$_POST['nomClient'];
	$img =				$_POST['imgBase64'];
	
	$img = str_replace('data:image/png;base64,', '', $img);
	$img = str_replace(' ', '+', $img);
	$fileData = base64_decode($img);
	
	if (!file_exists($fileDirectory)) {
		mkdir($fileDirectory, 0777, true);
	}
	
	$fileName = $fileDirectory."/".$fileName;
	
	file_put_contents($fileName, $fileData);
?>