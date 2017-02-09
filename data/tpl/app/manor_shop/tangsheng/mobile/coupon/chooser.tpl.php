<?php defined('IN_IA') or exit('Access Denied');?><link rel="stylesheet" type="text/css" href="../addons/manor_shop/plugin/coupon/template/mobile/default/images/style.css">
<style type="text/css">
    #coupon_chooser_layer {height: 100%; width: 100%; background: rgba(0,0,0,0.6); bottom:5px;display:none }
    #coupon_chooser {height:auto; width:100%;padding-bottom:45px; background:#eee;display:none}
    .coupon_list { width:100%;}
    .coupon_get { margin:10px 30px;border:1px solid #ff6600; color:#ff6600; background:#fff; border-radius: 3px; text-align: center;padding:5px 0;font-size:13px;}
    .coupon_no {height:100px;  margin:50px 0px 60px; color:#bbb; font-size:12px; text-align:center;}
    .coupon_no_menu {height:40px; width:100%;}
    .coupon_no_nav {height:38px; width:43%; background:#eee; margin:0px 3%; float:left; border:1px solid #d4d4d4; border-radius:5px; text-align:center; line-height:38px; color:#666;}
    .coupon_footer { position: fixed; bottom:0px; height:50px;width:100%;}
    .coupon_footer .coupon_btn { float:left;height:50px;line-height:50px; font-size:14px; text-align: center; color:#fff;}
    .coupon_footer .coupon_cancel { background:#ccc; width:40%; }
    .coupon_footer .coupon_confirm { background:#f60; width:60%; }

    .qname{ width: 20px;font-size: 20px;word-wrap: break-word;letter-spacing: 20px;color: #fff;float: right;position: absolute;right:8%;font-weight: 500;top: 17%;}
    .qcon{position: absolute;top: 8%;bottom:10%;max-width:80%;left: 7%;height: 80%;width: 68%;padding: 0px;border: 1px dashed #fff; border-radius: 5px;}
    .qgz1{text-align: center;font-size: 50px; color: #fff;font-weight: 500; line-height: 50px;margin: 5px;}
    .qgz2{line-height: 20px; font-size: 12px;margin-left: 10px;color: #fff;font-weight: 500;}
    .coupon_selected{height:auto;border: 3px solid rgba(255, 104, 1, 0.87);border-radius: 5px;pading:10px;}
    .addorder_pay{ position: fixed;bottom: 0px;}
    .addorder_price{margin-bottom:10px;}

</style>
<div id='coupon_container' style="position: relative;top: -100px;"></div>
<script id='tpl_coupons' type='text/html'>
    <div id="coupon_chooser_layer"></div>
    <div id="coupon_chooser">
        <div class="coupon_list">
            <%each coupons as coupon%>
            <div class="coupon_item"
                 data-couponname="<%coupon.couponname%>"
                 data-couponid="<%coupon.id%>"
                 data-deduct="<%coupon.deduct%>"
                 data-discount="<%coupon.discount%>"
                 data-backtype="<%coupon.backtype%>"
                 style="height: auto;">

                <!-- 老的优惠券-->
                <!-- <div class='bg cside side side-left'></div>
                <div class="cthumb" <%if coupon.thumb==''%>style="width:8px;"<%/if%>> <%if coupon.thumb!=''%><img src='<%coupon.thumb%>' /><%/if%></div>
            <div class="cinfo" >
                <div class="inner" >
                    <div class="name"><%coupon.couponname%></div>
                    <div class="time">
                        <%if coupon.timestr==''%>
                        永久有效
                        <%else%>
                        <%if coupon.past%>已过期<%else%>
                        有效期 <%coupon.timestr%>
                        <%/if%>
                        <%/if%></div>
                </div>
            </div>
            <div class="cright">
                <div class="bg png png-<%coupon.css%>"></div>
                <div class="bg sideleft side side-<%coupon.css%>"></div>
                <div class="rinfo" >
                    <div class='rinner <%coupon.css%>'>
                        <div class="price"><%if coupon.backpre%>￥<%/if%><span><%coupon.backmoney%></span></div>
                        <div class="type"><%coupon.backstr%></div>
                    </div>
                </div>
                <div class="bg sideright side side-<%coupon.css%>"></div>

            </div>-->
            <!-- 新版优惠券-->
                <%if coupon.coupon_goods_id>0%>
                     <img src="../addons/manor_shop/plugin/coupon/template/mobile/default/images/yhq3.png" style="width:100%;" />
                <%else if coupon.backtype == 0 && coupon.enough<=0%>
                    <img src="../addons/manor_shop/plugin/coupon/template/mobile/default/images/yhq2.png" style="width:100%;" />
                <%else if coupon.backtype == 0 && coupon.enough>0%>
                    <img src="../addons/manor_shop/plugin/coupon/template/mobile/default/images/yhq1.png" style="width:100%;" />
                <%/if%>
            <div class="qcon">
                <div class="qgz1"><%if coupon.backpre%>￥<%/if%><%coupon.deduct%></div>
                <div class="qgz2"><%coupon.couponname%></div>
                <div class="qgz2">
                    <%if coupon.timestr==''%>
                    永久有效
                    <%else%>
                    <%if coupon.past%>已过期<%else%>
                    有效期 <%coupon.timestr%>
                    <%/if%>
                    <%/if%>
                </div>
                <div class="qgz2">特别提醒：本优惠券不可用于支付运费</div>
            </div>
            <div class="qname">
                <%if coupon.coupon_goods_id>0%>
                单品
                <%else if coupon.enough>0%>
                购物
                <%else if coupon.enough<=0%>
                购物
                <%/if%>
                <%if coupon.backtype == 0 && coupon.enough<=0%>
                立减
                <%else if coupon.backtype == 0 && coupon.enough>0%>
                满减
                <%else if coupon.backtype == 1%>
                💉打折
                <%else if coupon.backtype == 2%>
                返利
                <%/if%>

            </div>
        </div>
        <%/each%>
    </div>

    <div class="coupon_footer">
        <div class="coupon_btn coupon_cancel">不使用优惠券</div>
        <div class="coupon_btn coupon_confirm">确定使用</div>
    </div>
    </div>
</script>
<script language="javascript">
    var CouponChooser = {};
    require(['core','tpl'],function(core,tpl){
        CouponChooser.close = function(){
            $('#coupon_chooser_layer').hide();
            $('#coupon_chooser').hide();
        }
        CouponChooser.open = function(result){
            var html = tpl('tpl_coupons',result);
            $('#coupon_container').html(html);
            var h = $(document.body).height() * 0.7;
            $('#coupon_chooser').css('max-height',h).fadeIn(300);
            $('.coupon_list').css('max-height',h);
            $('#coupon_chooser_layer').fadeIn(300).unbind('click').click(function(){
                CouponChooser.close();
            });

            var couponid = $('#couponid').val();

            if( couponid== '' ) {
                couponid='0';
            }

            $('#coupon_chooser .coupon_item').removeClass('coupon_selected');
            $('#coupon_chooser .coupon_item[data-couponid=' +couponid +']' ).addClass('coupon_selected');

            $('#coupon_chooser .coupon_item').unbind('click').click(function(){
                $('#coupon_chooser .coupon_item').removeClass('coupon_selected');
                $(this).addClass('coupon_selected');
                $('#couponid').val($(this).data('couponid'));
                $('#couponselect').data({
                    'couponname': $(this).data('couponname'),
                    'couponid':$(this).data('couponid'),
                    'deduct':$(this).data('deduct'),
                    'discount':$(this).data('discount'),
                    'backtype':$(this).data('backtype')
                }).html("我选择了 " +$(this).data('couponname'));

            });
            $('#coupon_chooser .coupon_cancel').unbind('click').click(function(){
                $('#couponid').val('');
                $('#couponselect').html('我要使用优惠券');
                $('#coupon_chooser .coupon_item').removeClass('coupon_selected');
                CouponChooser.close();
                if(CouponChooser.cancelCallback){
                    CouponChooser.cancelCallback();
                }
            });
            $('#coupon_chooser .coupon_confirm').unbind('click').click(function(){
                if($('#couponid').val()=='' || $('#couponid').val()=='0'){
                    core.tip.show('请选择优惠券!');
                    return;
                }
                $('#coupon_chooser .coupon_item').removeClass('coupon_selected');
                CouponChooser.close();
                if(CouponChooser.confirmCallback){
                    CouponChooser.confirmCallback();
                }
            });


        }
    });


</script>