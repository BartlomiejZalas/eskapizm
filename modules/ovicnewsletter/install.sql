DROP TABLE IF EXISTS `PREFIX_ovic_register_newsletter`;
CREATE TABLE `PREFIX_ovic_register_newsletter` (
  `id_shop` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `background` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `width` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id_shop`,`id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `PREFIX_ovic_register_newsletter` (`id_shop`, `id_lang`, `content`, `background`, `width`) VALUES
(1,	1,	'<p class=\"text-n1\">SIGN UP FOR OUR NEWSLETTER & PROMOTIONS !</p>\r\n<p class=\"text-n2\">GET</p>\r\n<p class=\"text-n3\">25</p>\r\n<p class=\"text-n4\"><span class=\"text-4\">%</span><br /> <span class=\"text-4\">OFF</span></p>\r\n<p class=\"text-n6\">ON YOUR NEXT PURCHASE</p>',	'background-1-1.jpg',	0),
(1,	2,	'<p class=\"text-n1\">SIGN UP FOR OUR NEWSLETTER & PROMOTIONS !</p>\r\n<p class=\"text-n2\">GET</p>\r\n<p class=\"text-n3\">25</p>\r\n<p class=\"text-n4\"><span class=\"text-4\">%</span><br /> <span class=\"text-4\">OFF</span></p>\r\n<p class=\"text-n6\">ON YOUR NEXT PURCHASE</p>',	'background-1-2.jpg',	0);
