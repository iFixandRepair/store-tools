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
    /*** function:    getSalesReport                                                   ***/
    /*** Description: Funcion que se encarga de traer los datos del reporte de ventas  ***/
    /*** In Params:   $date_ini:    Fecha inicial del reporte                          ***/
    /***              $date_end:    Fecha final del reporte                            ***/
    /***              $typeRep:     Tipo del reporte 1.Diario 2.Semanal 3.Mensual      ***/
    /*** Return:      Arreglo de las ventas                                            ***/ 
    /*************************************************************************************/
    /*************************************************************************************/
    public function getSalesReport($date_ini,$date_end,$typeRep ){
        $date_ini = date('Y-m-d',strtotime($date_ini));
        $date_end = date('Y-m-d',strtotime($date_end));
        $dbh = DataBase::getDbh();
        try 
        {
            $query = $this->PrepareSQLQuery($typeRep);
           // echo $query;
            $selSth = $dbh->prepare($query); 
            $selSth->execute(array($date_ini, $date_end));         
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
    /*** In Params:   $typeRep:     Tipo del reporte 1.Diario 2.Semanal 3.Mensual      ***/
    /*** Return:      String (query)                                                   ***/ 
    /*************************************************************************************/
    /*************************************************************************************/
    private function PrepareSQLQuery($typeRep)
    {
        ## Campos que se van a usar en cada consulta 
        $SQL_SEL_CAMPOS_REP = " 
            ,s.Store
            ,sg.Manager
            ,ROUND(SUM(s.Sales),2)                      AS 'Sales'
            ,ROUND(SUM(s.Accesories),2)                 AS 'Accesories'
            ,ROUND(SUM(s.GrossProfit),2)                AS 'GrossProfit'
            ,ROUND(SUM(s.Hours),2)                      AS 'Hours'
            ,ROUND(SUM(s.OT),2)                         AS 'OT'    
            -- ,sg.Hrs_mgr                                 AS 'MGR_req_hrs'
            ,ROUND(SUM(sp.RegularTime),2)               AS 'MGR_HRS'
            ,MONTH(s.date_sale)                         AS 'MONTH' 
            ,YEAR(s.date_sale)                         AS 'YEAR' ";

        if($typeRep==1)
        {
            ## Consulta en caso de que se desee Diaria
            $query = " 
                SELECT
                    s.date_sale                                         AS 'Date' 
                    ,ROUND((sg.Hours / 7),0)                            AS 'Budget'
                    ,ROUND(SUM(s.Hours) - (sg.Hours / 7),2)             AS 'Budget_plus/minus'
                    ,ROUND((sg.Hrs_mgr / 7),2)                          AS 'MGR_req_hrs' 
                    ,ROUND((sg.Hrs_mgr / 7) - SUM(sp.RegularTime),2)    AS 'MGR_hrs_plus/minus'".$SQL_SEL_CAMPOS_REP ."
                FROM 
                    sales s
                    INNER JOIN sales_store_goals sg ON s.Store = sg.Store
                    INNER JOIN sales_payroll sp ON s.Store = sp.Store AND s.date_sale = sp.date_sale AND sg.Manager = sp.Employee
                WHERE
                    s.date_sale BETWEEN  ?  AND   ? 
                GROUP BY
                    s.date_sale
                    ,s.Store
                    ,sg.Manager";
        }
        if($typeRep==2)
        {
            ## Consulta en caso de que se desee Semanal
            $query = " 
                SELECT
                    WEEK(s.date_sale)                           AS 'Date' 
                    ,sg.Hours                                   AS 'Budget'
                    ,ROUND(SUM(s.Hours) - sg.Hours ,2)          AS 'Budget_plus/minus' 
                    ,sg.Hrs_mgr                                 AS 'MGR_req_hrs' 
                    ,ROUND(sg.Hrs_mgr - SUM(sp.RegularTime),2)  AS 'MGR_hrs_plus/minus'".$SQL_SEL_CAMPOS_REP ."
                FROM 
                    sales s
                    INNER JOIN sales_store_goals sg ON s.Store = sg.Store
                    INNER JOIN sales_payroll sp ON s.Store = sp.Store AND s.date_sale = sp.date_sale AND sg.Manager = sp.Employee
                WHERE
                    s.date_sale BETWEEN  ?  AND   ? 
                GROUP BY
                    WEEK(s.date_sale) 
                    ,s.Store
                    ,sg.Manager";
        }
        if($typeRep==3)
        {
            ## Consulta en caso de que se desee Mensual
            $query = " 
                SELECT
                    MONTH(s.date_sale)                                  AS 'Date' 
                    ,ROUND((sg.Hours * 4),0)                            AS 'Budget'
                    ,ROUND(SUM(s.Hours) - (sg.Hours * 4),2)             AS 'Budget_plus/minus'
                    ,ROUND((sg.Hrs_mgr * 4),2)                          AS 'MGR_req_hrs' 
                    ,ROUND((sg.Hrs_mgr * 4) - SUM(sp.RegularTime),2)    AS 'MGR_hrs_plus/minus'".$SQL_SEL_CAMPOS_REP ."
                FROM 
                    sales s
                    INNER JOIN sales_store_goals sg ON s.Store = sg.Store
                    INNER JOIN sales_payroll sp ON s.Store = sp.Store AND s.date_sale = sp.date_sale AND sg.Manager = sp.Employee
                WHERE
                    s.date_sale BETWEEN  ?  AND   ? 
                GROUP BY
                    MONTH(s.date_sale) 
                    ,s.Store
                    ,sg.Manager";
        }
        //echo $query;
        return $query;
    }
}
?>
