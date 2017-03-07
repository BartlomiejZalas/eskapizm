<?php
/*
*  @author Ovic Developer
*  @copyright  2014 Ovic Developer
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_'))
	exit;
include_once _PS_MODULE_DIR_.'blockcollection/blockcollectionClass.php';
class Blockcollection extends Module
{
    public function __construct()
    {
        $this->name = 'blockcollection';
        $this->tab = 'front_office_features';
        $this->version = 1.0;
		$this->author = 'Ovicsoft';
		$this->need_instance = 0;
        
        $this->bootstrap = true;
        parent::__construct();

		$this->displayName = $this->l('Fashion - Collection block');
        $this->description = $this->l('Displays collections of Desigers.');
    }

	public function install()
	{
		if (!parent::install() || !$this->installDB() || 
        !Configuration::updateValue('COLLECTION_NBBLOCKS', 3) || 
        !$this->registerHook('HOOK_COLLECTION') ||  
        !$this->registerHook('header') || !$this->installFixtures())
            return false;
        return true;
    }

    public function hookHOOK_COLLECTION($params)
	{
	   if (!$this->isCached('blockcollection.tpl', $this->getCacheId()))
		{
			$collections = $this->getListContent($this->context->language->id);
            $products = $this->getAllProducts((int)Context::getContext()->language->id, $collections[0]['id_collection']);
			$this->context->smarty->assign(array(
                'collections' => $collections,
                'products' => $products,
                'nbblocks' => count($collections)
            ));
		}
		return $this->display(__FILE__, 'blockcollection.tpl', $this->getCacheId());
	}
	
    public function hookHome($params) {
        return $this->hookHOOK_COLLECTION($params);
    }
    
	public function hookHeader($params)
	{
	    // CSS in global.css
		// $this->context->controller->addCSS(($this->_path).'blockcollection.css', 'all');
        $this->context->controller->addJS($this->_path.'blockcollection.js');
		$this->context->controller->addJqueryPlugin(array('scrollTo', 'serialScroll'));
        $this->context->controller->addJS($this->_path.'blockcollection-ajax.js');
	}
    
  /********************* Database ****************************/
    
	public function installDB()
	{
		$return = true;
		$return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'block_collections` (
				`id_collection` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned NOT NULL ,
				`file_name` VARCHAR(100) NOT NULL,
				PRIMARY KEY (`id_collection`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');
		
		$return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'block_collections_lang` (
				`id_collection` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_lang` int(10) unsigned NOT NULL ,
                `list_products_id` VARCHAR(100) NOT NULL,
				`name` VARCHAR(300) NOT NULL,
                `name_collection` VARCHAR(300) NOT NULL,
                `company` VARCHAR(300) NOT NULL,
                `text` VARCHAR(300) NOT NULL,
				PRIMARY KEY (`id_collection`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');
		
		return $return;
	}

	public function uninstall()
	{
		return Configuration::deleteByName('COLLECTION_NBBLOCKS') && 
            $this->uninstallDB() &&
			parent::uninstall();
	}

	public function uninstallDB()
	{
		return Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'block_collections`') && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'block_collections_lang`');
	}

	public function addToDB()
	{
		if (isset($_POST['nbblocks']))
		{
			for ($i = 1; $i <= (int)$_POST['nbblocks']; $i++)
			{
				$filename = explode('.', $_FILES['info'.$i.'_file']['name']);
				if (isset($_FILES['info'.$i.'_file']) && isset($_FILES['info'.$i.'_file']['tmp_name']) && !empty($_FILES['info'.$i.'_file']['tmp_name']))
				{
					if ($error = ImageManager::validateUpload($_FILES['info'.$i.'_file']))
						return false;
					elseif (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['info'.$i.'_file']['tmp_name'], $tmpName))
						return false;
					elseif (!ImageManager::resize($tmpName, dirname(__FILE__).'/img/'.$filename[0].'.jpg'))
						return false;
					unlink($tmpName);
				}
				Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'block_collections` (`filename`,`text`,`name`,`name_collection`,`list_products_id`,`company`)
											VALUES ("'.((isset($filename[0]) && $filename[0] != '') ? pSQL($filename[0]) : '').
					'", "'.((isset($_POST['info'.$i.'_text']) && $_POST['info'.$i.'_text'] != '') ? pSQL($_POST['info'.$i.'_text']) : '').
                    '", "'.((isset($_POST['info'.$i.'_name']) && $_POST['info'.$i.'_name'] != '') ? pSQL($_POST['info'.$i.'_name']) : '').
                    '", "'.((isset($_POST['info'.$i.'_name_collection']) && $_POST['info'.$i.'_name_collection'] != '') ? pSQL($_POST['info'.$i.'_name_collection']) : '').
                    '", "'.((isset($_POST['info'.$i.'_list_products_id']) && $_POST['info'.$i.'_list_products_id'] != '') ? pSQL($_POST['info'.$i.'_list_products_id']) : '').
                    '", "'.((isset($_POST['info'.$i.'_company']) && $_POST['info'.$i.'_company'] != '') ? pSQL($_POST['info'.$i.'_company']) : '').'")');
			}
			return true;
		} else
			return false;
	}

	public function removeFromDB()
	{
		$dir = opendir(dirname(__FILE__).'/img');
		while (false !== ($file = readdir($dir)))
		{
			$path = dirname(__FILE__).'/img/'.$file;
			if ($file != '..' && $file != '.' && !is_dir($file))
				unlink($path);
		}
		closedir($dir);

		return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'block_collections`');
	}

	public function getContent()
	{
		$html = '';
		$id_collection = (int)Tools::getValue('id_collection');

		if (Tools::isSubmit('saveblockcollection'))
		{
			if ($id_collection = Tools::getValue('id_collection'))
				$blockcollection = new blockcollectionClass((int)$id_collection);
			else
				$blockcollection = new blockcollectionClass();
			$blockcollection->copyFromPost();
			$blockcollection->id_shop = $this->context->shop->id;
			
			if ($blockcollection->validateFields(false) && $blockcollection->validateFieldsLang(false))
			{
				$blockcollection->save();
				if (isset($_FILES['image']) && isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name']))
				{
					if ($error = ImageManager::validateUpload($_FILES['image']))
						return false;
					elseif (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image']['tmp_name'], $tmpName))
						return false;
					elseif (!ImageManager::resize($tmpName, dirname(__FILE__).'/img/blockcollection-'.(int)$blockcollection->id.'-'.(int)$blockcollection->id_shop.'.jpg'))
						return false;
					unlink($tmpName);
					$blockcollection->file_name = 'blockcollection-'.(int)$blockcollection->id.'-'.(int)$blockcollection->id_shop.'.jpg';
					$blockcollection->save();
				}
				$this->_clearCache('blockcollection.tpl');
			}
			else
				$html .= '<div class="conf error">'.$this->l('An error occurred while attempting to save.').'</div>';
		}
		
		if (Tools::isSubmit('updateblockcollection') || Tools::isSubmit('addblockcollection'))
		{
			$helper = $this->initForm();
			foreach (Language::getLanguages(false) as $lang)
				if ($id_collection)
				{
					$blockcollection = new blockcollectionClass((int)$id_collection);
					$helper->fields_value['text'][(int)$lang['id_lang']] = $blockcollection->text[(int)$lang['id_lang']];
                    $helper->fields_value['name'][(int)$lang['id_lang']] = $blockcollection->name[(int)$lang['id_lang']];
                    $helper->fields_value['name_collection'][(int)$lang['id_lang']] = $blockcollection->name_collection[(int)$lang['id_lang']];
                    $helper->fields_value['list_products_id'][(int)$lang['id_lang']] = $blockcollection->list_products_id[(int)$lang['id_lang']];
                    $helper->fields_value['company'][(int)$lang['id_lang']] = $blockcollection->company[(int)$lang['id_lang']];
				}	
				else{
				    $helper->fields_value['text'][(int)$lang['id_lang']] = Tools::getValue('text_'.(int)$lang['id_lang'], '');
                    $helper->fields_value['name'][(int)$lang['id_lang']] = Tools::getValue('name_'.(int)$lang['id_lang'], '');
                    $helper->fields_value['name_collection'][(int)$lang['id_lang']] = Tools::getValue('name_collection_'.(int)$lang['id_lang'], '');
                    $helper->fields_value['list_products_id'][(int)$lang['id_lang']] = Tools::getValue('list_products_id'.(int)$lang['id_lang'], '');
                    $helper->fields_value['company'][(int)$lang['id_lang']] = Tools::getValue('company_'.(int)$lang['id_lang'], '');
				}
					
			if ($id_collection = Tools::getValue('id_collection'))
			{
				$this->fields_form[0]['form']['input'][] = array('type' => 'hidden', 'name' => 'id_collection');
				$helper->fields_value['id_collection'] = (int)$id_collection;
 			}
				
			return $html.$helper->generateForm($this->fields_form);
		}
		else if (Tools::isSubmit('deleteblockcollection'))
		{
			$blockcollection = new blockcollectionClass((int)$id_collection);
			if (file_exists(dirname(__FILE__).'/img/'.$blockcollection->file_name))
				unlink(dirname(__FILE__).'/img/'.$blockcollection->file_name);
			$blockcollection->delete();
			$this->_clearCache('blockcollection.tpl');
			Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		else
		{
			$helper = $this->initList();
			$html .= $helper->generateList($this->getListContent((int)Configuration::get('PS_LANG_DEFAULT')), $this->fields_list);
            $html .= '
            <form method="post" action="'.AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'">            	
                <div class="form-group">
                    <input type="submit" class="btn btn-default btn-lg button-new-item" name="submitLinkAdd" value="'.$this->l('Add a new collection').'" />
            	</div>
			</form>';
            return $html;
        }                        

		if (isset($_POST['submitModule']))
		{
			Configuration::updateValue('COLLECTION_NBBLOCKS', ((isset($_POST['nbblocks']) && $_POST['nbblocks'] != '') ? (int)$_POST['nbblocks'] : ''));
			if ($this->removeFromDB() && $this->addToDB())
			{
				$this->_clearCache('blockcollection.tpl');
				$output = '<div class="conf confirm">'.$this->l('The block configuration has been updated.').'</div>';
			}
			else
				$output = '<div class="conf error"><img src="../img/admin/disabled.gif"/>'.$this->l('An error occurred while attempting to save.').'</div>';
		}
	}

	protected function getListContent($id_lang)
	{
		return  Db::getInstance()->executeS('
			SELECT lc.`id_collection`, lc.`id_shop`, lcl.`list_products_id`, lc.`file_name`, lcl.`text`, lcl.`name`, lcl.`name_collection`, lcl.`company`
			FROM `'._DB_PREFIX_.'block_collections` lc
			LEFT JOIN `'._DB_PREFIX_.'block_collections_lang` lcl ON (lc.`id_collection` = lcl.`id_collection`)
			WHERE `id_lang` = '.(int)$id_lang.' '.Shop::addSqlRestrictionOnLang());
	}
    
    public function getProductsbyIdCollection($id_collection = null, $langid = 1) {
        
        if (empty($id_collection)) {
            return  Db::getInstance()->executeS('
    			SELECT lcl.`list_products_id`
    			FROM `'._DB_PREFIX_.'block_collections_lang` lcl
                WHERE id_lang = '.$langid);
        }else{
            return  Db::getInstance()->executeS('
    			SELECT lcl.`list_products_id`
    			FROM `'._DB_PREFIX_.'block_collections_lang` lcl
    			WHERE lcl.`id_collection` = '.$id_collection.' AND id_lang = '.$langid);    
        }
        
    }
	protected function initForm()
	{
	    $this->context->controller->addJqueryPlugin('tablednd');
		$this->context->controller->addJS(_PS_JS_DIR_.'admin-dnd.js');
		
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$this->fields_form[0]['form'] = array(
            'legend' => array(
				'title' => $this->l('New Colection item'),
                'icon' => 'icon-edit'
			),
			'input' => array(
				array(
					'type' => 'file',
					'label' => $this->l('Image:'),
					'name' => 'image',
					'value' => true
				),
                array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'lang' => true,
					'name' => 'name',
                    'size'     => 38
				),
                array(
					'type' => 'text',
					'label' => $this->l('Name of Collection:'),
					'lang' => true,
					'name' => 'name_collection',
                    'size'     => 38
				),
                array(
					'type' => 'text',
					'label' => $this->l('List products id:'),
					'lang' => true,
					'name' => 'list_products_id',
                    'size'     => 38
				),
                array(
					'type' => 'text',
					'label' => $this->l('Company:'),
					'lang' => true,
					'name' => 'company',
                    'size'     => 38
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Description:'),
					'lang' => true,
					'name' => 'text',
					'cols' => 40,
					'rows' => 10
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);

		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = 'blockcollection';
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		foreach (Language::getLanguages(false) as $lang)
			$helper->languages[] = array(
				'id_lang' => $lang['id_lang'],
				'iso_code' => $lang['iso_code'],
				'name' => $lang['name'],
				'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
			);

		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		$helper->toolbar_scroll = true;
		$helper->title = $this->displayName;
		$helper->submit_action = 'saveblockcollection';
		$helper->toolbar_btn =  array(
			'save' =>
			array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' =>
			array(
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);
		return $helper;
	}

	protected function initList()
	{
		$this->fields_list = array(
			'id_collection' => array(
				'title' => $this->l('Id'),
				'width' => 120,
				'type' => 'text',
			),
            'list_products_id' => array(
				'title' => $this->l('List products id'),
				'width' => 120,
				'type' => 'text',
			),
            
            'name' => array(
				'title' => $this->l('Name'),
				'width' => 120,
				'type' => 'text',
			),
            
            'name_collection' => array(
				'title' => $this->l('Name of Collection'),
				'width' => 120,
				'type' => 'text',
			),
			'text' => array(
				'title' => $this->l('Description'),
				'width' => 140,
				'type' => 'text',
				'filter_key' => 'a!lastname'
			),
		);

		if (Shop::isFeatureActive())
			$this->fields_list['id_shop'] = array('title' => $this->l('ID Shop'), 'align' => 'center', 'width' => 25, 'type' => 'int');

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id_collection';
		$helper->actions = array('edit', 'delete');
		$helper->show_toolbar = true;
		$helper->imageType = 'jpg';
        
		$helper->toolbar_btn['new'] =  array(
			'href' => AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
			'desc' => $this->l('Add new')
		);
        
		$helper->title = $this->displayName;
		$helper->table = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		return $helper;
	}

	public function installFixtures()
	{
		$return = true;
		$tab_texts = array(
			array('name' => $this->l('Bill Kenney'),'name_collection' => $this->l('Collection name'), 'list_products_id' => '1,2,3,4,5', 'company' => 'Co-Founder, Art Director' ,'text' => $this->l('Aenean vestibulum mi quis est ultrices vehicula. Quisque pellentesque augue ante, eget placerat odio.'), 'file_name' => 'blockcollection-1-1.jpg'),
			array('name' => $this->l('Jonhy Packer'),'name_collection' => $this->l('Collection name'), 'list_products_id' => '4,5,6,7,8', 'company' => 'Co-Founder, Art Director' ,'text' => $this->l('Nulla porttitor accumsan tincidunt. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem.'), 'file_name' => 'blockcollection-2-1.jpg'),
			array('name' => $this->l('Alex Max'),'name_collection' => $this->l('Collection name'), 'list_products_id' => '7,8,9,10,11', 'company' => 'Co-Founder, Art Director' ,'text' => $this->l('Lorem ipsum dolor sit amet, consectetur adipiscing elit.'), 'file_name' => 'blockcollection-3-1.jpg')
		);
		
		foreach($tab_texts as $tab)
		{
			$blockcollection = new blockcollectionClass();
			foreach (Language::getLanguages(false) as $lang){
			     $blockcollection->text[$lang['id_lang']] = $tab['text'];
                 $blockcollection->name[$lang['id_lang']] = $tab['name'];
                 $blockcollection->name_collection[$lang['id_lang']] = $tab['name_collection'];
                 $blockcollection->list_products_id[$lang['id_lang']] = $tab['list_products_id'];
                 $blockcollection->company[$lang['id_lang']] = $tab['company'];
			}
            				
			$blockcollection->file_name = $tab['file_name'];
			$blockcollection->id_shop = $this->context->shop->id;
			$return &= $blockcollection->save();
		}
		return $return;
	}
    
    
    
    // Get All Products
    public function getAllProducts($id_lang, $id_collection = null, $count = false, $order_by = null, $order_way = null, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
        
		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront')))
			$front = false;
            
        if (empty($id_collection)){
            $products_id_list = $this->getProductsbyIdCollection(null,$this->context->language->id);
            $products_id = '';
            foreach($products_id_list as $key => $products_id_list_item){
                if($key==0){
                    $products_id .= $products_id_list_item['list_products_id'];
                }else{
                    $products_id .= ','.$products_id_list_item['list_products_id'];    
                }
            }
        }else{
            $products_id_list = $this->getProductsbyIdCollection($id_collection, $this->context->language->id);
            $products_id = $products_id_list[0]['list_products_id'];    
        }
        
        if (empty($last_id))
            $last_id = 0;
		if (empty($order_way)) 
            $order_way = 'DESC';
		if (empty($order_by))
            $order_by = 'id_product';
		
		if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way))
			die(Tools::displayError());

		$groups = FrontController::getCurrentCustomerGroups();
		$sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
		if (strpos($order_by, '.') > 0)
		{
			$order_by = explode('.', $order_by);
			$order_by_prefix = $order_by[0];
			$order_by = $order_by[1];
		}
		if ($count)
		{
			$sql = 'SELECT COUNT(p.`id_product`) AS nb
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					WHERE product_shop.`active` = 1
					AND product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'"
					'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
					AND p.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` '.$sql_groups.'
					)';
			return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
		}

		$sql = new DbQuery();
		$sql->select(
			'p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
			pl.`meta_keywords`, pl.`meta_title`, pl.`name`, MAX(image_shop.`id_image`) id_image, il.`legend`, m.`name` AS manufacturer_name,
			product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'" as new'
		);

		$sql->from('product', 'p');
		$sql->join(Shop::addSqlAssociation('product', 'p'));
		$sql->leftJoin('product_lang', 'pl', '
			p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl')
		);
		$sql->leftJoin('image', 'i', 'i.`id_product` = p.`id_product`');
		$sql->join(Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1'));
		$sql->leftJoin('image_lang', 'il', 'i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang);
		$sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

		$sql->where('product_shop.`active` = 1');
		if ($front)
			$sql->where('product_shop.`visibility` IN ("both", "catalog")');
		//$sql->where('product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'"');
     
        $sql->where('p.`id_product` IN (
		SELECT cp.`id_product`
		FROM `'._DB_PREFIX_.'category_group` cg
		LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
		WHERE cg.`id_group` '.$sql_groups.')
        AND p.`id_product` IN ('.$products_id.')');
        
		
		$sql->groupBy('product_shop.id_product');

		$sql->orderBy('p.`'.$order_by.'`', $order_way);

		if (Combination::isFeatureActive())
		{
			$sql->select('MAX(product_attribute_shop.id_product_attribute) id_product_attribute');
			$sql->leftOuterJoin('product_attribute', 'pa', 'p.`id_product` = pa.`id_product`');
			$sql->join(Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on = 1'));
		}
		$sql->join(Product::sqlStock('p', Combination::isFeatureActive() ? 'product_attribute_shop' : 0));

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		if ($order_by == 'price')
			Tools::orderbyPrice($result, $order_way);
		if (!$result)
			return false;

		$products_ids = array();
		foreach ($result as $row)
			$products_ids[] = $row['id_product'];
		// Thus you can avoid one query per product, because there will be only one query for all the products of the cart
		Product::cacheFrontFeatures($products_ids, $id_lang);

		return Product::getProductsProperties((int)$id_lang, $result);
	}
    
    // Ajax load products function
    public function ajaxCall($id_collection)
	{
	    $context = Context::getContext();
        
        $products = $this->getAllProducts((int)Context::getContext()->language->id,$id_collection); 
        $context->smarty->assign('products', $products);
        
        $product_list = $context->smarty->fetch(_PS_MODULE_DIR_.'blockcollection/product-list.tpl');
        
		return Tools::jsonEncode(array('productList' => $product_list));
	}
}
