<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<title>个人中心</title>
<style type="text/css">
    *{box-sizing:border-box;}
    body {margin:0px; background:#eee; -moz-appearance:none;}
    a {text-decoration:none;}
    .header {height: 175px; width:100%; padding:0 10px; background:url("../addons/manor_shop/template/mobile/default/static/images/header_bg.png");background-size: 100%;margin-bottom: 15px;}
    .header .user {height:97px; padding:15px 10px;position: relative;border-bottom: 1px solid #d6d6d6;}
    .header .user .user-head {height:56px; width:56px; background:#fff; border-radius:50%; float:left;}
    .header .user .user-head img {height:56px; width:56px; border-radius:50%;}
    .header .user .user-info { width:auto; float:left; margin-left:14px; color:#fff;}
    .header .user .user-info .user-name {width:auto; font-size:16px; line-height:62px;color: #444444;padding-bottom: 10px;}
    .header .user .user-info .user-other { width:auto; font-size:12px;color: #fff;}
    .header .user .user-info .user-other span{background:#fc4b7d;border-radius: 10px;padding:5px 8px;}
    .header .user .user-info .user-other a{color: #fff;}
    .header .user-gold {height:35px; width:94%; padding:5px 3%; border-bottom:1px solid #ddd; background:#fff; font-size:16px; line-height:35px;}
    .header .user-gold .title {height:35px; width:auto; float:left; color:#666;}
    .header .user-gold .num {height:35px; width:auto; float:left; color:#f90;}

    .header .user-gold .draw {width:80px; height:30px; background:#6c9; float:right;}
    .header .user .set { margin-top:10px;font-size: 12px;color: #fff;position: absolute;bottom: 10px;right: 20px;}

    .header .user-op { height:35px; width:94%; padding:5px 3%; border-bottom:1px solid #ddd; background:#fff; font-size:16px; line-height:35px; }
    .header .user-balance{width: 100%;height: 78px;padding-top: 20px;}
    .header .user-balance a{display: block;width: 50%;float: left;text-align: center;line-height: 24px;border-right: 1px solid #d6d6d6;color: #444444;font-size: 14px;position: relative;}
    .header .user-balance a:last-child{border:none;}
    .header .user-balance a span{color: #5d5d5d;}
    .header .user-balance a i{position: absolute;width: 25px;height: 14px;font-size: 12px;color: #fff;background: #fc3a71;right: 15%;top: -5px;line-height: 14px;font-style: normal;border-top-right-radius: 3px;border-bottom-left-radius: 3px;}
    .take {height:30px; width:auto; padding:0 10px; line-height:30px; font-size:16px; float:right; background:#ff6600; border-radius:5px; margin-top:5px; color:#fff;}
    .take1 {height:30px; width:auto; padding:0 10px; line-height:30px; font-size:16px; float:right; background:#00cc00; border-radius:5px; margin-top:5px; color:#fff;}

    .order {width:100%; background:#fff;margin-bottom: 15px;}
    .order_all {width:100%; color:#6f6f6f;font-size: 14px;overflow: hidden;background:#fff;}
    .order_all .fa{margin-bottom: 5px;}
   .order_pub {float:left; padding:20px 0; text-align:center; color:#666; position:relative;}
    .order_pub span {height:16px; width:16px; background:#fc3a71; border-radius:8px; position:absolute; top:10px; right:25%; font-size:12px; color:#fff; line-height:16px;}
    .order .order_1,.order .order_2,.order .order_3,.order .order_4,.order .order_5{width:20%;}
    .order_5 .order_number,.order_6 .order_number,.order_7 .order_number,.order_8 .order_number{font-size: 20px;color: #ff6665;height: 14px;display: block;font-style: normal;}
    .list1 {height:42px; border-bottom:1px solid #ddd; line-height:42px; color:#444444;}
    .list1 i {font-size:20px; margin-right:10px;}
    .allorder {float:right; color:#aaa; margin-right:15px; text-decoration:none;}


    .cart {height:auto; width:100%; background:#fff; margin-top:20px; border-bottom:1px solid #ddd;}
    .address {height:38px; width:100%; background:#fff; margin-top:20px; border-bottom:1px solid #ddd; border-top:1px solid #ddd; line-height:38px;}

    .copyright {height:40px; width:100%; text-align:center; line-height:40px; font-size:12px; color:#999; margin-top:10px;}

	span.count {float:right; margin-top:15px; margin-right: 5px; height:16px; width:16px; background:#f30; border-radius:8px; font-size:12px; color:#fff; line-height:16px; text-align: center;}
    .help_explain_title{padding-left: 11px;font-size: 14px;color:#6f6f6f;margin: 0;line-height: 25px;}
    .help_explain .fa{color: #fff;border-radius: 50%;font-size: 24px;width: 30px;height: 30px;text-align: center;line-height: 30px;display: block;margin: 0 auto 5px;}
    .help_explain .order_1,.help_explain .order_2,.help_explain .order_3,.help_explain .order_4,.help_explain .order_5,.help_explain .order_6,.help_explain .order_7,.help_explain .order_8,.order_9{width: 25%;border-left: 1px solid #d4d4d4;border-bottom: 1px solid #d4d4d4;}
    .help_explain .order_1,.help_explain .order_2,.help_explain .order_3,.help_explain .order_4{border-bottom: 1px solid #d4d4d4;}
    .default_address{width: 100%;background:#fff;padding:0px 3%; margin-top: 8px;font-size: 14px;}
    .default_address a span{float: right;margin-right: 2%;line-height: 36px;}
    .detault_detail{padding:10px 0;}
    .detault_detail p{color: #6f6f6f;margin: 0;line-height: 24px;}
</style>

<div id="container"></div>
<script id="member_center" type="text/html">
    <div class="header">
      <a href="<?php  echo $this->createMobileUrl('member/info')?>">
        <div class="user">
          <div class="user-head"><img src="<%member.avatar%>" /></div>
          <div class="user-info">
            <div class="user-name"><%member.nickname%></div>
            <div class="user-other">
              <!--<span>签到送好礼</span>-->
                      <!-- <a href="javascript:;">可用积分: <%member.credit1%>积分
                          <%if open_creditshop%>
                          <div class="take1" onclick="location.href='<?php  echo $this->createPluginMobileUrl('creditshop')?>'">积分兑换</div>
                           <%/if%>
                      </a> -->
            </div>
          </div>
          <i onclick="location.href='<?php  echo $this->createMobileUrl('member/info')?>'" class="fa fa-angle-right" style="float:right;color:#999;font-size:24px;line-height:60px;"></i>
              <!-- <div class="set" onclick='location.href="<?php  echo $this->createMobileUrl('member/info')?>"'>账户设置>> -->
        </div>
      </a>
            <div class="user-balance">
              <a href="#" <?php  if(isset($set['trade']) && $set['trade']['withdraw']==1) { ?> id="btnwithdraw" <?php  } ?>>
                余额<br/><span><%member.credit2%></span>
              </a>
              <a href="<?php  echo $this->createPluginMobileUrl('coupon/my')?>">
                <!--<i class="youli">有礼</i>-->
                优惠券<br/><span><%counts.couponcount%></span>
              </a>
              <!-- <a href="#">
                积分<br/><span><%member.credit1%></span>
              </a>-->
            </div>
        </div>
</div>
<div class="order">
  <a href="<?php  echo $this->createMobileUrl('order')?>" class="list1" style="padding-left: 10px;float: left;width: 100%;border: 0px;">
      <div style="border-bottom: 1px solid #ddd;height: 100%;">
          <i style="float:left; line-height:44px;"><img src="../addons/manor_shop/template/mobile/default/static/images/order.png" width="16px" /></i>
          <span style="float:left;">我的订单</span>
          <i class="fa fa-angle-right" style="color:#999; font-size:24px; float:right; line-height:42px;"></i>
          <div class="allorder">全部订单</div>
      </div>
  </a>
    <div class="order_all">
        <a href="<?php  echo $this->createMobileUrl('order',array('status'=>0))?>"><div class="order_pub order_1"><i><img src="../addons/manor_shop/template/mobile/default/static/images/order1.png" width="26px" style="margin-bottom:5px;" /></i><br>待付款<%if order.status0>0%><span><%order.status0%></span><%/if%></div></a>
        <a href="<?php  echo $this->createMobileUrl('order',array('status'=>1))?>"><div class="order_pub order_2"><i><img src="../addons/manor_shop/template/mobile/default/static/images/order2.png" width="26px" style="margin-bottom:5px;" /></i><br>待发货<%if order.status1>0%><span><%order.status1%></span><%/if%></div></a>
        <a href="<?php  echo $this->createMobileUrl('order',array('status'=>2))?>"><div class="order_pub order_3"><i><img src="../addons/manor_shop/template/mobile/default/static/images/order3.png" width="26px" style="margin-bottom:5px;" /></i><br>待收货<%if order.status2>0%><span><%order.status2%></span><%/if%></div></a>
        <a href="<?php  echo $this->createMobileUrl('order',array('status'=>3))?>"><div class="order_pub order_4"><i><img src="../addons/manor_shop/template/mobile/default/static/images/order4.png" width="26px" style="margin-bottom:5px;" /></i><br>待评价<%if order.status3>0%><span>
        <%order.status3%></span><%/if%></div></a>
         <a href="<?php  echo $this->createMobileUrl('order', array('status'=>4))?>">
            <div class="order_pub order_5"><i><img src="../addons/manor_shop/template/mobile/default/static/images/order5.png" width="26px" style="margin-bottom:5px;" /></i><br>退换货<%if order.status4>0%><span><%order.status4%></span><%/if%>
            </div>
        </a>
    </div>
    <!-- <div class="order_all">
        <a href="javascript:;"><div class="order_pub order_5" <?php  if(isset($set['trade']) && $set['trade']['withdraw']==1) { ?> id="btnwithdraw"<?php  } ?> style="border-left:0px;"><i class="order_number"><%member.credit2%></i><br>余额</div></a>
        <a href="<?php  echo $this->createMobileUrl('shop/favorite')?>"><div class="order_pub order_6"><i class="order_number collect"><%counts.favcount%></i><br>收藏</div></a>
        <a href="<?php  echo $this->createPluginMobileUrl('coupon/my')?>"><div class="order_pub order_7"><i class="order_number"><%counts.couponcount%></i><br>优惠券</div></a>
        <a href="javascript:;">
            <div class="order_pub order_8" onclick="location.href='<?php  echo $this->createMobileUrl('member/recharge',array('openid'=>$openid))?>'"><i style="font-size:22px;width:30px;height:30px;background:#5fb823;color:#fff;border-radius:50%;font-style:normal;margin:0 auto;font-weight:bold;display:block;">充</i>充值<%if order.status4>0%><span><%order.status4%></span><%/if%>
            </div>
        </a>
    </div> -->
</div>
<div class="help_explain">
    <!-- <p class="help_explain_title">帮助说明</p> -->
    <div class="order_all">
        <a href="<?php  echo $this->createMobileUrl('member/info')?>"><div class="order_pub order_1" style="border-left:0px;"><i><img src="../addons/manor_shop/template/mobile/default/static/images/xb5.png" width="28px" style="margin-bottom:5px;" /></i><br>会员资料</div></a>
        <a href="<?php  echo $this->createMobileUrl('shop/address')?>"><div class="order_pub order_2"><i><img src="../addons/manor_shop/template/mobile/default/static/images/xb6.png" width="28px" style="margin-bottom:5px;" /></i><br>地址管理</div></a>
        <a href="<?php  echo $this->createMobileUrl('member/notice')?>"><div class="order_pub order_3"><i><img src="../addons/manor_shop/template/mobile/default/static/images/xb7.png" width="28px" style="margin-bottom:5px;" /></i><br>消息通知</div></a>
        <a href="<?php  echo $this->createMobileUrl('member/recharge', array('openid'=>$openid))?>"><div class="order_pub order_4"><i><img src="../addons/manor_shop/template/mobile/default/static/images/xb4.png" width="28px" style="margin-bottom:5px;" /></i><br>充值</div></a>
    </div>
    <div class="order_all">
        <a href="<?php  echo $this->createMobileUrl('member/help')?>&op=about"><div class="order_pub order_5" style="border-left:0px;"><i><img src="../addons/manor_shop/template/mobile/default/static/images/xb3.png" width="28px" style="margin-bottom:5px;" /></i><br>关于</div></a>
        <a href="<?php  echo $this->createMobileUrl('member/help')?>&op=distribution"><div class="order_pub order_6"><i><img src="../addons/manor_shop/template/mobile/default/static/images/xb2.png" width="28px" style="margin-bottom:5px;" /></i><br>配送说明</div></a>
        <a href="<?php  echo $this->createMobileUrl('member/help')?>&op=returns"><div class="order_pub order_7"><i><img src="../addons/manor_shop/template/mobile/default/static/images/xb1.png" width="28px" style="margin-bottom:5px;" /></i><br>退货说明</div></a>
          <!--<a href="" onclick="javascript:window.open('http://b.qq.com/webc.htm?new=0&sid=853898868&o=tangshengmanor.com&q=7', '_blank', 'height=502, width=644,toolbar=no,scrollbars=no,menubar=no,status=no');"  border="0"><div class="order_pub order_8"><i><img src="../addons/manor_shop/template/mobile/default/static/images/xb0.png" width="28px" style="margin-bottom:5px;" /></i><br>联系客服</div></a>-->
        <a href="<?php  echo $this->createMobileUrl('member/feedback')?>" ><div class="order_pub order_5" style="margin-top: -8px"><i><img src="../addons/manor_shop/template/mobile/tangsheng/static/images/feedback.png" width="28px" style="margin-bottom:5px;" /></i><br>意见反馈</div></a>
    </div>
</div>
    <div style="width: 100%;text-align: center;font-size:13px;height: 120px;line-height: 60px;">
        <a href="tel:<?php  echo $set['shop']['phone'];?>" style="color: #666;padding: 10px 5px;background: rgba(253,173,39,1);border-radius: 5px;color: #fff;">
            客服电话:<?php  if(!$set['shop']['phone']) { ?>暂无<?php  } else { ?><?php  echo $set['shop']['phone'];?><?php  } ?>
        </a>
    </div>
</script>
<script language="javascript">
    require(['tpl', 'core'], function(tpl, core) {

        core.json('member/center',{},function(json){

           $('#container').html(  tpl('member_center',json.result) );
           var withdrawmoney = <?php echo empty($set['trade']['withdrawmoney'])?0:floatval($set['trade']['withdrawmoney'])?>;
           $('#btnwithdraw').click(function(){

               if(json.result.member.credit2<=0){
                   core.tip.show('无余额可提!');
                   return;
               }
               if(withdrawmoney>0 && json.result.member.credit2<withdrawmoney){
                   core.tip.show('满' +withdrawmoney + "元才能提现!" );
                   return;
               }
               location.href = core.getUrl('member/withdraw');
           })

        },true);

    })
</script>
<?php  $show_footer=true?>
<?php  $footer_current='member'?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
