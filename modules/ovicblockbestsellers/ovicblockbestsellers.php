<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class OvicBlockBestSellers extends Module
{
	protected static $cache_best_sellers;

	public function __construct()
	{
		$this->name = 'ovicblockbestsellers';
		$this->tab = 'front_office_features';
		$this->version = '1.5.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Fashion - Top-sellers block');
		$this->description = $this->l('Adds a block displaying your store\'s top-selling products.');
	}

	public function install()
	{
		$this->_clearCache('*');

		if (!parent::install()
			|| !$this->registerHook('header')
			|| !$this->registerHook('actionOrderStatusPostUpdate')
			|| !$this->registerHook('addproduct')
			|| !$this->registerHook('updateproduct')
			|| !$this->registerHook('deleteproduct')
			|| !$this->registerHook('displayHomeTab')
			|| !$this->registerHook('displayHomeTabContent')
            || !Configuration::updateValue('OVIC_BESTSELLERS_NBR', 8)
			|| !ProductSale::fillProductSales()
		)
			return false;

		// Hook the module either on the left or right column
		$theme = new Theme(Context::getContext()->shop->id_theme);
		if ((!$theme->default_left_column || !$this->registerHook('leftColumn'))
			&& (!$theme->default_right_column || !$this->registerHook('rightColumn'))
		)
		{
			// If there are no colums implemented by the template, throw an error and uninstall the module
			$this->_errors[] = $this->l('This module need to be hooked in a column and your theme does not implement one');
			parent::uninstall();

			return false;
		}

		return true;
	}

	public function uninstall()
	{
		$this->_clearCache('*');

		return parent::uninstall();
	}

	public function hookAddProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookUpdateProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookDeleteProduct($params)
	{
		$this->_clearCache('*');
	}

	public function hookActionOrderStatusPostUpdate($params)
	{
		$this->_clearCache('*');
	}

	public function _clearCache($template, $cache_id = NULL, $compile_id = NULL)
	{
		parent::_clearCache('ovicblockbestsellers.tpl');
		parent::_clearCache('ovicblockbestsellers-home.tpl', $this->getCacheId('ovicblockbestsellers-home'));
		parent::_clearCache('tab.tpl', $this->getCacheId('ovicblockbestsellers-tab'));
	}

	/**
	 * Called in administration -> module -> configure
	 */
	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitBestSellers'))
		{
            if (!($productNbr = Tools::getValue('OVIC_BESTSELLERS_NBR')) || empty($productNbr))
				$output .= $this->displayError($this->l('Please complete the "products to display" field.'));
			elseif ((int)($productNbr) == 0)
				$output .= $this->displayError($this->l('Invalid number.'));
			else{
    			Configuration::updateValue('PS_BLOCK_BESTSELLERS_DISPLAY', (int)Tools::getValue('PS_BLOCK_BESTSELLERS_DISPLAY'));
                Configuration::updateValue('OVIC_BESTSELLERS_NBR', (int)($productNbr));
                $this->_clearCache('*');
    			$output .= $this->displayConfirmation($this->l('Settings updated'));
            }
		}

		return $output.$this->renderForm();
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
                    array(
    						'type' => 'text',
    						'label' => $this->l('Products to display'),
    						'name' => 'OVIC_BESTSELLERS_NBR',
    						'class' => 'fixed-width-xs',
    						'desc' => $this->l('Define the number of products to be displayed in this block.')
    					),
					array(
						'type' => 'switch',
						'label' => $this->l('Always display this block'),
						'name' => 'PS_BLOCK_BESTSELLERS_DISPLAY',
						'desc' => $this->l('Show the block even if no best sellers are available.'),
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					)
				),
				'submit' => array(
					'title' => $this->l('Save')
				)
			)
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitBestSellers';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => array('PS_BLOCK_BESTSELLERS_DISPLAY' => Tools::getValue('PS_BLOCK_BESTSELLERS_DISPLAY', Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY')),
                                    'OVIC_BESTSELLERS_NBR' => Tools::getValue('OVIC_BESTSELLERS_NBR', Configuration::get('OVIC_BESTSELLERS_NBR')),),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
    
    public function AjaxCall($page){
		$this->smarty->assign(
			array(
				'products' => $this->getBestSellers((int)$page),
				'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
				'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
			)
		);
        $product_list = $this->display(__FILE__,'list.tpl');
		return Tools::jsonEncode(array('productList' => $product_list));
    }
    
    private function getTotalTopSales(){
		return count(ProductSale::getBestSalesLight((int)$this->context->cookie->id_lang, 0,999999));
    }
    
	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index')
			$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
        $this->context->controller->addJS(($this->_path).'ovicblockbestsellers.js');
		//$this->context->controller->addCSS($this->_path.'ovicblockbestsellers.css', 'all');
	}

	public function hookDisplayHomeTab($params)
	{
		if (!$this->isCached('tab.tpl', $this->getCacheId('ovicblockbestsellers-tab')))
		{
			OvicBlockBestSellers::$cache_best_sellers = $this->getBestSellers();
			$this->smarty->assign('best_sellers', OvicBlockBestSellers::$cache_best_sellers);
		}

		if (OvicBlockBestSellers::$cache_best_sellers === false)
			return false;

		return $this->display(__FILE__, 'tab.tpl', $this->getCacheId('ovicblockbestsellers-tab'));
	}

	public function hookdisplayHomeTabContent($params)
	{
		if (!$this->isCached('ovicblockbestsellers-home.tpl', $this->getCacheId('ovicblockbestsellers-home')))
		{
			$this->smarty->assign(array(
                'toppage' => Tools::getValue('toppage',1),
                'toptotal' => $this->getTotalTopSales(),
				'best_sellers' => OvicBlockBestSellers::$cache_best_sellers,
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))
			));
		}

		if (OvicBlockBestSellers::$cache_best_sellers === false)
			return false;

		return $this->display(__FILE__, 'ovicblockbestsellers-home.tpl', $this->getCacheId('ovicblockbestsellers-home'));
	}

	public function hookRightColumn($params)
	{
		if (!$this->isCached('ovicblockbestsellers.tpl', $this->getCacheId('ovicblockbestsellers-col')))
		{
			if (!isset(OvicBlockBestSellers::$cache_best_sellers))
				OvicBlockBestSellers::$cache_best_sellers = $this->getBestSellers();
			$this->smarty->assign(array(
				'best_sellers' => OvicBlockBestSellers::$cache_best_sellers,
				'mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
				'smallSize' => Image::getSize(ImageType::getFormatedName('small'))
			));
		}

		if (OvicBlockBestSellers::$cache_best_sellers === false)
			return false;

		return $this->display(__FILE__, 'ovicblockbestsellers.tpl', $this->getCacheId('ovicblockbestsellers-col'));
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	protected function getBestSellers($page=0)
	{
        	   
		if (Configuration::get('PS_CATALOG_MODE'))
			return false;

		if (!($result = ProductSale::getBestSalesLight((int)$this->context->cookie->id_lang, $page,(int)Configuration::get('OVIC_BESTSELLERS_NBR'))))
			return (Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY') ? array() : false);

		$currency = new Currency($this->context->cookie->id_currency);
		$usetax = (Product::getTaxCalculationMethod((int)$this->context->customer->id) != PS_TAX_EXC);
		foreach ($result as &$row)
			$row['price'] = Tools::displayPrice(Product::getPriceStatic((int)$row['id_product'], $usetax), $currency);

		return $result;
	}
}
