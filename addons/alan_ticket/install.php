<?php
global $_W;
$sql = "
DROP TABLE IF EXISTS `ims_ticket_range_mock`;
CREATE TABLE `ims_ticket_range_mock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `nickname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `headimg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wish` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vote_number` int(11) DEFAULT NULL,
  `is_mock` int(11) DEFAULT '0',
  `uniacid` int(11) COLLATE utf8_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `ims_ticket_range`;
CREATE TABLE `ims_ticket_range` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `nickname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `headimg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wish` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vote_number` int(11) DEFAULT NULL,
  `isshield` SMALLINT (2) DEFAULT 0,
  `uniacid` int(11) COLLATE utf8_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `ims_ticket_user`;
CREATE TABLE `ims_ticket_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `nickname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `realname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `headimg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wish` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ismock` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uniacid` int(11) COLLATE utf8_unicode_ci DEFAULT '',
  `follow` SMALLINT (2) COLLATE utf8_unicode_ci DEFAULT '',
  `created_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `ims_ticket_vote`;
CREATE TABLE `ims_ticket_vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` int(11) DEFAULT NULL,
  `to_id` int(11) DEFAULT NULL,
  `vote_time` int(11) DEFAULT NULL,
  `uniacid` int(11) COLLATE utf8_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
";
pdo_query($sql);