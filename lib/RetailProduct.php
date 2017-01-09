<?php
require_once("DataBase.php");

class RetailProduct{

    const SQL_SEL_BY_SKU = "SELECT * FROM retail_products WHERE sku=? LIMIT 1";
    const SQL_SELECT = "SELECT * FROM retail_products WHERE retail_product_id=? LIMIT 1";
    const SQL_UPDATE = "UPDATE retail_products SET sku=?, name=?, box_name_id=? WHERE retail_product_id=?";
    const SQL_CREATE = "INSERT INTO retail_products(sku, name, box_name_id) VALUES(?,?,?)";

    private $retailProductId;
    private $sku;
    private $name;
    private $boxNameId;

    public function __construct(){
		$this->dbh = DataBase::getDbh();
	}

    public function getRetailProductId(){
        return $this->retailProductId;
    }

    public function setRetailProductId($value){
        $this->retailProductId=$value;
    }

    public function setSku($value){
        $this->sku=$value;
    }

    public function setName($value){
        $this->name=$value;
    }

    public function setBoxNameId($value){
        $this->boxNameId=$value;
    }
    
    public static function findBySku($sku){
		$product = null;
		$dbh = DataBase::getDbh();
		try {
			$selSth = $dbh->prepare(self::SQL_SEL_BY_SKU);			
			$selSth->execute(array($sku));
			if($row = $selSth->fetch()){
				$product = new RetailProduct();
				$product->setRetailProductId($row["retail_product_id"]);
                $product->setSku($row["sku"]);
                $product->setName($row["name"]);
				$product->setBoxNameId($row["box_name_id"]);
			}
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		return $product;
	}

    public function create(){
        try {
			$selSth = $this->dbh->prepare(self::SQL_CREATE);			
			$selSth->execute(array(
                $this->sku,
                $this->name,
                $this->boxNameId                
                ));
            $this->retailProductId = $this->dbh->lastInsertId();
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }
    
    public function save(){
        try {
			$selSth = $this->dbh->prepare(self::SQL_SELECT);			
			$selSth->execute(array($this->retailProductId));
            if($row = $selSth->fetch())
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
			$updSth = $this->dbh->prepare(self::SQL_UPDATE);			
			$updSth->execute(array(
                $this->sku,
                $this->name,
                $this->boxNameId,
                $this->retailProductId
                ));
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }
}
?>