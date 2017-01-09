<?php

require_once "DataBase.php";

class Admin{
    const SQL_SEL_TOOL_ADMIN = "SELECT * FROM ifixandm_stores WHERE tool=? AND email=?";

    private $email;

    public function isAdminForTool($toolName){
        $dbh = DataBase::getDbh();
        try {			
            $selSth = $dbh->prepare(self::SQL_SEL_TOOL_ADMIN);
			$selSth->execute(array(
                $toolName,
                $this->email
            ));
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
    }

    public function setEmail($email){
        $thsi->email=$email;
    }
}
?>