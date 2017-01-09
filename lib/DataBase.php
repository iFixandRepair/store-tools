<?php
class DataBase{
	
	private static $dbh;

	public static function getDbh(){
		if(!isset(self::$dbh)){
			$dsn = 'mysql:host=127.0.0.1;dbname=ifixandm_stores';
			$username = 'ifixandm_ckelley';
			$password = 'ck321*';
			$options = array(
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
			);

			self::$dbh = new PDO($dsn, $username, $password, $options);
			self::$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,TRUE);
		}
		return self::$dbh;
	}
}
?>