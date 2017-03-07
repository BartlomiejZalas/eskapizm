<?php

function productReviewCompare($p1,$p2){
     if ($p1['review'] == $p2['review']) {
        return 0;
    }
    return ($p1['review'] < $p2['review']) ? 1 : -1;
}

class HomeCategories extends Module
{
    protected static $cache_best_sellers_1;
    protected static $cache_new_products_1;
    protected static $cache_most_review_1;
    protected static $cache_best_sellers_2;
    protected static $cache_new_products_2;
    protected static $cache_most_review_2;

	public function __construct()
	{
		$this->name = 'homecategories';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'OvicSoft';

		parent::__construct();

		$this->displayName = $this->l('Fashion - Home Categories');
		$this->description = $this->l('Display newproducts, bestseller and most reviews of category on home page.');
        $this->bootstrap = true;

		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	}
    public function install()
	{
		if (!parent::install() || !$this->registerHook('displayHome')
		|| !$this->registerHook('actionOrderStatusPostUpdate')
		|| !$this->registerHook('addproduct')
		|| !$this->registerHook('updateproduct')
		|| !$this->registerHook('deleteproduct')
        || !$this->registerHook('displayHomeCategory')
        || !$this->registerHook('displayBackOfficeHeader')
        || !$this->registerHook('header')
        || !Configuration::updateValue('HOMECATEGORY_1',Configuration::get('PS_HOME_CATEGORY'))
        || !Configuration::updateValue('HOMECATEGORY_2',3))
			return false;
		return true;
	}
    public function uninstall()
	{

		if (!parent::uninstall())
			return false;
        $this->_clearCache('*');
        Configuration::deleteByName('HOMECATEGORY_1');
        Configuration::deleteByName('HOMECATEGORY_2');
		return true;
	}

    public function getContent()
    {
        $output = '';
		$errors = array();
        $languages = Language::getLanguages();
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $id_lang = $this->context->language->id;
		if (Tools::isSubmit('submitGlobal1'))
		{
            Configuration::updateValue('HOMECATEGORY_1', Tools::getValue('HOMECATEGORY_1'));
            Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('homecategories_home.tpl'));
            $output .= $this->displayConfirmation($this->l('Your settings have been updated.'));
		}elseif(Tools::isSubmit('submitGlobal2'))
		{
            Configuration::updateValue('HOMECATEGORY_2', Tools::getValue('HOMECATEGORY_2'));
            Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('homecategories.tpl'));
            $output .= $this->displayConfirmation($this->l('Your settings have been updated.'));
		}
        $output .= $this->displayForm();
        return $output;
    }

    public function displayForm()
	{
	    $HOMECATEGORY_1 = Tools::getValue('HOMECATEGORY_1',Configuration::get('HOMECATEGORY_1'));
        $HOMECATEGORY_2 = Tools::getValue('HOMECATEGORY_2',Configuration::get('HOMECATEGORY_2'));
		$this->context->smarty->assign(array(
            'cate_option1' => $this->getCategoryOption(1,$HOMECATEGORY_1,$HOMECATEGORY_2),
            'cate_option2' => $this->getCategoryOption(1,$HOMECATEGORY_2,$HOMECATEGORY_1),
            'postAction' => AdminController::$currentIndex .'&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
        ));

		return $this->display(__FILE__, 'views/templates/admin/main.tpl');
	}

    private function getCategoryOption($id_category = 1, $selected = null, $disabled = null, $id_lang = false, $id_shop = false,
        $recursive = true)
    {
        $html = '';
        $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
        $category = new Category((int)$id_category, (int)$id_lang, (int)$id_shop);
        if (is_null($category->id))
            return;
        if ($recursive)
        {
            $children = Category::getChildren((int)$id_category, (int)$id_lang, true, (int)
                $id_shop);
            if ($category->level_depth > 1)
                $spacer = str_repeat('&nbsp;', 3 * ((int)$category->level_depth -1));
            else
                $spacer = '';
        }
        $shop = (object)Shop::getShop((int)$category->getShopID());
         if (!in_array($category->id,array(Configuration::get('PS_HOME_CATEGORY'), Configuration::get('PS_ROOT_CATEGORY')))){
            $disable_str = '';
            if ($category->id == $disabled && $selected !=$category->id){
                $disable_str = 'disabled ';
            }
            $html .= '<option '.$disable_str.($selected ==$category->id? 'selected="selected" ':'').'value="' . (int)$category->id . '">'.(isset($spacer) ? $spacer : '') . $category->name .
                    '</option>';
        }elseif($category->id != Configuration::get('PS_ROOT_CATEGORY')){
            $html .= '<optgroup label="' .(isset($spacer) ? $spacer : ''). $category->name . '">';
        }
        if (isset($children) && count($children))
            foreach ($children as $child)
            {
                $html .= $this->getCategoryOption((int)$child['id_category'],$selected,$disabled, (int)$id_lang, (int)
                    $child['id_shop'], $recursive);
            }
        return $html;
    }

    public function hookDisplayBackOfficeHeader()
	{
		if (Tools::getValue('configure') != $this->name)
			return;
		$this->context->controller->addCSS($this->_path.'css/admin.css');
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
		parent::_clearCache('homecategories.tpl');
		parent::_clearCache('homecategories_home.tpl');
	}
    public function hookdisplayHome($params)
	{
		//if (!$this->isCached('homecategories_home.tpl', $this->getCacheId()))
		//{
            $id_category = (int)Configuration::get('HOMECATEGORY_1');
    		if ($id_category && Validate::isUnsignedId($id_category)){
                $category = new Category($id_category,(int) $this->context->language->id);
                if (!isset(HomeCategories::$cache_new_products_1))
				    HomeCategories::$cache_new_products_1 = $this->getNewProducts($id_category);
    			if (!isset(HomeCategories::$cache_best_sellers_1))
				    HomeCategories::$cache_best_sellers_1 = $this->filterProductByCategory($id_category,ProductSale::getBestSales((int) $this->context->language->id, 0, 1000));
                if (!isset(HomeCategories::$cache_most_review_1))
				    HomeCategories::$cache_most_review_1 = $this->getReviewProducts($id_category);
                $this->context->smarty->assign(array(
                    'cate_name' => $category->name,
                    'hook_position' => 1,
                    'newproducts' => HomeCategories::$cache_new_products_1,
                    'bestsellers' => HomeCategories::$cache_best_sellers_1,
                    'mostreviews' => HomeCategories::$cache_most_review_1,

        		));
            }
		//}
		return $this->display(__FILE__, 'homecategories_home.tpl', $this->getCacheId());
	}
    public function hookdisplayHomeCategory($params)
	{
		if (!$this->isCached('homecategories.tpl', $this->getCacheId()))
		{
            $id_category = (int)Configuration::get('HOMECATEGORY_2');
    		if ($id_category && Validate::isUnsignedId($id_category)){
                $category = new Category($id_category,(int) $this->context->language->id);
                if (!isset(HomeCategories::$cache_new_products_2))
				    HomeCategories::$cache_new_products_2 = $this->getNewProducts($id_category);
    			if (!isset(HomeCategories::$cache_best_sellers_2))
				    HomeCategories::$cache_best_sellers_2 = $this->filterProductByCategory($id_category,ProductSale::getBestSales((int) $this->context->language->id, 0, 1000));
                if (!isset(HomeCategories::$cache_most_review_2))
				    HomeCategories::$cache_most_review_2 = $this->getReviewProducts($id_category);
                $this->context->smarty->assign(array(
                    'cate_name' => $category->name,
                    'hook_position' => 2,
                    'newproducts' => HomeCategories::$cache_new_products_2,
                    'bestsellers' => HomeCategories::$cache_best_sellers_2,
                    'mostreviews' => HomeCategories::$cache_most_review_2,
        		));
            }
		}
		return $this->display(__FILE__, 'homecategories.tpl', $this->getCacheId());
	}
    private function getNewProducts($id_category){
        $total_news = Product::getNewProducts((int) $this->context->language->id, 0, 10,true);
        return $this->filterProductByCategory($id_category,Product::getNewProducts((int) $this->context->language->id, 0, $total_news));
    }

    private function getReviewProducts($id_category){
        $filename = _PS_MODULE_DIR_.'productcomments/ProductComment.php';
        if (!file_exists($filename) || Module::isInstalled('productcomments') != 1){
            return false;
        }
        require_once($filename);
        $category = new Category($id_category);
        $nbProducts = $category->getProducts(null, null, null, null, null, true);
        $cat_products = $category->getProducts((int) $this->context->language->id,0,$nbProducts);
        $results = array();
        foreach ($cat_products as $product){
            $average = ProductComment::getAverageGrade($product['id_product']);
            if ($average['grade']>0){
                $product['review'] = $average['grade'];
                $results[] = $product;
            }
        }
        uasort($results, 'productReviewCompare');
        return $results;
    }


    private function filterProductByCategory($id_category = null, $products = array()){
        if (is_null($id_category) || empty($products))
            return false;
        $results = array();
        $nbs = Configuration::get('PS_PRODUCTS_PER_PAGE');
        if ((int)$nbs < 1)
            $nbs = 20;
        foreach ($products as $k => $product){
            $product_categories = Product::getProductCategories($product['id_product']);
            if (in_array($id_category,$product_categories)){
                $results[] = $product;
            }
            if (sizeof($results) >= $nbs)
                break;
        }
        return $results;
    }

    public function hookHeader()
	{
	    //$this->context->controller->addCSS($this->_path.'css/homecategories.css');
		$this->context->controller->addJS(($this->_path).'js/homecategories.js');
	}
 }