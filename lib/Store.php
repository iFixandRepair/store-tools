<?php
require_once("DataBase.php");

class Store{
    
	const SQL_SEL_SIMILAR_LOCATION = "SELECT * FROM stores WHERE MATCH (location) AGAINST (? IN NATURAL LANGUAGE MODE)";
	const SQL_UPDATE = "UPDATE stores SET email=?, location=?, manager=?, rq_id=? WHERE store_id=?";
	const SQL_SEL_STORE_BY_EMAIL = "SELECT * FROM stores WHERE email = ?";
	const SQL_SEL_STORE_BY_ID = "SELECT * FROM stores WHERE store_id = ?";

	private $storeId;
	private $email;
	private $location;
	private $manager;
	private $rqId;

    public function __construct(){
		$this->dbh = DataBase::getDbh();
	}

	public function getStoreId(){
		return $this->storeId;
	}

	public function getLocation(){
		return $this->location;
	}

	public function setStoreId($value){
		$this->storeId=$value;
	}

	public function setEmail($value){
		$this->email=$value;
	}

	public function setLocation($value){
		$this->location=$value;
	}

	public function setManager($value){
		$this->manager=$value;
	}

	public function setRqId($value){
		$this->rqId=$value;
	}

	public static function getRqIdsIndex(){
		$index = array();
		$dbh = DataBase::getDbh();
		try {
			$selSth = $dbh->query(self::SQL_SEL_ALL_RQID);			
			while($row = $selSth->fetch()){
				if($row["rq_id"] != null)
					$index[$row["rq_id"]] = $row["store_id"];
			}
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		return $index;
	}

	public static function findFromRQname($storeName){
		$store = null;
		$dbh = DataBase::getDbh();
		try {
			$selSth = $dbh->prepare(self::SQL_SEL_SIMILAR_LOCATION);			
			$selSth->execute(array($storeName));
			if($row = $selSth->fetch())
				$store = self::fillStoreFromResultRow($row);
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		return $store;
	}

	public function update(){
        try {
			$updSth = $this->dbh->prepare(self::SQL_UPDATE);			
			$updSth->execute(array(
                $this->email,
                $this->location,
				$this->manager,
				$this->rqId,
				$this->storeId
                ));
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }

	public static function getStoreByEmail($email){
		$store = null;
		$dbh = DataBase::getDbh();
		try {
			$selSth = $dbh->prepare(self::SQL_SEL_STORE_BY_EMAIL);
			$selSth->execute(array($email));
			if ($row = $selSth->fetch())
				$store = self::fillStoreFromResultRow($row);
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		return $store;
	}

	public static function getStoreById($storeId){
		$store = null;
		$dbh = DataBase::getDbh();
		try {
			$selSth = $dbh->prepare(self::SQL_SEL_STORE_BY_ID);
			$selSth->execute(array($storeId));
			if ($row = $selSth->fetch())
				$store = self::fillStoreFromResultRow($row);
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
		return $store;
	}

	private static function fillStoreFromResultRow($row){
		$store = new Store();
		$store->setStoreId($row["store_id"]);
		$store->setEmail($row["email"]);
		$store->setLocation($row["location"]);
		$store->setManager($row["manager"]);
		return $store;
	}
}

?>