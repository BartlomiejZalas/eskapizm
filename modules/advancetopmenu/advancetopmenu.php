<?php if (!defined('_PS_VERSION_')) exit;
include_once (dirname(__file__) . '/class/Item.php');
include_once (dirname(__file__) . '/class/Block.php');
include_once (dirname(__file__) . '/class/Submenu.php');
class AdvanceTopMenu extends Module
{
    public $absoluteUrl;
    private $absolutePath;
    private $admin_tpl_path;
    public function __construct()
    {
        $this->name = 'advancetopmenu';
        $this->tab = 'front_office_features';
        $this->version = '2.4';
        $this->author = 'OvicSoft';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Fashion - Advanced Top Menu');
        $this->description = $this->l('Advanced Top Menu.');
        $this->secure_key = Tools::encrypt($this->name);
        $this->absoluteUrl = $this->is_https() ? 'https://' : 'http://' .Tools::getShopDomainSsl().__PS_BASE_URI__.
            'modules/' . $this->name . '/';
        $this->absolutePath = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $this->name .
            DIRECTORY_SEPARATOR;
        $this->admin_tpl_path = _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/';
    }
    // this also works, and is more future-proof
    public function install()
    {
        if (!parent::install() || !$this->registerHook('header') || !$this->registerHook('displayBackOfficeHeader') ||
            !$this->registerHook('displayTop') || !$this->installDB()) return false;
        $this->installSampleData();
        return true;
    }
    public function installDb()
    {
        return (Db::getInstance()->execute('
        CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'advance_topmenu_items` (
			`id_item` int(6) NOT NULL AUTO_INCREMENT,
            `id_block` int(6) NOT NULL,
            `position` int(3),
            `type` varchar(30) NOT NULL,
            `icon` varchar(50),
            `link` varchar(255),
            `target` varchar(30),
            `class` varchar(200),
            `active` TINYINT(1) unsigned DEFAULT 1,
			PRIMARY KEY(`id_item`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8') && Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'advance_topmenu_items_lang` (
			`id_item` int(6) NOT NULL,
            `id_lang` int(6) unsigned NOT NULL,
			`title` varchar(255) ,
            `text` text ,
			PRIMARY KEY(`id_item`,`id_lang`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8') && Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'advance_topmenu_blocks` (
			`id_block` int(6) NOT NULL AUTO_INCREMENT,
            `position` int(3),
            `id_sub` int(6) NOT NULL,
            `width` int(6),
            `class` varchar(200),
			PRIMARY KEY(`id_block`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8') && Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'advance_topmenu_main_shop` (
            `id_item` int(10) unsigned NOT NULL,
            `id_shop` int(10) unsigned NOT NULL,
            PRIMARY KEY(`id_item`,`id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8') && Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'advance_topmenu_sub` (
			`id_sub` int(6) NOT NULL AUTO_INCREMENT,
            `id_parent` int(6) NOT NULL,
            `width` int(6),
			`class` varchar(200) ,
            `active` TINYINT(1) unsigned DEFAULT 1,
			PRIMARY KEY(`id_sub`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' default CHARSET=utf8'));
    }
    private function installSampleData()
    {
        $sql = "INSERT INTO `" . _DB_PREFIX_ .
            "advance_topmenu_blocks` (`id_block`, `position`, `id_sub`, `width`, `class`) VALUES
                (1, 0, 1, 12, 'list'),
                (2, 0, 2, 3, 'list'),
                (3, 0, 2, 3, 'list'),
                (4, 0, 2, 6, ''),
                (5, 0, 3, 3, 'list'),
                (6, 0, 3, 3, 'list'),
                (7, 0, 3, 3, 'list'),
                (8, 0, 3, 3, 'list'),
                (9, 0, 4, 6, ''),
                (10, 0, 4, 3, 'list'),
                (11, 0, 4, 3, ''),
                (12, 0, 5, 3, 'list'),
                (13, 0, 5, 3, 'list'),
                (14, 0, 5, 3, 'list'),
                (15, 0, 5, 3, 'list'),
                (16, 0, 6, 12, '');";
        $result = Db::getInstance()->execute($sql);
        $sql = "INSERT INTO `" . _DB_PREFIX_ .
            "advance_topmenu_items` (`id_item`, `id_block`, `position`, `type`, `icon`, `link`, `target`, `class`) VALUES
                (1, 0, 0, 'link', '', 'PAGindex', NULL, ''),
                (2, 0, 1, 'link', '', '#', NULL, ''),
                (3, 0, 2, 'link', '', '#', NULL, ''),
                (4, 0, 3, 'link', '', '#', NULL, 'list-dropdown'),
                (5, 0, 4, 'link', '', '#', NULL, ''),
                (6, 1, 5, 'link', '', '#', NULL, ''),
                (7, 1, 1, 'link', '', '#', NULL, ''),
                (8, 1, 2, 'link', '', '#', NULL, ''),
                (9, 1, 4, 'link', '', '#', NULL, ''),
                (10, 1, 3, 'link', '', '#', NULL, ''),
                (11, 1, 0, 'link', '', '#', NULL, ''),
                (12, 2, 0, 'link', '', '#', NULL, 'group_header'),
                (13, 2, 6, 'link', '', '#', NULL, ''),
                (14, 2, 4, 'link', '', '#', NULL, ''),
                (15, 2, 2, 'link', '', '#', NULL, ''),
                (16, 2, 3, 'link', '', '#', NULL, ''),
                (17, 2, 1, 'link', '', '#', NULL, ''),
                (18, 3, 0, 'link', '', 'PAGmanufacturer', NULL, 'group_header'),
                (19, 4, 0, 'html', '', '', NULL, 'clearfix'),
                (20, 3, 1, 'link', '', '#', NULL, ''),
                (21, 3, 2, 'link', '', '#', NULL, ''),
                (22, 3, 3, 'link', '', '#', NULL, ''),
                (23, 3, 4, 'link', '', '#', NULL, ''),
                (24, 3, 5, 'link', '', '#', NULL, ''),
                (25, 5, 0, 'link', '', '#', NULL, 'group_header'),
                (26, 5, 6, 'link', '', '#', NULL, ''),
                (27, 5, 4, 'link', '', '#', NULL, ''),
                (28, 5, 2, 'link', '', '#', NULL, ''),
                (29, 5, 5, 'link', '', '#', NULL, ''),
                (30, 5, 1, 'link', '', '#', NULL, ''),
                (31, 5, 3, 'link', '', '#', NULL, ''),
                (32, 6, 0, 'link', '', 'PAGmanufacturer', NULL, 'group_header'),
                (33, 6, 1, 'link', '', '#', NULL, ''),
                (34, 6, 2, 'link', '', '#', NULL, ''),
                (35, 6, 3, 'link', '', '#', NULL, ''),
                (36, 6, 4, 'link', '', '#', NULL, ''),
                (37, 6, 5, 'link', '', '#', NULL, ''),
                (38, 6, 6, 'link', '', '#', NULL, ''),
                (39, 7, 0, 'link', '', '#', NULL, 'group_header'),
                (40, 7, 1, 'link', '', '#', NULL, ''),
                (41, 7, 2, 'link', '', '#', NULL, ''),
                (42, 7, 3, 'link', '', '#', NULL, ''),
                (43, 7, 4, 'link', '', '#', NULL, ''),
                (44, 7, 5, 'link', '', '#', NULL, ''),
                (45, 7, 6, 'link', '', '#', NULL, ''),
                (46, 8, 1, 'link', '', '#', NULL, ''),
                (47, 8, 2, 'link', '', '#', NULL, ''),
                (48, 8, 0, 'link', '', '', NULL, 'group_header'),
                (49, 8, 3, 'link', '', '#', NULL, ''),
                (50, 8, 4, 'link', '', '#', NULL, ''),
                (51, 8, 5, 'link', '', '#', NULL, ''),
                (52, 8, 6, 'link', '', '#', NULL, ''),
                (53, 9, 0, 'img', '1405411206top_manu_banner2.png', '', NULL, ''),
                (54, 10, 0, 'link', '', '', NULL, 'group_header'),
                (55, 10, 1, 'link', '', '#', NULL, ''),
                (56, 10, 2, 'link', '', '#', NULL, ''),
                (57, 10, 3, 'link', '', '#', NULL, ''),
                (58, 10, 4, 'link', '', '#', NULL, ''),
                (59, 10, 5, 'link', '', '#', NULL, ''),
                (60, 10, 6, 'link', '', '#', NULL, ''),
                (61, 11, 0, 'link', '', 'PAGmanufacturer', NULL, 'group_header'),
                (62, 11, 1, 'img', '1405414726megaMenu_04---gift_05.png', 'MAN11', NULL, ''),
                (63, 11, 2, 'img', '1405414765megaMenu_04---gift_11.png', '#', NULL, ''),
                (64, 11, 3, 'img', '1405415898megaMenu_04---gift_17.png', 'MAN9', NULL, ''),
                (65, 12, 0, 'img', '1405476801megaMenu_05_11.png', 'CAT3', NULL, ''),
                (66, 12, 1, 'link', '', '#', NULL, 'group_header'),
                (67, 12, 5, 'link', '', '#', NULL, ''),
                (68, 12, 4, 'link', '', '#', NULL, ''),
                (69, 12, 2, 'link', '', '#', NULL, ''),
                (70, 12, 6, 'link', '', '#', NULL, ''),
                (71, 12, 3, 'link', '', '#', NULL, ''),
                (72, 13, 0, 'img', '1405483018megaMenu_05_13.png', 'CAT3', NULL, ''),
                (73, 13, 1, 'link', '', '#', NULL, 'group_header'),
                (74, 13, 5, 'link', '', '#', NULL, ''),
                (75, 13, 4, 'link', '', '#', NULL, ''),
                (76, 13, 2, 'link', '', '#', NULL, ''),
                (77, 13, 6, 'link', '', '#', NULL, ''),
                (78, 13, 3, 'link', '', '#', NULL, ''),
                (79, 14, 0, 'img', '1405483746megaMenu_05_15.png', '', NULL, ''),
                (80, 14, 1, 'link', '', '', NULL, 'group_header'),
                (81, 14, 5, 'link', '', '#', NULL, ''),
                (82, 14, 4, 'link', '', '#', NULL, ''),
                (83, 14, 2, 'link', '', '#', NULL, ''),
                (84, 14, 6, 'link', '', '#', NULL, ''),
                (85, 14, 3, 'link', '', '#', NULL, ''),
                (86, 15, 0, 'img', '1405500152megaMenu_05_17.png', '', NULL, ''),
                (87, 15, 1, 'link', '', '', NULL, 'group_header'),
                (88, 15, 5, 'link', '', '#', NULL, ''),
                (89, 15, 4, 'link', '', '#', NULL, ''),
                (90, 15, 2, 'link', '', '#', NULL, ''),
                (91, 15, 6, 'link', '', '#', NULL, ''),
                (92, 15, 3, 'link', '', '#', NULL, ''),
                (95, 2, 5, 'link', '', '#', NULL, ''),
                (96, 3, 6, 'link', '', '#', NULL, ''),
                (98, 0, 7, 'link', '', 'PAGindex', NULL, ''),
                (99, 0, 8, 'link', '', '#', NULL, 'list-dropdown'),
                (100, 0, 9, 'link', '', '#', NULL, ''),
                (101, 16, 0, 'link', '', '#', NULL, ''),
                (102, 16, 1, 'link', '', '#', NULL, ''),
                (103, 16, 2, 'link', '', '#', NULL, ''),
                (104, 16, 3, 'link', '', '#', NULL, ''),
                (105, 16, 4, 'link', '', '#', NULL, ''),
                (106, 16, 5, 'link', '', '#', NULL, ''),
                (107, 0, 5, 'link', '', '', NULL, '');";
        $result &= Db::getInstance()->execute($sql);
        $sql = "INSERT INTO `" . _DB_PREFIX_ .
            "advance_topmenu_items_lang` (`id_item`, `id_lang`, `title`, `text`) VALUES ";
        foreach (Language::getLanguages(false) as $lang){
                $sql .= "(1,".$lang['id_lang'].",'Home', ''),";
                $sql .= "(2,".$lang['id_lang'].",'Women', ''),";
                $sql .= "(3,".$lang['id_lang'].",'Men', ''),";
                $sql .= "(4,".$lang['id_lang'].",'Kids', ''),";
                $sql .= "(5,".$lang['id_lang'].",'Gift', ''),";
                $sql .= "(6,".$lang['id_lang'].",'Scervas', ''),";
                $sql .= "(7,".$lang['id_lang'].",'Tops', ''),";
                $sql .= "(8,".$lang['id_lang'].",'Skirts', ''),";
                $sql .= "(9,".$lang['id_lang'].",'Pants', ''),";
                $sql .= "(10,".$lang['id_lang'].",'Jackets', ''),";
                $sql .= "(11,".$lang['id_lang'].",'Dresses', ''),";
                $sql .= "(12,".$lang['id_lang'].",'Category', ''),";
                $sql .= "(13,".$lang['id_lang'].",'Scarves', ''),";
                $sql .= "(14,".$lang['id_lang'].",'Tops', ''),";
                $sql .= "(15,".$lang['id_lang'].",'Skirts', ''),";
                $sql .= "(16,".$lang['id_lang'].",'Jackets', ''),";
                $sql .= "(17,".$lang['id_lang'].",'Dresses', ''),";
                $sql .= "(18,".$lang['id_lang'].",'Brand', ''),";
                $sql .= "(19,".$lang['id_lang'].",'Collection 2014', '<div class=\"col-sm-7 col\">\r\n<h2>Collection 2014</h2>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>\r\n<img alt=\"\" src=\"http://kutethemes.com/demo/fashion/london-stars1/img/cms/top_manu_banner1.png\" width=\"200\" height=\"91\" class=\"img-responsive\" /></div>\r\n<div class=\"col-sm-5 col\"><img alt=\"\" src=\"http://kutethemes.com/demo/fashion/london-stars1/img/cms/top_manu_banner.png\" class=\"img-responsive\" width=\"190\" height=\"282\" /></div>'),";
                $sql .= "(20,".$lang['id_lang'].",'Gucci', ''),";
                $sql .= "(21,".$lang['id_lang'].",'Chanel', ''),";
                $sql .= "(22,".$lang['id_lang'].",'Dolce Gabbana', ''),";
                $sql .= "(23,".$lang['id_lang'].",'Prada', ''),";
                $sql .= "(24,".$lang['id_lang'].",'Armani', ''),";
                $sql .= "(25,".$lang['id_lang'].",'Categories', ''),";
                $sql .= "(26,".$lang['id_lang'].",'Suits', ''),";
                $sql .= "(27,".$lang['id_lang'].",'Shorts', ''),";
                $sql .= "(28,".$lang['id_lang'].",'Trousers', ''),";
                $sql .= "(29,".$lang['id_lang'].",'Jumpers Cardigans', ''),";
                $sql .= "(30,".$lang['id_lang'].",'Jackets', ''),";
                $sql .= "(31,".$lang['id_lang'].",'Underwear', ''),";
                $sql .= "(32,".$lang['id_lang'].",'Brands', ''),";
                $sql .= "(33,".$lang['id_lang'].",'Gucci', ''),";
                $sql .= "(34,".$lang['id_lang'].",'Chanel', ''),";
                $sql .= "(35,".$lang['id_lang'].",'Dolce Gabbana', ''),";
                $sql .= "(36,".$lang['id_lang'].",'Prada', ''),";
                $sql .= "(37,".$lang['id_lang'].",'Armani', ''),";
                $sql .= "(38,".$lang['id_lang'].",'Versace', ''),";
                $sql .= "(39,".$lang['id_lang'].",'Accessories', ''),";
                $sql .= "(40,".$lang['id_lang'].",'Software', ''),";
                $sql .= "(41,".$lang['id_lang'].",'Fabric', ''),";
                $sql .= "(42,".$lang['id_lang'].",'Art/ Print', ''),";
                $sql .= "(43,".$lang['id_lang'].",'Clothing', ''),";
                $sql .= "(44,".$lang['id_lang'].",'Jalathen', ''),";
                $sql .= "(45,".$lang['id_lang'].",'Betylen', ''),";
                $sql .= "(46,".$lang['id_lang'].",'Software', ''),";
                $sql .= "(47,".$lang['id_lang'].",'Fabric', ''),";
                $sql .= "(48,".$lang['id_lang'].",'Quick Links', ''),";
                $sql .= "(49,".$lang['id_lang'].",'Art/ Print', ''),";
                $sql .= "(50,".$lang['id_lang'].",'Clothing', ''),";
                $sql .= "(51,".$lang['id_lang'].",'Jalathen', ''),";
                $sql .= "(52,".$lang['id_lang'].",'Betylen', ''),";
                $sql .= "(53,".$lang['id_lang'].",'Collection 2014', ''),";
                $sql .= "(54,".$lang['id_lang'].",'Shop', ''),";
                $sql .= "(55,".$lang['id_lang'].",'Gucci', ''),";
                $sql .= "(56,".$lang['id_lang'].",'Chanel', ''),";
                $sql .= "(57,".$lang['id_lang'].",'Dolce Gabbana', ''),";
                $sql .= "(58,".$lang['id_lang'].",'Prada', ''),";
                $sql .= "(59,".$lang['id_lang'].",'Armani', ''),";
                $sql .= "(60,".$lang['id_lang'].",'Versace', ''),";
                $sql .= "(61,".$lang['id_lang'].",'Brands', ''),";
                $sql .= "(62,".$lang['id_lang'].",'Dolce Gabbana', ''),";
                $sql .= "(63,".$lang['id_lang'].",'Prada', ''),";
                $sql .= "(64,".$lang['id_lang'].",'Gucci', ''),";
                $sql .= "(65,".$lang['id_lang'].",'Categories Image', ''),";
                $sql .= "(66,".$lang['id_lang'].",'Categories', ''),";
                $sql .= "(67,".$lang['id_lang'].",'Scarves', ''),";
                $sql .= "(68,".$lang['id_lang'].",'Tops', ''),";
                $sql .= "(69,".$lang['id_lang'].",'Skirts', ''),";
                $sql .= "(70,".$lang['id_lang'].",'Pants', ''),";
                $sql .= "(71,".$lang['id_lang'].",'Jackets', ''),";
                $sql .= "(72,".$lang['id_lang'].",'Categories Image', ''),";
                $sql .= "(73,".$lang['id_lang'].",'Categories', ''),";
                $sql .= "(74,".$lang['id_lang'].",'Scarves', ''),";
                $sql .= "(75,".$lang['id_lang'].",'Tops', ''),";
                $sql .= "(76,".$lang['id_lang'].",'Skirts', ''),";
                $sql .= "(77,".$lang['id_lang'].",'Pants', ''),";
                $sql .= "(78,".$lang['id_lang'].",'Jackets', ''),";
                $sql .= "(79,".$lang['id_lang'].",'Categories Image', ''),";
                $sql .= "(80,".$lang['id_lang'].",'Categories', ''),";
                $sql .= "(81,".$lang['id_lang'].",'Scarves', ''),";
                $sql .= "(82,".$lang['id_lang'].",'Tops', ''),";
                $sql .= "(83,".$lang['id_lang'].",'Skirts', ''),";
                $sql .= "(84,".$lang['id_lang'].",'Pants', ''),";
                $sql .= "(85,".$lang['id_lang'].",'Jackets', ''),";
                $sql .= "(86,".$lang['id_lang'].",'Categories Image', ''),";
                $sql .= "(87,".$lang['id_lang'].",'Categories', ''),";
                $sql .= "(88,".$lang['id_lang'].",'Scarves', ''),";
                $sql .= "(89,".$lang['id_lang'].",'Tops', ''),";
                $sql .= "(90,".$lang['id_lang'].",'Skirts', ''),";
                $sql .= "(91,".$lang['id_lang'].",'Pants', ''),";
                $sql .= "(92,".$lang['id_lang'].",'Jackets', ''),";
                $sql .= "(95,".$lang['id_lang'].",'Pants', ''),";
                $sql .= "(96,".$lang['id_lang'].",'Versace', ''),";
                $sql .= "(98,".$lang['id_lang'].",'Home', ''),";
                $sql .= "(99,".$lang['id_lang'].",'Women', ''),";
                $sql .= "(100,".$lang['id_lang'].",'Shop 1', ''),";
                $sql .= "(101,".$lang['id_lang'].",'Dresses', ''),";
                $sql .= "(102,".$lang['id_lang'].",'Pants', ''),";
                $sql .= "(103,".$lang['id_lang'].",'Scarves', ''),";
                $sql .= "(104,".$lang['id_lang'].",'Skirts', ''),";
                $sql .= "(105,".$lang['id_lang'].",'Jackets', ''),";
                $sql .= "(106,".$lang['id_lang'].",'Scarves', ''),";
                $sql .= "(107,".$lang['id_lang'].",'Blog', ''),";
            }
        $sql = rtrim($sql, ",").";";
        $result &= Db::getInstance()->execute($sql);
        
        $sql = "INSERT INTO `" . _DB_PREFIX_ . "advance_topmenu_main_shop` (`id_item`, `id_shop`) VALUES
                (1, 1),
                (2, 1),
                (3, 1),
                (4, 1),
                (5, 1),
                (98, 2),
                (99, 2),
                (100, 2),
                (107, 1);";
        $result &= Db::getInstance()->execute($sql);
        $sql = "INSERT INTO `" . _DB_PREFIX_ .
            "advance_topmenu_sub` (`id_sub`, `id_parent`, `width`, `class`) VALUES
                (1, 4, 200, ''),
                (2, 2, 0, 'mega_dropdown'),
                (3, 3, 0, 'mega_dropdown  men'),
                (4, 5, 930, 'mega_dropdown gift'),
                (5, 1, 0, 'mega_dropdown home'),
                (6, 99, 200, 'list');";
        $result &= Db::getInstance()->execute($sql);
        return $result;
    }
    public function uninstall()
    {
        Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('topmenu.tpl'));
        if (!parent::uninstall() || !$this->uninstallDB()) return false;
        return true;
    }
    private function uninstallDb()
    {
        Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'advance_topmenu_blocks`');
        Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'advance_topmenu_sub`');
        Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'advance_topmenu_items`');
        Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'advance_topmenu_items_lang`');
        Db::getInstance()->execute('DROP TABLE `' . _DB_PREFIX_ . 'advance_topmenu_main_shop`');
        return true;
    }
    public function getContent()
    {
        $output = '';
        $errors = array();
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages(false);
        if (Tools::getValue('confirm_msg'))
        {
            $this->context->smarty->assign('confirmation', Tools::getValue('confirm_msg'));
        }
        
        if (Tools::isSubmit('submitnewitem'))
        {
            
            $id_item = (int)Tools::getValue('item_id');
            if ($id_item && Validate::isUnsignedId($id_item))
            {
                $new_item = new Item($id_item);
            }
            else
            {
                $new_item = new Item();
            }
            $new_item->id_block = (int)Tools::getValue('block_id');
            $new_item->type = Tools::getValue('linktype');
            $new_item->active = (int)Tools::getValue('active');
            $itemtitle_set = false;
            foreach ($languages as $language)
            {
                $item_title = Tools::getValue('item_title_' . $language['id_lang']);
                if (strlen($item_title) > 0)
                {
                    $itemtitle_set = true;
                }
                $new_item->title[$language['id_lang']] = $item_title;
            }
            if (!$itemtitle_set)
            {
                $lang_title = Language::getLanguage($this->context->language->id);
                if ($new_item->type == 'img') $errors[] = 'This Alt text field is required at least in ' . $lang_title['name'];
                else  $errors[] = 'This item title field is required at least in ' . $lang_title['name'];
            }
            $new_item->class = Tools::getValue('custom_class');
            if ($new_item->type == 'link')
            {
                $new_item->icon = Tools::getValue('item_icon');
                $new_item->link = Tools::getValue('link_value');
            }
            elseif ($new_item->type == 'img')
            {
                if (isset($_FILES['item_img']) && strlen($_FILES['item_img']['name']) > 0)
                {
                    if (!$img_file = $this->moveUploadedImage($_FILES['item_img']))
                    {
                        $errors[] = 'An error occurred during the image upload.';
                    }
                    else
                    {
                        $new_item->icon = $img_file;
                        if (Tools::getValue('old_img') != '')
                        {
                            $filename = Tools::getValue('old_img');
                            if (file_exists(dirname(__file__) . '/img/' . $filename))
                            {
                                @unlink(dirname(__file__) . '/img/' . $filename);
                            }
                        }
                    }
                }
                else
                {
                    $new_item->icon = Tools::getValue('old_img');
                }
                $new_item->link = Tools::getValue('link_value');
            }
            elseif ($new_item->type == 'html')
            {
                foreach ($languages as $language) $new_item->text[$language['id_lang']] = Tools::getValue('item_html_' .
                        $language['id_lang']);
            }
            if (!count($errors))
            {
                if ($id_item && Validate::isUnsignedId($id_item))
                {
                    if (!$new_item->update())
                    {
                        $errors[] = 'An error occurred while update data.';
                    }
                }
                else
                {
                    if (!$new_item->add())
                    {
                        $errors[] = 'An error occurred while saving data.';
                    }
                }
                if (!count($errors))
                {
                    if ($id_item && Validate::isUnsignedId($id_item))
                    {
                        $this->context->smarty->assign('confirmation', $this->l('Item successfully updated.'));
                    }
                    else
                    {
                        $confirm_msg = $this->l('New item successfully added.');
                        Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('topmenu.tpl'));
                        Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' .
                            Tools::getAdminTokenLite('AdminModules') . '&confirm_msg=' . $confirm_msg);
                    }
                }
            }
        }
        elseif (Tools::isSubmit('submit_del_item'))
        {
            $item_id = Tools::getValue('item_id');
            if ($item_id && Validate::isUnsignedId($item_id))
            {
                $subs = $this->getSupMenu($item_id);
                $del = true;
                if ($subs && count($subs) > 0)
                {
                }
                foreach ($subs as $sub)
                {
                    $del &= $this->deleteSub($sub['id_sub']);
                }
                $item = new Item($item_id);
                if (!$item->delete() || !$del)
                {
                    $errors[] = 'An error occurred while delete item.';
                }
                else
                {
                    Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('topmenu.tpl'));
                    $this->context->smarty->assign('confirmation', $this->l('Delete successful.'));
                }
            }
        }
        elseif (Tools::isSubmit('submitnewsub'))
        {
            $id_sub = Tools::getValue('id_sub');
            if ($id_sub && Validate::isUnsignedId($id_sub))
            {
                $sub = new Submenu($id_sub);
            }
            else
            {
                $sub = new Submenu();
            }
            $sub->id_parent = Tools::getValue('id_parent');
            $sub->width = Tools::getValue('subwidth');
            $sub->class = Tools::getValue('sub_class');
            $sub->active = Tools::getValue('active');
            if ($id_sub && Validate::isUnsignedId($id_sub))
            {
                if (!$sub->update())
                {
                    $errors[] = 'An error occurred while update data.';
                }
            }
            else
            {
                if (!$sub->checkAvaiable())
                {
                    if (!$sub->add())
                    {
                        $errors[] = 'An error occurred while saving data.';
                    }
                }
                else
                {
                    $parent_item = new Item($sub->id_parent);
                    $errors[] = $parent_item->title[$this->context->language->id] . ' already have a sub.';
                }
            }
            if (!count($errors))
            {
                if ($id_sub && Validate::isUnsignedId($id_sub))
                {
                    $this->context->smarty->assign('confirmation', $this->l('Submenu successfully updated.'));
                }
                else
                {
                    $confirm_msg = $this->l('New sub successfully added.');
                    Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('topmenu.tpl'));
                    Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' .
                        Tools::getAdminTokenLite('AdminModules') . '&confirm_msg=' . $confirm_msg);
                }
            }
        }
        elseif (Tools::isSubmit('submit_del_sub'))
        {
            $id_sub = (int)Tools::getValue('id_sub');
            if ($id_sub && Validate::isUnsignedId($id_sub))
            {
                if (!$this->deleteSub($id_sub))
                {
                    $errors[] = 'An error occurred while delete sub menu.';
                }
                else
                {
                    Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('topmenu.tpl'));
                    $this->context->smarty->assign('confirmation', $this->l('Delete successful.'));
                }
            }
        }
        elseif (Tools::isSubmit('submitnewblock'))
        {
            $id_block = Tools::getValue('id_block');
            if ($id_block && Validate::isUnsignedId($id_block))
            {
                $block = new Block($id_block);
            }
            else
            {
                $block = new Block();
            }
            $block->id_sub = Tools::getValue('id_sub');
            $block->width = Tools::getValue('block_widh');
            $block->class = Tools::getValue('block_class');
            if ($id_block && Validate::isUnsignedId($id_block))
            {
                if (!$block->update())
                {
                    $errors[] = 'An error occurred while update block.';
                }
            }
            else
            {
                if (!$block->add())
                {
                    $errors[] = 'An error occurred while saving data.';
                }
            }
            if (!count($errors))
            {
                if ($id_block && Validate::isUnsignedId($id_block))
                {
                    $this->context->smarty->assign('confirmation', $this->l('Block successfully updated.'));
                }
                else
                {
                    $confirm_msg = $this->l('New block successfully added.');
                    Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('topmenu.tpl'));
                    Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' .
                        Tools::getAdminTokenLite('AdminModules') . '&confirm_msg=' . $confirm_msg);
                }
            }
        }
        elseif (Tools::isSubmit('submit_del_block'))
        {
            $id_block = Tools::getValue('id_block');
            if ($id_block && Validate::isUnsignedId($id_block))
            {
                if (!$this->deleteBlock($id_block))
                {
                    $errors[] = 'An error occurred while delete block.';
                }
                else
                {
                    Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('topmenu.tpl'));
                    $this->context->smarty->assign('confirmation', $this->l('Delete successful.'));
                }
            }
        }
        elseif (Tools::isSubmit('changeactive'))
        {
            $id_item = (int)Tools::getValue('item_id');
            if ($id_item && Validate::isUnsignedId($id_item))
            {
                $item = new Item($id_item);
                $item->active = !$item->active;
                if (!$item->update())
                {
                    $errors[] = $this->displayError('Could not change');
                }
                else
                {
                    $confirm_msg = $this->l('Successfully updated.');
                    Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('topmenu.tpl'));
                    Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' .
                        Tools::getAdminTokenLite('AdminModules') . '&confirm_msg=' . $confirm_msg);
                }
            }
        }
        elseif (Tools::isSubmit('changestatus'))
        {
            $id_sub = (int)Tools::getValue('id_sub');
            if ($id_sub && Validate::isUnsignedId($id_sub))
            {
                $sub_menu = new Submenu($id_sub);
                $sub_menu->active = !$sub_menu->active;
                if (!$sub_menu->update())
                {
                    $errors[] = $this->displayError('Could not change');
                }
                else
                {
                    $confirm_msg = $this->l('Submenu successfully updated.');
                    Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('topmenu.tpl'));
                    Tools::redirectAdmin(AdminController::$currentIndex . '&configure=' . $this->name . '&token=' .
                        Tools::getAdminTokenLite('AdminModules') . '&confirm_msg=' . $confirm_msg);
                }
            }
        }
        $this->context->smarty->assign(array(
            'admin_tpl_path' => $this->admin_tpl_path,
            'postAction' => AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite
                ('AdminModules'),
            ));
        if (count($errors) > 0)
        {
            if (isset($errors) && count($errors)) $output .= $this->displayError(implode('<br />', $errors));
        }
        if (Tools::isSubmit('submit_edit_item') || (Tools::isSubmit('submitnewItem') && count($errors) > 0))
        {
            $output .= $this->displayItemForm();
        }
        elseif (Tools::isSubmit('submit_edit_sub'))
        {
            $output .= $this->displaySubForm();
        }
        elseif (Tools::isSubmit('submit_new_block'))
        {
            $output .= $this->displayBlockForm();
        }
        else
        {
            $output .= $this->displayForm();
        }
        return $output;
    }
    private function deleteSub($id_sub = null)
    {
        if (is_null($id_sub)) return false;
        $blocks = $this->getAllBlocks($id_sub);
        $del = true;
        if ($blocks && count($blocks) > 0)
            foreach ($blocks as $bl)
            {
                $del &= $this->deleteBlock($bl['id_block']);
            }
        if ($del)
        {
            $sub = new Submenu($id_sub);
            return $sub->delete();
        }
        return false;
    }
    private function deleteBlock($id_block = null)
    {
        if (is_null($id_block)) return false;
        $items = $this->getItemByBlock($id_block);
        $del = true;
        if ($items && count($items) > 0)
            foreach ($items as $it)
            {
                $item = new Item($it['id_item']);
                $del &= $item->delete();
            }
        if ($del)
        {
            $block = new Block($id_block);
            return $block->delete();
        }
        return false;
    }
    private function deleteItem($id_item = null)
    {
        if (is_null($id_item)) return false;
        $item = new Item($id_item);
        return $item->delete();
    }
    public function updateMenuPosition($order = null)
    {
        if (is_null($order)) return false;
        $position = explode('::', $order);
        $res = false;
        if (count($position) > 0)
            foreach ($position as $key => $id_item)
            {
                $res = Db::getInstance()->execute('
                    UPDATE `' . _DB_PREFIX_ . 'advance_topmenu_items`
                    SET `position` = ' . $key . '
                    WHERE `id_item` = ' . (int)$id_item);
                if (!$res) break;
            }
        Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('topmenu.tpl'));
        return $res;
    }
    public function updateBlockPosition($order = null)
    {
        if (is_null($order)) return false;
        $position = explode('::', $order);
        $res = false;
        if (count($position) > 0)
            foreach ($position as $key => $id_block)
            {
                $res = Db::getInstance()->execute('
                    UPDATE `' . _DB_PREFIX_ . 'advance_topmenu_blocks`
                    SET `position` = ' . $key . '
                    WHERE `id_block` = ' . (int)$id_block);
                if (!$res) break;
            }
        Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath('topmenu.tpl'));
        return $res;
    }
    private function displayBlockForm()
    {
        $id_sub = Tools::getValue('id_sub');
        if (!strlen($id_sub) > 0)
        {
            return;
        }
        $id_block = Tools::getValue('id_block');
        if (strlen($id_block) > 0)
        {
            $block = new Block($id_block);
        }
        else
        {
            $block = new Block($id_block);
        }
        $block->id_sub = (int)$id_sub;
        $this->context->smarty->assign(array(
            'form' => 'block',
            'block' => $block,
            ));
        return $this->display(__file__, 'views/templates/admin/block_form.tpl');
    }
    private function displaySubForm()
    {
        $id_sub = Tools::getValue('id_sub');
        if (strlen($id_sub) > 0)
        {
            $sub = new Submenu($id_sub);
        }
        else
        {
            $sub = new Submenu($id_sub);
        }
        $main_items = $this->getMainItem();
        $this->context->smarty->assign(array(
            'form' => 'sub',
            'submenu' => $sub,
            'main_items' => $main_items));
        return $this->display(__file__, 'views/templates/admin/sub_form.tpl');
    }
    private function displayForm()
    {
        $main_items = $this->getMainItem();
        $supmenu = $this->getSupMenu();
        foreach ($supmenu as &$sub)
        {
            $sub['blocks'] = $this->getAllBlocks($sub['id_sub']);
            if (count($sub['blocks']))
            {
                foreach ($sub['blocks'] as &$block)
                {
                    $block['items'] = $this->getItemByBlock($block['id_block']);
                }
            }
        }
        $this->context->smarty->assign(array(
            'form' => 'main',
            'imgpath' => $this->_path . 'img/',
            'supmenu' => $supmenu,
            'ajaxUrl' => $this->absoluteUrl . 'ajax.php?secure_key=' . $this->secure_key,
            'list_items' => $main_items));
        return $this->display(__file__, 'views/templates/admin/admin.tpl');
    }
    private function displayItemForm()
    {
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages();
        $id_lang = $this->context->language->id;
        $item_id = (int)Tools::getValue('item_id');
        $link_texts = array();
        if ($item_id && Validate::isUnsignedId($item_id))
        {
            $Item = new Item($item_id);
        }
        else
        {
            $Item = new Item();
            $block_id = (int)Tools::getValue('block');
            $Item->id_block = $block_id;
        }
        if (Tools::isSubmit('submitnewItem'))
        {
            $Item->id_block = Tools::getValue('block_id');
            $Item->type = Tools::getValue('linktype');
            $Item->active = (int)Tools::getValue('active');
            $Item->class = Tools::getValue('custom_class');
            if ($Item->type == 'link')
            {
                $Item->icon = Tools::getValue('item_icon');
                $Item->link = Tools::getValue('link_value');
            }
            elseif ($Item->type == 'img')
            {
                $item_img = Tools::getValue('old_img');
                if (strlen($item_img) > 0) $Item->icon = $item_img;
                $Item->link = Tools::getValue('link_value');
            }
            elseif ($Item->type == 'html')
            {
                foreach ($languages as $language) $Item->text[$language['id_lang']] = Tools::getValue('item_html_' .
                        $language['id_lang']);
            }
        }
        $lang_ul = '<ul class="dropdown-menu">';
        $default_link_option = array();
        foreach ($languages as $lg)
        {
            $link_text = $this->fomartLink((array )$Item, $lg['id_lang']);
            $link_texts[$lg['id_lang']] = $link_text['link'];
            $lang_ul .= '<li><a href="javascript:hideOtherLanguage(' . $lg['id_lang'] . ');" tabindex="-1">' . $lg['name'] .
                '</a></li>';
            $default_link_option[$lg['id_lang']] = $this->getAllDefaultLink($lg['id_lang']);
        }
        $lang_ul .= '</ul>';
        $this->context->smarty->assign(array(
            'form' => 'item',
            'item' => $Item,
            'link_text' => $link_texts,
            'absoluteUrl' => $this->absoluteUrl,
            'default_link_option' => $default_link_option,
            'lang_ul' => $lang_ul,
            'langguages' => array(
                'default_lang' => $id_lang_default,
                'all' => $languages,
                'lang_dir' => _THEME_LANG_DIR_)));
        $iso = Language::getIsoById((int)($id_lang));
        $isoTinyMCE = (file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en');
        $ad = dirname($_SERVER["PHP_SELF"]);
        $html = '<script type="text/javascript">
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
        return $html . $this->display(__file__, 'views/templates/admin/item_form.tpl');
    }
    private function getAllBlocks($id_sub = null)
    {
        if (is_null($id_sub)) return;
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'advance_topmenu_blocks`
                WHERE `id_sub` = ' . $id_sub . '
                ORDER BY  `position` ASC';
        $results = Db::getInstance()->executeS($sql);
        return $results;
    }
    private function getSupMenu($id_main = null, $active = false)
    {
        $id_lang = $this->context->language->id;
        $id_shop = (int)$this->context->shop->id;
        $sql = 'SELECT s.*, til.`title` FROM `' . _DB_PREFIX_ . 'advance_topmenu_sub` s

                LEFT JOIN `' . _DB_PREFIX_ .
            'advance_topmenu_items_lang` til ON (s.`id_parent` = til.`id_item`)

                LEFT JOIN `' . _DB_PREFIX_ .
            'advance_topmenu_main_shop` tms ON (s.`id_parent` = tms.`id_item`)

                WHERE til.`id_lang` = ' . (int)$id_lang . '

                 AND id_shop = ' . $id_shop . ($active ? ' AND active = ' . $active : '') . (is_null
            ($id_main) ? '' : ' AND s.`id_parent` = ' . (int)$id_main);
        $results = Db::getInstance()->executeS($sql);
        return $results;
    }
    private function getItemByBlock($id_block = null, $active = false)
    {
        if (is_null($id_block)) return false;
        $id_lang = (int)$this->context->language->id;
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'advance_topmenu_items` ti

                LEFT JOIN `' . _DB_PREFIX_ .
            'advance_topmenu_items_lang` til ON (ti.`id_item` = til.`id_item`)

                WHERE `id_block` = ' . (int)$id_block . ' AND

                id_lang = ' . $id_lang . ($active ? ' AND active = ' . $active : '') . '

                ORDER BY  ti.`position` ASC';
        $results = Db::getInstance()->executeS($sql);
        return $results;
    }
    private function getMainItem($active = false)
    {
        $id_lang = (int)$this->context->language->id;
        $id_shop = (int)$this->context->shop->id;
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'advance_topmenu_items` ti

                LEFT JOIN `' . _DB_PREFIX_ .
            'advance_topmenu_items_lang` til ON (ti.`id_item` = til.`id_item`)

                LEFT JOIN `' . _DB_PREFIX_ .
            'advance_topmenu_main_shop` tis ON (ti.`id_item` = tis.`id_item`)

                WHERE `id_block` = 0 AND

                tis.`id_shop` = ' . $id_shop . ' AND

                til.`id_lang` = ' . $id_lang . ($active ? ' AND active = ' . $active : '') . '

                ORDER BY  ti.`position` ASC';
        $results = Db::getInstance()->executeS($sql);
        return $results;
    }
    private function getCMSOptions($parent = 0, $depth = 0, $id_lang = false, $link = false)
    {
        $html = '';
        $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
        $categories = $this->getCMSCategories(false, (int)$parent, (int)$id_lang);
        $pages = $this->getCMSPages((int)$parent, false, (int)$id_lang);
        $spacer = str_repeat('&nbsp;', 3 * (int)$depth);
        foreach ($categories as $category)
        {
            //$html .= '<option value="CMS_CAT'.$category['id_cms_category'].'" style="font-weight: bold;">'.$spacer.$category['name'].'</option>';
            $html .= $this->getCMSOptions($category['id_cms_category'], (int)$depth + 1, (int)$id_lang, $link);
            //$spacer = str_repeat('&nbsp;', 3 * (int)$depth);
        }
        foreach ($pages as $page)
            if ($link) $html .= '<option value="' . $this->context->link->getCMSLink($page['id_cms']) . '">' . (isset
                    ($spacer) ? $spacer : '') . $page['meta_title'] . '</option>';
            else  $html .= '<option value="CMS' . $page['id_cms'] . '">' . $page['meta_title'] . '</option>';
        return $html;
    }
    private function getCMSCategories($recursive = false, $parent = 1, $id_lang = false)
    {
        $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
        if ($recursive === false)
        {
            $sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`, bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
				FROM `' . _DB_PREFIX_ . 'cms_category` bcp
				INNER JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl
				ON (bcp.`id_cms_category` = cl.`id_cms_category`)
				WHERE cl.`id_lang` = ' . (int)$id_lang . '
				AND bcp.`id_parent` = ' . (int)$parent;
            return Db::getInstance()->executeS($sql);
        }
        else
        {
            $sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`, bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
				FROM `' . _DB_PREFIX_ . 'cms_category` bcp
				INNER JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl
				ON (bcp.`id_cms_category` = cl.`id_cms_category`)
				WHERE cl.`id_lang` = ' . (int)$id_lang . '
				AND bcp.`id_parent` = ' . (int)$parent;
            $results = Db::getInstance()->executeS($sql);
            foreach ($results as $result)
            {
                $sub_categories = $this->getCMSCategories(true, $result['id_cms_category'], (int)$id_lang);
                if ($sub_categories && count($sub_categories) > 0) $result['sub_categories'] = $sub_categories;
                $categories[] = $result;
            }
            return isset($categories) ? $categories : false;
        }
    }
    private function getCMSPages($id_cms_category, $id_shop = false, $id_lang = false)
    {
        $id_shop = ($id_shop !== false) ? (int)$id_shop : (int)Context::getContext()->shop->id;
        $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
        $sql = 'SELECT c.`id_cms`, cl.`meta_title`, cl.`link_rewrite`
			FROM `' . _DB_PREFIX_ . 'cms` c
			INNER JOIN `' . _DB_PREFIX_ . 'cms_shop` cs
			ON (c.`id_cms` = cs.`id_cms`)
			INNER JOIN `' . _DB_PREFIX_ . 'cms_lang` cl
			ON (c.`id_cms` = cl.`id_cms`)
			WHERE c.`id_cms_category` = ' . (int)$id_cms_category . '
			AND cs.`id_shop` = ' . (int)$id_shop . '
			AND cl.`id_lang` = ' . (int)$id_lang . '
			AND c.`active` = 1
			ORDER BY `position`';
        return Db::getInstance()->executeS($sql);
    }
    private function getPagesOption($id_lang = null, $link = false)
    {
        if (is_null($id_lang)) $id_lang = (int)$this->context->cookie->id_lang;
        $files = Meta::getMetasByIdLang($id_lang);
        $html = '';
        foreach ($files as $file)
        {
            if ($link) $html .= '<option value="' . $this->context->link->getPageLink($file['page']) . '">' . (($file['title'] !=
                    '') ? $file['title'] : $file['page']) . '</option>';
            else  $html .= '<option value="PAG' . $file['page'] . '">' . (($file['title'] != '') ? $file['title'] :
                    $file['page']) . '</option>';
        }
        return $html;
    }
    private function getCategoryOption($id_category = 1, $id_lang = false, $id_shop = false, $recursive = true,
        $link = false)
    {
        $html = '';
        $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
        $category = new Category((int)$id_category, (int)$id_lang, (int)$id_shop);
        if (is_null($category->id)) return;
        if ($recursive)
        {
            $children = Category::getChildren((int)$id_category, (int)$id_lang, true, (int)$id_shop);
            $spacer = str_repeat('&nbsp;', 3 * (int)$category->level_depth);
        }
        $shop = (object)Shop::getShop((int)$category->getShopID());
        if (!in_array($category->id, array(Configuration::get('PS_HOME_CATEGORY'), Configuration::get('PS_ROOT_CATEGORY'))))
        {
            if ($link) $html .= '<option value="' . $this->context->link->getCategoryLink($category->id) . '">' . (isset
                    ($spacer) ? $spacer : '') . str_repeat('&nbsp;', 3 * (int)$category->level_depth) . $category->name .
                    '</option>';
            else  $html .= '<option value="CAT' . (int)$category->id . '">' . str_repeat('&nbsp;', 3 * (int)$category->level_depth) .
                    $category->name . '</option>';
        }
        elseif ($category->id != Configuration::get('PS_ROOT_CATEGORY'))
        {
            $html .= '<optgroup label="' . str_repeat('&nbsp;', 3 * (int)$category->level_depth) . $category->name .
                '">';
        }
        if (isset($children) && count($children))
            foreach ($children as $child)
            {
                $html .= $this->getCategoryOption((int)$child['id_category'], (int)$id_lang, (int)$child['id_shop'],
                    $recursive, $link);
            }
        return $html;
    }
    private function getAllDefaultLink($id_lang = null, $link = false)
    {
        if (is_null($id_lang)) $id_lang = (int)$this->context->language->id;
        $html = '<optgroup label="' . $this->l('Category') . '">';
        $html .= $this->getCategoryOption(1, $id_lang, false, true, $link);
        $html .= '</optgroup>';
        //CMS option
        $html .= '<optgroup label="' . $this->l('Cms') . '">';
        $html .= $this->getCMSOptions(0, 0, $id_lang, $link);
        $html .= '</optgroup>';
        //Manufacturer option
        $html .= '<optgroup label="' . $this->l('Manufacturer') . '">';
        $manufacturers = Manufacturer::getManufacturers(false, $id_lang);
        foreach ($manufacturers as $manufacturer)
        {
            if ($link) $html .= '<option value="' . $this->context->link->getManufacturerLink($manufacturer['id_manufacturer']) .
                    '">' . $manufacturer['name'] . '</option>';
            else  $html .= '<option value="MAN' . (int)$manufacturer['id_manufacturer'] . '">' . $manufacturer['name'] .
                    '</option>';
        }
        $html .= '</optgroup>';
        //Supplier option
        $html .= '<optgroup label="' . $this->l('Supplier') . '">';
        $suppliers = Supplier::getSuppliers(false, $id_lang);
        foreach ($suppliers as $supplier)
        {
            if ($link) $html .= '<option value="' . $this->context->link->getSupplierLink($supplier['id_supplier']) .
                    '">' . $supplier['name'] . '</option>';
            else  $html .= '<option value="SUP' . (int)$supplier['id_supplier'] . '">' . $supplier['name'] .
                    '</option>';
        }
        $html .= '</optgroup>';
        //Page option
        $html .= '<optgroup label="' . $this->l('Page') . '">';
        $html .= $this->getPagesOption($id_lang, $link);
        $shoplink = Shop::getShops();
        if (count($shoplink) > 1)
        {
            $html .= '<optgroup label="' . $this->l('Shops') . '">';
            foreach ($shoplink as $sh)
            {
                $html .= '<option value="SHO' . (int)$sh['id_shop'] . '">' . $sh['name'] . '</option>';
            }
        }
        $html .= '</optgroup>';
        return $html;
    }
    /**
     * Move an uploaded image to the module img/ folder
     */
    private function moveUploadedImage($file)
    {
        $img_name = time() . $file['name'];
        $main_name = $this->absolutePath . 'img' . DIRECTORY_SEPARATOR . $img_name;
        if (!move_uploaded_file($file['tmp_name'], $main_name))
        {
            return false;
        }
        return $img_name;
    }
    private function is_https()
    {
        return strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? true : false;
    }
    private function fomartLink($item = null, $id_lang = null)
    {
        if (is_null($item)) return;
        if (!empty($this->context->controller->php_self)) $page_name = $this->context->controller->php_self;
        else
        {
            $page_name = Dispatcher::getInstance()->getController();
            $page_name = (preg_match('/^[0-9]/', $page_name) ? 'page_' . $page_name : $page_name);
        }
        $html = '';
        $selected_item = false;
        if (is_null($id_lang)) $id_lang = (int)$this->context->language->id;
        $type = substr($item['link'], 0, 3);
        $key = substr($item['link'], 3, strlen($item['link']) - 3);
        switch ($type)
        {
            case 'CAT':
                if ($page_name == 'category' && (int)Tools::getValue('id_category') == (int)$key) $selected_item = true;
                $html = $this->context->link->getCategoryLink((int)$key, null, $id_lang);
                break;
            case 'CMS':
                if ($page_name == 'cms' && (int)Tools::getValue('id_cms') == (int)$key) $selected_item = true;
                $html = $this->context->link->getCMSLink((int)$key, null, $id_lang);
                break;
            case 'MAN':
                if ($page_name == 'manufacturer' && (int)Tools::getValue('id_manufacturer') == (int)$key) $selected_item = true;
                $man = new Manufacturer((int)$key, $id_lang);
                $html = $this->context->link->getManufacturerLink($man->id, $man->link_rewrite, $id_lang);
                break;
            case 'SUP':
                if ($page_name == 'supplier' && (int)Tools::getValue('id_supplier') == (int)$key) $selected_item = true;
                $sup = new Supplier((int)$key, $id_lang);
                $html = $this->context->link->getSupplierLink($sup->id, $sup->link_rewrite, $id_lang);
                break;
            case 'PAG':
                $pag = Meta::getMetaByPage($key, $id_lang);
                $html = $this->context->link->getPageLink($pag['page'], true, $id_lang);
                if ($page_name == $pag['page']) $selected_item = true;
                break;
            case 'SHO':
                $shop = new Shop((int)$key);
                $html = $shop->getBaseURL();
                break;
            default:
                $actual_link = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
                $html = $item['link'];
                if ($actual_link == $item['link']) $selected_item = true;
                break;
        }
        return array('link' => $html, 'selected_item' => $selected_item);
    }
    private function preHook()
    {
        $results = $this->getMainItem(true);
        if (count($results) > 0)
            foreach ($results as &$result)
            {
                $submenu = $this->getSupMenu($result['id_item'], true);
                if (count($submenu) > 0)
                {
                    $submenu = $submenu[0];
                    $blocks = $this->getAllBlocks($submenu['id_sub']);
                    if (count($blocks) > 0)
                    {
                        foreach ($blocks as &$block)
                        {
                            $items = $this->getItemByBlock($block['id_block'], true);
                            if (count($items) > 0)
                                foreach ($items as &$item)
                                {
                                    $checklink = $this->fomartLink($item);
                                    $item['link'] = $checklink['link'];
                                    $item['active'] = $checklink['selected_item'];
                                }
                            $block['items'] = $items;
                        }
                        $submenu['blocks'] = $blocks;
                        $result['submenu'] = $submenu;
                    }
                }
                $mainlink = $this->fomartLink($result);
                $result['link'] = $mainlink['link'];
                $result['active'] = $mainlink['selected_item'];
            }
        return $results;
    }
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') != $this->name) return;
        $this->context->controller->addCSS($this->_path . 'css/admin.css');
        $this->context->controller->addJquery();
        $this->context->controller->addJS(_PS_JS_DIR_ . 'tiny_mce/tiny_mce.js');
        $this->context->controller->addJS($this->_path . 'js/tinymce.inc.js');
        $this->context->controller->addJS($this->_path . 'js/jquery-ui.js');
        $this->context->controller->addJS($this->_path . 'js/topmenu_admin.js');
    }
    public function hookDisplayTop($param)
    {
        $cache_id = Tools::encrypt($_SERVER['REQUEST_URI']);
        if (!$this->isCached('topmenu.tpl', $this->getCacheId($cache_id)))
        {
            $this->smarty->assign(array('MENU' => $this->preHook(), 'absoluteUrl' => $this->absoluteUrl));
        }
        return $this->display(__file__, 'topmenu.tpl', $this->getCacheId($cache_id));
    }
    public function hookDisplayHeader($params)
    {
        $this->hookHeader($params);
    }
    public function hookHeader($params)
    {
        //$this->context->controller->addCSS(($this->_path) . 'css/topmenu.css', 'all');
        $this->context->controller->addJS(($this->_path) . 'js/top_menu.js');
    }
}
