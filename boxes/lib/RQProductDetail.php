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
}
?>