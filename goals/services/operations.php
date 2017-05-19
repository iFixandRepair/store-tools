<?php
/**********************************************/
/**********************************************/
/***		OPERATIONS VIEW REPORT VR 		***/
/**********************************************/
/***	web.developer@ifixandrepair.com		***/
/**********************************************/
/**********************************************/
require_once "../config.php";
require_once '../lib/Goals.php';

$method = '';
$opt = '';
if (isset($_REQUEST['method']))
{
	if (isset($_REQUEST['opt']))
	{
		$method = $_REQUEST['method'];
		$opt 	= $_REQUEST['opt'];
		//echo 'METHOD: '.$method."<br>OPT: ".$opt.'<br>';
		#########################
		######### GOALS #########
		#########################
		if($method=='goals')
		{
			if($opt=='load')
			{
				$objDateTime	= new DateTime('NOW');
				$CLS_Goals 		= new Goals();
				$arr 			= $CLS_Goals->GetGoals();
				/*foreach ($arr as $row) 
				{
					## Obtengo el nombre del mes mediante PHP
					$SrtFecha = $objDateTime->format('m').'/'.$row->Month.'/'.$objDateTime->format('Y');
					$row->Month = date('F',strtotime($SrtFecha));
				}*/
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

	}
}
	
	



?>