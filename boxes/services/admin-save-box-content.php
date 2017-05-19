<?php
require_once "config.php";
require_once "Store.php";
require_once "StoreBox.php";

$auth_data = json_decode(
	file_get_contents(
	'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token='
	. $_POST["id_token"])
);

if(isset($_POST["boxName"])
&& isset($_POST["field"])
&& isset($_POST["value"])
&& isset($_POST["boxId"])){
    $box = new StoreBox();
    $box->setBoxId($_POST["boxId"]);
    $box->loadBoxContent();
    $box->saveBoxContent($_POST["boxName"], $_POST["field"], $_POST["value"]);
}

?>