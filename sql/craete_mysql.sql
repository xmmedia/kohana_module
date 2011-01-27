-- Additional SQL to use all the features of the xmmedia module

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



-- Country table and countries
CREATE TABLE `country` (
  `id` int(11) NOT NULL auto_increment,
  `expiry_date` datetime NOT NULL,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `symbol` varchar(5) collate utf8_unicode_ci NOT NULL,
  `exchange_rate` decimal(11,5) NOT NULL default '1.00000',
  `code` char(3) collate utf8_unicode_ci NOT NULL,
  `currency_code` varchar(3) collate utf8_unicode_ci NOT NULL,
  `display_order` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `code` (`code`),
  KEY `display_order` (`display_order`),
  KEY `date_expired` (`expiry_date`),
  KEY `currency_code` (`currency_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `country` VALUES(1, '0000-00-00 00:00:00', 'Afghanistan', '', '1.00000', 'AF\0', '', 100);
INSERT INTO `country` VALUES(2, '0000-00-00 00:00:00', 'Albania', '', '1.00000', 'AL\0', '', 100);
INSERT INTO `country` VALUES(3, '0000-00-00 00:00:00', 'Algeria', '', '1.00000', 'DZ\0', '', 100);
INSERT INTO `country` VALUES(4, '0000-00-00 00:00:00', 'American Samoa', '', '1.00000', 'AS\0', '', 100);
INSERT INTO `country` VALUES(5, '0000-00-00 00:00:00', 'Andorra', '', '1.00000', 'AD\0', '', 100);
INSERT INTO `country` VALUES(6, '0000-00-00 00:00:00', 'Angola', '', '1.00000', 'AO\0', '', 100);
INSERT INTO `country` VALUES(7, '0000-00-00 00:00:00', 'Anguilla', '', '1.00000', 'AI\0', '', 100);
INSERT INTO `country` VALUES(8, '0000-00-00 00:00:00', 'Antarctica', '', '1.00000', 'AQ\0', '', 100);
INSERT INTO `country` VALUES(9, '0000-00-00 00:00:00', 'Antigua and Barbuda', '', '1.00000', 'AG\0', '', 100);
INSERT INTO `country` VALUES(10, '0000-00-00 00:00:00', 'Argentina', '', '1.00000', 'AR\0', '', 100);
INSERT INTO `country` VALUES(11, '0000-00-00 00:00:00', 'Armenia', '', '1.00000', 'AM\0', '', 100);
INSERT INTO `country` VALUES(12, '0000-00-00 00:00:00', 'Aruba', '', '1.00000', 'AW\0', '', 100);
INSERT INTO `country` VALUES(13, '0000-00-00 00:00:00', 'Australia', '', '1.00000', 'AU\0', '', 100);
INSERT INTO `country` VALUES(14, '0000-00-00 00:00:00', 'Austria', '$', '1.00000', 'AT\0', 'AUD', 100);
INSERT INTO `country` VALUES(15, '0000-00-00 00:00:00', 'Azerbaijan', '', '1.00000', 'AZ\0', '', 100);
INSERT INTO `country` VALUES(16, '0000-00-00 00:00:00', 'Bahamas', '', '1.00000', 'BS\0', '', 100);
INSERT INTO `country` VALUES(17, '0000-00-00 00:00:00', 'Bahrain', '', '1.00000', 'BH\0', '', 100);
INSERT INTO `country` VALUES(18, '0000-00-00 00:00:00', 'Bangladesh', '', '1.00000', 'BD\0', '', 100);
INSERT INTO `country` VALUES(19, '0000-00-00 00:00:00', 'Barbados', '', '1.00000', 'BB\0', '', 100);
INSERT INTO `country` VALUES(20, '0000-00-00 00:00:00', 'Belarus', '', '1.00000', 'BY\0', '', 100);
INSERT INTO `country` VALUES(21, '0000-00-00 00:00:00', 'Belgium', '', '1.00000', 'BE\0', '', 100);
INSERT INTO `country` VALUES(22, '0000-00-00 00:00:00', 'Belize', '', '1.00000', 'BZ\0', '', 100);
INSERT INTO `country` VALUES(23, '0000-00-00 00:00:00', 'Benin', '', '1.00000', 'BJ\0', '', 100);
INSERT INTO `country` VALUES(24, '0000-00-00 00:00:00', 'Bermuda', '', '1.00000', 'BM\0', '', 100);
INSERT INTO `country` VALUES(25, '0000-00-00 00:00:00', 'Bhutan', '', '1.00000', 'BT\0', '', 100);
INSERT INTO `country` VALUES(26, '0000-00-00 00:00:00', 'Bolivia', '', '1.00000', 'BO\0', '', 100);
INSERT INTO `country` VALUES(27, '0000-00-00 00:00:00', 'Bosnia and Herzegovina', '', '1.00000', 'BA\0', '', 100);
INSERT INTO `country` VALUES(28, '0000-00-00 00:00:00', 'Botswana', '', '1.00000', 'BW\0', '', 100);
INSERT INTO `country` VALUES(29, '0000-00-00 00:00:00', 'Bouvet Island', '', '1.00000', 'BV\0', '', 100);
INSERT INTO `country` VALUES(30, '0000-00-00 00:00:00', 'Brazil', '', '1.00000', 'BR\0', '', 100);
INSERT INTO `country` VALUES(31, '0000-00-00 00:00:00', 'British Indian Ocean Territory', '', '1.00000', 'IO\0', '', 100);
INSERT INTO `country` VALUES(32, '0000-00-00 00:00:00', 'Brunei Darussalam', '', '1.00000', 'BN\0', '', 100);
INSERT INTO `country` VALUES(33, '0000-00-00 00:00:00', 'Bulgaria', '', '1.00000', 'BG\0', '', 100);
INSERT INTO `country` VALUES(34, '0000-00-00 00:00:00', 'Burkina Faso', '', '1.00000', 'BF\0', '', 100);
INSERT INTO `country` VALUES(35, '0000-00-00 00:00:00', 'Burundi', '', '1.00000', 'BI\0', '', 100);
INSERT INTO `country` VALUES(36, '0000-00-00 00:00:00', 'Cambodia', '', '1.00000', 'KH\0', '', 100);
INSERT INTO `country` VALUES(37, '0000-00-00 00:00:00', 'Cameroon', '', '1.00000', 'CM\0', '', 100);
INSERT INTO `country` VALUES(38, '0000-00-00 00:00:00', 'Canada', '$', '1.00000', 'CA', 'CAD', 1);
INSERT INTO `country` VALUES(39, '0000-00-00 00:00:00', 'Cape Verde', '', '1.00000', 'CV\0', '', 100);
INSERT INTO `country` VALUES(40, '0000-00-00 00:00:00', 'Cayman Islands', '', '1.00000', 'KY\0', '', 100);
INSERT INTO `country` VALUES(41, '0000-00-00 00:00:00', 'Central African Republic', '', '1.00000', 'CF\0', '', 100);
INSERT INTO `country` VALUES(42, '0000-00-00 00:00:00', 'Chad', '', '1.00000', 'TD\0', '', 100);
INSERT INTO `country` VALUES(43, '0000-00-00 00:00:00', 'Chile', '', '1.00000', 'CL\0', '', 100);
INSERT INTO `country` VALUES(44, '0000-00-00 00:00:00', 'China', '', '1.00000', 'CN\0', '', 100);
INSERT INTO `country` VALUES(45, '0000-00-00 00:00:00', 'Christmas Island', '', '1.00000', 'CX\0', '', 100);
INSERT INTO `country` VALUES(46, '0000-00-00 00:00:00', 'Cocos (keeling) Islands', '', '1.00000', 'CC\0', '', 100);
INSERT INTO `country` VALUES(47, '0000-00-00 00:00:00', 'Colombia', '', '1.00000', 'CO\0', '', 100);
INSERT INTO `country` VALUES(48, '0000-00-00 00:00:00', 'Comoros', '', '1.00000', 'KM\0', '', 100);
INSERT INTO `country` VALUES(49, '0000-00-00 00:00:00', 'Congo', '', '1.00000', 'CG\0', '', 100);
INSERT INTO `country` VALUES(50, '0000-00-00 00:00:00', 'Congo, The Democratic Republic Of The', '', '1.00000', 'CD\0', '', 100);
INSERT INTO `country` VALUES(51, '0000-00-00 00:00:00', 'Cook Islands', '', '1.00000', 'CK\0', '', 100);
INSERT INTO `country` VALUES(52, '0000-00-00 00:00:00', 'Costa Rica', '', '1.00000', 'CR\0', '', 100);
INSERT INTO `country` VALUES(53, '0000-00-00 00:00:00', 'Cote D''ivoire', '', '1.00000', 'CI\0', '', 100);
INSERT INTO `country` VALUES(54, '0000-00-00 00:00:00', 'Croatia', '', '1.00000', 'HR\0', '', 100);
INSERT INTO `country` VALUES(55, '0000-00-00 00:00:00', 'Cuba', '', '1.00000', 'CU\0', '', 100);
INSERT INTO `country` VALUES(56, '0000-00-00 00:00:00', 'Cyprus', '', '1.00000', 'CY\0', '', 100);
INSERT INTO `country` VALUES(57, '0000-00-00 00:00:00', 'Czech Republic', '', '1.00000', 'CZ\0', 'CZK', 100);
INSERT INTO `country` VALUES(58, '0000-00-00 00:00:00', 'Denmark', '', '1.00000', 'DK\0', 'DKK', 100);
INSERT INTO `country` VALUES(59, '0000-00-00 00:00:00', 'Djibouti', '', '1.00000', 'DJ\0', '', 100);
INSERT INTO `country` VALUES(60, '0000-00-00 00:00:00', 'Dominica', '', '1.00000', 'DM\0', '', 100);
INSERT INTO `country` VALUES(61, '0000-00-00 00:00:00', 'Dominican Republic', '', '1.00000', 'DO\0', '', 100);
INSERT INTO `country` VALUES(62, '0000-00-00 00:00:00', 'Ecuador', '', '1.00000', 'EC\0', '', 100);
INSERT INTO `country` VALUES(63, '0000-00-00 00:00:00', 'Egypt', '', '1.00000', 'EG\0', '', 100);
INSERT INTO `country` VALUES(64, '0000-00-00 00:00:00', 'El Salvador', '', '1.00000', 'SV\0', '', 100);
INSERT INTO `country` VALUES(65, '0000-00-00 00:00:00', 'Equatorial Guinea', '', '1.00000', 'GQ\0', '', 100);
INSERT INTO `country` VALUES(66, '0000-00-00 00:00:00', 'Eritrea', '', '1.00000', 'ER\0', '', 100);
INSERT INTO `country` VALUES(67, '0000-00-00 00:00:00', 'Estonia', '', '1.00000', 'EE\0', '', 100);
INSERT INTO `country` VALUES(68, '0000-00-00 00:00:00', 'Ethiopia', '', '1.00000', 'ET\0', '', 100);
INSERT INTO `country` VALUES(69, '0000-00-00 00:00:00', 'Falkland Islands (malvinas)', '', '1.00000', 'FK\0', '', 100);
INSERT INTO `country` VALUES(70, '0000-00-00 00:00:00', 'Faroe Islands', '', '1.00000', 'FO\0', '', 100);
INSERT INTO `country` VALUES(71, '0000-00-00 00:00:00', 'Fiji', '', '1.00000', 'FJ\0', '', 100);
INSERT INTO `country` VALUES(72, '0000-00-00 00:00:00', 'Finland', '', '1.00000', 'FI\0', '', 100);
INSERT INTO `country` VALUES(73, '0000-00-00 00:00:00', 'France', '', '1.00000', 'FR\0', '', 100);
INSERT INTO `country` VALUES(74, '0000-00-00 00:00:00', 'French Guiana', '', '1.00000', 'GF\0', '', 100);
INSERT INTO `country` VALUES(75, '0000-00-00 00:00:00', 'French Polynesia', '', '1.00000', 'PF\0', '', 100);
INSERT INTO `country` VALUES(76, '0000-00-00 00:00:00', 'French Southern Territories', '', '1.00000', 'TF\0', '', 100);
INSERT INTO `country` VALUES(77, '0000-00-00 00:00:00', 'Gabon', '', '1.00000', 'GA\0', '', 100);
INSERT INTO `country` VALUES(78, '0000-00-00 00:00:00', 'Gambia', '', '1.00000', 'GM\0', '', 100);
INSERT INTO `country` VALUES(79, '0000-00-00 00:00:00', 'Georgia', '', '1.00000', 'GE\0', '', 100);
INSERT INTO `country` VALUES(80, '0000-00-00 00:00:00', 'Germany', '', '1.00000', 'DE\0', '', 100);
INSERT INTO `country` VALUES(81, '0000-00-00 00:00:00', 'Ghana', '', '1.00000', 'GH\0', '', 100);
INSERT INTO `country` VALUES(82, '0000-00-00 00:00:00', 'Gibraltar', '', '1.00000', 'GI\0', '', 100);
INSERT INTO `country` VALUES(83, '0000-00-00 00:00:00', 'Greece', '', '1.00000', 'GR\0', '', 100);
INSERT INTO `country` VALUES(84, '0000-00-00 00:00:00', 'Greenland', '', '1.00000', 'GL\0', '', 100);
INSERT INTO `country` VALUES(85, '0000-00-00 00:00:00', 'Grenada', '', '1.00000', 'GD\0', '', 100);
INSERT INTO `country` VALUES(86, '0000-00-00 00:00:00', 'Guadeloupe', '', '1.00000', 'GP\0', '', 100);
INSERT INTO `country` VALUES(87, '0000-00-00 00:00:00', 'Guam', '', '1.00000', 'GU\0', '', 100);
INSERT INTO `country` VALUES(88, '0000-00-00 00:00:00', 'Guatemala', '', '1.00000', 'GT\0', '', 100);
INSERT INTO `country` VALUES(89, '0000-00-00 00:00:00', 'Guinea', '', '1.00000', 'GN\0', '', 100);
INSERT INTO `country` VALUES(90, '0000-00-00 00:00:00', 'Guinea-bissau', '', '1.00000', 'GW\0', '', 100);
INSERT INTO `country` VALUES(91, '0000-00-00 00:00:00', 'Guyana', '', '1.00000', 'GY\0', '', 100);
INSERT INTO `country` VALUES(92, '0000-00-00 00:00:00', 'Haiti', '', '1.00000', 'HT\0', '', 100);
INSERT INTO `country` VALUES(93, '0000-00-00 00:00:00', 'Heard Island and Mcdonald Islands', '', '1.00000', 'HM\0', '', 100);
INSERT INTO `country` VALUES(94, '0000-00-00 00:00:00', 'Holy See (vatican City State)', '', '1.00000', 'VA\0', '', 100);
INSERT INTO `country` VALUES(95, '0000-00-00 00:00:00', 'Honduras', '', '1.00000', 'HN\0', '', 100);
INSERT INTO `country` VALUES(96, '0000-00-00 00:00:00', 'Hong Kong', '', '1.00000', 'HK\0', 'HKD', 100);
INSERT INTO `country` VALUES(97, '0000-00-00 00:00:00', 'Hungary', '', '1.00000', 'HU\0', 'HUF', 100);
INSERT INTO `country` VALUES(98, '0000-00-00 00:00:00', 'Iceland', '', '1.00000', 'IS\0', '', 100);
INSERT INTO `country` VALUES(99, '0000-00-00 00:00:00', 'India', '', '1.00000', 'IN\0', '', 100);
INSERT INTO `country` VALUES(100, '0000-00-00 00:00:00', 'Indonesia', '', '1.00000', 'ID\0', '', 100);
INSERT INTO `country` VALUES(101, '0000-00-00 00:00:00', 'Iran, Islamic Republic Of', '', '1.00000', 'IR\0', '', 100);
INSERT INTO `country` VALUES(102, '0000-00-00 00:00:00', 'Iraq', '', '1.00000', 'IQ\0', '', 100);
INSERT INTO `country` VALUES(103, '0000-00-00 00:00:00', 'Ireland', '', '1.00000', 'IE\0', '', 100);
INSERT INTO `country` VALUES(104, '0000-00-00 00:00:00', 'Israel', '', '1.00000', 'IL\0', '', 100);
INSERT INTO `country` VALUES(105, '0000-00-00 00:00:00', 'Italy', '', '1.00000', 'IT\0', '', 100);
INSERT INTO `country` VALUES(106, '0000-00-00 00:00:00', 'Jamaica', '', '1.00000', 'JM\0', '', 100);
INSERT INTO `country` VALUES(107, '0000-00-00 00:00:00', 'Japan', '', '1.00000', 'JP\0', 'JPY', 100);
INSERT INTO `country` VALUES(108, '0000-00-00 00:00:00', 'Jordan', '', '1.00000', 'JO\0', '', 100);
INSERT INTO `country` VALUES(109, '0000-00-00 00:00:00', 'Kazakhstan', '', '1.00000', 'KZ\0', '', 100);
INSERT INTO `country` VALUES(110, '0000-00-00 00:00:00', 'Kenya', '', '1.00000', 'KE\0', '', 100);
INSERT INTO `country` VALUES(111, '0000-00-00 00:00:00', 'Kiribati', '', '1.00000', 'KI\0', '', 100);
INSERT INTO `country` VALUES(112, '0000-00-00 00:00:00', 'Korea, Democratic People''s Republic Of', '', '1.00000', 'KP\0', '', 100);
INSERT INTO `country` VALUES(113, '0000-00-00 00:00:00', 'Korea, Republic Of', '', '1.00000', 'KR\0', '', 100);
INSERT INTO `country` VALUES(114, '0000-00-00 00:00:00', 'Kuwait', '', '1.00000', 'KW\0', '', 100);
INSERT INTO `country` VALUES(115, '0000-00-00 00:00:00', 'Kyrgyzstan', '', '1.00000', 'KG\0', '', 100);
INSERT INTO `country` VALUES(116, '0000-00-00 00:00:00', 'Lao People''s Democratic Republic', '', '1.00000', 'LA\0', '', 100);
INSERT INTO `country` VALUES(117, '0000-00-00 00:00:00', 'Latvia', '', '1.00000', 'LV\0', '', 100);
INSERT INTO `country` VALUES(118, '0000-00-00 00:00:00', 'Lebanon', '', '1.00000', 'LB\0', '', 100);
INSERT INTO `country` VALUES(119, '0000-00-00 00:00:00', 'Lesotho', '', '1.00000', 'LS\0', '', 100);
INSERT INTO `country` VALUES(120, '0000-00-00 00:00:00', 'Liberia', '', '1.00000', 'LR\0', '', 100);
INSERT INTO `country` VALUES(121, '0000-00-00 00:00:00', 'Libyan Arab Jamahiriya', '', '1.00000', 'LY\0', '', 100);
INSERT INTO `country` VALUES(122, '0000-00-00 00:00:00', 'Liechtenstein', '', '1.00000', 'LI\0', '', 100);
INSERT INTO `country` VALUES(123, '0000-00-00 00:00:00', 'Lithuania', '', '1.00000', 'LT\0', '', 100);
INSERT INTO `country` VALUES(124, '0000-00-00 00:00:00', 'Luxembourg', '', '1.00000', 'LU\0', '', 100);
INSERT INTO `country` VALUES(125, '0000-00-00 00:00:00', 'Macao', '', '1.00000', 'MO\0', '', 100);
INSERT INTO `country` VALUES(126, '0000-00-00 00:00:00', 'Macedonia, The Former Yugoslav Republic Of', '', '1.00000', 'MK\0', '', 100);
INSERT INTO `country` VALUES(127, '0000-00-00 00:00:00', 'Madagascar', '', '1.00000', 'MG\0', '', 100);
INSERT INTO `country` VALUES(128, '0000-00-00 00:00:00', 'Malawi', '', '1.00000', 'MW\0', '', 100);
INSERT INTO `country` VALUES(129, '0000-00-00 00:00:00', 'Malaysia', '', '1.00000', 'MY\0', '', 100);
INSERT INTO `country` VALUES(130, '0000-00-00 00:00:00', 'Maldives', '', '1.00000', 'MV\0', '', 100);
INSERT INTO `country` VALUES(131, '0000-00-00 00:00:00', 'Mali', '', '1.00000', 'ML\0', '', 100);
INSERT INTO `country` VALUES(132, '0000-00-00 00:00:00', 'Malta', '', '1.00000', 'MT\0', '', 100);
INSERT INTO `country` VALUES(133, '0000-00-00 00:00:00', 'Marshall Islands', '', '1.00000', 'MH\0', '', 100);
INSERT INTO `country` VALUES(134, '0000-00-00 00:00:00', 'Martinique', '', '1.00000', 'MQ\0', '', 100);
INSERT INTO `country` VALUES(135, '0000-00-00 00:00:00', 'Mauritania', '', '1.00000', 'MR\0', '', 100);
INSERT INTO `country` VALUES(136, '0000-00-00 00:00:00', 'Mauritius', '', '1.00000', 'MU\0', '', 100);
INSERT INTO `country` VALUES(137, '0000-00-00 00:00:00', 'Mayotte', '', '1.00000', 'YT\0', '', 100);
INSERT INTO `country` VALUES(138, '0000-00-00 00:00:00', 'Mexico', '', '1.00000', 'MX\0', '', 100);
INSERT INTO `country` VALUES(139, '0000-00-00 00:00:00', 'Micronesia, Federated States Of', '', '1.00000', 'FM\0', '', 100);
INSERT INTO `country` VALUES(140, '0000-00-00 00:00:00', 'Moldova, Republic Of', '', '1.00000', 'MD\0', '', 100);
INSERT INTO `country` VALUES(141, '0000-00-00 00:00:00', 'Monaco', '', '1.00000', 'MC\0', '', 100);
INSERT INTO `country` VALUES(142, '0000-00-00 00:00:00', 'Mongolia', '', '1.00000', 'MN\0', '', 100);
INSERT INTO `country` VALUES(143, '0000-00-00 00:00:00', 'Montserrat', '', '1.00000', 'MS\0', '', 100);
INSERT INTO `country` VALUES(144, '0000-00-00 00:00:00', 'Morocco', '', '1.00000', 'MA\0', '', 100);
INSERT INTO `country` VALUES(145, '0000-00-00 00:00:00', 'Mozambique', '', '1.00000', 'MZ\0', '', 100);
INSERT INTO `country` VALUES(146, '0000-00-00 00:00:00', 'Myanmar', '', '1.00000', 'MM\0', '', 100);
INSERT INTO `country` VALUES(147, '0000-00-00 00:00:00', 'Namibia', '', '1.00000', 'NA\0', '', 100);
INSERT INTO `country` VALUES(148, '0000-00-00 00:00:00', 'Nauru', '', '1.00000', 'NR\0', '', 100);
INSERT INTO `country` VALUES(149, '0000-00-00 00:00:00', 'Nepal', '', '1.00000', 'NP\0', '', 100);
INSERT INTO `country` VALUES(150, '0000-00-00 00:00:00', 'Netherlands', '', '1.00000', 'NL\0', '', 100);
INSERT INTO `country` VALUES(151, '0000-00-00 00:00:00', 'Netherlands Antilles', '', '1.00000', 'AN\0', '', 100);
INSERT INTO `country` VALUES(152, '0000-00-00 00:00:00', 'New Caledonia', '', '1.00000', 'NC\0', '', 100);
INSERT INTO `country` VALUES(153, '0000-00-00 00:00:00', 'New Zealand', '', '1.00000', 'NZ\0', 'NZD', 100);
INSERT INTO `country` VALUES(154, '0000-00-00 00:00:00', 'Nicaragua', '', '1.00000', 'NI\0', '', 100);
INSERT INTO `country` VALUES(155, '0000-00-00 00:00:00', 'Niger', '', '1.00000', 'NE\0', '', 100);
INSERT INTO `country` VALUES(156, '0000-00-00 00:00:00', 'Nigeria', '', '1.00000', 'NG\0', '', 100);
INSERT INTO `country` VALUES(157, '0000-00-00 00:00:00', 'Niue', '', '1.00000', 'NU\0', '', 100);
INSERT INTO `country` VALUES(158, '0000-00-00 00:00:00', 'Norfolk Island', '', '1.00000', 'NF\0', '', 100);
INSERT INTO `country` VALUES(159, '0000-00-00 00:00:00', 'Northern Mariana Islands', '', '1.00000', 'MP\0', '', 100);
INSERT INTO `country` VALUES(160, '0000-00-00 00:00:00', 'Norway', '', '1.00000', 'NO\0', 'NOK', 100);
INSERT INTO `country` VALUES(161, '0000-00-00 00:00:00', 'Oman', '', '1.00000', 'OM\0', '', 100);
INSERT INTO `country` VALUES(162, '0000-00-00 00:00:00', 'Pakistan', '', '1.00000', 'PK\0', '', 100);
INSERT INTO `country` VALUES(163, '0000-00-00 00:00:00', 'Palau', '', '1.00000', 'PW\0', '', 100);
INSERT INTO `country` VALUES(164, '0000-00-00 00:00:00', 'Palestinian Territory, Occupied', '', '1.00000', 'PS\0', '', 100);
INSERT INTO `country` VALUES(165, '0000-00-00 00:00:00', 'Panama', '', '1.00000', 'PA\0', '', 100);
INSERT INTO `country` VALUES(166, '0000-00-00 00:00:00', 'Papua New Guinea', '', '1.00000', 'PG\0', '', 100);
INSERT INTO `country` VALUES(167, '0000-00-00 00:00:00', 'Paraguay', '', '1.00000', 'PY\0', '', 100);
INSERT INTO `country` VALUES(168, '0000-00-00 00:00:00', 'Peru', '', '1.00000', 'PE\0', '', 100);
INSERT INTO `country` VALUES(169, '0000-00-00 00:00:00', 'Philippines', '', '1.00000', 'PH\0', '', 100);
INSERT INTO `country` VALUES(170, '0000-00-00 00:00:00', 'Pitcairn', '', '1.00000', 'PN\0', '', 100);
INSERT INTO `country` VALUES(171, '0000-00-00 00:00:00', 'Poland', '', '1.00000', 'PL\0', 'PLN', 100);
INSERT INTO `country` VALUES(172, '0000-00-00 00:00:00', 'Portugal', '', '1.00000', 'PT\0', '', 100);
INSERT INTO `country` VALUES(173, '0000-00-00 00:00:00', 'Puerto Rico', '', '1.00000', 'PR\0', '', 100);
INSERT INTO `country` VALUES(174, '0000-00-00 00:00:00', 'Qatar', '', '1.00000', 'QA\0', '', 100);
INSERT INTO `country` VALUES(175, '0000-00-00 00:00:00', 'Reunion', '', '1.00000', 'RE\0', '', 100);
INSERT INTO `country` VALUES(176, '0000-00-00 00:00:00', 'Romania', '', '1.00000', 'RO\0', '', 100);
INSERT INTO `country` VALUES(177, '0000-00-00 00:00:00', 'Russian Federation', '', '1.00000', 'RU\0', '', 100);
INSERT INTO `country` VALUES(178, '0000-00-00 00:00:00', 'Rwanda', '', '1.00000', 'RW\0', '', 100);
INSERT INTO `country` VALUES(179, '0000-00-00 00:00:00', 'Saint Helena', '', '1.00000', 'SH\0', '', 100);
INSERT INTO `country` VALUES(180, '0000-00-00 00:00:00', 'Saint Kitts and Nevis', '', '1.00000', 'KN\0', '', 100);
INSERT INTO `country` VALUES(181, '0000-00-00 00:00:00', 'Saint Lucia', '', '1.00000', 'LC\0', '', 100);
INSERT INTO `country` VALUES(182, '0000-00-00 00:00:00', 'Saint Pierre and Miquelon', '', '1.00000', 'PM\0', '', 100);
INSERT INTO `country` VALUES(183, '0000-00-00 00:00:00', 'Saint Vincent and The Grenadines', '', '1.00000', 'VC\0', '', 100);
INSERT INTO `country` VALUES(184, '0000-00-00 00:00:00', 'Samoa', '', '1.00000', 'WS\0', '', 100);
INSERT INTO `country` VALUES(185, '0000-00-00 00:00:00', 'San Marino', '', '1.00000', 'SM\0', '', 100);
INSERT INTO `country` VALUES(186, '0000-00-00 00:00:00', 'Sao Tome and Principe', '', '1.00000', 'ST\0', '', 100);
INSERT INTO `country` VALUES(187, '0000-00-00 00:00:00', 'Saudi Arabia', '', '1.00000', 'SA\0', '', 100);
INSERT INTO `country` VALUES(188, '0000-00-00 00:00:00', 'Senegal', '', '1.00000', 'SN\0', '', 100);
INSERT INTO `country` VALUES(189, '0000-00-00 00:00:00', 'Serbia and Montenegro', '', '1.00000', 'CS\0', '', 100);
INSERT INTO `country` VALUES(190, '0000-00-00 00:00:00', 'Seychelles', '', '1.00000', 'SC\0', '', 100);
INSERT INTO `country` VALUES(191, '0000-00-00 00:00:00', 'Sierra Leone', '', '1.00000', 'SL\0', '', 100);
INSERT INTO `country` VALUES(192, '0000-00-00 00:00:00', 'Singapore', '', '1.00000', 'SG\0', 'SGD', 100);
INSERT INTO `country` VALUES(193, '0000-00-00 00:00:00', 'Slovakia', '', '1.00000', 'SK\0', '', 100);
INSERT INTO `country` VALUES(194, '0000-00-00 00:00:00', 'Slovenia', '', '1.00000', 'SI\0', '', 100);
INSERT INTO `country` VALUES(195, '0000-00-00 00:00:00', 'Solomon Islands', '', '1.00000', 'SB\0', '', 100);
INSERT INTO `country` VALUES(196, '0000-00-00 00:00:00', 'Somalia', '', '1.00000', 'SO\0', '', 100);
INSERT INTO `country` VALUES(197, '0000-00-00 00:00:00', 'South Africa', '', '1.00000', 'ZA\0', '', 100);
INSERT INTO `country` VALUES(198, '0000-00-00 00:00:00', 'South Georgia and The South Sandwich Islands', '', '1.00000', 'GS\0', '', 100);
INSERT INTO `country` VALUES(199, '0000-00-00 00:00:00', 'Spain', '', '1.00000', 'ES\0', '', 100);
INSERT INTO `country` VALUES(200, '0000-00-00 00:00:00', 'Sri Lanka', '', '1.00000', 'LK\0', '', 100);
INSERT INTO `country` VALUES(201, '0000-00-00 00:00:00', 'Sudan', '', '1.00000', 'SD\0', '', 100);
INSERT INTO `country` VALUES(202, '0000-00-00 00:00:00', 'Suriname', '', '1.00000', 'SR\0', '', 100);
INSERT INTO `country` VALUES(203, '0000-00-00 00:00:00', 'Svalbard and Jan Mayen', '', '1.00000', 'SJ\0', '', 100);
INSERT INTO `country` VALUES(204, '0000-00-00 00:00:00', 'Swaziland', '', '1.00000', 'SZ\0', '', 100);
INSERT INTO `country` VALUES(205, '0000-00-00 00:00:00', 'Sweden', '', '1.00000', 'SE\0', 'SEK', 100);
INSERT INTO `country` VALUES(206, '0000-00-00 00:00:00', 'Switzerland', '', '1.00000', 'CH\0', 'CHF', 100);
INSERT INTO `country` VALUES(207, '0000-00-00 00:00:00', 'Syrian Arab Republic', '', '1.00000', 'SY\0', '', 100);
INSERT INTO `country` VALUES(208, '0000-00-00 00:00:00', 'Taiwan, Province Of China', '', '1.00000', 'TW\0', '', 100);
INSERT INTO `country` VALUES(209, '0000-00-00 00:00:00', 'Tajikistan', '', '1.00000', 'TJ\0', '', 100);
INSERT INTO `country` VALUES(210, '0000-00-00 00:00:00', 'Tanzania, United Republic Of', '', '1.00000', 'TZ\0', '', 100);
INSERT INTO `country` VALUES(211, '0000-00-00 00:00:00', 'Thailand', '', '1.00000', 'TH\0', '', 100);
INSERT INTO `country` VALUES(212, '0000-00-00 00:00:00', 'Timor-leste', '', '1.00000', 'TL\0', '', 100);
INSERT INTO `country` VALUES(213, '0000-00-00 00:00:00', 'Togo', '', '1.00000', 'TG\0', '', 100);
INSERT INTO `country` VALUES(214, '0000-00-00 00:00:00', 'Tokelau', '', '1.00000', 'TK\0', '', 100);
INSERT INTO `country` VALUES(215, '0000-00-00 00:00:00', 'Tonga', '', '1.00000', 'TO\0', '', 100);
INSERT INTO `country` VALUES(216, '0000-00-00 00:00:00', 'Trinidad and Tobago', '', '1.00000', 'TT\0', '', 100);
INSERT INTO `country` VALUES(217, '0000-00-00 00:00:00', 'Tunisia', '', '1.00000', 'TN\0', '', 100);
INSERT INTO `country` VALUES(218, '0000-00-00 00:00:00', 'Turkey', '', '1.00000', 'TR\0', '', 100);
INSERT INTO `country` VALUES(219, '0000-00-00 00:00:00', 'Turkmenistan', '', '1.00000', 'TM\0', '', 100);
INSERT INTO `country` VALUES(220, '0000-00-00 00:00:00', 'Turks and Caicos Islands', '', '1.00000', 'TC\0', '', 100);
INSERT INTO `country` VALUES(221, '0000-00-00 00:00:00', 'Tuvalu', '', '1.00000', 'TV\0', '', 100);
INSERT INTO `country` VALUES(222, '0000-00-00 00:00:00', 'Uganda', '', '1.00000', 'UG\0', '', 100);
INSERT INTO `country` VALUES(223, '0000-00-00 00:00:00', 'Ukraine', '', '1.00000', 'UA\0', '', 100);
INSERT INTO `country` VALUES(224, '0000-00-00 00:00:00', 'United Arab Emirates', '', '1.00000', 'AE\0', '', 100);
INSERT INTO `country` VALUES(225, '0000-00-00 00:00:00', 'United Kingdom', '', '1.00000', 'GB\0', 'GBP', 100);
INSERT INTO `country` VALUES(226, '0000-00-00 00:00:00', 'United States', '$', '1.00000', 'US\0', 'USD', 100);
INSERT INTO `country` VALUES(227, '0000-00-00 00:00:00', 'United States Minor Outlying Islands', '', '1.00000', 'UM\0', '', 100);
INSERT INTO `country` VALUES(228, '0000-00-00 00:00:00', 'Uruguay', '', '1.00000', 'UY\0', '', 100);
INSERT INTO `country` VALUES(229, '0000-00-00 00:00:00', 'Uzbekistan', '', '1.00000', 'UZ\0', '', 100);
INSERT INTO `country` VALUES(230, '0000-00-00 00:00:00', 'Vanuatu', '', '1.00000', 'VU\0', '', 100);
INSERT INTO `country` VALUES(231, '0000-00-00 00:00:00', 'Venezuela', '', '1.00000', 'VE\0', '', 100);
INSERT INTO `country` VALUES(232, '0000-00-00 00:00:00', 'Viet Nam', '', '1.00000', 'VN\0', '', 100);
INSERT INTO `country` VALUES(233, '0000-00-00 00:00:00', 'Virgin Islands, British', '', '1.00000', 'VG\0', '', 100);
INSERT INTO `country` VALUES(234, '0000-00-00 00:00:00', 'Virgin Islands, U.s.', '', '1.00000', 'VI\0', '', 100);
INSERT INTO `country` VALUES(235, '0000-00-00 00:00:00', 'Wallis and Futuna', '', '1.00000', 'WF\0', '', 100);
INSERT INTO `country` VALUES(236, '0000-00-00 00:00:00', 'Western Sahara', '', '1.00000', 'EH\0', '', 100);
INSERT INTO `country` VALUES(237, '0000-00-00 00:00:00', 'Yemen', '', '1.00000', 'YE\0', '', 100);
INSERT INTO `country` VALUES(238, '0000-00-00 00:00:00', 'Zambia', '', '1.00000', 'ZM\0', '', 100);
INSERT INTO `country` VALUES(239, '0000-00-00 00:00:00', 'Zimbabwe', '', '1.00000', 'ZW\0', '', 100);



-- State table and states
CREATE TABLE `state` (
  `id` int(11) NOT NULL auto_increment,
  `expiry_date` datetime NOT NULL,
  `country_id` int(11) NOT NULL,
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `abbrev` varchar(3) collate utf8_unicode_ci NOT NULL,
  `alternate` varchar(255) collate utf8_unicode_ci NOT NULL,
  `display_order` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `date_expired` (`expiry_date`),
  KEY `alternate` (`alternate`),
  KEY `country_id` (`country_id`),
  KEY `display_order` (`display_order`),
  KEY `date_expired_2` (`expiry_date`,`country_id`,`name`,`display_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `state` VALUES(1, '0000-00-00 00:00:00', 38, 'Alberta', 'AB', '', 10);
INSERT INTO `state` VALUES(2, '0000-00-00 00:00:00', 226, 'Alaska', 'AK', '', 100);
INSERT INTO `state` VALUES(3, '0000-00-00 00:00:00', 226, 'Alabama', 'AL', '', 100);
INSERT INTO `state` VALUES(4, '0000-00-00 00:00:00', 226, 'American Samoa', 'AS', '', 100);
INSERT INTO `state` VALUES(5, '0000-00-00 00:00:00', 226, 'Arkansas', 'AR', '', 100);
INSERT INTO `state` VALUES(6, '0000-00-00 00:00:00', 226, 'Arizona', 'AZ', '', 100);
INSERT INTO `state` VALUES(7, '0000-00-00 00:00:00', 38, 'British Columbia', 'BC', '', 10);
INSERT INTO `state` VALUES(8, '0000-00-00 00:00:00', 226, 'California', 'CA', '', 100);
INSERT INTO `state` VALUES(9, '0000-00-00 00:00:00', 226, 'Colorado', 'CO', '', 100);
INSERT INTO `state` VALUES(10, '0000-00-00 00:00:00', 226, 'Connecticut', 'CT', '', 100);
INSERT INTO `state` VALUES(11, '0000-00-00 00:00:00', 226, 'District of Columbia', 'DC', '', 100);
INSERT INTO `state` VALUES(12, '0000-00-00 00:00:00', 226, 'Delaware', 'DE', '', 100);
INSERT INTO `state` VALUES(13, '0000-00-00 00:00:00', 226, 'Florida', 'FL', '', 100);
INSERT INTO `state` VALUES(14, '0000-00-00 00:00:00', 226, 'Georgia', 'GA', '', 100);
INSERT INTO `state` VALUES(15, '0000-00-00 00:00:00', 226, 'Guam', 'GU', '', 100);
INSERT INTO `state` VALUES(16, '0000-00-00 00:00:00', 226, 'Hawaii', 'HI', '', 100);
INSERT INTO `state` VALUES(17, '0000-00-00 00:00:00', 226, 'Iowa', 'IA', '', 100);
INSERT INTO `state` VALUES(18, '0000-00-00 00:00:00', 226, 'Idaho', 'ID', '', 100);
INSERT INTO `state` VALUES(19, '0000-00-00 00:00:00', 226, 'Illinois', 'IL', '', 100);
INSERT INTO `state` VALUES(20, '0000-00-00 00:00:00', 226, 'Indiana', 'IN', '', 100);
INSERT INTO `state` VALUES(21, '0000-00-00 00:00:00', 226, 'Kansas', 'KS', '', 100);
INSERT INTO `state` VALUES(22, '0000-00-00 00:00:00', 226, 'Kentucky', 'KY', '', 100);
INSERT INTO `state` VALUES(23, '0000-00-00 00:00:00', 226, 'Louisiana', 'LA', '', 100);
INSERT INTO `state` VALUES(24, '0000-00-00 00:00:00', 226, 'Massachusetts', 'MA', '', 100);
INSERT INTO `state` VALUES(25, '0000-00-00 00:00:00', 38, 'Manitoba', 'MB', '', 10);
INSERT INTO `state` VALUES(26, '0000-00-00 00:00:00', 226, 'Maryland', 'MD', '', 100);
INSERT INTO `state` VALUES(27, '0000-00-00 00:00:00', 226, 'Maine', 'ME', '', 100);
INSERT INTO `state` VALUES(28, '0000-00-00 00:00:00', 226, 'Michigan', 'MI', '', 100);
INSERT INTO `state` VALUES(29, '0000-00-00 00:00:00', 226, 'Micronesia', 'FM', '', 100);
INSERT INTO `state` VALUES(30, '0000-00-00 00:00:00', 226, 'Minnesota', 'MN', '', 100);
INSERT INTO `state` VALUES(31, '0000-00-00 00:00:00', 226, 'Missouri', 'MO', '', 100);
INSERT INTO `state` VALUES(32, '0000-00-00 00:00:00', 226, 'Mississippi', 'MS', '', 100);
INSERT INTO `state` VALUES(33, '0000-00-00 00:00:00', 226, 'Montana', 'MT', '', 100);
INSERT INTO `state` VALUES(34, '0000-00-00 00:00:00', 38, 'New Brunswick', 'NB', '', 10);
INSERT INTO `state` VALUES(35, '0000-00-00 00:00:00', 226, 'North Carolina', 'NC', '', 100);
INSERT INTO `state` VALUES(36, '0000-00-00 00:00:00', 226, 'North Dakota', 'ND', '', 100);
INSERT INTO `state` VALUES(37, '0000-00-00 00:00:00', 226, 'Nebraska', 'NE', '', 100);
INSERT INTO `state` VALUES(38, '0000-00-00 00:00:00', 38, 'Newfoundland/Labrador', 'NL', 'Newfoundland|Labrador|Newfoundland and Labrador|Newfoundland & Labrador', 10);
INSERT INTO `state` VALUES(39, '0000-00-00 00:00:00', 226, 'New Hampshire', 'NH', '', 100);
INSERT INTO `state` VALUES(40, '0000-00-00 00:00:00', 226, 'New Jersey', 'NJ', '', 100);
INSERT INTO `state` VALUES(41, '0000-00-00 00:00:00', 226, 'New Mexico', 'NM', '', 100);
INSERT INTO `state` VALUES(42, '0000-00-00 00:00:00', 38, 'Nova Scotia', 'NS', '', 10);
INSERT INTO `state` VALUES(43, '0000-00-00 00:00:00', 38, 'Northwest Territories', 'NT', 'NWT|North West Territories', 10);
INSERT INTO `state` VALUES(44, '0000-00-00 00:00:00', 38, 'Nunavut', 'NU', '', 10);
INSERT INTO `state` VALUES(45, '0000-00-00 00:00:00', 226, 'Nevada', 'NV', '', 100);
INSERT INTO `state` VALUES(46, '0000-00-00 00:00:00', 226, 'New York', 'NY', '', 100);
INSERT INTO `state` VALUES(47, '0000-00-00 00:00:00', 226, 'Ohio', 'OH', '', 100);
INSERT INTO `state` VALUES(48, '0000-00-00 00:00:00', 226, 'Oklahoma', 'OK', '', 100);
INSERT INTO `state` VALUES(49, '0000-00-00 00:00:00', 38, 'Ontario', 'ON', '', 10);
INSERT INTO `state` VALUES(50, '0000-00-00 00:00:00', 226, 'Oregon', 'OR', '', 100);
INSERT INTO `state` VALUES(51, '0000-00-00 00:00:00', 226, 'Pennsylvania', 'PA', '', 100);
INSERT INTO `state` VALUES(52, '0000-00-00 00:00:00', 38, 'Prince Edward Island', 'PE', 'PEI', 10);
INSERT INTO `state` VALUES(53, '0000-00-00 00:00:00', 226, 'Puerto Rico', 'PR', '', 100);
INSERT INTO `state` VALUES(54, '0000-00-00 00:00:00', 38, 'Quebec', 'QC', '', 10);
INSERT INTO `state` VALUES(55, '0000-00-00 00:00:00', 226, 'Rhode Island', 'RI', '', 100);
INSERT INTO `state` VALUES(56, '0000-00-00 00:00:00', 226, 'South Carolina', 'SC', '', 100);
INSERT INTO `state` VALUES(57, '0000-00-00 00:00:00', 226, 'South Dakota', 'SD', '', 100);
INSERT INTO `state` VALUES(58, '0000-00-00 00:00:00', 38, 'Saskatchewan', 'SK', '', 10);
INSERT INTO `state` VALUES(59, '0000-00-00 00:00:00', 226, 'Tennessee', 'TN', '', 100);
INSERT INTO `state` VALUES(60, '0000-00-00 00:00:00', 226, 'Texas', 'TX', '', 100);
INSERT INTO `state` VALUES(61, '0000-00-00 00:00:00', 226, 'Utah', 'UT', '', 100);
INSERT INTO `state` VALUES(62, '0000-00-00 00:00:00', 226, 'Virginia', 'VA', '', 100);
INSERT INTO `state` VALUES(63, '0000-00-00 00:00:00', 226, 'Virgin Islands', 'VI', '', 100);
INSERT INTO `state` VALUES(64, '0000-00-00 00:00:00', 226, 'Vermont', 'VT', '', 100);
INSERT INTO `state` VALUES(65, '0000-00-00 00:00:00', 226, 'Washington', 'WA', '', 100);
INSERT INTO `state` VALUES(66, '0000-00-00 00:00:00', 226, 'Wisconsin', 'WI', '', 100);
INSERT INTO `state` VALUES(67, '0000-00-00 00:00:00', 226, 'West Virginia', 'WV', '', 100);
INSERT INTO `state` VALUES(68, '0000-00-00 00:00:00', 226, 'Wyoming', 'WY', '', 100);
INSERT INTO `state` VALUES(69, '0000-00-00 00:00:00', 38, 'Yukon', 'YT', '', 10);


-- Permissions for the DB Change (dbchange controller)
INSERT INTO `permission` VALUES(NULL, 'dbchange/index', 'DB Change', 'Allows the user to run SQL commands across multiple databases.');


-- Permission for User Admin (useradmin controller)
INSERT INTO `permission` VALUES(NULL, 'useradmin/index', 'User Admin', 'Gives access to all User Admin functionality.');