<?php
require_once("DataBase.php");

class RetailInvoice{

    const SQL_SEL_BY_ID = "SELECT * FROM retail_invoices WHERE retail_invoice_id=? LIMIT 1";
    const SQL_UPDATE = "UPDATE retail_invoices SET rq_id=?, store_id=?, sold_datetime=? WHERE retail_invoice_id=?";
    const SQL_INSERT = "INSERT INTO retail_invoices(rq_id, store_id, sold_datetime) VALUES(?,?,?)";
    const SQL_SEL_PRODUCT="SELECT * FROM retail_invoices_products WHERE retail_invoice_id=? AND retail_product_id=? LIMIT 1";
    const SQL_INS_PRODUCT="INSERT INTO retail_invoices_products(retail_invoice_id, retail_product_id, quantity) VALUES(?,?,?)";
    const SQL_UPD_PRODUCT="UPDATE retail_invoices_products SET quantity=?, WHERE retail_invoice_id=? AND retail_product_id=?";

    private $retailInvoiceId;
    private $rqId;
    private $storeId;
    private $invoiceDate;

    private static $insSth = null;
    private static $selSth = null;
    private static $updSth = null;

    public function __construct(){
		$this->dbh = DataBase::getDbh();
	}

    public function createProduct($productId, $qty){
        try {
			$insSth = $this->dbh->prepare(self::SQL_INS_PRODUCT);			
			$insSth->execute(array(
                $this->retailInvoiceId,
                $productId,
                $qty 
                ));
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }

    public function saveProduct($productId, $qty){
        try {
			$selSth = $this->dbh->prepare(self::SQL_SEL_PRODUCT);            		
			$selSth->execute(array(
                $this->retailInvoiceId,
                $productId                
                ));
            if($row=$selSth->fetch())
                $this->updateProduct($productId, $qty);
            else
                $this->createProduct($productId, $qty);
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }

    public function updateProduct($productId, $qty){
        try {
			$updSth = $this->dbh->prepare(self::SQL_UPD_PRODUCT);			
			$updSth->execute(array(
                $qty,
                $this->retailInvoiceId,
                $productId                 
                ));
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }

    public function setRetailInvoiceId($value){
        $this->retailInvoiceId=$value;
    }
    
    public function setRqId($value){
        $this->rqId=$value;
    }

    public function setStoreId($value){
        $this->storeId=$value;
    }

    public function setInvoiceDate($value){
        $this->invoiceDate=$value;
    }

    public function save(){
        try {
			if(self::$selSth == null)
                self::$selSth = $this->dbh->prepare(self::SQL_SEL_BY_ID);			
			self::$selSth->execute(array($this->retailInvoiceId));
            if($row = self::$selSth->fetch())
                $this->update();
            else
                $this->create();
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }

    public function update(){
        try {
			if(self::$updSth == null)
                self::$updSth = $this->dbh->prepare(self::SQL_UPDATE);			
			self::$updSth->execute(array(
                $this->rqId,
                $this->storeId,
                date("Y-m-d H:i:s", $this->invoiceDate),
                $this->retailInvoiceId
                ));
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }

    public function create(){
        try {
			$insSth = $this->dbh->prepare(self::SQL_INSERT);			
			$insSth->execute(array(
                $this->rqId,
                $this->storeId,
                date("Y-m-d H:i:s", $this->invoiceDate)
                ));
            $newId = $this->dbh->lastInsertId();
            $this->retailInvoiceId =$newId;
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }
}
?>