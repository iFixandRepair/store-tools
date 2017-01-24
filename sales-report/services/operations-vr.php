<?php
/**********************************************/
/**********************************************/
/***		OPERATIONS VIEW REPORT VR 		***/
/**********************************************/
/***	web.developer@ifixandrepair.com		***/
/**********************************************/
/**********************************************/

require_once "../config.php";
require_once '../lib/SalesReport.php';

$opt = '';
if (isset($_REQUEST['opt']))
{
	$opt = $_REQUEST['opt'];
	if($opt=='get')
	{		
		$date_ini 	= $_REQUEST['date_ini'];
		$date_end	= $_REQUEST['date_end'];
		$typeRep 	= $_REQUEST['typeRep'];
		
		$CLS_StoreGoals = new SalesReport();
		$arr = $CLS_StoreGoals->getSalesReport($date_ini,$date_end,$typeRep );	
		## En caso de que sea por meses, convierto el numero de mes al nombre	
		if($typeRep==3)
		{
			foreach ($arr as $row) 
			{
				$objDateTime = new DateTime('NOW');
				$SrtFecha = $objDateTime->format('m').'/'.$row->Date.'/'.$objDateTime->format('Y');
				$row->Date = date('F',strtotime($SrtFecha));
			}
			
		}
		echo '{"data": '.json_encode($arr).' }';
	}	

}
	
	



?>