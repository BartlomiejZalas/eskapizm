<?php if (!defined('_PS_VERSION_')) exit;
class Parallax extends ObjectModel
{
    /**
     @var integer id_parallax */
    public $id_parallax;
    /**
     @var int active status */
    public $active;
    /**
     @var int active status */
    public $type;
    /**
     @var int hook_postition */
    public $hook_postition;
    /**
     @var string image  */
    public $image;
    /**
     @var float ratio  */
    public $ratio;
    /**
     @var string image  */
    public $module;
    /**
     @var string image  */
    public $hook;
    /**
     @var string content  */
    public $content;
      
    public static $definition = array(
        'table' => 'ovic_parallax',
        'primary' => 'id_parallax',
        'multilang' => true,
        'fields' => array(
            'type'           =>  array('type' => self::TYPE_BOOL, 'validate' => 'isBool','required' => true),
            'hook_postition' =>  array('type' => self::TYPE_INT, 'validate' => 'isInt','required' => true),
            'active'         =>  array('type' => self::TYPE_BOOL,'validate' => 'isBool','required' => true),
            'image'          =>	 array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'ratio'          =>  array('type' => self::TYPE_FLOAT,'validate' => 'isFloat','required' => true),
            'module'         =>	 array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'hook'           =>	 array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            // Lang fields
            'content'        =>	 array('type' => self::TYPE_HTML, 'lang' => true,'validate' => 'isString'))
        );
    public function add($autodate = true, $null_values = false)
    {
        $context = Context::getContext();
        $id_shop = $context->shop->id;
        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute('
			INSERT INTO `' . _DB_PREFIX_ . 'ovic_parallax_shop` (`id_shop`, `id_parallax`)
			VALUES(' . (int)$id_shop . ', ' . (int)$this->id . ')');
        return $res;
    }
    public function delete()
    {
        $res = true;
        $image = $this->image;
        if ($image && file_exists(dirname(__file__) . '/img/' . $image))
            $res &= @unlink(dirname(__file__) .'/img/' . $image);
        $res &= Db::getInstance()->execute('
			DELETE FROM `' . _DB_PREFIX_ . 'ovic_parallax_shop`
			WHERE `id_parallax` = ' . (int)$this->id);
        $res &= parent::delete();
        return $res;
    }
}
