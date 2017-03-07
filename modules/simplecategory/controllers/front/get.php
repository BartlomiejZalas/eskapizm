<?php
class SimpleCategoryGetModuleFrontController extends ModuleFrontController
{

	public function __construct(){
		parent::__construct();
		$this->context = Context::getContext();
		//include_once($this->module->getLocalPath().'WishList.php');
		include_once($this->module->getLocalPath().'simplecategory.php');
	}

	public function initContent(){
		parent::initContent();
		$token = Tools::getValue('token');
		$module = new SimpleCategory();
	}
	public static function getTest($value){
		return $value;
	}
}
