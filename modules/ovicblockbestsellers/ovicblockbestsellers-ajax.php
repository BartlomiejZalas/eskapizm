<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/ovicblockbestsellers.php');
$bestsell = new OvicBlockBestSellers();
if (Tools::isSubmit('toppage')){
    $page = (int)Tools::getValue('toppage');
    echo $bestsell->AjaxCall($page); 
}
    
