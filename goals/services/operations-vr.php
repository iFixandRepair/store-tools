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
		$date 		= $_REQUEST['date'];
		$typeRep 	= $_REQUEST['typeRep'];
		$CLS_ViewReport = new SalesReport();
		$arr = $CLS_ViewReport->getGoalsReport($date,$typeRep);	
		## En caso de que sea por meses, convierto el numero de mes al nombre	
		echo '{"data": '.json_encode($arr).' }';
	}
}
?>