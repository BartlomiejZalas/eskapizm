DROP TABLE IF EXISTS `PREFIX_ovic_parallax`;
CREATE TABLE `PREFIX_ovic_parallax` (
  `id_parallax` int(6) NOT NULL AUTO_INCREMENT,
  `image` varchar(64) DEFAULT NULL,
  `ratio` float DEFAULT NULL,
  `module` varchar(64) DEFAULT NULL,
  `hook` varchar(64) DEFAULT NULL,
  `hook_postition` int(2) NOT NULL,
  `type` tinyint(1) unsigned DEFAULT '1',
  `active` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id_parallax`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `PREFIX_ovic_parallax_lang`;
CREATE TABLE `PREFIX_ovic_parallax_lang` (
  `id_parallax` int(6) NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `content` text,
  PRIMARY KEY (`id_parallax`,`id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `PREFIX_ovic_parallax_shop`;
CREATE TABLE `PREFIX_ovic_parallax_shop` (
  `id_parallax` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_parallax`,`id_shop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;