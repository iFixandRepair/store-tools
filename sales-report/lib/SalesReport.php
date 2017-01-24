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

    ## Campos que se van a usar en cada consulta
    const SQL_SEL_CAMPOS_REP = " 
            ,s.Store
            ,sg.Manager
            ,ROUND(SUM(s.Sales),2)                      AS 'Sales'
            ,ROUND(SUM(s.GrossProfit),2)                AS 'GrossProfit'
            ,ROUND(SUM(s.Hours),2)                      AS 'Hours'
            ,sg.Hours                                   AS 'Budget'
            ,ROUND(SUM(s.Hours) - sg.Hours,2)           AS 'Budget_plus/minus'
            ,ROUND(SUM(s.OT),2)                         AS 'OT'    
            ,sg.Hrs_mgr                                 AS 'MGR_req_hrs'
            ,ROUND(SUM(sp.RegularTime),2)               AS 'MGR_HRS'
            ,ROUND(sg.Hrs_mgr - SUM(sp.RegularTime),2)  AS 'MGR_hrs_plus/minus'";

    ## Consulta en caso de que se desee Diaria
    const SQL_SEL_SALES_Daily_REP = " 
        SELECT
            s.date_sale                                 AS 'Date' ".self::SQL_SEL_CAMPOS_REP ."
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

    ## Consulta en caso de que se desee Semanal
    const SQL_SEL_SALES_Weekly_REP = " 
        SELECT
            WEEK(s.date_sale)                           AS 'Date' ".self::SQL_SEL_CAMPOS_REP ."
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

    ## Consulta en caso de que se desee Mensual
    const SQL_SEL_SALES_Monthly_REP = " 
        SELECT
            MONTH(s.date_sale)                          AS 'Date' ".self::SQL_SEL_CAMPOS_REP ."
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
    public static function getSalesReport($date_ini,$date_end,$typeRep ){
        $date_ini = date('Y-m-d',strtotime($date_ini));
        $date_end = date('Y-m-d',strtotime($date_end));
        $dbh = DataBase::getDbh();
        try 
        {
            if($typeRep==1)
            {
                $selSth = $dbh->prepare(self::SQL_SEL_SALES_Daily_REP); 
            }   
            else if($typeRep==2)
            {
                $selSth = $dbh->prepare(self::SQL_SEL_SALES_Weekly_REP); 
            }   
            else if($typeRep==3)
            {
                $selSth = $dbh->prepare(self::SQL_SEL_SALES_Monthly_REP); 
            }   
			$selSth->execute(array($date_ini, $date_end));         
            $arr = $selSth->fetchAll(PDO::FETCH_CLASS);    
            return $arr;

		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
            return false;
			die();
		}
    }
}
?>
