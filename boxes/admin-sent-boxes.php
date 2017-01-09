<?php
require_once "config.php";
require_once "Store.php";
require_once "StoreBox.php";

$auth_data = json_decode(
	file_get_contents(
	'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token='
	. $_POST["id_token"])
);

$sentBoxes = StoreBox::getAllSentBoxes();
echo json_encode(
	array(
		"sentBoxes" => array_values($sentBoxes)
	)
);
?>