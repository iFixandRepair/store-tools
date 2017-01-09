<?php
require_once "config.php";
require_once "Store.php";
require_once "StoreBox.php";

$auth_data = json_decode(
	file_get_contents(
	'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token='
	. $_POST["id_token"])
);

$store = Store::getStoreByEmail($auth_data->email);
$openBox = StoreBox::getCurrentOpenBox($store->getStoreId());
if(isset($_POST["boxName"]) && isset($_POST["field"]) && isset($_POST["value"]))
    $openBox->saveBoxContent($_POST["boxName"], $_POST["field"], $_POST["value"]);
?>