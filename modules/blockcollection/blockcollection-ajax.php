<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/blockcollection.php');

$blockcollection = new Blockcollection();
$id_collection = $_REQUEST['id_collection'];
echo $blockcollection->ajaxCall($id_collection);