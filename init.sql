CREATE TABLE IF NOT EXISTS  `yourls_url_platform_params` (
  `platform_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `platform_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `params` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`platform_id`),
  UNIQUE KEY `platform_name` (`platform_name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;