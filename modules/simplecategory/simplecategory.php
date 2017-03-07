<?php
class SimpleCategory extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
	protected static $tables = array(	
                                        'simplecategory_group_lang'=>'lang',
                                        'simplecategory_module_lang'=>'lang',
                                        'simplecategory_module'=>'module',					
                                        'simplecategory_module_product'=>'',  
										'simplecategory_group'=>'', 
										'simplecategory_group_product'=>'', 
										'simplecategory_product_view'=>'view'
										);
	protected static $arrPosition = array(
		'displayHomeTopColumn', 
		'displayHomeTopContent',		 
		'displayHome', 		 
		'displayBottomProduct', 
		'displayLeftColumn', 
		'displayRightColumn', 
		'displaySmartBlogLeft', 
		'displaySmartBlogRight',
		'displayHomeBottomContent',
		'displayHomeBottomColumn',
		'displayBottomColumn',
		'displaySimpleCategory',
		'displaySimpleCategory1',
		'displaySimpleCategory2',
		'displaySimpleCategory3',
		'displaySimpleCategory4',
		'displaySimpleCategory5',
		);
	protected static $orderBy = array();
    protected static $orderWay = array();
    protected static $displayOnCondition = array();
	protected static $displayOnSale = array();
	protected static $displayOnNew = array();
	protected static $displayOnDiscount = array();
	protected static $display = array();
    protected static $categoryType = array();
    protected static $layout = array();
	protected static $compareProductIds = array();
	protected static $imageHomeSize = array();
	public $pathImage = '';
	public $liveImage = '';
	public static $sameDatas = '';
	public $secure_key= '';	
    protected static $producPropertiesCache = array();	
	public function __construct()
	{
		
		$this->name = 'simplecategory';
		self::$orderBy = array('seller'=>$this->l('Seller'), 'price'=>$this->l('Price'), 'discount'=>$this->l('Discount'), 'date_add'=>$this->l('Add Date'), 'position'=>$this->l('Position'), 'review'=>$this->l('Review'), 'view'=>$this->l('View'), 'rate'=>$this->l('Rates'));
        self::$orderWay = array('asc'=>$this->l('Ascending'), 'desc'=>$this->l('Descending'));
        self::$displayOnCondition = array('all'=>$this->l('All'), 'new'=>$this->l('New'), 'used'=>$this->l('Used'), 'refurbished'=>$this->l('Refurbished'));
		self::$displayOnSale = array('2'=>$this->l('All'), '0'=>$this->l('No'), '1'=>$this->l('Yes'));
		self::$displayOnNew = array('2'=>$this->l('All'), '0'=>$this->l('No'), '1'=>$this->l('Yes'));
		self::$displayOnDiscount = array('2'=>$this->l('All'), '0'=>$this->l('No'), '1'=>$this->l('Yes'));		
        self::$categoryType = array('auto'=>$this->l('Auto'), 'manual'=>$this->l('Manual'));
        self::$layout = array(
                'lookbook'			=>  $this->l('Look book'),                
                'simple'			=>  $this->l('Simple category'),                
                'simple_deal'  		=>  $this->l('Simple category and deal'),
                'option1_popular'	=>	$this->l('Popular [option1]'),
                'option1_special'	=>	$this->l('Special [option1]'),
                'option1_tab'		=>  $this->l('Home tab [option1]'),
                'option1_owl'		=>  $this->l('Product carousel [option1]'),
                'option3_owl'		=>  $this->l('Product carousel [option3]'),
                'fsvs'				=>	$this->l('FSVS Slider'),
            );
        $this->secure_key = Tools::encrypt('e7fb95f2623fd84c2382d43a159ed4bd-'.$this->name);
        $this->pathImage = dirname(__FILE__).'/images/';
		self::$sameDatas = dirname(__FILE__).'/samedatas/';
		if(Configuration::get('PS_SSL_ENABLED'))
			$this->liveImage = _PS_BASE_URL_SSL_.__PS_BASE_URI__.'modules/simplecategory/images/'; 
		else
			$this->liveImage = _PS_BASE_URL_.__PS_BASE_URI__.'modules/simplecategory/images/';
		$this->tab = 'front_office_features';
		$this->version = '2.0';
		$this->author = 'OVIC-SOFT';
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Ovic - Simple Category');
		$this->description = $this->l('Ovic - Simple Category');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		
	}	
	/*
    public function  __call($method, $args){        
        if(!method_exists($this, $method)) {            
          return $this->hooks($method, $args);
        }
    }
	*/
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
				if (!DB::getInstance()->execute(trim($query)))
					return false;
		}
		if(!parent::install() || !$this->registerHook('displayHeader') || !$this->registerHook('displayBackOfficeHeader')) return false;
		if(self::$arrPosition)
			foreach(self::$arrPosition as $hook)
				if(!$this->registerHook($hook)) 
					return false;
		if (!Configuration::updateGlobalValue('MOD_SIMPLECATEGORY', '1')) return false;
		$this->importSameData();  		
		return true;
	}
    
	public function importSameData($directory=''){
	   
	   //foreach(self::$tables as $table=>$value) Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.$table);
		if($directory) self::$sameDatas = $directory;
        $curent_id_option = Configuration::get('OVIC_CURRENT_DIR');
        if($curent_id_option) $curent_id_option .= '.';         
        else $curent_id_option = '';
                
		$langs = Db::getInstance()->executeS("Select id_lang From "._DB_PREFIX_."lang Where active = 1");		
		if(self::$tables){
			$homeCategory = Configuration::get('PS_HOME_CATEGORY');
            $categoryIds = $this->getCategoryIds(Configuration::get('PS_HOME_CATEGORY'));
            $productIds = $this->getProductIds();        
			foreach(self::$tables as $table=>$value){			 
				if (file_exists(self::$sameDatas.$curent_id_option.$table.'.sql')){
				    
                    Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.$table);
                    
					$sql = file_get_contents(self::$sameDatas.$curent_id_option.$table.'.sql');
					$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
					$sql = preg_split("/;\s*[\r\n]+/", trim($sql));
                    
					if($value == 'lang'){
						foreach ($sql as $query){
							foreach($langs as $lang){							 
								$query_result = str_replace('id_lang', $lang['id_lang'], trim($query));
                                if(strpos($query_result, 'category_id')){
                                    $a = explode('category_id', $query_result);
                                    if(isset($a[1])){
                                        $catId = (int)$a[1];
                                        if(in_array($catId, $categoryIds)){
                                            $query_result = str_replace('category_id', '', $query_result);
                                        }else{
                                            $query_result = str_replace('category_id'.$catId, $homeCategory, $query_result);    
                                        }
                                    }else{
                                        $query_result = str_replace('category_id', $homeCategory, $query_result);    
                                    }    								
    							}
                                if(strpos($query_result, 'product_id')){  
                                    $b = explode('product_id', $query_result);
                                    if(isset($b[1])){
                                        $proId = (int)$b[1];
                                        if(in_array($proId, $productIds)){
                                            $query_result = str_replace('product_id', '', $query_result);
                                        }else{
                                            $query_result = str_replace('product_id'.$proId, $homeCategory, $query_result);    
                                        }
                                    }else{
                                        $query_result = str_replace('product_id', $homeCategory, $query_result);    
                                    }
    								
                                }                                
                                if($query_result) Db::getInstance()->execute($query_result);
							}
						}
					}else{
						foreach ($sql as $query){
                            $query_result = trim($query);
                            if(strpos($query_result, 'category_id')){
                                $a = explode('category_id', $query_result);
                                if(isset($a[1])){
                                    $catId = (int)$a[1];
                                    if(in_array($catId, $categoryIds)){
                                        $query_result = str_replace('category_id', '', $query_result);
                                    }else{
                                        $query_result = str_replace('category_id'.$catId, $homeCategory, $query_result);    
                                    }
                                }else{
                                    $query_result = str_replace('category_id', $homeCategory, $query_result);    
                                }    								
							}
                            if(strpos($query_result, 'product_id')){  
                                $b = explode('product_id', $query_result);
                                if(isset($b[1])){
                                    $proId = (int)$b[1];
                                    if(in_array($proId, $productIds)){
                                        $query_result = str_replace('product_id', '', $query_result);
                                    }else{
                                        $query_result = str_replace('product_id'.$proId, $homeCategory, $query_result);    
                                    }
                                }else{
                                    $query_result = str_replace('product_id', $homeCategory, $query_result);    
                                }
								
                            }
                            /*
                            if(strpos($query_result, 'category_id')){    								
								$query_result = str_replace('category_id', $categoryIds[array_rand($categoryIds)], $query_result);
							}
                            if(strpos($query_result, 'product_id')){    								
								$query_result = str_replace('product_id', $productIds[array_rand($productIds)], $query_result);
                            }
                            */
                            if($query_result) Db::getInstance()->execute($query_result);
						}
					}
				}
			}
		}
		return true;
	}
	public function uninstall($keep = true)
	{	   
		if (!parent::uninstall()) return false;
        if($keep){
			if(self::$tables){
				foreach(self::$tables as $table=>$value)
					Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.$table.'`');
			}			
        }		
        if (!Configuration::deleteByName('MOD_SIMPLECATEGORY')) return false;		
		return true;
	}
	public function reset()
	{
		if (!$this->uninstall(false))
			return false;
		if (!$this->install(false))
			return false;
        
		return true;
	}
	public function getBannerSrc($image, $check = true){
        if($image){
            if(strpos($image, 'http') !== false){
                return $image;
    	   }else{
                if(file_exists($this->pathImage.'b/'.$image))
                    return $this->liveImage.'b/'.$image;
                else
                    if($check == true) 
                        return '';
                    else
                        return $this->liveImage.'default.jpg';	  	 
          }    
        }else{
            if($check == true) 
                return '';
            else
                return $this->liveImage.'default.jpg';
        }	
	}
	public function getIconSrc($image){
		if($image){
        	if(strpos($image, '.') !== false){        		
	        	if(strpos($image, 'http') !== false){
	                $results = array('type'=>'image', 'img'=>$image);
	            }else{
	                if(file_exists($this->pathImage.$image))
						$results = array('type'=>'image', 'img'=>$this->liveImage.$image);	                    
	        		else if($image && file_exists($this->pathImage.'icons/'.$image))
						$results = array('type'=>'image', 'img'=>$this->liveImage.'icons/'.$image);
	                else{
	                	$results = array('type'=>'none', 'img'=>'');
	                }
						
	                        
	            }	
        	}else{
        		$results = array('type'=>'class', 'img'=>$image);
        	}
        }else{
        	$results = array('type'=>'none', 'img'=>'');
        }    
		return $results;
	
		
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
	/*
	public function getPositionOptions($selected = 0){
        $positionOptions = '';
        $items = DB::getInstance()->executeS("Select id_hook From "._DB_PREFIX_."hook_module Where id_module = ".$this->id);
        $options = '';
        if($items){
            foreach($items as $item){
                if($selected == $item['id_hook']) $options .= '<option selected="selected" value="'.$item['id_hook'].'">'.Hook::getNameById($item['id_hook']).'</option>';
                else $options .= '<option value="'.$item['id_hook'].'">'.Hook::getNameById($item['id_hook']).'</option>';
            }
        }
        return $options; 
    }
	*/ 
	static function getPositionByModule($moduleId){
		$items = Db::getInstance()->executeS("Select position_name From "._DB_PREFIX_."simplecategory_module_position Where module_id = $moduleId");
		$result = '';
		if($items){
			foreach($items as $item)
				$result .= '<div>'.$item['position_name'].'</div>';
		}
		return $result;
	}
	
	public function getPositionMultipleOptions($moduleId=0){
		$options = '';
		$selected = array();
		$id_shop = (int)Context::getContext()->shop->id;		
		$items = DB::getInstance()->executeS("Select * From "._DB_PREFIX_."simplecategory_module_position Where module_id = $moduleId");
		if($items){
			foreach($items as $item) $selected[] = $item['position_id'];
		}
		$items = DB::getInstance()->executeS("Select h.name, h.id_hook From "._DB_PREFIX_."hook AS h Inner Join "._DB_PREFIX_."hook_module as hm On h.id_hook = hm.id_hook Where h.name NOT LIKE 'action%' AND hm.id_module = ".$this->id." AND hm.id_shop = ".$id_shop);        		
		if($items){
			foreach($items as $item){
				if(in_array($item['id_hook'], $selected)) $options .='<option selected="selected" value="'.$item['id_hook'].'">'.$item['name'].'</option>';
				else $options .='<option value="'.$item['id_hook'].'">'.$item['name'].'</option>';
			}
		}
        return $options;
    }
	public function getPositionOptions($selected=''){
		$options = '';
		if(self::$arrPosition){			
			foreach(self::$arrPosition as $value){
				$hookId = Hook::getIdByName($value);
				if(strtolower($value) == strtolower($selected)) $options .='<option selected="selected" value="'.$value.'">'.$value.'</option>';
				else $options .='<option value="'.$value.'">'.$value.'</option>';
			}
		}
        return $options;
    }
	  
	public function getLayoutOptions($selected=''){
		$options = '';
		foreach (self::$layout as $key => $value) {
			if($selected == $key) $options .= '<option selected="selected" value="'.$key.'">'.$value.'</option>';
			else $options .= '<option value="'.$key.'">'.$value.'</option>';
		}
		return $options;
	}
	public function getOrderByOptions($selected=''){
		$options = '';
		foreach (self::$orderBy as $key => $value) {
			if($selected == $key) $options .= '<option selected="selected" value="'.$key.'">'.$value.'</option>';
			else $options .= '<option value="'.$key.'">'.$value.'</option>';
		}
		return $options;
	}
	public function getOrderWayOptions($selected=''){
		$options = '';
		foreach (self::$orderWay as $key => $value) {
			if($selected == $key) $options .= '<option selected="selected" value="'.$key.'">'.$value.'</option>';
			else $options .= '<option value="'.$key.'">'.$value.'</option>'; 
		}
		return $options;
	}
	public function getCategoryTypeOptions($selected = ''){
		$options = '';
		foreach (self::$categoryType as $key => $value) {
			if($selected == $key) $options .= '<option selected="selected" value="'.$key.'">'.$value.'</option>';
			else $options .= '<option value="'.$key.'">'.$value.'</option>';
		}
		return $options;
	}
	public function getAllCategories($langId, $shopId, $parentId = 0, $sp='', $arr=null, $maxDepth=10){
        if($arr == null) $arr = array();
        $items = DB::getInstance()->executeS("Select c.id_category, cl.name From "._DB_PREFIX_."category as c Inner Join "._DB_PREFIX_."category_lang as cl On c.id_category = cl.id_category Where c.active = 1 AND c.level_depth <= $maxDepth AND c.id_shop_default = $shopId AND c.id_parent = $parentId AND cl.id_lang = ".$langId." AND cl.id_shop = ".$shopId);		
        if($items){
            foreach($items as $item){
                $arr[] = array('id_category'=>$item['id_category'], 'name'=>$item['name'], 'sp'=>$sp);
                $arr = $this->getAllCategories($langId, $shopId, $item['id_category'], $sp.'|-', $arr);
            }
        }
        return $arr;
    }
	public function getCategoryIds($parentId = 0, $arr=null){
        if($arr == null) $arr = array();
		$id_shop = (int)Context::getContext()->shop->id;	
        $items = DB::getInstance()->executeS("Select id_category From "._DB_PREFIX_."category Where id_shop_default = $id_shop AND active = 1 AND id_parent = $parentId");
        if($items){
            foreach($items as $item){
                $arr[] = $item['id_category'];
                $arr = $this->getCategoryIds($item['id_category'], $arr);
            }
        }
        return $arr;
    }
    protected function getProductIds($categoryId = 0, $arr=null){
        if($arr == null) $arr = array();
		$id_shop = (int)Context::getContext()->shop->id;	
        if($categoryId >0)
            $items = DB::getInstance()->executeS("Select id_product From "._DB_PREFIX_."product_shop Where active = 1 AND  id_shop = $id_shop AND id_category_default = '$categoryId'");
        else
            $items = DB::getInstance()->executeS("Select id_product From "._DB_PREFIX_."product_shop Where active = 1 AND  id_shop = $id_shop");
        if($items){
            foreach($items as $item){
                $arr[] = $item['id_product'];
            }
        }
        return $arr;
    }
	public function getCategoryOptions($selected = 0, $parentId = 0){
        $langId = Context::getContext()->language->id;
        $shopId = Context::getContext()->shop->id;
        $options = '';
        if($parentId <=0) $parentId = Configuration::get('PS_HOME_CATEGORY');
		$parentName = $this->getCategoryNameById($parentId);
		$options ='<option selected="selected" value="'.$parentId.'">'.$parentName.'</option>';
		$options .='<option value="0">'.$this->l('Current category').'</option>';
        $items = $this->getAllCategories($langId, $shopId, $parentId, '|-', null);		
        if($items){
            foreach($items as $item){
                if($item['id_category'] == $selected) $options .='<option selected="selected" value="'.$item['id_category'].'">'.$item['sp'].$item['name'].'</option>';
                else $options .='<option value="'.$item['id_category'].'">'.$item['sp'].$item['name'].'</option>';
            }
        }
        return  $options;
    }
	protected static function getOnConditionOptions($selected = ''){
		$options = '';
		foreach (self::$displayOnCondition as $key => $value) {
			if($selected == $key) $options .= '<option selected="selected" value="'.$key.'">'.$value.'</option>';
			else $options .= '<option value="'.$key.'">'.$value.'</option>';
		}
		return $options;
	}
	protected static function getOnSaleOptions($selected = ''){
		$options = '';
		foreach (self::$displayOnSale as $key => $value) {
			if($selected == $key) $options .= '<option selected="selected" value="'.$key.'">'.$value.'</option>';
			else $options .= '<option value="'.$key.'">'.$value.'</option>';
		}
		return $options;
	}
	protected static function getOnNewOptions($selected = ''){
		$options = '';
		foreach (self::$displayOnNew as $key => $value) {
			if($selected == $key) $options .= '<option selected="selected" value="'.$key.'">'.$value.'</option>';
			else $options .= '<option value="'.$key.'">'.$value.'</option>';
		}
		return $options;
	}
	protected static function getOnDiscountOptions($selected = ''){
		$options = '';
		foreach (self::$displayOnDiscount as $key => $value) {
			if($selected == $key) $options .= '<option selected="selected" value="'.$key.'">'.$value.'</option>';
			else $options .= '<option value="'.$key.'">'.$value.'</option>';
		}
		return $options;
	}
	protected static function getModuleByLang($id, $langId=0){
		if($langId == 0) $langId = Context::getContext()->language->id;
		$itemLang = DB::getInstance()->getRow("Select name, banners, description From "._DB_PREFIX_."simplecategory_module_lang Where module_id = ".$id." AND `id_lang` = ".$langId);
		
		if(!$itemLang) $itemLang = array('name'=>'', 'banners'=>'', 'description'=>'');
		return $itemLang;
	}
	protected static function getGroupByLang($id, $langId=0){
		if($langId == 0) $langId = Context::getContext()->language->id;
		$itemLang = Db::getInstance()->getRow("Select name, banners, description From "._DB_PREFIX_."simplecategory_group_lang Where group_id = ".$id." AND `id_lang` = ".$langId);
		if(!$itemLang) $itemLang = array('name'=>'', 'banners'=>'', 'description'=>'');
		return $itemLang;
	}
	static function getCategoryNameById($id, $langId=0, $shopId=0){
		if(!$langId) $langId = Context::getContext()->language->id;
	    if(!$shopId) $shopId = Context::getContext()->shop->id;
        return DB::getInstance()->getValue("Select name From "._DB_PREFIX_."category_lang Where id_category = $id AND `id_shop` = '$shopId' AND `id_lang` = '$langId'");        
    }
	
	public function renderModuleForm($id = 0){
		$shopId = Context::getContext()->shop->id;
		$item = DB::getInstance()->getRow("Select * From "._DB_PREFIX_."simplecategory_module Where id = ".$id);		
		if(!$item){
			$item = array(
				'id'=>0, 
				'id_shop'=>0, 
				'category_id'=>0, 
				'position_id'=>0, 
				'position_name'=>'',
				'maxItem'=>12,
				'order_by'=>'date_add',
				'order_way'=>'desc',
				'on_condition'=>'all',
				'on_sale'=>2,
				'on_new'=>2,
				'on_discount'=>2,
				'type'=>'auto',  
				'status'=>1, 
				'ordering'=>1, 
				'layout'=>'simple', 
				'custom_class'=>'', 
				'display_name'=>1
				);
			$params = array('color'=>'', 'icon'=>'', 'icon_active'=>'');			
		}else{
			if($item['params'])
				$params = get_object_vars(json_decode($item['params']));
			else
				$params = array('color'=>'', 'icon'=>'', 'icon_active'=>'');
		}		
		$languages = $this->getAllLanguages();		
		$inputTitle = '';
		$html = array('config'=>'', 'banners'=>'');
		$langActive = '<input type="hidden" id="moduleLangActive" value="0" />';
		$banners = '';
        $descriptions = '';        
		if($languages){
			foreach ($languages as $key => $language) {				
				$itemLang = self::getModuleByLang($id, $language->id);
				if($language->active == '1'){
					$html['banners'] .= '<table class="table table-hover tbl-banners tbl-banners-lang-'.$language->id.'" id="tbl-banners-lang-'.$language->id.'"><thead><tr><th width="100">'.$this->l('Image').'</th><th>'.$this->l('File name').'</th><th>'.$this->l('Banner link').'</th><th>'.$this->l('Image alt').'</th><th>#</th></tr></thead><tbody>';
					if($itemLang['banners']){
						$itemBanners = json_decode($itemLang['banners']);						
						if($itemBanners){
							foreach($itemBanners as $itemBanner){
								$html['banners'] .= '<tr>
	                                                    <td rowspan="2"><div style="width: 100px"><img class="img-responsive" src="'.$this->getBannerSrc($itemBanner->image).'" /></div></td>
	                                                    <td><input type="text" name="bannerNames'.$language->id.'[]" value="'.$itemBanner->image.'" class="form-control" /></td>
	                                                    <td><input type="text" name="bannerLink'.$language->id.'[]" value="'.$itemBanner->link.'" class="form-control"  /></td>
	                                                    <td><input type="text" name="bannerAlt'.$language->id.'[]" value="'.$itemBanner->alt.'" class="form-control"  /></td>
	                                                    <td rowspan="2" class="pointer dragHandle center" ><div class="dragGroup"><a href="javascript:void(0)" class="lik-banner-del color-red" title="Delete banner">Del</a></div></td>	                                                        
	                                                </tr>
                                                    <tr>	                                                    
	                                                    <td colspan="3"><textarea style="width: 100%; height: 80px"  name="bannerDescription'.$language->id.'[]"  placeholder="'.$this->l('Description').'">'.(isset($itemBanner->description) ? $itemBanner->description : '').'</textarea></td>	                                                    	                                                        
	                                                </tr>';
							}
						}
					}					
					$html['banners'] .= '</tbody></table>';
					$langActive = '<input type="hidden" id="moduleLangActive" value="'.$language->id.'" />';
					$inputTitle .= '<input type="text" value="'.$itemLang['name'].'" name="names[]" class="form-control module-lang-'.$language->id.'" />';
                    $descriptions .= '<div class="module-lang-'.$language->id.'"><textarea class="editor" name="descriptions[]" id="description-'.$language->id.'">'.$itemLang['description'].'</textarea></div>';			
				}else{					
					$inputTitle .= '<input type="text" value="'.$itemLang['name'].'" name="names[]" class="form-control module-lang-'.$language->id.'" style="display:none" />';
                    $descriptions .= '<div style="display:none" class="module-lang-'.$language->id.'"><textarea class="editor" name="descriptions[]" id="description-'.$language->id.'">'.$itemLang['description'].'</textarea></div>';
                    
					$html['banners'] .= '<table style="display:none" class="table table-hover tbl-banners tbl-banners-lang-'.$language->id.'" id="tbl-banners-lang-'.$language->id.'"><thead><tr><th width="100">'.$this->l('Image').'</th><th>'.$this->l('File name').'</th><th>'.$this->l('Banner link').'</th><th>'.$this->l('Image alt').'</th><th>#</th></tr></thead><tbody>';
					if($itemLang['banners']){
						$itemBanners = json_decode($itemLang['banners']);
						if($itemBanners){
							foreach($itemBanners as $itemBanner){
								$html['banners'] .= '<tr>
                                                        <td rowspan="2">
                                                            <div style="width: 100px"><img class="img-responsive" src="'.$this->getBannerSrc($itemBanner->image).'"  /></div>                                                                                                                        
                                                        </td>
                                                        <td><input type="text" name="bannerNames'.$language->id.'[]" value="'.$itemBanner->image.'" class="form-control" /></td>
                                                        <td><input type="text" name="bannerLink'.$language->id.'[]" value="'.$itemBanner->link.'" class="form-control"  /></td>
                                                        <td><input type="text" name="bannerAlt'.$language->id.'[]" value="'.$itemBanner->alt.'" class="form-control"  /></td>
                                                        <td rowspan="2" class="pointer dragHandle center" ><div class="dragGroup"><a href="javascript:void(0)" class="lik-banner-del color-red" title="Delete banner">Del</a></div></td>	
                                                    </tr>
                                                    <tr>	                                                    
                                                        <td colspan="3"><textarea style="width: 100%; height: 80px"  name="bannerDescription'.$language->id.'[]"  '.$this->l('Description').'>'.(isset($itemBanner->description) ? $itemBanner->description : '').'</textarea></td>	                                                    	                                                        
                                                    </tr>';
							}
						}
					}					
					$html['banners'] .= '</tbody></table>';
				}				
			}
		}
		$langOptions = $this->getLangOptions();
        $html['description'] = '<div class="col-sm-12 ">                
                <div class="col-sm-10">'.$descriptions.'</div>
                <div class="col-sm-2">
                    <select class="module-lang" onchange="moduleChangeLanguage(this.value)">'.$langOptions.'</select>
                </div>
            </div>';
		$html['config'] = '<input type="hidden" name="moduleId" value="'.$item['id'].'" />';
		$html['config'] .= '<input type="hidden" name="action" value="saveModule" />';
		$html['config'] .= '<input type="hidden" name="secure_key" value="'.$this->secure_key.'" />';
		$html['config'] .= $langActive;
		$html['config'] .= '<div class="form-group"><div class="col-sm-12 "><div class="col-sm-12 "><label>'.$this->l('Module name').'</label></div><div class="col-sm-10">'.$inputTitle.'</div><div class="col-sm-2"><select class="module-lang" onchange="moduleChangeLanguage(this.value)">'.$langOptions.'</select></div></div></div>';
		$html['config'] .= '<div class="form-group">								
								<div class="col-sm-6">					    
			                        <div class="col-sm-12">
				                        <div class="input-group">
				                            <input type="text" class="form-control" id="module-icon" name="icon" value="'.$params['icon'].'"  placeholder="Icon" />
				                            <span class="input-group-btn">
				                                <button id="btn-module-icon" type="button" class="btn btn-default"><i class="icon-folder-open"></i></button>
				                            </span>
				                        </div>
			                        </div>
								</div>
								<div class="col-sm-6">				    
			                        <div class="col-sm-12">
				                        <div class="input-group">
				                            <input type="text" class="form-control" id="module-icon-active" name="icon_active" value="'.$params['icon_active'].'"  placeholder="Background" />
				                            <span class="input-group-btn">
				                                <button id="btn-module-icon-active" type="button" class="btn btn-default"><i class="icon-folder-open"></i></button>
				                            </span>
				                        </div>
			                        </div>
								</div>																	
							</div>';
		$html['config'] .= '<div class="form-group">
					<div class="col-sm-6 ">
						<div class="col-sm-12 "><input type="text" name="custom_class" value="'.$item['custom_class'].'" placeholder="Custom class" class="form-control" /></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-12 ">
							<div class="input-group">
                                <input type="text" class="mColorPicker form-control" id="color" name="color" placeholder="module color" value="'.$params['color'].'" data-hex="true"  />
                                <span id="icp_color" class="mColorPickerTrigger input-group-addon" data-mcolorpicker="true">
                                    <img src="../img/admin/color.png" />
                                </span>                                
                            </div>
						</div>
					</div>				    
                </div>';
		if($item['display_name'] == 1){
			$html['config'] .= '<div class="form-group">	
					<div class="col-sm-6">
						<div class="col-sm-12"><label>'.$this->l('Position').'</label></div>								
						<div class="col-sm-12">
							<select  name="position_name" class="form-control" >'.$this->getPositionOptions($item['position_name']).'</select>
						</div>
					</div>				
					<div class="col-sm-6">
						<div class="col-sm-12">
							<label>'.$this->l('Display name').'</label>	
						</div>						
						<div class="col-sm-12">	                        
                            <span class="switch prestashop-switch fixed-width-lg" id="module-display-name">
                                <input type="radio" value="1" class="module_display_name" checked="checked" id="module_display_name_on" name="module_display_name" />
            					<label for="module_display_name_on">Yes</label>
            				    <input type="radio" value="0" class="module_display_name" id="module_display_name_off" name="module_display_name" />
            					<label for="module_display_name_off">No</label>
                                <a class="slide-button btn"></a>
            				</span>
	                    </div>	
					</div>
									    
                </div>';	
		}else{
			$html['config'] .= '<div class="form-group">
					<div class="col-sm-6">
						<div class="col-sm-12"><label>'.$this->l('Position').'</label></div>								
						<div class="col-sm-12">
							<select  name="position_name" class="form-control" >'.$this->getPositionOptions($item['position_name']).'</select>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-12">
							<label>'.$this->l('Display name').'</label>	
						</div>
						<div class="col-sm-12">	                        
                            <span class="switch prestashop-switch fixed-width-lg" id="module-display-name">
                                <input type="radio" value="1" class="module_display_name" id="module_display_name_off" name="module_display_name" />
            					<label for="module_display_name_off">Yes</label>
            				    <input type="radio" value="0" class="module_display_name" checked="checked" id="module_display_name_on" name="module_display_name" />
            					<label for="module_display_name_on">No</label>
                                <a class="slide-button btn"></a>
            				</span>
	                    </div>
					</div>
                    			    
                </div>';
		}
		$html['config'] .= '<div class="form-group">								
								<div class="col-sm-6">
									<div class="col-sm-12"><label>'.$this->l('Categories').'</label></div>
									<div class="col-sm-12"><select class="form-control" name="categoryId" id="module-category">'.$this->getCategoryOptions($item['category_id'], 0).'</select></div>
								</div>
								<div class="col-sm-6">
									<div class="col-sm-12 "><label>'.$this->l('Module layout').'</label></div>
									<div class="col-sm-12 "><select name="moduleLayout">'.$this->getLayoutOptions($item['layout']).'</select></div>
								</div>																		
							</div>';
		$html['config'] .= '<div class="form-group">
			                    <div class="col-lg-12">
				                    <div class="col-lg-12 "><label>'.$this->l('Module product type').'</label></div>								    
			                        <div class="col-sm-12"><select name="type" class="form-control" onchange="moduleChangeType(this.value)">'.$this->getCategoryTypeOptions($item['type']).'</select></div>
			                    </div>
			                </div>';
		$html['config'] .= '<div class="module-type-auto" style="display:'.($item['type'] == 'auto' ? 'block' : 'none').'">
								<div class="form-group">
									<div class="col-sm-3">
										<div class="col-sm-12 "><label>'.$this->l('Only Sale').'</label></div>
										<div class="col-sm-12 "><select name="on_sale">'.$this->getOnSaleOptions($item['on_sale']).'</select></div>
									</div>
									<div class="col-sm-3">
										<div class="col-sm-12 "><label>'.$this->l('Only New').'</label></div>
										<div class="col-sm-12 "><select name="on_new">'.$this->getOnNewOptions($item['on_new']).'</select></div>
									</div>
									<div class="col-sm-3">
										<div class="col-sm-12 "><label>'.$this->l('Only Discount').'</label></div>
										<div class="col-sm-12 "><select name="on_discount">'.$this->getOnDiscountOptions($item['on_discount']).'</select></div>
									</div>									
									<div class="col-sm-3">
										<div class="col-sm-12 "><label>'.$this->l('Product condition').'</label></div>
										<div class="col-sm-12 "><select name="on_condition">'.self::getOnConditionOptions($item['on_condition']).'</select></div>
									</div>						
								</div>
								<div class="form-group">
									<div class="col-sm-3">
										<div class="col-sm-12 "><label>'.$this->l('Order by').'</label></div>
										<div class="col-sm-12 "><select name="order_by">'.$this->getOrderByOptions($item['order_by']).'</select></div>
									</div>
									<div class="col-sm-3">
										<div class="col-sm-12 "><label>'.$this->l('Order way').'</label></div>
										<div class="col-sm-12 "><select name="order_way">'.$this->getOrderWayOptions($item['order_way']).'</select></div>
									</div>
									<div class="col-sm-3">
										<div class="col-sm-12 "><label>'.$this->l('Max item').'</label></div>
										<div class="col-sm-12 "><input type="text" name="maxItem" value="'.$item['maxItem'].'" class="form-control" /></div>
									</div>									
								</div>
							</div>';
		$html['config'] .= '<div class="module-type-manual" style="display:'.($item['type'] == 'manual' ? 'block' : 'none').'">								
								<div class="form-group">
									<div class="col-sm-12">
										<div class="col-sm-12 "><label>'.$this->l('Product list').' [<a href="javascript:void(0)" class="add-manual-product" data-group="'.$id.'" onclick="openProductsModal(\''.$id.'\')">'.$this->l('add product').'</a>]</label></div>
										<div class="col-sm-12 ">
											<ul id="module-list-products" class="result-list-products">'.$this->getModuleProduct($id).'</ul>
										</div>
									</div>
								</div>
							</div>';
		
		return $html;
	}
	protected function getGroupProduct($groupId=0){
		if(!$groupId) return '';
		$items = Db::getInstance()->executeS("Select * From "._DB_PREFIX_."simplecategory_group_product Where group_id = '$groupId' Order By ordering");
		$langId = Context::getContext()->language->id;
		$result = '';
		if($items){
			foreach($items as $item){
				$productName = Product::getProductName($item['product_id']);
				$result .= '<li id="group-manual-product-'.$item['product_id'].'"><input type="hidden" class="product_ids" name="product_ids[]" value="'.$item['product_id'].'" /><span>'.$productName.'</span><a class="group-manual-product-delete pull-right" data-id="'.$item['product_id'].'"><i class="icon-trash "></i></a></li>';
			}
		}
		return $result;
	}
	protected function getModuleProduct($moduleId=0){
		if(!$moduleId) return '';
		$items = Db::getInstance()->executeS("Select * From "._DB_PREFIX_."simplecategory_module_product Where module_id = '$moduleId' Order By ordering");
		$langId = Context::getContext()->language->id;
		$result = '';
		if($items){
			foreach($items as $item){
				$productName = Product::getProductName($item['product_id']);
				$result .= '<li id="module-manual-product-'.$item['product_id'].'"><input type="hidden" class="product_ids" name="product_ids[]" value="'.$item['product_id'].'" /><span>'.$productName.'</span><a class="module-manual-product-delete pull-right" data-id="'.$item['product_id'].'"><i class="icon-trash "></i></a></li>';
			}
		}
		return $result;
	}		
	public function renderGroupForm($id = 0){
		
		$shopId = Context::getContext()->shop->id;
		$item = DB::getInstance()->getRow("Select * From "._DB_PREFIX_."simplecategory_group Where id = ".$id);
		if(!$item){
			$item = array(
				'id'=>0, 
				'module_id'=>0, 
				'category_id'=>0, 
				'maxItem'=>12, 
				'order_by'=>'date_add', 
				'order_way'=>'desc', 
				'on_condition'=>'all',
				'on_sale'=>2,
				'on_new'=>2,
				'on_discount'=>2, 
				'type'=>'auto', 
				'ordering'=>1, 
				'status'=>1, 
				'custom_class'=>'', 
				'params'=>'', 
				'icon'=>'', 
				'icon_active'=>''
				);
			$params = array('color'=>'', 'icon'=>'', 'icon_active'=>'');
		}else{
			if($item['params'])
				$params = get_object_vars(json_decode($item['params']));
			else
				$params = array('color'=>'', 'icon'=>'', 'icon_active'=>'');
		}
		$html = array('config'=>'', 'banners'=>'');
		$languages = $this->getAllLanguages();
		$inputTitle = '';
		$descriptions = '';		
		$langActive = '<input type="hidden" id="groupLangActive" value="0" />';
		if($languages){
			foreach ($languages as $key => $language) {				
				$itemLang = self::getGroupByLang($id, $language->id);
				if($language->active == '1'){
					$langActive = '<input type="hidden" id="groupLangActive" value="'.$language->id.'" />';
					$inputTitle .= '<input type="text" value="'.$itemLang['name'].'" name="names[]" class="form-control group-lang-'.$language->id.'" />';
					$descriptions .= '<div class="group-lang-'.$language->id.'"><textarea class="editor" name="descriptions[]" id="group-description-'.$language->id.'">'.$itemLang['description'].'</textarea></div>';					
					$html['banners'] .= '<table class="table table-hover tbl-banners tbl-group-banners-lang-'.$language->id.'" id="tbl-group-banners-lang-'.$language->id.'">
						<thead>
							<tr>
								<th width="100">'.$this->l('Image').'</th>
								<th>'.$this->l('File name').'</th>
								<th>'.$this->l('Banner link').'</th>
								<th>'.$this->l('Image alt').'</th>
								<th>#</th>
							</tr>
						</thead>
						<tbody>';
					if($itemLang['banners']){
						$itemBanners = json_decode($itemLang['banners']);
						if($itemBanners){
							foreach($itemBanners as $itemBanner){
								$html['banners'] .= '<tr>
	                                                    <td rowspan="2"><div style="width: 100px"><img class="img-responsive" src="'.$this->getBannerSrc($itemBanner->image).'" /></div></td>
	                                                    <td><input type="text" name="bannerNames'.$language->id.'[]" value="'.$itemBanner->image.'" class="form-control" /></td>
	                                                    <td><input type="text" name="bannerLink'.$language->id.'[]" value="'.$itemBanner->link.'" class="form-control"  /></td>
	                                                    <td><input type="text" name="bannerAlt'.$language->id.'[]" value="'.$itemBanner->alt.'" class="form-control"  /></td>
	                                                    <td rowspan="2" class="pointer dragHandle center" >
	                                                    	<div class="dragGroup"><a href="javascript:void(0)" class="lik-group-banner-del color-red" title="Delete banner">Del</a></div>
	                                                    </td>	                                                        
	                                                </tr>
                                                    <tr>	                                                    
	                                                    <td colspan="3"><textarea style="width: 100%; height: 80px" name="bannerDescription'.$language->id.'[]" '.$this->l('Description').'>'.(isset($itemBanner->description) ? $itemBanner->description : '').'</textarea></td>	                                                    	                                                        
	                                                </tr>';
							}
						}
					}					
					$html['banners'] .= '</tbody></table>';					
				}else{
					$inputTitle .= '<input type="text" value="'.$itemLang['name'].'" name="names[]" class="form-control group-lang-'.$language->id.'" style="display:none" />';
					$descriptions .= '<div style="display:none" class="group-lang-'.$language->id.'"><textarea class="editor" name="descriptions[]" id="group-description-'.$language->id.'">'.$itemLang['description'].'</textarea></div>';
					$html['banners'] .= '<table style="display:none" class="table table-hover tbl-banners tbl-group-banners-lang-'.$language->id.'" id="tbl-group-banners-lang-'.$language->id.'"><thead><tr><th width="100">'.$this->l('Image').'</th><th>'.$this->l('File name').'</th><th>'.$this->l('Banner link').'</th><th>'.$this->l('Image alt').'</th><th>#</th></tr></thead><tbody>';
					if($itemLang['banners']){
						$itemBanners = json_decode($itemLang['banners']);
						if($itemBanners){
							foreach($itemBanners as $itemBanner){
								$html['banners'] .= '<tr>
                                                        <td rowspan="2">
                                                            <div style="width: 100px"><img class="img-responsive" src="'.$this->getBannerSrc($itemBanner->image).'"  /></div>                                                                                                                        
                                                        </td>
                                                        <td><input type="text" name="bannerNames'.$language->id.'[]" value="'.$itemBanner->image.'" class="form-control" /></td>
                                                        <td><input type="text" name="bannerLink'.$language->id.'[]" value="'.$itemBanner->link.'" class="form-control"  /></td>
                                                        <td><input type="text" name="bannerAlt'.$language->id.'[]" value="'.$itemBanner->alt.'" class="form-control"  /></td>
                                                        <td rowspan="2" class="pointer dragHandle center" ><div class="dragGroup"><a href="javascript:void(0)" class="lik-group-banner-del color-red" title="Delete banner">Del</a></div></td>	
                                                    </tr>
                                                    <tr>	                                                    
	                                                    <td colspan="3"><textarea style="width: 100%; height: 80px" name="bannerDescription'.$language->id.'[]" '.$this->l('Description').'>'.(isset($itemBanner->description) ? $itemBanner->description : '').'</textarea></td>	                                                    	                                                        
	                                                </tr>';
							}
						}
					}					
					$html['banners'] .= '</tbody></table>';					
				}				
			}
		}
		$langOptions = $this->getLangOptions();
		$html['description'] = '<div class="col-sm-12 ">                
                <div class="col-sm-10">'.$descriptions.'</div>
                <div class="col-sm-2">
                    <select class="module-lang" onchange="groupChangeLanguage(this.value)">'.$langOptions.'</select>
                </div>
            </div>';
		$html['config'] = '<input type="hidden" name="groupId" value="'.$item['id'].'" />';
		$html['config'] .= '<input type="hidden" name="action" value="saveGroup" />';
		$html['config'] .= '<input type="hidden" name="secure_key" value="'.$this->secure_key.'" />';
		$html['config'] .= $langActive;
		$html['config'] .= '<div class="form-group">
			                    <div class="col-lg-12 ">
				                    <div class="col-lg-12 "><label>'.$this->l('Group title').'</label></div>								    
			                        <div class="col-sm-10">'.$inputTitle.'</div>
			                        <div class="col-sm-2">
			                            <select id="group-lang" onchange="groupChangeLanguage(this.value)">'.$langOptions.'</select>
			                        </div>				                    
			                    </div>
			                </div>';
		$html['config'] .= '<div class="form-group clearfix">
								<div class="col-sm-6">			    
			                        <div class="col-sm-12">
				                        <div class="input-group">
				                            <input type="text" class="form-control mColorPicker" id="icon" name="icon" value="'.$item['icon'].'"  placeholder="Icon" />
				                            <span class="input-group-btn">
				                                <button id="group-icon" type="button" class="btn btn-default"><i class="icon-folder-open"></i></button>
				                            </span>
				                        </div>
			                        </div>
			                    </div>
								<div class="col-sm-6">
			                        <div class="col-sm-12">
				                        <div class="input-group">
				                            <input type="text" class="form-control" id="icon_active" name="icon_active" value="'.$item['icon_active'].'" placeholder="Background" />
				                            <span class="input-group-btn">
				                                <button id="group-iconActive" type="button" class="btn btn-default"><i class="icon-folder-open"></i></button>
				                            </span>
				                        </div>
			                        </div>
			                    </div>
			                </div>';
		$html['config'] .= '<div class="form-group">									
									<div class="col-sm-6">						    
			                        	<div class="col-sm-12"><input type="text" name="custom_class" value="'.$item['custom_class'].'" class="form-control" placeholder="Custom class" /></div>
									</div>
									<div class="col-sm-6">
										<div class="col-sm-12 ">
											<div class="input-group">
				                                <input type="text" class="mColorPicker form-control" id="g_color" name="color" value="'.$params['color'].'" data-hex="true"  />
				                                <span id="icp_g_color" class="mColorPickerTrigger input-group-addon" data-mcolorpicker="true">
				                                    <img src="../img/admin/color.png" />
				                                </span>                                
				                            </div>
										</div>
									</div>
								</div>';
		
		$html['config'] .= '<div class="form-group">
			                    <div class="col-lg-6">
				                    <div class="col-lg-12 "><label>'.$this->l('Group type').'</label></div>								    
			                        <div class="col-sm-12"><select name="type" class="form-control" onchange="groupChangeType(this.value)">'.$this->getCategoryTypeOptions($item['type']).'</select></div>
			                    </div>
			                    <div class="col-sm-6">
									<div class="col-sm-12"><label>'.$this->l('Categories').'</label></div>
									<div class="col-sm-12"><select class="form-control" name="category_id" id="group_category">'.$this->getCategoryOptions($item['category_id'], 0).'</select></div>
								</div>
			                    
			                </div>';
		$html['config'] .= '<div class="group-type-auto" style="display:'.($item['type'] == 'auto' ? 'block' : 'none').'">
								<div class="form-group">
									<div class="col-sm-3">
										<div class="col-sm-12 "><label>'.$this->l('Only Sale').'</label></div>
										<div class="col-sm-12 "><select name="on_sale">'.$this->getOnSaleOptions($item['on_sale']).'</select></div>
									</div>
									<div class="col-sm-3">
										<div class="col-sm-12 "><label>'.$this->l('Only New').'</label></div>
										<div class="col-sm-12 "><select name="on_new">'.$this->getOnNewOptions($item['on_new']).'</select></div>
									</div>
									<div class="col-sm-3">
										<div class="col-sm-12 "><label>'.$this->l('Only Discount').'</label></div>
										<div class="col-sm-12 "><select name="on_discount">'.$this->getOnDiscountOptions($item['on_discount']).'</select></div>
									</div>									
									<div class="col-sm-3">
										<div class="col-sm-12 "><label>'.$this->l('Product condition').'</label></div>
										<div class="col-sm-12 "><select name="on_condition">'.self::getOnConditionOptions($item['on_condition']).'</select></div>
									</div>
									
									
																	
								</div>
								<div class="form-group">
									<div class="col-sm-3">
										<div class="col-sm-12 "><label>'.$this->l('Order by').'</label></div>
										<div class="col-sm-12 "><select name="order_by">'.$this->getOrderByOptions($item['order_by']).'</select></div>
									</div>
									<div class="col-sm-3">
										<div class="col-sm-12 "><label>'.$this->l('Order way').'</label></div>
										<div class="col-sm-12 "><select name="order_way">'.$this->getOrderWayOptions($item['order_way']).'</select></div>
									</div>
									<div class="col-sm-3">
										<div class="col-sm-12 "><label>'.$this->l('Max item').'</label></div>
										<div class="col-sm-12 "><input type="text" name="maxItem" value="'.$item['maxItem'].'" class="form-control" /></div>
									</div>									
								</div>
							</div>';
		$html['config'] .= '<div class="group-type-manual" style="display:'.($item['type'] == 'manual' ? 'block' : 'none').'">								
								<div class="form-group">
									<div class="col-sm-12">
										<div class="col-sm-12 "><label>'.$this->l('Product list').' [<a href="javascript:void(0)" class="add-manual-product" data-group="'.$id.'" onclick="openProductsModal(\''.$id.'\')">'.$this->l('add product').'</a>]</label></div>
										<div class="col-sm-12 ">
											<ul id="group-list-products" class="result-list-products">'.$this->getGroupProduct($id).'</ul>
										</div>
									</div>
								</div>
							</div>';
		
		return $html;
	}
	
	protected function getCurrentUrl($excls=array())
	{
		$pageURL = 'http';		
     	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
     	$pageURL .= "://";
     	if ($_SERVER["SERVER_PORT"] != "80") {
    		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    	} else {
    		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     	}
     	return $pageURL;
	}
	public function getContent()
	{
		
		// foreach(self::$arrPosition as $hook)
				// $this->registerHook($hook); 
		//$this->registerHook('displaySmartBlogLeft') ;
		//$this->registerHook('displaySmartBlogRight') ;
		//$this->registerHook('displayBottomProduct') ;
		//$this->registerHook('displayLeftColumn');
		//$subject = 'asdas d sad sa dsa dsa dsa {deal}["cat":1, "count":2]{/deal} asda sdas dsa';
        //$pattern = '/\{deal\}(.*?)\{\/deal\}/';
        //$aaa = preg_replace( $pattern, 'abc', $subject);
        //echo $aaa;
        //die;
        //$abc = preg_match_all($pattern, $subject, $match);
        //print_r($match);
        //die;
		$action = Tools::getValue('action', 'view');
		if($action == 'view'){			
			$langId = $this->context->language->id;
	        $shopId = $this->context->shop->id;
	        $this->context->controller->addJquery();
            $this->context->controller->addJQueryUI('ui.sortable');	       
			$this->context->controller->addJS(($this->_path).'js/back-end/common.js');                
	        $this->context->controller->addJS(($this->_path).'js/back-end/ajaxupload.3.5.js');
			$this->context->controller->addJS(($this->_path).'js/back-end/jquery.serialize-object.min.js');
			$this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/plugins/jquery.tablednd.js');
			$this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/plugins/jquery.colorpicker.js');
            $this->context->controller->addJS(__PS_BASE_URI__.'js/tiny_mce/tinymce.min.js');
			$this->context->controller->addJS(($this->_path).'js/back-end/tinymce.inc.js');
	        $this->context->controller->addCSS(($this->_path).'css/back-end/style.css');
			$items = Db::getInstance()->executeS("Select DISTINCT m.*, ml.name From "._DB_PREFIX_."simplecategory_module as m Left Join "._DB_PREFIX_."simplecategory_module_lang as ml On ml.module_id = m.id Where m.id_shop='".$shopId."' AND ml.id_lang = ".$langId." Order By ordering");			
	        $this->context->smarty->assign(array(
	        	'baseModuleUrl'=> __PS_BASE_URI__.'modules/'.$this->name,
	            'currentUrl'=> $this->getCurrentUrl(),	            
	            'moduleId'=>$this->id,
	            'langId'=>$langId,
	            'iso'=>$this->context->language->iso_code,
	            'ad'=>$ad = dirname($_SERVER["PHP_SELF"]),
	            'langOptions'=> $this->getLangOptions(),
	            'secure_key'=>$this->secure_key,
	            'items'=>$items,
	            'moduleForm'=>$this->renderModuleForm(),
	            'groupForm'=>$this->renderGroupForm()
	        ));
			return $this->display(__FILE__, 'views/templates/admin/modules.tpl');			
		}else if($action == 'data-export'){
			$this->exportSameData();
			echo $this->l('Export data success!');
			die;
			die(Tools::jsonEncode($this->l('Export data success!')));
		}elseif($action == 'data-import'){
			$this->importSameData();
			echo $this->l('Install data success!');
			die;
			die(Tools::jsonEncode($this->l('Install data success!'))); 		
		}else{
			if(method_exists ($this, $action)){			 
				$this->$action();
			}else{
				$response = new stdClass();
				if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		            $response->status = '1';
            		$response->msg = $this->l("Method ".$action."() not found!.");
					die(Tools::jsonEncode($response));
		        }else{
		        	die($this->l("Method ".$action."() not found!."));
		        }
			}			
		}
	}
	function exportSameData($directory=''){		
	   if($directory) self::$sameDatas = $directory;
		$link = mysql_connect(_DB_SERVER_,_DB_USER_,_DB_PASSWD_);
		mysql_select_db(_DB_NAME_,$link);
        //$curent_id_option = Configuration::get('OVIC_CURRENT_OPTION');
        $curent_id_option = Configuration::get('OVIC_CURRENT_DIR');
        if($curent_id_option) $curent_id_option .= '.';         
        else $curent_id_option = '';
        
		foreach(self::$tables as $table=>$type){
            if($type == 'view') continue;
			$fields = array();
			$query2 = mysql_query('SHOW COLUMNS FROM '._DB_PREFIX_.$table);				
				while($row = mysql_fetch_row($query2))
					$fields[] = $row[0];
			$return = '';            
			if($type == 'lang'){
				$query1 = mysql_query('SELECT * FROM '._DB_PREFIX_.$table." Where id_lang = ". $this->context->language->id);		
				$num_fields = mysql_num_fields($query1);
				for ($i = 0; $i < $num_fields; $i++) 
				{
					while($row = mysql_fetch_row($query1))
					{
						$return.= 'INSERT INTO PREFIX_'.$table.' VALUES(';
						for($j=0; $j<$num_fields; $j++) 
						{
							$row[$j] = addslashes($row[$j]);
							$row[$j] = ereg_replace("\n","\\n",$row[$j]);
							if (isset($row[$j])) {
								if($fields[$j] == 'id_lang')
								 	$return.= '"id_lang"' ;
								elseif($fields[$j] == 'linkType')
								 	$return.= '"CUSTOMLINK-0"' ;
								elseif($fields[$j] == 'link')
									$return.= '"#"';
                                elseif($fields[$j] == 'category_id')
                                    $return .= '"category_id'.$row[$j].'"';
                                elseif($fields[$j] == 'product_id')
                                    $return .= '"product_id'.$row[$j].'"';
								else
									$return.= '"'.$row[$j].'"' ; 
							} else {
								 $return.= '""'; 
							}
							if ($j<($num_fields-1)) { $return.= ','; }
						}
						$return.= ");\n";
					}
				}
			}elseif($type == 'position' || $type =='module'){			 
				$query1 = mysql_query('SELECT * FROM '._DB_PREFIX_.$table);		
				$num_fields = mysql_num_fields($query1);                
				for ($i = 0; $i < $num_fields; $i++) 
				{
					while($row = mysql_fetch_row($query1))
					{
						$return.= 'INSERT INTO PREFIX_'.$table.' VALUES(';
						for($j=0; $j<$num_fields; $j++) 
						{
							$row[$j] = addslashes($row[$j]);
							$row[$j] = ereg_replace("\n","\\n",$row[$j]);
							if (isset($row[$j])) {
								if($fields[$j] == 'position_id')
								 	$return.= '"position_id"' ;
								elseif($fields[$j] == 'linkType')
								 	$return.= '"CUSTOMLINK-0"' ;
								elseif($fields[$j] == 'link')
									$return.= '"#"';
                                elseif($fields[$j] == 'category_id')
                                    $return .= '"category_id'.$row[$j].'"';
                                elseif($fields[$j] == 'product_id')
                                    $return .= '"product_id'.$row[$j].'"';
								else
									$return.= '"'.$row[$j].'"' ; 
							} else {
								 $return.= '""'; 
							}
							if ($j<($num_fields-1)) { $return.= ','; }
						}
						$return.= ");\n";
					}
				}
                
			}else{
				$query1 = mysql_query('SELECT * FROM '._DB_PREFIX_.$table);		
				$num_fields = mysql_num_fields($query1);
				for ($i = 0; $i < $num_fields; $i++) 
				{
					while($row = mysql_fetch_row($query1))
					{
						$return.= 'INSERT INTO PREFIX_'.$table.' VALUES(';
						for($j=0; $j<$num_fields; $j++) 
						{
							$row[$j] = addslashes($row[$j]);
							$row[$j] = ereg_replace("\n","\\n",$row[$j]);
							if (isset($row[$j])) {
								if($fields[$j] == 'linkType')
								 	$return.= '"CUSTOMLINK-0"' ;
								elseif($fields[$j] == 'link')
									$return.= '"#"';
                                elseif($fields[$j] == 'category_id')
                                    $return .= '"category_id'.$row[$j].'"';
                                elseif($fields[$j] == 'product_id')
                                    $return .= '"product_id'.$row[$j].'"';                                	
								else
									$return.= '"'.$row[$j].'"' ; 
                        
							} else {
								 $return.= '""'; 
							}
							if ($j<($num_fields-1)) { $return.= ','; }
						}
						$return.= ");\n";
					}
				}					
			}           
			//$return.="\n\n\n";
			$handle = fopen(self::$sameDatas.$curent_id_option.$table.'.sql','w+');
			fwrite($handle,$return);
			fclose($handle);
		}
		return true;
	}
	function saveModule(){	    
		$languages = $this->getAllLanguages();
		$shopId = $this->context->shop->id;
		$itemId = Tools::getValue('moduleId', 0);
		$names = Tools::getValue('names', array());
		$position_name = Tools::getValue('position_name', '');
		$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
		$position_id = 0;
		$positions = Tools::getValue('positions', array());
		$categoryId = Tools::getValue('categoryId', 0);		
		$moduleLayout = Tools::getValue('moduleLayout', 'default');
		$custom_class = Tools::getValue('custom_class', '');
		$item_min_width = intval(Tools::getValue('item_min_width', 0));
		$display_name = Tools::getValue('module_display_name', 1);		
       	$response = new stdClass();
		$icon = Tools::getValue('icon', '');
		$icon_active = Tools::getValue('icon_active', '');
		$color = Tools::getValue('color', '');
		$type = Tools::getValue('type', 'auto');
		$on_sale = Tools::getValue('on_sale', 2);
		$on_new = Tools::getValue('on_new', 2);
		$on_discount = Tools::getValue('on_discount', 2);
		$on_condition = Tools::getValue('on_condition', 'all');
		$order_by = Tools::getValue('order_by', 'date_add');
		$order_way = Tools::getValue('order_way', 'desc');
		$maxItem = Tools::getValue('maxItem', 12);
		$product_ids = Tools::getValue('product_ids', array());
        $moduleDescriptions = Tools::getValue('descriptions', array());
		if($itemId == 0){			
       		$maxOrdering = $db->getValue("Select MAX(ordering) From "._DB_PREFIX_."simplecategory_module Where id_shop = ".$shopId);
			if($maxOrdering >0) $maxOrdering++;
			else $maxOrdering = 1;
			if($icon){
				if(strpos($icon, '.') !== false && strpos($icon, 'http') === false){
					if(file_exists($this->pathImage.'temps/'.$icon)){
						copy($this->pathImage.'temps/'.$icon, $this->pathImage.'icons/'.$icon);
						unlink($this->pathImage.'temps/'.$icon);
					}		
				} 
			}						
			if($icon_active){
				if(strpos($icon_active, '.') !== false && strpos($icon_active, 'http') === false){
					if(file_exists($this->pathImage.'temps/'.$icon_active)){
						copy($this->pathImage.'temps/'.$icon_active, $this->pathImage.'icons/'.$icon_active);
						unlink($this->pathImage.'temps/'.$icon_active);
					}		
				} 
			}			
			$params = array('color'=>$color, 'icon'=>$icon, 'icon_active'=>$icon_active);			
            if($db->execute("Insert Into "._DB_PREFIX_."simplecategory_module 
            	(`id_shop`, `category_id`, `position_id`, `position_name`, `maxItem`, `order_by`, `on_condition`, `on_sale`, `on_new`, `on_discount`, `layout`, `type`, `order_way`, `custom_class`, `status`, `ordering`, `display_name`, `params`) Values 
            	('".$shopId."', '$categoryId', '$position_id', '$position_name', '$maxItem', '$order_by', '$on_condition', '$on_sale', '$on_new', '$on_discount', '$moduleLayout', '$type', '$order_way',  '$custom_class', '1', '$maxOrdering', '$display_name', '".json_encode($params)."')")){
                $insertId = $db->Insert_ID();
				if($type == 'manual'){
					if($product_ids){
						foreach ($product_ids as $index=>$product_id) {
							Db::getInstance()->execute("Insert into "._DB_PREFIX_."simplecategory_module_product (`module_id`, `product_id`, `ordering`) Values ('$insertId', '$product_id', '".($index + 1)."')");
						}
					}	
				}
				//Db::getInstance()->escape()
                if($languages){
                	$insertSql = array();
                	foreach($languages as $i=>$language){
                		$images = Tools::getValue('bannerNames'.$language->id, array());
						$links = Tools::getValue('bannerLink'.$language->id, array());
						$alts = Tools::getValue('bannerAlt'.$language->id, array());
                        $descriptions = Tools::getValue('bannerDescription'.$language->id, array());
						$banners = array();
						if($images){
							foreach ($images as $j => $image) {							     
								if($image && file_exists($this->pathImage.'temps/'.$image)){
									if(copy($this->pathImage.'temps/'.$image, $this->pathImage.'b/'.$image)){                                        
										$banners[] = array('image'=>$image, 'link'=>$db->escape($links[$j]), 'alt'=>$db->escape($alts[$j]), 'description'=>$db->escape($descriptions[$j], true));
										unlink($this->pathImage.'temps/'.$image);			
									}									
								}
							}							
							$insertSql[] = array('module_id'=>$insertId, 'id_lang'=>$language->id, 'name'=>$db->escape($names[$i]), 'banners'=>json_encode($banners), 'description'=>$db->escape($moduleDescriptions[$i], true)) ;
						}else{
							$insertSql[] = array('module_id'=>$insertId, 'id_lang'=>$language->id, 'name'=>$db->escape($names[$i]), 'banners'=>'', 'description'=>$db->escape($moduleDescriptions[$i], true)) ;
						}						
                	}					
					if($insertSql) $db->insert('simplecategory_module_lang', $insertSql);
                }                
                $response->status = 1;
				$response->msg = $this->l('Add new module success!');
            }else{
            	$response->status = 0;
				$response->msg = $this->l('Error: Add new module failed!');
            }
       }else{
            
            $item = $db->getRow("Select * From "._DB_PREFIX_."simplecategory_module Where id = ".$itemId);			
            if($item){
            	$params = array();
				$params['color'] = $color;
            	if($item['params']){
            		$itemParams = get_object_vars(json_decode($item['params']));
					if($icon){
						if(strpos($icon, '.') === false || strpos($icon, 'http') !== false){
							$params['icon'] = $icon;
						}else{
							if(file_exists($this->pathImage.'temps/'.$icon)){
								copy($this->pathImage.'temps/'.$icon, $this->pathImage.'icons/'.$icon);
								unlink($this->pathImage.'temps/'.$icon);
								if(isset($itemParams['icon']) && $itemParams['icon'] != '' && file_exists($this->pathImage.'icons/'.$itemParams['icon'])) 
									unlink($this->pathImage.'icons/'.$itemParams['icon']);
								$params['icon'] = $icon;
							}else{
								$params['icon'] = $itemParams['icon'];
							}	
						}
					}else{
						$params['icon'] = '';
					}
					if($icon_active){
						if(strpos($icon_active, '.') === false || strpos($icon_active, 'http') !== false){
							$params['icon_active'] = $icon_active;
						}else{
							if(file_exists($this->pathImage.'temps/'.$icon_active)){
								copy($this->pathImage.'temps/'.$icon_active, $this->pathImage.'icons/'.$icon_active);
								unlink($this->pathImage.'temps/'.$icon_active);
								if(isset($itemParams['icon_active']) && $itemParams['icon_active'] != '' && file_exists($this->pathImage.'icons/'.$itemParams['icon_active'])) 
									unlink($this->pathImage.'icons/'.$itemParams['icon_active']);
								$params['icon_active'] = $icon_active;
							}else{
								$params['icon_active'] = $itemParams['icon_active'];
							}		
						}
						
					}else{
						$params['icon_active'] = '';
					}
            	}else{
            		if($icon){
            			if(strpos($icon, '.') === false || strpos($icon, 'http') !== false){
							$params['icon'] = $icon;
						}else{
							if(file_exists($this->pathImage.'temps/'.$icon)){
								copy($this->pathImage.'temps/'.$icon, $this->pathImage.'icons/'.$icon);
								unlink($this->pathImage.'temps/'.$icon);
								$params['icon'] = $icon;
							}else{
								$params['icon'] = '';
							}	
						}
	            		
            		}else{
            			$params['icon'] = '';
            		}
					if($icon_active){
						if(strpos($icon_active, '.') === false || strpos($icon_active, 'http') !== false){
							$params['icon_active'] = $icon_active;
						}else{
							if(file_exists($this->pathImage.'temps/'.$icon_active)){
								copy($this->pathImage.'temps/'.$icon_active, $this->pathImage.'icons/'.$icon_active);
								unlink($this->pathImage.'temps/'.$icon_active);
								$params['icon_active'] = $icon_active;
							}else{
								$params['icon_active'] = '';
							}	
						}
	            		
            		}else{
            			$params['icon_active'] = '';
            		}            		
            	}            	
                $db->execute("Update "._DB_PREFIX_."simplecategory_module Set 
                	`category_id`='".$categoryId."', 
                	`position_id`='$position_id', 
                	`position_name`= '$position_name', 
                	`maxItem`='".$maxItem."', 
                	`order_by`='".$order_by."', 
                	`order_way` = '".$order_way."', 
                	`on_condition` = '".$on_condition."', 
                	`on_sale` = '".$on_sale."', 
                	`on_new` = '".$on_new."', 
                	`on_discount` = '".$on_discount."', 
                	`type` = '".$type."',                 	
                	`layout` = '$moduleLayout', 
                	`custom_class` = '$custom_class', 
                	`display_name`='$display_name', 
                	`params`='".json_encode($params)."' 
                	Where id = ".$itemId);                			
				//Db::getInstance()->execute("Delete From "._DB_PREFIX_."simplecategory_module_position Where `module_id` = ".$itemId);
				Db::getInstance()->execute("Delete From "._DB_PREFIX_."simplecategory_module_product Where `module_id` = ".$itemId);
				if($type == 'manual'){
					if($product_ids){
						foreach ($product_ids as $index=>$product_id) {
							Db::getInstance()->execute("Insert into "._DB_PREFIX_."simplecategory_module_product (`module_id`, `product_id`, `ordering`) Values ('$itemId', '$product_id', '".($index + 1)."')");
						}
					}	
				}
				if($languages){
                	$insertSql = array();
                	foreach($languages as $i=>$language){
                		$check = DB::getInstance()->getValue("Select module_id From "._DB_PREFIX_."simplecategory_module_lang Where module_id = ".$itemId." AND id_lang = ".$language->id);						
                		$images = Tools::getValue('bannerNames'.$language->id, array());
						$links = Tools::getValue('bannerLink'.$language->id, array());
						$alts = Tools::getValue('bannerAlt'.$language->id, array());
                        $descriptions = Tools::getValue('bannerDescription'.$language->id, array());
						$banners = array();
						if($images){
							foreach ($images as $j => $image) {
								if($image && file_exists($this->pathImage.'temps/'.$image)){
									if(copy($this->pathImage.'temps/'.$image, $this->pathImage.'b/'.$image)){										
										unlink($this->pathImage.'temps/'.$image);			
									}									
								}
								$banners[] = array('image'=>$image, 'link'=>$db->escape($links[$j]), 'alt'=>$db->escape($alts[$j]), 'description'=>$db->escape($descriptions[$j], true));
							}
							if($check){
								$db->execute("Update "._DB_PREFIX_."simplecategory_module_lang Set `name`='".$db->escape($names[$i])."', `description`='".$db->escape($moduleDescriptions[$i], true)."', `banners`='".json_encode($banners)."' Where `module_id` = '".$itemId."' AND `id_lang` = '".$language->id."'");	
							}else{
								$insertSql[] = array('module_id'=>$itemId, 'id_lang'=>$language->id, 'name'=>$db->escape($names[$i]), 'banners'=>json_encode($banners), 'description'=>$db->escape($moduleDescriptions[$i], true)) ;
							}							
						}else{
							if($check){
								$db->execute("Update "._DB_PREFIX_."simplecategory_module_lang Set `name`='".$db->escape($names[$i])."', `description`='".$db->escape($moduleDescriptions[$i], true)."', `banners`='' Where `module_id` = '".$itemId."' AND `id_lang` = ".$language->id);	
							}else{
								$insertSql[] = array('module_id'=>$itemId, 'id_lang'=>$language->id, 'name'=>$db->escape($names[$i]), 'banners'=>'', 'description'=>$db->escape($moduleDescriptions[$i], true)) ;
							}
						}						
                	}
					if($insertSql) $db->insert('simplecategory_module_lang', $insertSql);
                }
				$response->status = 1;
				$response->msg = $this->l('Update module success!');				   
            }else{
				$response->status = 1;
				$response->msg = $this->l('Error: Not isset Group.');
            }
       }
       die(Tools::jsonEncode($response));               
	}
	
	function saveGroup(){
		$languages = $this->getAllLanguages();
		$moduleId = Tools::getValue('moduleId', 0);
		$itemId = Tools::getValue('groupId', 0);
		$names = Tools::getValue('names', array());
		$category_id = Tools::getValue('category_id', 0);
		$icon = Tools::getValue('icon', '');
		$icon_active = Tools::getValue('icon_active', '');		
		$type = Tools::getValue('type', 'auto');
		$on_sale = Tools::getValue('on_sale', 2);
		$on_new = Tools::getValue('on_new', 2);
		$on_discount = Tools::getValue('on_discount', 2);
		$on_condition = Tools::getValue('on_condition', 'all');		
		$order_by = Tools::getValue('order_by', 'add');
		$order_way = Tools::getValue('order_way', 'desc');		
		$maxItem = Tools::getValue('maxItem', 12);		
		$product_ids = Tools::getValue('product_ids', array());
		$custom_class = Tools::getValue('custom_class', '');
		$groupDescriptions = Tools::getValue('descriptions', array());
		$params = '';
       	$response = new stdClass();
	   	$db = Db::getInstance();
		if($itemId == 0){
       		$maxOrdering = $db->getValue("Select MAX(ordering) From "._DB_PREFIX_."simplecategory_group");
			if($maxOrdering >0) $maxOrdering++;
			else $maxOrdering = 1;
			if($icon){
				if(strpos($icon, '.') !== false && strpos($icon, 'http') === false){
					if(file_exists($this->pathImage.'temps/'.$icon)){
						copy($this->pathImage.'temps/'.$icon, $this->pathImage.'icons/'.$icon);
						unlink($this->pathImage.'temps/'.$icon);
					}		
				} 
			}						
			if($icon_active){
				if(strpos($icon_active, '.') !== false && strpos($icon_active, 'http') === false){
					if(file_exists($this->pathImage.'temps/'.$icon_active)){
						copy($this->pathImage.'temps/'.$icon_active, $this->pathImage.'icons/'.$icon_active);
						unlink($this->pathImage.'temps/'.$icon_active);
					}		
				} 
			}
			
			$params = array('color'=>$color, 'icon'=>$icon, 'icon_active'=>$icon_active);	
            if($db->execute("Insert Into "._DB_PREFIX_."simplecategory_group (`module_id`, `category_id`, `maxItem`, `on_condition`, `order_by`, `order_way`, `on_sale`, `on_new`, `on_discount`, `type`, `ordering`, `status`, `custom_class`, `params`) Values ('".$moduleId."', '".$category_id."', '$maxItem', '$on_condition', '$order_by', '$order_way', '$on_sale', '$on_new', '$on_discount', '$type', '$maxOrdering', '1', '$custom_class', '".json_encode($params)."')")){
                $insertId = $db->Insert_ID();
				if($type == 'manual'){
					if($product_ids){
						foreach ($product_ids as $index=>$product_id) {
							Db::getInstance()->execute("Insert into "._DB_PREFIX_."simplecategory_group_product (`group_id`, `product_id`, `ordering`) Values ('$insertId', '$product_id', '".($index + 1)."')");
						}
					}	
				}
                if($languages){
                	$insertSql = array();
                	foreach($languages as $i=>$language){
                		$images = Tools::getValue('bannerNames'.$language->id, array());
						$links = Tools::getValue('bannerLink'.$language->id, array());
						$alts = Tools::getValue('bannerAlt'.$language->id, array());
                        $descriptions = Tools::getValue('bannerDescription'.$language->id, array());
						$banners = array();
						if($images){
							foreach ($images as $j => $image) {
								if($image && file_exists($this->pathImage.'temps/'.$image)){
									if(copy($this->pathImage.'temps/'.$image, $this->pathImage.'b/'.$image)){
										$banners[] = array('image'=>$image, 'link'=>$db->escape($links[$j]), 'alt'=>$db->escape($alts[$j]), 'description'=>$db->escape($descriptions[$j], true));
										unlink($this->pathImage.'temps/'.$image);			
									}									
								}
							}							
							$insertSql[] = array('group_id'=>$insertId, 'id_lang'=>$language->id, 'name'=>$db->escape($names[$i]), 'banners'=>json_encode($banners), 'description'=>$db->escape($groupDescriptions[$i], true)) ;
						}else{
							$insertSql[] = array('group_id'=>$insertId, 'id_lang'=>$language->id, 'name'=>$db->escape($names[$i]), 'banners'=>'', 'description'=>$db->escape($groupDescriptions[$i], true)) ;
						}						
                	}					
					if($insertSql) $db->insert('simplecategory_group_lang', $insertSql);
                }                
                $response->status = 1;
				$response->msg = $this->l('Add new group success!');
            }else{
            	$response->status = 0;
				$response->msg = $this->l('Error: Add new group failed!');
            }
       }else{       		
            $item = $db->getRow("Select * From "._DB_PREFIX_."simplecategory_group Where id = ".$itemId);
			
            if($item){
            	$params = array();
				$params['color'] = $color;
            	if($item['params']){
            		$itemParams = get_object_vars(json_decode($item['params']));
					
					if($icon){
						if(strpos($icon, '.') === false || strpos($icon, 'http') !== false){
							$params['icon'] = $icon;
						}else{
							if(file_exists($this->pathImage.'temps/'.$icon)){
								copy($this->pathImage.'temps/'.$icon, $this->pathImage.'icons/'.$icon);
								unlink($this->pathImage.'temps/'.$icon);
								if(isset($itemParams['icon']) && $itemParams['icon'] != '' && file_exists($this->pathImage.'icons/'.$itemParams['icon'])) 
									unlink($this->pathImage.'icons/'.$itemParams['icon']);
								$params['icon'] = $icon;
							}else{
								$params['icon'] = $itemParams['icon'];
							}	
						}
							
					}else{
						$params['icon'] = '';
					}
					
					if($icon_active){
						if(strpos($icon_active, '.') === false || strpos($icon_active, 'http') !== false){
							$params['icon_active'] = $icon_active;
						}else{
							if(file_exists($this->pathImage.'temps/'.$icon_active)){
								copy($this->pathImage.'temps/'.$icon_active, $this->pathImage.'icons/'.$icon_active);
								unlink($this->pathImage.'temps/'.$icon_active);
								if(isset($itemParams['icon_active']) && $itemParams['icon_active'] != '' && file_exists($this->pathImage.'icons/'.$itemParams['icon_active'])) 
									unlink($this->pathImage.'icons/'.$itemParams['icon_active']);
								$params['icon_active'] = $icon_active;
							}else{
								$params['icon_active'] = $itemParams['icon_active'];
							}	
						}
							
					}else{
						$params['icon_active'] = '';
					}
            	}else{
            		if($icon){
            			if(strpos($icon, '.') === false || strpos($icon, 'http') !== false){
							$params['icon'] = $icon;
						}else{
							if(file_exists($this->pathImage.'temps/'.$icon)){
								copy($this->pathImage.'temps/'.$icon, $this->pathImage.'icons/'.$icon);
								unlink($this->pathImage.'temps/'.$icon);
								$params['icon'] = $icon;
							}else{
								$params['icon'] = '';
							}	
						}
	            		
            		}else{
            			$params['icon'] = '';
            		}
					if($icon_active){
						if(strpos($icon_active, '.') === false || strpos($icon_active, 'http') !== false){
							$params['icon_active'] = $icon_active;
						}else{
							if(file_exists($this->pathImage.'temps/'.$icon_active)){
								copy($this->pathImage.'temps/'.$icon_active, $this->pathImage.'icons/'.$icon_active);
								unlink($this->pathImage.'temps/'.$icon_active);
								$params['icon_active'] = $icon_active;
							}else{
								$params['icon_active'] = '';
							}	
						}	            		
            		}else{
            			$params['icon_active'] = '';
            		}            		
            	}            	
                Db::getInstance()->execute("Update "._DB_PREFIX_."simplecategory_group Set 
                	`maxItem`='".$maxItem."', 
                	`category_id`='".$category_id."', 
                	`order_by` = '$order_by', 
                	`order_way` = '$order_way', 
                	`on_condition`='$on_condition', 
                	`on_sale` = '".$on_sale."', 
                	`on_new` = '".$on_new."', 
                	`on_discount` = '".$on_discount."',                 	
                	`type`='$type', 
                	`custom_class`='$custom_class', 
                	`params`='".json_encode($params)."' 
                	Where id = ".$itemId);				
				Db::getInstance()->execute("Delete From "._DB_PREFIX_."simplecategory_group_product Where `group_id` = ".$itemId);
				if($type == 'manual'){
					if($product_ids){
						foreach ($product_ids as $index=>$product_id) {
							Db::getInstance()->execute("Insert into "._DB_PREFIX_."simplecategory_group_product (`group_id`, `product_id`, `ordering`) Values ('$itemId', '$product_id', '".($index + 1)."')");
						}
					}	
				}				
				if($languages){
                	$insertSql = array();
                	foreach($languages as $i=>$language){
                		$check = DB::getInstance()->getRow("Select * From "._DB_PREFIX_."simplecategory_group_lang Where group_id = ".$itemId." AND id_lang = ".$language->id);
                		$images = Tools::getValue('bannerNames'.$language->id, array());
						$links = Tools::getValue('bannerLink'.$language->id, array());
						$alts = Tools::getValue('bannerAlt'.$language->id, array());
                        $descriptions = Tools::getValue('bannerDescription'.$language->id, array());
						$banners = array();						
						if($images){
							foreach ($images as $j => $image) {
								if($image && file_exists($this->pathImage.'temps/'.$image)){
									if(copy($this->pathImage.'temps/'.$image, $this->pathImage.'b/'.$image)){										
										unlink($this->pathImage.'temps/'.$image);			
									}									
								}
                                $banners[] = array('image'=>$image, 'link'=>$db->escape($links[$j]), 'alt'=>$db->escape($alts[$j]), 'description'=>$db->escape($descriptions[$j], true));
							}
							if($check){
								$db->execute("Update "._DB_PREFIX_."simplecategory_group_lang Set `name`='".$db->escape($names[$i])."', `description`='".$db->escape($groupDescriptions[$i], true)."', `banners`='".json_encode($banners)."' Where `group_id` = '".$itemId."' AND `id_lang` = '".$language->id."'");	
							}else{
								$insertSql[] = array('group_id'=>$itemId, 'id_lang'=>$language->id, 'name'=>$db->escape($names[$i]), 'banners'=>json_encode($banners), 'description'=>$db->escape($groupDescriptions[$i], true)) ;
							}							
						}else{							
							if($check){
								$db->execute("Update "._DB_PREFIX_."simplecategory_group_lang Set `name`='".$db->escape($names[$i])."', `description`='".$db->escape($groupDescriptions[$i], true)."', `banners`='' Where `group_id` = '".$itemId."' AND `id_lang` = ".$language->id);	
							}else{
								$insertSql[] = array('group_id'=>$itemId, 'id_lang'=>$language->id, 'name'=>$db->escape($names[$i]), 'banners'=>'', 'description'=>$db->escape($groupDescriptions[$i], true)) ;
							}
						}						
                	}
					if($insertSql) $db->insert('simplecategory_group_lang', $insertSql);
                }
				$response->status = 1;
				$response->msg = $this->l('Update group success!');				   
            }else{
				$response->status = 1;
				$response->msg = $this->l('Error: Not isset Group.');
            }
       }
       die(Tools::jsonEncode($response));               
	}
	protected function changeModuleStatus(){
		$itemId = intval($_POST['itemId']);
		$value = intval($_POST['value']);		
		$response = new stdClass();
		if($value == '1'){
			DB::getInstance()->execute("Update "._DB_PREFIX_."simplecategory_module Set `status` = 0 Where id = ".$itemId);
			$response->status = 1;
			$response->msg = $this->l('Update status success');
		}else{
			DB::getInstance()->execute("Update "._DB_PREFIX_."simplecategory_module Set `status` = 1 Where id = ".$itemId);
			$response->status = 1;
			$response->msg = $this->l('Update status success');
		}		
		die(Tools::jsonEncode($response));
	}
	protected function changeGroupStatus(){
		$itemId = intval($_POST['itemId']);
		$value = intval($_POST['value']);		
		$response = new stdClass();
		if($value == '1'){
			DB::getInstance()->execute("Update "._DB_PREFIX_."simplecategory_group Set `status` = 0 Where id = ".$itemId);
			$response->status = 1;
			$response->msg = $this->l('Update status success');
		}else{
			DB::getInstance()->execute("Update "._DB_PREFIX_."simplecategory_group Set `status` = 1 Where id = ".$itemId);
			$response->status = 1;
			$response->msg = $this->l('Update status success');
		}		
		die(Tools::jsonEncode($response));
	}
	function loadModuleItem(){
		$itemId = intval($_POST['itemId']);
		$response = new stdClass();
		if($itemId >0){			
			$response = $this->renderModuleForm($itemId);
			$response['status'] = 1;
			$response['msg']=$this->l("load module success!");
		}else{
			$response['status'] = 0;
			$response['msg']=$this->l("module not found!");
		}
		die(Tools::jsonEncode($response));
	}
	function loadGroupItem(){		
		$itemId = intval($_POST['itemId']);
		$response = new stdClass();
		if($itemId >0){
			$response = $this->renderGroupForm($itemId);
			$response['status'] = 1;
			$response['msg']=$this->l("load group success!");
		}else{
			$response['status'] = 0;
			$response['msg']=$this->l("Group not found!");
		}
		die(Tools::jsonEncode($response));
	}
	
	function loadModuleContent(){
		$moduleId = intval($_POST['moduleId']);
		$response = new stdClass();
		if($moduleId >0){
			$response->status = 1;
			$langId = $this->context->language->id;
			$items = Db::getInstance()->executeS("Select g.*, gl.name From "._DB_PREFIX_."simplecategory_group AS g Left Join "._DB_PREFIX_."simplecategory_group_lang AS gl On gl.group_id = g.id Where g.module_id = '$moduleId' AND gl.`id_lang`='$langId' Order By g.ordering");
			$response->content = '';
			if($items){				
				foreach ($items as $key => $item) {
					if($item['status'] == "1"){
	                    $status = '<a title="Enabled" class="list-action-enable action-enabled lik-group-status" data-id="'.$item['id'].'" data-value="'.$item['status'].'"><i class="icon-check"></i></a>';
	                }else{
	                    $status = '<a title="Disabled" class="list-action-enable action-disabled lik-group-status" data-id="'.$item['id'].'" data-value="'.$item['status'].'"><i class="icon-check"></i></a>';
	                }
					if($item['type'] == 'auto'){
						$response->content .= '<tr id="gro_'.$item['id'].'" class="">
													<td>'.$item['name'].'</td>                        
							                        <td class="center">'.$item['type'].'</td>
							                        <td class="center">'.$item['display_only'].'</td>
							                        <td class="center">'.$item['order_by'].'</td>
							                        <td class="center">'.$item['order_way'].'</td>
							                        <td class="center">'.$item['maxItem'].'</td>								
							    				    <td class="pointer dragHandle center" ><div class="dragGroup"><div class="positions">'.$item['ordering'].'</div></div></td>		
							    				    <td class="center">'.$status.'</td>                        
							                        <td class="center">
							                            <a href="javascript:void(0)" item-id="'.$item['id'].'" class="lik-group-edit" title="'.$this->l('Edit group content').'"><i class="icon-edit"></i></a>&nbsp;
							                            <a href="javascript:void(0)" item-id="'.$item['id'].'" class="lik-group-delete" title="'.$this->l('Delete group content').'"><i class="icon-trash" ></i></a>
							                        </td>
												</tr>';	
					}else{
						$rows = Db::getInstance()->executeS("Select product_id From "._DB_PREFIX_."simplecategory_group_product Where group_id = ".$item['id']." Order By ordering");
						$ids = array();
						if($rows){
							foreach ($rows as $key => $row) {
								$ids[] = $row['product_id'];
							}
						}
						$response->content .= '<tr id="gro_'.$item['id'].'" class="">
													<td>'.$item['name'].'</td>                        
							                        <td class="center">'.$item['type'].'</td>
							                        <td colspan="4">'.$this->l('Product ids: ').(implode(', ', $ids)).'</td>	
							    				    <td class="pointer dragHandle center" ><div class="dragGroup"><div class="positions">'.$item['ordering'].'</div></div></td>		
							    				    <td class="center">'.$status.'</td>                        
							                        <td class="center">
							                            <a href="javascript:void(0)" item-id="'.$item['id'].'" class="lik-group-edit"><i class="icon-edit"></i></a>&nbsp;
							                            <a href="javascript:void(0)" item-id="'.$item['id'].'" class="lik-group-delete"><i class="icon-trash" ></i></a>
							                        </td>
												</tr>';
					}
					
				}
			}
		}else{
			$response->status = 0;
			$response->msg = $this->l("Module not found!");
		}
		die(Tools::jsonEncode($response));
	}
	function loadListProducts(){		
        $link = $this->context->link;
        $langId = $this->context->language->id;
        $shopId = $this->context->shop->id;        
        $pageSize = 5;
        $page = intval($_POST['page']);
        //$groupId = intval($_POST['groupId']);
        //$moduleId = intval($_POST['moduleId']);
        $categoryId =  Tools::getValue('categoryId', Configuration::get('PS_HOME_CATEGORY')); // Db::getInstance()->getValue("Select category_id From "._DB_PREFIX_."simplecategory_module Where id = ".$moduleId);
        $keyword = Tools::getValue('keyword', '');
		$productIds = Tools::getValue('productIds', array());
		
        $arrSubCategory = $this->getCategoryIds($categoryId);
        $arrSubCategory[] = $categoryId;
        $offset=($page - 1) * $pageSize;
        $total = $this->getProductList($langId, $arrSubCategory, $productIds, $keyword, true);
		
		$response = new stdClass();
        $response->pagination = '';
        $response->list = '';
        if($total >0){            
            $response->pagination = $this->paginationAjaxEx($total, $pageSize, $page, 6, 'loadListProducts');
            $items = $this->getProductList($langId, $arrSubCategory, '0', $keyword, false, $offset, $pageSize);
            if($items){
                if($items){
                	if($productIds){
                		foreach($items as $item){
	                        $imagePath = $link->getImageLink($item['link_rewrite'], $item['id_image'], 'cart_default');
							if(in_array($item['id_product'], $productIds)){
								$response->list .= '<tr id="pListTr_'.$item['id_product'].'">
	                                                <td>'.$item['id_product'].'</td>
	                                                <td class="center"><img src="'.$imagePath.'" width="50" /></td>
	                                                <td>'.$item['name'].'</td>
	                                                <td>'.$item['reference'].'</td>
	                                                <td class="center">'.$item['price'].'</td>
	                                                <td class="center">'.$item['quantity_all_versions'].'</td>
	                                                <td class="center"><div><a href="javascript:void(0)" id="manual-product-'.$item['id_product'].'" item-id="'.$item['id_product'].'" item-name="'.$item['name'].'" class="link-add-manual-product-off"><i class="icon-check-square-o"></i></a></div></td>
	                                            </tr>';
							}else{
								$response->list .= '<tr id="pListTr_'.$item['id_product'].'">
		                                                <td>'.$item['id_product'].'</td>
		                                                <td class="center"><img src="'.$imagePath.'" width="50" /></td>
		                                                <td>'.$item['name'].'</td>
		                                                <td>'.$item['reference'].'</td>
		                                                <td class="center">'.$item['price'].'</td>
		                                                <td class="center">'.$item['quantity_all_versions'].'</td>
		                                                <td class="center"><div><a href="javascript:void(0)" id="manual-product-'.$item['id_product'].'" item-id="'.$item['id_product'].'" item-name="'.$item['name'].'" class="link-add-manual-product"><i class="icon-plus"></i></a></div></td>
		                                            </tr>';	
							}
	                        
	                    }
                	}else{
	                	foreach($items as $item){
	                        $imagePath = $link->getImageLink($item['link_rewrite'], $item['id_image'], 'cart_default');							
	                        $response->list .= '<tr id="pListTr_'.$item['id_product'].'">
	                                                <td>'.$item['id_product'].'</td>
	                                                <td class="center"><img src="'.$imagePath.'" width="50" /></td>
	                                                <td>'.$item['name'].'</td>
	                                                <td>'.$item['reference'].'</td>
	                                                <td class="center">'.$item['price'].'</td>
	                                                <td class="center">'.$item['quantity_all_versions'].'</td>
	                                                <td class="center"><div><a href="javascript:void(0)" id="manual-product-'.$item['id_product'].'" item-id="'.$item['id_product'].'" item-name="'.$item['name'].'" class="link-add-manual-product"><i class="icon-plus"></i></a></div></td>
	                                            </tr>';
	                    }	
                	}
                    
                }
            }   
        }
        die(Tools::jsonEncode($response));
    }
	function updateModuleOrdering(){
		$ids = $_POST['ids'];        
        if($ids){
            //$strIds = implode(', ', $ids);            
            //$minOrder = DB::getInstance()->getValue("Select Min(ordering) From "._DB_PREFIX_."flexiblecustom_modules Where id IN ($strIds)");            
            foreach($ids as $i=>$id){
                DB::getInstance()->query("Update "._DB_PREFIX_."simplecategory_module Set ordering=".($i + 1)." Where id = ".$id);                
            }
        }
        die(Tools::jsonEncode('Update ordering success!'));
	}
	function updateGroupOrdering(){
		$ids = $_POST['ids'];        
        if($ids){            
            foreach($ids as $i=>$id){
                DB::getInstance()->query("Update "._DB_PREFIX_."simplecategory_group Set ordering=".($i + 1)." Where id = ".$id);                
            }
        }
        die(Tools::jsonEncode('Update ordering success!'));
	}
	function deleteModule(){
		$itemId = intval($_POST['itemId']);  
		$response = new stdClass();
		$params = Db::getInstance()->getValue("Select params From "._DB_PREFIX_."simplecategory_module Where id = $itemId");
		if(DB::getInstance()->execute("Delete From "._DB_PREFIX_."simplecategory_module Where id = ".$itemId)){
			if($params){
				$itemParams = json_decode($params);
				if(isset($itemParams->icon) && $itemParams->icon != "" && file_exists($this->pathImage.$itemParams->icon)) unlink($this->pathImage.$itemParams->icon);
				if(isset($itemParams->icon_active) && $itemParams->icon_active != "" && file_exists($this->pathImage.$itemParams->icon_active)) unlink($this->pathImage.$itemParams->icon_active);
			}
			DB::getInstance()->execute("Delete From "._DB_PREFIX_."simplecategory_module_product Where module_id = ".$itemId);
			$rows = DB::getInstance()->executeS("Select * From "._DB_PREFIX_."simplecategory_module_lang Where module_id = $itemId");
            if($rows){
            	foreach($rows as $row){
            		if($row['banners']){
            			$banners = json_decode($row['banners']);
            			if($banners){
            				foreach($banners as $banner){
            					if($banner->image && file_exists($this->pathImage.'b/'.$banner->image)) unlink($module->pathImage.'b/'.$banner->image);
            				}
            			}
            		}
            	}
            }
            DB::getInstance()->execute("Delete From "._DB_PREFIX_."simplecategory_module_lang Where module_id = ".$itemId);
			$rows = DB::getInstance()->executeS("Select * From "._DB_PREFIX_."simplecategory_group_lang Where group_id IN (Select id From "._DB_PREFIX_."simplecategory_group Where module_id = $itemId)");
			if($rows){
            	foreach($rows as $row){
            		if($row['banners']){
            			$banners = json_decode($row['banners']);
            			if($banners){
            				foreach($banners as $banner){
            					if($banner->image && file_exists($this->pathImage.'b/'.$banner->image)) unlink($module->pathImage.'b/'.$banner->image);
            				}
            			}
            		}
            	}
            }
			DB::getInstance()->execute("Delete From "._DB_PREFIX_."simplecategory_group_lang Where group_id IN (Select id From "._DB_PREFIX_."simplecategory_group Where module_id = $itemId)");
			Db::getInstance()->execute("Delete From "._DB_PREFIX_."simplecategory_group_product Where group_id IN (Select id From "._DB_PREFIX_."simplecategory_group Where module_id = $itemId)");
			Db::getInstance()->execute("Delete From "._DB_PREFIX_."simplecategory_group Where module_id = $itemId");
			$response->status = 1;
			$response->msg = 'Delete module success!';				
        }else{
            $response->status = 0;
			$response->msg = 'Delete module not success!';    
        }
		die(Tools::jsonEncode($response));		
	}
	function deleteGroup(){
		$itemId = intval($_POST['itemId']);  
		$response = new stdClass();
		$params = Db::getInstance()->getValue("Select params From "._DB_PREFIX_."simplecategory_group Where id = ".$itemId);
		if(DB::getInstance()->execute("Delete From "._DB_PREFIX_."simplecategory_group Where id = ".$itemId)){
			if($params){
				$itemParams = json_decode($params);
				if(isset($itemParams->icon) && $itemParams->icon != "" && file_exists($this->pathImage.$itemParams->icon)) unlink($this->pathImage.$itemParams->icon);
				if(isset($itemParams->icon_active) && $itemParams->icon_active != "" && file_exists($this->pathImage.$itemParams->icon_active)) unlink($this->pathImage.$itemParams->icon_active);
			}			
			$rows = DB::getInstance()->executeS("Select * From "._DB_PREFIX_."simplecategory_group_lang Where group_id = $itemId");
			if($rows){
            	foreach($rows as $row){
            		if($row['banners']){
            			$banners = json_decode($row['banners']);
            			if($banners){
            				foreach($banners as $banner){
            					if($banner->image && file_exists($this->pathImage.'b/'.$banner->image)) unlink($module->pathImage.'b/'.$banner->image);
            				}
            			}
            		}
            	}
            }
			DB::getInstance()->execute("Delete From "._DB_PREFIX_."simplecategory_group_lang Where group_id = $itemId");
			Db::getInstance()->execute("Delete From "._DB_PREFIX_."simplecategory_group_product Where group_id = $itemId");
			$response->status = 1;
			$response->msg = 'Delete module success!';				
        }else{
            $response->status = 0;
			$response->msg = 'Delete module not success!';    
        }
		die(Tools::jsonEncode($response));	
	}
























    public function hookdisplayHeader()
	{
		
		//$this->print_s(Tools::getValue('id_category'));
		
		$this->page_name = Dispatcher::getInstance()->getController();       
		
        if ($this->page_name == 'product'){        	
            $shopId = $this->context->shop->id;
			$productId = (int)Tools::getValue('id_product');
            $check = Db::getInstance()->getValue("Select product_id From "._DB_PREFIX_."simplecategory_product_view Where id_shop = '$shopId' AND product_id = ".$productId);
            if($check){
                Db::getInstance()->execute("Update "._DB_PREFIX_."simplecategory_product_view Set total = total + 1 Where id_shop = '$shopId' AND product_id =" .$productId);
            }else{
                Db::getInstance()->execute("Insert Into "._DB_PREFIX_."simplecategory_product_view (id_shop, product_id, total) Value ('$shopId', '$productId', 1)");
            }
		}
		//$imageSize =  Image::getSize(ImageType::getFormatedName('home'));
        // Call in option2.css		
		$this->context->controller->addCSS(($this->_path).'css/front-end/style.css');
        $this->context->controller->addJS(array(
			($this->_path).'js/front-end/common.js',
			($this->_path).'js/front-end/jquery.countdown.plugin.min.js',
			($this->_path).'js/front-end/jquery.countdown.min.js',
			($this->_path).'js/front-end/jquery.actual.min.js',
			($this->_path).'js/front-end/fsvs.js'
		));
        if(!$compareProductIds = CompareProduct::getCompareProducts($this->context->cookie->id_compare)) $compareProductIds = array();
        $this->context->smarty->assign(array(                        
            'imageSize'=>Image::getSize(ImageType::getFormatedName('home')),
            'compareProductIds'=>$compareProductIds                
        ));
		/*
		$this->context->smarty->assign(array(
            'comparator_max_item' => (int)(Configuration::get('PS_COMPARATOR_MAX_ITEM')),            
            'baseModuleUrl'=> __PS_BASE_URI__.'modules/'.$this->name,
            'imageSize'=>$this->imageHomeSize,
        	'h_per_w'=> round($this->imageHomeSize['height']/$this->imageHomeSize['width'], 2),
            'compareProductIds'=>$this->compareProductIds                 
        ));
		 * 
		 */ 
		//include_once (_PS_CONTROLLER_DIR_.'front/CompareController.php');
		//if(!$this->compareProductIds = CompareProduct::getCompareProducts($this->context->cookie->id_compare)) $this->compareProductIds = array();
	}
	//'displayHomeTopColumn', 'displayHome', 'displaySimpleCategory'
	public function hookdisplayHomeTopColumn($params)	{		
        return $this->hooks('hookdisplayHomeTopColumn', $params);	
    }
    public function hookdisplayHomeTopContent($params)	{
    			
        return $this->hooks('hookdisplayHomeTopContent', $params);	
    }    
	public function hookdisplayHome($params){		
        return $this->hooks('hookdisplayHome', $params);	
    }		
    public function hookdisplaySimpleCategory($params)	{		
        return $this->hooks('hookdisplaySimpleCategory', $params);	
    }
	public function hookdisplayLeftColumn($params)
	{	
		return $this->hooks('displayLeftColumn', $params);		
	}
    public function hookdisplayRightColumn($params)
	{
		return $this->hooks('displayRightColumn', $params);		
	}
	public function hookDisplayBottomProduct($params)
	{
		return $this->hooks('hookDisplayBottomProduct', $params);		
	}
	public function hookDisplaySmartBlogLeft($params)
	{
		return $this->hooks('hookDisplaySmartBlogLeft', $params);		
	}
	public function hookDisplaySmartBlogRight($params)
	{
		return $this->hooks('hookDisplaySmartBlogRight', $params);		
	}
	
	
	
	
	public function hookdisplayHomeBottomContent($params)
	{
		return $this->hooks('displayHomeBottomContent', $params);		
	}
	public function hookdisplayHomeBottomColumn($params)
	{
		return $this->hooks('displayHomeBottomColumn', $params);		
	}
	public function hookdisplayBottomColumn($params)
	{
		return $this->hooks('displayBottomColumn', $params);		
	}
	
	public function hookdisplaySimpleCategory1($params)
	{
		return $this->hooks('displaySimpleCategory1', $params);		
	}
	public function hookdisplaySimpleCategory2($params)
	{
		return $this->hooks('displaySimpleCategory2', $params);		
	}
	public function hookdisplaySimpleCategory3($params)
	{
		return $this->hooks('displaySimpleCategory3', $params);		
	}
	public function hookdisplaySimpleCategory4($params)
	{
		return $this->hooks('displaySimpleCategory4', $params);		
	}
	public function hookdisplaySimpleCategory5($params)
	{
		return $this->hooks('displaySimpleCategory5', $params);		
	}

	
	
    public function s_print($content, $id_employee=0){
        echo "<pre>";
        	print_r($content);
        echo "</pre>";
        die();
		
	}
    public function hooks($hookName, $param){
    	//$time_start = microtime(true);
		
		
		    	
    	//$this->s_print(self::getProductRatings(1));
        $langId = $this->context->language->id;
	    $shopId = $this->context->shop->id;
        $hookName = strtolower(str_replace('hook','', $hookName));
		
        $hookId = (int)Hook::getIdByName($hookName);		
		if($hookId <=0) return '';		
        $items = DB::getInstance()->executeS("Select DISTINCT m.*, ml.`name`, ml.`banners`, ml.description  
	        From ("._DB_PREFIX_."simplecategory_module AS m 
	        INNER JOIN "._DB_PREFIX_."simplecategory_module_lang AS ml On m.id = ml.module_id)  
	        Where LOWER(m.position_name) = '".$hookName."' AND m.status = 1 AND  m.id_shop = '".$shopId."' AND ml.id_lang = '".$langId."' 
	        Order By m.ordering");
		$results = array();
		
        if($items){        	
            foreach($items as $item){
		    	$results[] = $this->frontBuildModuleContent($item, $hookName, $shopId, $langId);		
            }			
			$this->context->smarty->assign(array(
				'simplecategory_modules'=> $results,
			));
			//$time_end = microtime(true);
			//echo $hookName.': '.($time_end - $time_start);
			
			return $this->display(__FILE__, 'simplecategory.tpl');	            
        }else return "";			
		        
    }
    public function frontBuildModuleContent($item, $hookName='', $shopId=0, $langId=0){
    	if(!$item) return '';
    	$cache_id = Tools::encrypt('simplecategory::'.$hookName.'::'.$item['id']);
    	if($item['layout'] != 'option1_tab'){    		
    		$homeCategory = Configuration::get('PS_HOME_CATEGORY');        
			$results = array();            
	    	// module banner    	
	    	if($item['banners']){
		    	$banners = json_decode($item['banners']);
				$item['banners'] = array();
				if($banners){
					foreach($banners as $banner){
						if($banner){
							$fullPath = $this->getBannerSrc($banner->image, true);
							if($fullPath){										
								$banner->image = $fullPath;								
								$item['banners'] = get_object_vars($banner);
							}	
						}							
					}
				}	
	    	}else $item['banners'] = array();		    	
			// end module banner
			if($item['params']){
				$item['params'] = get_object_vars(json_decode($item['params']));
				$item['params']['icon'] = $this->getIconSrc($item['params']['icon']);
				$item['params']['icon_active'] = $this->getIconSrc($item['params']['icon_active']);
			}
			$id_category = 0;				
			if($item['category_id'] == 0){
				if($this->page_name == 'category'){
					$id_category = intval(Tools::getValue('id_category', 0));	
				}elseif($this->page_name == 'product'){
					$productId = (int)Tools::getValue('id_product');
	            	$id_category = Db::getInstance()->getValue("Select id_category_default From "._DB_PREFIX_."product Where id_shop = '$shopId' AND id_product = ".$productId);
				}else{
					$id_category = $homeCategory;
				}
			}else{
				$id_category = $item['category_id'];
			}
			$arrCategoryId = $this->getCategoryIds($id_category, array($id_category));		
			$item['products'] = array();
			if($item['type'] == 'auto'){
				if($item['layout'] == 'deal'){
					$item['products'] = $this->frontGetProducts2($arrCategoryId, $item['on_condition'], $item['on_sale'], $item['on_new'], $item['on_discount'], $langId, 1, $item['maxItem'], $item['order_by'], $item['order_way'], null, null, true);
				}else{
					$item['products'] = $this->frontGetProducts2($arrCategoryId, $item['on_condition'], $item['on_sale'], $item['on_new'], $item['on_discount'], $langId, 1, $item['maxItem'], $item['order_by'], $item['order_way'], null, null);
				}
			}else{
				$rows = Db::getInstance()->executeS("Select product_id, ordering From "._DB_PREFIX_."simplecategory_module_product Where module_id = ".$item['id']." Order By ordering");
	            if($rows){                
	                foreach($rows as $row){
	                    $item['products'][] = $this->frontGetProductById($row['product_id'], $langId);		                    
	                }    
	            }
			}
	        // short codes
	        //if($item['description']) $item['description'] = Tools::htmlentitiesDecodeUTF8($item['description']);
	        $moduleDescription = $item['description'];
			if($moduleDescription){
				// short code deal
		        $pattern = '/\{deal\}(.*?)\{\/deal\}/';
		        $checkDeal = preg_match_all($pattern, $moduleDescription, $match);
		        if($checkDeal){
		            $deals = $match[1];
		            if($deals){
		                foreach($deals as $deal){                    
		                    $dealConfig = json_decode(str_replace(array('\\', '\''), array('', '"'), $deal));                    
		                    if($dealConfig){                        
		                        $html = $this->buildDescriptionDeal($arrCategoryId, $dealConfig, $item['layout']);
		                        $item['description'] = str_replace('{deal}'.$deal.'{/deal}', $html, $item['description']);
		                    }else{
		                        $item['description'] = str_replace('{deal}'.$deal.'{/deal}', '', $item['description']);
		                    }
		                    
		                }
		            }
		        }	
			} // end if check module description
    	} // end if check layout
        $item['groups'] = $this->frontBuildContentGroups($item['id'], $item['layout'], $shopId, $langId, Tools::encrypt('simplecategory::'.$hookName.'::'.$this->page_name.'::'.$item['id']));
		$this->context->smarty->assign(array(
			'simplecategory_item'=> $item,
		));				
		return $this->display(__FILE__, 'simplecategory.'.$item['layout'].'.module.tpl');        
    }
	public function frontBuildContentGroups($moduleId, $layout, $shopId, $langId, $cache_id=''){
		
		$items = array();        
		$items = DB::getInstance()->executeS("Select g.*, gl.name, gl.banners, gl.description  
			From "._DB_PREFIX_."simplecategory_group AS g 
			Inner Join "._DB_PREFIX_."simplecategory_group_lang AS gl On g.id = gl.group_id 
			Where g.status = '1' AND module_id = '".$moduleId."' AND gl.id_lang = '".$langId."' 
			Order By g.ordering");		
		if($items){			
			$homeCategory = Configuration::get('PS_HOME_CATEGORY');
			foreach($items as &$item){
				//$item['description'] = Tools::htmlentitiesDecodeUTF8($item['description']);
				$item['icon'] = $this->getIconSrc($item['icon']);
				$item['icon_active'] = $this->getIconSrc($item['icon_active']);				
				// banners	
				if($item['banners']){
					$banners = json_decode($item['banners']);
					if($banners){
						foreach($banners as &$banner){
							$fullPath = $this->getBannerSrc($banner->image, true);
							if(!$fullPath) unset($banner);
							else {
								$banner->fullPath = $fullPath;
								$banner = get_object_vars($moduleBanner);	
							}
						}
					}
					$item['banners'] = $banners;
				}else $item['banners'] = array();
				// end banners
				if($item['category_id'] == 0){
					if($this->page_name == 'category'){
						$id_category = intval(Tools::getValue('id_category', 0));	
					}elseif($this->page_name == 'product'){
						$productId = (int)Tools::getValue('id_product');
		            	$id_category = Db::getInstance()->getValue("Select id_category_default From "._DB_PREFIX_."product Where id_shop = '$shopId' AND id_product = ".$productId);
					}else{
						$id_category = $homeCategory;
					}
				}else{
					$id_category = $item['category_id'];
				}
				$arrCategoryId = $this->getCategoryIds($id_category, array($id_category));
				$item['products'] = array();
				if($item['type'] == 'auto'){					
					$item['products'] = $this->frontGetProducts2($arrCategoryId, $item['on_condition'], $item['on_sale'], $item['on_new'], $item['on_discount'], $langId, 1, $item['maxItem'], $item['order_by'], $item['order_way'], null, null);					
				}else{
					$rows = Db::getInstance()->executeS("Select product_id, ordering From "._DB_PREFIX_."simplecategory_group_product Where group_id = ".$item['id']." Order By ordering");
		            if($rows){                
		                foreach($rows as $row){
		                    $item['products'][] = $this->frontGetProductById($row['product_id'], $langId);		                    
		                }    
		            }
				}	

				// short codes
		        $moduleDescription = $item['description'];
				if($moduleDescription){
					// short code deal
			        $pattern = '/\{deal\}(.*?)\{\/deal\}/';
			        $checkDeal = preg_match_all($pattern, $moduleDescription, $match);
			        if($checkDeal){
			            $deals = $match[1];
			            if($deals){
			                foreach($deals as $deal){                    
			                    $dealConfig = json_decode(str_replace(array('\\', '\''), array('', '"'), $deal));                    
			                    if($dealConfig){                        
			                        $html = $this->buildDescriptionDeal($arrCategoryId, $dealConfig, $layout);
			                        $item['description'] = str_replace('{deal}'.$deal.'{/deal}', $html, $item['description']);
			                    }else{
			                        $item['description'] = str_replace('{deal}'.$deal.'{/deal}', '', $item['description']);
			                    }
			                    
			                }
			            }
			        }
					// end short code deal	
				}		
			}			
			
			$this->context->smarty->assign(array(
				'simplecategory_groups'	=> $items,				
			));		
		}else return "";
		
		return $this->display(__FILE__, 'simplecategory.'.$layout.'.groups.tpl');
	}
    function buildDescriptionDeal($arrCategoryId, $config, $layout='default'){
        $langId = $this->context->language->id;
	    $shopId = $this->context->shop->id;        
        
        if(isset($config->on_condition)) $on_condition = $config->on_condition;
        else $on_condition = 'all';
        
        if(isset($config->on_sale)) $on_sale = intval($config->on_sale);
        else $on_sale = 2;
        
        if(isset($config->on_new)) $on_new = intval($config->on_new);
        else $on_new = 2;
        
        if(isset($config->count)) $count = intval($config->count);
        else $count = 1;
        
        $products = $this->frontGetProducts2($arrCategoryId, $on_condition, $on_sale, $on_new, 1, $langId, 1, $count, 'discount', 'DESC', null, null, true);
        $this->context->smarty->assign(
            array(
    			'description_products'=>$products,
    			'livePath'=>$this->liveImage,			
            )
        );		
		return $this->display(__FILE__, 'simplecategory.'.$layout.'.descriptiondeal.tpl');               
    }
	function getCacheId($name=null){
		return parent::getCacheId('simplecategory|'.$name);
	}
	function clearCache($cacheKey)
	{
		if(!$cacheKey){
			parent::_clearCache('simplecategory.tpl');
			parent::_clearCache('simplecategory.default.tpl');
		}else{
			parent::_clearCache('simplecategory.tpl', $this->getCacheId($cacheKey));
			parent::_clearCache('simplecategory.default.tpl', $this->getCacheId($cacheKey));	
		} 		
       return true;
	}
	public static function getProductRatings($id_product)
	{
		$validate = Configuration::get('PRODUCT_COMMENTS_MODERATE');
		$sql = 'SELECT (SUM(pc.`grade`) / COUNT(pc.`grade`)) AS avg,
				MIN(pc.`grade`) AS min,
				MAX(pc.`grade`) AS max,
				COUNT(pc.`grade`) AS review
			FROM `'._DB_PREFIX_.'product_comment` pc
			WHERE pc.`id_product` = '.(int)$id_product.'
			AND pc.`deleted` = 0'.
			($validate == '1' ? ' AND pc.`validate` = 1' : '');


		$item = DB::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		if($item){
			$item['avg'] = (int) $item['avg'] * 20;
			$item['min'] = (int) $item['min'] * 20;
			$item['max'] = (int) $item['max'] * 20;
			return $item;
		}
		else return array('avg'=>0, 'min'=>0, 'max'=>0);
	}
    
	
	
	public static function toItemDateTime($date_time){
		$result = array();
		$strTime = strtotime($date_time);
		$result['year'] = date('Y', $strTime);
		$result['month'] = date('m', $strTime);
		$result['day'] = date('d', $strTime);
		$result['hour'] = date('H', $strTime);
		$result['minute'] = date('m', $strTime);
		$result['second'] = date('s', $strTime);
        $result['untilTime'] = $strTime - time();
		return $result;
	}
	
	public function frontCheckAccess($id_customer)
	{
		$cache_id = 'Category::checkAccess_'.(int)$this->id.'-'.$id_customer.(!$id_customer ? '-'.(int)Group::getCurrent()->id : '');
		if (!Cache::isStored($cache_id))
		{
			if (!$id_customer)
				$result = (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT ctg.`id_group`
				FROM '._DB_PREFIX_.'category_group ctg
				WHERE ctg.`id_category` = '.(int)$this->id.' AND ctg.`id_group` = '.(int)Group::getCurrent()->id);
			else
				$result = (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT ctg.`id_group`
				FROM '._DB_PREFIX_.'category_group ctg
				INNER JOIN '._DB_PREFIX_.'customer_group cg on (cg.`id_group` = ctg.`id_group` AND cg.`id_customer` = '.(int)$id_customer.')
				WHERE ctg.`id_category` = '.(int)$this->id);
			Cache::store($cache_id, $result);
		}
		return Cache::retrieve($cache_id);
	}
	protected function getProductIdByDate($id_shop, $id_currency, $id_country, $id_group, $beginning, $ending, $id_customer = 0, $with_combination_id = false, $deal=false)
	{
		if (!SpecificPrice::isFeatureActive())
			return array();
		if($deal === true){
			$where = '(`from` = \'0000-00-00 00:00:00\' OR \''.pSQL($beginning).'\' >= `from`) AND (`to` != \'0000-00-00 00:00:00\' AND \''.pSQL($ending).'\' <= `to`)';
		}else{
			$where = '(`from` = \'0000-00-00 00:00:00\' OR \''.pSQL($beginning).'\' >= `from`) AND (`to` = \'0000-00-00 00:00:00\' OR \''.pSQL($ending).'\' <= `to`)';
		}
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT `id_product`, `id_product_attribute`
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE	`id_shop` IN(0, '.(int)$id_shop.') AND
					`id_currency` IN(0, '.(int)$id_currency.') AND
					`id_country` IN(0, '.(int)$id_country.') AND
					`id_group` IN(0, '.(int)$id_group.') AND
					`id_customer` IN(0, '.(int)$id_customer.') AND
					`from_quantity` = 1 AND
					('.$where.') 
					AND
					`reduction` > 0
		', false);
		$ids_product = array();
		while ($row = Db::getInstance()->nextRow($result))
			$ids_product[] = $with_combination_id ? array('id_product' => (int)$row['id_product'], 'id_product_attribute' => (int)$row['id_product_attribute']) : (int)$row['id_product'];
		return $ids_product;
	}
	protected function _getProductIdByDate($beginning, $ending, Context $context = null, $with_combination_id = false, $id_customer=0, $deal=false)
	{
		if (!$context)
			$context = Context::getContext();

		$id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
		$ids = Address::getCountryAndState($id_address);
		$id_country = $ids['id_country'] ? (int)$ids['id_country'] : (int)Configuration::get('PS_COUNTRY_DEFAULT');
		if (!SpecificPrice::isFeatureActive())
			return array();
		if($deal == true){
			$where = '(`from` = \'0000-00-00 00:00:00\' OR \''.pSQL($beginning).'\' >= `from`) AND (`to` != \'0000-00-00 00:00:00\' AND \''.pSQL($ending).'\' <= `to`)';
		}else{
			$where = '(`from` = \'0000-00-00 00:00:00\' OR \''.pSQL($beginning).'\' >= `from`) AND (`to` = \'0000-00-00 00:00:00\' OR \''.pSQL($ending).'\' <= `to`)';
		}
		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT `id_product`, `id_product_attribute`
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE	`id_shop` IN(0, '.(int)$context->shop->id.') AND
					`id_currency` IN(0, '.(int)$context->currency->id.') AND
					`id_country` IN(0, '.(int)$id_country.') AND
					`id_group` IN(0, '.(int)$context->customer->id_default_group.') AND
					`id_customer` IN(0, '.(int)$id_customer.') AND
					`from_quantity` = 1 AND
					('.$where.') 
					AND
					`reduction` > 0
		', false);
		$ids_product = array();
		while ($row = Db::getInstance()->nextRow($result))
			$ids_product[] = $with_combination_id ? array('id_product' => (int)$row['id_product'], 'id_product_attribute' => (int)$row['id_product_attribute']) : (int)$row['id_product'];
		return $ids_product;
		/*
		return self::getProductIdByDate(
			$context->shop->id,
			$context->currency->id,
			$id_country,
			$context->customer->id_default_group,
			$beginning,
			$ending,
			0,
			$with_combination,
			$deal
		);
		 * 
		 */
	}
	protected function frontGetProducts2($categoryIds = array(), $on_condition='all', $on_sale=2, $on_new=2, $on_discount=2, $id_lang, $p, $n, $order_by = null, $order_way = null, $beginning=null, $ending=null, $deal=false, $get_total = false, $active = true, $random = false, $random_number_products = 1, Context $context = null){		
		if(!$categoryIds) return array();		
		$where = "";
		if($on_condition != 'all'){
             $where .= " AND p.condition = '".$on_condition."' ";                
        }
		if($on_sale != 2){
			$where .= " AND p.on_sale = '".$on_sale."' ";
		}
        Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? $PS_NB_DAYS_NEW_PRODUCT = (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : $PS_NB_DAYS_NEW_PRODUCT = 20;
		if($on_new == 0){
			$where .= " AND product_shop.`date_add` <= '".date('Y-m-d', strtotime('-'.$PS_NB_DAYS_NEW_PRODUCT.' DAY'))."' ";
		}elseif($on_new == 1){
			$where .= " AND product_shop.`date_add` > '".date('Y-m-d', strtotime('-'.$PS_NB_DAYS_NEW_PRODUCT.' DAY'))."' ";
		}
		$ids_product = '';
		if($on_discount == 0){
			$current_date = date('Y-m-d H:i:s');
			$product_reductions = $this->_getProductIdByDate((!$beginning ? $current_date : $beginning), (!$ending ? $current_date : $ending), $context, true, 0, $deal);		
			if ($product_reductions){
				$ids_product = ' AND (';
				foreach ($product_reductions as $product_reduction)
					$ids_product .= '( product_shop.`id_product` != '.(int)$product_reduction['id_product'].($product_reduction['id_product_attribute'] ? ' OR product_attribute_shop.`id_product_attribute`='.(int)$product_reduction['id_product_attribute'] :'').') AND';
				$ids_product = rtrim($ids_product, 'AND').')';
			}
		}elseif($on_discount == 1){
			$current_date = date('Y-m-d H:i:s');
			$product_reductions = $this->_getProductIdByDate((!$beginning ? $current_date : $beginning), (!$ending ? $current_date : $ending), $context, true, 0, $deal);            		
			if ($product_reductions)
			{
				$ids_product = ' AND (';
				foreach ($product_reductions as $product_reduction)
					$ids_product .= '( product_shop.`id_product` = '.(int)$product_reduction['id_product'].($product_reduction['id_product_attribute'] ? ' AND product_attribute_shop.`id_product_attribute`='.(int)$product_reduction['id_product_attribute'] :'').') OR';
				$ids_product = rtrim($ids_product, 'OR').')';
			}else{
		      if($deal == true) return array();
			}
		}else{
			if($order_by == 'discount'){
				$current_date = date('Y-m-d H:i:s');
				$product_reductions = $this->_getProductIdByDate((!$beginning ? $current_date : $beginning), (!$ending ? $current_date : $ending), $context, true, 0, $deal);		
				if ($product_reductions){
					$ids_product = ' AND (';
					foreach ($product_reductions as $product_reduction)
						$ids_product .= '( product_shop.`id_product` = '.(int)$product_reduction['id_product'].($product_reduction['id_product_attribute'] ? ' AND product_attribute_shop.`id_product_attribute`='.(int)$product_reduction['id_product_attribute'] :'').') OR';
					$ids_product = rtrim($ids_product, 'OR').')';
				}				
			}
		}		
		if($ids_product) $where .= $ids_product;
		if (!$context) $context = Context::getContext();
		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) $front = false;		
		if ($p < 1) $p = 1;
		
		if (empty($order_by)){
			$order_by = 'position';
		}else{
			$order_by = strtolower($order_by);
		}			
		if (empty($order_way)) $order_way = 'ASC';		
		$order_by_prefix = false;
		
		$addJoin = '';
		$addSelect = '';	
		if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd'){
			$order_by_prefix = 'p';
		}elseif ($order_by == 'name'){
			$order_by_prefix = 'pl';
		}elseif ($order_by == 'manufacturer' || $order_by == 'manufacturer_name'){
			$order_by_prefix = 'm';
			$order_by = 'name';
		}elseif ($order_by == 'position'){
			$order_by_prefix = 'cp';
		}elseif($order_by == 'discount'){
			$order_by_prefix = 'sp';
			$order_by = 'reduction';
			$addJoin = ' LEFT JOIN `'._DB_PREFIX_.'specific_price` sp On p.`id_product` = sp.`id_product` ';
			$addSelect = ', sp.reduction, sp.`from`, sp.`to`';
		}elseif($order_by == 'review'){
			$order_by_prefix = '';
			$order_by = 'total_review';
			$addJoin = ' LEFT JOIN `'._DB_PREFIX_.'product_comment` pr ON pr.`id_product` = p.`id_product` ';
			$addSelect = ', COUNT(pr.grade) as total_review';
		}elseif($order_by == 'view'){
			$order_by_prefix = '';
			$order_by = 'total_view';
			$addJoin = ' LEFT JOIN '._DB_PREFIX_.'simplecategory_product_view as pv ON pv.`product_id` = p.`id_product` ';
			$addSelect = ', pv.total as total_view';
		}elseif($order_by == 'rate'){
			$order_by_prefix = '';
			$order_by = 'total_avg';
			$addJoin = ' LEFT JOIN `'._DB_PREFIX_.'product_comment` pr ON pr.`id_product` = p.`id_product` ';
			$addSelect = ', (SUM(pr.`grade`) / COUNT(pr.`grade`)) AS total_avg';
		}elseif($order_by == 'seller'){
			$order_by_prefix = '';
			$order_by = 'sales';
			$addJoin = ' LEFT JOIN `'._DB_PREFIX_.'product_sale` ps ON ps.`id_product` = p.`id_product` ';
			$addSelect = ', ps.`quantity` AS sales';
		} 
		if($order_by != 'reduction' && $on_discount != 2){
			$addJoin = ' LEFT JOIN `'._DB_PREFIX_.'specific_price` sp On p.`id_product` = sp.`id_product` ';
			$addSelect = ', sp.reduction, sp.`from`, sp.`to`';
		}
		if ($order_by == 'price') $order_by = 'orderprice';
		
		
		
		
		if (!Validate::isBool($active) || !Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) die (Tools::displayError());
		$id_supplier = (int)Tools::getValue('id_supplier');
		
		if ($get_total)
		{
			$sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `'._DB_PREFIX_.'product` p 					
					'.Shop::addSqlAssociation('product', 'p').' '.$addJoin.'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` IN ('.implode(', ', $categoryIds).') '.$where. 
					($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
					($active ? ' AND product_shop.`active` = 1' : '').
					(($ids_product) ? $ids_product : '').
					($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '');
			return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
		}
        
		$sql = 'SELECT DISTINCT 
				p.id_product,  MAX(product_attribute_shop.id_product_attribute) id_product_attribute, pl.`link_rewrite`, pl.`name`, pl.`description_short`, product_shop.`id_category_default`,
				MAX(image_shop.`id_image`) id_image, il.`legend`, p.`ean13`, p.`upc`, cl.`link_rewrite` AS category, p.show_price, p.available_for_order, IFNULL(stock.quantity, 0) as quantity, p.customizable,
				IFNULL(pa.minimal_quantity, p.minimal_quantity) as minimal_quantity, stock.out_of_stock,
				product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.$PS_NB_DAYS_NEW_PRODUCT.' DAY')).'" as `new`,
				product_shop.`on_sale`, MAX(product_attribute_shop.minimal_quantity) AS product_attribute_minimal_quantity, product_shop.price AS orderprice '.$addSelect.'
				FROM `'._DB_PREFIX_.'category_product` cp 
				LEFT JOIN `'._DB_PREFIX_.'product` p 
					ON p.`id_product` = cp.`id_product` 
				'.Shop::addSqlAssociation('product', 'p').
				$addJoin.
				(Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa 
				ON (p.`id_product` = pa.`id_product`) 
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').' 
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop) :  Product::sqlStock('p', 'product', false, Context::getContext()->shop)).' 
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl 
					ON (product_shop.`id_category_default` = cl.`id_category` 
					AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl 
					ON (p.`id_product` = pl.`id_product` 
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i 
					ON (i.`id_product` = p.`id_product`) '.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').' 
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il 
					ON (image_shop.`id_image` = il.`id_image` 
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m 
					ON m.`id_manufacturer` = p.`id_manufacturer` 
				WHERE product_shop.`id_shop` = '.(int)$context->shop->id.' 
					AND cp.`id_category` IN ('.implode(', ', $categoryIds).') '
					.$where 
					.($active ? ' AND product_shop.`active` = 1' : '')
					.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
					.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '')
					.' GROUP BY product_shop.id_product';
		if ($random === true) $sql .= ' ORDER BY RAND() LIMIT '.(int)$random_number_products;		
		else $sql .= ' ORDER BY '.(!empty($order_by_prefix) ? $order_by_prefix.'.' : '').'`'.bqSQL($order_by).'` '.pSQL($order_way).' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;        
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if ($order_by == 'orderprice') Tools::orderbyPrice($result, $order_way);
		if (!$result) return array();
		return Product::getProductsProperties($id_lang, $result);
	}
	
	public function frontGetProducts($categoryIds = array(), $display_only='', $id_lang, $p, $n, $order_by = null, $order_way = null, $get_total = false, $active = true, $random = false, $random_number_products = 1, Context $context = null){
		if(!$categoryIds) return array();
		$where = "";
		if($display_only){
            if($display_only == 'condition-new') $where .= " AND p.condition = 'new'";
            elseif($display_only == 'condition-used') $where .= " AND p.condition = 'used'";
            elseif($display_only == 'condition-refurbished') $where .= " AND p.condition = 'refurbished'";    
        }
		if (!$context) $context = Context::getContext();
		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) $front = false;		
		if ($p < 1) $p = 1;
		if (empty($order_by)) $order_by = 'position';

		else $order_by = strtolower($order_by);
		if (empty($order_way)) $order_way = 'ASC';
		$order_by_prefix = false;
		if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd') $order_by_prefix = 'p';
		elseif ($order_by == 'name') $order_by_prefix = 'pl';
		elseif ($order_by == 'manufacturer' || $order_by == 'manufacturer_name'){
			$order_by_prefix = 'm';
			$order_by = 'name';
		}elseif ($order_by == 'position') $order_by_prefix = 'cp';
		if ($order_by == 'price') $order_by = 'orderprice';
		if (!Validate::isBool($active) || !Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) die (Tools::displayError());
		$id_supplier = (int)Tools::getValue('id_supplier');
		if ($get_total)
		{
			$sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` IN ('.implode(', ', $categoryIds).') '.$where. 
					($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
					($active ? ' AND product_shop.`active` = 1' : '').
					($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '');
			return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
		}
		$sql = 'SELECT
				p.id_product,  MAX(product_attribute_shop.id_product_attribute) id_product_attribute, pl.`link_rewrite`, pl.`name`, pl.`description_short`, product_shop.`id_category_default`,
				MAX(image_shop.`id_image`) id_image, il.`legend`, p.`ean13`, p.`upc`, cl.`link_rewrite` AS category, p.show_price, p.available_for_order, IFNULL(stock.quantity, 0) as quantity, p.customizable,
				IFNULL(pa.minimal_quantity, p.minimal_quantity) as minimal_quantity, stock.out_of_stock,
				product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'" as new,
				product_shop.`on_sale`, MAX(product_attribute_shop.minimal_quantity) AS product_attribute_minimal_quantity, product_shop.price AS orderprice 
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').
				(Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop) :  Product::sqlStock('p', 'product', false, Context::getContext()->shop)).'
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
				WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
					AND cp.`id_category` IN ('.implode(', ', $categoryIds).')'
					.($active ? ' AND product_shop.`active` = 1' : '')
					.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
					.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '')
					.' GROUP BY product_shop.id_product';
		if ($random === true) $sql .= ' ORDER BY RAND() LIMIT '.(int)$random_number_products;
		else $sql .= ' ORDER BY '.(!empty($order_by_prefix) ? $order_by_prefix.'.' : '').'`'.bqSQL($order_by).'` '.pSQL($order_way).' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;
			
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if ($order_by == 'orderprice') Tools::orderbyPrice($result, $order_way);
		if (!$result) return array();
		return Product::getProductsProperties($id_lang, $result);
	}
	public function frontGetProductByIds($ids = array(), $id_lang, $order_by = null, $order_way = null, $active = true, Context $context = null){
		if(!$ids) return array();
		if (!$context) $context = Context::getContext();
		//if($check_access && !$this->frontCheckAccess($context->customer->id)) return array();
		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) $front = false;		
		
		if (empty($order_by)) $order_by = 'position';
		else $order_by = strtolower($order_by);
		if (empty($order_way)) $order_way = 'ASC';		
		$order_by_prefix = false;
		if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd') $order_by_prefix = 'p';
		elseif ($order_by == 'name') $order_by_prefix = 'pl';
		elseif ($order_by == 'manufacturer' || $order_by == 'manufacturer_name'){
			$order_by_prefix = 'm';
			$order_by = 'name';
		}elseif ($order_by == 'position') $order_by_prefix = 'cp';
		if ($order_by == 'price') $order_by = 'orderprice';
		if (!Validate::isBool($active) || !Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) die (Tools::displayError());
		$id_supplier = (int)Tools::getValue('id_supplier');		
		$sql = 'SELECT
				p.id_product,  MAX(product_attribute_shop.id_product_attribute) id_product_attribute, pl.`link_rewrite`, pl.`name`, pl.`description_short`, product_shop.`id_category_default`,
				MAX(image_shop.`id_image`) id_image, il.`legend`, p.`ean13`, p.`upc`, cl.`link_rewrite` AS category, p.show_price, p.available_for_order, IFNULL(stock.quantity, 0) as quantity, p.customizable,
				IFNULL(pa.minimal_quantity, p.minimal_quantity) as minimal_quantity, stock.out_of_stock,
				product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'" as new,
				product_shop.`on_sale`, MAX(product_attribute_shop.minimal_quantity) AS product_attribute_minimal_quantity, product_shop.price AS orderprice 
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').
				(Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop) :  Product::sqlStock('p', 'product', false, Context::getContext()->shop)).'
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
				WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
					AND product_shop.id_product IN ('.implode(', ', $ids).') '.
					' AND product_shop.`active` = 1'.
					' AND product_shop.`visibility` IN ("both", "catalog")'.
					($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '').
					' GROUP BY product_shop.id_product';
		$sql .= ' ORDER BY '.(!empty($order_by_prefix) ? $order_by_prefix.'.' : '').'`'.bqSQL($order_by).'` '.pSQL($order_way);
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if ($order_by == 'orderprice') Tools::orderbyPrice($result, $order_way);
		if (!$result) return array();
		return Product::getProductsProperties($id_lang, $result);
	}
	public function frontGetProductById($productId = 0, $id_lang, $active = true, Context $context = null){
		if(!$productId) return array();
		if (!$context) $context = Context::getContext();
		//if($check_access && !$this->frontCheckAccess($context->customer->id)) return array();
		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) $front = false;		
		
		
		if (!Validate::isBool($active)) die (Tools::displayError());
		$id_supplier = (int)Tools::getValue('id_supplier');		
		$sql = 'SELECT
				p.id_product,  MAX(product_attribute_shop.id_product_attribute) id_product_attribute, pl.`link_rewrite`, pl.`name`, pl.`description_short`, product_shop.`id_category_default`,
				MAX(image_shop.`id_image`) id_image, il.`legend`, p.`ean13`, p.`upc`, cl.`link_rewrite` AS category, p.show_price, p.available_for_order, IFNULL(stock.quantity, 0) as quantity, p.customizable,
				IFNULL(pa.minimal_quantity, p.minimal_quantity) as minimal_quantity, stock.out_of_stock,
				product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'" as new,
				product_shop.`on_sale`, MAX(product_attribute_shop.minimal_quantity) AS product_attribute_minimal_quantity, product_shop.price AS orderprice 
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').
				(Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop) :  Product::sqlStock('p', 'product', false, Context::getContext()->shop)).'
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
				WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
					AND product_shop.id_product =  '.$productId.
					' AND product_shop.`active` = 1'.
					' AND product_shop.`visibility` IN ("both", "catalog")'.
					($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '').
					' GROUP BY product_shop.id_product';
		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);		
		if (!$result) return array();		
		return Product::getProductProperties($id_lang, $result);
	}
	public function frontGetMostReview($categoryIds = array(), $display_only='', $id_lang, $p, $n, $order_way = null, $get_total = false, $active = true, $random = false, $random_number_products = 1, Context $context = null){
		if(!$categoryIds) return array();
		$where = "";
		if($display_only){
            if($display_only == 'condition-new') $where .= " AND p.condition = 'new'";
            elseif($display_only == 'condition-used') $where .= " AND p.condition = 'used'";
            elseif($display_only == 'condition-refurbished') $where .= " AND p.condition = 'refurbished'";    
        }
		if (!$context) $context = Context::getContext();
		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) $front = false;		
		if ($p < 1) $p = 1;
		$order_by = 'total_review';
		//if (empty($order_by)) $order_by = 'position';
		//else $order_by = strtolower($order_by);
		if (empty($order_way)) $order_way = 'DESC';
		//$order_by_prefix = false;
		//if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd') $order_by_prefix = 'p';
		//elseif ($order_by == 'name') $order_by_prefix = 'pl';
		//elseif ($order_by == 'manufacturer' || $order_by == 'manufacturer_name'){
		//	$order_by_prefix = 'm';
		//	$order_by = 'name';
		//}elseif ($order_by == 'position') $order_by_prefix = 'cp';
		//if ($order_by == 'price') $order_by = 'orderprice';
		if (!Validate::isBool($active) || !Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) die (Tools::displayError());
		$id_supplier = (int)Tools::getValue('id_supplier');
		if ($get_total)
		{
			$sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` IN ('.implode(', ', $categoryIds).') '.$where. 
					($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
					($active ? ' AND product_shop.`active` = 1' : '').
					($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '');
			return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
		}
		$sql = 'SELECT
				p.id_product,  MAX(product_attribute_shop.id_product_attribute) id_product_attribute, pl.`link_rewrite`, pl.`name`, pl.`description_short`, product_shop.`id_category_default`,
				MAX(image_shop.`id_image`) id_image, il.`legend`, p.`ean13`, p.`upc`, cl.`link_rewrite` AS category, p.show_price, p.available_for_order, IFNULL(stock.quantity, 0) as quantity, p.customizable,
				IFNULL(pa.minimal_quantity, p.minimal_quantity) as minimal_quantity, stock.out_of_stock, COUNT(pr.grade) as total_review, 
				product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'" as new,
				product_shop.`on_sale`, MAX(product_attribute_shop.minimal_quantity) AS product_attribute_minimal_quantity, product_shop.price AS orderprice 
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').
				(Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop) :  Product::sqlStock('p', 'product', false, Context::getContext()->shop)).'
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
				LEFT JOIN `'._DB_PREFIX_.'product_comment` pr
					ON pr.`id_product` = p.`id_product`
				WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
					AND cp.`id_category` IN ('.implode(', ', $categoryIds).')'
					.($active ? ' AND product_shop.`active` = 1' : '')
					.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
					.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '')
					.' GROUP BY product_shop.id_product';
					
		
		$sql .= ' ORDER BY `'.bqSQL($order_by).'` '.pSQL($order_way).' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		//if ($order_by == 'orderprice') Tools::orderbyPrice($result, $order_way);
		if (!$result) return array();
		return Product::getProductsProperties($id_lang, $result);
	}
	public function frontGetMostRates($categoryIds = array(), $display_only='', $id_lang, $p, $n, $order_way = null, $get_total = false, $active = true, $random = false, $random_number_products = 1, Context $context = null){
		if(!$categoryIds) return array();
		$where = "";
		if($display_only){
            if($display_only == 'condition-new') $where .= " AND p.condition = 'new'";
            elseif($display_only == 'condition-used') $where .= " AND p.condition = 'used'";
            elseif($display_only == 'condition-refurbished') $where .= " AND p.condition = 'refurbished'";    
        }
		if (!$context) $context = Context::getContext();
		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) $front = false;		
		if ($p < 1) $p = 1;
		$order_by = 'total_avg';
		//if (empty($order_by)) $order_by = 'position';
		//else $order_by = strtolower($order_by);
		if (empty($order_way)) $order_way = 'DESC';
		//$order_by_prefix = false;
		//if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd') $order_by_prefix = 'p';
		//elseif ($order_by == 'name') $order_by_prefix = 'pl';
		//elseif ($order_by == 'manufacturer' || $order_by == 'manufacturer_name'){
		//	$order_by_prefix = 'm';
		//	$order_by = 'name';
		//}elseif ($order_by == 'position') $order_by_prefix = 'cp';
		//if ($order_by == 'price') $order_by = 'orderprice';
		if (!Validate::isBool($active) || !Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) die (Tools::displayError());
		$id_supplier = (int)Tools::getValue('id_supplier');
		if ($get_total)
		{
			$sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` IN ('.implode(', ', $categoryIds).') '.$where. 
					($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
					($active ? ' AND product_shop.`active` = 1' : '').
					($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '');
			return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
		}
		$sql = 'SELECT
				p.id_product,  MAX(product_attribute_shop.id_product_attribute) id_product_attribute, pl.`link_rewrite`, pl.`name`, pl.`description_short`, product_shop.`id_category_default`,
				MAX(image_shop.`id_image`) id_image, il.`legend`, p.`ean13`, p.`upc`, cl.`link_rewrite` AS category, p.show_price, p.available_for_order, IFNULL(stock.quantity, 0) as quantity, p.customizable,
				IFNULL(pa.minimal_quantity, p.minimal_quantity) as minimal_quantity, stock.out_of_stock, (SUM(pr.`grade`) / COUNT(pr.`grade`)) AS total_avg, 
				product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'" as new,
				product_shop.`on_sale`, MAX(product_attribute_shop.minimal_quantity) AS product_attribute_minimal_quantity, product_shop.price AS orderprice 
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').
				(Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop) :  Product::sqlStock('p', 'product', false, Context::getContext()->shop)).'
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
				LEFT JOIN `'._DB_PREFIX_.'product_comment` pr
					ON pr.`id_product` = p.`id_product`
				WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
					AND cp.`id_category` IN ('.implode(', ', $categoryIds).')'
					.($active ? ' AND product_shop.`active` = 1' : '')
					.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
					.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '')
					.' GROUP BY product_shop.id_product';
					
		
		$sql .= ' ORDER BY `'.bqSQL($order_by).'` '.pSQL($order_way).' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		//if ($order_by == 'orderprice') Tools::orderbyPrice($result, $order_way);
		if (!$result) return array();
		return Product::getProductsProperties($id_lang, $result);
	}
	public function frontGetMostView($categoryIds = array(), $display_only='', $id_lang, $p, $n, $order_way = null, $get_total = false, $active = true, $random = false, $random_number_products = 1, Context $context = null){
		if(!$categoryIds) return array();
		$where = "";
		if($display_only){
            if($display_only == 'condition-new') $where .= " AND p.condition = 'new'";
            elseif($display_only == 'condition-used') $where .= " AND p.condition = 'used'";
            elseif($display_only == 'condition-refurbished') $where .= " AND p.condition = 'refurbished'";    
        }
		if (!$context) $context = Context::getContext();
		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) $front = false;		
		if ($p < 1) $p = 1;
		$order_by = 'total_view';
		
		//$order_way = 'DESC';
		
		//if (empty($order_by)) $order_by = 'position';
		//else $order_by = strtolower($order_by);
		if (empty($order_way)) $order_way = 'DESC';
		//$order_by_prefix = false;
		//if ($order_by == 'id_product' || $order_by == 'date_add' || $order_by == 'date_upd') $order_by_prefix = 'p';
		//elseif ($order_by == 'name') $order_by_prefix = 'pl';
		//elseif ($order_by == 'manufacturer' || $order_by == 'manufacturer_name'){
		//	$order_by_prefix = 'm';
		//	$order_by = 'name';
		//}elseif ($order_by == 'position') $order_by_prefix = 'cp';
		//if ($order_by == 'price') $order_by = 'orderprice';
		if (!Validate::isBool($active) || !Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) die (Tools::displayError());
		$id_supplier = (int)Tools::getValue('id_supplier');
		if ($get_total)
		{
			$sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` IN ('.implode(', ', $categoryIds).') '.$where. 
					($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
					($active ? ' AND product_shop.`active` = 1' : '').
					($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '');
			return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
		}
		$sql = 'SELECT
				p.id_product,  MAX(product_attribute_shop.id_product_attribute) id_product_attribute, pl.`link_rewrite`, pl.`name`, pl.`description_short`, product_shop.`id_category_default`,
				MAX(image_shop.`id_image`) id_image, il.`legend`, p.`ean13`, p.`upc`, cl.`link_rewrite` AS category, p.show_price, p.available_for_order, IFNULL(stock.quantity, 0) as quantity, p.customizable,
				IFNULL(pa.minimal_quantity, p.minimal_quantity) as minimal_quantity, stock.out_of_stock, pv.total as total_view, 
				product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'" as new,
				product_shop.`on_sale`, MAX(product_attribute_shop.minimal_quantity) AS product_attribute_minimal_quantity, product_shop.price AS orderprice 
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').
				(Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop) :  Product::sqlStock('p', 'product', false, Context::getContext()->shop)).'
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
				LEFT JOIN `'._DB_PREFIX_.'product_comment` pr
					ON pr.`id_product` = p.`id_product` 
				LEFT JOIN '._DB_PREFIX_.'simplecategory_product_view as pv 
                    ON pv.`product_id` = p.`id_product` 
				WHERE product_shop.`id_shop` = '.(int)$context->shop->id.'
					AND cp.`id_category` IN ('.implode(', ', $categoryIds).')'
					.($active ? ' AND product_shop.`active` = 1' : '')
					.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
					.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '')
					.' GROUP BY product_shop.id_product';
					
		
		$sql .= ' ORDER BY `'.bqSQL($order_by).'` '.pSQL($order_way).' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		//if ($order_by == 'orderprice') Tools::orderbyPrice($result, $order_way);
		if (!$result) return array();
		return Product::getProductsProperties($id_lang, $result);
	}
	public function frontGetSpecial($categoryIds = array(), $display_only = '', $id_lang, $p, $n, $order_way = null, $beginning=null, $ending=null, $get_total = false, $active = true, Context $context = null){
		if(!$categoryIds) return array();
		if (!$context) $context = Context::getContext();
		$where = "";
		if($display_only){
            if($display_only == 'condition-new') $where .= " AND p.condition = 'new'";
            elseif($display_only == 'condition-used') $where .= " AND p.condition = 'used'";
            elseif($display_only == 'condition-refurbished') $where .= " AND p.condition = 'refurbished'";    
        }
		$current_date = date('Y-m-d H:i:s');
		$product_reductions = Product::_getProductIdByDate((!$beginning ? $current_date : $beginning), (!$ending ? $current_date : $ending), $context, true);		
		if ($product_reductions)
		{
			$ids_product = ' AND (';
			foreach ($product_reductions as $product_reduction)
				$ids_product .= '( product_shop.`id_product` = '.(int)$product_reduction['id_product'].($product_reduction['id_product_attribute'] ? ' AND product_attribute_shop.`id_product_attribute`='.(int)$product_reduction['id_product_attribute'] :'').') OR';
			$ids_product = rtrim($ids_product, 'OR').')';
		}	
				
		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) $front = false;		
		if ($p < 1) $p = 1;
		$order_by = 'sp.reduction';
		//$order_way = 'DESC';
		if (empty($order_way)) $order_way = 'DESC';
		if (!Validate::isBool($active) || !Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) die (Tools::displayError());
		$id_supplier = (int)Tools::getValue('id_supplier');
		if ($get_total)
		{
			$sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM (`'._DB_PREFIX_.'product` p INNER JOIN `'._DB_PREFIX_.'specific_price` sp On p.id_product = sp.id_product)
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` IN ('.implode(', ', $categoryIds).') '.$where. 
					' AND product_shop.`visibility` IN ("both", "catalog")'.
					' AND product_shop.`active` = 1'.
					(($ids_product) ? $ids_product : '').
					($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '');
			return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
		}
		$sql = 'SELECT
				p.id_product,  MAX(product_attribute_shop.id_product_attribute) id_product_attribute, pl.`link_rewrite`, pl.`name`, pl.`description_short`, product_shop.`id_category_default`,
				MAX(image_shop.`id_image`) id_image, il.`legend`, p.`ean13`, p.`upc`, cl.`link_rewrite` AS category, p.show_price, p.available_for_order, IFNULL(stock.quantity, 0) as quantity, p.customizable,
				IFNULL(pa.minimal_quantity, p.minimal_quantity) as minimal_quantity, stock.out_of_stock,
				product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'" as new,
				product_shop.`on_sale`, MAX(product_attribute_shop.minimal_quantity) AS product_attribute_minimal_quantity, product_shop.price AS orderprice, sp.reduction 
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN (`'._DB_PREFIX_.'product` p INNER JOIN `'._DB_PREFIX_.'specific_price` sp On p.id_product = sp.id_product)
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').
				(Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop) :  Product::sqlStock('p', 'product', false, Context::getContext()->shop)).'
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
				WHERE product_shop.`id_shop` = '.(int)$context->shop->id.(($ids_product) ? $ids_product : '').
					' AND cp.`id_category` IN ('.implode(', ', $categoryIds).')'. $where .					
					' AND product_shop.`active` = 1'.
					(($ids_product) ? $ids_product : '').
					' AND product_shop.`visibility` IN ("both", "catalog")'.
					($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '').
					' GROUP BY product_shop.id_product'.
					' ORDER BY `'.bqSQL($order_by).'` '.pSQL($order_way).' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if (!$result) return array();
		return Product::getProductsProperties($id_lang, $result);
	}
	
	public function frontGetBestSeller($categoryIds=array(), $on_condition = 'all', $on_sale=2, $on_new=2, $on_discount = 2, $id_lang, $page_number = 0, $nb_products = 10, Context $context = null)
	{
		if(!$categoryIds) return array();
		$where = "";
		if($on_condition != 'all'){
			$where .= " AND p.condition = '".$on_condition."'";			
            //if($display_only == 'condition-new') $where .= " AND p.condition = 'new'";
            //elseif($display_only == 'condition-used') $where .= " AND p.condition = 'used'";
            //elseif($display_only == 'condition-refurbished') $where .= " AND p.condition = 'refurbished'";    
        }
		if (!$context) $context = Context::getContext();
		if ($page_number < 0) $page_number = 0;
		if ($nb_products < 1) $nb_products = 10;
		//FROM `'._DB_PREFIX_.'category_product` cp
		$sql = '
		SELECT
			p.id_product,  MAX(product_attribute_shop.id_product_attribute) id_product_attribute, pl.`link_rewrite`, pl.`name`, pl.`description_short`, product_shop.`id_category_default`,
			MAX(image_shop.`id_image`) id_image, il.`legend`,
			ps.`quantity` AS sales, p.`ean13`, p.`upc`, cl.`link_rewrite` AS category, p.show_price, p.available_for_order, IFNULL(stock.quantity, 0) as quantity, p.customizable,
			IFNULL(pa.minimal_quantity, p.minimal_quantity) as minimal_quantity, stock.out_of_stock,
			product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'" as new,
			product_shop.`on_sale`, MAX(product_attribute_shop.minimal_quantity) AS product_attribute_minimal_quantity
		FROM `'._DB_PREFIX_.'product_sale` ps
		LEFT JOIN `'._DB_PREFIX_.'product` p ON ps.`id_product` = p.`id_product`
		'.Shop::addSqlAssociation('product', 'p').'
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
			ON (p.`id_product` = pa.`id_product`)
		'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
		'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
			ON p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
		Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
			ON cl.`id_category` = product_shop.`id_category_default`
			AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl');
				
		if (Group::isFeatureActive())
		{
			$groups = FrontController::getCurrentCustomerGroups();
			$sql .= '
				JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = p.`id_product`)
				JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').')';
		}else{
			$sql .= ' JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = p.`id_product`)';
		}

		$sql.= '
		WHERE product_shop.`active` = 1
		AND cp.`id_category` IN ('.implode(', ', $categoryIds).') '.$where.'  
		
		AND p.`visibility` != \'none\'
		GROUP BY product_shop.id_product
		ORDER BY sales DESC
		LIMIT '.(int)($page_number * $nb_products).', '.(int)$nb_products;

		if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql))
			return false;

		return Product::getProductsProperties($id_lang, $result);
	}
	
	
	
	
	public static function _getLayoutName($layout){
		return self::$layout[$layout];
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
				WHERE  cp.id_category IN ('.$catIds.') AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').'
			)';
		}else{
            $where .= ' AND p.`id_product` IN (
				SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_product` cp 
				WHERE cp.id_category IN ('.$catIds.'))';
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
			
                $result = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                return Product::getProductsProperties($id_lang, $result);            
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
