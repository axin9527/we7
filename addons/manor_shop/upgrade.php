<?php
	global $_W;
	$sql = "
	ALTER TABLE `ims_manor_shop_activity` ADD COLUMN `is_activity` smallint(2) DEFAULT 0 AFTER `is_mention`;
	ALTER TABLE `ims_manor_shop_order_goods` ADD COLUMN `expresscom` varchar(30) AFTER `is_give`, ADD COLUMN `expresssn` varchar(50) AFTER `expresscom`, ADD COLUMN `express` varchar(255) AFTER `expresssn`, ADD COLUMN `sendtime` int(11) AFTER `express`, ADD COLUMN `finishtime` int(11) AFTER `sendtime`, ADD COLUMN `refundtime` int(11) AFTER `finishtime`, ADD COLUMN `status` smallint(3) DEFAULT 0 COMMENT '-1取消状态，0普通状态，1为已付款，2为已发货，3为成功' AFTER `refundtime`, ADD COLUMN `refundid` int(11) AFTER `status`, ADD COLUMN `paytype` int(11) AFTER `refundid`, ADD COLUMN `paytime` int(11) AFTER `paytype`, ADD COLUMN `rstatus` int(11) AFTER `paytime`;
	ALTER TABLE `ims_manor_shop_activity` ADD COLUMN `goods_sort_type` smallint(2) DEFAULT 1 COMMENT '1 横排固定 2 横排滑动 3 竖排' AFTER `is_activity`;
	";
	pdo_query($sql);
