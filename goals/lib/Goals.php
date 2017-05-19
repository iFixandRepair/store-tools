<?php
/**********************************************/
/**********************************************/
/***           GOALS OPERATIONS             ***/
/**********************************************/
/***     webdeveloper@ifixandrepair.com     ***/
/**********************************************/
/**********************************************/
require_once("DataBase.php");
class Goals{
    ## Consulta de los Store Goals del mes actual
    const SQL_SEL_GOALS = 
        "SELECT 
            eg.employee_id
            ,e.name employee_name
            ,eg.store_id
            ,s.rq_name
            ,eg.acc_goal
            ,eg.rep_goal
        FROM 
            employees_goals eg
            INNER JOIN employees e ON eg.employee_id = e.employee_id
            INNER JOIN rq_stores s ON eg.store_id = s.rq_store_id
        WHERE
            eg.month = MONTH(CURRENT_DATE()) 
            AND 
            eg.year = YEAR(CURRENT_DATE()) 
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
    /*** function:    GetGoals                                                         ***/
    /*** Description: Funcion que se encarga de traer los datos de Goals tanto de los  ***/
    /***              Empleados como las Tiendas                                       ***/
    /*** In Params:                                                                    ***/
    /*************************************************************************************/
    /*************************************************************************************/
    public static function GetGoals(){
        $dbh = DataBase::getDbh();
        $arr = null;
        try {
			$selSth = $dbh->prepare(self::SQL_SEL_GOALS);		
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