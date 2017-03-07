<?php
/*
*  @author SonNC Ovic <nguyencaoson.zpt@gmail.com>
*/
// check module da cai hay chua Module::isInstalled('productcomments') != 1
//require (dirname(__FILE__).'/Functions.php');
//require (dirname(__FILE__).'/FlexibleCustomFastCache.php');
//FlexibleCustomFastCache::$storage = "auto";
//FlexibleCustomFastCache::$path = dirname(__FILE__).'/cache/';
//FlexibleCustomFastCache::$securityKey = "flexible-custom.cache";
//FlexibleCustomFastCache::cleanup();
class FlexibleCustom extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
	public $orderValue = array();
    public $orderType = array();
    public $display = array();
    public $categoryType = array();
    public $moduleLayout = array();
	public $compareProductIds = array();
	public $imageHomeSize = array();
	public $tempPath = '';
	public $bannerPath = '';
	public $iconPath = '';
	public $livePath = '';
	public $secure_key= '';
	public $cache = null;
	public $cacheTime = 86400;
    public static $_taxCalculationMethod = null;
    protected static $producPropertiesCache = array();
	protected static $arrPosition = array('displayHome', 'displayFlexibleCategory','displayHomeBottomContent');
	public function __construct()
	{
		$this->name = 'flexiblecustom';
		$this->imageHomeSize = Image::getSize(ImageType::getFormatedName('home'));
        $this->orderValue = array('sales'=>$this->l('Sales'), 'price'=>$this->l('Price'), 'discount'=>$this->l('Discount'), 'add'=>$this->l('Add Date'), 'rand'=>$this->l('Random'));
        $this->orderType = array('asc'=>$this->l('Ascending'), 'desc'=>$this->l('Descending'));
        $this->display = array('all'=>$this->l('All'), 'condition-new'=>$this->l('New'), 'condition-used'=>$this->l('Used'), 'condition-refurbished'=>$this->l('Refurbished'));
        $this->categoryType = array('auto'=>$this->l('Auto'), 'manual'=>$this->l('Manual'));
        $this->moduleLayout = array('option1'=>$this->l('Layout 1'), 'option2'=>$this->l('Layout 2'), 'layout3'=>$this->l('Layout 3'));
        $this->tempPath = dirname(__FILE__).'/images/temps/';
        $this->bannerPath = dirname(__FILE__).'/images/bn/';
		$this->iconPath = dirname(__FILE__).'/images/icons/';
		if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
			$this->livePath = _PS_BASE_URL_SSL_.__PS_BASE_URI__.'modules/flexiblecustom/images/'; 
		else
			$this->livePath = _PS_BASE_URL_.__PS_BASE_URI__.'modules/flexiblecustom/images/';
		$this->secure_key = Tools::encrypt('ovic-'.$this->name);
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'OvicSoft';		
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Fashion Flexible Custom Module');
		$this->description = $this->l('Fashion Flexible Custom Module');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		//$this->cache = new FlexibleCustomFastCache();
	}	/*
    public function  __call($method, $args){
        if(!method_exists($this, $method)) {            
          return $this->hooks($method, $args);// call_user_func_array(array($this, $method), $args);
        }
    }	*/
	public function install($keep = true)
	{
	   if ($keep)
		{
			if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
				return false;
			else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
				return false;
			$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
			$sql = preg_split("/;\s*[\r\n]+/", trim($sql));
			foreach ($sql as $query)
				if (!Db::getInstance()->execute(trim($query)))
					return false;
		}
		if(!parent::install() 
            || !$this->registerHook('displayHeader') 
            || !$this->registerHook('displayHome') 
            || !$this->registerHook('displayFlexibleCategory')
            || !$this->registerHook('actionProductAdd')
            || !$this->registerHook('actionProductAttributeDelete')
            || !$this->registerHook('actionProductAttributeUpdate')
            || !$this->registerHook('actionProductDelete')
            || !$this->registerHook('actionProductSave')
            || !$this->registerHook('actionProductUpdate')
            || !$this->registerHook('actionCategoryAdd')
            || !$this->registerHook('actionCategoryDelete')
            || !$this->registerHook('displayHomeBottomContent')
            ) return false;
		if (!Configuration::updateGlobalValue('MOD_FLEXIBLE_CUSTOMS', 'FLEXIBLE_CUSTOMS')) return false;
        //$this->cache->cleanup();
        $this->clearCache();
        $this->installSampleData();		        
		$this->moduleUpdatePosition();	
		return true;
	}
    private function installSampleData(){
        $sql = "INSERT INTO `" . _DB_PREFIX_ .
            "flexiblecustom_modules` (`id`, `params`, `status`, `position`, `position_name`, `ordering`, `note`) VALUES
                (1,	'{\"moduleLayout\":\"option1\",\"displayOnly\":\"\",\"orderValue\":\"\",\"orderType\":\"\"}',	1,	190,	'displayFlexibleCategory',	1,	''),
                (2,	'{\"moduleLayout\":\"option1\",\"displayOnly\":\"\",\"orderValue\":\"\",\"orderType\":\"\"}',	1,	190,	'displayFlexibleCategory',	2,	''),
                (3,	'{\"moduleLayout\":\"option2\",\"displayOnly\":\"\",\"orderValue\":\"\",\"orderType\":\"\"}',	1,	9,	'displayHome',	1,	'');";
        $result = Db::getInstance()->execute($sql);
        $sql = "INSERT INTO `" . _DB_PREFIX_ .
            "flexiblecustom_modules_lang` (`module_id`, `id_lang`, `id_shop`, `module_title`, `banners`) VALUES ";
        foreach (Language::getLanguages(false) as $lang){
            $sql .= "(1,".$lang['id_lang'].",".$this->context->shop->id.",	'Women',	'[{\"image\":\"banner1.jpg\",\"link\":\"\",\"alt\":\"\"}]'),";
            $sql .= "(2,".$lang['id_lang'].",".$this->context->shop->id.",	'Top',	'[{\"image\":\"banner2.jpg\",\"link\":\"\",\"alt\":\"\"}]'),";
            $sql .= "(3,".$lang['id_lang'].",".$this->context->shop->id.",	'Top seller in',	''),";
        }
        $sql = rtrim($sql, ",").";";
        $result &= Db::getInstance()->execute($sql);
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
			FROM `'._DB_PREFIX_.'category` c
			INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').')
			INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = '.(int)$this->context->shop->id.')
			WHERE (c.`active` = 1 OR c.`id_category` = '.(int)Configuration::get('PS_HOME_CATEGORY').')
			AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').'
			AND c.id_category IN (
				SELECT id_category
				FROM `'._DB_PREFIX_.'category_group`
				WHERE `id_group` IN ('.pSQL(implode(', ', Customer::getGroupsStatic((int)$this->context->customer->id))).')
			)
			ORDER BY `level_depth` ASC, '.(Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'cs.`position`').' '.(Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC'));
        $cate_ids = array();
        foreach ($results as $row){
            $cate_ids[] = $row['id_category'];
        }
        $sql = "INSERT INTO `" . _DB_PREFIX_ .
            "flexiblecustom_module_groups` (`id`, `module_id`, `categoryId`, `productCount`, `maxItem`, `productIds`, `type`, `ordering`, `status`, `icon`, `iconActive`, `params`) VALUES
                (1,	1,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	1,	1,	'1-icon.jpg',	'1-icon.jpg',	'{\"displayOnly\":\"all\",\"orderValue\":\"add\",\"orderType\":\"desc\"}'),
                (2,	1,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	2,	1,	'2-icon.jpg',	'2-icon.jpg',	'{\"displayOnly\":\"all\",\"orderValue\":\"add\",\"orderType\":\"desc\"}'),
                (3,	1,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	3,	1,	'3-icon.jpg',	'3-icon.jpg',	'{\"displayOnly\":\"all\",\"orderValue\":\"add\",\"orderType\":\"desc\"}'),
                (4,	1,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	4,	1,	'4-icon.jpg',	'4-icon.jpg',	'{\"displayOnly\":\"all\",\"orderValue\":\"add\",\"orderType\":\"desc\"}'),
                (5,	1,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	5,	1,	'5-icon.jpg',	'5-icon.jpg',	'{\"displayOnly\":\"all\",\"orderValue\":\"add\",\"orderType\":\"desc\"}'),
                (7,	2,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	1,	1,	'7-icon.jpg',	'7-icon.jpg',	'{\"displayOnly\":\"all\",\"orderValue\":\"add\",\"orderType\":\"desc\"}'),
                (8,	2,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	2,	1,	'8-icon.jpg',	'8-icon.jpg',	'{\"displayOnly\":\"all\",\"orderValue\":\"add\",\"orderType\":\"desc\"}'),
                (9,	2,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	3,	1,	'9-icon.jpg',	'9-icon.jpg',	'{\"displayOnly\":\"all\",\"orderValue\":\"add\",\"orderType\":\"desc\"}'),
                (10,	2,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	4,	1,	'10-icon.jpg',	'10-icon.jpg',	'{\"displayOnly\":\"all\",\"orderValue\":\"add\",\"orderType\":\"desc\"}'),
                (11,	2,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	5,	1,	'11-icon.jpg',	'11-icon.jpg',	'{\"displayOnly\":\"all\",\"orderValue\":\"add\",\"orderType\":\"desc\"}'),
                (12,	3,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	1,	1,	'',	'',	'{\"displayOnly\":\"all\",\"orderValue\":\"sales\",\"orderType\":\"desc\"}'),
                (13,	3,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	2,	1,	'',	'',	'{\"displayOnly\":\"all\",\"orderValue\":\"sales\",\"orderType\":\"desc\"}'),
                (14,	3,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	3,	1,	'',	'',	'{\"displayOnly\":\"all\",\"orderValue\":\"sales\",\"orderType\":\"desc\"}');
                (16,	1,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	6,	1,	'16-icon.jpg',	'16-icon.jpg',	'{\"displayOnly\":\"all\",\"orderValue\":\"add\",\"orderType\":\"desc\"}'),
                (17,	2,	".$cate_ids[array_rand($cate_ids, 1)].",	4,	12,	'',	'auto',	6,	1,	'17-icon.jpg',	'17-icon.jpg',	'{\"displayOnly\":\"all\",\"orderValue\":\"add\",\"orderType\":\"desc\"}');";
        $result &= Db::getInstance()->execute($sql);
        $sql = "INSERT INTO `" . _DB_PREFIX_ .
            "flexiblecustom_module_group_lang` (`group_id`, `id_lang`, `id_shop`, `title`) VALUES ";
        foreach (Language::getLanguages(false) as $lang){
            $sql .= "(1,".$lang['id_lang'].",".$this->context->shop->id.",	'T-shirt'),";
            $sql .= "(2,".$lang['id_lang'].",".$this->context->shop->id.",	'Short'),";
            $sql .= "(3,".$lang['id_lang'].",".$this->context->shop->id.",	'Shoes'),";
            $sql .= "(4,".$lang['id_lang'].",".$this->context->shop->id.",	'Handbag'),";
            $sql .= "(5,".$lang['id_lang'].",".$this->context->shop->id.",	'Glasse'),";
            $sql .= "(6,".$lang['id_lang'].",".$this->context->shop->id.",	'Glasse'),";
            $sql .= "(7,".$lang['id_lang'].",".$this->context->shop->id.",	'T-shirt'),";
            $sql .= "(8,".$lang['id_lang'].",".$this->context->shop->id.",	'Short'),";
            $sql .= "(9,".$lang['id_lang'].",".$this->context->shop->id.",	'Shoes'),";
            $sql .= "(10,".$lang['id_lang'].",".$this->context->shop->id.",	'Handbag'),";
            $sql .= "(11,".$lang['id_lang'].",".$this->context->shop->id.",	'Glasse'),";
            $sql .= "(12,".$lang['id_lang'].",".$this->context->shop->id.",	'Clothing'),";
            $sql .= "(13,".$lang['id_lang'].",".$this->context->shop->id.",	'Bags'),";
            $sql .= "(14,".$lang['id_lang'].",".$this->context->shop->id.",	'Shoes'),";
        }
        $sql = rtrim($sql, ",").";";
        $result &= Db::getInstance()->execute($sql);
        return $result;
    }
	public function uninstall($keep = true)
	{	   
		if (!parent::uninstall()) return false;
        if($keep){
            if(!Db::getInstance()->execute('
			DROP TABLE IF EXISTS
			`'._DB_PREFIX_.'flexiblecustom_modules`,
			`'._DB_PREFIX_.'flexiblecustom_modules_lang`,
			`'._DB_PREFIX_.'flexiblecustom_module_groups`,
            `'._DB_PREFIX_.'flexiblecustom_module_group_lang`,
			`'._DB_PREFIX_.'flexiblecustom_module_group_products`')) return false;
        }		
        if (!Configuration::deleteByName('MOD_FLEXIBLE_CUSTOMS')) return false;
        $this->clearCache();		
		return true;
	}
	public function moduleUpdatePosition(){
		$items = DB::getInstance()->executeS("Select DISTINCT position_name From "._DB_PREFIX_."flexiblecustom_modules  Where `position_name` <> ''");
		if($items){
			foreach ($items as $key => $item) {
				$position = Hook::getIdByName($item['position_name']);
				DB::getInstance()->execute("Update "._DB_PREFIX_."flexiblecustom_modules Set position = '".$position."' Where `position_name` = '".$item['position_name']."'");
			}
		}
	}
	public function reset()
	{
		if (!$this->uninstall(false))
			return false;
		if (!$this->install(false))
			return false;
		return true;
	}
	public function getModuleBannerSrc($image, $check = true){
		if($image && file_exists($this->bannerPath.$image))
            return $this->livePath.'bn/'.$image;
        else
            if($check == true) 
                return '';
            else
                return $this->livePath.'default.jpg';
	}
	public function getGroupIconSrc($image, $check = true){
		if($image && file_exists($this->iconPath.$image))
            return $this->livePath.'icons/'.$image;
        else
            if($check == true) 
                return '';
            else
                return $this->livePath.'default.jpg';
	}
    public function buildSelectOption($arrContent = array(), $selected = ''){
        $keys = array_keys($arrContent);		
    	$content = '';					
		for($i = 0; $i<count($arrContent); $i++){		  
			if($keys[$i] === $selected){
				$content .= '<option value = "'.$keys[$i].'" selected="selected">'.$arrContent[$keys[$i]].'</option>';						
			}else{
				$content .= '<option value = "'.$keys[$i].'">'.$arrContent[$keys[$i]].'</option>';
			}					
		}
    	return $content;
    }
	public function getLangOptions($langId = 0){
        if(intval($langId) == 0) $langId = Context::getContext()->language->id;
        $items = DB::getInstance()->executeS("Select id_lang, name, iso_code From "._DB_PREFIX_."lang Where active = 1");
        $options = '';
        if($items){
            foreach($items as $item){
                if($item['id_lang'] == $langId){
                    $options .= '<option value="'.$item['id_lang'].'" selected="selected">'.$item['iso_code'].'</option>';
                }else{
                    $options .= '<option value="'.$item['id_lang'].'">'.$item['iso_code'].'</option>';
                }
            }
        }
        return $options;
    }
	public function getAllLanguages(){
        $langId = Context::getContext()->language->id;
        $items = DB::getInstance()->executeS("Select id_lang, name, iso_code From "._DB_PREFIX_."lang Where active = 1 Order By id_lang");
        $arr = array();
        if($items){
            foreach($items as $i=>$item){
            	$objItem = new stdClass();
				$objItem->id = $item['id_lang'];
				$objItem->iso_code = $item['iso_code'];
                if($item['id_lang'] == $langId){
                    $objItem->active = 1;
                }else{
                    $objItem->active = 0;
                }
				$arr[$i] = $objItem;
            }
        }
        return $arr;
    }
	public function getPositionOptions($selected = 0){
		$options = '';		
		if(self::$arrPosition){			
			foreach(self::$arrPosition as $value){
				$hookId = Hook::getIdByName($value);
				if($hookId == $selected) $options .='<option selected="selected" value="'.$hookId.'">'.$value.'</option>';
				else $options .='<option value="'.$hookId.'">'.$value.'</option>';
			}
		}		
        return $options;
		/*
        $positionOptions = '';
        //$items = Db::getInstance()->executeS("Select id_hook From "._DB_PREFIX_."hook_module Where id_module = ".$this->id);
        $items = Db::getInstance()->executeS("Select id_hook From "._DB_PREFIX_."ovic_backup_hook_module Where id_module = ".$this->id);
        $options = '';
        if($items){
            foreach($items as $item){
                if($selected == $item['id_hook']) $options .= '<option selected="selected" value="'.$item['id_hook'].'">'.Hook::getNameById($item['id_hook']).'</option>';
                else $options .= '<option value="'.$item['id_hook'].'">'.Hook::getNameById($item['id_hook']).'</option>';
            }
        }
        return $options; 
		*/
    }
	public function getLayoutOptions($selected=''){
		$options = '';
		foreach ($this->moduleLayout as $key => $value) {
			if($selected == $key) $options .= '<option selected="selected" value="'.$key.'">'.$value.'</option>';
			else $options .= '<option value="'.$key.'">'.$value.'</option>';
		}
		return $options;
	}
	public function getOrderValueOptions($selected=''){
		$options = '';
		foreach ($this->orderValue as $key => $value) {
			if($selected == $key) $options .= '<option selected="selected" value="'.$key.'">'.$value.'</option>';
			else $options .= '<option value="'.$key.'">'.$value.'</option>';
		}
		return $options;
	}
	public function getOrderTypeOptions($selected=''){
		$options = '';
		foreach ($this->orderType as $key => $value) {
			if($selected == $key) $options .= '<option selected="selected" value="'.$key.'">'.$value.'</option>';
			else $options .= '<option value="'.$key.'">'.$value.'</option>'; 
		}
		return $options;
	}
	public function getCategoryTypeOptions($selected = ''){
		$options = '';
		foreach ($this->categoryType as $key => $value) {
			if($selected == $key) $options .= '<option selected="selected" value="'.$key.'">'.$value.'</option>';
			else $options .= '<option value="'.$key.'">'.$value.'</option>';
		}
		return $options;
	}
	public function getAllCategories($langId, $shopId, $parentId = 0, $sp='', $arr=null){
        if($arr == null) $arr = array();
        $items = DB::getInstance()->executeS("Select c.id_category, cl.name From "._DB_PREFIX_."category as c Inner Join "._DB_PREFIX_."category_lang as cl On c.id_category = cl.id_category Where c.id_parent = $parentId AND cl.id_lang = ".$langId." AND id_shop = ".$shopId);
        if($items){
            foreach($items as $item){
                $arr[] = array('id_category'=>$item['id_category'], 'name'=>$item['name'], 'sp'=>$sp);
                $arr = $this->getAllCategories($langId, $shopId, $item['id_category'], $sp.'- ', $arr);
            }
        }
        return $arr;
    }
	public function getCategoryIds($parentId = 0, $arr=null){
        if($arr == null) $arr = array();
        $items = DB::getInstance()->executeS("Select id_category From "._DB_PREFIX_."category Where id_parent = $parentId");
        if($items){
            foreach($items as $item){
                $arr[] = $item['id_category'];
                $arr = $this->getCategoryIds($item['id_category'], $arr);
            }
        }
        return $arr;
    }
	public function getCategoryOptions($selected = 0, $parentId = 0){
        $langId = Context::getContext()->language->id;
        $shopId = Context::getContext()->shop->id;
        $options = '';
        if($parentId <=0) $parentId = Configuration::get('PS_HOME_CATEGORY');		
        $items = $this->getAllCategories($langId, $shopId, $parentId, '- ', null);        
        if($items){
            foreach($items as $item){
                if($item['id_category'] == $selected) $options .='<option selected="selected" value="'.$item['id_category'].'">'.$item['sp'].$item['name'].'</option>';
                else $options .='<option value="'.$item['id_category'].'">'.$item['sp'].$item['name'].'</option>';
            }
        }
        return  $options;
    }
	public function getDisplayOptions($selected = ''){
		$options = '';
		foreach ($this->display as $key => $value) {
			if($selected == $key) $options .= '<option selected="selected" value="'.$key.'">'.$value.'</option>';
			else $options .= '<option value="'.$key.'">'.$value.'</option>';
		}
		return $options;
	}
	public function getModuleByLang($id, $langId=0, $shopId=0){
		if($langId == 0) $langId = Context::getContext()->language->id;
        if($shopId == 0) $shopId = Context::getContext()->shop->id;
		$itemLang = DB::getInstance()->getRow("Select module_title, banners From "._DB_PREFIX_."flexiblecustom_modules_lang Where module_id = ".$id." AND `id_lang` = ".$langId." AND `id_shop` = ".$shopId);
		if(!$itemLang) $itemLang = array('module_title'=>'', 'banners'=>'');
		return $itemLang;
	}
	public function getGroupByLang($id, $langId=0, $shopId=0){
		if($langId == 0) $langId = Context::getContext()->language->id;
        if($shopId == 0) $shopId = Context::getContext()->shop->id;
		$itemLang = DB::getInstance()->getRow("Select title From "._DB_PREFIX_."flexiblecustom_module_group_lang Where group_id = ".$id." AND `id_lang` = ".$langId." AND `id_shop` = ".$shopId);
		if(!$itemLang) $itemLang = array('title'=>'');
		return $itemLang;
	}
	static function getCategoryNameById($id, $langId, $shopId){
        return DB::getInstance()->getValue("Select name From "._DB_PREFIX_."category_lang Where id_category = $id AND `id_shop` = '$shopId' AND `id_lang` = '$langId'");        
    }
	public function ovicModuleRenderForm($id = 0){
		$shopId = Context::getContext()->shop->id;
		$item = DB::getInstance()->getRow("Select * From "._DB_PREFIX_."flexiblecustom_modules Where id = ".$id);
		if(!$item){
			$item = array('id'=>0, 'position'=>0, 'status'=>1, 'ordering'=>1, 'params'=>'', 'note'=>'');
			$params = new stdClass();
			$params->moduleLayout = 'option1';
			$params->displayOnly = 'all';
			$params->orderValue = 'add';
			$params->orderType = 'desc';
		}else{
			$params = json_decode($item['params']);
		}
		$languages = $this->getAllLanguages();
		$inputTitle = '';
		$html = array('config'=>'', 'banners'=>'');
		$langActive = '<input type="hidden" id="moduleLangActive" value="0" />';
		$banners = '';
		if($languages){
			foreach ($languages as $key => $language) {				
				$itemLang = $this->getModuleByLang($id, $language->id, $shopId);
				if($language->active == '1'){
					$html['banners'] .= '<table class="table table-hover tbl-banners tbl-banners-lang-'.$language->id.'" id="tbl-banners-lang-'.$language->id.'"><thead><tr><th width="100">'.$this->l('Image').'</th><th>'.$this->l('File name').'</th><th>'.$this->l('Banner link').'</th><th>'.$this->l('Image alt').'</th><th>#</th></tr></thead><tbody>';
					if($itemLang['banners']){
						$itemBanners = json_decode($itemLang['banners']);
						if($itemBanners){
							foreach($itemBanners as $itemBanner){
								$html['banners'] .= '<tr>
                                                    <td><div style="width: 100px"><img class="img-responsive" src="'.$this->getModuleBannerSrc($itemBanner->image).'" /></div></td>
                                                    <td><input type="text" name="bannerNames'.$language->id.'[]" value="'.$itemBanner->image.'" class="form-control" /></td>
                                                    <td><input type="text" name="bannerLink'.$language->id.'[]" value="'.$itemBanner->link.'" class="form-control"  /></td>
                                                    <td><input type="text" name="bannerAlt'.$language->id.'[]" value="'.$itemBanner->alt.'" class="form-control"  /></td>
                                                    <td class="pointer dragHandle center" ><div class="dragGroup"><a href="javascript:void(0)" class="lik-banner-del color-red" title="Delete banner">Del</a></div></td>	                                                        
                                                </tr>';
							}
						}
					}					
					$html['banners'] .= '</tbody></table>';
					$langActive = '<input type="hidden" id="moduleLangActive" value="'.$language->id.'" />';
					$inputTitle .= '<input type="text" value="'.$itemLang['module_title'].'" name="titles[]" class="form-control module-lang-'.$language->id.'" />';					
				}else{					
					$inputTitle .= '<input type="text" value="'.$itemLang['module_title'].'" name="titles[]" class="form-control module-lang-'.$language->id.'" style="display:none" />';
					$html['banners'] .= '<table style="display:none" class="table table-hover tbl-banners tbl-banners-lang-'.$language->id.'" id="tbl-banners-lang-'.$language->id.'"><thead><tr><th width="100">'.$this->l('Image').'</th><th>'.$this->l('File name').'</th><th>'.$this->l('Banner link').'</th><th>'.$this->l('Image alt').'</th><th>#</th></tr></thead><tbody>';
					if($itemLang['banners']){
						$itemBanners = json_decode($itemLang['banners']);
						if($itemBanners){
							foreach($itemBanners as $itemBanner){
								$html['banners'] .= '<tr>
                                                    <td>
                                                        <div style="width: 100px"><img class="img-responsive" src="'.$this->getModuleBannerSrc($itemBanner->image).'"  /></div>                                                                                                                        
                                                    </td>
                                                    <td><input type="text" name="bannerNames'.$language->id.'[]" value="'.$itemBanner->image.'" class="form-control" /></td>
                                                    <td><input type="text" name="bannerLink'.$language->id.'[]" value="'.$itemBanner->link.'" class="form-control"  /></td>
                                                    <td><input type="text" name="bannerAlt'.$language->id.'[]" value="'.$itemBanner->alt.'" class="form-control"  /></td>
                                                    <td class="pointer dragHandle center" ><div class="dragGroup"><a href="javascript:void(0)" class="lik-banner-del color-red" title="Delete banner">Del</a></div></td>	
                                                </tr>';
							}
						}
					}					
					$html['banners'] .= '</tbody></table>';
				}				
			}
		}
		$langOptions = $this->getLangOptions();
		$html['config'] = '<input type="hidden" name="moduleId" value="'.$item['id'].'" />';
		$html['config'] .= '<input type="hidden" name="action" value="saveModule" />';
		$html['config'] .= '<input type="hidden" name="secure_key" value="'.$this->secure_key.'" />';
		$html['config'] .= $langActive;
		$html['config'] .= '<div class="form-group"><div class="col-lg-12 "><div class="col-lg-12 "><label>'.$this->l('Module name').'</label></div><div class="col-sm-10">'.$inputTitle.'</div><div class="col-sm-2"><select class="module-lang" onchange="moduleChangeLanguage(this.value)">'.$langOptions.'</select></div></div></div>';
		$html['config'] .= '<div class="form-group"><div class="col-lg-12 "><div class="col-lg-12 "><label>'.$this->l('Module position').'</label></div><div class="col-lg-12 "><select name="position" class="form-control">'.$this->getPositionOptions($item['position']).'</select></div></div></div>';
		$html['config'] .= '<div class="form-group"><div class="col-lg-12 "><div class="col-lg-12 "><label>'.$this->l('Module layout').'</label></div><div class="col-lg-12 "><select name="moduleLayout">'.$this->getLayoutOptions($params->moduleLayout).'</select></div></div></div>';
		//$html['config'] .= '<div class="form-group"><div class="col-lg-12 "><div class="col-lg-12 "><label>'.$this->l('Product display type').'</label></div><div class="col-lg-12 "><select class="form-control" name="displayOnly">'.$this->getDisplayOptions($params->displayOnly).'</select></div></div></div>';
		//$html['config'] .= '<div class="form-group"><div class="col-lg-12 "><div class="col-lg-12 "><label>'.$this->l('Product field order').'</label></div><div class="col-lg-12 "><select class="form-control" name="orderValue">'.$this->getOrderValueOptions($params->orderValue).'</select></div></div></div>';
		//$html['config'] .= '<div class="form-group"><div class="col-lg-12 "><div class="col-lg-12 "><label>'.$this->l('Module order type').'</label></div><div class="col-lg-12 "><select class="form-control" name="orderType">'.$this->getOrderTypeOptions($params->orderType).'</select></div></div></div>';          
		return $html;
	}
	public function getCategoryByLang($id, $langId=0, $shopId=0){
		if($langId == 0) $langId = Context::getContext()->language->id;
        if($shopId = 0) $shopId = Context::getContext()->shop->id;
        $itemLang = DB::getInstance()->getRow("Select name From "._DB_PREFIX_."flexiblecustom_module_category Where category_id = ".$id." AND `id_lang` = ".$langId." AND `id_shop` = ".$shopId);
		if(!$itemLang) $itemLang = array('name'=>'');
		return $itemLang;
	}
	public function ovicGroupRenderForm($id = 0){
		$shopId = Context::getContext()->shop->id;
		$item = DB::getInstance()->getRow("Select * From "._DB_PREFIX_."flexiblecustom_module_groups Where id = ".$id);
		if(!$item){
			$item = array('id'=>0, 'module_id'=>0, 'categoryId'=>0, 'productCount'=>3, 'maxItem'=>12, 'productIds'=>'', 'type'=>'auto', 'ordering'=>1, 'status'=>1, 'icon'=>'', 'iconActive'=>'', 'params'=>'');
			$params = new stdClass();
			$params->displayOnly = 'all';
			$params->orderValue = 'add';
			$params->orderType = 'desc';
		}else{
			if($item['params']){
				$params = json_decode($item['params']);	
			}else{
				$params = new stdClass();
				$params->displayOnly = 'all';
				$params->orderValue = 'add';
				$params->orderType = 'desc';	
			}			
		}
		$languages = $this->getAllLanguages();
		$inputTitle = '';		
		$langActive = '<input type="hidden" id="groupLangActive" value="0" />';
		if($languages){
			foreach ($languages as $key => $language) {				
				$itemLang = $this->getGroupByLang($id, $language->id, $shopId);
				if($language->active == '1'){
					$langActive = '<input type="hidden" id="groupLangActive" value="'.$language->id.'" />';
					$inputTitle .= '<input type="text" value="'.$itemLang['title'].'" name="titles[]" class="form-control group-lang-'.$language->id.'" />';					
				}else{
					$inputTitle .= '<input type="text" value="'.$itemLang['title'].'" name="titles[]" class="form-control group-lang-'.$language->id.'" style="display:none" />';					
				}				
			}
		}
		$langOptions = $this->getLangOptions();
		$html = '<input type="hidden" name="groupId" value="'.$item['id'].'" />';
		$html .= '<input type="hidden" name="action" value="saveGroup" />';
		$html .= '<input type="hidden" name="secure_key" value="'.$this->secure_key.'" />';
		$html .= $langActive;
		$html .= '<div class="form-group">
                    <label class="control-label col-lg-3">'.$this->l('Group title').'</label>
				    <div class="col-lg-9 form-group">
                        <div class="col-sm-10">'.$inputTitle.'</div>
                        <div class="col-sm-2">
                            <select id="group-lang" onchange="groupChangeLanguage(this.value)">'.$langOptions.'</select>
                        </div>                        
                    </div>
                </div>';
		$html .= '<div class="form-group clearfix">
                    <label class="control-label col-lg-3">'.$this->l('Icon').'</label>
                    <div class="col-lg-9 ">                        
                        <div class="input-group mar-t-10">
                            <input type="text" class="form-control" id="category-icon" name="icon" value="'.$item['icon'].'"  placeholder="Icon" readonly="readonly" />
                            <span class="input-group-btn">
                                <button id="group-icon" type="button" class="btn btn-default"><i class="icon-folder-open"></i></button>
                            </span>
                        </div>
                    </div>                    
                </div>';
		$html .= '<div class="form-group clearfix">
                    <label class="control-label col-lg-3">'.$this->l('Active icon').'</label>
                    <div class="col-lg-9 ">                        
                        <div class="input-group mar-t-10">
                            <input type="text" class="form-control" id="category-iconActive" name="iconActive" value="'.$item['iconActive'].'" placeholder="Active icon" readonly="readonly" />
                            <span class="input-group-btn">
                                <button id="group-iconActive" type="button" class="btn btn-default"><i class="icon-folder-open"></i></button>
                            </span>
                        </div>
                    </div>                    
                </div>';
		$html .= '<div class="form-group">
                    <label class="control-label col-lg-3">'.$this->l('Type').'</label>
				    <div class="col-lg-9 "><select name="type" class="form-control" onchange="groupChangeType(this.value)">'.$this->getCategoryTypeOptions($item['type']).'</select></div>
                </div>';
		$html .= '<div class="form-group">
                    <label class="control-label col-lg-3">'.$this->l('Categories').'</label>
				    <div class="col-lg-9 ">
                        <select class="form-control" name="categoryId">'.$this->getCategoryOptions($item['categoryId'], 0).'</select>
                    </div>
                </div>';
		/*
		$html .= '<div class="form-group clearfix type-auto" style="display: '.($item['type'] == 'auto' ? 'block' : 'none').'">
                    <label class="control-label col-lg-3">'.$this->l('Load count').'</label>
				    <div class="col-lg-3"><input type="text" name="productCount" value="'.$item['productCount'].'" class="form-control" /></div>
                </div>';
		 * 
		 */
		$html .= '<div class="form-group type-auto" style="display: '.($item['type'] == 'auto' ? 'block' : 'none').'">
					<label class="control-label col-lg-3">'.$this->l('Product type').'</label>
				    <div class="col-lg-9"><select class="form-control" name="displayOnly">'.$this->getDisplayOptions($params->displayOnly).'</select></div>
				  </div>';
		$html .= '<div class="form-group type-auto" style="display: '.($item['type'] == 'auto' ? 'block' : 'none').'">
					<label class="control-label col-lg-3">'.$this->l('Product field order').'</label>
				    <div class="col-lg-9"><select class="form-control" name="orderValue">'.$this->getOrderValueOptions($params->orderValue).'</select></div>
				</div>';
		$html .= '<div class="form-group type-auto" style="display: '.($item['type'] == 'auto' ? 'block' : 'none').'">
					<label class="control-label col-lg-3">'.$this->l('Product order').'</label>
				    <div class="col-lg-9"><select class="form-control" name="orderType">'.$this->getOrderTypeOptions($params->orderType).'</select></div>				   
				</div>';
		$html .= '<div class="form-group clearfix type-auto" style="display: '.($item['type'] == 'auto' ? 'block' : 'none').'">
                    <label class="control-label col-lg-3">'.$this->l('Max item').'</label>
				    <div class="col-lg-3"><input type="text" name="maxItem" value="'.$item['maxItem'].'" class="form-control" /></div>
                </div>';
		return $html;
	}
	public function getAllModule(){
		$langId = Context::getContext()->language->id;
        $shopId = Context::getContext()->shop->id;
		$items = DB::getInstance()->executeS("Select * From "._DB_PREFIX_."flexiblecustom_modules Order By position, ordering");
        $html = '';
        if($items){
            foreach($items as $item){            	
            	$itemLang = $this->getModuleByLang($item['id'], $langId, $shopId);
                if($item['status'] == "1"){
                    $status = '<a title="Enabled" class="list-action-enable action-enabled lik-module-status" item-id="'.$item['id'].'" value="'.$item['status'].'"><i class="icon-check"></i></a>';
                }else{
                    $status = '<a title="Disabled" class="list-action-enable action-disabled lik-module-status" item-id="'.$item['id'].'" value="'.$item['status'].'"><i class="icon-check"></i></a>';
                }
                $params = json_decode($item['params']);                
                $html .= '<tr id="mod_'.$item['id'].'">
    				    <td><a class="module-item" href="javascript:void(0)" item-id="'.$item['id'].'">'.$itemLang['module_title'].'</a></td>                        
                        <td class="center">'.$this->moduleLayout[$params->moduleLayout].'</td>
                        <td class="center">'.Hook::getNameById($item['position']).'</td>								
    				    <td class="pointer dragHandle center" ><div class="dragGroup"><div class="positions">'.$item['ordering'].'</div></div></td>		
    				    <td class="center">'.$status.'</td>                        
                        <td class="center">
                            <a href="javascript:void(0)" item-id="'.$item['id'].'" class="lik-module-edit"><i class="icon-edit"></i></a>&nbsp;
                            <a href="javascript:void(0)" item-id="'.$item['id'].'" class="lik-module-delete"><i class="icon-trash" ></i></a>
                        </td>
                    </tr>';
            }
        }
		return $html;
	}
	public function getAllGroup($moduleId){
		$langId = Context::getContext()->language->id;
        $shopId = Context::getContext()->shop->id;
		$items = DB::getInstance()->executeS("Select * From "._DB_PREFIX_."flexiblecustom_module_groups Where module_id = '$moduleId' Order By ordering");
		$html = '';
		if($items){			
			foreach($items as $item){
				$itemLang = $this->getGroupByLang($item['id'], $langId, $shopId);                   
                if($item['status'] == "1"){
                    $status = '<a title="Enabled" class="list-action-enable action-enabled lik-group-status" item-id="'.$item['id'].'" value="'.$item['status'].'"><i class="icon-check"></i></a>';
                }else{
                    $status = '<a title="Disabled" class="list-action-enable action-disabled lik-group-status" item-id="'.$item['id'].'" value="'.$item['status'].'"><i class="icon-check"></i></a>';
                }
				if($item['type'] == 'manual'){
					$item['maxItem'] = '-';
					$item['productCount'] = '-';
				}
                $html .= '<tr id="gro_'.$item['id'].'">
                    <td><a class="group-item" href="javascript:void(0)" item-id="'.$item['id'].'" item-type="'.$item['type'].'">'.$itemLang['title'].'</a></td>                        
                    <td>'.$this->getCategoryNameById($item['categoryId'], $langId, $shopId).'</td>
                    <td class="center">'.$this->categoryType[$item['type']].'</td>
                    <td class="center">'.$item['maxItem'].'</td>
				    <td class="pointer dragHandle center"><div class="dragGroup"><div class="positions">'.$item['ordering'].'</div></div></td>		
				    <td class="center">'.$status.'</td>
                    <td class="center">
                        <a href="javascript:void(0)" item-id="'.$item['id'].'" class="lik-group-edit"><i class="icon-edit"></i></a>&nbsp;
                        <a href="javascript:void(0)" item-id="'.$item['id'].'" class="lik-group-delete"><i class="icon-trash" ></i></a>
                    </td>
                </tr>';
			}
		}
		return $html;
	}
	public function getContent()
	{	   
	   // lang default       
		$this->context->controller->addJS(($this->_path).'js/back-end/common.js');                
        $this->context->controller->addJS(($this->_path).'js/back-end/ajaxupload.3.5.js');
		$this->context->controller->addJS(($this->_path).'js/back-end/jquery.serialize-object.min.js');
		$this->context->controller->addJS(_PS_BASE_URL_.__PS_BASE_URI__.'js/jquery/plugins/jquery.tablednd.js');
        $this->context->controller->addCSS(($this->_path).'css/back-end/style.css');
        $langId = Context::getContext()->language->id;
        $shopId = Context::getContext()->shop->id;        
        $this->context->smarty->assign(array(
            'langOptions'=>$this->getLangOptions(),
            'baseModuleUrl'=> _PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name,
            'moduleId'=>$this->id,
            'content'=>$this->getAllModule(),
            'secure_key'=>$this->secure_key,
            'moduleForm'=>$this->ovicModuleRenderForm(),
            'groupForm'=>$this->ovicGroupRenderForm()
        ));
		return $this->display(__FILE__, 'views/templates/admin/modules.tpl');
	}
    public function hookdisplayHeader()
	{
		//$imageSize =  Image::getSize(ImageType::getFormatedName('home'));
        // Call in option2.css		
		$this->context->controller->addCSS(($this->_path).'css/front-end/option1.style.css');
		//$this->context->controller->addCSS(($this->_path).'css/front-end/option2.style.css');
        $this->context->controller->addJS(($this->_path).'js/front-end/option1.common.js');
		//$this->context->controller->addJS(($this->_path).'js/front-end/option2.common.js');
        $this->context->controller->addJS(($this->_path).'js/front-end/jquery.actual.min.js');
		include_once (_PS_CONTROLLER_DIR_.'front/CompareController.php');
		if(!$this->compareProductIds = CompareProduct::getCompareProducts($this->context->cookie->id_compare)) $this->compareProductIds = array();
        $this->context->smarty->assign(array(
            'comparator_max_item' => (int)(Configuration::get('PS_COMPARATOR_MAX_ITEM')),            
            'baseModuleUrl'=> _PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name,
            'imageSize'=>$this->imageHomeSize,
        	'h_per_w'=> round($this->imageHomeSize['height']/$this->imageHomeSize['width'], 2),
            'compareProductIds'=>$this->compareProductIds                 
        )); 
	}		
    public function hookDisplayFlexibleCategory($params)	{		
        return $this->hooks('displayFlexibleCategory', $params);	
    }
    public function hookDisplayHome($params)	{		
        return $this->hooks('displayHome', $params);	
    }
    public function hookActionProductAdd($params)	{		
        $this->clearCache();
        return true;	
    }
    public function hookActionProductAttributeDelete($params)	{		
        return $this->hookActionProductAdd();	
    }
    public function hookActionProductAttributeUpdate($params)	{		
        return $this->hookActionProductAdd();	
    }
    public function hookActionProductDelete($params)	{		
        return $this->hookActionProductAdd();	
    }
    public function hookActionProductSave($params)	{		
        return $this->hookActionProductAdd();	
    }
    public function hookActionProductUpdate($params)	{		
        return $this->hookActionProductAdd();	
    }
    public function hookActionCartSave($params)	{		
        return $this->hookActionProductAdd();	
    }
    public function hookActionCategoryAdd($params)	{		
        return $this->hookActionProductAdd();	
    }
    public function hookActionCategoryDelete($params)	{		
        return $this->hookActionProductAdd();	
    }
    public function hookdisplayHomeBottomContent($params){
        return $this->hooks('displayHomeBottomContent',$params);
    }
    public function hooks($hookName, $param){		
        $langId = Context::getContext()->language->id;
        $shopId = Context::getContext()->shop->id;        
        $hookName = str_replace('hook','', $hookName);        
        $hookId = Hook::getIdByName($hookName);
		if($hookId <=0) return '';		
		$page_name = Dispatcher::getInstance()->getController();
		$page_name = (preg_match('/^[0-9]/', $page_name) ? 'page_'.$page_name : $page_name);
		$cacheKey = 'flexiblecustom|'.$langId.'|'.$hookId.'|'.$page_name;	
        if (!$this->isCached('flexiblecustom.tpl', $cacheKey)){
            $items = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS("Select DISTINCT m.*, ml.`module_title`, ml.`banners` From "._DB_PREFIX_."flexiblecustom_modules as m INNER JOIN "._DB_PREFIX_."flexiblecustom_modules_lang AS ml On m.id = ml.module_id Where m.status = 1 AND m.position = ".$hookId." AND id_lang = ".$langId." AND id_shop = ".$shopId." Order By ordering");
    		$results = array();		
            if($items){            
                foreach($items as $i=>$item){
                	$moduleParams = json_decode($item['params']);
    				$results[] = array(
    					'moduleId'=>$item['id'], 
    					'moduleTitle'=>$item['module_title'], 
    					'moduleLayout'=>$moduleParams->moduleLayout, 
    					'groups'=>$this->buildModule($item, 'modulecontent-'.$item['id'].'|'.$cacheKey)
    					);
                }            
            }
            $this->context->smarty->assign(array(
    			'modules'=>$results			
    		));	
        }
        return $this->display(__FILE__, 'flexiblecustom.tpl', $cacheKey);        
    }
    public function buildModule($module, $cacheKey=''){		
    	$langId = Context::getContext()->language->id;
        $shopId = Context::getContext()->shop->id;    
    	$moduleParams = json_decode($module['params']);
        //$moduleParams->moduleLayout = 'option1';
        if (!$this->isCached($moduleParams->moduleLayout.'.layout.tpl', $cacheKey)){
			$items = array();
			//$cacheKey = 'module-'.$langId.'-'.$shopId.$module['id'];		
			//$items = @$this->cache->get($cacheKey);
			//if(!$items){
				$items = DB::getInstance()->executeS("Select g.*, gl.title From "._DB_PREFIX_."flexiblecustom_module_groups AS g Inner Join "._DB_PREFIX_."flexiblecustom_module_group_lang AS gl On g.id = gl.group_id Where g.status = 1 AND module_id = ".$module['id']." AND gl.id_lang = ".$langId." AND gl.id_shop = ".$shopId." Order By g.ordering");
				if($items){
					foreach($items as &$item){
						$item['products'] = $this->getGroupProducts($item, $module);
					}
				}else return "";
				//$this->cache->set($cacheKey, $items, $this->cacheTime);
			//}
			//if(!$this->cache->exists('flexible-custom-banner-'.$langId.'-'.$shopId.'-'.$module['id'])){
				if($module['banners']){
					$banners = json_decode($module['banners']);
					if($banners){
						foreach($banners as $key=>&$banner){
							$src = $this->getModuleBannerSrc($banner->image, true);
							if($src){
								$banner->src = $src;
								if(!$banner->link) $banner->link = '#';
								if(!$banner->alt) $banner->alt = $module['module_title'];
							}else{
								unset($banner);
							}
						}
						//if($banners) $this->cache->set('flexible-custom-banner-'.$langId.'-'.$shopId.'-'.$module['id'], $banners, $this->cacheTime);                    
					}else{
						$banners = array();
					}
				}else{
					$banners = array();
				} 
			//}else{
			//    $banners = $this->cache->get('flexible-custom-banner-'.$langId.'-'.$shopId.'-'.$module['id']);
			//}
			$this->context->smarty->assign(array(
				'groups'=>$items,
				'moduleName'=>$module['module_title'],
				'moduleId'=>$module['id'],
				'livePath'=>$this->livePath,
				'moduleLayout'=>$moduleParams->moduleLayout,
				'banners'=>$banners
			));
		}
		return $this->display(__FILE__, $moduleParams->moduleLayout.'.layout.tpl', $cacheKey);		
    }
    public function getGroupProducts($group, $module){
    	$langId = Context::getContext()->language->id;
        $shopId = Context::getContext()->shop->id;
		$params = json_decode($group['params']);
		$arrSubCategory = $this->getCategoryIds($group['categoryId']);
        $arrSubCategory[] = $group['categoryId'];        
        if($group['type'] == 'auto'){
            if($params->orderValue == 'sales'){
                //$total = Functions::getProductsOrderSales($langId, $arrSubCategory, $params, $moduleCategoryItem['productCount'], true, false, true);
                $products =  $this->getProductsOrderSales($langId, $arrSubCategory, $params, $group['maxItem'], false, true, false, 0);
				if(!$products) $products = array();                
            }elseif($params->orderValue == 'price'){
                //$total = Functions::getProductsOrderPrice($langId, $arrSubCategory, $params, $moduleCategoryItem['productCount'], true, false, true);
                $products = $this->getProductsOrderPrice($langId, $arrSubCategory, $params, $group['maxItem'], false, true, false, 0);
				if(!$products) $products = array();
            }elseif($params->orderValue == 'discount'){
               //$total = Functions::getProductsOrderSpecial($langId, $arrSubCategory, $params, $moduleCategoryItem['productCount'], true, false, true);
                $products = $this->getProductsOrderSpecial($langId, $arrSubCategory, $params, $group['maxItem'], false, true, false, 0);
				if(!$products) $products = array();
            }elseif($params->orderValue == 'add'){
               //$total = Functions::getProductsOrderAddDate($langId, $arrSubCategory, $params, $moduleCategoryItem['productCount'], true, false, true);
                $products = $this->getProductsOrderAddDate($langId, $arrSubCategory, $params, $group['maxItem'], false, true, false, 0);
				if(!$products) $products = array();
            }elseif($params->orderValue == 'rand'){
                //$total = $moduleCategoryItem['productCount'];
                $products = $this->getProductsOrderRand($langId, $arrSubCategory, $params, $group['maxItem'], false, true);
				if(!$products) $products = array();
            }
        }else{
        	$products = array();
            $items = DB::getInstance()->executeS("Select product_id, ordering From "._DB_PREFIX_."flexiblecustom_module_group_products Where module_id = ".$module['id']." AND group_id = ".$group['id']." Order By ordering");
            if($items){                
                foreach($items as $item){
                    $products[] = $this->getProductById($langId, $item['product_id'], false, true);
                }    
            }
        }	
    	return $products;
	}
	function getCacheId($name=null)
	{
		return parent::getCacheId('flexiblecustom|'.$name);
	}
	function clearCache($cacheKey = null)
	{
		if($cacheKey){
			parent::_clearCache('flexiblecustom.tpl', $cacheKey);
			parent::_clearCache('default.layout.tpl', $cacheKey);
		}else{
			parent::_clearCache('flexiblecustom.tpl');
			parent::_clearCache('default.layout.tpl');
		}
	   //Tools::clearCache();		
	}
   public function getProductsOrderSales($id_lang, $arrCategory = array(), $params = null, $limit, $short = true, $getProperties = true, $total=false, $offset = 0){	
        $context = Context::getContext();
        $order_by = 'sales';
        $order_way = $params->orderType;
        $where = "";
        if($arrCategory) $catIds = implode(', ', $arrCategory);        
        if($params->displayOnly == 'condition-new') $where .= " AND p.condition = 'new'";
        elseif($params->displayOnly == 'condition-used') $where .= " AND p.condition = 'used'";
        elseif($params->displayOnly == 'condition-refurbished') $where .= " AND p.condition = 'refurbished'";
        if (Group::isFeatureActive())
		{
			$groups = FrontController::getCurrentCustomerGroups();
			$where .= 'AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_group` cg
				LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
				WHERE cp.id_category IN ('.$catIds.') AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').'
			)';
		}else{
            $where .= 'AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_product` cp 
				WHERE cp.id_category IN ('.$catIds.'))';
		}
		if($total == true){
			$sql = 'SELECT COUNT(p.id_product)
				FROM `'._DB_PREFIX_.'product_sale` ps
				LEFT JOIN `'._DB_PREFIX_.'product` p ON ps.`id_product` = p.`id_product`
				'.Shop::addSqlAssociation('product', 'p', false).'
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'				
				WHERE product_shop.`active` = 1 AND p.`visibility` != \'none\'  '.$where;
				return (int) DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);				
		}        	
        if($short == true){
        	$interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;
            $sql = 'SELECT p.id_product, p.on_sale, p.price, p.id_category_default, p.reference, p.ean13, p.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`name`, pl.`link_rewrite`, ps.`quantity` AS sales, MAX(image_shop.`id_image`) id_image, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.$interval.' DAY)) > 0 AS new';
        }else{
            $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, MAX(product_attribute_shop.id_product_attribute) id_product_attribute, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, ps.`quantity` AS sales , MAX(image_shop.`id_image`) id_image,
					il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new, product_shop.price AS orderprice';
        }        
        $sql .= ' FROM `'._DB_PREFIX_.'product_sale` ps
				LEFT JOIN `'._DB_PREFIX_.'product` p
			 	   ON p.`id_product` = ps.`id_product`
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i
					ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE
                    product_shop.`id_shop` = '.(int)$context->shop->id.' 
                    AND product_shop.`visibility` IN ("both", "catalog") '.$where.' 
                    AND product_shop.`active` = 1 
                    GROUP BY product_shop.id_product
                    ORDER BY `'.pSQL($order_by).'` '.pSQL($order_way).' Limit '.$offset.', '.$limit;		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if (!$result) return false;
        if($getProperties == false) return $result;
		return Product::getProductsProperties($id_lang, $result);
	}
	public static function getProductRatings($id_product)
	{
		$validate = Configuration::get('PRODUCT_COMMENTS_MODERATE');
		$sql = 'SELECT (SUM(pc.`grade`) / COUNT(pc.`grade`)) AS avg,
				MIN(pc.`grade`) AS min,
				MAX(pc.`grade`) AS max
			FROM `'._DB_PREFIX_.'product_comment` pc
			WHERE pc.`id_product` = '.(int)$id_product.'
			AND pc.`deleted` = 0'.
			($validate == '1' ? ' AND pc.`validate` = 1' : '');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
	}
    function getProductsOrderPrice($id_lang, $arrCategory = array(), $params = null, $limit, $short = true, $getProperties = true, $total = false, $offset = 0){
        $context = Context::getContext();
        $order_by = 'price';
        $order_way = 'DESC';        
        $where = "";
        if($arrCategory) $catIds = implode(', ', $arrCategory);
        if($params){
            $order_way = $params->orderType;    
            if($params->displayOnly == 'condition-new') $where .= " AND p.condition = 'new'";
            elseif($params->displayOnly == 'condition-used') $where .= " AND p.condition = 'used'";
            elseif($params->displayOnly == 'condition-refurbished') $where .= " AND p.condition = 'refurbished'";    
        }
        if (Group::isFeatureActive())
		{
			$groups = FrontController::getCurrentCustomerGroups();
			$where .= 'AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_group` cg
				LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
				WHERE cp.id_category IN ('.$catIds.') AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').'
			)';
		}else{
            $where .= 'AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_product` cp 
				WHERE cp.id_category IN ('.$catIds.'))';
		}
		if($total == true){
			$sql = 'SELECT COUNT(p.id_product)
				FROM  `'._DB_PREFIX_.'product` p 
                '.Shop::addSqlAssociation('product', 'p', false).'				
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'				
				WHERE product_shop.`active` = 1
					AND p.`visibility` != \'none\' '.$where;				
				return (int) DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);				
		}
        if($short == true){
        	$interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;
            $sql = 'SELECT p.id_product, p.on_sale, p.price, p.id_category_default, p.reference, p.ean13, p.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`name`, pl.`link_rewrite`, MAX(image_shop.`id_image`) id_image, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.$interval.' DAY)) > 0 AS new';
        }else{
            $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, MAX(product_attribute_shop.id_product_attribute) id_product_attribute, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, MAX(image_shop.`id_image`) id_image,
					il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new, product_shop.price AS orderprice';
        }        
        $sql .= ' FROM  `'._DB_PREFIX_.'product` p 
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i
					ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE
                    product_shop.`id_shop` = '.(int)$context->shop->id.' 
                    AND product_shop.`visibility` IN ("both", "catalog") '.$where.' 
                    AND product_shop.`active` = 1 
                    GROUP BY product_shop.id_product
                    ORDER BY p.`'.pSQL($order_by).'` '.pSQL($order_way).' Limit '.$offset.', '.$limit;
        /*
        $interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;
        if($short == true){
            $select = 'p.id_product, p.on_sale, p.price, p.id_category_default, p.reference, p.ean13, p.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`name`, pl.`link_rewrite`, MAX(image_shop.`id_image`) id_image, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.$interval.' DAY)) > 0 AS new';
        }else{
            $select = 'p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
			pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, MAX(image_shop.`id_image`) id_image, il.`legend`, m.`name` AS manufacturer_name,
			product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'" as new';
            /*
            $select = 'p.*, product_shop.*,
					pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
					pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,					
					MAX(image_shop.`id_image`) id_image, DATEDIFF(p.`date_add`, DATE_SUB(NOW(),INTERVAL '.$interval.' DAY)) > 0 AS new';
        }         
        $sql = 'SELECT DISTINCT '.$select.'
				FROM  `'._DB_PREFIX_.'product` p 
                '.Shop::addSqlAssociation('product', 'p', false).'				
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`)
					AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
					AND tr.`id_state` = 0
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				'.Product::sqlStock('p').'				
				WHERE product_shop.`active` = 1
					AND p.`visibility` != \'none\' '.$where.'			
				GROUP BY product_shop.id_product
				ORDER BY p.`'.pSQL($order_by).'` '.pSQL($order_way).' Limit '.$offset.', '.$limit;
          */     
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);    
        if (!$result) return false;
        if($getProperties == false) return $result;
        return Product::getProductsProperties($id_lang, $result);
    }
    function getProductsOrderRand($id_lang, $arrCategory = array(), $params = null, $limit, $short = true, $getProperties = true){
        $context = Context::getContext();
        $order_by = 'RAND()';
        //$order_way = $params->orderType;        
        $where = "";
        if($arrCategory) $catIds = implode(', ', $arrCategory);
        if($params){
            if($params->displayOnly == 'condition-new') $where .= " AND p.condition = 'new'";
            elseif($params->displayOnly == 'condition-used') $where .= " AND p.condition = 'used'";
            elseif($params->displayOnly == 'condition-refurbished') $where .= " AND p.condition = 'refurbished'";    
        }        
        if (Group::isFeatureActive())
		{
			$groups = FrontController::getCurrentCustomerGroups();
			$where .= 'AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_group` cg
				LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
				WHERE cp.id_category IN ('.$catIds.') AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').'
			)';
		}else{
            $where .= 'AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_product` cp 
				WHERE cp.id_category IN ('.$catIds.'))';
		}
        if($short == true){
        	$interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;
            $sql = 'SELECT p.id_product, p.on_sale, p.price, p.id_category_default, p.reference, p.ean13, p.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`name`, pl.`link_rewrite`, MAX(image_shop.`id_image`) id_image, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.$interval.' DAY)) > 0 AS new';
        }else{
            $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, MAX(product_attribute_shop.id_product_attribute) id_product_attribute, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, MAX(image_shop.`id_image`) id_image,
					il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new, product_shop.price AS orderprice';
        }        
        $sql .= ' FROM  `'._DB_PREFIX_.'product` p 
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i
					ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE
                    product_shop.`id_shop` = '.(int)$context->shop->id.' 
                    AND product_shop.`visibility` IN ("both", "catalog") '.$where.' 
                    AND product_shop.`active` = 1 
                    GROUP BY product_shop.id_product
                    ORDER BY RAND() Limit '.$limit;
        /*
        $interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;        
        if($short == true){
            $select = 'p.id_product, p.on_sale, p.price, p.id_category_default, p.reference, p.ean13, p.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`name`, pl.`link_rewrite`, MAX(image_shop.`id_image`) id_image, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.$interval.' DAY)) > 0 AS new';
        }else{
            $select = 'p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
			pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, MAX(image_shop.`id_image`) id_image, il.`legend`, m.`name` AS manufacturer_name,
			product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'" as new';
            /*
            $select = 'p.*, product_shop.*, 
					pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
					pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
					MAX(image_shop.`id_image`) id_image, DATEDIFF(p.`date_add`, DATE_SUB(NOW(),INTERVAL '.$interval.' DAY)) > 0 AS new';
        }        
        $sql = 'SELECT DISTINCT '.$select.'
				FROM  `'._DB_PREFIX_.'product` p 
                '.Shop::addSqlAssociation('product', 'p', false).'				
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`)
					AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
					AND tr.`id_state` = 0
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				'.Product::sqlStock('p').'
				WHERE product_shop.`active` = 1
					AND p.`visibility` != \'none\' '.$where.' 					
				GROUP BY product_shop.id_product
				ORDER BY RAND() Limit '.$limit;    
            */   
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (!$result) return false;
        if($getProperties == false) return $result;
        return Product::getProductsProperties($id_lang, $result);
    }
    function getProductsOrderAddDate($id_lang, $arrCategory = array(), $params = null, $limit, $short = true, $getProperties = true, $total=false, $offset = 0){
        $context = Context::getContext();        
        $order_by = 'date_add';
        $order_way = 'DESC';        
        $where = "";
        if($arrCategory) $catIds = implode(', ', $arrCategory);
        if($params){
            $order_way = $params->orderType;
            if($params->displayOnly == 'condition-new') $where .= " AND p.condition = 'new'";
            elseif($params->displayOnly == 'condition-used') $where .= " AND p.condition = 'used'";
            elseif($params->displayOnly == 'condition-refurbished') $where .= " AND p.condition = 'refurbished'";    
        }        
        if (Group::isFeatureActive())
		{
			$groups = FrontController::getCurrentCustomerGroups();
			$where .= 'AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_group` cg
				LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
				WHERE cp.id_category IN ('.$catIds.') AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').'
			)';
		}else{
            $where .= 'AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_product` cp 
				WHERE cp.id_category IN ('.$catIds.'))';
		}
		if($total == true){
			$sql = 'SELECT COUNT(p.id_product)
				FROM  `'._DB_PREFIX_.'product` p 
                '.Shop::addSqlAssociation('product', 'p', false).'				
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'				
				WHERE product_shop.`active` = 1 AND p.`visibility` != \'none\' '.$where;				
				return (int) DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);				
		}
        if($short == true){
        	$interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;
            $sql = 'SELECT p.id_product, p.on_sale, p.price, p.id_category_default, p.reference, p.ean13, p.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`name`, pl.`link_rewrite`, MAX(image_shop.`id_image`) id_image, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.$interval.' DAY)) > 0 AS new';
        }else{
            $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, MAX(product_attribute_shop.id_product_attribute) id_product_attribute, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, MAX(image_shop.`id_image`) id_image,
					il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new, product_shop.price AS orderprice';
        }        
        $sql .= ' FROM  `'._DB_PREFIX_.'product` p 
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i
					ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE
                    product_shop.`id_shop` = '.(int)$context->shop->id.' 
                    AND product_shop.`visibility` IN ("both", "catalog") '.$where.' 
                    AND product_shop.`active` = 1 
                    GROUP BY product_shop.id_product
                    ORDER BY p.`'.pSQL($order_by).'` '.pSQL($order_way).' Limit '.$offset.', '.$limit;
        /*
        $sql = 'SELECT DISTINCT '.$select.'
				FROM  `'._DB_PREFIX_.'product` p 
                '.Shop::addSqlAssociation('product', 'p', false).'				
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`)
					AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
					AND tr.`id_state` = 0
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				'.Product::sqlStock('p').'
				WHERE product_shop.`active` = 1
					AND p.`visibility` != \'none\' '.$where.' 					
				GROUP BY product_shop.id_product
				ORDER BY p.`'.pSQL($order_by).'` '.pSQL($order_way).' Limit '.$offset.', '.$limit;   
            */            
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (!$result) return false;
        if($getProperties == false) return $result;
        return Product::getProductsProperties($id_lang, $result);
    }
    function getProductById($id_lang, $productId, $short = true, $getProperties = true){
        $context = Context::getContext();
        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, MAX(product_attribute_shop.id_product_attribute) id_product_attribute, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, MAX(image_shop.`id_image`) id_image,
					il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new, product_shop.price AS orderprice
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i
					ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE 
                    product_shop.`id_shop` = '.(int)$context->shop->id.'
					AND product_shop.`id_product` = '.$productId.' 
                    AND product_shop.`visibility` IN ("both", "catalog")
                    AND product_shop.`active` = 1 
                    GROUP BY product_shop.id_product';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        if (!$result) return false;
        if($getProperties == false) return $result;                        
        return Product::getProductProperties($id_lang, $result);
    }
    public function getProductsOrderSpecial($id_lang, $arrCategory = array(), $params = null, $limit, $short = true, $getProperties = true, $total = false, $offset = 0)
	{
        $currentDate = date('Y-m-d');
        $context = Context::getContext();
        $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
		$ids = Address::getCountryAndState($id_address);
		$id_country = (int)($ids['id_country'] ? $ids['id_country'] : Configuration::get('PS_COUNTRY_DEFAULT'));        
        $order_by = 'reduction'; 
        $order_way = "DESC";      
        $where = "";        
        if($arrCategory) $catIds = implode(', ', $arrCategory);        
        if($params){
            $order_way = $params->orderType;
            if($params->displayOnly == 'condition-new') $where .= " AND p.condition = 'new'";
            elseif($params->displayOnly == 'condition-used') $where .= " AND p.condition = 'used'";
            elseif($params->displayOnly == 'condition-refurbished') $where .= " AND p.condition = 'refurbished'";        
        }
        if (Group::isFeatureActive())
		{
			$groups = FrontController::getCurrentCustomerGroups();
			$where .= 'AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_group` cg
				LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
				WHERE cp.id_category IN ('.$catIds.') AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').'
			)';
		}else{
            $where .= 'AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_product` cp 
				WHERE cp.id_category IN ('.$catIds.'))';
		}
		if($total == true){
			$sql = 'SELECT COUNT(p.id_product)
				FROM  (`'._DB_PREFIX_.'product` p 
                INNER JOIN `'._DB_PREFIX_.'specific_price` sp On p.id_product = sp.id_product)  
                '.Shop::addSqlAssociation('product', 'p', false).'				
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'				
				WHERE product_shop.`active` = 1 
                    AND sp.`id_shop` IN(0, '.(int)$context->shop->id.') 
					AND sp.`id_currency` IN(0, '.(int)$context->currency->id.') 
					AND sp.`id_country` IN(0, '.(int)$id_country.') 
					AND sp.`id_group` IN(0, '.(int)$context->customer->id_default_group.') 
					AND sp.`id_customer` IN(0) 
					AND sp.`from_quantity` = 1 					
					AND (sp.`from` = \'0000-00-00 00:00:00\' OR \''.pSQL($currentDate).'\' >= sp.`from`)
					AND (sp.`to` = \'0000-00-00 00:00:00\' OR \''.pSQL($currentDate).'\' <= sp.`to`)					
					AND sp.`reduction` > 0
					AND p.`visibility` != \'none\' '.$where;			
			return DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);					
		}
		//$interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;
        if($short == true){
        	$interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;
            $sql = 'SELECT p.id_product, p.on_sale, p.price, p.id_category_default, p.reference, p.ean13, p.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`name`, pl.`link_rewrite`, MAX(image_shop.`id_image`) id_image, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.$interval.' DAY)) > 0 AS new';
        }else{
            $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, MAX(product_attribute_shop.id_product_attribute) id_product_attribute, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, MAX(image_shop.`id_image`) id_image,
					il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new, product_shop.price AS orderprice';
        }        
        $sql .= ' FROM (`'._DB_PREFIX_.'product` p 
                    INNER JOIN `'._DB_PREFIX_.'specific_price` sp On p.id_product = sp.id_product)  
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i
					ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE 
                    product_shop.`id_shop` = '.(int)$context->shop->id.' 
                    AND sp.`id_shop` IN(0, '.(int)$context->shop->id.') 
					AND sp.`id_currency` IN(0, '.(int)$context->currency->id.') 
					AND sp.`id_country` IN(0, '.(int)$id_country.') 
					AND sp.`id_group` IN(0, '.(int)$context->customer->id_default_group.')  
					AND sp.`id_customer` IN(0) 
					AND sp.`from_quantity` = 1 
					AND (sp.`from` = \'0000-00-00 00:00:00\' OR \''.pSQL($currentDate).'\' >= sp.`from`) 
					AND (sp.`to` = \'0000-00-00 00:00:00\' OR \''.pSQL($currentDate).'\' <= sp.`to`) 					
					AND sp.`reduction` > 0 
                    AND product_shop.`visibility` IN ("both", "catalog") '.$where.' 
                    AND product_shop.`active` = 1 
                    GROUP BY product_shop.id_product 
                    ORDER BY sp.`'.pSQL($order_by).'` '.pSQL($order_way).' Limit '.$offset.', '.$limit;
        /*            
        $sql = 'SELECT DISTINCT '.$select.'
				FROM  (`'._DB_PREFIX_.'product` p 
                INNER JOIN `'._DB_PREFIX_.'specific_price` sp On p.id_product = sp.id_product)  
                '.Shop::addSqlAssociation('product', 'p', false).'				
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`)
					AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
					AND tr.`id_state` = 0
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				'.Product::sqlStock('p').'
				WHERE product_shop.`active` = 1 
                    AND sp.`id_shop` IN(0, '.(int)$context->shop->id.') 
					AND sp.`id_currency` IN(0, '.(int)$context->currency->id.') 
					AND sp.`id_country` IN(0, '.(int)$id_country.') 
					AND sp.`id_group` IN(0, '.(int)$context->customer->id_default_group.') 
					AND sp.`id_customer` IN(0) 
					AND sp.`from_quantity` = 1 					
					AND (sp.`from` = \'0000-00-00 00:00:00\' OR \''.pSQL($currentDate).'\' >= sp.`from`)
					AND (sp.`to` = \'0000-00-00 00:00:00\' OR \''.pSQL($currentDate).'\' <= sp.`to`)					
					AND sp.`reduction` > 0
					AND p.`visibility` != \'none\' '.$where.' 					
				GROUP BY product_shop.id_product
				ORDER BY sp.`'.pSQL($order_by).'` '.pSQL($order_way).' Limit '.$offset.', '.$limit;
		      */
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (!$result) return false;
        if($getProperties == false) return $result;
        return Product::getProductsProperties($id_lang, $result);        
	}
    function getProductList($id_lang, $arrCategory = array(), $notIn = '', $keyword = '', $getTotal = false, $offset=0, $limit=10){
        $where = "";
        if($arrCategory){
            $catIds = implode(', ', $arrCategory);
        }
        if (Group::isFeatureActive())
		{
			$groups = FrontController::getCurrentCustomerGroups();
			$where .= ' AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_group` cg
				LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
				WHERE cp.`id_product` Not In ('.$notIn.') AND cp.id_category IN ('.$catIds.') AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').'
			)';
		}else{
            $where .= ' AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_product` cp 
				WHERE cp.`id_product` Not In ('.$notIn.') AND cp.id_category IN ('.$catIds.'))';
		}
        if($keyword != '') $where .= " AND (p.id_product) LIKE '%".$keyword."%' OR pl.name LIKE '%".$keyword."%'";
        $sqlTotal = 'SELECT COUNT(p.`id_product`) AS nb
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').' 
                    LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					   ON p.`id_product` = pl.`id_product`
					   AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
					WHERE product_shop.`active` = 1 AND product_shop.`active` = 1 AND p.`visibility` != \'none\' '.$where;
        $total = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sqlTotal);
        if($getTotal == true) return $total;
        if($total <=0) return false;                    
        $sql = 'Select p.*, pl.`name`, pl.`link_rewrite`, IFNULL(stock.quantity, 0) as quantity_all, MAX(image_shop.`id_image`) id_image 
                FROM  `'._DB_PREFIX_.'product` p 
                '.Shop::addSqlAssociation('product', 'p', false).'				
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`)
					AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
					AND tr.`id_state` = 0
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				'.Product::sqlStock('p').'
				WHERE product_shop.`active` = 1
					AND p.`visibility` != \'none\'  '.$where.'			
				GROUP BY product_shop.id_product Limit '.$offset.', '.$limit;
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                return Product::getProductsProperties($id_lang, $result);            
    		      //return $result;
    }
    public function paginationAjaxEx($total, $page_size, $current = 1, $index_limit = 10, $func='loadPage'){
		$total_pages=ceil($total/$page_size);
		$start=max($current-intval($index_limit/2), 1);
		$end=$start+$index_limit-1;
		$output = '';                       
		$output = '<ul class="pagination">';
		if($current==1) {
			$output .= '<li><span>Prev</span></li>';
		}else{
			$i = $current-1;
			$output .= '<li><a href="javascript:void(0)" onclick="'.$func.'(\''.$i.'\')">Prev</a></li>';
		}
		if($start>1){
			$i = 1;
			$output .= '<li><a href="javascript:void(0)" onclick="'.$func.'(\''.$i.'\')">'.$i.'</a></li>';
			$output .= '<li><span>...</span></li>';
		}	
		for ($i=$start;$i<=$end && $i<= $total_pages;$i++) {
			if($i==$current) 
				$output .= '<li class="active"><span >'.$i.'</span></li>';
			else 
				$output .= '<li><a  href="javascript:void(0)" onclick="'.$func.'(\''.$i.'\')">'.$i.'</a></li>';
		}		
		if($total_pages>$end) {
			$i = $total_pages;
			$output .= '<li><span>...</span></li>';
			$output .= '<li><a href="javascript:void(0)" onclick="'.$func.'(\''.$i.'\')">'.$i.'</a></li>';
		}		
		if($current<$total_pages) {
			$i = $current+1;
			$output .= '<li><a href="javascript:void(0)" onclick="'.$func.'(\''.$i.'\')">Next</a></li>';
		} else {
			$output .= '<li><span>Next</span></li>';
		}
		$output .= '</ul>';		
		return $output;		
	}
}