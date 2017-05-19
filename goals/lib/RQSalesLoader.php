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

    const COLHEAD_LOCATION      = "Location";
    const COLHEAD_SALES         = "Sales";
    const COLHEAD_GROSSPROFIT   = "Gross Profit";    
    const COLHEAD_EMPLOYEE      = "Employee";  
    const COLHEAD_OVERTIME      = "Overtime Clocked";  
    const COLHEAD_REGULARTIME   = "Total Clocked";    
    const COLHEAD_FIRSTNAME     = "First Name";    
    const COLHEAD_LASTNAME      = "Last Name"; 
    const COLHEAD_MANAGER       = "Manager";   
    const COLHEAD_SGHOURS       = "Hours";  
    const COLHEAD_SGSALARY      = "Salary";  
    const COLHEAD_SGMANAGER     = "Manager";  
    const COLHEAD_SGMGRHOURS    = "Manager N";    
    const COLHEAD_SGEMPHOURS    = "Employees";  
    const ROWHEAD_INDEX         = 3;  

    var $arrSales = array();
    var $arrOvers = array(); 
    var $arrPush = array(); 

    private $colHeaders = array();
    private $boxNameSeparators = array();

    public function __construct(){
        $this->colHeaders = array(
            self::COLHEAD_LOCATION => 0,
            self::COLHEAD_SALES => 0,
            self::COLHEAD_GROSSPROFIT => 0, 
            self::COLHEAD_EMPLOYEE => 0, 
            self::COLHEAD_OVERTIME => 0, 
            self::COLHEAD_REGULARTIME => 0,  
            self::COLHEAD_FIRSTNAME => 0,  
            self::COLHEAD_LASTNAME => 0,  
            self::COLHEAD_MANAGER => 1,  
            self::COLHEAD_SGHOURS => 3,  
            self::COLHEAD_SGSALARY => 2,  
            self::COLHEAD_SGMANAGER => 1,  
            self::COLHEAD_SGMGRHOURS => 4,    
            self::COLHEAD_SGEMPHOURS => 5,      
        );
    }
    
    /*************************************************************************************/
    /*************************************************************************************/
    /*** function:    loadDailySales                                                   ***/
    /*** Description: Funcion que procesa los 3 archivos subidos en Upload Sale Tab    ***/
    /*** In Params:   $filePath:    Ruta del archivo.                                  ***/
    /***              $typeFile:    Tipo de archivo 1.Sales 2.Payroll 3.Autopunch      ***/
    /***                            4.Accesories                                       ***/
    /***              $date_sales:  Fecha del dia al cual pertenecen las ventas        ***/
    /*** Return:      Arreglo                                                          ***/
    /*************************************************************************************/
    /*************************************************************************************/
    public function loadDailySales($filePath,$typeFile,$date_sales)
    {
        
        list($sheet, $highRow, $highCol) = $this->loadSheetAndLimits($filePath);
        
        $this->readHeaders($sheet, self::ROWHEAD_INDEX, $highCol);
        $query = "";
        $delqry = "";
        for($row = self::ROWHEAD_INDEX+1; $row <= $highRow; ++$row) 
        {            
            if($rowData = $this->readRowData($sheet, $row, $typeFile))
            {
                if($typeFile==1)
                {
                    list($Location, $Sales,$GrossProfit) =  $rowData;
                    if($Location!='')
                    {
                        $arrSale['Location']    = $Location;        
                        $arrSale['Sales']       = $Sales;       
                        $arrSale['Accesories']  = 0;        
                        $arrSale['GrossProfit'] = $GrossProfit;        
                        $arrSale['Hours']       = '';          
                        $arrSale['AP']          = 0;        
                        $arrSale['Employee_AP'] = '';  
                        $this->arrSales[$Location]=$arrSale;
                    }
                }
                if($typeFile==2)
                {
                    list($Location, $RegularTime, $OverTime, $Employee) =  $rowData;
                    if($Location!='')
                    {
                        $arrSale['Location']    = $Location;  
                        if(isset ( $this->arrSales[$Location] ) )
                        {
                            $arrSale['RegularTime'] = $arrSale['RegularTime'] + $RegularTime;                           
                        } 
                        else
                        {       
                            $arrSale['RegularTime'] = $RegularTime;
                        }                        
                        $this->arrSales[$Location]=$arrSale;            
                    }
                    if($Location!="")
                    {
                        $this->GuardarPayroll($date_sales, $Location, $Employee, $RegularTime, $OverTime);
                    }
                }
                if($typeFile==3)
                {
                    list($Location, $Name) =  $rowData;
                    if($Location!='')
                    { 
                        $arrSale['Location']    = $Location;    
                        if(isset ( $this->arrSales[$Location] ) )
                        {
                            $arrSale['Name'] = $this->arrSales[$Location] ['Name'] .', '. $Name;                               
                            $arrSale['Cont'] = $this->arrSales[$Location] ['Cont']+1;                          
                        } 
                        else
                        {       
                            $arrSale['Name'] = $Name;
                            $arrSale['Cont'] = 1;                            
                        }             
                        $this->arrSales[$Location]=$arrSale;                   
                    }
                }
                if($typeFile==4)
                {
                    list($Location, $Accesories) =  $rowData;
                    if($Location!='')
                    { 
                        $arrSale['Location']    = $Location;    
                        $arrSale['Accesories']  = $Accesories; 
                        $this->arrSales[$Location]=$arrSale;                        
                    }
                }
            }     
        }
        return $this->arrSales;
    }

    /*************************************************************************************/
    /*************************************************************************************/
    /*** function:    loadStoreGoals                                                   ***/
    /*** Description: Funcion que guarda los store goals en la base de datos           ***/
    /*** In Params:   $filePath:    Ruta del archivo.                                  ***/
    /***              $Month:       Mes para procesar                                  ***/
    /*** Return:      Booleano                                                         ***/
    /*************************************************************************************/
    /*************************************************************************************/
    public function loadStoreGoals($filePath,$Month)
    {
        
        list($sheet, $highRow, $highCol) = $this->loadSheetAndLimits($filePath);
        ## TypeFile es el tipo de proceso que se le debe dar al archivo para la funcion readRowData()
        $typeFile = 99;
        $this->readHeaders($sheet, self::ROWHEAD_INDEX, $highCol);
        $query  = "";
        $year   = date("Y");     
        for($row = self::ROWHEAD_INDEX+1; $row <= $highRow; ++$row) 
        {            
            if($rowData = $this->readRowData($sheet, $row, $typeFile))
            {
                list($Location, $Manager, $Salary, $Budget, $MgrHours, $EmpHours) =  $rowData;
                if ($query!='') {
                   $query.= ",";
                }
                $query.= "(".$Month.",".$year.",'".$Location."','".$Manager."','".$Salary."','".$Budget."','".$MgrHours."','".$EmpHours."')";                
            }     
        }

        $query  = "INSERT INTO sales_store_goals (Month, Year, Store, Manager, Salary, Hours, Hrs_mgr, Hrs_emp) VALUES ".$query;
        $dbh    = DataBase::getDbh();
        $rst    = $dbh->query($query);
        if($rst)
        {
            return true;
        }
        return false;
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
    /***              $typeFile:    Tipo de archivo 1.Sales 2.Payroll 3.Autopunch      ***/
    /***                            4.StoreGoals                                       ***/
    /*** Return:      Arreglo                                                          ***/
    /*************************************************************************************/
    /*************************************************************************************/
    private function readRowData($sheet, $row, $typeFile)
    {
        ## Lee Daily Sales
        if($typeFile==1)
        {
            $Location = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_LOCATION],
                $row)->getValue();
            
            $Sales = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_SALES],
                $row)->getValue();
            
            $GrossProfit = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_GROSSPROFIT],
                $row)->getValue();
            return array($Location, $Sales,$GrossProfit);
        }
        ## Lee Payroll
        if($typeFile==2)
        {
            $Location = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_LOCATION],
                $row)->getValue();

            $Employee = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_EMPLOYEE],
                $row)->getValue();
            
            $RegularTime = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_REGULARTIME],
                $row)->getValue();
            
            $OverTime = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_OVERTIME],
                $row)->getValue();
            return array($Location, $RegularTime, $OverTime, $Employee);
        }
        ## Lee autopunch
        if($typeFile==3)
        {
            $Location = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_LOCATION],
                $row)->getValue();
            
            $first_name = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_FIRSTNAME],
                $row)->getValue();

            $last_name = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_LASTNAME],
                $row)->getValue();

            return array($Location, $first_name.' '.$last_name);
        } 
        ## Lee Accesories
        if($typeFile==4)
        {
            $Location = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_LOCATION],
                $row)->getValue();
            
            $Accesories = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_SALES],
                $row)->getValue();

            return array($Location, $Accesories);
        }
        ## Lee Store Goals
        if($typeFile==99)
        {
            $Location = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_LOCATION],
                $row)->getValue();
            
            $Manager = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_SGMANAGER],
                $row)->getValue();

            $Salary = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_SGSALARY],
                $row)->getValue();

            $Budget = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_SGHOURS],
                $row)->getValue();

            $MgrHours = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_SGMGRHOURS],
                $row)->getValue();

            $EmpHours = $sheet->getCellByColumnAndRow(
                $this->colHeaders[self::COLHEAD_SGEMPHOURS],
                $row)->getValue();

            if (isset($Location) && $Location!='Location')
            {
                /*## Como en el excel de Store goals no vienen en columnas separadas y el formato similar que traen es "MANAGER 80 EMPLOYEE 50"
                ## Opte simplemente por explotar espacios y obtener los valores de las posiciones 1 y 3, en caso de que se tome un valor
                ## Erroneo se puede editar unicamente el registro mediante la tabla que aparece en Store Goals en caso de que se halla
                ## Subido el archivo del mes actual
                $MgrEmpHours = explode(' ', $MgrEmpHours);*/
                //echo '<br>Location: '.$Location.'<br>Manager: '.$Manager.'<br>Salary: '.$Salary.'<br>Budget: '.$Budget.'<br>MgrHours: '.$MgrHours.'<br>EmpHours: '.$EmpHours;
                return array($Location, $Manager, $Salary, $Budget, $MgrHours, $EmpHours);
            }
            
        }
    }

    /*************************************************************************************/
    /*************************************************************************************/
    /*** function:    GuardarVentasProcesadas                                          ***/
    /*** Description: Funcion encargada guardar las ventas diarias de cada tienda      ***/
    /*** In Params:   $arrVentas:   Arreglo de las ventas del dia                      ***/
    /***              $date_sales:  Fecha del dia al cual pertenecen las ventas        ***/
    /*** Return:      Booleano                                                         ***/
    /*************************************************************************************/
    /*************************************************************************************/
    public function GuardarVentasProcesadas($arrVentas,$date_sales)
    {
        $query      ='';
        $date_sales = date('Y-m-d',strtotime($date_sales));
        foreach ($arrVentas as $venta) 
        {
            ## Selecciono Tienda por Tienda para proceder con el insert
            $tienda = $venta['Location'];
            list($Location,$Sales,$Accesories,$GrossProfit,$Hours,$AP,$Employee_AP) = array_values($arrVentas[$tienda]);
            if($query!='')
            {
                $query.= ",";
            }            
            $query.= "( '".$date_sales."','".$Location."','".$Sales."','".$Accesories."','".$GrossProfit."','".$Hours."',".$AP.",'".$Employee_AP."')";
        }
        $query  = 'INSERT INTO sales(date_sale, Store, Sales, Accesories, GrossProfit, Hours, AP, Employee_AP) VALUES '.$query;
        //echo $query;
        $dbh    = DataBase::getDbh();
        $rst    = $dbh->query($query);
        if($rst)
        {
            return true;
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