<?php defined('IN_IA') or exit('Access Denied');?><?php  if($show_footer) { ?>

<?php  if($this->footer['diymenu']) { ?>
<div id="designer-nav" style="display: none">
    <?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('designer/menu', TEMPLATE_INCLUDEPATH)) : (include template('designer/menu', TEMPLATE_INCLUDEPATH));?>
</div>

<?php  } else { ?>
<style type="text/css">
    <?php  if($this->footer['commission']) { ?>
    footer#footer-nav .menu-list li { width:20%}
    <?php  } ?>
</style>
<div class="define_bar">
<div class="tab-bar">
    <a  href="<?php  echo $this->footer['first']['url']?>" >
        <img src="../addons/manor_shop/template/mobile/tangsheng/static/images/home/sy.png" class='tab-bar-icon '>
        <p class="<?php  if($footer_current=='first') { ?>active<?php  } ?>">首页</p>
    </a>
    <a href="<?php  echo $this->footer['second']['url']?>">
        <img src="../addons/manor_shop/template/mobile/tangsheng/static/images/home/fl.png" class='tab-bar-icon'>
        <p  class="<?php  if($footer_current=='second') { ?>active<?php  } ?>">分类</p>
    </a>
    <?php  if($this->footer['commission']) { ?>
    <a href="#" class="gift">
        <img src="../addons/manor_shop/template/mobile/tangsheng/static/images/home/gift.png">
        <p class="<?php  if($footer_current=='commission') { ?>active<?php  } ?>">0元换柚子</p>
    </a>
    <?php  } ?>
    <a href="<?php  echo $this->createMobileUrl('shop/cart')?>" >
        <span class="cart_number" <?php  if($this->footer['cart_num']<=0) { ?> style="display:none;"<?php  } ?>><?php  if($this->footer['cart_num']) { ?><?php  echo $this->footer['cart_num']?><?php  } ?></span>
        <img style="position: relative;left: -11px;<?php  if($this->footer['cart_num']<=0) { ?>left: -2px;<?php  } ?>" src="../addons/manor_shop/template/mobile/tangsheng/static/images/home/gwc.png" class='tab-bar-icon '>
        <p class="<?php  if($footer_current=='cart') { ?>active<?php  } ?>">购物车</p>
    </a>
    <a href="<?php  echo $this->createMobileUrl('member')?>">
        <img src="../addons/manor_shop/template/mobile/tangsheng/static/images/home/wd.png" class='tab-bar-icon'>
        <p   class="<?php  if($footer_current=='member') { ?>active<?php  } ?>">我的</p>
    </a>
</div>
</div>
<!--  <footer id="footer-nav" style="display: none">
    <ul class="menu-list" style="margin:0;">
        <li <?php  if($footer_current=='first') { ?>class='active'<?php  } ?>><a href="<?php  echo $this->footer['first']['url']?>"><i class="fa fa-<?php  echo $this->footer['first']['ico']?>"></i><span><?php  echo $this->footer['first']['text']?></span></a></li>
        <li <?php  if($footer_current=='second') { ?>class='active'<?php  } ?>><a href="<?php  echo $this->footer['second']['url']?>"><i class="fa fa-<?php  echo $this->footer['second']['ico']?>"></i><span><?php  echo $this->footer['second']['text']?></span></a></li>
        <?php  if($this->footer['commission']) { ?>
        <li <?php  if($footer_current=='commission') { ?>class='active'<?php  } ?>><a href="<?php  echo $this->footer['commission']['url']?>"><i class="fa fa-<?php  echo $this->footer['commission']['ico']?>"></i><span><?php  echo $this->footer['commission']['text']?></span></a></li>
        <?php  } ?>
        <li <?php  if($footer_current=='cart') { ?>class='active'<?php  } ?>><a href="<?php  echo $this->createMobileUrl('shop/cart')?>"><span class="cart_number" <?php  if($this->footer['cart_num']<=0) { ?> style="display:none;"<?php  } ?>><?php  if($this->footer['cart_num']) { ?><?php  echo $this->footer['cart_num']?><?php  } ?></span><i class="fa fa-shopping-cart"></i><span>购物车</span></a></li>
        <li <?php  if($footer_current=='member') { ?>class='active'<?php  } ?>><a href="<?php  echo $this->createMobileUrl('member')?>"><i class="fa fa-user"></i><span>个人中心</span></a></li>
    </ul>
</footer>-->
<?php  } ?>
<?php  } ?>

<?php  $systemcopyright = false?>
<?php  $psystem = p('system')?>
<?php  if($psystem) { ?>
<?php  $systemcopyright = $psystem->getCopyright()?>
<?php  } ?>
<?php  if($show_footer && $show_copyright) { ?>
<div style='display:none;margin:0;padding:0;padding-bottom:0px;float:left;width:100%;background-color:<?php  echo $systemcopyright['bgcolor'];?>' id="systemcopyright">
<?php  echo $systemcopyright['copyright']?>
</div>
<div style='height:50px; width:100%;margin:0;padding:0;float:left;display:block;'></div>
<?php  } else if($show_copyright) { ?>
<div style='display:none;margin:0;padding:0;padding-bottom:0px;float:left;width:100%;background-color:<?php  echo $systemcopyright['bgcolor'];?>' id="systemcopyright">
<?php  echo $systemcopyright['copyright']?>
</div>
<?php  if($footertype==2 || $hide_footer=1) { ?>
<div style='height:50px; width:100%;margin:0;padding:0;float:left;display:block;'></div>
<?php  } ?>
<?php  } else if($show_footer) { ?>
<div style='height:50px; width:100%;margin:0;padding:0;float:left;display:block;'></div>
<?php  } ?>
<div class="go-top"><img style="width: 55px !important;" src="../addons/manor_shop/template/mobile/tangsheng/static/images/return_top.png" alt=""></div>
<script>
    window.onscroll=function(){
        if($(window).scrollTop()>100){
            $('.go-top').show();
        }else{
            $('.go-top').hide();
        }
    }
    $('.go-top').click(function(){
        scroll('0px', 300);

    });
    function scroll(scrollTo, time) {
        var scrollFrom = parseInt(document.body.scrollTop),
                i = 0,
                runEvery = 5; // run every 5ms

        scrollTo = parseInt(scrollTo);
        time /= runEvery;

        var interval = setInterval(function () {
            i++;

            document.body.scrollTop = (scrollTo - scrollFrom) / time * i + scrollFrom;

            if (i >= time) {
                clearInterval(interval);
            }
        }, runEvery);
    }
    function add_cart(obj,src,start,end) {
        var object = $(obj);
        if(src){
            var img = src;
        } else {
            var img = object.parent().parent().find('.good_right > .img > img').attr('src');
        }
        if(!start) {
            start = {
                left: event.pageX-40,  //开始位置（必填）#fly元素会被设置成position: fixed
                top: event.pageY-40,  //开始位置（必填）
            };
        }
        if(!end) {
            end = {
                left: 200, //结束位置（必填）
                top: 700,  //结束位置（必填）
                width: 30, //结束时高度
                height: 30, //结束时高度
            }
        }
        var flyer = $('<img style="width: 40px;border-radius: 18px" class="u-flyer" src="'+img+'">');
        flyer.fly({
            start:start,
            end:end,
            onEnd: function(){ //结束回调
                //this.destory(); //移除dom
            }
        });
        require(['core'],function(core){
            var goods_id = object.attr('data-id');
            var data = {};
            core.json('shop/cart',{op:'add', id:goods_id,optionid:$('#optionid').val(),total:1},function(ret){
                if(ret.status==1){
                    $('.cart_number').show();
                    var _html = $('.cart_number').html() ? $('.cart_number').html() : 0;
                    var num = parseInt(_html);
                    $('.cart_number').html(num + 1);
                } else{
                    core.message(ret.result,'','error');
                }
            },true,true);
        });

    }
</script>
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer_base', TEMPLATE_INCLUDEPATH)) : (include template('common/footer_base', TEMPLATE_INCLUDEPATH));?>