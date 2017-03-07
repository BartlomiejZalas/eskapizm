<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/ovicblocknewproducts.php');
$module = new OvicBlockNewProducts();
if (Tools::isSubmit('newpage')){
    $page = (int)Tools::getValue('newpage');
    
    echo $module->AjaxCall($page);
}
    
