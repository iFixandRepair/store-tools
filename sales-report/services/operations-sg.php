<?php
/**********************************************/
/**********************************************/
/***		OPERATIONS VIEW REPORT VR 		***/
/**********************************************/
/***	web.developer@ifixandrepair.com		***/
/**********************************************/
/**********************************************/
require_once "../config.php";
require_once '../lib/StoreGoals.php';

$opt = '';
if (isset($_REQUEST['opt']))
{
	$opt = $_REQUEST['opt'];
	if($opt=='load')
	{
		$objDateTime = new DateTime('NOW');
		$CLS_StoreGoals = new StoreGoals();
		$arr = $CLS_StoreGoals->GetStoreGoals();
		foreach ($arr as $row) 
		{
			## Obtengo el nombre del mes mediante PHP
			$SrtFecha = $objDateTime->format('m').'/'.$row->Month.'/'.$objDateTime->format('Y');
			$row->Month = date('F',strtotime($SrtFecha));
		}
		echo '{"data": '.json_encode($arr).' }';
	}
	if($opt=='update')
	{
		$Id 	= $_REQUEST['Id'];
		$Hours 	= $_REQUEST['Hours'];
		$MgrHrs = $_REQUEST['MgrHrs'];
		$EmpHrs = $_REQUEST['EmpHrs'];

		$CLS_StoreGoals = new StoreGoals();
		$success = $CLS_StoreGoals->UpdateStoreGoals($Id,$Hours,$MgrHrs,$EmpHrs);
		echo '{"success": '.json_encode($success).' }';
	}

}
	
	



?>