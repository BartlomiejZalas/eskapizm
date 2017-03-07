<?php
/*
*  @author SonNC Ovic <nguyencaoson.zpt@gmail.com>
*/
class OvicNewsletter extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';	
    public $arrWidth = array();
    public $pathImage = '';
	public function __construct()
	{
        
		$this->name = 'ovicnewsletter';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'OvicSoft';		
		$this->secure_key = Tools::encrypt('ovic-soft'.$this->name);
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Ovic Register Newsletter Module');
		$this->description = $this->l('Ovic Register Newsletter Module');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		$this->pathImage = dirname(__FILE__).'/images/';
        $this->arrWidth = array('1'=>'1 column', '2'=>'2 column', '3'=>'3 column', '4'=>'4 column', '5'=>'5 column', '6'=>'6 column', '7'=>'7 column', '8'=>'8 column', '9'=>'9 column', '10'=>'10 column', '11'=>'11 column', '12'=>'12 column');
	}
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
				if (!Db::getInstance()->execute(trim($query))) return false;			
		}		
		if(!parent::install() 
			|| !$this->registerHook('displayHeader')
			|| !$this->registerHook('displayFooter')) return false;
		if (!Configuration::updateGlobalValue('MOD_OVIC_NEWSLETTER', '1')) return false;
		return true;
	}
	
	public function uninstall($keep = true)
	{	   
		if (!parent::uninstall()) return false;		
        if($keep){			
            if(!Db::getInstance()->execute('
			DROP TABLE IF EXISTS 
			`'._DB_PREFIX_.'ovic_register_newsletter`')) return false;
			
        }		
        if (!Configuration::deleteByName('MOD_OVIC_NEWSLETTER')) return false;
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
	public function getBackgroundSrc($image = '', $check = false){
        if($image && file_exists($this->pathImage.$image))
            return _PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/images/'.$image;
        else
            if($check == true) 
                return '';
            else
                return _PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/images/default.jpg'; 
    }
	public function getLangOptions($langId = 0){
        if(intval($langId) == 0) $langId = Context::getContext()->language->id;
        $items = DB::getInstance()->executeS("Select id_lang, name, iso_code From "._DB_PREFIX_."lang Where active = 1");
        $options = '';
        if($items){
            foreach($items as $item){
                if($item['id_lang'] == $langId){
                    $options .= '<option value="'.$item['id_lang'].'" selected="selected">'.$item['name'].'</option>';
                }else{
                    $options .= '<option value="'.$item['id_lang'].'">'.$item['name'].'</option>';
                }
            }
        }
        return $options;
    }
    public function getWidthOptions($width=6){
        
        $options = '';        
        foreach($this->arrWidth as $key=>$value){
            if($key == $width){
                $options .= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
            }else{
                $options .= '<option value="'.$key.'">'.$value.'</option>';
            }
        }
        
        return $options;
    }
	public function getAllLangs(){
        $langId = Context::getContext()->language->id;
        return $items = DB::getInstance()->executeS("Select id_lang, name, iso_code From "._DB_PREFIX_."lang Where active = 1 Order By id_lang");
        
    }
	
	
	public function ovicRenderForm($id = 0){		
		$shopId = Context::getContext()->shop->id;
        $langId = Context::getContext()->language->id;
		$langs = $this->getAllLangs();
		$inputContent = '';
		$inputBackground = '';
        $inputWidth = '';
        $html = '';
		if($langs){
			foreach ($langs as $key => $lang) {
                $item = DB::getInstance()->getRow("Select * From "._DB_PREFIX_."ovic_register_newsletter Where id_shop = ".$shopId." AND id_lang = ".$lang['id_lang']);
                
                if(!$item) $item = array('id_shop'=>$shopId, 'id_lang'=>$lang['id_lang'], 'content'=>'', 'background'=>'', 'width'=>6);
                $background = '';
                if($item['background']) $background = $this->getBackgroundSrc($item['background'], true);
                if($background) $background = '<img src="'.$background.'" class="img-responsive" />';
                else $background = '';
                
				if($lang['id_lang'] == $langId){
				    $inputWidth .= '<div class="lang-'.$lang['id_lang'].'" id="width-'.$lang['id_lang'].'">
                                        <select name="widths[]" class="form-control">'.$this->getWidthOptions($item['width']).'</select>
                                    </div>';
					$inputContent .= '<div class="lang-'.$lang['id_lang'].'" id="content-'.$lang['id_lang'].'">
                                        <textarea class="editor" name="contents[]">'.$item['content'].'</textarea>
                                    </div>';
                    $inputBackground .= '<div class="lang-'.$lang['id_lang'].'" id="background-'.$lang['id_lang'].'">
                                            <input type="file" id="title-1" name="backgrounds[]" />
                                            '.$background.'
                                        </div>';						
				}else{
				    $inputWidth .= '<div style="display:none" class="lang-'.$lang['id_lang'].'" id="width-'.$lang['id_lang'].'">
                                        <select name="widths[]" class="form-control">'.$this->getWidthOptions($item['width']).'</select>
                                    </div>';
				    $inputContent .= '<div style="display:none" class="lang-'.$lang['id_lang'].'" id="content-'.$lang['id_lang'].'">
                                        <textarea class="editor" name="contents[]">'.$item['content'].'</textarea>
                                    </div>';
                    $inputBackground .= '<div style="display:none" class="lang-'.$lang['id_lang'].'" id="background-'.$lang['id_lang'].'">
                                            <input type="file" id="title-1" name="backgrounds[]" />
                                            '.$background.'
                                        </div>';					
				}				
			}
		}
        
		$langOptions = $this->getLangOptions();		
		$html .= '<input type="hidden" name="action" value="saveData" />';

		$html .= '<div class="form-group">
                    <label class="control-label col-sm-2">'.$this->l('Text').'</label>
                    <div class="col-lg-10">
                        <div class="col-sm-10">                            
                            '.$inputContent.'         
                        </div>
                        <div class="col-sm-2">
                            <select onchange="changeLanguage(this.value)" class="lang form-control">'.$langOptions.'</select>
                        </div>
                    </div>
                </div>';
		$html .= '<div class="form-group">
                    <label class="control-label col-sm-2">'.$this->l('Background').'</label>
                    <div class="col-lg-10">
                        <div class="col-sm-10">
                            '.$inputBackground.'
                        </div>
                        <div class="col-sm-2">
                            <select onchange="changeLanguage(this.value)" class="lang form-control">'.$langOptions.'</select>
                        </div>
                    </div>
                </div>';
		return $html;
	}
	public function getContent()
	{
        //$this->context->controller->addJquery();
        $action = Tools::getValue('action', 'view');
        if($action == 'view'){
            $this->context->controller->addCSS(($this->_path).'css/back-end/style.css');        
    		$this->context->controller->addJS(($this->_path).'js/back-end/common.js');                
            $this->context->controller->addJS(($this->_path).'js/back-end/ajaxupload.3.5.js');
            $this->context->controller->addJS(($this->_path).'js/back-end/tinymce.inc.js');
            $this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tinymce.min.js');           
            $langId = Context::getContext()->language->id;
            $shopId = Context::getContext()->shop->id;
            $this->context->smarty->assign(array(
                
                'baseModuleUrl'=> _PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name,
                'moduleId'=>$this->id,
                'langId'=>$langId,
                'ad'=>dirname($_SERVER["PHP_SELF"]),
                'iso'=>$this->context->language->iso_code,
                'langOptions'=>$this->getLangOptions($langId),
                'secure_key'=> $this->secure_key,
                'form'=>$this->ovicRenderForm()
            ));
    		return $this->display(__FILE__, 'views/templates/admin/modules.tpl');    
        }else{
            if(method_exists($this, $action)){
                $this->$action();             
            }
            Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
        }
        
	}
    public function saveData(){
		$this->clearCache();
        $langId = Context::getContext()->language->id;
        $shopId = Context::getContext()->shop->id;
        $widths = 12;
        $contents = $_POST['contents'];        
        $files = $_FILES['backgrounds'];
        $fileNames = $files['name'];
        $fileTypes = $files['type'];
        $fileTmp_names = $files['tmp_name'];
        $fileErrors = $files['error'];
        $langs = $this->getAllLangs();
        $arrInsert = array();
        $types = array('jpg', 'jpeg', 'png', 'gif');
        if($langs){            
            foreach($langs as $i=>$lang){
                $fileExt = strtolower(pathinfo($fileNames[$i], PATHINFO_EXTENSION));
                if($fileErrors[$i] == 0){
                    if(in_array($fileExt, $types)){                        
                        $newFileName = 'background-'.$shopId.'-'.$lang['id_lang'].'.'.$fileExt;                        
                        if(!@move_uploaded_file($fileTmp_names[$i], $this->pathImage.$newFileName)){
                            $newFileName = '';
                        }    
                    }else{
                        $newFileName = '';
                    }                    
                }else{
                    $newFileName = '';
                }
                $check = DB::getInstance()->getValue("Select id_shop From "._DB_PREFIX_."ovic_register_newsletter Where `id_shop` = ".$shopId." AND `id_lang` = ".$lang['id_lang']);
                if($check){
                    if($newFileName == ''){
                        DB::getInstance()->execute("Update "._DB_PREFIX_."ovic_register_newsletter Set `content` = '".$contents[$i]."', `width`='".(int)$widths[$i]."' Where `id_shop`=".$shopId." AND `id_lang` = ".$lang['id_lang']);
                    }else{
                        DB::getInstance()->execute("Update "._DB_PREFIX_."ovic_register_newsletter Set `content` = '".$contents[$i]."', `background` = '".$newFileName."', `width`='".(int)$widths[$i]."' Where `id_shop`=".$shopId." AND `id_lang` = ".$lang['id_lang']);
                    }
                }else{
                    $arrInsert[] = array('id_shop'=>$shopId, 'id_lang'=>$lang['id_lang'], 'content'=>$contents[$i], 'background'=>$newFileName, 'width'=>(int)$widths[$i]);
                }
            }
        }
        if($arrInsert) DB::getInstance()->insert('ovic_register_newsletter', $arrInsert);
        return true;
    }
    public function hookdisplayHeader()
	{	
		$this->context->controller->addCSS(($this->_path).'ovicnewsletter.css');
        $this->context->controller->addJS(($this->_path).'ovicnewsletter.js');
	}
	public function hookdisplayFooter($params)
	{
	   $page_name = Dispatcher::getInstance()->getController();
       if($page_name != 'index') return '';
	   //Context::getContext()->cookie->__set('persistent', 0);
       if(isset(Context::getContext()->cookie->persistent)) $persistent = Context::getContext()->cookie->persistent;
       else $persistent = 0;
       if($persistent == 0){
            if (!$this->isCached('ovicnewsletter.tpl'))
    		{
                $langId = Context::getContext()->language->id;
                $shopId = Context::getContext()->shop->id;
    			$item = DB::getInstance()->getRow("Select * From "._DB_PREFIX_."ovic_register_newsletter Where `id_shop`=".$shopId." AND `id_lang`=".$langId." AND `content` <> ''");                
                if($item){
                    if($item['background']) $item['background'] = $this->getBackgroundSrc($item['background'], true);
                }else $item = array();                           
    			$this->context->smarty->assign(array('newsletter_setting'=>$item, 'ovicNewsletterUrl'=>_PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name));                
    		}
    		return $this->display(__FILE__, 'ovicnewsletter.tpl');
       }else return '';
	}    
    function clearCache($name=null)    
	{		
		parent::_clearCache('ovicnewsletter.tpl');
	}
    
}
