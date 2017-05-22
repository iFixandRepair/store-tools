<?php
/**********************************************/
/**********************************************/
/***            SALES REPORT class          ***/
/**********************************************/
/***    web.developer@ifixandrepair.com     ***/
/**********************************************/
/**********************************************/
require_once("DataBase.php");
class SalesReport{

    /*************************************************************************************/
    /*************************************************************************************/
    /*** function:    getGoalsReport                                                   ***/
    /*** Description: Funcion que se encarga de traer los datos del reporte Goals      ***/
    /*** In Params:   $date_ini:    Fecha del reporte                                  ***/
    /***              $typeRep:     Tipo del reporte 1.Stores - Employees 2.Stores     ***/
    /***                            3.Employees                                        ***/
    /*** Return:      Arreglo de las ventas                                            ***/ 
    /*************************************************************************************/
    /*************************************************************************************/
    public function getGoalsReport($date,$typeRep){
        $date = date('Y-m-d',strtotime($date));
        $dbh = DataBase::getDbh();
        try 
        {
            $query = $this->PrepareSQLQuery($date,$typeRep);
            //echo $query;
            $selSth = $dbh->prepare($query); 
            $selSth->execute(array());         
            $arr = $selSth->fetchAll(PDO::FETCH_CLASS);    
            return $arr;

        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            return false;
            die();
        }
    }
    /*************************************************************************************/
    /*************************************************************************************/
    /*** function:    PrepareSQLQuery                                                  ***/
    /*** Description: Funcion que se encarga retornar la consulta para el reporte      ***/
    /*** In Params:   $date_ini:    Fecha del reporte                                  ***/
    /***              $typeRep:     Tipo del reporte 1.Stores - Employees 2.Stores     ***/
    /***                            3.Employees                                        ***/
    /*** Return:      String (query)                                                   ***/ 
    /*************************************************************************************/
    /*************************************************************************************/
    private function PrepareSQLQuery($date,$typeRep)
    {
        $query = " 
               SELECT 
                    eg.employee_id
                    ,ege.name                   employee_name
                    ,eg.goal                    EMPL_GOAL_GP           
                    ,ACGP.gp                    EMPL_ACTUAL_GP
                    ,ACGP.date                  EMPL_LAST_DATE
                    ,(ACGP.gp/(DAY(ACGP.date)*(eg.goal / DAY(LAST_DAY(ACGP.date) ))))*100 as EMPL_TREND
                    ,CASE 
                        WHEN (eg.goal / DAY(LAST_DAY(ACGP.date))) > ((eg.goal - ACGP.gp) / (DAY(LAST_DAY(ACGP.date))-DAY(ACGP.date))) THEN  (eg.goal / DAY(LAST_DAY(ACGP.date))) 
                        ELSE ((eg.goal - ACGP.gp) / (DAY(LAST_DAY(ACGP.date))-DAY(ACGP.date)))  
                    END EMPL_NEEDED                    
                    ,eg.store_id
                    ,egs.rq_name        store_name
                    ,sg.goal            STOR_GOAL_GP       
                    ,ACGPSG.gp          STOR_ACTUAL_GP
                    ,ACGPSG.date        STOR_LAST_DATE
                    ,(ACGPSG.gp/(DAY(ACGPSG.date)*(eg.goal / DAY(LAST_DAY(ACGPSG.date) ))))*100 as STOR_TREND
                    ,CASE 
                        WHEN (eg.goal / DAY(LAST_DAY(ACGPSG.date))) > ((eg.goal - ACGPSG.gp) / (DAY(LAST_DAY(ACGPSG.date))-DAY(ACGPSG.date))) THEN  (eg.goal / DAY(LAST_DAY(ACGPSG.date))) 
                        ELSE ((eg.goal - ACGP.gp) / (DAY(LAST_DAY(ACGPSG.date))-DAY(ACGPSG.date)))  
                    END STOR_NEEDED
                FROM 
                    store_goals sg 
                    LEFT JOIN employees_goals eg ON eg.store_id = sg.store_id
                    INNER JOIN employees ege ON eg.employee_id = ege.employee_id
                    INNER JOIN rq_stores egs ON eg.store_id = egs.rq_store_id
                    LEFT JOIN ( SELECT 
                                    store_id
                                    ,MAX(date) date
                                    ,MAX(profit) gp
                                FROM store_profit
                                WHERE 
                                    date BETWEEN '01-'+MONTH('".$date."')+'-'+YEAR('".$date."') AND '".$date."'
                                GROUP BY 
                                    store_id
                            ) as ACGPSG ON sg.store_id = ACGPSG.store_id
                    LEFT JOIN ( SELECT 
                                    employee_id
                                    ,MAX(date) date
                                    ,MAX(profit) gp
                                FROM employee_profit
                                WHERE 
                                    date BETWEEN '01-'+MONTH('".$date."')+'-'+YEAR('".$date."') AND '".$date."' 
                                GROUP BY 
                                    employee_id
                            ) as ACGP ON eg.employee_id = ACGP.employee_id";
        if($typeRep==1 || $typeRep==2)
        { 
            ## Consulta en caso de que se Stores - Employees
            $query.= " ORDER BY 
                    egs.rq_name ASC";
        }
        else
        {
            ## Consulta en caso de que se Stores - Employees
            $query.= " ORDER BY 
                    ege.name ASC";
        }
        
        //echo $query;
        return $query;
    }
}

?>
