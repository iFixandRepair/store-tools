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
		
		$CLS_ViewReport = new SalesReport();
		$arr = $CLS_ViewReport->getSalesReport($date_ini,$date_end,$typeRep );	
		## En caso de que sea por meses, convierto el numero de mes al nombre	
		if($typeRep==3)
		{
			foreach ($arr as $row) 
			{
				$objDateTime = new DateTime('NOW');
				$SrtFecha = $row->Date.'/'.$row->Date.'/'.$objDateTime->format('Y');
				//echo $SrtFecha.'<br>';
				$row->Date = date('F',strtotime($SrtFecha));
			}
			
		}
		echo '{"data": '.json_encode($arr).' }';
	}
	if($opt=='saveComment')
	{		
		$store 		= $_REQUEST['store'];
		$message	= $_REQUEST['message'];	
		$mes 		= $_REQUEST['month'];
		$year		= $_REQUEST['year'];		
		
		$CLS_ViewReport = new SalesReport();
		$rst = $CLS_ViewReport->setStoreComment($store,$message,$mes,$year);
		//echo 'Va a guardar: <br>'.$store.', Mensaje: '.$message.', Mes: '.$mes.', Año: '.$year;	
		echo '{"success": '.$rst.' }';
	}	

}
	
	



?>