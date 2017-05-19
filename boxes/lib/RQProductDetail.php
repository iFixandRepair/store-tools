<?php
class RQProductDetail{
    
    const SQL_SEL_BOXNAMES_BY_STORE = 
        "SELECT t.box_name, s.freq as store_freq, t.freq as total_freq
        FROM (SELECT box_name, COUNT(*) as freq FROM `rq_product_detail` GROUP BY box_name) t
        LEFT JOIN (SELECT box_name, COUNT(*) as freq FROM `rq_product_detail` WHERE store_id = ? GROUP BY box_name) s 
        ON t.box_name = s.box_name 
        ORDER BY store_freq DESC, total_freq DESC";

    const SQL_SEL_RQID_BY_NAME = 
        "SELECT store_id, store_name 
        FROM rq_product_detail 
        WHERE MATCH (store_name) AGAINST (? IN NATURAL LANGUAGE MODE) LIMIT 1";

    const SQL_SEL_RQID_BY_STORE = 
        "SELECT rq_store_id 
        FROM rq_stores 
        WHERE store_id = ?";

    const SQL_SEL_EXPECTED_BY_LAPSE = 
<<<<<<< HEAD
        "SELECT box_name, SUM(quantity) as expected FROM `rq_product_detail`
        WHERE store_id = ?
        AND invoice_date BETWEEN FROM_UNIXTIME(?) AND FROM_UNIXTIME(?)
        AND quantity > 0
=======
        "SELECT box_name, SUM(quantity) as expected FROM (
            SELECT invoice_id, box_name, SUM(quantity) as quantity 
            FROM `rq_product_detail`
            WHERE store_id = ?
            AND invoice_date BETWEEN FROM_UNIXTIME(?) AND FROM_UNIXTIME(?)
            GROUP BY invoice_id, box_name
            ) q
>>>>>>> origin/master
        GROUP BY box_name";
    
    const SQL_SEL_ALL_BOXNAMES = 
        "SELECT box_name, COUNT(*) as freq
        FROM `rq_product_detail`
        GROUP BY box_name
        ORDER BY `freq` DESC";

    public static function getAllBoxNames(){
        $dbh = DataBase::getDbh();
        $names = null;
        try {
			$selSth = $dbh->query(self::SQL_SEL_ALL_BOXNAMES);			
			$names = $selSth->fetchAll();
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
        return $names;
    }

    public static function getBoxNamesByRQId($rqId){
        $dbh = DataBase::getDbh();
        $names = null;
        try {
			$selSth = $dbh->prepare(self::SQL_SEL_BOXNAMES_BY_STORE);			
			$selSth->execute(array($rqId));
			$names = $selSth->fetchAll();
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
        return $names;
    }

    public static function getRqIdByLocation($location){
        $dbh = DataBase::getDbh();
        $id = false;
        try {
			$selSth = $dbh->prepare(self::SQL_SEL_RQID_BY_NAME);			
			$selSth->execute(array($location));
			if($row = $selSth->fetch())
                $id = $row["store_id"];
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
        return $id;
    }

    public static function getRqIdByStoreId($storeId){
        $dbh = DataBase::getDbh();
        $id = false;
        try {
			$selSth = $dbh->prepare(self::SQL_SEL_RQID_BY_STORE);			
			$selSth->execute(array($storeId));
			if($row = $selSth->fetch())
                $id = $row["rq_store_id"];
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
        return $id;
    }

    public static function getExpectedRcycles($storeRqId, $from, $to){
        $dbh = DataBase::getDbh();
        $expected = array();
        try {
			$selSth = $dbh->prepare(self::SQL_SEL_EXPECTED_BY_LAPSE);			
			$selSth->execute(array($storeRqId, $from, $to));
			$expected = $selSth->fetchAll();
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
        return $expected;
    }
}
?>