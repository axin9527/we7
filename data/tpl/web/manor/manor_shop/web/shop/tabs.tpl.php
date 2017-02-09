<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-tabs">
    <?php if(cv('shop.goods.view')) { ?><li <?php  if($_GPC['p'] == 'goods' || empty($_GPC['p'])) { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('shop/goods')?>">商品管理</a></li><?php  } ?>
    <?php if(cv('shop.activity.view')) { ?><li <?php  if($_GPC['p'] == 'activity' ) { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('shop/activity')?>">活动管理</a></li><?php  } ?>
     <?php if(cv('shop.navigation.view')) { ?><li <?php  if($_GPC['p'] == 'navigation' ) { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('shop/navigation')?>">首页导航管理</a></li><?php  } ?>
     <?php if(cv('shop.live_video.view')) { ?><li <?php  if($_GPC['p'] == 'live_video' ) { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('shop/live_video')?>">直播视频管理</a></li><?php  } ?>
    <?php if(cv('shop.category.view')) { ?><li <?php  if($_GPC['p'] == 'category') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('shop/category')?>">商品分类管理</a></li><?php  } ?>
    <?php if(cv('shop.dispatch.view')) { ?><li <?php  if($_GPC['p'] == 'dispatch') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('shop/dispatch')?>">配送方式</a></li><?php  } ?>
    <?php if(cv('shop.adv.view')) { ?><li <?php  if($_GPC['p'] == 'adv') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('shop/adv')?>">幻灯片管理</a></li><?php  } ?>
    <?php if(cv('shop.notice.view')) { ?><li <?php  if($_GPC['p'] == 'notice') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('shop/notice')?>">公告管理</a></li><?php  } ?>
    <?php if(cv('shop.comment.view')) { ?><li <?php  if($_GPC['p'] == 'comment') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('shop/comment')?>">评价管理</a></li><?php  } ?>
    <?php if(cv('shop.dispatch.view')) { ?><li <?php  if($_GPC['p'] == 'refundaddress') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('shop/refundaddress')?>">退货地址</a></li><?php  } ?>
    <?php if(cv('shop.feedback.view')) { ?><li <?php  if($_GPC['p'] == 'feedback') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('shop/feedback')?>">反馈管理</a></li><?php  } ?>
</ul>
