<?php
require_once './vendor/autoload.php';
include './Crud.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

// Here you need to export your JSON secret file
$serviceAccount = \Kreait\Firebase\ServiceAccount::fromJsonFile(__DIR__ . '/secret/rugged-plane-196108-dfb1c2d5a6a5.json');

$firebase = (new Factory)
	->withServiceAccount($serviceAccount)
	->create();

$database = $firebase->getDatabase();
?>