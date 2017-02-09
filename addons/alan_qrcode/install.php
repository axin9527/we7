<?php
global $_W;
$sql = "
    DROP TABLE IF EXISTS `ims_alan_qrcode`;
CREATE TABLE `ims_alan_qrcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号 id',
  `rid` int(11) DEFAULT NULL COMMENT '规则id',
  `name` varchar(40) DEFAULT NULL COMMENT '规则名称',
  `title` varchar(40) DEFAULT NULL COMMENT '场景名称',
  `keyword` varchar(80) DEFAULT NULL COMMENT '关键字',
  `group_id` int(11) DEFAULT NULL COMMENT '分组 id',
  `wx_url` varchar(300) DEFAULT NULL COMMENT '微信二维码路径',
  `ticket` varchar(150) DEFAULT NULL COMMENT '二维码生成的唯一凭证',
  `subnum` int(11) DEFAULT '0' COMMENT '关注数',
  `scannum` int(11) DEFAULT '0' COMMENT '扫码数',
  `cancelnum` int(11) DEFAULT '0' COMMENT '取消关注数',
  `deleted` smallint(2) DEFAULT '0' COMMENT '是否删除 1 是 0 否 默认0',
  `model` smallint(2) DEFAULT '2' COMMENT '二维码类型 1 临时 2 永久',
  `expire` int(11) DEFAULT '0' COMMENT '过期时间 0 不过期',
  `wx_data` text COMMENT '微信数据，备份',
  `path` varchar(150) DEFAULT '0' COMMENT '写入的图片资源',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `scan_key` varchar(20) DEFAULT NULL COMMENT '场景值',
  `mid` varchar(40) DEFAULT NULL COMMENT '微信图像资源',
  `m_data` varchar(1024) DEFAULT NULL COMMENT '微信图像资源',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='二维码表';
DROP TABLE IF EXISTS `ims_alan_qrcode_cash_record`;
CREATE TABLE `ims_alan_qrcode_cash_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `openid` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `qr_id` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='扫码资金记录';
DROP TABLE IF EXISTS `ims_alan_qrcode_fans`;
CREATE TABLE `ims_alan_qrcode_fans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号 id',
  `openid` varchar(40) DEFAULT NULL,
  `sex` smallint(2) DEFAULT NULL COMMENT '性别 1 男 2 女 3未知',
  `nickname` varchar(150) DEFAULT NULL COMMENT '昵称 urlencode',
  `city` varchar(40) DEFAULT NULL COMMENT '所在城市',
  `province` varchar(40) DEFAULT NULL COMMENT '省',
  `country` varchar(40) DEFAULT NULL COMMENT '国家',
  `headimgurl` varchar(300) DEFAULT NULL COMMENT '头像',
  `group_id` int(11) DEFAULT NULL COMMENT '所在分组',
  `qrcode_id` int(11) DEFAULT NULL COMMENT '场景二维码id',
  `subscribe_time` int(11) DEFAULT NULL COMMENT '关注时间',
  `cancel_sub_time` int(11) DEFAULT NULL COMMENT '取消关注时间',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `wx_data` text COMMENT '微信用户信息',
  `follow` smallint(2) DEFAULT NULL COMMENT '是否关注 1 是 2 否',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户信息表';
DROP TABLE IF EXISTS `ims_alan_qrcode_fans_group`;
CREATE TABLE `ims_alan_qrcode_fans_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号 id',
  `name` varchar(40) DEFAULT NULL COMMENT '分组名称',
  `group_desc` varchar(150) DEFAULT NULL COMMENT '分组描述',
  `mc_group_id` int(11) DEFAULT NULL COMMENT '分组id',
  `count` int(11) DEFAULT '0' COMMENT '分组内用户总数量',
  `scan_count` int(11) DEFAULT '0' COMMENT '场景内分组总数量',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `cancel_count` int(11) DEFAULT '0' COMMENT 'cancel_count',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='分组表';
DROP TABLE IF EXISTS `ims_alan_qrcode_record`;
CREATE TABLE `ims_alan_qrcode_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(150) DEFAULT NULL COMMENT '昵称',
  `avartar` varchar(300) DEFAULT NULL COMMENT '头像',
  `qrcode_id` int(11) DEFAULT NULL COMMENT '二维码id',
  `ticket` varchar(300) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL COMMENT '类型 qr 扫描 subscribe 关注 unsubscribe 取消关注',
  `wx_data` varchar(1024) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `date` int(8) DEFAULT NULL,
  `uniacid` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `qr_data` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='扫码记录日志';
DROP TABLE IF EXISTS `ims_alan_qrcode_stat`;
CREATE TABLE `ims_alan_qrcode_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL COMMENT '公众号 id',
  `qrcode_id` int(11) DEFAULT NULL COMMENT '场景二维码id',
  `group_id` int(11) DEFAULT NULL COMMENT '分组id',
  `date` int(8) DEFAULT NULL COMMENT '时间Ymd',
  `scan_count` int(11) DEFAULT '0' COMMENT '扫码数',
  `sub_num` int(11) DEFAULT '0' COMMENT '关注数',
  `cancel_num` int(11) DEFAULT '0' COMMENT '取消关注数',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='统计表';
";
pdo_query($sql);
?>