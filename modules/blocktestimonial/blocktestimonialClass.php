<?php
/*
* 2014 Leo Developer
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
*
*  @author Leo Developer
*  @copyright  2014 Leo Developer

*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class blocktestimonialClass extends ObjectModel
{
	/** @var integer testimonial id*/
	public $id;
	
	/** @var integer testimonial id shop*/
	public $id_shop;
	
	/** @var string testimonial file name icon*/
	public $file_name;
    
    /** @var list products id of testimonial text*/
    public $list_products_id;

	/** @var string testimonial text*/
	public $text;
    
	/** @var string designer name*/
	public $name;
    
    /** @var string testimonial name*/
	public $name_testimonial;

    /** @var string testimonial company*/
	public $company;


	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'block_testimonials',
		'primary' => 'id_testimonial',
		'multilang' => true,
		'fields' => array(
			'id_shop' =>				array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
			'file_name' =>				array('type' => self::TYPE_STRING, 'validate' => 'isFileName'),
			// Lang fields
			'text' =>					array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true),
            'name' =>					array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true),
            'company' =>				array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true),
		)
	);

	public function copyFromPost()
	{
		/* Classical fields */
		foreach ($_POST AS $key => $value)
			if (key_exists($key, $this) AND $key != 'id_'.$this->table)
				$this->{$key} = $value;

		/* Multilingual fields */
		if (sizeof($this->fieldsValidateLang))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages AS $language)
				foreach ($this->fieldsValidateLang AS $field => $validation)
					if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
						$this->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
		}
	}
}
