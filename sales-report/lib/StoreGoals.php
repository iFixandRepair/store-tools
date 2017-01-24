<?php
/**********************************************/
/**********************************************/
/***        OPERATIONS VIEW REPORT VR       ***/
/**********************************************/
/***    web.developer@ifixandrepair.com     ***/
/**********************************************/
/**********************************************/
require_once("DataBase.php");
class StoreGoals{
    ## Consulta de los Store Goals del mes actual
    const SQL_SEL_STORE_GOALS = 
        "SELECT 
            Id
            ,Month
            ,Store
            ,Manager
            ,Hours
            ,Hrs_mgr
            ,Hrs_emp
        FROM 
            sales_store_goals        
        WHERE
            Month = MONTH(CURRENT_DATE()) 
        ORDER BY 
            store ASC
        ";

    ## Update del registro que se modifico
    const SQL_UPD_STORE_GOALS = 
        "UPDATE 
            sales_store_goals
        SET
            Hours = ?
            ,Hrs_mgr = ?
            ,Hrs_emp = ?
        WHERE 
           Id = ?";

    /*************************************************************************************/
    /*************************************************************************************/
    /*** function:    GetStoreGoals                                                    ***/
    /*** Description: Funcion que se encarga de traer los datos de Store StoreGoals    ***/
    /*** In Params:                                                                    ***/
    /*************************************************************************************/
    /*************************************************************************************/
    public static function GetStoreGoals(){
        $dbh = DataBase::getDbh();
        $arr = null;
        try {
			$selSth = $dbh->prepare(self::SQL_SEL_STORE_GOALS);			
			$selSth->execute();
			$arr = $selSth->fetchAll(PDO::FETCH_CLASS);            
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
        return $arr;
    }

    /*************************************************************************************/
    /*************************************************************************************/
    /*** function:    getSalesReport                                                   ***/
    /*** Description: Funcion encargada de actualizar el registro modificado,          ***/
    /***              Esta funcion unicamente actualiza 3 reigstros de la tabla        ***/
    /***              (Hours,Hrs_mg,Hrs_emp)                                           ***/
    /*** In Params:   $Id:      Id del registro a modificar                            ***/
    /***              $Hours:   Horas totales (la suma de las MgrHrs y EmpHrs)         ***/
    /***              $MgrHrs:  Horas del Manager                                      ***/
    /***              $EmpHrs:  Horas de los Employees                                 ***/
    /*** Return:      Booleano                                                         ***/
    /*************************************************************************************/
    /*************************************************************************************/
    public static function UpdateStoreGoals($Id,$Hours,$MgrHrs,$EmpHrs){
        $dbh = DataBase::getDbh();
        try {
            $selSth = $dbh->prepare(self::SQL_UPD_STORE_GOALS);    	
			$rst = $selSth->execute(array($Hours, $MgrHrs, $EmpHrs, $Id));
            return $rst;

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
            return false;
			die();
		}
    }
}
?>