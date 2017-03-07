<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_0($object)
{
		$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
		//Db::getInstance()->escape($string)
		$items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("Select * From "._DB_PREFIX_."simplecategory_module_lang");
		if($items){
			foreach($items as $item){
				$description = @Tools::htmlentitiesDecodeUTF8($item['description']);
				if(isset($description) && $description){
									
					Db::getInstance()->update('simplecategory_module_lang', array('description'=>$db->escape($description, true)), "`module_id`='".$item['module_id']."' AND `id_lang`='".$item['id_lang']."'");	
				}
			}
		}
		$items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("Select * From "._DB_PREFIX_."simplecategory_group_lang");
		if($items){
			foreach($items as $item){
				$description = @Tools::htmlentitiesDecodeUTF8($item['description']);
				if(isset($description) && $description){					 				
					Db::getInstance()->update('simplecategory_group_lang', array('description'=>$db->escape($description, true)), "`group_id`='".$item['group_id']."' AND `id_lang`='".$item['id_lang']."'");	
				}
				
			}
		}		
		return true;
}
