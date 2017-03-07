<?php
class Product extends ProductCore{
	
	/*
    * module: simplecategory
    * date: 2017-03-07 21:33:24
    * version: 2.0
    */
    public static function getRatings($id_product)
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
}
