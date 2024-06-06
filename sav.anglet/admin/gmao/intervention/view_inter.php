<?php
	require_once(__DIR__ . '/../../inc/login.inc.php');
	require_once(__DIR__ . '/../../inc/intergen.inc.php');
	require_once (__DIR__ . '/../../../vendor/autoload.php');
	$mpdf = new \Mpdf\Mpdf([
		'default_font_size' => 6,
		'margin_top' => 30,
		'margin_bottom' => 30
		]
	);
	$mpdf->showImageErrors = true;
	$mpdf->WriteHTML($html['VOUCHER']);
	if(!empty($html['BILLINGS_THIRD']))
	{
		$mpdf->AddPage('','',1);
		$mpdf->WriteHTML($html['BILLINGS_THIRD']);
	}
	if(!empty($html['ATTACHMENTS']))
	{
		$mpdf->AddPage('','',1);
		$mpdf->WriteHTML($html['ATTACHMENTS']);
	}
	if(!empty($_INTER['PAPERWORKS']) && $_INTER['PAPERWORKS'] != '{}')
	{
		$paperworks = JSON_decode($_INTER['PAPERWORKS']);
		for($x = 0; $x < count($paperworks); $x++)
		{
			$mpdf->AddPage('','',1);
			$mpdf->WriteHTML('<!--mpdf<sethtmlpageheader name="myheader" value="off" show-this-page="1" />mpdf-->');
			$mpdf->WriteHTML('<!--mpdf<htmlpagefooter name="myfooter">
					<div style="border-top: 0.5mm solid #000000; font-size: 5pt; text-align: center; padding-top: 3mm; ">
						'.$_ESTABLISHMENT['END_TEXT'].'<br /><br />
						<span style="font-size:8pt">'.$paperworks[$x][0].'</span><br />
						<span style="font-size:8pt">{PAGENO} / {nbpg}</span>
					</div>
				</htmlpagefooter>
				<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
				<sethtmlpagefooter name="myfooter" value="on" />
			mpdf-->');
			$mpdf->WriteHTML($paperworks[$x][1]);
		}
	}
	
	$mpdf->Output();
?>