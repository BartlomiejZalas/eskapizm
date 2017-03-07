<?php
/**
 * module ovicparallaxblock
 * ------------------------------------------------
 * Copyright 2015 by HoangGia@yahoo.com
 * Author: HoangGia@yahoo.com
 * License Information:
 * ------------------------------------------------
 * 1. You have my permission to edit, update anything on the source code.
 * 2. You have my permission to use this class on any projects ( included commercial projects )
 * 3. You can not change function or method of module.
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Module"), to deal
 * in the Module without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Module, and to permit persons to whom the Module is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Module.
 * You can update, write more extension, drivers.
 * THE MODULE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE MODULE OR THE USE OR OTHER DEALINGS IN
 * THE MODULE.
 */
if (!defined('_PS_VERSION_')) exit;
include_once (dirname(__file__) . '/class/Parallax.php');
class OvicParallaxBlock extends Module{
    const INSTALL_SQL_FILE = 'install.sql';
    protected static $hookArr = Array('displayTopColumn','displayHomeTopColumn','displayHomeTopContent','displayHome','displayHomeBottomContent','displayHomeBottomColumn','displayBottomColumn','displayFooter', 'displayParalax1', 'displayParalax2' , 'displayParalax3', 'displayParalax4', 'displayParalax5' , 'displayParalax6');
    public static $sameDatas = '';		
	protected static $tables = array('ovic_parallax'=>'','ovic_parallax_lang'=>'lang',  'ovic_parallax_shop'=>'');
    
    public function __construct()
    {
        $this->name = 'ovicparallaxblock';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'OvicSoft';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Ovic - Parallax block');
        $this->description = $this->l('Add parallax background to any block.');
        $this->secure_key = Tools::encrypt($this->name);
        self::$sameDatas = dirname(__FILE__).'/samedatas/';
    }
    public function install($delete_params = true)
	{
	   if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) return false;
        else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE)) return false;        
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", trim($sql));
        foreach ($sql as $query)
            if (!Db::getInstance()->execute(trim($query))) return false;
            
	    if (!parent::install())
            return false;
        $result = true;
        foreach (self::$hookArr as $hookname){
            if (!$this->registerHook($hookname)){
                $result &= false;
                break;
            }
        }
        if (!$result || !$this->registerHook('DisplayBackOfficeHeader') || !$this->registerHook('displayHeader'))
            return $result;
        if ($delete_params)
            if (!$this->importSameData())
				return false;
        return true;
    }
    
    public function importSameData($directory=''){
		//foreach(self::$tables as $table=>$value){
			//Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.$table);
		//}
        foreach(self::$tables as $table=>$value) Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.$table);
        if($directory) self::$sameDatas = $directory;
		$langs = Db::getInstance()->executeS("Select id_lang From "._DB_PREFIX_."lang Where active = 1");			
		if(self::$tables){
		  
          $curent_option = Configuration::get('OVIC_CURRENT_DIR');
            if($curent_option) $curent_option .= '.';         
            else $curent_option = '';
			
            foreach(self::$tables as $table=>$value){
                //Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.$table);
				if (file_exists(self::$sameDatas.$curent_option.$table.'.sql')){
					$sql = file_get_contents(self::$sameDatas.$curent_option.$table.'.sql');
					$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
					$sql = preg_split("/;\s*[\r\n]+/", trim($sql));
					if($value == 'lang'){
						foreach ($sql as $query){
							foreach($langs as $lang){								
								$query_result = str_replace('id_lang', $lang['id_lang'], trim($query));
								Db::getInstance()->execute($query_result);
							}
						}				
                    }else if($value == 'position-test'){
                        foreach ($sql as $query){                            
                            if(strpos($query, 'displayHeader')){
								$hookId = hook::getIdByName('displayHeader');                                        
								$query = str_replace('displayHeader', $hookId, trim($query));	
							}elseif(strpos($query, 'displayBackOfficeHeader')){
								$hookId = hook::getIdByName('displayBackOfficeHeader');                                        
								$query = str_replace('displayBackOfficeHeader', $hookId, trim($query));	
							}elseif(strpos($query, 'displayHome')){
								$hookId = hook::getIdByName('displayHome');                                        
								$query = str_replace('displayHome', $hookId, trim($query));	
							}elseif(strpos($query, 'displayLeftColumn')){
								$hookId = hook::getIdByName('displayLeftColumn');
								$query = str_replace('displayLeftColumn', $hookId, trim($query));	
							}elseif(strpos($query, 'displayRightColumn')){
								$hookId = hook::getIdByName('displayRightColumn');                                        
								$query = str_replace('displayRightColumn', $hookId, trim($query));	
							}elseif(strpos($query, 'displayFooter')){
								$hookId = hook::getIdByName('displayFooter');                                        
								$query = str_replace('displayFooter', $hookId, trim($query));	
							}elseif(strpos($query, 'displayNav')){
								$hookId = hook::getIdByName('displayNav');                                        
								$query = str_replace('displayNav', $hookId, trim($query));	
							}elseif(strpos($query, 'displayTopColumn')){
								$hookId = hook::getIdByName('displayTopColumn');                                        
								$query = str_replace('displayTopColumn', $hookId, trim($query));	
							}elseif(strpos($query, 'CustomHtml')){
								$hookId = hook::getIdByName('CustomHtml');                                        
								$query = str_replace('CustomHtml', $hookId, trim($query));	
							}elseif(strpos($query, 'Contactform')){
								$hookId = hook::getIdByName('Contactform');
								$query = str_replace('Contactform', $hookId, trim($query));	
							}elseif(strpos($query, 'displayTop')){
								$hookId = hook::getIdByName('displayTop');
								$query = str_replace('displayTop', $hookId, trim($query));	
							}
                            if($query)
                                Db::getInstance()->execute($query);								
						}
					}else{
						foreach ($sql as $query){
							if (!Db::getInstance()->execute(trim($query))) return false;
						}
					}
				}
				
			}
		}
		return true;
	}
	public function exportSameData($directory=''){
	   if($directory) self::$sameDatas = $directory;
        $curent_option = Configuration::get('OVIC_CURRENT_DIR');
        if($curent_option) $curent_option .= '.';         
        else $curent_option = '';
            
		$link = mysql_connect(_DB_SERVER_,_DB_USER_,_DB_PASSWD_);
		mysql_select_db(_DB_NAME_,$link);		
		foreach(self::$tables as $table=>$type){
			$fields = array();
			$query2 = mysql_query('SHOW COLUMNS FROM '._DB_PREFIX_.$table);				
				while($row = mysql_fetch_row($query2))
					$fields[] = $row[0];
			
			$return = '';
			if($type == 'lang'){
				$query1 = mysql_query('SELECT * FROM '._DB_PREFIX_.$table." Where id_lang = ". Context::getContext()->language->id);		
				$num_fields = mysql_num_fields($query1);
				for ($i = 0; $i < $num_fields; $i++) 
				{
					while($row = mysql_fetch_row($query1))
					{
						$return.= 'INSERT INTO PREFIX_'.$table.' VALUES(';
						for($j=0; $j<$num_fields; $j++) 
						{
							$row[$j] = addslashes($row[$j]);
                            $row[$j] = str_replace(array("\n", "\r"), '', $row[$j]);
							//$row[$j] = ereg_replace("\n","\\n",$row[$j]);
							if (isset($row[$j])) {
								if($fields[$j] == 'id_lang')
								 	$return .= '"id_lang"' ;								
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
            }else if($type == 'position-test'){
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
                            $row[$j] = str_replace(array("\n", "\r"), '', $row[$j]);
							//$row[$j] = ereg_replace("\n","\\n",$row[$j]);
							if (isset($row[$j])) {
							     
                                if($fields[$j] == 'hook_postition'){
                                    $hookName = Hook::getNameById((int)$row[$j]);
                                    $return.= '"'.$hookName.'"' ;
                                }else
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
                            $row[$j] = str_replace(array("\n", "\r"), '', $row[$j]);
							//$row[$j] = ereg_replace("\n","\\n",$row[$j]);
							if (isset($row[$j])) {								
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
			$return.="\n";
			$handle = fopen(self::$sameDatas.$curent_option.$table.'.sql','w+');
			fwrite($handle,$return);
			fclose($handle);
		}
		return true;
	}
    
    
    
    private function installDb()
    {
        $res = Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ovic_parallax` (
        `id_parallax` int(6) NOT NULL AUTO_INCREMENT,
        `image` varchar(64) DEFAULT NULL,
        `ratio` float DEFAULT NULL,
        `module` varchar(64) DEFAULT NULL,
        `hook` varchar(64) DEFAULT NULL,
        `hook_postition` int(2) NOT NULL,
        `type` tinyint(1) unsigned DEFAULT 1,
        `active` tinyint(1) unsigned DEFAULT 1,
        PRIMARY KEY(`id_parallax`)
        ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8');
        $res &= Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ovic_parallax_lang` (
        `id_parallax` int(6) NOT NULL,
        `id_lang` int(10) unsigned NOT NULL,
        `content` text DEFAULT NULL,
        PRIMARY KEY(`id_parallax`,`id_lang`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8');
        $res &= Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ovic_parallax_shop` (
        `id_parallax` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        PRIMARY KEY(`id_parallax`,`id_shop`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8');
        return $res;
    }
    public function uninstall($delete_params = true)
    {
        if (!parent::uninstall())
			return false;
        if ($delete_params)
			if (!$this->uninstallDB())
				return false;
		return true;
    }
    private function installSampleData()
    {
        $sql = "INSERT INTO `" . _DB_PREFIX_ . "ovic_parallax` (`id_parallax`, `image`, `ratio`, `module`, `hook`, `hook_postition`, `type`, `active`) VALUES
                (1,	'f99f774aa6a68260b2bee24026214e2e.jpg',	0.1,	'blocknewsletter',	'displayFooter',	3,	0,	1);";
        $result &= Db::getInstance()->execute($sql);
        $sql = "INSERT INTO `" . _DB_PREFIX_ .
            "ovic_parallax_lang` (`id_parallax`, `id_lang`, `content`) VALUES ";
        foreach (Language::getLanguages(false) as $lang)
            $sql .= "(1,".$lang['id_lang'].",''),";
        $sql = rtrim($sql, ",").";";
        $result &= Db::getInstance()->execute($sql);        
        $sql = "INSERT INTO `" . _DB_PREFIX_ . "ovic_parallax_shop` (`id_parallax`, `id_shop`) VALUES
                    (1, ". (int)$this->context->shop->id.");";
        $result = Db::getInstance()->execute($sql);
        return $result;
    }
    private function uninstallDB()
    {
        Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'ovic_parallax`');
        Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'ovic_parallax_lang`');
        Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'ovic_parallax_shop`');
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
    public function hookDisplayBackOfficeHeader()
	{
		if (Tools::getValue('configure') != $this->name)
			return;
		$this->context->controller->addJquery();
        $this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
        $this->context->controller->addJS($this->_path.'js/tinymce.inc.js');
		$this->context->controller->addJS($this->_path.'js/ovicparallax_admin.js');
        $this->context->controller->addCSS($this->_path.'css/parallax_admin.css');
	}
    public function hookdisplayHeader($params)
	{
	   
        $this->context->controller->addCSS($this->_path.'css/ovicparallax.css');
        $this->context->controller->addJS($this->_path.'js/jquery.stellar.min.js');
        $this->context->controller->addJS($this->_path.'js/ovicparallax.js');
	}
    private function displayForm()
    {
        $id_position = (int)Tools::getValue('id_position',0);
        $sql = 'SELECT op.`id_parallax` FROM `' . _DB_PREFIX_ . 'ovic_parallax` op
                LEFT JOIN `' . _DB_PREFIX_ . 'ovic_parallax_shop` ops ON op.`id_parallax` = ops.`id_parallax`
                WHERE op.`hook_postition` = '.$id_position.' AND ops.`id_shop` = '. $this->context->shop->id;
        $id_parallax = Db::getInstance()->getValue($sql);
        if ($id_parallax && Validate::isUnsignedId($id_parallax))
            $parallax = new Parallax($id_parallax);
        else
            $parallax = new Parallax();
        $has_image = false;
        if ($parallax->image && strlen($parallax->image)>0)
            $has_image = true;
        if (Tools::isSubmit('submitParallax')){
            $parallax->id_parallax = (int)Tools::getValue('id_parallax',$parallax->id_parallax);
            $parallax->active = (int)Tools::getValue('active',$parallax->active);
            $parallax->hook_postition = (int)Tools::getValue('id_position',$parallax->hook_postition);
            $parallax->image =  Tools::getValue('parallax_bg',$parallax->image); 
            if ((Tools::getIsset('sw_type') && Tools::getValue('sw_type') == 'module') || ($parallax->type == 0)){
                $parallax->type = 0;
                $parallax->module = Tools::getValue('module',$parallax->module);
                $parallax->hook = Tools::getValue('hook',$parallax->hook);
            }else{
                $parallax->type = 1;
                $languages = Language::getLanguages();
                foreach ($languages as $language)
                    $parallax->content[$language['id_lang']] = Tools::getValue('item_html_' . $language['id_lang'],$parallax->content[$language['id_lang']]);
            }
        }
        $hookOption = '';
        if (isset($parallax->module) && strlen($parallax->module) > 0)
        {
            $moduleOption = $this->getModulesOption($parallax->module);
            if ($parallax->hook && Validate::isHookName($parallax->hook))
                $hookOption = $this->getHookOptionByModuleName($parallax->module, $parallax->hook);
            else
                $hookOption = $this->getHookOptionByModuleName($parallax->module);
        }
        else{
            $moduleOption = $this->getModulesOption();
            $module_list = $this->getModules();
            $first_module = array_shift($module_list);
            $hookOption = $this->getHookOptionByModuleName($first_module['name']);
        }
        $languages = Language::getLanguages();
        $lang_ul = '<ul class="dropdown-menu">';
        foreach ($languages as $lg)
            $lang_ul .='<li><a href="javascript:hideOtherLanguage('.$lg['id_lang'].');" tabindex="-1">'.$lg['name'].'</a></li>';
        $lang_ul .='</ul>';
        $iso = Language::getIsoById((int)($this->context->language->id));
        $isoTinyMCE = (file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ?
            $iso : 'en');
        $ad = dirname($_SERVER["PHP_SELF"]);
        $html ='<script type="text/javascript">
    			var iso = \'' . $isoTinyMCE . '\' ;
    			var pathCSS = \'' . _THEME_CSS_DIR_ . '\' ;
    			var ad = \'' . $ad . '\' ;
    			$(document).ready(function(){
    			tinySetup({
    				editor_selector :"rte",
            		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,fontselect,fontsizeselect",
            		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,codemagic,|,insertdate,inserttime,preview,|,forecolor,backcolor",
            		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
            		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
            		theme_advanced_toolbar_location : "top",
            		theme_advanced_toolbar_align : "left",
            		theme_advanced_statusbar_location : "bottom",
            		theme_advanced_resizing : false,
                    extended_valid_elements: \'pre[*],script[*],style[*]\',
                    valid_children: "+body[style|script],pre[script|div|p|br|span|img|style|h1|h2|h3|h4|h5],*[*]",
                    valid_elements : \'*[*]\',
                    force_p_newlines : false,
                    cleanup: false,
                    forced_root_block : false,
                    force_br_newlines : true
    				});
    			});</script>';
        $this->context->smarty->assign(array(
            'item' => $parallax,
            'moduleOption' => $moduleOption,
            'hookOption' => $hookOption,
            'lang_ul' => $lang_ul,
            'has_image' => $has_image,
            'image_baseurl' => $this->_path.'img/',
            'ajaxPath' => AdminController::$currentIndex .'&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            'langguages' => array(
				'default_lang' => (int)Configuration::get('PS_LANG_DEFAULT'),
				'all' => $languages,
				'lang_dir' => _THEME_LANG_DIR_)
        ));
        return $html.$this->display(__file__, 'views/templates/admin/parallax.tpl');
    }
    public function displayContent(){
        reset(self::$hookArr);
        $id_position = Tools::getValue('id_position',key(self::$hookArr));
        $this->context->smarty->assign(array(
            'hookArr' => self::$hookArr,            
            'default_position' => $id_position,
            'parallax_form' => $this->displayForm(),
            'postAction' => AdminController::$currentIndex .'&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules')
        ));
        return $this->display(__file__, 'views/templates/admin/main.tpl');
    }
    public function getContent(){
        if(Tools::getValue('data-export')){
            $this->exportSameData();
			//$this->exportDataDemo();
			echo $this->l('Export data success!');
			die;
		}
		if(Tools::getValue('data-import')){
			$this->importSameData();
            //$this->installDataDemo();
			echo $this->l('Install data demo success!');
			die;
		}
        $output = '';
        $errors = array();
        if (Tools::isSubmit('submitParallax')){
            $id_position = (int)Tools::getValue('id_position',key(reset(self::$hookArr)));
            $sql = 'SELECT op.`id_parallax` FROM `' . _DB_PREFIX_ . 'ovic_parallax` op
                    LEFT JOIN `' . _DB_PREFIX_ . 'ovic_parallax_shop` ops ON op.`id_parallax` = ops.`id_parallax`
                    WHERE op.`hook_postition` = '.$id_position.' AND ops.`id_shop` = '. $this->context->shop->id;
            $id_parallax = Db::getInstance()->getValue($sql);
            if ($id_parallax && Validate::isUnsignedId($id_parallax))
                $parallax = new Parallax($id_parallax);
            else
                $parallax = new Parallax();
            $parallax->active = (int)Tools::getValue('active',1);
            //$hook_postition
            $parallax->hook_postition = $id_position; 
            $parallax->ratio = (float)Tools::getValue('ratio'); 
            $sw_type = Tools::getValue('sw_type');
            if ($sw_type == 'module'){
                $parallax->type = 0;
                $parallax->module = Tools::getValue('module');
                $parallax->hook = Tools::getValue('hook');
            }else{
                $parallax->type = 1;
                $languages = Language::getLanguages();
                foreach ($languages as $language)
                    $parallax->content[$language['id_lang']] = Tools::getValue('item_html_' . $language['id_lang']);
            }
            if (Tools::getValue('has_image') == false && (!isset($_FILES['parallax_bg']) || empty($_FILES['parallax_bg']['tmp_name'])))
				$errors[] = $this->l('The image is not set.');
            /* Uploads image and sets slide */
			$type = strtolower(substr(strrchr($_FILES['parallax_bg']['name'], '.'), 1));
			$imagesize = array();
			$imagesize = @getimagesize($_FILES['parallax_bg']['tmp_name']);
			if (isset($_FILES['parallax_bg']) &&
				isset($_FILES['parallax_bg']['tmp_name']) &&
				!empty($_FILES['parallax_bg']['tmp_name']) &&
				!empty($imagesize) &&
				in_array(strtolower(substr(strrchr($imagesize['mime'], '/'), 1)), array('jpg', 'gif', 'jpeg', 'png')) &&
				in_array($type, array('jpg', 'gif', 'jpeg', 'png')))
			{
				$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
				$salt = sha1(microtime());
				if ($error = ImageManager::validateUpload($_FILES['parallax_bg']))
					$errors[] = $error;
				elseif (!$temp_name || !move_uploaded_file($_FILES['parallax_bg']['tmp_name'], $temp_name))
					return false;
				elseif (!ImageManager::resize($temp_name, dirname(__FILE__).'/img/'.Tools::encrypt($_FILES['parallax_bg']['name'].$salt).'.'.$type, null, null, $type))
					$errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
				if (isset($temp_name))
					@unlink($temp_name);
                if (Tools::getValue('old_parallax_bg') != ''){
                    $filename = Tools::getValue('old_parallax_bg');
                    if (file_exists(dirname(__FILE__).'/img/'.$filename))
                        @unlink(dirname(__FILE__).'/img/'.$filename);
                }
                $parallax->image = Tools::encrypt($_FILES['parallax_bg']['name'].$salt).'.'.$type;
            }
            if (!$errors || count($errors) < 1)
                {
                    /* Update */
                    if ($id_parallax && Validate::isUnsignedId($id_parallax)){
                        if (!$parallax->update()) $errors[] = $this->displayError($this->l('The Parallax could not be updated.'));
                    }elseif (!$parallax->add()) $errors[] = $this->displayError($this->l('The Parallax could not be add.'));
                    /* Adds */
                }
            if (!isset($errors) || count($errors) < 1){
                if ($id_parallax && Validate::isUnsignedId($id_parallax))
                    $confirm_msg = $this->l('Parallax successfully updated.');
                else
                    $confirm_msg = $this->l('New Parallax successfully added.');
                Tools::clearCache();
                Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->
                            name . '&token=' . Tools::getAdminTokenLite('AdminModules').'&confirm_msg='.$confirm_msg.'&id_position='.$id_position);
            }
        }elseif (Tools::isSubmit('action')){
            $action = Tools::getValue('action');
            if ($action == 'getModuleHook'){
                $module_name =  Tools::getValue('module_name');
                if ($module_name && Validate::isModuleName($module_name)){
                    $hooks = $this->getHookOptionByModuleName($module_name);
                    //echo $hooks;
                    die($hooks);
                }
            }elseif ($action == 'changeHookPosition'){
                $form_content = $this->displayForm();
                die($form_content);                
            }
        }
        if (count($errors))
			$output .= $this->displayError(implode('<br />', $errors));
        return $output.$this->displayContent();
    }
    private function getModulesOption($selected = null)
    {
        $modules = $this->getModules();
        $html = '';
        if (count($modules) > 0)
            foreach ($modules as $m)
                if (is_null($selected)) $html .= '<option value="' . $m['name'] . '">' . $m['name'] . '</option>';
                else  $html .= '<option ' . ($selected == $m['name'] ? 'selected="selected"' : '') . ' value="' . $m['name'] .
                        '">' . $m['name'] . '</option>';
        return $html;
    }
    public function getHookOptionByModuleName($module_name, $selected = null)
    {
        $hooks = $this->getHooksByModuleName($module_name);
        if (count($hooks) > 0)
        {
            $html = '';
            foreach ($hooks as $h)
                $html .= '<option ' . ($selected == $h['name'] ? 'selected="selected"' : '') . ' value="' . $h['name'] .
                    '">' . $h['name'] . '</option>';
            return $html;
        }
        return;
    }
    private function getModules()
    {
        $id_shop = (int)Context::getContext()->shop->id;
        $results = Db::getInstance()->ExecuteS('
            SELECT m.*
            FROM `' . _DB_PREFIX_ . 'module` m
            JOIN `' . _DB_PREFIX_ . 'module_shop` ms ON (m.`id_module` = ms.`id_module` AND ms.`id_shop` = ' . (int)
                        ($id_shop) . ')
            WHERE m.`name` <> \'' . $this->name . '\'');
        if (count($results) > 0)
        {
            $modules = array();
            foreach ($results as $result)
                if ($this->getHooksByModuleName($result['name'])) $modules[] = $result;
        }
        return $modules;
    }
    private function getHooksByModuleName($module_name)
    {
        $moduleInstance = Module::getInstanceByName($module_name);
        $hooks = array();
        $hookAssign = $this->getHookexecuteList();
        if ($hookAssign && is_array($hookAssign) && sizeof($hookAssign)>0)
            foreach ($hookAssign as $hook)
                if (_PS_VERSION_ < "1.5")
                {
                    if (is_callable(array($moduleInstance, 'hook' . $hook)))
                        $hooks[] = $hook;
                }
                else
                {
                    $retro_hook_name = Hook::getRetroHookName($hook);
                    if (is_callable(array($moduleInstance, 'hook' . $hook)) || is_callable(array($moduleInstance, 'hook' .
                            $retro_hook_name)))
                        $hooks[] = $hook;
                }
        $results = self::getHookByArrName($hooks);
        return $results;
    }
    private function getHookByArrName($arrName)
    {
        $result = Db::getInstance()->ExecuteS('
            SELECT `id_hook`, `name`
            FROM `' . _DB_PREFIX_ . 'hook`
            WHERE `name` IN (\'' . implode("','", $arrName) . '\')');
        return $result;
    }
    private function getHookexecuteList(){
        $sql='SELECT name FROM `' . _DB_PREFIX_ . 'hook` WHERE name NOT LIKE \'action%\' AND name NOT LIKE \'dashboard%\'
                AND name NOT LIKE \'displayAdmin%\' AND name NOT LIKE \'displayAttribute%\' AND name NOT LIKE \'displayBackOffice%\'
                AND name NOT LIKE \'displayBefore%\' AND name NOT LIKE \'displayCarrier%\' AND name NOT LIKE \'displayCompare%\'
                AND name NOT LIKE \'displayCustomer%\' AND name NOT LIKE \'displayFeature%\' AND name NOT LIKE \'display%Product\'
                AND name NOT LIKE \'displayHeader%\' AND name NOT LIKE \'displayInvoice%\' AND name NOT LIKE \'displayMaintenance%\'
                AND name NOT LIKE \'displayMobile%\' AND name NOT LIKE \'displayMyAccount%\' AND name NOT LIKE \'displayOrder%\'
                AND name NOT LIKE \'displayOverride%\' AND name NOT LIKE \'displayPayment%\' AND name NOT LIKE \'displayPDF%\'
                AND name NOT LIKE \'displayProduct%\' AND name NOT LIKE \'displayShopping%\'';
        $results = Db::getInstance()->executeS($sql);
        $hooklist = array();
        if ($results && is_array($results) && sizeof($results))
            foreach ($results as $result)
                $hooklist[] = $result['name'];
        return array_values($hooklist);
    }
    private function generalHook($position, $template = '')
    {
        if ($template == '' || Module::_isTemplateOverloadedStatic(basename(__file__, '.php'), $template) === null)
            $template = 'ovic_parallax.tpl';
        $id_position = array_search($position,self::$hookArr);
        $sql = 'SELECT op.`id_parallax` FROM `' . _DB_PREFIX_ . 'ovic_parallax` op
                LEFT JOIN `' . _DB_PREFIX_ . 'ovic_parallax_shop` ops ON op.`id_parallax` = ops.`id_parallax`
                WHERE op.`hook_postition` = '.$id_position.' AND ops.`id_shop` = '. $this->context->shop->id;
        $id_parallax = Db::getInstance()->getValue($sql);
        $html = '';
        if ($id_parallax && Validate::isUnsignedId($id_parallax)){
            if (!$this->isCached($template, $this->getCacheId($id_parallax)))
            {
                $content = '';
                $parallax = new Parallax($id_parallax,$this->context->language->id);
                if ($parallax->type == 0){
                    if (Validate::isModuleName($parallax->module) && Validate::isHookName($parallax->hook))
                        $content = $this->ModuleHookExec($parallax->module,$parallax->hook);
                }elseif ($parallax->type == 1)
                    $content = $parallax->content;
                $this->context->smarty->assign(array(
                    'background' => $parallax->image,
                    'ratio' =>$parallax->ratio,
                    'content' => $content,
                    'img_path' => $this->_path
                ));
            }
            $html = $this->display(__file__, $template, $this->getCacheId($id_parallax));
        }
        return $html;
    }
    /**
	 * Execute modules for specified hook
	 * @param module $moduleInstance Execute hook for this module only
	 * @param string $hook_name Hook Name
	 * @return string modules output
	 */
     private function ModuleHookExec($module_name, $hook_name){
        $output ='';
        if ($module_name && Validate::isModuleName($module_name))
            $moduleInstance = Module::getInstanceByName($module_name);
        else
            return '';
        if (Validate::isLoadedObject($moduleInstance) && $moduleInstance->id) {
            $altern = 0;
            $id_hook = Hook::getIdByName($hook_name);
            $retro_hook_name = Hook::getRetroHookName($hook_name);
            $disable_non_native_modules = (bool)Configuration::get('PS_DISABLE_NON_NATIVE_MODULE');
            if ($disable_non_native_modules && Hook::$native_module && count(Hook::$native_module) && !in_array($moduleInstance->name, self::$native_module))
				return '';
            //check disable module
            $device = (int)$this->context->getDevice();
           if (Db::getInstance()->getValue('
			SELECT COUNT(`id_module`) FROM '._DB_PREFIX_.'module_shop
			WHERE enable_device & '.(int)$device.' AND id_module='.(int)$moduleInstance->id.
			Shop::addSqlRestriction()) == 0)
                return '';
            // Check permissions
			$exceptions = $moduleInstance->getExceptions($id_hook);
			$controller = Dispatcher::getInstance()->getController();
			$controller_obj = Context::getContext()->controller;
			//check if current controller is a module controller
			if (isset($controller_obj->module) && Validate::isLoadedObject($controller_obj->module))
				$controller = 'module-'.$controller_obj->module->name.'-'.$controller;
			if (in_array($controller, $exceptions))
				return '';
			//retro compat of controller names
			$matching_name = array(
				'authentication' => 'auth',
				'productscomparison' => 'compare'
			);
			if (isset($matching_name[$controller]) && in_array($matching_name[$controller], $exceptions))
				return '';
			if (Validate::isLoadedObject($this->context->employee) && !$moduleInstance->getPermission('view', $this->context->employee))
				return '';
            if (!isset($hook_args['cookie']) or !$hook_args['cookie'])
                $hook_args['cookie'] = $this->context->cookie;
            if (!isset($hook_args['cart']) or !$hook_args['cart'])
                $hook_args['cart'] = $this->context->cart;
            $hook_callable = is_callable(array($moduleInstance, 'hook'.$hook_name));
			$hook_retro_callable = is_callable(array($moduleInstance, 'hook'.$retro_hook_name));
            if (($hook_callable || $hook_retro_callable) && Module::preCall($moduleInstance->name))
			{
				$hook_args['altern'] = ++$altern;
				// Call hook method
				if ($hook_callable)
					$display = $moduleInstance->{'hook'.$hook_name}($hook_args);
				elseif ($hook_retro_callable)
					$display = $moduleInstance->{'hook'.$retro_hook_name}($hook_args);
                $output .= $display;
			}
        }
        return $output;
     }
     //('displayTopColumn','displayHomeTopColumn','displayHomeTopContent','displayHome','displayHomeBottomContent','displayHomeBottomColumn','displayBottomColumn','displayFooter', 'displayParalax1', 'displayParalax2' , 'displayParalax3');
    public function hookdisplayTopColumn($params){
        return $this->generalHook('displayTopColumn');
    }
    public function hookdisplayHomeTopColumn($params){
        return $this->generalHook('displayHomeTopColumn');
    }
    public function hookdisplayHomeTopContent($params){
        return $this->generalHook('displayHomeTopContent');
    }
    public function hookdisplayHome($params){
        return $this->generalHook('displayHome');
    }
    public function hookdisplayHomeBottomContent($params){
        return $this->generalHook('displayHomeBottomContent');
    }
    public function hookdisplayHomeBottomColumn($params){
        return $this->generalHook('displayHomeBottomColumn','funs-block.tpl');
    }
    public function hookdisplayBottomColumn($params){
        return $this->generalHook('displayBottomColumn','bottom-column.tpl');
    }
    public function hookdisplayFooter($params){
        return $this->generalHook('displayFooter');
    }
    public function hookdisplayParalax1($params){
        return $this->generalHook('displayParalax1','paralax1.tpl');
    }
    public function hookdisplayParalax2($params){
        return $this->generalHook('displayParalax2','paralax2.tpl');
    }
    public function hookdisplayParalax3($params){
        return $this->generalHook('displayParalax3','paralax3.tpl');
    }
    public function hookdisplayParalax4($params){
        return $this->generalHook('displayParalax4','paralax4.tpl');
    }
    public function hookdisplayParalax5($params){
        return $this->generalHook('displayParalax5','paralax5.tpl');
    }
    public function hookdisplayParalax6($params){
        return $this->generalHook('displayParalax6','paralax6.tpl');
    }
}