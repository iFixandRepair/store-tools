<<<<<<< HEAD
<?php
require_once "config.php";
require_once "DataBase.php";

class nameCompare{

    const SQL_SEL_RQID_BY_NAME = 
        "SELECT store_id, store_name, 
            MATCH (
            store_name
            )
            AGAINST (
            ?
            IN NATURAL LANGUAGE
            MODE
            ) AS score
            FROM rq_product_detail
            GROUP BY store_name
            HAVING score >0
            ORDER BY score DESC";

    const SQL_SEL_LOCATIONS =
        "SELECT store_id, location FROM stores";

    public static function compare(){
        $dbh = DataBase::getDbh();
        try {
            $locSth = $dbh->query(self::SQL_SEL_LOCATIONS);
            while($locRow = $locSth->fetch()){
                echo "<h2>$locRow[store_id] - $locRow[location]</h2><ul>";
                $selSth = $dbh->prepare(self::SQL_SEL_RQID_BY_NAME);			
			    $selSth->execute(array($locRow['location']));
			    while($row = $selSth->fetch())
                    echo "<li>$row[store_id] - $row[store_name]: $row[score]</li>";
                echo "</ul>";
            }
			
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
        return $id;
    }
}

nameCompare::compare();
=======
<?php
require_once "config.php";
require_once "DataBase.php";

class nameCompare{

    const SQL_SEL_RQID_BY_NAME = 
        "SELECT store_id, store_name, 
            MATCH (
            store_name
            )
            AGAINST (
            ?
            IN NATURAL LANGUAGE
            MODE
            ) AS score
            FROM rq_product_detail
            GROUP BY store_name
            HAVING score >0
            ORDER BY score DESC";

    const SQL_SEL_LOCATIONS =
        "SELECT store_id, location FROM stores";

    public static function compare(){
        $dbh = DataBase::getDbh();
        try {
            $locSth = $dbh->query(self::SQL_SEL_LOCATIONS);
            while($locRow = $locSth->fetch()){
                echo "<h2>$locRow[store_id] - $locRow[location]</h2><ul>";
                $selSth = $dbh->prepare(self::SQL_SEL_RQID_BY_NAME);			
			    $selSth->execute(array($locRow['location']));
			    while($row = $selSth->fetch())
                    echo "<li>$row[store_id] - $row[store_name]: $row[score]</li>";
                echo "</ul>";
            }
			
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
        return $id;
    }
}

nameCompare::compare();
>>>>>>> origin/master
?>