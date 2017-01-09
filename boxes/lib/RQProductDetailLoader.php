<?php
require_once 'PHPExcel/Classes/PHPExcel/IOFactory.php';
require_once("DataBase.php");

class RQProductDetailLoader{

    const COLHEAD_INVOICE = "Invoice #";
    const COLHEAD_STORE = "Invoiced By";
    const COLHEAD_INVDATE = "Sold On";    
    const COLHEAD_SKU = "Product SKU";	
    const COLHEAD_PRODUCT = "Product Name";
    const COLHEAD_QTY = "Qty";

    const ROWHEAD_INDEX = 3;

    const BOXNAME_SEP_LCD = "LCD";
    const BOXNAME_SEP_DIGI = "Digitizer";

    private $colHeaders = array();
    private $boxNameSeparators = array();

    public function __construct(){
        $this->colHeaders = array(
            self::COLHEAD_INVOICE => 0,
            self::COLHEAD_STORE => 0,
            self::COLHEAD_INVDATE => 0,            
            self::COLHEAD_SKU => 0,	
            self::COLHEAD_PRODUCT => 0,
            self::COLHEAD_QTY => 0
        );

        $this->boxNameSeparators=array(
            self::BOXNAME_SEP_LCD,
            self::BOXNAME_SEP_DIGI
        );
    }
    

    public function loadProductDetail($filePath){
        list($sheet, $highRow, $highCol) 
            = $this->loadSheetAndLimits($filePath);
        
        $this->readHeaders($sheet, self::ROWHEAD_INDEX, $highCol);
        $query = "";
        $delqry = "";
        for($row = self::ROWHEAD_INDEX+1; $row <= $highRow; ++$row) {            
            if($rowData = $this->readRowData($sheet, $row)){
                if($query){
                    $query .= ",\n";
                    $delqry .= ", ";
                }
                list($invoiceId, $rqStoreId, $storeName, $invoiceDate, $productSku, 
                    $productName, $boxName, $qty) =  $rowData;
            $query .= "('$invoiceId', '$productSku', $qty, $rqStoreId, '$storeName', "
                ." FROM_UNIXTIME($invoiceDate), '$productName', '$boxName')\n";
            $delqry .= "'$invoiceId'";
            }     
        }
        $delqry = "DELETE FROM rq_product_detail WHERE invoice_id IN ($delqry)";
        $query = "INSERT INTO rq_product_detail VALUES " . $query;        
        $dbh = DataBase::getDbh();
        $dbh->query($delqry);
        $dbh->query($query);
    }

    private function loadSheetAndLimits($filePath){
        $reader = PHPExcel_IOFactory::createReader('Excel5');
        $reader->setReadDataOnly(TRUE);
        $PHPExcel = $reader->load($filePath);
        $sheet = $PHPExcel->getActiveSheet();
        $highRow = $sheet->getHighestRow();
        $highCol = $sheet->getHighestColumn();
        return array($sheet, $highRow, PHPExcel_Cell::columnIndexFromString($highCol));
    }

    private function readHeaders($sheet, $row, $limit){
        for($col = 0; $col <= $limit; ++$col) {
            $colHeader = $sheet->getCellByColumnAndRow($col, $row)->getValue();
            if(array_key_exists($colHeader, $this->colHeaders))
                 $this->colHeaders[$colHeader] = $col;
        }
    }

    private function readRowData($sheet, $row){
        $invoiceId = $sheet->getCellByColumnAndRow(
            $this->colHeaders[self::COLHEAD_INVOICE],
            $row)->getValue();
        
        if( !$storeData = $sheet->getCellByColumnAndRow(
            $this->colHeaders[self::COLHEAD_STORE],
            $row)->getValue())
            return false;
        list($storeName, $rqStoreId) = explode(" - ", $storeData);
        
        $soldOn = $sheet->getCellByColumnAndRow(
            $this->colHeaders[self::COLHEAD_INVDATE],
            $row)->getValue();
        $invoiceDate = PHPExcel_Shared_Date::ExcelToPHP($soldOn);

        $productSku = $sheet->getCellByColumnAndRow(
            $this->colHeaders[self::COLHEAD_SKU],
            $row)->getValue();
        
        $productName = $sheet->getCellByColumnAndRow(
            $this->colHeaders[self::COLHEAD_PRODUCT],
            $row)->getValue();
        
        $boxName = $this->boxName($productName);

        $qty = $sheet->getCellByColumnAndRow(
            $this->colHeaders[self::COLHEAD_QTY],
            $row)->getValue();
        
        return array($invoiceId, intval($rqStoreId), $storeName, 
            $invoiceDate, $productSku, $productName, $boxName, $qty);
    }

    private function boxName($productName){
        $minSepPos = strlen($productName);
        foreach ($this->boxNameSeparators as $sep){
            $sepPos = strpos($productName, $sep);
            if( $sepPos !== false && $sepPos < $minSepPos)
                $minSepPos =$sepPos; 
        }
        return substr($productName, 0, $minSepPos-1);
    }
}
?>