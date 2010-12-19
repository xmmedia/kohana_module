-- Additional SQL to use all the features of the xmmedia module

-- Change log table
CREATE TABLE `change_log` (
  `id` int(11) NOT NULL auto_increment,
  `event_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `user_id` int(11) NOT NULL,
  `table_name` varchar(64) collate utf8_unicode_ci NOT NULL,
  `record_pk` int(11) NOT NULL,
  `query_type` varchar(12) collate utf8_unicode_ci NOT NULL,
  `row_count` int(11) NOT NULL,
  `query_time` decimal(13,6) NOT NULL,
  `sql` varchar(15000) collate utf8_unicode_ci NOT NULL,
  `changed` varchar(5000) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `table_name` (`table_name`),
  KEY `query_type` (`query_type`),
  KEY `record_pk` (`record_pk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Config table
CREATE TABLE `config` (
  `id` int(11) NOT NULL auto_increment,
  `setting` varchar(50) collate utf8_unicode_ci NOT NULL,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `value` varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `setting` (`setting`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Contact table
CREATE TABLE `contact` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `email` varchar(100) collate utf8_unicode_ci NOT NULL,
  `phone` varchar(15) collate utf8_unicode_ci NOT NULL,
  `message` text collate utf8_unicode_ci NOT NULL,
  `date_submitted` datetime NOT NULL,
  `ip_address` varchar(15) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;