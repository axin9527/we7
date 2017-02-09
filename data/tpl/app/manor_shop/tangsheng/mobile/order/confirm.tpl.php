<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<title>确认订单</title>
<?php  if(!empty($order_formInfo)) { ?>
	 <script src="../addons/manor_shop/static/js/dist/mobiscroll/mobiscroll.core-2.5.2.js" type="text/javascript"></script>
<script src="../addons/manor_shop/static/js/dist/mobiscroll/mobiscroll.core-2.5.2-zh.js" type="text/javascript"></script>
<link href="../addons/manor_shop/static/js/dist/mobiscroll/mobiscroll.core-2.5.2.css" rel="stylesheet" type="text/css" />
<link href="../addons/manor_shop/static/js/dist/mobiscroll/mobiscroll.animation-2.5.2.css" rel="stylesheet" type="text/css" />
<script src="../addons/manor_shop/static/js/dist/mobiscroll/mobiscroll.datetime-2.5.1.js" type="text/javascript"></script>
<script src="../addons/manor_shop/static/js/dist/mobiscroll/mobiscroll.datetime-2.5.1-zh.js" type="text/javascript"></script>
<script src="../addons/manor_shop/static/js/dist/mobiscroll/mobiscroll.android-ics-2.5.2.js" type="text/javascript"></script>
<link href="../addons/manor_shop/static/js/dist/mobiscroll/mobiscroll.android-ics-2.5.2.css" rel="stylesheet" type="text/css" />

<link href="../addons/manor_shop/template/mobile/default/static/js/star-rating.css" media="all" rel="stylesheet" type="text/css"/>
<script src="../addons/manor_shop/template/mobile/default/static/js/star-rating.js" type="text/javascript"></script>
<script src="../addons/manor_shop/static/js/dist/ajaxfileupload.js" type="text/javascript"></script>
<?php  } ?>
<script type="text/javascript" src="../addons/manor_shop/static/js/dist/area/cascade.js"></script>
<style type="text/css">
    body {margin:0px; background:#efefef;}
    .addorder_topbar {height:34px; background:#5f6e8b; padding:15px;}
    .addorder_topbar .ico {height:34px; width:30px; line-height:34px; float:left; font-size:26px; text-align:center; color:#fff;}
    .addorder_topbar .tips {height:34px;  margin-left:10px; font-size:13px; color:#fff; line-height:17px;}

    .addorder_nav { height:30px; padding:10px;}
    .addorder_nav .nav { padding:2px 5px 2px 5px;; border:1px solid #5f6e8b; color:#5f6e8b; background:#fff; float:left; margin-left:10px;}
    .addorder_nav .selected { border:1px solid #ff6600; color:#ff6600; }

    .addorder_user {height:90px;  background:url("../addons/manor_shop/template/mobile/default/static/images/confirm_bg.png") no-repeat;background-size: 100%;padding:5px 10px; border-bottom:1px solid #eaeaea;margin-top: 10px;}
    .addorder_user .info .ico { float:left;height:100px; width:30px; line-height:100px; text-align:center;}
		.addorder_user .info .ico img{width: 15px;}
		.addorder_user .info .info1 {height:54px; width:100%; float:left;margin-left:-30px;margin-right:-30px;}
    .addorder_user .info .info1 .inner { margin-left:30px;margin-right:30px;overflow:hidden;}
    .addorder_user .info .info1 .inner .user {height:40px; width:100%; font-size:16px; color:#444; line-height:40px;overflow:hidden;}
    .addorder_user .info .info1 .inner .user #address_mobile{font-size: 12px;padding:0 10px;color: #666;}
		.addorder_user .info .info1 .inner .user  .addressType,.chooser .address .info .inner .name .addressType{font-size: 12px;padding: 3px 10px;border-radius: 10px;margin-right: 5px;}
		.addorder_user .info .info1 .inner .address {width:100%; font-size:14px; color:#444; line-height:20px;overflow:hidden;}
		.addorder_user .info .info1 .inner .addrArea{font-size: 14px;color: #444;}
		.addorder_user .info .ico2 {height:100px;  width:30px; line-height:100px; float:right; font-size:16px; text-align:right; color:#999;}

    .addorder_exp {height:42px;  background:#fff; padding:5px; border-bottom:1px solid #eaeaea; line-height:42px; font-size:16px; color:#333;}
    .addorder_exp .t1 {height:42px; width:auto; float:left;padding-left:10px;}
    .addorder_exp .t2 {height:42px; width:auto; float:right;}
    .addorder_exp .ico {height:42px; width:30px;  float:right;text-align:right;color:#999; font-size:16px;margin-top:5px; }


    .addorder_good {height:auto;padding:10px;background:#fff;  margin-top:10px; border-bottom:1px solid #eaeaea; border-top:1px solid #eaeaea;}
    .addorder_good .ico {height:6px; width:10%; line-height:36px; float:left; text-align:center;}
    .addorder_good .shop {height:36px; width:90%; padding-left:10%; border-bottom:1px solid #eaeaea; line-height:36px; font-size:18px; color:#333;}
    .addorder_good .good {height:80px; width:100%; padding:10px 0px; border-bottom:1px solid #eaeaea;}
    .addorder_good .img {width:80px; float:left;margin-top: 15px;}
    .addorder_good .img img {height:100%; width:100%;}
    .addorder_good .info {width:100%;float:left; margin-left:-80px;margin-right:-60px;}
    .addorder_good .info .inner { margin-left:90px;margin-right:60px; }
    .addorder_good .info .inner .name {height:32px; width:100%; float:left; font-size:14px; color:#444;overflow:hidden;}
    .addorder_good .info .inner .option {height:18px; width:100%; float:left; font-size:12px; color:#888;overflow:hidden;word-break: break-all}
		.addorder_good .info .inner .pnum{float: left;color: #fc3a71;}
		.addorder_good .info .inner .pnum span{color: #fc3a71;font-size: 17px;}
		.addorder_good span { color:#666;}
    .addorder_good .price { float:right;width:60px;;height:54px;margin-left:-60px;padding-top: 20px;}
    .addorder_good .price .pnum { height:20px;width:100%;text-align:right;font-size:14px; }
    .addorder_good .price .num { height:20px;width:100%;text-align:right;}
    .message{padding:10px 0 17px;}
		.message input {height:44px; width:90%; border:1px solid #d4d4d4;background:#fff; margin:0 3%; -webkit-appearance: none; padding: 0 1%;}
    .addorder_good .text {height:34px; width:100%; line-height:34px; text-align:right; font-size:16px; color:#666;}

    .addorder_price {height:auto;  background:#fff; padding:5px 10px; margin-top:10px; border-bottom:1px solid #eaeaea; border-top:1px solid #eaeaea;}
    .addorder_price .price {height:auto; width:100%; border-bottom:1px solid #eaeaea;}
    .addorder_price .price .line {height:40px; width:100%; font-size:16px; color:#444;}
    .addorder_price .price .line span {height:33px; width:auto; float:right;font-size: 14px;}
    .addorder_price .all {height:47px; width:100%; line-height:47px; font-size:16px; color:#666;}
    .addorder_price .all span {height:47px; width:auto; float:right; color:#ff771b;}
		.addorder_pay {position: fixed;bottom: 0;height:50px; width:97%; background:#fff; padding-left:3%; border-top:1px solid #d7d7d7;margin-top: 30px;}
    .addorder_pay span {height:60px; width:auto; margin-right:16px; float:right; line-height:60px; color:#fc3a71; font-size:16px;}
    .addorder_pay .paysub {height:100%; width:auto; background:#fc3a71; padding:0px 35px; color:#fff; line-height:50px; float:right;}
    .chooser {overflow: auto; width: 100%; background:#efefef; position: fixed; top: 0px; right: -100%; z-index: 1;}
    .chooser .address {height:80px; background:#fff; padding:10px;margin-top: 10px;}
    .chooser .address .ico {float:left; height:50px; width:30px; line-height:50px; float:left; font-size:20px; text-align:center; color:#999;}
    .chooser .address .info {height:50px; width:100%;float:left;margin-left:-30px;margin-right:-30px;}
    .chooser .address .info .inner { margin-left:35px;margin-right:30px;}
    .chooser .address .info .inner .name {height:32px; width:100%; font-size:16px; color:#444; line-height:32px;overflow:hidden;}
		.chooser .address .info .inner .name span{font-size: 12px;color: #666;padding:0 10px;}
		.chooser .address .info .inner .addr {height:22px; width:100%; font-size:14px; color:#444; line-height:22px;overflow: hidden;}
		.chooser .address .info .inner .addrArea{font-size: 14px;color: #444;}
		.chooser .address .edit {height:50px; width:30px; float:right;margin-left:-30px;text-align:center;line-height:50px;color:#666;}

    .chooser .add_address {height:44px; padding:5px; background:#fff; border-bottom:1px solid #eaeaea; line-height:44px; font-size:16px; color:#666;margin-top: 30px;text-align: center;}



    .address_main {height:100%; width:100%; background:#f4f6f6;  position: fixed; top: 0px; right: -100%; z-index: 1;}
		.address_main .line_wrap{background: #fff;margin-top: 10px;}
		.address_main .line {height:44px; margin: 0 5px; border-bottom:1px solid #f0f0f0; line-height:44px;}
		.address_main .line label{height: 44px;width: 35%;float: left;}
		.address_main .line .address_type{width: 60%;height: 44px;float: right;}
		.address_main .line .address_type li{width: 33%;float: left;color: #444444;}
		.address_main .line .address_type li i{font-size: 18px;color: #939393;}
		.address_main .line input {float:left; height:44px; width:60%; padding:0px; margin:0px; border:0px; outline:none; font-size:16px; color:#666;padding-left:5px;text-align: right;}
    .address_main .line select { border:none;width:60%;color:#666;font-size:16px;height: 44px;line-height: 44px;float: left;direction:rtl;color: #aeaeae;}
		.address_main .line .switch{width: 51px;height: 31px;float: right;margin:5px 10px 0 0;}
		.address_main .line .off{background:url("../addons/manor_shop/template/mobile/tangsheng/static/images/off.png") no-repeat;background-size: 100%;}
		.address_main .line .on{background:url("../addons/manor_shop/template/mobile/tangsheng/static/images/off.png") no-repeat;background-size: 100%;}
		.address_main .address_sub_btn{height:44px;width: 100%;margin:20px 4%;}
		.address_main .address_sub1,.address_main .address_sub3 {height:44px;  text-align:center; font-size:16px; line-height:44px; width: 40%;float: left;margin:0 2%;border:1px solid #fc3a71;}
		.address_main .address_sub1{background:#fc3a71;color: #fff;}
		.address_main .address_sub3{color: #fc3a71;}
		/*.address_main .address_sub2 {height:44px; margin:14px 12px; background:#ddd; text-align:center; font-size:18px; line-height:44px; color:#666; border:1px solid #d4d4d4;}*/
.select { -webkit-appearance: none }

    .carrier_input_info {height:auto;width:100%; background:#fff; margin-top:14px; border-bottom:1px solid #e8e8e8; border-top:1px solid #e8e8e8;}
    .carrier_input_info .row { padding:0 10px; height:40px; background:#fff; border-bottom:1px solid #e8e8e8; line-height:40px; color:#999;}
    .carrier_input_info .row .title {height:40px; width:85px; line-height:40px; color:#444; float:left; font-size:16px;}
    .carrier_input_info .row .info { width:100%;float:right;margin-left:-85px; }
    .carrier_input_info .row .inner { margin-left:85px; }
    .carrier_input_info .row .inner input {height:30px; color:#666;background:transparent;margin-top:0px; width:100%;border-radius:0;padding:0px; margin:0px; border:0px; float:left; font-size:16px;}

    .addorder_price .line .nav {height:22px; width:40px; background:#ccc; margin:3px 0px; float:right; border-radius:40px;}
.addorder_price .line .on {background:#4ad966;}
.addorder_price .line .nav nav {height:20px; width:20px; background:#fff; margin:1px; border-radius:20px;}
.addorder_price .line .nav .on {margin-left:19px;}
.cnum {height:20px; width:61px; border:1px solid #e2e2e2; }
.cnum .leftnav {height:20px; width:19px; float:left; border-right:1px solid #e2e2e2; background:#eee; color:#6b6b6b; text-align:center; line-height:20px; font-size:18px; font-weight:bold;}
.cnum .shownum {height:20px; width:20px; float:left;  border:0px; margin:0px; padding:0px; text-align:center;}
.cnum .rightnav {height:20px; width:19px; float:right; border-left:1px solid #e2e2e2; background:#eee; color:#6b6b6b; text-align:center; line-height:20px; font-size:18px; font-weight:bold;}

.couponcount {float:right; margin-top:12px;  margin-right: 5px; height:16px; width:16px; background:#f30; border-radius:8px; font-size:12px; color:#fff; line-height:16px; text-align: center;}
.mask{width: 100%;height: 100%;background: black;position: fixed;top: 0;opacity: 0.4;}
    .span_tips{background: green;  color: #fff !important;  border-radius: 4px;  padding: 1px;  font-size: 12px;}
    /*定位*/
    .pay_type{}
</style>

<div id='carrier_container'></div>
<div id='dispatch_container'></div>
<div id='address_container'></div>
<?php  if($g_num ==1) { ?>
<div id='confirm_container' style="height: 850px"></div>
<?php  } else if($g_num == 2) { ?>
<div id='confirm_container' style="height: 950px"></div>
<?php  } else if($g_num == 3) { ?>
<div id='confirm_container' style="height: 1050px"></div>
<?php  } else if($g_num == 4) { ?>
<div id='confirm_container' style="height: 1150px"></div>
<?php  } else if($g_num == 5) { ?>
<div id='confirm_container' style="height: 1250px"></div>
<?php  } else if($g_num == 6) { ?>
<div id='confirm_container' style="height: 1350px"></div>
<?php  } else if($g_num == 7) { ?>
<div id='confirm_container' style="height: 1450px"></div>
<?php  } else if($g_num == 8) { ?>
<div id='confirm_container' style="height: 1550px"></div>
<?php  } else if($g_num == 9) { ?>
<div id='confirm_container' style="height: 1650px"></div>
<?php  } else if($g_num == 10) { ?>
<div id='confirm_container' style="height: 1750px"></div>
<?php  } else { ?>
<div id='confirm_container'></div>
<?php  } ?>
<input type="hidden" id="can_buy" value="1">
<script id='tpl_address_list' type='text/html'>
    <div class="chooser choose_address" >
        <%each list as address%>
        <div class="address"
             data-addressid='<%address.id%>'
             data-realname='<%address.realname%>'
             data-mobile='<%address.mobile%>'
             data-addrArea = '<%address.province%> <%address.city%> <%address.area%>'
             data-address='<%address.address%>'
             data-type = '<%address.type%>'
             data-isdefault = '<%address.isdefault%>'
             >
            <div class="ico" >
							<%if selectedAdressID==address.id%>
								<i class="fa fa-check-circle" style="color:#169959;"></i>
							<%else%>
								<i class="fa fa-circle-o" style="color:#9b9b9b;"></i>
							<%/if%>
						</div>
            <div class="info">
                <div class='inner'>
                    <div class="name"><%address.realname%><span><%address.mobile%></span>
                        <%if address.isdefault == 1%>
                        <span class="addressType" style="color:#fc3a71;border:1px solid #fc3a71;">默认</span>
                        <%/if%>
                        <%if address.type > 0%>
                        <%if address.type == 1%>
                        <span class="addressType" style="color:#169959;border:1px solid #169959;">家</span></div>
                        <%/if%>
                        <%if address.type == 2%>
                        <span class="addressType" style="color:#169959;border:1px solid #169959;">公司</span></div>
                         <%/if%>
                        <%if address.type == 3%>
                         <span class="addressType" style="color:#169959;border:1px solid #169959;">其他</span></div>
                        <%/if%>
                        <%/if%>
                    <div class="addrArea"><%address.province%> <%address.city%> <%address.area%></div>
                    <div class="addr"><%address.address%></div>
                </div>
            </div>
            <div class="edit"><i class='fa fa-angle-right' style="font-size:24px;color:#c6c6c6;"></i></div>
        </div>
        <%/each%>
        <div class="add_address"><i class="fa fa-plus-circle" style="margin-left:3%; margin-right:10px; line-height:44px; font-size:20px;color:#fc3a71;"></i>新增地址</div>
    </div>
</script>

<script id='tpl_address_new' type='text/html'>
    <input type='hidden' id='edit_addressid' value="<%address.id%>" />
    <div class="address_main">
			<div class="line_wrap">
        <div class="line"><label>收货人</label><input type="text" placeholder="收件人姓名" id="realname" value="<?php  if(member.realname) { ?><%member.realname%><?php  } ?>" /></div>
        <div class="line"><label>选择省</label><select id="sel-provance" onchange="selectCity();" class="select"><option value="" selected="true">所在省份</option></select></div>
        <div class="line"><label>选择市</label><select id="sel-city" onchange="selectcounty()" class="select"><option value="" selected="true">所在城市</option></select></div>
        <div class="line"><label>选择区</label><select id="sel-area" class="select"><option value="" selected="true">所在地区</option></select></div>

        <div class="line"><label>详细地址</label><input type="text" placeholder="街道，小区，门牌号" id="address" value="<?php  if(address.address) { ?><%address.address%><?php  } ?>"/></div>
				<div class="line"><label>联系方式</label><input type="text" placeholder="收货人手机"  id="mobile" value="<?php  if(address.mobile) { ?><%address.mobile%><?php  } ?>"/></div>
				<div class="line">
					<label>地址类型</label>
					<ul class="address_type">
                        <input type="hidden" name="type" id="add_type" value="<%address.type%>">
						<li onclick="check_address_type(this)" data-type="1"><i <%if address.type == 1%>  style="color:rgb(22, 153, 89);"<%/if%> class="fa <%if address.type == 1%> fa-check-circle <%else%> fa-circle-o<%/if%>"></i>家</li>
						<li onclick="check_address_type(this)" data-type="2"><i <%if address.type == 2%>  style="color:rgb(22, 153, 89);"<%/if%>  class="fa <%if address.type == 2%> fa-check-circle <%else%> fa-circle-o<%/if%>"></i>公司</li>
						<li onclick="check_address_type(this)" data-type="3"><i <%if address.type == 3%>  style="color:rgb(22, 153, 89);"<%/if%>  class="fa <%if address.type == 3%> fa-check-circle <%else%> fa-circle-o<%/if%>"></i>其他</li>
					</ul>
				</div>
				<!--<div class="line">
					<label>设为默认地址</label>
					<div class="switch off" onclick="check_default(this)"></div>
				</div>-->
<!--        <div class="line"><input type="text" placeholder="邮政编码"  id="zipcode" value="<?php  if(address.zipcode) { ?><%address.zipcode%><?php  } ?>"/></div>-->
			</div>
				<div class="address_sub_btn">
                    <div class="address_sub2 address_sub3">取消</div>
					<!-- <div class="address_sub3">删除</div>-->
	        <div class="address_sub1">保存</div>
				</div>
        <!-- <div class="address_sub2">取消</div> -->
    </div>
</script>

<script id='tpl_carrier_list' type='text/html'>
    <div class="chooser choose_carrier">
        <%each carrier_list as carrier index%>
        <div class="address carrier"
             data-index='<%index%>'
             data-id='<%carrier.id%>'
             data-realname='<%carrier.realname%>'
             data-mobile='<%carrier.mobile%>'
             data-address='<%carrier.address%>'
             >
            <div class="ico" ><%if selectedCarrierIndex==index%><i class="fa fa-check" style="color:#0c9"></i><%/if%></div>
            <div class="info">
                <div class='inner'>
                    <div class="name"><%carrier.realname%>(<%carrier.mobile%>)</div>
                    <div class="addr"><%carrier.address%></div>
                </div>
            </div>
        </div>
        <%/each%>
    </div>
</script>


<script id='tpl_confirm_info' type='text/html'>

    <!-- <div class="addorder_topbar">
        <div class="ico"><i class="fa fa-file-text-o"></i></div>
        <div class="tips">确认订单后请尽快付款，<br>过时订单将自动取消。</div>
    </div> -->
    <input type="hidden"  id="isverify" value="<%if isverify || isvirtual || goods.type==2%>true<%else%>false<%/if%>" />

	  <?php  if($show==1) { ?>
			<%if isverify || isvirtual || goods.type==2%>
			 <input type="hidden"  id="dispatchtype" value="0" />
			  <div class="carrier_input_info" >
					<div class="row">
						<div class='title'>联系人姓名</div>
						<div class='info'>
								<div class='inner'><input type="text" placeholder="联系人姓名" id="carrier_input_realname" value="<%member.realname%>" style='height:35px;'/></div>
						</div>
					</div>
					<div class="row">
						<div class='title'>联系人手机</div>
						<div class='info'>
							  <div class='inner'><input type="text" placeholder="联系人手机"  id="carrier_input_mobile" value="<%member.mobile%>" style='height:35px;'/></div>
						</div>
					</div>
			</div>
			<%else%>
			<input type="hidden"  id="dispatchtype" value="0" />
			<%if carrier_list.length>0%>
			<div class="addorder_nav">
				<div class="nav selected" data-nav='0'>快递配送</div>
				<div class="nav"  data-nav='1'>上门自提</div>
			</div>
			<%/if%>
			<div class="addorder_user addorder_user_0">
				<input type='hidden' id='addressid' value='<%address.id%>' />
				<div class="info" id='address_select' <%if !address %>style='display:none'<%/if%>>
					 <div class="ico">
						 <!-- <i class="fa fa-map-marker"></i> -->
						 <img src="../addons/manor_shop/template/mobile/tangsheng/static/images/address.png" />
					 </div>
					 <div class='info1'>
						 <div class='inner'>
								<div class="user"><span id='address_realname'><%address.realname%></span><span id='address_mobile'><%address.mobile%></span>
                                   <span class="type_tips">
                                       <%if address.isdefault == 1%>
                                       <span class="addressType" style="color:#fc3a71;border:1px solid #fc3a71;">默认</span>
                                       <%/if%>
                                       <%if address.type >0%>
                                       <%if address.type == 1%>
                                       <span class="addressType" style="color:#169959;border:1px solid #fc3a71;">家</span>
                                       <%/if%>
                                       <%if address.type == 2%>
                                       <span class="addressType" style="color:#169959;border:1px solid #169959;">公司</span>
                                       <%/if%>
                                       <%if address.type == 3%>
                                       <span class="addressType" style="color:#169959;border:1px solid #169959;">其他</span>
                                       <%/if%>
                                       <%/if%>
                                   </span>
                                </div>
								<div class="addrArea"><%address.province%> <%address.city%> <%address.area%></div>
								<div class="address"><span id='address_address'><%address.address%></span></div>
						 </div>
					 </div>
					 <div class="ico2"><i class='fa fa-angle-right fa-2x' style="color:#c6c6c6;"></i></div>
				</div>
				<div class='info' id='address_new'  <%if address %>style='display:none'<%/if%>>
					<div class="ico"><i class="fa fa-plus-circle"></i></div>
					<div class='info1'>
						 <div class='inner'>
							 <div class="user" style='padding-top:8px;'>新增地址</div>
						 </div>
					</div>
					<div class="ico2"><i class='fa fa-angle-right fa-2x'></i></div>
				</div>

			</div>
			<%if carrier%>
			<div class="addorder_user addorder_user_1" style='display:none'>
				<input type='hidden' id='carrierindex' value='0' />
				<input type='hidden' id='carrierid' value='<%carrier.id%>' />
				<div class="info" id='carrier_select' >
					<div class="ico"><i class="fa fa-map-marker"></i></div>
					<div class='info1'>
						 <div class='inner'>
							 <div class="user">自提地点：<span id='carrier_realname'><%carrier.realname%></span>(<span id='carrier_mobile'><%carrier.mobile%></span>)</div>
							 <div class="address"><span id='carrier_address'><%carrier.address%></span></div>
						 </div>
					</div>
					<div class="ico2"><i class='fa fa-angle-right fa-2x'></i></div>
				</div>

			</div>
			<%/if%>
			<div class="carrier_input_info" style='display:none' >
					<div class="row">
						<div class='title'>提货人姓名</div>
						<div class='info'>
								<div class='inner'><input type="text" placeholder="提货人姓名" id="carrier_input_realname" value="<%member.realname%>" style='height:35px;'/></div>
						</div>
					</div>
					<div class="row">
						<div class='title'>提货人手机</div>
						<div class='info'>
							  <div class='inner'><input type="text" placeholder="提货人手机"  id="carrier_input_mobile" value="<%member.mobile%>" style='height:35px;'/></div>
						</div>
					</div>
			</div>


			<%if dispatch%>
			<div class="addorder_exp" id='dispatch_select' style="display: none;">
				<input type='hidden' id='dispatchid' value='<%dispatch.id%>' />
				<div class="t1">配送方式</div>
				<div class="ico"><i class='fa fa-angle-right fa-2x'></i></div>
				<div class="t2"><span class='dispatchname'><%dispatch.dispatchname%></span> <span class='dispatchprice'><%dispatch.price%></span>元</div>
			</div>
			<%/if%>

			<%/if%>
    <?php  } ?>


    <div class="addorder_good">
        <!-- <div class="ico"><i class="fa fa-gift" style="color:#666; font-size:20px;"></i></div>
        <div class="shop"><%set.name%></div> -->
        <%each goods as g%>
        <div class="good" data-totalmaxbuy="<%g.totalmaxbuy%>">

            <div class="img"  onclick="location.href='<?php  echo $this->createMobileUrl('shop/detail')?>&id=<%g.goodsid%>'"><img src="<%g.thumb%>"/></div>
            <div class='info' onclick="location.href='<?php  echo $this->createMobileUrl('shop/detail')?>&id=<%g.goodsid%>'">
                <div class='inner'>
                       <div class="name">
                           <%if g.isnodiscount=='0' && g.dflag=='1'%><span style='color:red'>[折扣]</span><%/if%>
                           <%g.title%>
                       </div>
                    <%if sendcity[g.dispatchid]%>
                    <span class="span_tips"><% sendcity[g.dispatchid]%>发货</span>
                    <%/if%>
                    <%if g.is_full_cat_power == 0 && saleset.enoughfree==1%>
                    <span class="span_tips">满额包邮 </span>
                    <%/if%>
                    <%if g.is_full_gift == 0 && saleset.enoughs_goods_power==1%>
                    <span class="span_tips">满赠</span>
                    <%/if%>
                    <%if g.is_full_money == 0 && saleset.enoughs_reduce_power==1%>
                    <span class="span_tips">满减</span>
                    <%/if%>
                       <div class='option'><%if g.optionid!='0'%>规格:  <%g.optiontitle%><%/if%></div>
											 <div class='pnum'>￥<span class='marketprice'><%g.marketprice%></span></div>
                </div>
            </div>
            <div class="price">
                <%if changenum%>
                <div class="cnum"><div class="leftnav">-</div><input type="text" class="shownum" value="<%g.total%>" /><div class="rightnav">+</div></div>
                <%else%>
                <div class='pnum'><span class='total'>×<%g.total%></span></div>
                <%/if%>
            </div>
            <input type="hidden" class="area_city" value="<%g.express_area%>">
        </div>
        <%/each%>
        <%if marketing.full_gift%>
            <p>满赠礼品</p>
            <div id="man_zheng">
                <%if marketing.full_gift.id%>
                <div class="good">
                    <div class="img"  onclick="location.href='<?php  echo $this->createMobileUrl('shop/detail')?>&id=<%marketing.full_gift.id%>'"><img src="<%marketing.full_gift.thumb%>"/></div>
                    <div class='info' onclick="location.href='<?php  echo $this->createMobileUrl('shop/detail')?>&id=<%marketing.full_gift.id%>'">
                        <div class='inner'>
                            <div class="name">
                                <%marketing.full_gift.title%>
                            </div>
                            <div class='option' style="color: red;">满<%marketing.full_gift.money.enough_good%>赠送</div>
                        </div>
                    </div>
                    <div class="price">
                        <div class='pnum'>￥<span class='marketprice'>0.00</span></div>
                        <div class='pnum'><span class='total'>×1</span></div>
                    </div>
                    <input type="hidden" id="give_good" value="<%marketing.full_gift.id%>"/>
                </div>
                <%else%>
                <div class="good" style="color: #0bba07;font-size: 14px;height: 100px" onclick="local_index()">
                    温馨提示:<br/>您还差<%marketing.full_gift.merge_money%>元就能获得赠送的 【<%marketing.full_gift.goods.title%>】 一份,快去拼单吧
                </div>
                <%/if%>
            </div>
        <%/if%>
        <input type="hidden" id="goods" value="<%each goods as g%><%g.goodsid%>,<%g.optionid%>,<%g.total%>|<%/each%>"/>
        <div class="text">共 <span id="goodscount" style="color:#444;"><%if marketing.full_gift.id%><%total+1%><%else%><%total%><%/if%></span> 件商品 合计：<span style="color:#fc3a71;">￥<span class='goodsprice' style="color:#fc3a71;"><%goodsprice%></span></span></div>
    </div>
<?php  if(!empty($order_formInfo)) { ?>
	 <div class="carrier_input_info" >
		 <?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('diyform/formcss', TEMPLATE_INCLUDEPATH)) : (include template('diyform/formcss', TEMPLATE_INCLUDEPATH));?>
		  <style type='text/css'>
				     .diyform_main .dline { margin:0 10px;}
					 .diyform_main .dline .dtitle { color:#666;}
			 </style>
    <?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('diyform/formfields', TEMPLATE_INCLUDEPATH)) : (include template('diyform/formfields', TEMPLATE_INCLUDEPATH));?>
	 </div>
    <?php  } ?>

    <div class="addorder_price" >
        <input type="hidden" id="weight" value="<%weight%>" />
        <div class="price" style="border:none;">
            <div class="line" style="line-height:42px;border-bottom:1px solid #d7d7d7;">商品金额<span>￥<span class='goodsprice'><%goodsprice%></span></span></div>

            <div class="line" style="line-height:42px;">+运费<span>￥<span class='dispatchprice'><%dispatchprice%></span></span></div>
            <%if discountprice>0%>
            <div class="line" style="line-height:42px;">会员折扣<%if discount>0%>(<d class='memberdiscount'><%discount%></d>折)<%/if%><span>-￥<span class='discountprice'><%discountprice%></span></span></div>
            <%/if%>



            <div id="deductenough" class="line" style="line-height:26px;<%if !saleset.showenough %>display:none<%/if%>"  >单笔满 <d style='color:#ff6600' id="deductenough_enough"><%saleset.enoughmoney%></d> 元立减  <span>-￥<span id="deductenough_money"><%if saleset.showenough %><%saleset.enoughdeduct%><%/if%></span><span></div>
				<?php  if($hascouponplugin) { ?>

			<div id="coupondeduct_div" class="line" style="line-height:26px;display:<%if coupons==""%>none<%/if%>"><d id='coupondeduct_text'>优惠券优惠</d><span>-￥<span id="coupondeduct_money"><%coupons.deduct%></span><span></div>
				  <?php  } ?>

        </div>
    </div>
		<div class="message">
			<input type="text" id="remark" placeholder="(选填)给卖家留言" />
		</div>
	<input type="hidden" id="couponid" value="<?php  echo $_GPC['coupondataid'];?>" />

    <div class="addorder_price pay_type">
        <%if pay_type.wechat.success%>
        <input type="hidden" id="pay_type" value="weixin">
        <div class="line" style="line-height:42px;" onclick="change_balance(this)" data-id="weixin">
            <i class="fa fa-check-circle" style="color:#169959;"></i> 微信支付
        </div>
        <%/if%>
        <%if pay_type.alipay.success%>
        <div class="line" style="line-height:42px;border-top:1px solid #d7d7d7;" onclick="change_balance(this)" data-id="alipay">
            <i class="fa fa-circle-o" style="color:#169959;"></i> 支付宝支付
        </div>
        <%/if%>
        <%if pay_type.unionpay.success%>
        <div class="line" style="line-height:42px;border-top:1px solid #d7d7d7;" onclick="change_balance(this)" data-id="unionpay">
            <i class="fa fa-circle-o" style="color:#169959;"></i> 银联支付
        </div>
        <%/if%>

    </div>
    <div class="addorder_price">
        <div class="line" style="line-height:42px;" onclick="location.href='<?php  echo $this->createMobileUrl('member/recharge')?>'">
            余额充值<i class="fa fa-angle-right fa-2x" style="color:#c6c6c6;float: right;line-height: 40px"></i>
        </div>
    </div>


    <%if deductcredit>0 || deductcredit2>0  %>
     <div class="addorder_price" style="margin-top:5px;">
            <div class="price" style="border:none;">
            <%if deductcredit>0 %>
            <div class="line" style="line-height:35px;"><d id="deductcredit_info"><%deductcredit%></d> 积分可抵扣 <d id="deductcredit_money" style='color:#ff6600'><%deductmoney%></d> 元
                <div id='deductcredit' class="nav" on="0" credit="<%deductcredit%>" money='<%deductmoney%>'><nav></nav></div>
            </div>
            <%/if%>

            <%if deductcredit2>0 %>
            <div class="line" style="line-height:35px;">余额可抵扣 <d id="deductcredit2_money" style='color:#ff6600'><%deductcredit2%></d> 元
                <div id='deductcredit2' class="nav" on="0" credit2="<%deductcredit2%>"><nav></nav></div>
            </div>
            <%/if%>

        </div>
    </div>
    <%/if%>
    <div id="coupondiv" class="addorder_price" style="margin-top:5px;margin-bottom: 85px;<%if !hascoupon%>display:none<%/if%>" >
        <div class="price" style="border:none;">

            <div class="line" style="line-height:40px;" id="selectcoupon">
                <d id="couponselect"><%if coupons%>为您默认选中<%coupons.couponname%><%else%>我要使用优惠券<%/if%></d>
                <div class="ico2" style="float:right;color:#999;margin-top:2px;"><i class='fa fa-angle-right fa-2x'></i></div>
                <div class="couponcount"><%couponcount%></div>
            </div>


        </div>

    </div>
    <div class="addorder_pay">
        <div class="paysub">确认订单</div>
        <input type="hidden" id="orderprice" title="<%if coupons%><% (realprice-coupons.deduct).toFixed(2)%><%else%><%realprice%><%/if%>">
        <span>需付：￥<t class='totalprice'><%if coupons%><% (realprice-coupons.deduct).toFixed(2)%><%else%><%realprice%><%/if%></t></span>
    </div>
</script>
<script id="tpl_img" type="text/html">
    <div class='img' data-img='<%filename%>'>
        <img src='<%url%>' />
        <div class='minus'><i class='fa fa-minus-circle'></i></div>
    </div>
</script>
<?php  if($hascouponplugin) { ?>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('coupon/chooser', TEMPLATE_INCLUDEPATH)) : (include template('coupon/chooser', TEMPLATE_INCLUDEPATH));?>
<?php  } ?>
<script type="text/javascript">

    function change_balance(obj){
        $('#pay_type').val($(obj).data('id'));
        $('.pay_type .line i').removeClass('fa-check-circle').addClass('fa-circle-o');
        var i = $(obj).find('i');
        if(i.hasClass('fa-check-circle')) {
            i.removeClass('fa-check-circle');
            i.addClass('fa-circle-o');
        } else {
            i.removeClass('fa-circle-o');
            i.addClass('fa-check-circle');
        }
    }
var fromcart = 0;
    require(['tpl', 'core'], function(tpl, core) {

        var location_gps = JSON.parse(localStorage.getItem('location_gps'));
        core.json('order/confirm', {id:'<?php  echo intval($_GPC['id'])?>', optionid:'<?php  echo intval($_GPC['optionid'])?>', total:'<?php  echo intval($_GPC['total'])?>',coupondataid:"<?php  echo $_GPC['coupondataid'];?>",cartids:"<?php  echo $_GPC['cartids'];?>"}, function(json){
        if(json.status==-1){
            location.href=json.result.url;
            return;
        }
            var marketing = json.result.marketing;
            var total_goods_num = json.result.total;
        $('#confirm_container').html(tpl('tpl_confirm_info', json.result));

            <?php  if(!empty($order_formInfo)) { ?>
                <?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('diyform/common_js', TEMPLATE_INCLUDEPATH)) : (include template('diyform/common_js', TEMPLATE_INCLUDEPATH));?>
                <?php  } ?>

           $('.leftnav').click(function(){
                var input =$(this).next();
                if(!input.isInt()){
                    input.val('1');
                }
                var num = parseFloat( input.val() );
                num--;
                if(num<=0){
                    num=1;
                }
                input.val(num);
                    $('#goodscount').html(num);
                marketprice = parseFloat( $(this).closest('.good').find('.marketprice').html().replace(",","") ) * num;
                $('.goodsprice').html( marketprice.toFixed(2) );
                if( $('.memberdiscount').length>0){
                                var discountprice = marketprice - parseFloat( $('.memberdiscount').html().replace(",","") ) / 10 * marketprice;
                $('.discountprice').html( discountprice.toFixed(2) );
            }
                var zt =  $('.addorder_nav .selected').data('nav') =='1';
                getDispatchPrice(zt);

            })

             $('.rightnav').click(function(){
                var maxbuy = parseInt( $(this).closest('.good').data('totalmaxbuy'));

                var input =$(this).prev();
                if(!input.isInt()){
                    input.val('1');
                }
                var num = parseInt( input.val() );
                num++;

                if(num>maxbuy ){
                    num=maxbuy;
                    core.tip.show("您最多购买 " + maxbuy + " 件");
                }
                input.val(num);
                $('#goodscount').html(num);
                var marketprice = parseFloat( $(this).closest('.good').find('.marketprice').html().replace(",","") ) * num;
                $('.goodsprice').html( marketprice.toFixed(2) );
                     if( $('.memberdiscount').length>0){
                var discountprice = marketprice - parseFloat( $('.memberdiscount').html().replace(",","") ) / 10 * marketprice;
                $('.discountprice').html( discountprice.toFixed(2) );
                     }
               var zt =  $('.addorder_nav .selected').data('nav') =='1';
                getDispatchPrice(zt);

            });

     $('.shownum').blur(function(){

               var maxbuy = parseInt( $(this).closest('.good').data('totalmaxbuy'));

                var input =$(this);
                if(!input.isInt()){
                    input.val('1');
                    return;
                }
                if(parseInt(input.val())<0){
                    input.val('1');
                    return;
                }
                var num = parseInt( input.val() );


               if(num>maxbuy ){
                    num=maxbuy;
                    core.tip.show("您最多购买 " + maxbuy + " 件");
                }
                input.val(num);
                $('#goodscount').html(num);
                   marketprice = parseFloat( $(this).closest('.good').find('.marketprice').html().replace(",","") ) * num;
                $('.goodsprice').html( marketprice.toFixed(2) );
                     if( $('.memberdiscount').length>0){
                var discountprice = marketprice - parseFloat( $('.memberdiscount').html().replace(",","") ) / 10 * marketprice;
                $('.discountprice').html( discountprice.toFixed(2) );
                     }

               var zt =  $('.addorder_nav .selected').data('nav') =='1';
                getDispatchPrice(zt);

           })
        fromcart = json.result.fromcart;


        if (json.result.carrier_list.length > 0) {

            //选择快递或字提
            $('.addorder_nav .nav').click(function() {
                var nav = $(this).data('nav');
                $('.addorder_nav .nav').removeClass('selected');
                $(this).addClass('selected');
                $('.addorder_user').hide();
                $('.addorder_user_' + nav).show();
                if (nav == '1') {
                    $('.carrier_input_info').show();
                    $('.addorder_exp').hide();
                    getDispatchPrice(true);
                }
                else {
                    $('.carrier_input_info').hide();
                    $('.addorder_exp').show();
                    getDispatchPrice();
                }
                $('#dispatchtype').val(nav);
            });
            //选择自提
            $('#carrier_select').click(function() {
                json.result.selectedCarrierIndex = $("#carrierindex").val();

                $('#carrier_container').html(tpl('tpl_carrier_list', json.result));
                $(".chooser").height($(document.body).height());
                $(".choose_carrier").animate({right: "0px"}, 200);
                $('.carrier').click(function() {
                    var obj = $(this);
                    $("#carrierindex").val(obj.data('index'));
                    $("#carrierid").val(obj.data('id'));
                    $("#carrier_realname").html(obj.data('realname'));
                    $("#carrier_mobile").html(obj.data('mobile'));
                    $("#carrier_address").html(obj.data('address'));
                    $(".choose_carrier").animate({right: "-100%"}, 100);
                })
            })
        }

        //选择地址
        $('#address_select').click(function() {

            core.json('shop/address', {}, function(addresslist_json) {
                //默认地址
                addresslist_json.result.selectedAdressID = $("#addressid").val();

                $('#address_container').html(tpl('tpl_address_list', addresslist_json.result));
                $('.address .ico,.address .info').click(function() {
                    var $this = $(this).parent();
                    var ad_type = $this.data('type');
                    var isdefault = $this.data('isdefault');
                    var append = '';
                    var t = '';
                    if(isdefault) {
                        append += '<span class="addressType" style="color:#fc3a71;border:1px solid #fc3a71;">默认</span>';
                    }
                    if(ad_type == 1) {
                        t = '家';
                    } else if(ad_type == 2) {
                        t = '公司';
                    } else if(ad_type == 3) {
                        t = '其他';
                    }
                    if(ad_type > 0) {
                        append += '<span class="addressType" style="color:#169959;border:1px solid #169959;">'+t+'</span>';
                    }
                    $('.type_tips').html(append);
                    console.log($this.data('addrarea'));
                    $("#addressid").val($this.data('addressid'));
                    $("#address_realname").html($this.data('realname'));
                    $("#address_mobile").html($this.data('mobile'));
                    $(".addrArea").html($this.data('addrarea'));
                    $("#address_address").html($this.data('address'));
                    $(".choose_address").animate({right: "-100%"}, 200);
                    //重新获取运费
                    getDispatchPrice();
                    setTimeout(function () {
                        setaddress();
                    }, 500);
                });
                //地址编辑
                $('.address .edit').click(function() {
                    var addressid = $(this).parent().data('addressid');
                    core.json('shop/address', {op: 'get', id: addressid}, function(getaddress_json) {
                        $('#address_container').html(tpl('tpl_address_new', getaddress_json.result));
                        $(".chooser").height($(document.body).height());
                        $(".address_main").animate({right: "0px"}, 200);
                        var address = getaddress_json.result.address;
                        cascdeInit(address.province, address.city, address.area);
                        $('.address_sub2').click(function() {
                            $(".address_main").animate({right: "-100%"}, 200);
                        });
                        $('.address_sub1').click(function() {
                            saveAddress($(this));
                        });

                    }, true);
                    if($(this).attr('class') != 'edit') {
                        setTimeout(function () {
                            setaddress()
                        }, 500);
                    }
                });
				$(".chooser").height($(document.body).height());
                $(".choose_address").animate({right: "0px"}, 200);
                $('.add_address').click(function() {
                    addAddress($(this));
                })
            }, true);
        });


        //保存地址
        function saveAddress(obj) {
            if (obj.attr('saving') == '1') {
                return;
            }

            if ($('#realname').isEmpty()) {
                core.tip.show('请输入收件人!');
                return;
            }
            if (!$('#mobile').isMobile()) {
                core.tip.show('请输入正确的联系电话!');
                return;
            }
	   if($('#sel-provance').val()=='请选择省份'){
                    core.tip.show('请选择省份!');
                    return;
                }
	       if($('#sel-city').val()=='请选择城市'){
                    core.tip.show('请选择城市!');
                    return;
                }
		  if($('#sel-area').val()=='请选择地区'){
                    core.tip.show('请选择地区!');
                    return;
                }
            if ($('#address').isEmpty()) {
                core.tip.show('请输入详细地址!');
                return;
            }
            $('.address_sub1').html('正在处理...').attr('disabled', true);
            obj.attr('saving', '1');
            core.json('shop/address', {
                op: 'submit',
                id: $('#edit_addressid').val(),
                addressdata: {
                    realname: $('#realname').val(),
                    mobile: $('#mobile').val(),
                    address: $('#address').val(),
                    province: $('#sel-provance').val(),
                    city: $('#sel-city').val(),
                    area: $('#sel-area').val(),
                    type:$('#add_type').val(),
                 //   zipcode: $('#zipcode').val(),
                }
            }, function(saveaddress_json) {
                if (saveaddress_json.status == 1) {
                    $("#addressid").val(saveaddress_json.result.addressid);
                    $("#address_realname").html($('#realname').val());
                    $("#address_mobile").html($('#mobile').val());
                    $(".addrArea").html($('#sel-provance').val() + $('#sel-city').val() + $('#sel-area').val());
                    $("#address_address").html($('#address').val());
                    $("#address_select").show();
                    $(".address_main").animate({right: "-100%"}, 200);
                    $('#address_new').hide();
                    var ad_type = $('#add_type').val();
                    var isdefault = $('#isdefault').val();
                    var append = '';
                    var t = '';
                    if(isdefault) {
                        append += '<span class="addressType" style="color:#fc3a71;border:1px solid #fc3a71;">默认</span>';
                    }
                    if(ad_type == 1) {
                        t = '家';
                    } else if(ad_type == 2) {
                        t = '公司';
                    } else if(ad_type == 3) {
                        t = '其他';
                    }
                    if(ad_type > 0) {
                        append += '<span class="addressType" style="color:#169959;border:1px solid #169959;">'+t+'</span>';
                    }
                    $('.type_tips').html(append);
                    getDispatchPrice();
                }
                else {
                    core.tip.show('保存失败,请重试');
                }
                obj.removeAttr('saving');
            }, true, true);
            setTimeout(function () {
                setaddress();
            }, 500);
        }
        function getDispatchPrice(zt) {
            var goodsprice = parseFloat($('.goodsprice').html().replace(',',''));
            var discountprice =0;
            if($('.discountprice').length>0){
                 discountprice = parseFloat($(".discountprice").html().replace(',',''));
            }
            var goodscount = parseInt($('.shownum').val());
            if(isNaN(goodscount)) {
                goodscount = 1;
            }
            totalprice = goodsprice - discountprice;
            //重新获取运费
            if( $('.shownum').length>0){
                totalprice = parseFloat( $('.marketprice').html() ) * parseInt($('.shownum').val());
                var goodsinfo = $('#goods').val().split('|')[0];
                var goods = goodsinfo.split(',');
                var goodsid = goods[0];
                var optionid = goods[1];
                var num = parseInt( $('.shownum').val());
                $('#goods').val(goodsid + "," + optionid +"," + num + '|');
            }

            core.json('order/confirm', {
                op: 'getdispatchprice',
                totalprice:totalprice,
                full_cat_power: marketing.marketing_price.full_cat_power * goodscount,
                full_gift:marketing.marketing_price.full_gift * goodscount,
                full_money:marketing.marketing_price.full_money * goodscount,
                addressid: $('#addressid').val(),
                dispatchid: $('#dispatchid').val(),
                dflag: zt,
	            goods: $('#goods').val()
            }, function(getjson) {
                if (getjson.status == 1) {
                    if(zt){
                        $('.dispatchprice').html('0.00');
                    } else {
                        if(getjson.result.price == 0){
                            $('.dispatchprice').html('0.00');
                        } else {
                            $('.dispatchprice').html(getjson.result.price);
                        }
                    }

                    if(getjson.result.deductcredit){
                        $('#deductcredit_money').html( getjson.result.deductmoney);
                        $('#deductcredit_info').html( getjson.result.deductcredit);
                        $("#deductcredit").attr('credit',getjson.result.deductcredit);
                        $("#deductcredit").attr('money',getjson.result.deductmoney);
                    }

                    if(getjson.result.deductcredit2){
                        $('#deductcredit2_money').html( getjson.result.deductcredit2);
                        $("#deductcredit2").attr('credit2',getjson.result.deductcredit2);
                    }

					if(getjson.result.hascoupon){
						$('#coupondiv').show();
						$('#coupondiv .couponcount').html(getjson.result.couponcount);
						bindCouponEvents();
					}else{
						$('#couponid').val('');
						$('#couponselect').html('我要使用优惠券');
						$('#coupondiv').hide();
					}

					if(getjson.result.deductenough_money>0){
						$('#deductenough').show();
						$('#deductenough_money').html( getjson.result.deductenough_money);
						$('#deductenough_enough').html( getjson.result.deductenough_enough);
					} else{
						$('#deductenough').hide();
						$('#deductenough_money').html( '0');
						$('#deductenough_enough').html( '0');
					}
                    if(getjson.result.send_gift.id > 0) {
                        $('#goodscount').html(total_goods_num + 1);
                        var url = "location.href="+"<?php  echo $this->createMobileUrl('shop/detail')?>"+getjson.result.send_gift.id;
                        $('#man_zheng').html("<div class='good'><div class='img'  onclick='"+url+"'><img src='"+getjson.result.send_gift.thumb+"'/></div><div class='info' onclick="+url+"> <div class='inner'> <div class='name'>"+getjson.result.send_gift.title+" </div> <div class='option' style='color: red;'>满"+getjson.result.send_gift.money.enough_good+"赠送</div> </div> </div> <div class='price'> <div class='pnum'>￥<span class='marketprice'>0.00</span></div> <div class='pnum'><span class='total'>×1</span></div> </div> <input type='hidden' id='give_good' value='"+getjson.result.send_gift.id+"'/> </div>");
                    } else if(!getjson.result.send_gift || getjson.result.send_gift.need_money) {
                        $('#man_zheng').html("<div class='good' onclick='local_index()'  style='color: #0bba07;font-size: 14px;height: 100px;'>温馨提示:<br/>您还差"+getjson.result.send_gift.need_money+"元就能获得赠送的 【"+getjson.result.send_gift.goods.title+"】 一份,快去拼单吧</div>");
                    }
                    calctotalprice();
                       if( $('.shownum').length>0){

					var goodsinfo = $('#goods').val().split('|')[0];
					var goods = goodsinfo.split(',');
					var goodsid = goods[0];
					var optionid = goods[1];
					var num = parseInt( $('.shownum').val());
					$('#goods').val(goodsid + "," + optionid +"," + num + '|');
				}

                }
            }, true);
        }
        //新增地址
        function addAddress(obj) {

            core.json('shop/address', {'op': 'new'}, function(addaddress_json) {

                var result = addaddress_json.result;
                $('#address_container').html(tpl('tpl_address_new', result));
                if(location_gps && location_gps.addressComponent && location_gps.addressComponent.province) {
                    cascdeInit(location_gps.addressComponent.province, location_gps.addressComponent.city, location_gps.addressComponent.district);
                } else {
                    cascdeInit(result.address.province, result.address.city);
                }
                <?php  if($trade['shareaddress']=='1') { ?>
                    var shareAddress = <?php  echo json_encode($shareAddress)?>;
                                WeixinJSBridge.invoke('editAddress',shareAddress,function(res){
                                    if(res.err_msg=='edit_address:ok'){
                                        $("#realname").val( res.userName  );
                                        $('#mobile').val( res.telNumber );
                                        $('#address').val( res.addressDetailInfo );
                                        cascdeInit(res.proviceFirstStageName,res.addressCitySecondStageName,res.addressCountiesThirdStageName);
                                    }
                    });
                <?php  } ?>
					$(".chooser").height($(document.body).height());
                $(".address_main").animate({right: "0px"}, 200);
                $('.address_sub2').click(function() {
                    $(".address_main").animate({right: "-100%"}, 200);
                });
                $('.address_sub1').click(function() {
                    saveAddress($(this));
                });

            }, true);
        }

        $('#address_new').click(function() {
            addAddress($(this));
        });
        //计算总价
        function calctotalprice() {
            var goodsprice = parseFloat($('.goodsprice').html().replace(',',''));
            var dispatchprice = parseFloat($(".dispatchprice").html().replace(',',''));

            var discountprice =0;
            if($('.discountprice').length>0){
                 discountprice = parseFloat($(".discountprice").html().replace(',',''));
            }
	        var totalprice = goodsprice - discountprice;
            var enoughprice =0;
            if($("#deductenough_money").length>0 && $("#deductenough_money").html()!=''){
                 enoughprice = parseFloat($("#deductenough_money").html().replace(',',''));
            }
	   <?php  if($hascouponplugin) { ?>
	       totalprice = calcCouponPrice(totalprice);
	   <?php  } ?>
            totalprice = totalprice - enoughprice + dispatchprice;
            var deductprice = 0;
            if($("#deductcredit").length>0){
                if($("#deductcredit").attr('on')=='1'){
                    deductprice = parseFloat( $("#deductcredit").attr('money').replace(',','') )

                           if($("#deductcredit2").length>0){
                              var td1 = parseFloat( $("#deductcredit2").attr('credit2').replace(',','') );

                              if(totalprice-deductprice>=0){
                                  var td = totalprice - deductprice;
                                  if(td>td1){
                                      td = td1;
                                  }
                                  $("#deductcredit2_money").html( td.toFixed(2) );
                              }else{
                                   $("#deductcredit2").attr('on','0').removeClass('on');
                              }
                           }

                } else{
                     if($("#deductcredit2").length>0){
                        var td = parseFloat( $("#deductcredit2").attr('credit2').replace(',','') );
                        $("#deductcredit2_money").html( td.toFixed(2) );
                     }

                }
            }
            var deductprice2 = 0;
            if($("#deductcredit2").length>0){
                     if($("#deductcredit2").attr('on')=='1'){
                          deductprice2 = parseFloat( $("#deductcredit2_money").html().replace(',','') );
                     }
             }

            totalprice = totalprice - deductprice - deductprice2-parseFloat($('#coupondeduct_money').html());
            if(totalprice <= 0){
                totalprice = 0;
            }


            $('.totalprice').html(totalprice.toFixed(2));
            return totalprice;
        }
        //选择快递
        $('#dispatch_select').click(function() {

            json.result.selectedDispatchID = $("#dispatchid").val();
            $('#dispatch_container').html(tpl('tpl_dispatch_list', json.result));
			$(".chooser").height($(document.body).height());
            $(".choose_dispatch").animate({right: "0px"}, 200);
            $('.dispatch').click(function() {
                var obj = $(this);
                $("#dispatchid").val(obj.data('dispatchid'));
                $(".dispatchname").html(obj.data('dispatchname'));
                $(".chooser").animate({right: "-100%"}, 100);
                //重新获取运费
                getDispatchPrice();

            })
        });
        var can_buy = document.getElementById('can_buy').value;
        function setaddress() {
            $('#can_buy').val(1);
            var addressid = $("#addressid").val();
            core.json('shop/address', {op: 'get', id: addressid}, function(getaddress_json) {
                var current_city = localStorage.getItem('current_city').split('市')[0];
                var choose_city = getaddress_json.result.address.city;
                localStorage.setItem('choose_city', choose_city);
                var arr = [];
                var citys = [];
                $('.area_city').each(function (k,obj) {
                    var express_area = $(obj).val();
                    if (express_area.length > 0) {
                        arr.push(express_area);
                    }
                });
                if(arr.length > 0) {
                    for (var i=0; i<arr.length; i++) {
                        var item = arr[i].split(';').filter(function(n){return n});
                        if(item.length > 0) {
                            for (var j=0; j< item.length; j++){
                                citys.push(item[j]);
                            }
                        }

                    }
                }
                var new_citys = citys.filter(function (n) {return n;});
                var _return = true;
                if(new_citys.length > 0) {
                    $.each(new_citys, function (k,v) {
                        var vv = v.replace(/[市|辖区|辖县]/g, '');
                        new_citys.push(vv);
                    });
                    if(new_citys.indexOf(choose_city) == -1) {
                        $('#can_buy').val(0);
                        core.tip.show('该城市暂不支持列表中某些商品,请重新选择收货地址');
                        $('.paysub').css('background', 'gray');
                        _return = false;
                        return;
                    }
                }
                if(!_return) {
                    return;
                }
                $('.paysub').css('background', '#fc3a71');
            }, true);
        }
            setaddress();
        //订单
        $('.paysub').click(function() {
            if ($(this).attr('submitting') == '1') {
                return;
            }
            var pay_type = $('#pay_type').val();
            if(!pay_type) {
                core.tip.show('请选择支付方式');
                return;
            }
            var dispatchid = $("#dispatchid").val();
            var carrierid = $("#carrierid").val();
            var addressid = $("#addressid").val();
            var _can_buy = $('#can_buy').val();
            if(_can_buy == 0) {
                core.tip.show('该城市暂不支持列表中某些商品,请重新选择');
                return;
            }
            var goods = $("#goods").val();
            var give_goods = $("#give_good").val();
            var carrier_realname = $.trim( $('#carrier_input_realname').val() );
            var carrier_mobile = $.trim( $('#carrier_input_mobile').val() );
            if (goods == '') {
                core.tip.show("没有任何商品");
                return;
            }
			 <?php  if($show==1) { ?>
				if( $("#dispatchtype").val()=='0'){
					   if (addressid == '') {
							core.tip.show("请选择地址");
							return;
						}
						if (dispatchid == '') {
							core.tip.show("请选择配送方式");
							return;
						}
				}
				 if($('#isverify').val()=='true'){
					if (carrier_realname== '') {
						 core.tip.show("请填写联系人姓名");
						 return;
					 }
					  if (!$.isMobile(carrier_mobile)) {
							core.tip.show("请填写正确手机号");
							return;
					  }
				 }
				   if( $("#dispatchtype").val()=='1'){
						if (carrier_realname== '') {
							core.tip.show("请填写姓名");
							return;
						}
						if (!$.isMobile(carrier_mobile)) {
							core.tip.show("请填写正确手机号");
							return;
						}
					}
					<?php  } ?>
			 var diydata = '';
			 var gdid = <?php  echo intval($goods_data_id)?>;
   	                    <?php  if(!empty($order_formInfo)) { ?>
				 <?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('diyform/common_js_data', TEMPLATE_INCLUDEPATH)) : (include template('diyform/common_js_data', TEMPLATE_INCLUDEPATH));?>
			 <?php  } ?>
            $(this).attr('submitting', '1').html('提交中...');
            var data ={
                'op': 'create',
                'goods': goods,
                'give_goods': give_goods,
				'id':'<?php  echo $id;?>',
				gdid:gdid,
				diydata:diydata,
                'dispatchtype': $("#dispatchtype").val(),
                'fromcart':fromcart,
                'cartids':"<?php  echo $_GPC['cartids'];?>",
                'remark': $("#remark").val(),
                'deduct':0,
                'deduct2':0,
                'pay_type':pay_type
            };

             if( $("#dispatchtype").val()=='0'){

                 data.addressid = addressid;
                 data.dispatchid = dispatchid;
             }

             if( $("#dispatchtype").val()=='1' || $('#isverify').val()=='true'){
                 data.carrierid = carrierid;
                data.carrier = {
                    'carrier_realname': carrier_realname,
                    'carrier_mobile':carrier_mobile,
                    'realname': $('#carrier_realname').html(),
                    'mobile': $('#carrier_mobile').html(),
                    'address': $('#carrier_address').html()
                };
            }

            if($("#deductcredit").length>0){
                if($("#deductcredit").attr('on')=='1'){
                      data.deduct = 1;
                }
            }

            if($("#deductcredit2").length>0){
                if($("#deductcredit2").attr('on')=='1'){
                      data.deduct2 = 1;
                }
            }
	   <?php  if($hascouponplugin) { ?>
		data.couponid = $('#couponid').val();
	  <?php  } ?>
            core.json('order/confirm', data, function(create_json) {

                if (create_json.status == 1) {
                    location.href = "<?php  echo $this->createMobileUrl('order/detail')?>&id=" + create_json.result.orderid +"&openid=<?php  echo $openid;?>&pay=ok";
                }  else if (create_json.status == -1) {
                     $('.paysub').html('确认订单').removeAttr('submitting');
                     core.tip.show(create_json.result);
                }
                else {
                    $('.paysub').html('确认订单').removeAttr('submitting');
                    core.tip.show("生成订单失败!")
                }

            }, true, true);
        })

        //积分抵扣
        $('#deductcredit').click(function(){
               var on = $(this).attr('on')=='0'?'1':'0';
               $(this).attr('on',on);
               if(on=='1'){
                     $(this).addClass('on').find('nav').addClass('on');
               }
               else{
                     $(this).removeClass('on').find('nav').removeClass('on');
               }
               calctotalprice();
        });
        //余额抵扣
          $('#deductcredit2').click(function(){
               var on = $(this).attr('on')=='0'?'1':'0';
               $(this).attr('on',on);
               if(on=='1'){
                     $(this).addClass('on').find('nav').addClass('on');
               }
               else{
                     $(this).removeClass('on').find('nav').removeClass('on');
               }
               calctotalprice();
        });
       <?php  if($hascouponplugin) { ?>
            bindCouponEvents();
            function calcCouponPrice(totalprice){

	      /* $('#coupondeduct_div').hide();
	       $('#coupondeduct_text').html('');
	       $('#coupondeduct_money').html('0');*/
	      if(!$('#coupondeduct_money').html()) {
              if ($('#couponid').val() == '' || $('#couponid').val() == '0') {
                  return totalprice;
              }
          }

          var old_coupon = $('#coupondeduct_money').html();

	       var deduct   = parseFloat( $('#couponselect').data('deduct') );
                 var discount = parseFloat( $('#couponselect').data('discount') );
                 var backtype = parseFloat( $('#couponselect').data('backtype') );

	       var deductprice = 0;
	       if(deduct>0 && backtype==0){ //抵扣
		   if(deduct>totalprice){
			   deduct=totalprice;
		   }
		   if(deduct<=0){
			   deduct = 0;
		   }
		   //totalprice-=deduct;
               $('#coupondeduct_money').html(deduct.toFixed(2));
		   $('#coupondeduct_text').html('优惠券优惠');
	      }else if(discount>0 && backtype==1){//打折

		   deductprice = totalprice *  (1 - discount/10 );
		   if(deductprice>totalprice){
			   deductprice=totalprice;
		   }
		  if(deductprice<=0){
			   deductprice = 0;
		   }

    		   totalprice-=deductprice;
		   $('#coupondeduct_text').html('优惠券折扣(' + discount + '折)');
	        }

                if(!deductprice) {
                    deductprice = parseInt($('#coupondeduct_money').html());
                    if(!deductprice){deductprice=0}else {
                        //totalprice -=deductprice;
                    }
                }
	       if(deductprice>0){
		 $('#coupondeduct_div').show();
		    $('#coupondeduct_money').html(deductprice.toFixed(2));
       }
	      return totalprice;

            }
            function bindCouponEvents(){
               $('#selectcoupon').click( function(){
                    if($('#coupondeduct_div').css('display') != 'none') {
                        $('#coupon_chooser').css('margin-top', '15px');
                    }
                    var money =parseFloat( $('.totalprice').html().replace(",","") ) ;
                    var goods_id = $('#goods').val();
 				     core.pjson('coupon/util', {op: 'query', money: money, type:0,goods_id:goods_id}, function (rjson) {
							if (rjson.status != 1) {
								core.tip.show(rjson.result);
								$('#couponid').val('');
								calctotalprice();
								return;
							}
							if(rjson.result.coupons.length>0){
								CouponChooser.cancelCallback = function(){
									$('#coupondeduct_div').hide();
									$('#coupondeduct_text').html('');
									$('#coupondeduct_money').html('0');
									calctotalprice();

								}
								CouponChooser.confirmCallback = function(){
								    var coupon_price = parseFloat($('#coupondeduct_money').html());
								    var coupondeduct_money = parseFloat($('#coupondeduct_money').html());

									calctotalprice();
								}
								CouponChooser.open( rjson.result );

							}
						}, true, true);
				});
		}

       <?php  } ?>


    }, true);



    });
    function local_index() {
        location.href="<?php  echo $this->createMobileUrl('shop/index')?>";
    }
    /**
     * 验证输入地址的准确性
     * @param  {object} obj 该对象
     * @return {[type]}     返回错误正确信息
     */
    function check_address_type(obj) {
        var type = $(obj).attr('data-type');
        console.log(type);
        $('#add_type').val(type);
        $(obj).find("i").addClass("fa-check-circle").removeClass("fa-circle-o").css({"color": "#169959"});
        $(obj).siblings().find("i").addClass("fa-circle-o").removeClass("fa-check-circle").css({"color": "#939393"});
    }
var dflag = true;
function check_default(obj) {
    if (dflag) {
        $(obj).removeClass("off").addClass("on");
        dflag = false;
    } else {
        $(obj).removeClass("on").addClass("off");
        dflag = true;
    }
}

</script>