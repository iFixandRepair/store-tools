<?php
require_once "RQProductDetail.php";
require_once "Store.php";

class StoreBox{


    const SQL_SEL_OPEN_BOX_BY_STORE = "SELECT * FROM store_boxes WHERE store_id = ? AND ship_date IS NULL";
    const SQL_SEL_BOX_CONTENT = "SELECT * FROM store_box_content WHERE box_id = ?";
    const SQL_SEL_BOXES_BY_STORE = "SELECT * FROM store_boxes b INNER JOIN `store_box_content`c ON b.box_id = c.box_id WHERE b.store_id=? AND b.ship_date IS NOT NULL AND b.tracking_number IS NOT NULL";
    const SQL_SEL_ALL_SENT_BOXES = "SELECT * FROM store_boxes b INNER JOIN `store_box_content`c ON b.box_id = c.box_id INNER JOIN stores s ON s.store_id = b.store_id WHERE b.ship_date IS NOT NULL AND b.tracking_number IS NOT NULL";
    const SQL_INS_BOX = "INSERT INTO store_boxes(store_id) VALUES(?)";
    const SQL_UPDATE_SHIP = "UPDATE store_boxes SET ship_date=FROM_UNIXTIME(?), ship_method=?, tracking_number=? WHERE box_id=?";
    const SQL_UPDATE_TRACK = "UPDATE store_boxes SET tracking_number=? WHERE box_id=?";
    const SQL_INS_BOX_CONTENT = "INSERT INTO store_box_content(box_id, box_name, ?) VALUES(?, ?, ?)";
    const SQL_UPD_BOX_CONTENT = "UPDATE store_box_content SET ? = ? WHERE box_id = ? AND box_name = ?";
    const SQL_DEL_BOX_CONTENT = "DELETE FROM store_box_content WHERE box_id = ? AND box_name = ?";
    const SQL_SEL_PREV_BOX = "SELECT ship_date FROM store_boxes WHERE store_id=? AND ship_date < FROM_UNIXTIME(?) ORDER BY ship_date DESC LIMIT 1";
    const SQL_SEL_RECENT_BOXES = 
       "SELECT * FROM store_boxes s 
        INNER JOIN (
            SELECT store_id, MAX(ship_date) as max_date 
            FROM `store_boxes`
            WHERE ship_date > FROM_UNIXTIME(?)
<<<<<<< HEAD
<<<<<<< HEAD
=======
            AND tracking_number IS NOT NULL
>>>>>>> origin/master
=======
            AND tracking_number IS NOT NULL
>>>>>>> origin/master
            GROUP BY store_id 
        )mx ON s.store_id = mx.store_id AND s.ship_date = mx.max_date";
    const SQL_SEL_ALL_OPEN_BOXES = 
       "SELECT * 
        FROM store_boxes b
        INNER JOIN  `store_box_content` c ON b.box_id = c.box_id
        INNER JOIN stores s ON s.store_id = b.store_id
        WHERE b.ship_date IS NULL 
        AND (
        recycles_snt + rma_snt + doa_snt + tecdam_snt
        ) <>0";

    private $boxId;
    private $storeId;
    private $shipDate;
    private $shipMethod;
    private $trackingNumber;
    private $boxContent = array();

    public function create(){
        $dbh = DataBase::getDbh();
        try {
			$insSth = $dbh->prepare(self::SQL_INS_BOX);
			$insSth->execute(array($this->storeId));
			$this->boxId = $dbh->lastInsertId();
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }

    private function createBoxContent($boxName, $field, $value){
        $dbh = DataBase::getDbh();
        try {
			$insSql = str_replace("box_name, ?", "box_name, $field", self::SQL_INS_BOX_CONTENT);
            $insSth = $dbh->prepare($insSql);
			$insSth->execute(array(
                $this->boxId,
                $boxName,
                $value
            ));
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }

    public function deleteBoxContent($boxName){
        $dbh = DataBase::getDbh();
        try {
            $delSth = $dbh->prepare(self::SQL_DEL_BOX_CONTENT);
			$delSth->execute(array(
                $this->boxId,
                $boxName
            ));
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }

    public static function getAllSentBoxes(){
        return self::buildBoxListFromQuery(self::SQL_SEL_ALL_SENT_BOXES);
    }

    public static function getAllOpenBoxes(){
        return self::buildBoxListFromQuery(self::SQL_SEL_ALL_OPEN_BOXES);
    }

    private static function buildBoxListFromQuery($query){
        $dbh = DataBase::getDbh();
        $boxes = array();
        try {
			$selSth = $dbh->query($query);			            
            while($row = $selSth->fetch()){
                $boxId = $row['box_id'];
                if(!isset($boxes[$boxId])){
                    $boxes[$boxId] = $row;
                    $boxes[$boxId]['box_content'] = array();
                }
                $boxes[$boxId]['box_content'][] = $row; ;
            }
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
        return $boxes;
    }

    public function getBoxId(){
        return $this->boxId;
    }

    public function getBoxContent(){
        return $this->boxContent;
    }

    public static function getBoxesByStoreId($storeId){
        $dbh = DataBase::getDbh();
        $boxes = array();
        try {
			$selSth = $dbh->prepare(self::SQL_SEL_BOXES_BY_STORE);			
			$selSth->execute(array($storeId));            
            while($row = $selSth->fetch()){
                $boxId = $row['box_id'];
                if(!isset($boxes[$boxId])){
                    $boxes[$boxId] = $row;
                    $boxes[$boxId]['box_content'] = array();
                }
                $boxes[$boxId]['box_content'][] = $row; ;
            }
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
        return $boxes;
    }

    public static function getCurrentOpenBox($storeId){
        $dbh = DataBase::getDbh();
        $box = null;
        try {
			$box = new StoreBox();
            do{ 
                $selSth = $dbh->prepare(self::SQL_SEL_OPEN_BOX_BY_STORE);			
			    $selSth->execute(array($storeId));
			    $row = $selSth->fetch();
                if(!$row){
                    $box->setStoreId($storeId);
                    $box->create();
                    $box->saveDefaultContent();
                }
            }while(!$row);       
            $box->setFromSQLRow($row);
            $box->loadBoxContent();
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
        return $box;
    }

    public function getPreviousBoxDate(){
        $dbh = DataBase::getDbh();
        $previousDate = null;
        try {
			$selSth = $dbh->prepare(self::SQL_SEL_PREV_BOX);			
			$selSth->execute(array($this->storeId, $this->shipDate));
			$row = $selSth->fetch();
            $previousDate = $row["ship_date"];
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
        return strtotime($previousDate);
    }

    public function loadBoxContent(){
        $dbh = DataBase::getDbh();
        try {
			$selSth = $dbh->prepare(self::SQL_SEL_BOX_CONTENT);			
			$selSth->execute(array($this->boxId));
			while($row = $selSth->fetch()){
                $this->boxContent[$row["box_name"]] = $row;
            }
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }

    public static function saveAllExpectedContent($limitDate){        
        $dbh = DataBase::getDbh();
        try {
			$selSth = $dbh->prepare(self::SQL_SEL_RECENT_BOXES);
			$selSth->execute(array($limitDate));
            while($row = $selSth->fetch()){
                $box = new StoreBox();
                $box->setFromSQLRow($row);
                $box->loadBoxContent();
                $box->saveExpectedContent();
            }
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }

    public function saveBoxContent($boxName, $field, $value){
        if(isset($this->boxContent[$boxName]))
            $this->updateBoxContent($boxName, $field, $value);
        else
            $this->createBoxContent($boxName, $field, $value);
    }

    private function saveDefaultContent(){
        $productDetail = new RQProductDetail();
        $store = Store::getStoreById($this->storeId);
        $rqId = $productDetail->getRqIdByLocation($store->getStoreId());
        $boxNames = $productDetail->getBoxNamesByRQId($rqId);
        for($i = 0; $i < 5; $i++){
            $this->saveBoxContent($boxNames[$i]["box_name"], "recycles_snt", 0);
        }
    }

    public function saveExpectedContent(){
        $store = Store::getStoreById($this->storeId);
        //$storeRqId = RQProductDetail::getRqIdByLocation($store->getLocation());
        $storeRqId = RQProductDetail::getRqIdByStoreId($store->getStoreId());
        $from = $this->getPreviousBoxDate();
        $recycles = RQProductDetail::getExpectedRcycles($storeRqId, $from, $this->shipDate);
        foreach($recycles as $recycle)
            $this->saveBoxContent($recycle["box_name"], "recycles_exp", $recycle["expected"]);
    }

    public function setBoxId($value){
        $this->boxId = $value;
    }

    public function setBoxContent($boxName, $qtys){
        $this->boxContent[$boxName] = $qtys;
    }

    protected function setFromSQLRow($row){
         $this->setBoxId($row["box_id"]);
         $this->setStoreId($row["store_id"]);
         $this->setShipDate(strtotime($row["ship_date"]));
         $this->setShipMethod($row["ship_method"]);
         $this->setTrackingNumber($row["tracking_number"]);
    }

    public function setStoreId($value){
        $this->storeId = $value;
    }

    public function setShipDate($value){
        $this->shipDate = $value;
    }

    public function setShipMethod($value){
        $this->shipMethod = $value;
    }

    public function setTrackingNumber($value){
        $this->trackingNumber = $value;
    }

    public function updateShippingInfo(){
        $dbh = DataBase::getDbh();
        try {			
            $updSth = $dbh->prepare(self::SQL_UPDATE_SHIP);
			$updSth->execute(array(
                $this->shipDate,
                $this->shipMethod,
                $this->trackingNumber,
                $this->boxId             
            ));
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}        
    }

    public function updateTrackingNumber(){
        $dbh = DataBase::getDbh();
        try {			
            $updSth = $dbh->prepare(self::SQL_UPDATE_TRACK);
			$updSth->execute(array(                
                $this->trackingNumber,
                $this->boxId             
            ));
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}        
    }

    private function updateBoxContent($boxName, $field, $value){
        $dbh = DataBase::getDbh();
        try {
			$updSql = str_replace("SET ? = ?", "SET $field = ?", self::SQL_UPD_BOX_CONTENT);
            $updSth = $dbh->prepare($updSql);
			$updSth->execute(array(
                $value,
                $this->boxId,
                $boxName                
            ));
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }
}
?>