<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/ovichomefeatured.php');
$module = new OvicHomeFeatured();
if (Tools::isSubmit('featurepage')){
    $page = (int)Tools::getValue('featurepage');
    echo $module->AjaxCall($page);
}
    
