<?php defined('IN_IA') or exit('Access Denied');?><script id='tpl_show_message' type='text/html'><div class="sweet-alert" style="display:block;">
        <%if type=='error'%><div class="icon error animateErrorIcon" style="display: block;"><span class="x-mark animateXMark"><span class="line left"></span><span class="line right"></span></span></div><%/if%>
        <%if type=='warning'%><div class="icon warning pulseWarning" style="display: block;"><span class="body pulseWarningIns"></span><span class="dot pulseWarningIns"></span></div><%/if%>
        <%if type=='success'%><div class="icon success animate" style="display: block;"><span class="line tip animateSuccessTip"></span><span class="line long animateSuccessLong"></span><div class="placeholder"></div><div class="fix"></div></div><%/if%>
        <div class="info"><%message%><%if url%><br><span>如果您的浏览器没有自动跳转请点击此处</span><%/if%></div>
        
        <div class="sub" 
             <%if url%>
                    onclick="location.href='<%url%>'"
             <%else%>
                    <%if js%>
                        onclick="<%js%>"
                    <%else%>
                        onclick="history.back()"
                    <%/if%>
             <%/if%>
             >
        <%if type=='success'%><div class="green">确认</div><%/if%>
        <%if type=='warning'%><div class="grey">确认</div><%/if%>
        <%if type=='error'%><div class="red">确认</div><%/if%>
        </div></script>
        <?php 
        
        if (empty($_W['shopshare'])) {
        
            $set = m('common')->getSysset();
  
            $_W['shopshare'] = array(
               'title'=>empty($set['share']['title'])?$set['shop']['name']:$set['share']['title'],
               'imgUrl'=>empty($set['share']['icon'])?tomedia($set['shop']['logo']):tomedia($set['share']['icon']),
               'desc'=>empty($set['share']['desc'])?$set['shop']['description']:$set['share']['desc'] ,
               'link'=>$this->createMobileUrl('shop')
            );
           
            
            $plugin_commission = p('commission');
             if($plugin_commission){
                  $set = $plugin_commission->getSet();
                   if(!empty($set['level'])){
                  
                        $openid = m('user')->getOpenid();
                        $member = m('member')->getMember($openid);
                        
                        if(!empty($member) && $member['status']==1 && $member['isagent']==1){
                        	
                        	if (empty($set['closemyshop'])){
                        	    $myshop = $plugin_commission->getShop($member['id']);
                        	    $_W['shopshare'] = array(
					               'title'=> $myshop['name'],
					               'imgUrl'=>tomedia($myshop['logo']),
					               'desc'=>$myshop['desc'],
					               'link'=>$this->createPluginMobileUrl('commission/myshop',array('mid'=>$member['id']))
					            );
                        	}
                        	else{
                        	    
                        	   $_W['shopshare']['link'] = $this->createMobileUrl('shop',array('mid'=>$member['id']));
                        	}
                        	
                            if(empty($set['become_reg']) && ( empty($member['realname']) || empty($member['mobile']))){
                                $trigger = true;
                            }
                         } 
                         else if(!empty($_GPC['mid'])){
                         	$m = m('member')->getMember($_GPC['mid']);
                         	if(!empty($m) && $m['status']==1 && $m['isagent']==1){
                              if (empty($set['closemyshop'])){
	                        	    $myshop = $plugin_commission->getShop($_GPC['mid']);
	                        	    $_W['shopshare'] = array(
						               'title'=> $myshop['name'],
						               'imgUrl'=>tomedia($myshop['logo']),
						               'desc'=>$myshop['desc'],
						               'link'=>$this->createPluginMobileUrl('commission/myshop',array('mid'=>$member['id']))
						            );
	                        	}
	                        	else{
	                        	   $_W['shopshare']['link'] = $this->createPluginMobileUrl('commission/myshop',array('mid'=>$_GPC['mid']));
	                        	}
                            } else{
                           	   $_W['shopshare']['link'] = $this->createMobileUrl('shop',array('mid'=>$_GPC['mid']));
                            }
                         }
                   }
             }
        } ?>
		<span style='display:none'>
 <?php  $shopset = m('common')->getSysset('shop')?><?php  echo $shopset['diycode']?> 
 </span>
		<script language='javascript'>
	$(function(){
		$.ajax({
			url: "<?php  echo $this->createMobileUrl('runtasks')?>",
			cache:false 
		});
	})
</script>
 <script>
     var city = '北京';
     require(['http://res.wx.qq.com/open/js/jweixin-1.0.0.js'],function(wx){
                 window.shareData = <?php  echo json_encode($_W['shopshare'])?>;
               
	jssdkconfig = <?php  echo json_encode($_W['account']['jssdkconfig']);?> || { jsApiList:[] };
        <?php  if($trigger) { ?>
                window.shareData.trigger =function(e){
                    
                    require(['core'],function(core){
                        core.message('需要完善您的资料才能继续操作!',"<?php  echo $this->createMobileUrl('member/info',array('returnurl'=>urlencode($_W['siteurl'].$_SERVER['QUERY_STRING'])))?>",'warning');
                        return;
                    })   
                    wx.cancel();
                }
        <?php  } ?>
                  jssdkconfig.debug = false;
	jssdkconfig.jsApiList = ['checkJsApi','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','showOptionMenu','getLocation'];
	wx.config(jssdkconfig);
	wx.ready(function () {
		wx.showOptionMenu();
		wx.onMenuShareAppMessage(window.shareData);
		wx.onMenuShareTimeline(window.shareData);
		wx.onMenuShareQQ(window.shareData);
		wx.onMenuShareWeibo(window.shareData);
        wx.getLocation({
            type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
            success: function (res) {
                var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                var speed = res.speed; // 速度，以米/每秒计
                var accuracy = res.accuracy; // 位置精度
                var bmap_url = "<?php  echo $this->createMobileUrl('shop/util')?>&op=getcity&latitude="+latitude+"&longitude="+longitude+"";
                $.ajax({
                    url: bmap_url,
                    dataType: 'json',
                    success:function (res) {
                        if(res.result.status == 'OK') {
                            localStorage.setItem('location_gps', JSON.stringify(res.result.result));
                            city = res.result.result.addressComponent.city;
                            //微信地理位置定位
                            if(!localStorage.getItem('current_city')) {
                                localStorage.setItem('current_city', city);
                                location.reload();
                            } else {
                                city = localStorage.getItem('current_city');
                            }
                            $('#gps_city').html(city);
                        }

                    },
                });
            }
        });
	});
      });
     //微信地理位置定位
     var local_city = localStorage.getItem('current_city');
     if(!local_city) {
         localStorage.setItem('current_city', city);
         location.reload();
     } else {

     }
</script>

</body>
</html>
