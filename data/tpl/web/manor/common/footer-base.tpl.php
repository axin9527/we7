<?php defined('IN_IA') or exit('Access Denied');?>	<script type="text/javascript">
		require(['bootstrap']);
		$('.js-clip').each(function(){
			util.clip(this, $(this).attr('data-url'));
		})
	</script>
	<div class="container-fluid footer " role="footer">
		<div class="page-header"></div>
		<span class="pull-left">
            <p><?php  if(empty($_W['setting']['copyright']['footerleft'])) { ?>Powered by <a href="http://fresh.tangshengmanor.com"><b>唐盛庄园开发组</b></a> v<?php echo IMS_VERSION;?> &copy; 2014-2016 <a href="http://fresh.tangshengmanor.com">fresh.tangshengmanor.com</a><?php  } else { ?><?php  echo $_W['setting']['copyright']['footerleft'];?><?php  } ?></p>
		</span>
		<span class="pull-right">
            <p><?php  if(empty($_W['setting']['copyright']['footerright'])) { ?><a href="http://fresh.tangshengmanor.com">关于</a>&nbsp;&nbsp;<a href="http://fresh.tangshengmanor.com">唐盛庄园</a>&nbsp;&nbsp;<a href="#">联系客服</a><?php  } else { ?><?php  echo $_W['setting']['copyright']['footerright'];?><?php  } ?></p>
		</span>
	</div>
	<?php  if(!empty($_W['setting']['copyright']['statcode'])) { ?><?php  echo $_W['setting']['copyright']['statcode'];?><?php  } ?>
</body>
</html>
