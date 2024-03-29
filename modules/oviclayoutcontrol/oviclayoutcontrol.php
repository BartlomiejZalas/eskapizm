<?php if (!defined('_PS_VERSION_')) exit;
include_once (dirname(__file__) . '/class/Options.php');
include_once (dirname(__file__) . '/class/Hookmanager.php');
class OvicLayoutControl extends Module{
    public static $OptionHookAssign = array('displayNav','displayBeforeLogo','displayTop','displayTopColumn','displayHomeTopColumn','displayLeftColumn','displayRightColumn','displayHomeTopContent','displayHome','displayHomeTab','displayHomeTabContent','displayHomeBottomContent','displayHomeBottomColumn','displayBottomColumn','displayFooter');
    public function __construct()
    {
        $this->name = 'oviclayoutcontrol';
        $this->tab = 'administration';
        $this->version = '1.1';
        $this->author = 'OvicSoft';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Ovic - Layout control');
        $this->description = $this->l('select layout option.');
        $this->secure_key = Tools::encrypt($this->name);
    }
    public function install(){ 
        if (!parent::install() || !$this->registerHook('displayHeader') || !$this->registerHook('displayBackOfficeHeader') || !$this->installDB())
            return false;
        $result =true;
        foreach (self::$OptionHookAssign as $hookname){
            if (!$this->registerHook($hookname)){
                $result &= false;
            }
        }
        if (!$result || !$this->backupAllModulesHook('hook_module','ovic_backup_hook_module'))
            return false;
        if (!$this->registerHook('actionModuleRegisterHookAfter') || !$this->installSampleData())
            return false;
        $langs = Language::getLanguages();
        $tab = new Tab();
        $tab->class_name = "AdminThemeConfig";
        foreach ($langs as $l) {
            $tab->name[$l['id_lang']] = $this->l('Ovic Theme config');
        }
        $tab->module = '';
        $tab->id_parent = 0;  //Root tab
        $tab->save();
        $tab_id = $tab->id;
        $newtab = new Tab();
        $newtab->class_name = "AdminLayoutSetting";
        foreach ($langs as $l) {
            $newtab->name[$l['id_lang']] = $this->l('Layout Control');
        }
        $newtab->module = $this->name;
        $newtab->id_parent = $tab_id;
        $newtab->add();
        $newtab = new Tab();
        $newtab->class_name = "AdminLayoutBuilder";
        foreach ($langs as $l) {
            $newtab->name[$l['id_lang']] = $this->l('Layout Builder');
        }
        $newtab->module = $this->name;
        $newtab->id_parent = $tab_id;
        $newtab->add();
        return true;
    }
    private function installDB()
    {
         $results = Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ovic_backup_hook_module` (
              `id_module` int(10) unsigned NOT NULL,
              `id_shop` int(11) unsigned NOT NULL DEFAULT 1,
              `id_hook` int(10) unsigned NOT NULL,
              `position` tinyint(2) unsigned NOT NULL,
              PRIMARY KEY (`id_module`,`id_hook`,`id_shop`),
              KEY `id_hook` (`id_hook`),
              KEY `id_module` (`id_module`),
              KEY `position` (`id_shop`,`position`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8');
         $results &= Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ovic_options` (
              `id_option` int(6) NOT NULL AUTO_INCREMENT,
              `image` varchar(254),
              `column` varchar(10) NOT NULL,
              `active` TINYINT(1) unsigned DEFAULT 1,
 			    PRIMARY KEY(`id_option`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8')&& Db::getInstance()->execute('
    		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ovic_options_lang` (
    			`id_option` int(6) NOT NULL,
                `id_lang` int(6) unsigned NOT NULL,
    			`name` varchar(255) ,
    			PRIMARY KEY(`id_option`,`id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8')&& Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ovic_options_hook_module` (
              `id_option` int(6) unsigned NOT NULL,
              `hookname` varchar(64) NOT NULL,
              `modules` text NOT NULL,
              `id_shop` int(11) unsigned NOT NULL
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;')&& Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ovic_options_sidebar` (
              `page` varchar(50) NOT NULL,
              `left` TEXT,
              `right` TEXT,
              `id_shop` int(11) unsigned NOT NULL,
              PRIMARY KEY(`page`,`id_shop`),
              KEY `position` (`page`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8');
         return $results;
    }
    private function installSampleData(){
        $sql = "INSERT INTO `" . _DB_PREFIX_ .
            "ovic_options` (`id_option`, `image`, `column`, `active`) VALUES
                (1,	'1421401865preview.jpg',	'0:1:2:3',	1),
                (2,	'14213968211420768225index-v3.jpg',	'3',	1),
                (3,	'1422428969indexboxed.jpg',	'0:1:2:3',	1),
                (4,	'1424944621index-v2.jpg',	'3',	1),
                (5, '1444098006indexv6.jpg',    '3',    1);";
        $result = Db::getInstance()->execute($sql);
        $sql = "INSERT INTO `" . _DB_PREFIX_ .
            "ovic_options_lang` (`id_option`, `id_lang`, `name`) VALUES ";
        foreach (Language::getLanguages(false) as $lang){
                $sql .= "(1,".$lang['id_lang'].",'Option 1'),";
                $sql .= "(2,".$lang['id_lang'].",'Option 2'),";
                $sql .= "(3,".$lang['id_lang'].",'Option Boxed'),";
                $sql .= "(4,".$lang['id_lang'].",'Option 4'),";
                $sql .= "(5,".$lang['id_lang'].",'Option 5'),";
            }
        $sql = rtrim($sql, ",").";";
        $result &= Db::getInstance()->execute($sql);
        $sql = "INSERT INTO `" . _DB_PREFIX_ .
            "ovic_options_hook_module` (`id_option`, `hookname`, `modules`, `id_shop`) VALUES
                (1,	'displayCategorySlider',	'[[\"categoryslider\",\"displayCategorySlider\"]]',	1),
                (1,	'displayFooter',	'[[\"blocklayered\",\"displayFooter\"],[\"statsdata\",\"displayFooter\"],[\"themeconfigurator\",\"displayFooter\"],[\"scrolltop\",\"displayFooter\"],[\"advancefooter\",\"displayFooter\"]]',	1),
                (1,	'displayHome',	'[[\"blockcollection\",\"displayHome\"]]',	1),
                (1,	'displayHomeTab',	'[[\"ovichomefeatured\",\"displayHomeTab\"],[\"ovicblocknewproducts\",\"displayHomeTab\"],[\"ovicblockbestsellers\",\"displayHomeTab\"]]',	1),
                (1,	'displayHomeTabContent',	'[[\"ovichomefeatured\",\"displayHomeTabContent\"],[\"ovicblocknewproducts\",\"displayHomeTabContent\"],[\"ovicblockbestsellers\",\"displayHomeTabContent\"]]',	1),
                (1,	'displayLeftColumn',	'[[\"blockcategories\",\"displayLeftColumn\"],[\"blocklayered\",\"displayLeftColumn\"],[\"blockcms\",\"displayLeftColumn\"],[\"blockmanufacturer\",\"displayLeftColumn\"],[\"blockmyaccount\",\"displayLeftColumn\"],[\"blockpaymentlogo\",\"displayLeftColumn\"],[\"blockstore\",\"displayLeftColumn\"],[\"blocksupplier\",\"displayLeftColumn\"],[\"blocktags\",\"displayLeftColumn\"],[\"blockviewed\",\"displayLeftColumn\"],[\"themeconfigurator\",\"displayLeftColumn\"],[\"ovicblocknewproducts\",\"displayLeftColumn\"],[\"ovicblockbestsellers\",\"displayLeftColumn\"],[\"ovicblockspecials\",\"displayLeftColumn\"],[\"blocklink\",\"displayLeftColumn\"],[\"smartblogcategories\",\"displayLeftColumn\"],[\"smartblogarchive\",\"displayLeftColumn\"]]',	1),
                (1,	'displayNav',	'[[\"fashionblockuserinfo\",\"UserInfo\"],[\"blockcart\",\"displayNav\"],[\"blockcurrencies\",\"displayNav\"],[\"blocklanguages\",\"displayNav\"],[\"fashionblockuserinfo\",\"displayNav\"],[\"ovicnewsletter\",\"displayFooter\"]]',	1),
                (1,	'displayRightColumn',	'[[\"themeconfigurator\",\"displayRightColumn\"],[\"blockwishlist\",\"displayRightColumn\"],[\"blocktestimonial\",\"displayRightColumn\"],[\"ovicblockspecials\",\"displayRightColumn\"],[\"ovicblockbanner\",\"displayRightColumn\"]]',	1),
                (1,	'displaySmartBlogLeft',	'[[\"smartblogcategories\",\"displaySmartBlogLeft\"],[\"smartblogarchive\",\"displaySmartBlogLeft\"]]',	1),
                (1,	'displayTop',	'[[\"pagesnotfound\",\"displayTop\"],[\"sekeywords\",\"displayTop\"],[\"blockwishlist\",\"displayTop\"],[\"imagesearchblock\",\"displayTop\"],[\"advancetopmenu\",\"displayTop\"]]',	1),
                (1,	'displayTopColumn',	'[[\"homeslider\",\"displayTopColumn\"],[\"categoryslider\",\"displayCategorySlider\"]]',	1),
                (1,	'HomeBlog',	'[[\"smartbloghomelatestnews\",\"HomeBlog\"]]',	1),
                (1,	'HOOK_COLLECTION',	'[[\"blockcollection\",\"HOOK_COLLECTION\"]]',	1),
                (1,	'HOOK_NEWSLETTER',	'[[\"ovicblocknewsletter\",\"HOOK_NEWSLETTER\"]]',	1),
                (1,	'HOOK_TESTIMONIAL',	'[[\"blocktestimonial\",\"HOOK_TESTIMONIAL\"]]',	1),
                (1,	'ImageSearch',	'[[\"imagesearchblock\",\"ImageSearch\"]]',	1),
                (1,	'leftsidebar',	'[[\"advancesidebar\",\"leftsidebar\"]]',	1),
                (1,	'rightsidebar',	'[[\"advancesidebar\",\"rightsidebar\"]]',	1),
                (1,	'UserInfo',	'[[\"fashionblockuserinfo\",\"UserInfo\"]]',	1),
                (1,	'displayHomeTopColumn',	'[[\"themeconfigurator\",\"displayHome\"]]',	1),
                (1,	'displayHomeBottomContent',	'[[\"smartbloghomelatestnews\",\"HomeBlog\"],[\"ovicblocknewsletter\",\"HOOK_NEWSLETTER\"]]',	1),
                (2,	'displayCategorySlider',	'[[\"categoryslider\",\"displayCategorySlider\"]]',	1),
                (2,	'displayFooter',	'[[\"blocklayered\",\"displayFooter\"],[\"statsdata\",\"displayFooter\"],[\"themeconfigurator\",\"displayFooter\"],[\"scrolltop\",\"displayFooter\"],[\"advancefooter\",\"displayFooter\"]]',	1),
                (2,	'displayHome',	'[[\"discountproducts\",\"displayHome\"],[\"homecategories\",\"displayHome\"],[\"homecategories\",\"displayHomeCategory\"],[\"ovicblocknewsletter\",\"HOOK_NEWSLETTER\"],[\"blockcollection\",\"displayHome\"],[\"smartbloghomelatestnews\",\"HomeBlog\"]]',	1),
                (2,	'displayHomeTab',	'[]',	1),
                (2,	'displayHomeTabContent',	'[]',	1),
                (2,	'displayNav',	'[[\"blockcontact\",\"displayNav\"],[\"fashionblockuserinfo\",\"UserInfo\"],[\"ovicnewsletter\",\"displayFooter\"]]',	1),
                (2,	'displaySmartBlogLeft',	'[[\"smartblogcategories\",\"displaySmartBlogLeft\"],[\"smartblogarchive\",\"displaySmartBlogLeft\"]]',	1),
                (2,	'displayTop',	'[[\"pagesnotfound\",\"displayTop\"],[\"sekeywords\",\"displayTop\"],[\"fashionblockuserinfo\",\"displayNav\"],[\"blockwishlist\",\"displayTop\"],[\"blockcart\",\"displayTop\"],[\"blockcurrencies\",\"displayNav\"],[\"blocklanguages\",\"displayNav\"]]',	1),
                (2,	'displayTopColumn',	'[[\"imagesearchblock\",\"displayTop\"],[\"advancetopmenu\",\"displayTop\"],[\"categoryslider\",\"displayCategorySlider\"]]',	1),
                (2,	'HomeBlog',	'[[\"smartbloghomelatestnews\",\"HomeBlog\"]]',	1),
                (2,	'HOOK_COLLECTION',	'[[\"blockcollection\",\"HOOK_COLLECTION\"]]',	1),
                (2,	'HOOK_NEWSLETTER',	'[[\"ovicblocknewsletter\",\"HOOK_NEWSLETTER\"]]',	1),
                (2,	'HOOK_TESTIMONIAL',	'[[\"blocktestimonial\",\"HOOK_TESTIMONIAL\"]]',	1),
                (2,	'ImageSearch',	'[[\"imagesearchblock\",\"ImageSearch\"]]',	1),
                (2,	'leftsidebar',	'[[null,\"leftsidebar\"]]',	1),
                (2,	'rightsidebar',	'[[null,\"rightsidebar\"]]',	1),
                (2,	'UserInfo',	'[[\"fashionblockuserinfo\",\"UserInfo\"]]',	1),
                (2,	'displayHomeTopColumn',	'[[\"homeslider\",\"displayTopColumn\"],[\"themeconfigurator\",\"displayTopColumn\"]]',	1),
                (3,	'displayNav',	'[[\"fashionblockuserinfo\",\"UserInfo\"],[\"blockcart\",\"displayNav\"],[\"blockcurrencies\",\"displayNav\"],[\"blocklanguages\",\"displayNav\"],[\"fashionblockuserinfo\",\"displayNav\"],[\"ovicnewsletter\",\"displayFooter\"]]',	1),
                (3,	'displayTop',	'[[\"pagesnotfound\",\"displayTop\"],[\"sekeywords\",\"displayTop\"],[\"blockwishlist\",\"displayTop\"],[\"imagesearchblock\",\"displayTop\"],[\"advancetopmenu\",\"displayTop\"]]',	1),
                (3,	'displayTopColumn',	'[[\"homeslider\",\"displayTopColumn\"],[\"categoryslider\",\"displayCategorySlider\"]]',	1),
                (3,	'displayHomeTopColumn',	'[[\"themeconfigurator\",\"displayHome\"]]',	1),
                (3,	'displayLeftColumn',	'[[\"blockcategories\",\"displayLeftColumn\"],[\"blocklayered\",\"displayLeftColumn\"],[\"blockcms\",\"displayLeftColumn\"],[\"blockmanufacturer\",\"displayLeftColumn\"],[\"blockmyaccount\",\"displayLeftColumn\"],[\"blockpaymentlogo\",\"displayLeftColumn\"],[\"blockstore\",\"displayLeftColumn\"],[\"blocksupplier\",\"displayLeftColumn\"],[\"blocktags\",\"displayLeftColumn\"],[\"blockviewed\",\"displayLeftColumn\"],[\"themeconfigurator\",\"displayLeftColumn\"],[\"ovicblocknewproducts\",\"displayLeftColumn\"],[\"ovicblockbestsellers\",\"displayLeftColumn\"],[\"ovicblockspecials\",\"displayLeftColumn\"],[\"blocklink\",\"displayLeftColumn\"],[\"smartblogcategories\",\"displayLeftColumn\"],[\"smartblogarchive\",\"displayLeftColumn\"]]',	1),
                (3,	'displayRightColumn',	'[[\"themeconfigurator\",\"displayRightColumn\"],[\"blockwishlist\",\"displayRightColumn\"],[\"blocktestimonial\",\"displayRightColumn\"],[\"ovicblockspecials\",\"displayRightColumn\"],[\"ovicblockbanner\",\"displayRightColumn\"]]',	1),
                (3,	'displayHome',	'[[\"blockcollection\",\"displayHome\"]]',	1),
                (3,	'displayHomeTab',	'[[\"ovichomefeatured\",\"displayHomeTab\"],[\"ovicblocknewproducts\",\"displayHomeTab\"],[\"ovicblockbestsellers\",\"displayHomeTab\"]]',	1),
                (3,	'displayHomeTabContent',	'[[\"ovichomefeatured\",\"displayHomeTabContent\"],[\"ovicblocknewproducts\",\"displayHomeTabContent\"],[\"ovicblockbestsellers\",\"displayHomeTabContent\"]]',	1),
                (3,	'displayHomeBottomContent',	'[[\"smartbloghomelatestnews\",\"HomeBlog\"],[\"ovicblocknewsletter\",\"HOOK_NEWSLETTER\"]]',	1),
                (3,	'displayFooter',	'[[\"blocklayered\",\"displayFooter\"],[\"statsdata\",\"displayFooter\"],[\"themeconfigurator\",\"displayFooter\"],[\"scrolltop\",\"displayFooter\"],[\"advancefooter\",\"displayFooter\"]]',	1),
                (3,	'displayHeader',	'[[\"smartblog\",\"displayHeader\"]]',	1),
                (3,	'displayBackOfficeHeader',	'[[\"smartblog\",\"displayBackOfficeHeader\"]]',	1),
                (4,	'displayNav',	'[[\"fashionblockuserinfo\",\"UserInfo\"],[\"blockhtml\",\"displayNav\"],[\"ovicnewsletter\",\"displayFooter\"]]',	1),
                (4,	'displayTop',	'[[\"pagesnotfound\",\"displayTop\"],[\"sekeywords\",\"displayTop\"],[\"blockwishlist\",\"displayTop\"],[\"blockcurrencies\",\"displayNav\"],[\"blocklanguages\",\"displayNav\"],[\"fashionblockuserinfo\",\"displayNav\"],[\"blockcart\",\"displayTop\"],[\"blockhtml\",\"displayTop\"]]',	1),
                (4,	'displayTopColumn',	'[[\"imagesearchblock\",\"displayTop\"],[\"advancetopmenu\",\"displayTop\"],[\"categoryslider\",\"displayCategorySlider\"]]',	1),
                (4,	'displayHomeTopColumn',	'[[\"homeslider\",\"displayTopColumn\"]]',	1),
                (4,	'displayHome',	'[[\"blockhtml\",\"displayHome\"],[\"discountproducts\",\"displayHome\"],[\"flexiblecustom\",\"displayFlexibleCategory\"],[\"ovicblocknewsletter\",\"HOOK_NEWSLETTER\"],[\"flexiblecustom\",\"displayHome\"]]',	1),
                (4,	'displayHomeTab',	'[]',	1),
                (4,	'displayHomeTabContent',	'[]',	1),
                (4,	'displayFooter',	'[[\"blocklayered\",\"displayFooter\"],[\"statsdata\",\"displayFooter\"],[\"themeconfigurator\",\"displayFooter\"],[\"scrolltop\",\"displayFooter\"],[\"advancefooter\",\"displayFooter\"],[\"blockhtml\",\"displayFooter\"]]',	1),
                (4,	'displayBeforeLogo',	'[[\"blockcontact\",\"displayNav\"]]',	1),
                (4,	'displayHeader',	'[[\"flexiblecustom\",\"displayHeader\"],[\"blockhtml\",\"displayHeader\"],[\"ovicblocknewsletter\",\"displayHeader\"],[\"ovicnewsletter\",\"displayHeader\"]]',	1),
                (4,	'displayFlexibleCategory',	'[[\"flexiblecustom\",\"displayFlexibleCategory\"]]',	1),
                (4,	'displayBackOfficeHeader',	'[[\"blockhtml\",\"displayBackOfficeHeader\"]]',	1),
                (4,	'displayLeftColumn',	'[[\"blockhtml\",\"displayLeftColumn\"]]',	1),
                (4,	'displayRightColumn',	'[[\"blockhtml\",\"displayRightColumn\"]]',	1),
                (4,	'CustomHtml',	'[[\"blockhtml\",\"CustomHtml\"]]',	1),
                (4,	'Contactform',	'[[\"blockhtml\",\"Contactform\"]]',	1),
                (4,	'HOOK_NEWSLETTER',	'[[\"ovicblocknewsletter\",\"HOOK_NEWSLETTER\"]]',	1),
                (5, 'displayNav',   '[[\"ovicnewsletter\",\"displayFooter\"]]', 1),
                (5, 'displayTop',   '[[\"pagesnotfound\",\"displayTop\"],[\"sekeywords\",\"displayTop\"],[\"blockwishlist\",\"displayTop\"],[\"blockcurrencies\",\"displayNav\"],[\"blocklanguages\",\"displayNav\"],[\"fashionblockuserinfo\",\"displayNav\"],[\"blockcart\",\"displayTop\"],[\"imagesearchblock\",\"displayTop\"],[\"blockcontact\",\"displayNav\"]]',    1),
                (5, 'displayTopColumn', '[[\"advancetopmenu\",\"displayTop\"],[\"categoryslider\",\"displayCategorySlider\"]]', 1),
                (5, 'displayHomeTopColumn', '[[\"homeslider\",\"displayTopColumn\"],[\"blockhtml\",\"displayTopColumn\"],[\"simplecategory\",\"displayHomeTopColumn\"]]',   1),
                (5, 'displayHome',  '[[\"ovicparallaxblock\",\"displayHome\"],[\"flexiblecustom\",\"displayHome\"],[\"ovicblocknewsletter\",\"HOOK_NEWSLETTER\"],[\"blockhtml\",\"displayHomeBottomContent\"]]',    1),
                (5, 'displayHomeTab',   '[[\"ovicblocknewproducts\",\"displayHomeTab\"],[\"ovichomefeatured\",\"displayHomeTab\"],[\"ovicblockbestsellers\",\"displayHomeTab\"]]',  1),
                (5, 'displayHomeTabContent',    '[[\"ovicblocknewproducts\",\"displayHomeTabContent\"],[\"ovichomefeatured\",\"displayHomeTabContent\"],[\"ovicblockbestsellers\",\"displayHomeTabContent\"]]', 1),
                (5, 'displayFooter',    '[[\"blocklayered\",\"displayFooter\"],[\"statsdata\",\"displayFooter\"],[\"scrolltop\",\"displayFooter\"],[\"advancefooter\",\"displayFooter\"]]', 1),
                (5, 'displayHomeBottomContent', '[[\"simplecategory\",\"displayHomeBottomContent\"]]',  1),
                (5, 'displayHomeTopContent',    '[[\"simplecategory\",\"displayHomeTopContent\"]]', 1),
                (5, 'displayHomeBottomColumn',  '[[\"blockhtml\",\"displayHomeBottomColumn\"],[\"smartbloghomelatestnews\",\"displayHome\"]]',  1),
                (5, 'displayParalax1',  '[[\"ovicparallaxblock\",\"displayParalax1\"]]',    1),
                (5, 'displayParalax2',  '[[\"ovicparallaxblock\",\"displayParalax2\"]]',    1),
                (5, 'displayParalax3',  '[[\"ovicparallaxblock\",\"displayParalax3\"]]',    1),
                (5, 'displayParalax4',  '[[\"ovicparallaxblock\",\"displayParalax4\"]]',    1),
                (5, 'displayParalax5',  '[[\"ovicparallaxblock\",\"displayParalax5\"]]',    1),
                (5, 'displayParalax6',  '[[\"ovicparallaxblock\",\"displayParalax6\"]]',    1),
                (5, 'displayLeftColumn',    '[[\"simplecategory\",\"displayLeftColumn\"]]', 1),
                (5, 'displayRightColumn',   '[[\"simplecategory\",\"displayRightColumn\"]]',    1),
                (5, 'displaySmartBlogLeft', '[[\"simplecategory\",\"displaySmartBlogLeft\"]]',  1),
                (5, 'displaySmartBlogRight',    '[[\"simplecategory\",\"displaySmartBlogRight\"]]', 1),
                (5, 'displaySimpleCategory',    '[[\"simplecategory\",\"displaySimpleCategory\"]]', 1),
                (5, 'displaySimpleCategory1',   '[[\"simplecategory\",\"displaySimpleCategory1\"]]',    1),
                (5, 'displaySimpleCategory2',   '[[\"simplecategory\",\"displaySimpleCategory2\"]]',    1),
                (5, 'displaySimpleCategory3',   '[[\"simplecategory\",\"displaySimpleCategory3\"]]',    1),
                (5, 'displaySimpleCategory4',   '[[\"simplecategory\",\"displaySimpleCategory4\"]]',    1),
                (5, 'displaySimpleCategory5',   '[[\"simplecategory\",\"displaySimpleCategory5\"]]',    1),
                (5, 'displaySocialSharing', '[[\"socialsharing\",\"displaySocialSharing\"]]',   1);";
        $result &= Db::getInstance()->execute($sql);
        $sql = "INSERT INTO `" . _DB_PREFIX_ .
            "ovic_options_sidebar` (`page`, `left`, `right`, `id_shop`) VALUES
                ('best-sales',	'[[\"blockcategories\",\"displayLeftColumn\"],[\"themeconfigurator\",\"displayLeftColumn\"],[\"ovicblockspecials\",\"displayLeftColumn\"],[\"blockhtml\",\"displayLeftColumn\"],[\"blockhtml\",\"displayLeftColumn\"]]',	NULL,	1),
                ('category',	'[[\"blockcategories\",\"displayLeftColumn\"],[\"blocklayered\",\"displayLeftColumn\"],[\"ovicblockspecials\",\"displayLeftColumn\"],[\"themeconfigurator\",\"displayLeftColumn\"],[\"blockhtml\",\"displayLeftColumn\"],[\"blockhtml\",\"displayLeftColumn\"]]',	NULL,	1),
                ('discount',	'[[\"blockcategories\",\"displayLeftColumn\"],[\"blocklayered\",\"displayLeftColumn\"],[\"blockcms\",\"displayLeftColumn\"],[\"blockmanufacturer\",\"displayLeftColumn\"],[\"blockmyaccount\",\"displayLeftColumn\"],[\"blockpaymentlogo\",\"displayLeftColumn\"],[\"blockstore\",\"displayLeftColumn\"],[\"blocksupplier\",\"displayLeftColumn\"],[\"blocktags\",\"displayLeftColumn\"],[\"blockviewed\",\"displayLeftColumn\"],[\"themeconfigurator\",\"displayLeftColumn\"],[\"ovicblockspecials\",\"displayLeftColumn\"],[\"blocklink\",\"displayLeftColumn\"],[\"smartblogcategories\",\"displayLeftColumn\"],[\"smartblogarchive\",\"displayLeftColumn\"],[\"blockhtml\",\"displayLeftColumn\"],[\"blockhtml\",\"displayLeftColumn\"]]',	NULL,	1),
                ('index',	'[[\"blockcategories\",\"displayLeftColumn\"],[\"blocklayered\",\"displayLeftColumn\"],[\"blockcms\",\"displayLeftColumn\"],[\"blockmanufacturer\",\"displayLeftColumn\"],[\"blockmyaccount\",\"displayLeftColumn\"],[\"blockpaymentlogo\",\"displayLeftColumn\"],[\"blockstore\",\"displayLeftColumn\"],[\"blocksupplier\",\"displayLeftColumn\"],[\"blocktags\",\"displayLeftColumn\"],[\"blockviewed\",\"displayLeftColumn\"],[\"themeconfigurator\",\"displayLeftColumn\"],[\"ovicblocknewproducts\",\"displayLeftColumn\"],[\"ovicblockbestsellers\",\"displayLeftColumn\"],[\"ovicblockspecials\",\"displayLeftColumn\"],[\"blocklink\",\"displayLeftColumn\"],[\"smartblogcategories\",\"displayLeftColumn\"],[\"smartblogarchive\",\"displayLeftColumn\"]]',	NULL,	1),
                ('manufacturer',	'[[\"blockcms\",\"displayLeftColumn\"],[\"blockmyaccount\",\"displayLeftColumn\"],[\"blockviewed\",\"displayLeftColumn\"],[\"ovicblockspecials\",\"displayLeftColumn\"],[\"themeconfigurator\",\"displayLeftColumn\"],[\"blockhtml\",\"displayLeftColumn\"],[\"blockhtml\",\"displayLeftColumn\"]]',	NULL,	1),
                ('module-smartblog-category',	NULL,	'[[\"themeconfigurator\",\"displayRightColumn\"],[\"blockwishlist\",\"displayRightColumn\"],[\"blocktestimonial\",\"displayRightColumn\"],[\"ovicblockspecials\",\"displayRightColumn\"],[\"ovicblockbanner\",\"displayRightColumn\"]]',	1),
                ('module-smartblog-details',	NULL,	'[[\"themeconfigurator\",\"displayRightColumn\"],[\"blockwishlist\",\"displayRightColumn\"],[\"blocktestimonial\",\"displayRightColumn\"],[\"ovicblockspecials\",\"displayRightColumn\"],[\"ovicblockbanner\",\"displayRightColumn\"]]',	1),
                ('new-products',	'[[\"blockcategories\",\"displayLeftColumn\"],[\"blocklayered\",\"displayLeftColumn\"],[\"blockcms\",\"displayLeftColumn\"],[\"blockmyaccount\",\"displayLeftColumn\"],[\"blockstore\",\"displayLeftColumn\"],[\"blockviewed\",\"displayLeftColumn\"],[\"ovicblockspecials\",\"displayLeftColumn\"],[\"themeconfigurator\",\"displayLeftColumn\"],[\"blockhtml\",\"displayLeftColumn\"],[\"blockhtml\",\"displayLeftColumn\"]]',	NULL,	1),
                ('prices-drop',	'[[\"blockcategories\",\"displayLeftColumn\"],[\"blocklayered\",\"displayLeftColumn\"],[\"blockcms\",\"displayLeftColumn\"],[\"blockmanufacturer\",\"displayLeftColumn\"],[\"blockmyaccount\",\"displayLeftColumn\"],[\"blockpaymentlogo\",\"displayLeftColumn\"],[\"blockstore\",\"displayLeftColumn\"],[\"blocktags\",\"displayLeftColumn\"],[\"blockviewed\",\"displayLeftColumn\"],[\"themeconfigurator\",\"displayLeftColumn\"],[\"ovicblockspecials\",\"displayLeftColumn\"],[\"blockhtml\",\"displayLeftColumn\"],[\"blockhtml\",\"displayLeftColumn\"]]',	NULL,	1),
                ('search',	'[[\"blockcategories\",\"displayLeftColumn\"],[\"blocklayered\",\"displayLeftColumn\"],[\"blockcms\",\"displayLeftColumn\"],[\"blockmanufacturer\",\"displayLeftColumn\"],[\"blockmyaccount\",\"displayLeftColumn\"],[\"blockpaymentlogo\",\"displayLeftColumn\"],[\"blockstore\",\"displayLeftColumn\"],[\"blocksupplier\",\"displayLeftColumn\"],[\"blocktags\",\"displayLeftColumn\"],[\"blockviewed\",\"displayLeftColumn\"],[\"themeconfigurator\",\"displayLeftColumn\"],[\"ovicblocknewproducts\",\"displayLeftColumn\"],[\"ovicblockbestsellers\",\"displayLeftColumn\"],[\"ovicblockspecials\",\"displayLeftColumn\"],[\"blocklink\",\"displayLeftColumn\"],[\"smartblogcategories\",\"displayLeftColumn\"],[\"smartblogarchive\",\"displayLeftColumn\"],[\"blockhtml\",\"displayLeftColumn\"],[\"blockhtml\",\"displayLeftColumn\"]]',	NULL,	1);";
        $result &= Db::getInstance()->execute($sql);
        return $result;
    }
    public function uninstall(){
        if (!$this->backupAllModulesHook('ovic_backup_hook_module','hook_module'))
            return false;
        if (!parent::uninstall() || !$this->uninstallDB())
            return false;
        //Configuration::deleteByName('OVIC_FONT_LINK');
//        Configuration::deleteByName('OVIC_MAIN_COLOR');
//        Configuration::deleteByName('OVIC_BTN_COLOR');
//        Configuration::deleteByName('OVIC_BTN_HOVER_COLOR');
//        Configuration::deleteByName('OVIC_BTN_TEXT_COLOR');
//        Configuration::deleteByName('OVIC_BTN_TEXT_HOVER_COLOR');
//        Configuration::deleteByName('OVIC_CURRENT_OPTION');
//        Configuration::deleteByName('OVIC_LAYOUT_COLUMN');
        $classNames = array('AdminThemeConfig','AdminLayoutSetting');
        foreach ($classNames as $className){
            $idTab = Tab::getIdFromClassName($className);
            if ($idTab != 0){
                $tab = new Tab($idTab);
                $tab->delete();
            }
        }
        return true;
    }
    private function uninstallDB(){
        $results = Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'ovic_backup_hook_module`');
        $results = Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'ovic_options`');
        $results = Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'ovic_options_lang`');
        $results = Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'ovic_options_hook_module`');
        $results = Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'ovic_options_sidebar`');
        return $results;
    }
    /**
     * get all hookexecute from tbl hook
     * */
    public static function getHookexecuteList(){
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
        if ($results && is_array($results) && sizeof($results)){
            foreach ($results as $result){
                $hooklist[] = $result['name'];
            }
        }
        return array_values($hooklist);
    }
    public static function isAvailablebyId($id_option){
        $sql = 'SELECT COUNT(`id_option`) FROM `' . _DB_PREFIX_ . 'ovic_options` WHERE `id_option` = '.(int)$id_option;
        return Db::getInstance()->getValue($sql);
    }
    public static function copyHookModule($source_option,$destination_option){
         if ($source_option && Validate::isUnsignedId($source_option) && $destination_option && Validate::isUnsignedId($destination_option)){
            $return = true;
            $optionTheme = new Options($destination_option);
            $displayLeft = false;
            $displayRight = false;
            if (substr_count($optionTheme->column,'1')>0 || substr_count($optionTheme->column,'0')>0)
                $displayLeft = true;
            if (substr_count($optionTheme->column,'2')>0 || substr_count($optionTheme->column,'0')>0)
                $displayRight = true;
            foreach (OvicLayoutControl::$OptionHookAssign as $hookname){
                if ($hookname == 'displayLeftColumn' && !$displayLeft)
                    continue;
                if ($hookname == 'displayRightColumn' && !$displayRight)
                    continue;
                $sourceoptionModules = OvicLayoutControl::getModulesHook($source_option, $hookname);
                if ($sourceoptionModules && is_array($sourceoptionModules) && count($sourceoptionModules)>0){
                    $return &= Db::getInstance()->insert('ovic_options_hook_module', array(
                        'id_option' => (int)$destination_option,
                        'hookname' => $sourceoptionModules['hookname'],
            			'modules' => $sourceoptionModules['modules'],
            			'id_shop' => $sourceoptionModules['id_shop'],
            		));
                }
            }
         }else
            $return = false;
         return $return;
    }
    private function backupModuleHook($idHook = 0,$source, $destination, $removeSource = true){
        if (!Validate::isUnsignedId($idHook)){
            return false;
        }
        $id_shop = (int)$this->context->shop->id;
        $modules = Db::getInstance()->ExecuteS('
            SELECT *
            FROM `' . _DB_PREFIX_ . $source.'`
            WHERE `id_hook` = ' . (int)$idHook.' AND `id_shop` = ' .$id_shop);
        $return = true;
        if ($modules && count($modules)>0)
            foreach ($modules as $module){
                $moduleObject = Module::getInstanceById($module['id_module']);
                if ($moduleObject && Validate::isModuleName($moduleObject->name) && $moduleObject->name != $this->name){
                    // Check if already register
    				$sql = 'SELECT bhm.`id_module`
    					FROM `'._DB_PREFIX_.$destination.'` bhm, `'._DB_PREFIX_.'hook` h
    					WHERE bhm.`id_module` = '.(int)($module['id_module']).' AND h.`id_hook` = '.$idHook.'
    					AND h.`id_hook` = bhm.`id_hook` AND `id_shop` = '.(int)$module['id_shop'];
    				if (Db::getInstance()->getRow($sql))
                        continue;
                    // Get module position in hook
    				$sql = 'SELECT MAX(`position`) AS position
    					FROM `'._DB_PREFIX_.$destination.'`
    					WHERE `id_hook` = '.(int)$idHook.' AND `id_shop` = '.(int)$module['id_shop'];
    				if (!$position = Db::getInstance()->getValue($sql))
    					$position = 0;
    				// Register module in hook
    				$return &= Db::getInstance()->insert($destination, array(
    					'id_module' => (int)$module['id_module'],
    					'id_hook' => (int)$idHook,
    					'id_shop' => (int)$module['id_shop'],
    					'position' => (int)($position + 1),
    				));
                    if ($removeSource == true){
                         // remove from default hook_module table
                        $where ="`id_module` = ".(int)$module['id_module']. " AND `id_hook` = ".(int)$idHook." AND `id_shop` = ".(int)$module['id_shop'];
                        $return &= Db::getInstance()->delete($source,$where);
                    }
                }
            }
        return $return;
    }
    public static function getModuleArrFromBackuptbl($id_hook,$getshop = false){
        $modulesArr = array();
        $modules = Db::getInstance()->ExecuteS('
                SELECT *
                FROM `' . _DB_PREFIX_ .'ovic_backup_hook_module`
                WHERE `id_hook` = ' .(int)$id_hook);
        $hookname = Hook::getNameById($id_hook);
        if ($modules && count($modules)>0){
            foreach ($modules as $module)
                {
                    $moduleObject = Module::getInstanceById((int)$module['id_module']);
                    if (!is_object($moduleObject) || !Validate::isModuleName($moduleObject->name))
                        continue;
                    $moduleHook = array();
                    $moduleHook[] = $moduleObject->name;
                    $moduleHook[] = $hookname;
                    if ($getshop)
                        $modulesArr[$module['id_shop']][] = $moduleHook;
                    else
                        $modulesArr[] = $moduleHook;
                }
        }
        return $modulesArr;
    }
    /**
	 * insert all default prestashop hook in to option database
	 */
    public static function registerDefaultHookModule($id_option){
        if ($id_option && Validate::isUnsignedId($id_option)){
            $return = true;
            $optionTheme = new Options($id_option);
            $displayLeft = false;
            $displayRight = false;
            if (substr_count($optionTheme->column,'1')>0 || substr_count($optionTheme->column,'0')>0)
                $displayLeft = true;
            if (substr_count($optionTheme->column,'2')>0 || substr_count($optionTheme->column,'0')>0)
                $displayRight = true;
            foreach (self::$OptionHookAssign as $hookname){
                if ($hookname == 'displayLeftColumn' && !$displayLeft)
                    continue;
                if ($hookname == 'displayRightColumn' && !$displayRight)
                    continue;
                $idHook = (int)Hook::getIdByName($hookname);
                $modulesHook = self::getModuleArrFromBackuptbl($idHook,true);
                if ($modulesHook && count($modulesHook)>0)
                    foreach ($modulesHook as $key => $moduleHook){
                        $return &= OvicLayoutControl::registerHookModule($id_option,$hookname,Tools::jsonEncode($moduleHook),$key);
                    }
            }
        }else{
            $return =false;
        }
        return $return;
    }
    public function backupAllModulesHook($source = '', $destination =''){
        if (strlen($source) == 0 || strlen($destination) == 0){
            return false;
        }
        $return = true;
        foreach (self::getHookexecuteList() as $hookname){
            $idHook = Hook::getIdByName($hookname);
            if ($hookname && Validate::isHookName($hookname)){
                if (in_array($hookname,self::$OptionHookAssign))
                    $return &= $this->backupModuleHook($idHook,$source,$destination,true);
                else
                    $return &= $this->backupModuleHook($idHook,$source,$destination,false);
            }
        }
        return $return;
    }
    /**
     * get sidebar modules if not isset sidebar
     * */
    public static function getDefaultSidebarModule($column){
        $curent_id_option = Configuration::get('OVIC_CURRENT_OPTION');
        $hookname = 'display'.ucfirst(trim($column)).'Column';
        $idHook = Hook::getIdByName($hookname);
        $sidebarModule = self::getModulesHook($curent_id_option,$hookname);
        if (!is_null($sidebarModule['modules']))
            $sidebarModules = Tools::jsonDecode($sidebarModule['modules'],true);
        else{
            $sidebarModules = self::getModuleArrFromBackuptbl($idHook);
        }
        return $sidebarModules;
    }
    /**
     * get sidebar modules by page
     * */
    public static function getSideBarModulesByPage($pagename, $column, $full = true ){
        $context = Context::getContext();
        $id_shop = $context->shop->id;
        $curent_id_option = Configuration::get('OVIC_CURRENT_OPTION');
        $sql ="SELECT `".$column."` FROM `"._DB_PREFIX_."ovic_options_sidebar` WHERE `page` ='".$pagename."' AND `id_shop`=".(int)$id_shop;
        $sidebarModule = Db::getInstance()->getRow($sql);
        if ($sidebarModule == false || !count($sidebarModule) || empty($sidebarModule) === true){
            $sidebarModules = self::getDefaultSidebarModule($column);
            self::registerSidebarModule($pagename,$column,Tools::jsonEncode($sidebarModules),$id_shop);
        }else{
            $sidebarModules = Tools::jsonDecode($sidebarModule[$column],true);
        }
        if ($sidebarModules && is_array($sidebarModules) && sizeof($sidebarModules)>0){
            if (!$full){
                return $sidebarModules;
            }
            $results = array();
            foreach ($sidebarModules as $sidebarModule){
                if ($sidebarModule[1] == 'displayLeftColumn' || $sidebarModule[1] == 'displayRightColumn'){
                    $moduleObject = Module::getInstanceByName($sidebarModule[0]);
                    $id_hookexecute = (int)Hook::getIdByName($sidebarModule[1]);
                    $module = array();
                    $module['id'] = $moduleObject->id;
                    $module['version'] = $moduleObject->version;
                    $module['name'] = $moduleObject->name;
                    $module['displayName'] = $moduleObject->displayName;
                    $module['description'] = $moduleObject->description;
                    $module['id_hookexecute'] = $id_hookexecute;
                    $module['hookexec_name'] = $sidebarModule[1];
                    $module['tab'] = $moduleObject->tab;
                    $module['active'] = $moduleObject->active;
                    $results[] = $module;
                }
            }
            return $results;
        }else{
            return false;
        }
    }
    /**
    * insert or update list module of column
    */
    public static function registerSidebarModule($pagename,$column,$moduleHook,$idshop){
        // Check if already register
        //SELECT * FROM `ps_ovic_options_sidebar` WHERE `page` ='' AND `id_shop` =1
        $sql ="SELECT * FROM `"._DB_PREFIX_."ovic_options_sidebar` WHERE `page` ='".$pagename."' AND `id_shop`=".(int)$idshop;
		if (Db::getInstance()->getRow($sql)){
            $setArr[$column] = $moduleHook;
            $where = "`page` ='".$pagename."' AND `id_shop` = ".(int)$idshop;
            return Db::getInstance()->update('ovic_options_sidebar',$setArr,$where);
		}else{
            // Register module in hook
            $insertArr['page'] = $pagename;
            $insertArr[$column] = $moduleHook;
            $insertArr['id_shop'] = (int)$idshop;
    		return Db::getInstance()->insert('ovic_options_sidebar', $insertArr);
		}
    }
    /**
	 * get all module hooked into $id_hook
	 */
    public static function getModulesHook($id_option, $hookname){
        if ($hookname && Validate::isHookName($hookname) && $id_option && Validate::isUnsignedId($id_option)){
            $context = Context::getContext();
            $idshop = $context->shop->id;
            $sql = 'SELECT *
			         FROM `'._DB_PREFIX_.'ovic_options_hook_module` ohm
                     WHERE ohm.`id_option` = '.(int)($id_option).' AND ohm.`hookname` = \''.$hookname.'\'
			         AND `id_shop` = '.(int)$idshop;
            $result = Db::getInstance()->getRow($sql);
            if ($result == false || !count($result) || empty($result) === true)
    			return false;
            return $result;
        }else{
            return false;
        }
    }
    /**
	 * get all hook off modules. return hook <option> string
	 */
    public static function getHookOptionByModule($selectedModule, $hookName, $moduleObject,$selected=null, $sideBar = false)
    {
        $html = '';
        $hooks = self::getHooksByModule($moduleObject);
        if (count($hooks) > 0)
        {
            foreach ($hooks as $h)
            {
                $moduleHook = array();
                $moduleHook[] = $moduleObject->name;
                $moduleHook[] = $h['name'];
                $disableOption = false;
                $key = array_search($moduleHook,$selectedModule);
                if ($key && array_key_exists($key,$selectedModule)){
                   $disableOption = true;
                }
                if ($hookName == 'displayHomeTab'){
                    if ($h['name'] == $hookName){
                        $html .= '<option '. ($disableOption? 'disabled':''). ($selected == $h['id_hook'] ? 'selected="selected"' : '') . ' value="' . $h['id_hook'] .
                    '">' . $h['name'] . '</option>';
                        break;
                    }
                }elseif ($hookName == 'displayHomeTabContent'){
                    if ($h['name'] == $hookName){
                        $html .= '<option '. ($disableOption? 'disabled':''). ($selected == $h['id_hook'] ? 'selected="selected"' : '') . ' value="' . $h['id_hook'] .
                    '">' . $h['name'] . '</option>';
                        break;
                    }
                }else{
                    if ($sideBar){
                        if ($h['name'] == 'displayLeftColumn' || $h['name'] == 'displayRightColumn'){
                            $html .= '<option '. ($disableOption? 'disabled':''). ($selected == $h['id_hook'] ? ' selected="selected"' : '') . ' value="' . $h['id_hook'] .
                            '">' . $h['name'] . '</option>';
                        }
                    }else{
                        if ($h['name'] != 'displayHomeTab' && $h['name'] != 'displayHomeTabContent')
                            $html .= '<option '. ($disableOption? 'disabled':''). ($selected == $h['id_hook'] ? ' selected="selected"' : '') . ' value="' . $h['id_hook'] .
                            '">' . $h['name'] . '</option>';
                    }
                }
            }
        }
        return $html;
    }
    private function echoArr($array,$die = false){
        echo "<pre>";
        print_r($array);
        echo "</pre>";
        if ($die)
            die();
    }
    /************* get modules hooked in all hooks for an option ***************/
    public static function getOptionModulesHook($id_option){
        if ($id_option && Validate::isUnsignedId($id_option)){
             $optionModulesHook = array();
             foreach (self::$OptionHookAssign as $hookname){
                $idHook = (int)Hook::getIdByName($hookname);
                $optionModules = self::getModulesHook($id_option, $hookname);
                if (!is_null($optionModules['modules']))
                    $optionModules = Tools::jsonDecode($optionModules['modules'],true);
                else
                    $optionModules = array();
                $moduleObjects = array();
                if ($optionModules && is_array($optionModules) && sizeof($optionModules)>0){
                    foreach ($optionModules as $optionModule){
                        $moduleObject = Module::getInstanceByName($optionModule[0]);
                        if ($moduleObject && Validate::isModuleName($moduleObject->name)){
                            $id_hookexecute = (int)Hook::getIdByName($optionModule[1]);
                            $module = array();
                            $module['id'] = $moduleObject->id;
                            $module['version'] = $moduleObject->version;
                            $module['name'] = $moduleObject->name;
                            $module['displayName'] = $moduleObject->displayName;
                            $module['description'] = $moduleObject->description;
                            $module['id_hookexecute'] = $id_hookexecute;
                            $module['hookexec_name'] = $optionModule[1];
                            $module['tab'] = $moduleObject->tab;
                            $module['active'] = $moduleObject->active;
                            $moduleObjects[] = $module;
                        }
                    }
                }
                $ModulesHook = array();
                $ModulesHook['id_hook'] = $idHook;
                $ModulesHook['modules'] =  $moduleObjects;
                $optionModulesHook[$hookname] = $ModulesHook;
            }
            return $optionModulesHook;
        }else{
            return false;
        }
    }
    /********************** get all module can execusive ***********************/
    public static function getModuleExecList($hookname = null){
        $ModuleExecList = array();
        if (is_null($hookname)){
            $hookArr =self::getHookexecuteList() ;
        }elseif (is_array($hookname)){
            $hookArr = $hookname;
        }elseif (strlen($hookname) > 0){
            $hookArr = array($hookname);
        }
        $moduleArr = array();
        foreach ($hookArr as $hookname){
            $ModuleList = HookManager::getHookModuleExecList($hookname);
            if ($ModuleList && count($ModuleList)>0)
                foreach ($ModuleList as $moduleObj){
                    if (array_key_exists($moduleObj['id_module'],$moduleArr)){
                        continue;
                    }
                    $moduleArr[$moduleObj['id_module']] = 1;
                    $ModuleExecList[] = $moduleObj;
                }
        }
        return $ModuleExecList;
    }
    public static function getHooksByModule($moduleObject)
    {
        $hooks = array();
        $hookArr = self::getHookexecuteList();
        if ($hookArr)
        {
            foreach ($hookArr as $hook)
            {
                if (_PS_VERSION_ < "1.5")
                {
                    if (is_callable(array($moduleObject, 'hook' . $hook)))
                    {
                        $hooks[] = $hook;
                    }
                }
                else
                {
                    $retro_hook_name = Hook::getRetroHookName($hook);
                    if (is_callable(array($moduleObject, 'hook' . $hook)) || is_callable(array($moduleObject, 'hook' .
                            $retro_hook_name)))
                    {
                        $hooks[] = $hook;
                    }
                }
            }
        }
        $results = self::getHookByArrName($hooks);
        return $results;
    }
    /**
    * insert or update list module hook
    */
    public static function registerHookModule($id_option,$hookname,$moduleHook,$idshop){
        // Check if already register
		$sql = 'SELECT ohm.`hookname`
			FROM `'._DB_PREFIX_.'ovic_options_hook_module` ohm 
			 WHERE ohm.`id_option` = '.(int)($id_option).' AND ohm.`hookname` = \''.$hookname.'\'
			  AND `id_shop` = '.(int)$idshop;
		if (Db::getInstance()->getRow($sql)){
            $where = "`id_option` = ".(int)$id_option. " AND `hookname` = '".$hookname."' AND `id_shop` = ".(int)$idshop;
            return Db::getInstance()->update('ovic_options_hook_module',array(
                'modules' => $moduleHook
            ),$where);
		}else{
            // Register module in hook
    		return Db::getInstance()->insert('ovic_options_hook_module', array(
                'id_option' => (int)$id_option,
                'hookname' => $hookname,
    			'modules' => $moduleHook,
    			'id_shop' => (int)$idshop,
    		));
		}
    }
    public static function getHookByArrName($arrName)
    {
        $result = Db::getInstance()->ExecuteS('
            SELECT `id_hook`, `name`
            FROM `' . _DB_PREFIX_ . 'hook`
            WHERE `name` IN (\'' . implode("','", $arrName) . '\')');
        return $result;
    }
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->_path . 'css/themeconfig.css');
    }
    private function generalHook($hookname)
    {
        if (!Validate::isHookName($hookname))
            return '';
        $html = '';
        $layoutColumn = (int)Configuration::get('OVIC_LAYOUT_COLUMN');
        $curent_id_option = Configuration::get('OVIC_CURRENT_OPTION');
        if ($hookname == 'displayLeftColumn' || $hookname == 'displayRightColumn'){
            $module_name = '';
    		if (Validate::isModuleName(Tools::getValue('module')))
    			$module_name = Tools::getValue('module');
            if (!empty($this->context->controller->page_name)){
                $page_name =$this->context->controller->page_name;
            }
            elseif (!empty($this->context->controller->php_self)){
                $page_name = $this->context->controller->php_self;
            }
            elseif (Tools::getValue('fc') == 'module' && $module_name != '' && (Module::
                getInstanceByName($module_name) instanceof PaymentModule))
                $page_name = 'module-payment-submit';
            // @retrocompatibility Are we in a module ?
            elseif (preg_match('#^' . preg_quote($this->context->shop->physical_uri, '#') .
                'modules/([a-zA-Z0-9_-]+?)/(.*)$#', $_SERVER['REQUEST_URI'], $m)){
                    $page_name = 'module-' . $m[1] . '-' . str_replace(array('.php', '/'), array('',
                        '-'), $m[2]);
                }
            else {
                $page_name = Dispatcher::getInstance()->getController();
                $page_name = (preg_match('/^[0-9]/', $page_name) ? 'page_' . $page_name : $page_name);
            }
            if (strlen($page_name) <= 0)
                return '';
        }
        if ($hookname == 'displayLeftColumn')
        {
            if ($page_name == 'index' && $layoutColumn>1)
                return '';
            if ($page_name == 'index'){
                $optionModules = self::getModulesHook($curent_id_option, $hookname);
                if (!is_null($optionModules['modules']))
                    $optionModules = Tools::jsonDecode($optionModules['modules'],true);
                else
                    $optionModules = array();
            }else{
                $optionModules = self::getSideBarModulesByPage($page_name,'left',false);
            }
            if ($optionModules && is_array($optionModules) && sizeof($optionModules)>0){
                foreach ($optionModules as $optionModule){
                    $moduleObject = Module::getInstanceByName($optionModule[0]);
                    $html .= $this->ModuleHookExec($moduleObject, $optionModule[1]);
                }
            }
            return $html;
        }
        if ($hookname == 'displayRightColumn'){
            if ($page_name == 'index' && $layoutColumn !== 0 && $layoutColumn !==2)
                return '';
            if ($page_name == 'index'){
                $optionModules = self::getModulesHook($curent_id_option, $hookname);
                if (!is_null($optionModules['modules']))
                    $optionModules = Tools::jsonDecode($optionModules['modules'],true);
                else
                    $optionModules = array();
            }else{
                $optionModules = self::getSideBarModulesByPage($page_name,'right',false);
            }
            if ($optionModules && is_array($optionModules) && sizeof($optionModules)>0){
                foreach ($optionModules as $optionModule){
                    $moduleObject = Module::getInstanceByName($optionModule[0]);
                    $html .= $this->ModuleHookExec($moduleObject, $optionModule[1]);
                }
            }
            return $html;
        }
        $optionModules = self::getModulesHook($curent_id_option, $hookname);
        if (!is_null($optionModules['modules']))
            $optionModules = Tools::jsonDecode($optionModules['modules'],true);
        else
            $optionModules = array();
        if ($optionModules && is_array($optionModules) && sizeof($optionModules)>0){
            foreach ($optionModules as $optionModule){
                $moduleObject = Module::getInstanceByName($optionModule[0]);
                $html .= $this->ModuleHookExec($moduleObject, $optionModule[1]);
            }
        }
        return $html;
    }
    private function getIdThemeMetaByPage($page=null){
        return Db::getInstance()->getValue(
			'SELECT id_theme_meta
				FROM '._DB_PREFIX_.'theme_meta tm
				LEFT JOIN '._DB_PREFIX_.'meta m ON ( m.id_meta = tm.id_meta )
				WHERE m.page = "'.pSQL($page).'" AND tm.id_theme='.(int)$this->context->shop->id_theme
		);
    }
    private function ProcessLayoutColumn(){
        $theme = new Theme((int)$this->context->shop->id_theme);
        $layoutColumn = (int)Configuration::get('OVIC_LAYOUT_COLUMN');
        $id_theme_meta = $this->getIdThemeMetaByPage('index');
        if ($theme->hasLeftColumn('index')){
            if ($layoutColumn === 2 || $layoutColumn === 3)
                $this->processLeftMeta($id_theme_meta);
        }else{
            if ($layoutColumn === 0 || $layoutColumn === 1)
                $this->processLeftMeta($id_theme_meta);
        }
        if ($theme->hasRightColumn('index')){
            if ($layoutColumn === 1 || $layoutColumn === 3)
                $this->processRightMeta($id_theme_meta);
        }else{
            if ($layoutColumn === 0 || $layoutColumn === 2)
                $this->processRightMeta($id_theme_meta);
        }
        Tools::clearCache();
    }
    private function processLeftMeta($id_theme_meta)
	{
		$theme_meta = Db::getInstance()->getRow(
			'SELECT * FROM '._DB_PREFIX_.'theme_meta WHERE id_theme_meta = '.(int)$id_theme_meta
		);
		$result = false;
		if ($theme_meta)
		{
			$sql = 'UPDATE '._DB_PREFIX_.'theme_meta SET left_column='.(int)!(bool)$theme_meta['left_column'].' WHERE id_theme_meta='.(int)$id_theme_meta;
			$result = Db::getInstance()->execute($sql);
		}
        return $result;
    }
    private function processRightMeta($id_theme_meta)
	{
		$theme_meta = Db::getInstance()->getRow(
			'SELECT * FROM '._DB_PREFIX_.'theme_meta WHERE id_theme_meta = '.(int)$id_theme_meta
		);
		$result = false;
		if ($theme_meta)
		{
			$sql = 'UPDATE '._DB_PREFIX_.'theme_meta SET right_column='.(int)!(bool)$theme_meta['right_column'].' WHERE id_theme_meta='.(int)$id_theme_meta;
			$result = Db::getInstance()->execute($sql);
		}
        return $result;
    }
    
    /**
	 * Execute modules for specified hook
	 * @param module $moduleInstance Execute hook for this module only
	 * @param string $hook_name Hook Name
	 * @return string modules output
	 */
     private function ModuleHookExec($moduleInstance, $hook_name){
        $output ='';
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
			
			//$exceptions = $moduleInstance->getExceptions($id_hook);
            $exceptions = array();

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
    
    private function hex2rgba($hex) {
       $hex = str_replace("#", "", $hex);
       if(strlen($hex) == 3) {
          $r = hexdec(substr($hex,0,1).substr($hex,0,1));
          $g = hexdec(substr($hex,1,1).substr($hex,1,1));
          $b = hexdec(substr($hex,2,1).substr($hex,2,1));
       } else {
          $r = hexdec(substr($hex,0,2));
          $g = hexdec(substr($hex,2,2));
          $b = hexdec(substr($hex,4,2));
       }
       $rgba = 'rgba('.$r.','.$g.','.$b.',0.8)';
       return $rgba;
    }
    public function hookactionModuleRegisterHookAfter($params){
        $module = $params['object'];
        if ($module->name != $this->name){
            $hook_name = $params['hook_name'];
            if ($hook_name && Validate::isHookName($hook_name)){
                $id_hook = Hook::getIdByName($hook_name);
                $hook_name = Hook::getNameById($id_hook);// get full hookname
                $id_hook_header = Hook::getIdByName('Header');
                if ($id_hook && $id_hook === $id_hook_header){
                    $this->changeHeaderPosition();
                }
                if (in_array($hook_name,self::$OptionHookAssign)){
                    $this->backupModuleHook($id_hook,'hook_module','ovic_backup_hook_module',true);
                }elseif (in_array($hook_name,self::getHookexecuteList())){
                    $this->backupModuleHook($id_hook,'hook_module','ovic_backup_hook_module',false);
                }else{
                    return;
                }
                $id_shop = (int)$this->context->shop->id;
                $current_id_option = Configuration::get('OVIC_CURRENT_OPTION');
                $moduleHook = array();
                $moduleHook[] = $module->name;
                $moduleHook[] = $hook_name;
                if ($current_id_option && Validate::isUnsignedId($current_id_option)){
                    //insert module to current option
                    $HookedModulesArr = self::getModulesHook($current_id_option, $hook_name);
                    $HookedModulesArr = Tools::jsonDecode($HookedModulesArr['modules'],true);
                    if (!is_array($HookedModulesArr)){
                        $HookedModulesArr = array();
                    }
                    $key = array_search($moduleHook,$HookedModulesArr);
                    if (!array_key_exists($key,$HookedModulesArr)){
                        $HookedModulesArr[] = $moduleHook;
                        self::registerHookModule($current_id_option,$hook_name,Tools::jsonEncode($HookedModulesArr),$id_shop);
                    }
                }
                $pagelist = Meta::getMetas();
                $sidebarPages = array();
                $theme = new Theme((int)$this->context->shop->id_theme);
                if ($hook_name == 'displayLeftColumn' || $hook_name == 'displayRightColumn'){
                    foreach ($pagelist as $page){
                        if ($hook_name == 'displayLeftColumn' && $theme->hasLeftColumn($page['page'])){
                            $HookedModulesArr = self::getSideBarModulesByPage($page['page'],'left',false);
                            if (!is_array($HookedModulesArr)){
                                $HookedModulesArr = array();
                            }
                            $key = array_search($moduleHook,$HookedModulesArr);
                            if (!array_key_exists($key,$HookedModulesArr)){
                            $HookedModulesArr[] = $moduleHook;
                                self::registerSidebarModule($page['page'],'left',Tools::jsonEncode($HookedModulesArr),$id_shop);
                            }
                        }
                        if ($hook_name == 'displayRightColumn' && $theme->hasRightColumn($page['page'])){
                            $HookedModulesArr = self::getSideBarModulesByPage($page['page'],'right',false);
                            if (!is_array($HookedModulesArr)){
                                $HookedModulesArr = array();
                            }
                            $key = array_search($moduleHook,$HookedModulesArr);
                            if (!array_key_exists($key,$HookedModulesArr)){
                            $HookedModulesArr[] = $moduleHook;
                                self::registerSidebarModule($page['page'],'right',Tools::jsonEncode($HookedModulesArr),$id_shop);
                            }
                        }
                    }
                }
            }
        }
    }
    private function changeHeaderPosition(){
        $id_shop = (int)$this->context->shop->id;
        $id_hook_header = Hook::getIdByName('Header');
        $sql = "SELECT MAX(`position`) FROM `" . _DB_PREFIX_ . "hook_module` WHERE id_shop = ".(int)$id_shop." AND id_hook =" .(int)$id_hook_header;
        $max_pos = Db::getInstance()->getValue($sql);
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_module` SET position='.($max_pos+1).' WHERE id_module = '.(int)$this->id. ' AND id_shop = '.(int)$id_shop.' AND id_hook =' .(int)$id_hook_header;
        Db::getInstance()->execute($sql);
    }
    public function hookDisplayHeader($params)
	{
	   $current_id_option = Configuration::get('OVIC_CURRENT_OPTION');
       $emptyOption = false;
       if (!$current_id_option || !Validate::isUnsignedId($current_id_option) || !OvicLayoutControl::isAvailablebyId($current_id_option)){
            $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
            $sql = 'SELECT id_option FROM `' . _DB_PREFIX_ . 'ovic_options`';
            $options = Db::getInstance()->executeS($sql);
            if ($options && is_array($options) && sizeof($options)>0){
                foreach ($options as $option){
                    $current_option = new Options($option['id_option'],$this->context->language->id);
                    Configuration::updateValue('OVIC_CURRENT_OPTION',$option['id_option']);
                    $current_id_option = $option['id_option'];
                    break;
                }
            }else{
                $emptyOption = true;
            }
       }
       if (!$emptyOption){
            $current_option = new Options($current_id_option,$this->context->language->id);
            $selected_layout = Configuration::get('OVIC_LAYOUT_COLUMN');
            if (!$selected_layout || substr_count($current_option->column,$selected_layout)<1){
                if (strlen($current_option->column)>0){
                    $selected_layout = (int)substr($current_option->column,-1);
                    Configuration::updateValue('OVIC_LAYOUT_COLUMN',$selected_layout);
                    $this->ProcessLayoutColumn();
                }
            }
        }else{
            $this->context->smarty->assign(array('emptyOption' => Tools::displayError('There is no Option, please add new Option from Layout Builder menu.')));
        }
       $output = '';
        global $smarty;
	   //$linkfont =  Configuration::get('OVIC_FONT_LINK');
//       $start = strpos($linkfont,'family');
//       $linkfont = substr_replace($linkfont,'',0,$start+7);
//       $start = strpos($linkfont,"'");
//       $linkfont = substr_replace($linkfont,'',$start,strlen($linkfont));
//       if (strpos($linkfont,":")>0){
//            $start = strpos($linkfont,":");
//            $linkfont = substr_replace($linkfont,'',$start,strlen($linkfont));
//       }
//       $font_name = str_replace('+',' ',$linkfont);
//       $linkfont =  Configuration::get('OVIC_FONT_LINK');
//       $start = strpos($linkfont,'http');
//       $substr = substr_replace($linkfont,'',$start,strlen($linkfont)-$start);
//       $start = strpos($linkfont,'://');
//       $linkfont = substr_replace($linkfont,'',0,$start);
//       $linkfont = $substr.(empty( $_SERVER['HTTPS'] ) ? 'http' : 'https') .$linkfont;
//       $maincolor = Configuration::get('OVIC_MAIN_COLOR');
//       $btncolor = Configuration::get('OVIC_BTN_COLOR');
//       $btnHovercolor = Configuration::get('OVIC_BTN_HOVER_COLOR');
//       $btntextcolor = Configuration::get('OVIC_BTN_TEXT_COLOR');
//       $btntextHovercolor = Configuration::get('OVIC_BTN_TEXT_HOVER_COLOR');
//       $grbacolor = $this->hex2rgba($maincolor);
      // $this->context->smarty->assign(array(
//            'current_id_option' => Configuration::get('OVIC_CURRENT_OPTION'),
			//'linkfont' => $linkfont,
//			'fontname' => $font_name,
//			'maincolor' => $maincolor,
//            'btncolor' => $btncolor,
//            'btnHovercolor' => $btnHovercolor,
//            'btntextcolor' => $btntextcolor,
//            'btntextHovercolor' => $btntextHovercolor,
//            'grbacolor' => $grbacolor
//	      	));
        $current_id_option = Configuration::get('OVIC_CURRENT_OPTION');
        $smarty->assign(array(
            'current_id_option' => $current_id_option,
            'BEFORE_LOGO' => Hook::exec('displayBeforeLogo'),
            'HOOK_HOME_TOP_COLUMN' => Hook::exec('displayHomeTopColumn'),
            'HOME_BOTTOM_CONTENT' => Hook::exec('displayHomeBottomContent'),
            'HOME_BOTTOM_COLUMN' => Hook::exec('displayHomeBottomColumn'),
            'HOME_TOP_CONTENT' => Hook::exec('displayHomeTopContent'),
            'BOTTOM_COLUMN' => Hook::exec('displayBottomColumn'),
       ));
       $this->context->controller->addCSS(_THEME_CSS_DIR_.'option'.$current_id_option.'.css', 'all');
       return $output;
	}
    public function hookDisplayNav($params){
        return $this->generalHook('displayNav');
    }
    public function hookDisplayTop($params){
        return $this->generalHook('displayTop');
    }
    public function hookdisplayTopColumn(){
        return $this->generalHook('displayTopColumn');
    }
    public function hookdisplayLeftColumn($params){
        return $this->generalHook('displayLeftColumn');
    }
    public function hookdisplayRightColumn($params){
        return $this->generalHook('displayRightColumn');
    }
    public function hookdisplayHome($params){
        return $this->generalHook('displayHome');
    }
    public function hookdisplayHomeTab($params){
        return $this->generalHook('displayHomeTab');
    }
    public function hookdisplayHomeTabContent(){
        return $this->generalHook('displayHomeTabContent');
    }
    public function hookdisplayFooter($params){
        return $this->generalHook('displayFooter');
    }
    /****************/
    public function hookdisplayBeforeLogo($params){
        return $this->generalHook('displayBeforeLogo');
    }
    public function hookdisplayHomeTopColumn($params){
        return $this->generalHook('displayHomeTopColumn');
    }
    public function hookdisplayHomeTopContent($params){
        return $this->generalHook('displayHomeTopContent');
    }
    public function hookdisplayHomeBottomContent($params){
        return $this->generalHook('displayHomeBottomContent');
    }
    public function hookdisplayHomeBottomColumn($params){
        return $this->generalHook('displayHomeBottomColumn');
    }
    public function hookdisplayBottomColumn($params){
        return $this->generalHook('displayBottomColumn');
    }
}