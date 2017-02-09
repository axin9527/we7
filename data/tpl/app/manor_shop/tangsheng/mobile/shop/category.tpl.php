<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<title>商品分类</title>
<style type="text/css">
    body {margin:0px;padding:0px; width:100%; height:100%; background:#fff; color:#fff; }
 .topbar {position:fixed;top:0px;width:100%; height:40px;  background:#f9f9f9; border-bottom:1px solid #e8e8e8; font-size:16px; line-height:40px; text-align:center; color:#666;}
    .topbar a {height:40px; width:15px; display:block; float:left; margin-left:10px;outline:0px; color:#999; font-size:24px;}
 
    .main {position:fixed;top:41px;  height:100%;}
   .search {height:40px;margin-left:30px; color:#ccc; line-height:40px; font-size:14px; text-align:center;}
    #left_container { float:right;width:90px;height:100%;background:#efefef;overflow:auto;}
    #left_container .parent_item {width:94%; padding:0 3%; height:35px;line-height:35px;font-size:13px;float:left; text-align:center; color:#333;}
    #left_container .on {background:#fff;}
    
    #right_container { float:right;margin-right:-90px;width:100%;height:100%; z-index:1;overflow:auto;}
 
    /* #right_container .inner { margin-right: 90px; background:#fff;height:100%;padding:10px 10px  35px 10px;;}
    #right_container .inner .category_item { width:27%;float:left;padding:5px;color:#333;font-size:13px; text-align: center;}
    #right_container .inner .category_item .name {height:16px;overflow:hidden;width:100%; text-align: center;}
    #right_container .inner img{width:100%;}
    
    #right_container .inner .category_no {width:100%;height:100px;color:#333; text-align: center;}
    
    #right_container .inner .category_title { color:#999;font-size:13px;padding:10px 0 10px 0;width:100%;float:left;}
    
   #right_container .adv { color:#999;font-size:13px;margin:5px;float:left;padding:0;} 
   #right_container .adv img {width:100%;}*/
   
    
    #category_loading { width:94%;padding:10px;color:#666;text-align: center;float:right;}
    .act_group{color: green;text-align: center;font-size: 0.85rem;width: 100%;float: left;}
    .act_group .group_tiao{width: 28%;  height: 1px;margin: 3% 6%;padding: 0px;background-color: green;overflow: hidden; float: left;}
    .hot_search{height: 20px;font-size:14px; padding:10px;color: #000}
    .hot_search img{vertical-align:bottom;width: 15px}
    .search_con{background: #fff;height: 120px;padding: 16px 8px;font-size: 14px;color: #B9B9B9;overflow: scroll}
    .search_con span{border: 1px solid #ccc;padding: 7px 12px;margin: 7px;width: 27%;float: left; text-align: center;overflow: auto}
    .search1 .go_back{padding: 6px 12px;float: left;position: absolute;line-height: 40px;}
    /*商品*/
    .goods {height:100%; min-height:100px; width:98%;/* background:#f3f3f3;*/ overflow:hidden;float:left;margin: 3px}
    .goods .good {overflow:hidden;background:#fff;margin-bottom: 7px;position: relative;padding:24px 0 10px 12px;border: 1px solid #f3f3f3;}
    .goods .good>img{width: 46px;position: absolute;left: -5px;top: -5px;}
    .goods .good .good_left,.goods .good .good_right{width: 50%;float: left;}
    .goods .good .img {width:100%;overflow:hidden;height: 90px;}
    .goods .good .img img {width:100%;margin: 0 auto;display: block;}
    .goods .good .name {width:100%; font-size:0.95rem; line-height:20px; color:#000001; overflow:hidden;display: block;white-space: nowrap;  overflow: hidden; text-overflow: ellipsis;}
    .goods .good .sub_name {width:80%; font-size:0.85rem; line-height:20px; color:#ccc; overflow:hidden;display: block;white-space: nowrap;  overflow: hidden; text-overflow: ellipsis;}
    .goods .good .price {width:100%; color:#f03; font-size:14px;}
    .goods .good .original_price{text-decoration:line-through;font-size: 15px;color: #ff3366;margin:0;
        padding: 0;margin-top: 10px}
    .goods .good .magic_price{font-size: 0.8rem;color: #ff3366;font-weight: bold;margin: 0;
        padding:0;margin-top: -8px}
    .goods .good .magic_price span{font-size: 26px;}
    .tips_tuijian{position: absolute;font-size: 12px;left: 6px;top: 4px; color: #fff;}
</style>


<div id='container'></div>

<script id='tpl_main' type='text/html'>
   <div class="topbar"><a href="javascript:history.back()"><i class="fa fa-angle-left"></i></a>
    <div class='search'><i class="fa fa-search"></i> 在店铺内搜索</div>
   </div>
     <!--搜索-->
   <div class="search1">
       <div class="topbar1">
           <div class='right'>
               <button class="sub1"><i class="fa fa-times" style="color: #B9B9B9"></i></button>
               <div class="home1 to_search"><i class="fa fa-search" style="color: #B9B9B9"></i></div>
           </div>
           <span class="go_back home2"><i class="fa fa-angle-left fa-2" style="font-size: 20px;color: #797979;" aria-hidden="true"></i></span>
           <div class='left_wrap'>

               <div class='left'>
                   <input type="text" style="width: 84%;height: 30px" id='keywords' class="input1" value="<?php  echo $hot_search['0'];?>" placeholder='搜索: 输入商品关键词'/>
               </div>
           </div>
       </div>
       <div class="hot_search">
           <img src="../addons/manor_shop/template/mobile/tangsheng/static/images/hot.png" alt="">
           <span>热门搜索</span>
       </div>
       <div class="search_con">
           <?php  if($hot_search) { ?>
           <?php  if(is_array($hot_search)) { foreach($hot_search as $k => $v) { ?>
           <span class="clear_hi"><?php  echo $v;?></span>
           <?php  } } ?>
           <?php  } ?>
       </div>
       <div class="hot_search">
           <img width="15px" src="../addons/manor_shop/template/mobile/tangsheng/static/images/search_history.png" alt="">
           搜索历史
       </div>
       <div class="search_con" id="search_history">

       </div>
       <div style="text-align: center;margin-top: 32px" id="clear_history">
           <img width="135px" src="../addons/manor_shop/template/mobile/tangsheng/static/images/clear_history.png" alt="">
       </div>
       <div id='search_container' class='result1'>
       </div>
       </div>

     
     <div class="main">
       
         <div id="right_container">
             <div class='adv' style='display:none' ><img src='' /></div>
             <div class="inner"></div></div>
         <div id="left_container"></div>
         
     </div>
</script>
<script id='tpl_search_list' type='text/html'>
     <ul>
     <%each list as value%>
        <li><i class="fa fa-angle-right"></i> <a href="<?php  echo $this->createMobileUrl('shop/detail')?>&id=<%value.id%>"><%value.title%></a></li>
        <%/each%>
    </ul> 
</script>
<script id='tpl_parent_list' type='text/html'>
    <!-- <div class="parent_item on"  data-cate="rec">推荐分类</div>-->
    <%each category as value k%>
    <div class="parent_item <%if k==0%>on<%/if%>" data-cate="<%value.id%>" data-advimg='<%value.advimg%>' data-advurl='<%value.advurl%>'>
        <%value.name%>
    </div>
    <%/each%>
</script>

<script id='tpl_child_list' type='text/html'>

    <?php  if(intval($shopset['catlevel'])==3) { ?>
    <%if level==2%>
        <div class="category_item" data-cate='back' data-catid='<%catid%>'>
           <img src="../addons/manor_shop/template/mobile/default/static/images/catback.png" />
            <div class="name">返回上级</div>
    </div>
    <%/if%>
    <?php  } ?>
    <div class="goods" style="margin-bottom: 110px;position: absolute;left: 32%;bottom: 0;right: 0;top: 0;overflow-x:hidden;overflow-y:scroll;-webkit-overflow-scrolling: touch;">
        <div id="goods_container" style="overflow-y: auto; overflow-x: hidden;-webkit-overflow-scrolling: touch;margin-bottom: 110px;">
       <%each category as g%>
        <div class="good" data-goodsid='<%g.id%>' data-catid="<%g.catid%>">
            <%if g.istime==1%>
            <img src="../addons/manor_shop/template/mobile/tangsheng/static/images/good_icon2.png" />
            <span class="tips_tuijian">秒杀</span>
            <%else if g.issendfree==1%>
            <img src="../addons/manor_shop/template/mobile/tangsheng/static/images/good_icon2.png" />
            <span class="tips_tuijian">包邮</span>
            <%else if g.isrecommand==1%>
            <img src="../addons/manor_shop/template/mobile/tangsheng/static/images/good_icon2.png" />
            <span class="tips_tuijian">推荐</span>
            <%else if g.ishot==1%>
            <img src="../addons/manor_shop/template/mobile/tangsheng/static/images/good_icon2.png" />
            <span class="tips_tuijian">热销</span>
            <%else%>

            <%/if%>

            <div class="good_left" onclick="go_detail(this)">
                <div class="name"><%g.title%></div>
                <div class="sub_name"><%g.sub_title%></div>
                <div class="price">
                    <p class="original_price">
                        <%if g.productprice>0 && g.marketprice!=g.productprice%>￥<%g.productprice%><%/if%>
                    </p>
                    <p class="magic_price">惊爆价:￥<span><%g.marketprice%></span></p>
                </div>
            </div>
            <div class="good_right" onclick="go_detail(this)">
                <div class='img'><img src="<%g.thumb%>"></div>
            </div>
            <div class="go_cart">
                <%if g.total>0 && g.status ==1%>
                <a class="btn-cart" data-id="<%g.id%>" onclick="add_cart(this)" href="javascript:void (0);">加入购物车</a>
                <%else if g.total<=0 && g.status ==1%>
                <a class="btn-grey" href="">补货中</a>
                <%else g.status ==0%>
                <a class="btn-grey" href="">已下架</a>
                <%/if%>
            </div>
        </div>
        <%/each%>
        </div>
    </div>
</script>

<script id='tpl_third_page' type='text/html'>
        <%each category as value%>
             <%if value.level==2  && value.children.length>0%>
                  <div class="category_title"> 
                        <%value.name%>
                  </div>
                 <%each value.children as value1%>
                       <div class="category_item"  
                            data-ccate="<%value1.parentid%>"   
                            data-tcate="<%value1.id%>" 
                            data-level='<%value1.level%>'
                            data-advimg='<%value.advimg%>' data-advurl='<%value.advurl%>'
                            >
                           <img src="<%value1.thumb%>" />
                           <div class="name"><%value1.name%></div>
                       </div>
                 <%/each%>
              <%/if%>
           <%/each%>
    </script>

<script id='tpl_third_list' type='text/html'>
   
     
 
    <%if level==2%>
    <div class="category_item" data-cate='back' data-catid='<%catid%>'>
       <img src="../addons/manor_shop/template/mobile/default/static/images/catback.png" />
        <div class="name">返回上级</div>
    </div>
    <%/if%>
    <%each category as value%>
    
    <div class="category_item"  
         <%if value.level==2%>
         data-pcate="<%value.parentid%>"   
         data-ccate="<%value.id%>" 
         <%else%>
         data-ccate="<%value.parentid%>"   
         data-tcate="<%value.id%>" 
         <%/if%>
         data-level='<%value.level%>'>
        <img src="<%value.thumb%>" />
        <div class="name"><%value.name%></div>
    </div>
    <%/each%>
  
</script>

<script language="javascript" src="../addons/manor_shop/template/mobile/tangsheng/static/js/zquery.fly.min.js"></script>
<script language='javascript'>
    var category = [];
    var children = [];
    var recommand = [];
    var pcate = 'rec';
    require(['tpl', 'core'], function (tpl, core) {
     function bindChildEvents(){
          $('.category_item').unbind('click').click(function(){
              if($(this).data('cate')=='back'){
                 if(pcate!='rec')                  { 
                    setCategory(pcate);
                }else{
                    setCategory('rec');
                }
                  return;
             }
                        var level = $(this).data('level');
                        var show = <?php  echo intval($shopset['catshow'])?>;
                        <?php  if(intval($shopset['catlevel'])==3) { ?>
                              var ccate = $(this).data('ccate');
                              var tcate = $(this).data('tcate');
                             
                                    if(level==2){
                                         setCategory(ccate,2);
                                    }
                                    else {
                                      
                                        location.href = core.getUrl('shop/list',{ccate:ccate,tcate:tcate});     
                                   }
                             
                        <?php  } else { ?>
                                var pcate = $(this).data('pcate');
                                var ccate = $(this).data('ccate');
                                location.href = core.getUrl('shop/list',{pcate:pcate,ccate:ccate});
                        <?php  } ?>
                            
          })
     }
        function setCategory(catid,level){
           
            var ret = null;
            if(catid=='rec'){
                
                recommand = [];
                for(i in category){
              
                    if(category[i].isrecommand=='1'){
                     //   recommand.push(category[i]);
                    }
                    if(category[i].children.length>0){
                           for(j in category[i].children){
                                if(category[i].children[j].isrecommand==1){
                                         
                                            recommand.push(category[i].children[j]);
                                        
                                         
                                            for(k in category[i].children[j].children){
                                                    if(category[i].children[j].children[k].isrecommand==1){
                                                             recommand.push(category[i].children[j].children[k]);
                                                    }                
                                            }
                                  
                                 }
                           }
                    }
                }
                ret = recommand;
                 
                  setCategoryList(ret,level,catid);
            } else {
            
               if(level==2){
                   
                    for(i in category){
                         for(j in category[i].children){ //二级
                                if( category[i].children[j].id == catid){
                                            ret = category[i].children[j].children;
                                            setCategoryList(ret,level,catid);
                                            return;
                                }
                         }
                    }
               }else{
                    for(i in category){

                        if( category[i].id==catid){
                              ret =  category[i].children;
                              break;
                        }
                    } 
               }
           }
           setCategoryList(ret,level,catid);
        }
        function setCategoryList(ret,level,catid){
               showAdv(catid);
               if(catid=='rec'){ 
                     $('#right_container  .inner').html(tpl('tpl_child_list',{category:ret,level:level,catid:catid}));
                   $('.good img,.good .good_left').unbind('click').click(function(){
                       location.href = core.getUrl('shop/detail',{id:$(this).parent().data('goodsid') });
                   })
               }
               else{
                   <?php  if(intval($shopset['catlevel'])==3) { ?>

                            <?php  if(intval($shopset['catshow'])==0) { ?>
                              if(level==2){
                                     $('#right_container  .inner').html(tpl('tpl_child_list',{category:ret,level:level,catid:catid}));
                                 } else{
                                     $('#right_container  .inner').html(tpl('tpl_third_page',{category:ret,level:level,catid:catid}));
                                 }
                            <?php  } else { ?>
                              $('#right_container  .inner').html(tpl('tpl_third_list',{category:ret,level:level,catid:catid}));
                            <?php  } ?>
                     <?php  } else { ?>
                              $('#right_container  .inner').html(tpl('tpl_child_list',{category:ret,level:level,catid:catid}));
                     <?php  } ?>
                    
               }
            setTimeout(function(){

              $('#right_container  .inner img').each(function(){
                  $(this).height($(this).closest('.category_item').width());
              })
              },10);
             bindChildEvents(); ;
           
        }
        function showAdv(cate){
                        
            var adv = $('#right_container .adv');
            var img = '',url ='';
            if(cate=='rec'){
                img = "<?php  echo $shopset['catadvimg']?>",url = "<?php  echo $shopset['catadvurl']?>";
            }
            else{
        
            for(i in category){
                  if(category[i].id==cate) {
                      img = category[i].advimg,url = category[i].advurl;
                      break;
                  }
                  for(j in category[i].children){ //二级
                         if(category[i].children[j].id==cate) {
                            img = category[i].children[j].advimg,url = category[i].children[j].advurl;
                            break;
                        }
                   }
             }
         }
             if(img!=''){
                          adv.find('img').attr('src',img);
                         $('#right_container .adv').show();
                         if(url!=''){
                             adv.unbind('click').click(function(){
                                 location.href = url;
                            });
                         } 
                     } else{
                         adv.hide().unbind('click');
                     }
        }
        core.json('shop/util',{op:'category'}, function (json) {
                 result = json.result;
                 category = result.category;
                 $('#container').append(tpl('tpl_main'));
           
           /*$('.main').height($(document.body).height()-90);*/
                 $('#left_container').html(tpl('tpl_parent_list',result));

              var bw = $(document.body).width()-90;
             $('#right_container .inner').width( bw);
             $('#right_container .adv').width( bw-8);
                 setCategory(result.category[0].id);
                 
                 $('.parent_item').click(function(){
                     $('.parent_item').removeClass('on');
                     $(this).addClass('on');
                     pcate = $(this).data('cate');
                     setCategory($(this).data('cate'));
                  })
                  
                 
                 
                    $('.search').click(function(){
                        $(".search1").animate({right:"0px"},100);

                       /*$('#keywords').focus().unbind('keyup').keyup(function(){
                                var keywords = $.trim( $(this).val());
                                if(keywords==''){
                                    $('#search_container').html("");
                                    return;
                                }
                                core.json('shop/util',{op:'search',keywords:keywords }, function (json) {
                                     var result = json.result;
                                     if(result.list.length>0){
                                        $('#search_container').html(tpl('tpl_search_list',result));
                                     }
                                     else{
                                        $('#search_container').html("");
                                         //core.tip.show('暂无数据');
                                     }

                                  }, true);
                        });*/
                        $('.search1 .to_search').unbind('click').click(function(){
                            var keywords = $.trim( $('#keywords').val());
                            if(keywords){
                                write_search_history(keywords);
                            }
                            var url = core.getUrl('shop/list',{keywords:keywords});
                            location.href=  url;
                        });
                        $('.search1 .sub1').unbind('click').click(function(){
                            $('#keywords').val("");
                        });
                        $('.search1 .home2').unbind('click').click(function(){
                            $(".search1").animate({right:"-100%"},100);
                        });
                        $('#clear_history').unbind('click').click(function(){
                            if(confirm("确定清除搜索历史记录吗？"))
                            {
                                clear_search_history();
                            }
                        });
                        $('.clear_hi').unbind('click').click(function(){
                            $('#keywords').val($(this).html());
                        });
                    });
            get_search_history();
        }, true);
 
        $('.sort').click(function () {
                var display = $(".sort_list").css('display');
                if (display == 'none') {
                    $(".sort_list").fadeIn(200);
                } else {
                    $(".sort_list").fadeOut(100);
                }

        });
    });
    function write_search_history(val) {
        var old = localStorage.getItem('search_history');
        if(!old) {
            var old = [];
        } else {
            var old = JSON.parse(old);
        }
        if(old.indexOf(val) >= 0) {
            return;
        }
        old.push(val);
        localStorage.setItem('search_history', JSON.stringify(old));
    }
    function get_search_history() {
        var old = localStorage.getItem('search_history');
        var _html = "";
        if(!old){
            return;
        }
        var old = JSON.parse(old);
        for(var i=0;i<old.length;i++) {
            _html += '<span class="clear_hi">' + old[i] + '</span>';
        }
        $('#search_history').html(_html);
    }
    function clear_search_history() {
        localStorage.removeItem('search_history');
    }
    function go_detail(obj) {
        var id = $(obj).parent().data('goodsid');
        require(['core'], function (core) {
            var url =core.getUrl('shop/detail', {id:id});
            location.href = url;
        })
    }
</script>
<?php  $show_footer=true;$footer_current ='second'?> 
<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
