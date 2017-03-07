<?php
/*
*  @author Ovic Developer
*  @copyright  2014 Ovic Developer
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_'))
	exit;
include_once _PS_MODULE_DIR_.'blocktestimonial/blocktestimonialClass.php';
class BlockTestimonial extends Module
{
    public function __construct()
    {
        $this->name = 'blocktestimonial';
        $this->tab = 'front_office_features';
        $this->version = 1.0;
		$this->author = 'Ovicsoft';
		$this->need_instance = 0;
        
        $this->bootstrap = true;
        parent::__construct();

		$this->displayName = $this->l('Fashion - Testimonial block');
        $this->description = $this->l('Display testimonials of customers.');
    }

	public function install()
	{
		return parent::install() &&
            $this->installDB() &&
            Configuration::updateValue('TESTIMONIAL_NBBLOCKS', 3) &&
			$this->registerHook('HOOK_TESTIMONIAL') &&
            $this->registerHook('displayRightColumn') &&
			$this->registerHook('header') &&
            $this->installFixtures();
    }

    public function hookHOOK_TESTIMONIAL($params)
	{
	   if (!$this->isCached('blocktestimonial.tpl', $this->getCacheId()))
		{
			$testimonials = $this->getListContent($this->context->language->id);
			$this->context->smarty->assign(array(
                'testimonials' => $testimonials,
                'nbblocks' => count($testimonials)
            ));
		}
		return $this->display(__FILE__, 'blocktestimonial.tpl', $this->getCacheId());
	}
	
    public function hookdisplayLeftColumn($params) {
        return $this->hookHOOK_TESTIMONIAL($params);
    }
    public function hookdisplayRightColumn($params) {
        return $this->hookHOOK_TESTIMONIAL($params);
    }
    
	public function hookHeader($params)
	{
	    // CSS in global.css file
		//$this->context->controller->addCSS(($this->_path).'blocktestimonial.css', 'all');
        $this->context->controller->addJS($this->_path.'blocktestimonial.js');
	}
    
  /********************* Database ****************************/
    
	public function installDB()
	{
		$return = true;
		$return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'block_testimonials` (
				`id_testimonial` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned NOT NULL ,
				`file_name` VARCHAR(100) NOT NULL,
				PRIMARY KEY (`id_testimonial`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');
		
		$return &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'block_testimonials_lang` (
				`id_testimonial` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_lang` int(10) unsigned NOT NULL ,
				`name` VARCHAR(300) NOT NULL,
                `company` VARCHAR(300) NOT NULL,
                `text` VARCHAR(300) NOT NULL,
				PRIMARY KEY (`id_testimonial`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');
		
		return $return;
	}

	public function uninstall()
	{
		return Configuration::deleteByName('TESTIMONIAL_NBBLOCKS') && 
            $this->uninstallDB() &&
			parent::uninstall();
	}

	public function uninstallDB()
	{
		return Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'block_testimonials`') && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'block_testimonials_lang`');
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
				Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'block_testimonials` (`filename`,`text`,`name`,`company`)
											VALUES ("'.((isset($filename[0]) && $filename[0] != '') ? pSQL($filename[0]) : '').
					'", "'.((isset($_POST['info'.$i.'_text']) && $_POST['info'.$i.'_text'] != '') ? pSQL($_POST['info'.$i.'_text']) : '').
                    '", "'.((isset($_POST['info'.$i.'_name']) && $_POST['info'.$i.'_name'] != '') ? pSQL($_POST['info'.$i.'_name']) : '').
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

		return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'block_testimonials`');
	}

	public function getContent()
	{
		$html = '';
		$id_testimonial = (int)Tools::getValue('id_testimonial');

		if (Tools::isSubmit('saveblocktestimonial'))
		{
			if ($id_testimonial = Tools::getValue('id_testimonial'))
				$blocktestimonial = new blocktestimonialClass((int)$id_testimonial);
			else
				$blocktestimonial = new blocktestimonialClass();
			$blocktestimonial->copyFromPost();
			$blocktestimonial->id_shop = $this->context->shop->id;
			
			if ($blocktestimonial->validateFields(false) && $blocktestimonial->validateFieldsLang(false))
			{
				$blocktestimonial->save();
				if (isset($_FILES['image']) && isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name']))
				{
					if ($error = ImageManager::validateUpload($_FILES['image']))
						return false;
					elseif (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image']['tmp_name'], $tmpName))
						return false;
					elseif (!ImageManager::resize($tmpName, dirname(__FILE__).'/img/blocktestimonial-'.(int)$blocktestimonial->id.'-'.(int)$blocktestimonial->id_shop.'.jpg'))
						return false;
					unlink($tmpName);
					$blocktestimonial->file_name = 'blocktestimonial-'.(int)$blocktestimonial->id.'-'.(int)$blocktestimonial->id_shop.'.jpg';
					$blocktestimonial->save();
				}
				$this->_clearCache('blocktestimonial.tpl');
			}
			else
				$html .= '<div class="conf error">'.$this->l('An error occurred while attempting to save.').'</div>';
		}
		
		if (Tools::isSubmit('updateblocktestimonial') || Tools::isSubmit('addblocktestimonial'))
		{
			$helper = $this->initForm();
			foreach (Language::getLanguages(false) as $lang)
				if ($id_testimonial)
				{
					$blocktestimonial = new blocktestimonialClass((int)$id_testimonial);
					$helper->fields_value['text'][(int)$lang['id_lang']] = $blocktestimonial->text[(int)$lang['id_lang']];
                    $helper->fields_value['name'][(int)$lang['id_lang']] = $blocktestimonial->name[(int)$lang['id_lang']];
                    $helper->fields_value['company'][(int)$lang['id_lang']] = $blocktestimonial->company[(int)$lang['id_lang']];
				}	
				else{
				    $helper->fields_value['text'][(int)$lang['id_lang']] = Tools::getValue('text_'.(int)$lang['id_lang'], '');
                    $helper->fields_value['name'][(int)$lang['id_lang']] = Tools::getValue('name_'.(int)$lang['id_lang'], '');
                    $helper->fields_value['company'][(int)$lang['id_lang']] = Tools::getValue('company_'.(int)$lang['id_lang'], '');
				}
					
			if ($id_testimonial = Tools::getValue('id_testimonial'))
			{
				$this->fields_form[0]['form']['input'][] = array('type' => 'hidden', 'name' => 'id_testimonial');
				$helper->fields_value['id_testimonial'] = (int)$id_testimonial;
 			}
				
			return $html.$helper->generateForm($this->fields_form);
		}
		else if (Tools::isSubmit('deleteblocktestimonial'))
		{
			$blocktestimonial = new blocktestimonialClass((int)$id_testimonial);
			if (file_exists(dirname(__FILE__).'/img/'.$blocktestimonial->file_name))
				unlink(dirname(__FILE__).'/img/'.$blocktestimonial->file_name);
			$blocktestimonial->delete();
			$this->_clearCache('blocktestimonial.tpl');
			Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		else
		{
			$helper = $this->initList();
			$html .= $helper->generateList($this->getListContent((int)Configuration::get('PS_LANG_DEFAULT')), $this->fields_list);
            $html .= '
            <form method="post" action="'.AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'">            	
                <div class="form-group">
                    <input type="submit" class="btn btn-default btn-lg button-new-item" name="submitLinkAdd" value="'.$this->l('Add a new testimonial').'" />
            	</div>
			</form>';
            return $html;
        }                        

		if (isset($_POST['submitModule']))
		{
			Configuration::updateValue('TESTIMONIAL_NBBLOCKS', ((isset($_POST['nbblocks']) && $_POST['nbblocks'] != '') ? (int)$_POST['nbblocks'] : ''));
			if ($this->removeFromDB() && $this->addToDB())
			{
				$this->_clearCache('blocktestimonial.tpl');
				$output = '<div class="conf confirm">'.$this->l('The block configuration has been updated.').'</div>';
			}
			else
				$output = '<div class="conf error"><img src="../img/admin/disabled.gif"/>'.$this->l('An error occurred while attempting to save.').'</div>';
		}
	}

	protected function getListContent($id_lang)
	{
		return  Db::getInstance()->executeS('
			SELECT lc.`id_testimonial`, lc.`id_shop`, lc.`file_name`, lcl.`text`, lcl.`name`, lcl.`company`
			FROM `'._DB_PREFIX_.'block_testimonials` lc
			LEFT JOIN `'._DB_PREFIX_.'block_testimonials_lang` lcl ON (lc.`id_testimonial` = lcl.`id_testimonial`)
			WHERE `id_lang` = '.(int)$id_lang.' '.Shop::addSqlRestrictionOnLang());
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
					'label' => $this->l('Company and Position:'),
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
		$helper->name_controller = 'blocktestimonial';
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
		$helper->submit_action = 'saveblocktestimonial';
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
			'id_testimonial' => array(
				'title' => $this->l('Id'),
				'width' => 120,
				'type' => 'text',
			),
            'name' => array(
				'title' => $this->l('Name'),
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
		$helper->identifier = 'id_testimonial';
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
			array('name' => $this->l('Bill Kenney'), 'company' => 'Co-Founder, Art Director' ,'text' => $this->l('Aenean vestibulum mi quis est ultrices vehicula. Quisque pellentesque augue ante, eget placerat odio.'), 'file_name' => 'blocktestimonial-1-1.jpg'),
			array('name' => $this->l('Jonhy Packer'), 'company' => 'Co-Founder, Art Director' ,'text' => $this->l('Nulla porttitor accumsan tincidunt. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem.'), 'file_name' => 'blocktestimonial-2-1.jpg'),
			array('name' => $this->l('Alex Max'), 'company' => 'Co-Founder, Art Director' ,'text' => $this->l('Lorem ipsum dolor sit amet, consectetur adipiscing elit.'), 'file_name' => 'blocktestimonial-3-1.jpg')
		);
		
		foreach($tab_texts as $tab)
		{
			$blocktestimonial = new blocktestimonialClass();
			foreach (Language::getLanguages(false) as $lang){
			     $blocktestimonial->text[$lang['id_lang']] = $tab['text'];
                 $blocktestimonial->name[$lang['id_lang']] = $tab['name'];
                 $blocktestimonial->company[$lang['id_lang']] = $tab['company'];
			}
            				
			$blocktestimonial->file_name = $tab['file_name'];
			$blocktestimonial->id_shop = $this->context->shop->id;
			$return &= $blocktestimonial->save();
		}
		return $return;
	}
    
 
    
}
