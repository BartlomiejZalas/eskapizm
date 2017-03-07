
DROP TABLE IF EXISTS `PREFIX_simplecategory_group`;
CREATE TABLE `PREFIX_simplecategory_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `maxItem` tinyint(3) unsigned NOT NULL,
  `order_by` enum('seller','price','discount','date_add','position','review','view') COLLATE utf8_unicode_ci NOT NULL,
  `order_way` enum('asc','desc') COLLATE utf8_unicode_ci NOT NULL,
  `on_condition` enum('all','new','used','refurbished') COLLATE utf8_unicode_ci NOT NULL,
  `on_sale` tinyint(2) unsigned NOT NULL COMMENT '0 = off sale; 1 = on sale; 2 = all',
  `on_new` tinyint(3) unsigned NOT NULL COMMENT '0 = off new; 1 = new; 2 = all',
  `on_discount` tinyint(2) unsigned NOT NULL COMMENT '0 = no; 1 = yes; 2 = all',
  `type` enum('auto','manual') COLLATE utf8_unicode_ci NOT NULL,
  `ordering` int(10) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `custom_class` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `params` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `icon_active` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `PREFIX_simplecategory_group_lang`;
CREATE TABLE `PREFIX_simplecategory_group_lang` (
  `group_id` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `banners` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`group_id`,`id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `PREFIX_simplecategory_group_product`;
CREATE TABLE `PREFIX_simplecategory_group_product` (
  `group_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `ordering` int(10) unsigned NOT NULL,
  PRIMARY KEY (`group_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `PREFIX_simplecategory_module`;
CREATE TABLE `PREFIX_simplecategory_module` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` int(10) unsigned NOT NULL,
  `category_id` int(6) unsigned NOT NULL,
  `position_id` int(10) unsigned NOT NULL,
  `position_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `maxItem` tinyint(3) unsigned NOT NULL,
  `order_by` enum('seller','price','discount','date_add','position','review','view') COLLATE utf8_unicode_ci NOT NULL,
  `order_way` enum('asc','desc') COLLATE utf8_unicode_ci NOT NULL,
  `layout` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `custom_class` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` tinyint(3) unsigned NOT NULL,
  `on_condition` enum('all','new','used','refurbished') COLLATE utf8_unicode_ci NOT NULL,
  `on_sale` tinyint(3) unsigned NOT NULL,
  `on_new` tinyint(3) unsigned NOT NULL,
  `on_discount` tinyint(3) unsigned NOT NULL,
  `type` enum('auto','manual') COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `ordering` int(10) unsigned NOT NULL,
  `params` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `id_shop` (`id_shop`),
  KEY `status` (`status`),
  KEY `ordering` (`ordering`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `PREFIX_simplecategory_module_lang`;
CREATE TABLE `PREFIX_simplecategory_module_lang` (
  `module_id` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `banners` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`module_id`,`id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `PREFIX_simplecategory_module_position`;
CREATE TABLE `PREFIX_simplecategory_module_position` (
  `module_id` int(10) unsigned NOT NULL,
  `position_id` int(10) unsigned NOT NULL,
  `position_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`module_id`,`position_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `PREFIX_simplecategory_module_product`;
CREATE TABLE `PREFIX_simplecategory_module_product` (
  `module_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `ordering` int(10) unsigned NOT NULL,
  PRIMARY KEY (`module_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `PREFIX_simplecategory_product_view`;
CREATE TABLE `PREFIX_simplecategory_product_view` (
  `id_shop` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `total` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_shop`,`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

