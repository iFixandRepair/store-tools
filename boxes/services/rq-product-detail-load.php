<?php
require_once 'config.php';
require_once 'lib/RQProductDetailLoader.php';
$rqLoader= new RQProductDetailLoader();
$rqLoader->loadProductDetail("uploads/Product Detail Sales Report From 01-Oct-2016 To 01-Nov-2016 Recycles RMA DOA.xls");
?>