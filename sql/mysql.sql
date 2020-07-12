CREATE TABLE `cord_category` (
  `categoryid` int(12) unsigned NOT NULL auto_increment,
  `category_name` varchar(255) NOT NULL DEFAULT '',
  `category_shortname` varchar(30) NOT NULL DEFAULT '',
  `category_description` text,
  `category_parent` int(12) unsigned NOT NULL,
  KEY `parent` (`category_parent`),
  KEY `shortname` (`category_shortname`),
  PRIMARY KEY (`categoryid`)
) Type=MyISAM;

CREATE TABLE `cord_customer` (
  `customerid` int(12) unsigned NOT NULL auto_increment,
  `customer_name` varchar(255) NOT NULL DEFAULT '',
  `customer_shortname` varchar(30) NOT NULL DEFAULT '',
  `customer_email` varchar(255) NOT NULL DEFAULT '',
  `customer_image` varchar(255) NOT NULL DEFAULT '',
  `customer_desc` text,
  `uid` int NOT NULL default 0,
  KEY `shortname` (`customer_shortname`),
  KEY `user` (`uid`),
  PRIMARY KEY (`customerid`)
) Type=MyISAM;

CREATE TABLE `cord_hit` (
  `hitid` int(12) unsigned NOT NULL auto_increment,
  `hit_ip` int NOT NULL,
  `hit_time` int unsigned NOT NULL,
  `userid` int unsigned NOT NULL,
  `whitepaperid` int unsigned NOT NULL,
  `hit_name` varchar(255) NOT NULL DEFAULT '',
  `hit_address` varchar(255) NOT NULL DEFAULT '',
  `hit_postal` varchar(255) NOT NULL DEFAULT '',
  `hit_city` varchar(255) NOT NULL DEFAULT '',
  `hit_phone` varchar(255) NOT NULL DEFAULT '',
  `hit_email` varchar(255) NOT NULL DEFAULT '',
  KEY `whitepaper` (`whitepaperid`, `hit_time`, `userid`),
  PRIMARY KEY (`hitid`)
) Type=MyISAM;

CREATE TABLE `cord_keywordcategory` (
  `keywordid` int unsigned NOT NULL,
  `categoryid` int unsigned NOT NULL,
  KEY `category` (`categoryid`),
  PRIMARY KEY (`keywordid`, `categoryid`)
) Type=MyISAM;

CREATE TABLE `cord_keywordpaper` (
  `keywordid` int unsigned NOT NULL,
  `whitepaperid` int unsigned NOT NULL,
  KEY `whitepaper` (`whitepaperid`),
  PRIMARY KEY (`keywordid`, `whitepaperid`)
) Type=MyISAM;

CREATE TABLE `cord_papercat` (
  `whitepaperid` int unsigned NOT NULL,
  `categoryid` int unsigned NOT NULL,
  KEY `category` (`categoryid`),
  PRIMARY KEY (`whitepaperid`, `categoryid`)
) Type=MyISAM;

CREATE TABLE `cord_whitepaper` (
  `whitepaperid` int unsigned NOT NULL auto_increment,
  `whitepaper_title` varchar(255) NOT NULL DEFAULT '',
  `whitepaper_shortdesc` text,
  `whitepaper_desc` text,
  `whitepaper_pages` tinyint unsigned NOT NULL DEFAULT 1,
  `whitepaper_language` varchar(255) NOT NULL DEFAULT '',
  `whitepaper_active` tinyint unsigned NOT NULL DEFAULT 0,
  `whitepaper_date` int unsigned NOT NULL,
  `whitepaper_publishdate` int unsigned NOT NULL DEFAULT 0,
  `whitepaper_expiredate` int unsigned NOT NULL DEFAULT 0,
  `whitepaper_level` tinyint unsigned NOT NULL DEFAULT 1,
  `customerid` int unsigned NOT NULL DEFAULT 0,
  `whitepaper_reads` int unsigned NOT NULL DEFAULT 0,
  `whitepaper_hits` int unsigned NOT NULL DEFAULT 0,
  KEY `public` (`whitepaperid`,`whitepaper_active`,`whitepaper_publishdate`,`whitepaper_expiredate`,`whitepaper_date`),
  KEY `customer` (`customerid`,`whitepaper_active`,`whitepaper_publishdate`,`whitepaper_expiredate`,`whitepaper_date`),
  PRIMARY KEY (`whitepaperid`)
) Type=MyISAM;