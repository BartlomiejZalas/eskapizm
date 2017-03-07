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

class OvicHomeFeatured extends Module
{
	protected static $cache_products;

	public function __construct()
	{
		$this->name = 'ovichomefeatured';
		$this->tab = 'front_office_features';
		$this->version = '1.5';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Fashion - Featured products on the homepage');
		$this->description = $this->l('Displays featured products in the central column of your homepage.');
	}

	public function install()
	{
		$this->_clearCache('*');
		Configuration::updateValue('OVIC_HOME_FEATURED_NBR', 8);

		if (!parent::install()
			|| !$this->registerHook('header')
			|| !$this->registerHook('addproduct')
			|| !$this->registerHook('updateproduct')
			|| !$this->registerHook('deleteproduct')
			|| !$this->registerHook('categoryUpdate')
			|| !$this->registerHook('displayHomeTab')
			|| !$this->registerHook('displayHomeTabContent')
		)
			return false;

		return true;
	}

	public function uninstall()
	{
		$this->_clearCache('*');

		return parent::uninstall();
	}

	public function getContent()
	{
		$output = '';
		$errors = array();
		if (Tools::isSubmit('submitHomeFeatured'))
		{
			$nbr = (int)Tools::getValue('OVIC_HOME_FEATURED_NBR');
			if (!$nbr || $nbr <= 0 || !Validate::isInt($nbr))
				$errors[] = $this->l('An invalid number of products has been specified.');
			else
			{
				Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('ovichomefeatured.tpl'));
				Configuration::updateValue('OVIC_HOME_FEATURED_NBR', (int)$nbr);
			}
			if (isset($errors) && count($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->l('Your settings have been updated.'));
		}

		return $output.$this->renderForm();
	}
    public function AjaxCall($page){
		$this->smarty->assign(
			array(
				'products' => $this->getProducts($page),
				'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
				'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
			)
		);
        
        $product_list = $this->display(__FILE__,'list.tpl');

		return Tools::jsonEncode(array('productList' => $product_list));
    }
    private function getTotalProduct($id_category = null, $active = true){
        if (is_null($id_category))
            $id_category = Context::getContext()->shop->getCategory();
        $sql = 'SELECT COUNT(cp.`id_product`) AS total
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
				WHERE cp.`id_category` = '.(int)$id_category.
				($active ? ' AND product_shop.`active` = 1' : '');
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }
    
	public function hookDisplayHeader($params)
	{
		$this->hookHeader($params);
	}

	public function hookHeader($params)
	{
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index')
			$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
		//$this->context->controller->addCSS(($this->_path).'ovichomefeatured.css', 'all');
        $this->context->controller->addJS(($this->_path).'homefeatured.js');
	}

	public function _cacheProducts()
	{
		if (!isset(OvicHomeFeatured::$cache_products))
		{
			OvicHomeFeatured::$cache_products = $this->getProducts();
		}

		if (OvicHomeFeatured::$cache_products === false || empty(OvicHomeFeatured::$cache_products))
			return false;
	}
    public function getProducts($page = 1)
	{
		$category = new Category(Context::getContext()->shop->getCategory(), (int)Context::getContext()->language->id);
		$nb = (int)Configuration::get('OVIC_HOME_FEATURED_NBR');
		$cache_products = $category->getProducts((int)Context::getContext()->language->id, $page, ($nb ? $nb : 8), 'position');
		if (!$cache_products || empty($cache_products))
			return false;
        return $cache_products;
	}

	public function hookDisplayHomeTab($params)
	{
		if (!$this->isCached('tab.tpl', $this->getCacheId('ovichomefeatured-tab')))
			$this->_cacheProducts();

		return $this->display(__FILE__, 'tab.tpl', $this->getCacheId('ovichomefeatured-tab'));
	}

	public function hookDisplayHome($params)
	{
		if (!$this->isCached('ovichomefeatured.tpl', $this->getCacheId()))
		{
			$this->_cacheProducts();
			$this->smarty->assign(
				array(
                    'featurepage' => 2,
					'products' => OvicHomeFeatured::$cache_products,
                    'hometotal' => $this->getTotalProduct(),
					'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
					'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
				)
			);
		}

		return $this->display(__FILE__, 'ovichomefeatured.tpl', $this->getCacheId());
	}

	public function hookDisplayHomeTabContent($params)
	{
		return $this->hookDisplayHome($params);
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

	public function hookCategoryUpdate($params)
	{
		$this->_clearCache('*');
	}

	public function _clearCache($template, $cache_id = NULL, $compile_id = NULL)
	{
		parent::_clearCache('ovichomefeatured.tpl');
		parent::_clearCache('tab.tpl', 'ovichomefeatured-tab');
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'description' => $this->l('To add products to your homepage, simply add them to the root product category (default: "Home").'),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Number of products to be displayed'),
						'name' => 'OVIC_HOME_FEATURED_NBR',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Set the number of products that you would like to display on homepage (default: 8).'),
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = (int)Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitHomeFeatured';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'OVIC_HOME_FEATURED_NBR' => Tools::getValue('OVIC_HOME_FEATURED_NBR', Configuration::get('OVIC_HOME_FEATURED_NBR')),
		);
	}
}
