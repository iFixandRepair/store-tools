<?php
require_once "config.php";
require_once 'RQProductDetailLoader.php';
var_dump($_FILES);
if(isset($_FILES["productDetailFile"])){

	$target_dir = "../uploads/";
	$target_file = $target_dir . basename($_FILES["productDetailFile"]["name"]);
	move_uploaded_file($_FILES["productDetailFile"]["tmp_name"], $target_file);
	$rqLoader= new RQProductDetailLoader();
	$rqLoader->loadProductDetail($target_file);	
}
?>