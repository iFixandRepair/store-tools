<?php
require_once "config.php";
require_once "Store.php";
require_once "RQProductDetail.php";

$auth_data = json_decode(
	file_get_contents(
	'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token='
	. $_POST["id_token"])
);

$store = Store::getStoreByEmail($auth_data->email);
$rqId = RQProductDetail::getRqIdByLocation($store->getLocation());
$boxNameList = RQProductDetail::getBoxNamesByRQId($rqId);
echo json_encode(
	array(
		"boxNames" => $boxNameList,
	)
);
?>