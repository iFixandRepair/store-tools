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
	$date_sales = $_REQUEST['date-sales'];
	if($_FILES['salesReport']['name'] !='')
	{
		$arrSales = uploadFile('salesReport',1,$date_sales);
	}
	if($_FILES['accesoriesReport']['name'] !='')
	{
		$arrAccesories = uploadFile('accesoriesReport',4,$date_sales);
	}
	if($_FILES['locationPayroll']['name'] !='')
	{
		$arrPayroll = uploadFile('locationPayroll',2,$date_sales);
	}
	if($_FILES['AutoPunchOut']['name'] !='')
	{
		$arrPunch = uploadFile('AutoPunchOut',3,$date_sales);
	}
	foreach ($arrSales as $venta) 
	{
		## Selecciono cada tienda
		$tienda 		 			= $venta['Location'];
		## Valido si la tienda tiene ventas de Accesprops
		if(isset($arrAccesories[$tienda]))
		{			
			$Accesorios 			= $arrAccesories[$tienda]['Accesories'];
			#En caso de que hallan Valores Grabo los datos
			$arrSales[$tienda]['Accesories'] = $Accesorios; 
		} 

		## Valido si la tienda tiene referencias de Horas en el excel Location Payroll
		if(isset($arrPayroll[$tienda]))
		{			
			$horasTienda 			= $arrPayroll[$tienda]['RegularTime'];
			#En caso de que hallan referencias Grabo los datos
			$arrSales[$tienda]['Hours'] = $horasTienda; 
		} 
		## Valido si la tienda tiene referencias de Ponchado en el excel Auto Punch Out
		if(isset($arrPunch[$tienda]))
		{			
			$cantPonchados 			= $arrPunch[$tienda]['Cont'];
			$NomPonchados 			= $arrPunch[$tienda]['Name'];
			#En caso de que hallan referencias Grabo los datos
			$arrSales[$tienda]['AP'] = $cantPonchados; 
			$arrSales[$tienda]['Employee_AP'] = $NomPonchados; 
		}
	}

	$rqLoader= new RQSalesLoader();
	if($rqLoader->GuardarVentasProcesadas($arrSales,$date_sales))
	{
		echo '{"success":true}'; 
	}
	else
	{
		echo '{"success":false}'; 
	}
}

/*************************************************************************************/
/*************************************************************************************/
/*** function:    uploadFile                                                       ***/
/*** Description: Funcion encargada de subir los archivos al servidor el cual      ***/
/***              Dependiendo el archivo le coloca el nombre.        			   ***/
/***              Todos los archivos se guardan en la carpeta llamada "uploads"    ***/
/***              Que se encuentra en el root de este modulo 					   ***/
/*** In Params:   $sourceFile:	Archivo a subir (tipo $_FILE)                      ***/
/***              $typeFile:   	Tipo de archivo 1.Sales 2.Payroll 3.Autopunch      ***/
/***              $date_sales:  Fecha del dia al cual pertenecen las ventas        ***/
/*** Return:      Arreglo				                                           ***/
/*************************************************************************************/
/*************************************************************************************/
function uploadFile($sourceFile,$typeFile,$date_sales)
{
	$rqLoader 		= new RQSalesLoader();
	$name_file 		= $_FILES[$sourceFile]['name'];
	$ext_file 		= explode('.', $name_file);
	$lenght 		= count($ext_file)-1;
	$ext_file 		= $ext_file[$lenght];
	$target_dir 	= "../uploads/";
	$date_sales2	= str_replace('/', '-', $date_sales);
	$rst 			= ''; 
	if(!is_dir($target_dir)) 
	{
        mkdir($target_dir, 0777);
	}
	$objDateTime = new DateTime('NOW');
	if($typeFile==1)	
	{
		$target_file = $target_dir .'Upload_for_'.$date_sales2.'_sales_'.$objDateTime->format('Y-m-d').'_'.($objDateTime->format('H')-6).'-'.$objDateTime->format('i').'.'.$ext_file;
	}
	else if($typeFile==2)	
	{
		$target_file = $target_dir .'Upload_for_'.$date_sales2.'_payroll_'.$objDateTime->format('Y-m-d').'_'.($objDateTime->format('H')-6).'-'.$objDateTime->format('i').'.'.$ext_file;
	}
	else if($typeFile==3)	
	{
		$target_file = $target_dir .'Upload_for_'.$date_sales2.'_punch_'.$objDateTime->format('Y-m-d').'_'.($objDateTime->format('H')-6).'-'.$objDateTime->format('i').'.'.$ext_file;
	}
	else if($typeFile==4)	
	{
		$target_file = $target_dir .'Upload_for_'.$date_sales2.'_accesories_'.$objDateTime->format('Y-m-d').'_'.($objDateTime->format('H')-6).'-'.$objDateTime->format('i').'.'.$ext_file;
	}
	if ($name_file && move_uploaded_file($_FILES[$sourceFile]['tmp_name'], $target_file)) 
	{
		$rst = $rqLoader->loadDailySales($target_file,$typeFile,$date_sales);
		return $rst;  
	} 
	else 
	{
		return $rst; 		
	}
	
}
?>