<?php
/**********************************************/
/**********************************************/
/***        OPERATIONS VIEW REPORT VR       ***/
/**********************************************/
/***    web.developer@ifixandrepair.com     ***/
/**********************************************/
/**********************************************/
require_once 'PHPExcel/Classes/PHPExcel/IOFactory.php';
require_once("DataBase.php");

class RQSalesLoader{
 
    const COLHEAD_EMPLOYEE      = "Employee";  
    const COLHEAD_LOCATION      = "Location";
    const COLHEAD_GROSSPROFIT   = "Gross Profit"; 

    const ROWHEAD_INDEX         = 3;  

    var $arrSales = array();
    var $arrOvers = array(); 
    var $arrPush = array(); 

    private $colHeaders = array();
    private $boxNameSeparators = array();

    public function __construct(){
        $this->colHeaders = array(
            self::COLHEAD_EMPLOYEE => 0,
            self::COLHEAD_LOCATION => 0,
            self::COLHEAD_GROSSPROFIT => 0
        );
    }
    
    /*************************************************************************************/
    /*************************************************************************************/
    /*** function:    loadDailySales                                                   ***/
    /*** Description: Funcion que procesa los 3 archivos subidos en Upload Sale Tab    ***/
    /*** In Params:   $filePath:    Ruta del archivo.                                  ***/
    /***              $typeFile:   Tipo de archivo 1.Store Sales 2.Employee Sales      ***/
    /*** Return:      Arreglo                                                          ***/
    /*************************************************************************************/
    /*************************************************************************************/
    public function loadDailySales($filePath,$typeFile)
    {

        list($sheet, $highRow, $highCol) = $this->loadSheetAndLimits($filePath);
        
        $this->readHeaders($sheet, self::ROWHEAD_INDEX, $highCol);
        $query = "";
        $delqry = "";

        if($typeFile==1)
        {
            $Titulo = $sheet->getCellByColumnAndRow('A',1)->getValue();
            $fecha = explode("to ",$Titulo);
            if(!isset($fecha[1]))
            {
                return false;
            }
            $fecha = explode(" for",$fecha[1]);
            if(!isset($fecha[1]))
            {
                return false;
            }
            $fecha = $fecha[0];
        }
        if($typeFile==2)
        {
            $Titulo = $sheet->getCellByColumnAndRow('A',1)->getValue();
            $fecha = explode("To ",$Titulo);            
            if(!isset($fecha[1]))
            {
                return false;
            }
            $fecha = $fecha[1];
        }
        for($row = self::ROWHEAD_INDEX+1; $row <= $highRow; ++$row) 
        {            
            if($rowData = $this->readRowData($sheet, $row, $typeFile))
            {
                if($typeFile==1)
                {
                    list($Location, $GrossProfit) =  $rowData;
                    if($Location!='')
                    {
                        $nombre = explode(" - ",$Location);
                        $arrSale['Location_id']     = $nombre[1]; 
                        $arrSale['Location_name']   = $nombre[0];  
                        $arrSale['Date']            = date("Y-m-d", strtotime($fecha));   
                        $arrSale['GrossProfit']     = $GrossProfit;
                        $this->arrSales[]=$arrSale;
                    }
                }
                if($typeFile==2)
                {
                    list($Employee, $GrossProfit) =  $rowData;
                    if($Employee!='')
                    {
                        $nombre = explode(" - ",$Employee);
                        if(isset($nombre[1]))
                        {
                            $arrSale['Employee_id']     = $nombre[1]; 
                            $arrSale['Employee_name']   = $nombre[0]; 
                            $arrSale['Date']            = date("Y-m-d", strtotime($fecha));          
                            $arrSale['GrossProfit']     = $GrossProfit; 
                            
                            $this->arrSales[]=$arrSale;            
                        }
                    }
                }                
            }     
        }
        return $this->arrSales;
    }

    /*************************************************************************************/
    /*************************************************************************************/
    /*** function:    loadSheetAndLimits                                               ***/
    /*** Description: Funcion que crea el reader de excel y obtiene la hoja activa     ***/
    /***              maxima celda y maxima columna                                    ***/
    /*** In Params:   $filePath:    Ruta del archivo.                                  ***/
    /*** Return:      Arreglo                                                          ***/
    /*************************************************************************************/
    /*************************************************************************************/
    private function loadSheetAndLimits($filePath){
        $typeFile   = PHPExcel_IOFactory::identify($filePath);
        $reader     = PHPExcel_IOFactory::createReader($typeFile);        
        $reader->setReadDataOnly(TRUE);
        $PHPExcel   = $reader->load($filePath);
        $sheet      = $PHPExcel->getActiveSheet();
        $highRow    = $sheet->getHighestRow();
        $highCol    = $sheet->getHighestColumn();
        return array($sheet, $highRow, PHPExcel_Cell::columnIndexFromString($highCol));
    }

    /*************************************************************************************/
    /*************************************************************************************/
    /*** function:    readHeaders                                                      ***/
    /*** Description: Funcion que lee las celdas de cabecera                           ***/
    /***              maxima celda y maxima columna                                    ***/
    /*** In Params:   $sheet:   Hoja del excel a procesar.                             ***/
    /***              $row:     Fila en la que se va a correr                          ***/
    /***              $limit:   Limite el cual se debe correr por la hoja              ***/
    /*** Return:      Arreglo                                                          ***/
    /*************************************************************************************/
    /*************************************************************************************/
    private function readHeaders($sheet, $row, $limit){
        for($col = 0; $col <= $limit; ++$col) {
            $colHeader = $sheet->getCellByColumnAndRow($col, $row)->getValue();
            if(array_key_exists($colHeader, $this->colHeaders))
                 $this->colHeaders[$colHeader] = $col;
        }
    }

    /*************************************************************************************/
    /*************************************************************************************/
    /*** function:    readRowData                                                      ***/
    /*** Description: Funcion que lee las celdas de cabecera                           ***/
    /***              maxima celda y maxima columna                                    ***/
    /*** In Params:   $sheet:       Hoja del excel a procesar.                         ***/
    /***              $row:         Fila en la que se va a correr                      ***/
    /***              $typeFile:    Tipo de archivo  1.Store Sales 2.Employee Sales    ***/
    /*** Return:      Arreglo                                                          ***/
    /*************************************************************************************/
    /*************************************************************************************/
    private function readRowData($sheet, $row, $typeFile)
    {
        ## Lee Store Sales
        if($typeFile==1)
        {
            $Location = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_LOCATION],
                $row)->getValue();  

            $GrossProfit = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_GROSSPROFIT],
                $row)->getValue();

            return array($Location, $GrossProfit);
        }
        ## Lee Employee Sales
        if($typeFile==2)
        {

            $Employee = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_EMPLOYEE],
                $row)->getValue();
            
            $GrossProfit = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_GROSSPROFIT],
                $row)->getValue();
            
            return array($Employee, $GrossProfit);
        }
       
    }

    /*************************************************************************************/
    /*************************************************************************************/
    /*** function:    GuardarVentasProcesadas                                          ***/
    /*** Description: Funcion encargada guardar las ventas diarias de cada tienda      ***/
    /*** In Params:   $arrVentas:   Arreglo de las ventas del dia                      ***/
    /***              $typeFile:    Tipo de archivo  1.Store Sales 2.Employee Sales    ***/
    /*** Return:      Booleano                                                         ***/
    /*************************************************************************************/
    /*************************************************************************************/
    public function GuardarVentasProcesadas($arrVentas,$typeFile)
    {
        //print_r($arrVentas);
        if($arrVentas!=false)
        {
            $query      ='';
            if($typeFile==1)
            {
                foreach ($arrVentas as $venta) 
                {
                    ## Selecciono Tienda por Tienda para proceder con el insert                
                    if($query!='')
                    {
                        $query.= ",";
                    }            
                    $query.= "( ".$venta['Location_id'].",'".$venta['Date']."',".$venta['GrossProfit'].")";
                }
                //$query  = 'INSERT INTO employee_profit(employee_id, date, profit) VALUES '.$query;
                $query  = 'INSERT INTO store_profit(store_id, date, profit) VALUES '.$query;
            }
            if($typeFile==2)
            {
                foreach ($arrVentas as $venta) 
                {
                    ## Selecciono Tienda por Tienda para proceder con el insert                
                    if($query!='')
                    {
                        $query.= ",";
                    }            
                    $query.= "( ".$venta['Employee_id'].",'".$venta['Date']."',".$venta['GrossProfit'].")";
                }
                //$query  = 'INSERT INTO employee_profit(employee_id, date, profit) VALUES '.$query;
                $query  = 'INSERT INTO employee_profit(employee_id, date, profit) VALUES '.$query;
            }
            //echo $query;
            $dbh    = DataBase::getDbh();
            $rst    = $dbh->query($query);
            if($rst)
            {
                return true;
            }
        }
        return false;
    }
    
    /*************************************************************************************/
    /*************************************************************************************/
    /*** function:    GuardarPayroll                                          ***/
    /*** Description: Funcion encargada guardar las ventas diarias de cada tienda      ***/
    /*** In Params:   $arrVentas:   Arreglo de las ventas del dia                      ***/
    /***              $date_sales:  Fecha del dia al cual pertenecen las ventas        ***/
    /*** Return:      Booleano                                                         ***/
    /*************************************************************************************/
    /*************************************************************************************/
    private function GuardarPayroll($date_sales, $Location, $Employee, $RegularTime, $OverTime)
    {
        $cont=0;
        $query='';
        $date_sales = date('Y-m-d',strtotime($date_sales));
        
        $query = "INSERT INTO sales_payroll(date_sale, Store, Employee, RegularTime, OverTime) VALUES ( '".$date_sales."','".$Location."','".$Employee."','".$RegularTime."','".$OverTime."')";
        //echo 'QUERY: '.$query;
        $dbh = DataBase::getDbh();
        $rst = $dbh->query($query);
        if($rst)
        {
            return true;
        }
        return false;
    }
}
?>