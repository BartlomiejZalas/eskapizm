<?php
if (!defined('_PS_VERSION_'))
	exit;
include_once (dirname(__file__) . '/class/HtmlObject.php');

class BlockHtml extends Module
{
    protected static $hookArr;

    public function __construct()
    {
        $this->name = 'blockhtml';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'OvicSoft';
        parent::__construct();
        $this->displayName = $this->l('Fashion - Banner Block HTML'); 
        $this->description = $this->l('With this module you can put the HTML/JavaScript/CSS code anywhere you want');
        $this->bootstrap = true;
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        if (!isset(BlockHtml::$hookArr)){
            BlockHtml::$hookArr = Array('displayHome','displayTop','displayLeftColumn','displayRightColumn','displayFooter','displayNav','displayTopColumn','CustomHtml','Contactform');
        }
    }
    public function install()
    {//
        if (!parent::install() ||
            !$this->registerHook('displayHeader') ||
            !$this->registerHook('displayBackOfficeHeader') ||
            !$this->registerHook('displayHome') ||
            !$this->registerHook('displayTop') ||
			!$this->registerHook('displayLeftColumn') ||
			!$this->registerHook('displayRightColumn') ||
			!$this->registerHook('displayFooter') ||
            !$this->registerHook('displayNav') ||
            !$this->registerHook('displayTopColumn') ||
            !$this->registerHook('CustomHtml') ||
            !$this->registerHook('Contactform') ||
            !$this->installDB() ||
            !$this->installSampleData()
        ){
            return false;
        }
        return true;
    }
    private function installDB()
    {
        $res = Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'htmlobject` (
        `id_htmlobject` int(6) NOT NULL AUTO_INCREMENT,
        `hook_postition` int (2) NOT NULL,
        `active` TINYINT(1) unsigned DEFAULT 1,
        PRIMARY KEY(`id_htmlobject`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8');
        $res &= Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'htmlobject_lang` (
        `id_htmlobject` int(6) NOT NULL,
        `id_lang` int(10) unsigned NOT NULL,
        `title` varchar(255) DEFAULT NULL,
        `content` text DEFAULT NULL,
        PRIMARY KEY(`id_htmlobject`,`id_lang`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8');
        $res &= Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'htmlobject_shop` (
        `id_htmlobject` int(10) unsigned NOT NULL,
        `id_shop` int(10) unsigned NOT NULL,
        PRIMARY KEY(`id_htmlobject`,`id_shop`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8');
        return $res;
    }
    private function installSampleData()
    {
        $sql = "INSERT INTO `" . _DB_PREFIX_ . "htmlobject` (`id_htmlobject`, `hook_postition`, `active`) VALUES
                (2,	1,	1);";
        $result = Db::getInstance()->execute($sql);
        $sql = "INSERT INTO `" . _DB_PREFIX_ . "htmlobject_lang` (`id_htmlobject`, `id_lang`, `title`, `content`) VALUES ";
        foreach (Language::getLanguages(false) as $lang){
            $sql .= "(2,".$lang['id_lang'].",	'',	'<div class=\"row\">\r\n<div class=\"col-sm-4\">\r\n<div class=\"content\"><a href=\"#\" title=\"\"> <img class=\"img-responsive\" src=\"http://dev04.ovicsoft.com/hoai/fsoption2/img/cms/1.jpg\" alt=\"\" width=\"370\" height=\"170\" /></a></div>\r\n</div>\r\n<div class=\"col-sm-4\">\r\n<div class=\"content\"><a href=\"#\" title=\"\"><img class=\"img-responsive\" src=\"http://dev04.ovicsoft.com/hoai/fsoption2/img/cms/2.jpg\" alt=\"\" width=\"370\" height=\"170\" /></a></div>\r\n</div>\r\n<div class=\"col-sm-4\">\r\n<div class=\"content\"><a href=\"#\" title=\"\"><img class=\"img-responsive\" src=\"http://dev04.ovicsoft.com/hoai/fsoption2/img/cms/3.jpg\" alt=\"\" width=\"370\" height=\"170\" /></a></div>\r\n</div>\r\n</div>'),";
        }
        $sql = rtrim($sql, ",").";";
        $result &= Db::getInstance()->execute($sql);
   
        $sql = "INSERT INTO `" . _DB_PREFIX_ . "htmlobject_shop` (`id_htmlobject`, `id_shop`) VALUES
                (2,	1);";
        $result = Db::getInstance()->execute($sql);
        return $result;
    }
    public function uninstall()
    {
        if (!parent::uninstall() || !$this->uninstallDB()) return false;
        return true;
    }
    private function uninstallDb()
    {
        Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'htmlobject`');
        Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'htmlobject_lang`');
        Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'htmlobject_shop`');
        return true;
    }
    public function getContent()
    {
    	$action = Tools::getValue('action', 'display');
		if($action == 'AjaxCall'){
			$id_position = (int)Tools::getValue('id_position');
			echo $this->AjaxCall($id_position);
			die;
		}else{
			$output = '';
	        $errors = array();
	        $languages = Language::getLanguages();
	        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
	        $id_lang = $this->context->language->id;
	        if (Tools::getValue('confirm_msg')){
	            $output .= $this->displayConfirmation(Tools::getValue('confirm_msg'));
	        }
	        if (Tools::isSubmit('submitGlobal'))
	        {
	                $id_position = (int)Tools::getValue('id_position',1);
	                $html_id = $this->getHtmlIdByPosition($id_position);
	                if ($html_id && count($html_id)>0)
	                    $htmlObject = new HtmlObject($html_id['id_htmlobject']);
	                else
	                    $htmlObject = new HtmlObject();
	
	                $htmlObject->active = (int)Tools::getValue('active');
	                //$hook_postition
	                $htmlObject->hook_postition = (int)Tools::getValue('id_position');
	                foreach ($languages as $language)
	                {
	                    $htmlObject->title[$language['id_lang']] = Tools::getValue('item_title_' . $language['id_lang']);
	                    $htmlObject->content[$language['id_lang']] = Tools::getValue('item_html_' . $language['id_lang']);
	                }
	                if (!$errors || count($errors) < 1)
	                {
	                    /* Update */
	                    if ($html_id && count($html_id)>0)
	                    {
	                        if (!$htmlObject->update()) $errors[] = $this->displayError($this->l('The Advertising slide could not be updated.'));
	                    } elseif (!$htmlObject->add()) $errors[] = $this->displayError($this->l('The Advertising slide could not be add.'));
	                    /* Adds */
	                }
	                if (!isset($errors) || count($errors) < 1){
	                    if ($html_id && count($html_id)>0)
	                        $confirm_msg = $this->l('Slide successfully updated.');
	                    else
	                        $confirm_msg = $this->l('New slide successfully added.');
	                    Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('blockhtml.tpl'));
	                    Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->
	                                name . '&token=' . Tools::getAdminTokenLite('AdminModules').'&confirm_msg='.$confirm_msg.'&id_position='.$id_position);
	                }
	        }
	        if (isset($errors) && count($errors)) $output .= $this->displayError(implode('<br />',
	                $errors));
	        return $output.$this->displayForm();
		}
        
    }
    public function AjaxCall($id_position){
        $html_id = $this->getHtmlIdByPosition($id_position);
        if ($html_id && count($html_id)>0)
            $htmlObject = new HtmlObject($html_id['id_htmlobject']);
        else
            $htmlObject = new HtmlObject();
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
        $languages = Language::getLanguages();
        $lang_ul = '<ul class="dropdown-menu">';
        foreach ($languages as $lg){
            $lang_ul .='<li><a href="javascript:hideOtherLanguage('.$lg['id_lang'].');" tabindex="-1">'.$lg['name'].'</a></li>';
        }
        $lang_ul .='</ul>';
        $this->context->smarty->assign(array(
            'item' => $htmlObject,
            'lang_ul' => $lang_ul,
            'langguages' => array(
				'default_lang' => (int)Configuration::get('PS_LANG_DEFAULT'),
				'all' => $languages,
				'lang_dir' => _THEME_LANG_DIR_)
        ));
        return Tools::jsonEncode($html.$this->display(__file__, 'views/templates/admin/html_content.tpl'));
    }
    private function displayForm()
    {
        $id_position = (int)Tools::getValue('id_position',1);
        $html_id = $this->getHtmlIdByPosition($id_position);
        if ($html_id && count($html_id)>0)
            $htmlObject = new HtmlObject($html_id['id_htmlobject']);
        else
            $htmlObject = new HtmlObject();
        $languages = Language::getLanguages();

        $lang_ul = '<ul class="dropdown-menu">';
        foreach ($languages as $lg){
            $lang_ul .='<li><a href="javascript:hideOtherLanguage('.$lg['id_lang'].');" tabindex="-1">'.$lg['name'].'</a></li>';
        }

        $lang_ul .='</ul>';
        $this->context->smarty->assign(array(
            'item' => $htmlObject,
            'lang_ul' => $lang_ul,
            'hookArr' => BlockHtml::$hookArr,
            'ajaxUrl' => $this->_path.'ajaxhtml.php',
            'default_position' => $id_position,
            'admin_templates' => _PS_MODULE_DIR_.$this->name.'/views/templates/admin/',
            'postAction' => AdminController::$currentIndex .'&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            'langguages' => array(
				'default_lang' => (int)Configuration::get('PS_LANG_DEFAULT'),
				'all' => $languages,
				'lang_dir' => _THEME_LANG_DIR_)
            ));
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
        return $html.$this->display(__file__, 'views/templates/admin/main.tpl');
    }
    private function getHtmlIdByPosition($id_position = null,$active = null){
        if (is_null($id_position))
            return;
        $id_shop = $this->context->shop->id;
        $id_lang = $this->context->language->id;
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'htmlobject h
        LEFT JOIN ' . _DB_PREFIX_ .
            'htmlobject_lang hl ON (h.id_htmlobject = hl.id_htmlobject)
			LEFT JOIN ' . _DB_PREFIX_ .
            'htmlobject_shop hs ON (h.id_htmlobject = hs.id_htmlobject)
			WHERE (hs.`id_shop` = ' . (int)$id_shop . ')
			AND hl.`id_lang` = ' . (int)$id_lang
            .(!is_null($active) && $active ? ' AND hl.`active` = 1':'').
            ' AND h.`hook_postition` = '.(int)$id_position;
        return Db::getInstance()->getRow($sql);
    }

    public function hookdisplayTopColumn($params){
        if ($this->psversion() == 6 )

            return $this->prehook('displayTopColumn');
        else
            return;
    }

    public function hookdisplayNav($params){
        if ($this->psversion() == 6 )
            return $this->prehook('displayNav');
        else
            return;
    }

    public function hookfooter($params){
        return $this->prehook('displayFooter');
    }

    public function hookrightColumn($params){
        return $this->prehook('displayRightColumn');
    }

    public function hookleftColumn($params){
        return $this->prehook('displayLeftColumn');
    }

    public function hooktop($params){
        return $this->prehook('displayTop');
    }

    public function hookdisplayHome($params)
    {
        return $this->prehook('displayHome');
    }

    public function hookDisplayBackOfficeHeader()
	{
		if (Tools::getValue('configure') != $this->name)
			return;
		$this->context->controller->addJquery();
        $this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
        $this->context->controller->addJS($this->_path.'js/tinymce.inc.js');
		$this->context->controller->addJS($this->_path.'js/blockhtml_admin.js');
	}
    public function hookCustomHtml($params){
        return $this->prehook('CustomHtml');
    }

    public function hookContactForm($params){
        return $this->prehook('Contactform');
    }

    public function prehook($position){
        $id_position = array_search($position,BlockHtml::$hookArr)+1;
        if (!$this->isCached('blockhtml.tpl', $this->getCacheId($id_position,true))){
            $htmlObject = $this->getHtmlIdByPosition($id_position);
            $this->context->smarty->assign(array(
                'item' => $htmlObject,
                'hook_position' => $position
            ));
        }
        return $this->display(__file__, 'blockhtml.tpl', $this->getCacheId($id_position));
    }
    public function hookHeader()
    {
        //$this->context->controller->addCSS(($this->_path) . 'css/blockhtml.css', 'all');
        //$this->context->controller->addJS(($this->_path) . 'js/homeadvslide.js');
    }

    public function psversion() {
		$version=_PS_VERSION_;
		$exp=$explode=explode(".",$version);
		return $exp[1];
	}
}
