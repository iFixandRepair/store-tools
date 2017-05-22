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
	$rqLoader= new RQSalesLoader();
	$success='false';
	if($_FILES['LocationSales']['name'] !='')
	{
		$arrLocationSales = uploadFile('LocationSales',1);
		if($rqLoader->GuardarVentasProcesadas($arrLocationSales,1))
		{
			$success='true';
			$rst['data']['LocationSales'] = true;
		}
		else
		{
			$rst['data']['LocationSales'] = false;
		}
	}
	if($_FILES['EmployeeSales']['name'] !='')
	{
		$arrEmployeeSales = uploadFile('EmployeeSales',2);
		if($rqLoader->GuardarVentasProcesadas($arrEmployeeSales,2))
		{
			$success='true';
			$rst['data']['EmployeeSales'] = true;
		}
		else
		{
			$rst['data']['EmployeeSales'] = false;
		}
	}	

	echo '{"success": '.$success.',"data": '.json_encode($rst['data']).' }';
	

}

/*************************************************************************************/
/*************************************************************************************/
/*** function:    uploadFile                                                       ***/
/*** Description: Funcion encargada de subir los archivos al servidor el cual      ***/
/***              Dependiendo el archivo le coloca el nombre.        			   ***/
/***              Todos los archivos se guardan en la carpeta llamada "uploads"    ***/
/***              Que se encuentra en el root de este modulo 					   ***/
/*** In Params:   $sourceFile:	Archivo a subir (tipo $_FILE)                      ***/
/***              $typeFile:  Tipo de archivo 1.Store Sales 2.Employee Sales       ***/
/*** Return:      Arreglo				                                           ***/
/*************************************************************************************/
/*************************************************************************************/
function uploadFile($sourceFile,$typeFile)
{
	$rqLoader 		= new RQSalesLoader();
	$name_file 		= $_FILES[$sourceFile]['name'];
	$ext_file 		= explode('.', $name_file);
	$lenght 		= count($ext_file)-1;
	$ext_file 		= $ext_file[$lenght];
	$target_dir 	= "../uploads/";
	$rst 			= ''; 
	if(!is_dir($target_dir)) 
	{
        mkdir($target_dir, 0777);
	}
	$objDateTime = new DateTime('NOW');
	if($typeFile==1)	
	{
		$target_file = $target_dir .'Upload_for_Store_sales_'.$objDateTime->format('Y-m-d').'_'.($objDateTime->format('H')-6).'-'.$objDateTime->format('i').'.'.$ext_file;
	}
	else if($typeFile==2)	
	{
		$target_file = $target_dir .'Upload_for_Employee_payroll_'.$objDateTime->format('Y-m-d').'_'.($objDateTime->format('H')-6).'-'.$objDateTime->format('i').'.'.$ext_file;
	}
	
	if ($name_file && move_uploaded_file($_FILES[$sourceFile]['tmp_name'], $target_file)) 
	{
		$rst = $rqLoader->loadDailySales($target_file,$typeFile);
		##Borro los archivos procesados
		chmod($target_file, 0666);
		unlink($target_file);
		//print_r($rst);
		return $rst;  
	} 
	else 
	{
		return $rst; 		
	}
	
}
?>