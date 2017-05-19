<?php
/**********************************************/
/**********************************************/
/***		OPERATIONS VIEW REPORT VR 		***/
/**********************************************/
/***	web.developer@ifixandrepair.com		***/
/**********************************************/
/**********************************************/
require_once "../config.php";
require_once '../lib/RQSalesLoader.php';
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{	
	$Month 		= $_REQUEST['cmdMonth'];
	$sourceFile = 'storeGoals';
	if($_FILES['storeGoals']['name'] !='')
	{
		$rqLoader 		= new RQSalesLoader();
		$name_file 		= $_FILES[$sourceFile]['name'];
		$ext_file 		= explode('.', $name_file);
		$lenght 		= count($ext_file)-1;
		$ext_file 		= $ext_file[$lenght];
		$target_dir 	= "../uploads/";
		if(!is_dir($target_dir)) 
		{
	        mkdir($target_dir, 0777);
		}
		$objDateTime = new DateTime('NOW');
		$target_file = $target_dir .'Upload_for_Month_'.$Month.'_storeGoals_'.$objDateTime->format('Y-m-d').'_'.($objDateTime->format('H')-6).'-'.$objDateTime->format('i').'.'.$ext_file;
		if ($name_file && move_uploaded_file($_FILES[$sourceFile]['tmp_name'], $target_file)) 
		{
			$rst =  $rqLoader->loadStoreGoals($target_file,$Month);
			echo '{"success":'.$rst.'}'; 
		} 
		else 
		{
			echo '{"success":false}'; 		
		}
		
	}
}


?>