<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/_header', TEMPLATE_INCLUDEPATH)) : (include template('web/_header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/shop/tabs', TEMPLATE_INCLUDEPATH)) : (include template('web/shop/tabs', TEMPLATE_INCLUDEPATH));?>
<link href="../addons/manor_shop/plugin/designer/template/imgsrc/designer.css" rel="stylesheet">
<script type="text/javascript" src="../addons/manor_shop/plugin/designer/template/imgsrc/angular.min.js"></script>
<?php  if($operation == 'post') { ?>
<div class="main">

    <form  <?php if( ce('shop.live_video' ,$item) ) { ?>action="" method="post"<?php  } ?> class="form-horizontal form" enctype="multipart/form-data" >

    <div class="panel panel-default">
        <div class="panel-heading">
            商品直播视频
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if( ce('shop.live_video' ,$item) ) { ?>
                    <input type="text" name="displayorder" class="form-control" value="<?php  echo $item['displayorder'];?>" />
                    <?php  } else { ?>
                    <div class='form-control-static'><?php  echo $item['displayorder'];?></div>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>直播视频名称</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if( ce('shop.live_video' ,$item) ) { ?>
                    <input type="text" name="name" class="form-control" value="<?php  echo $item['name'];?>" />
                    <?php  } else { ?>
                    <div class='form-control-static'><?php  echo $item['name'];?></div>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">直播视频封面</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if( ce('shop.live_video' ,$item) ) { ?>
                    <?php  echo tpl_form_field_image('thumb', $item['thumb'])?>
                    <span class="help-block">前台视频列表显示的视频列表封面图,建议尺寸: 640*480，或参考微信图文推送的大小 </span>
                    <?php  } else { ?>
                    <?php  if(!empty($item['thumb'])) { ?>
                    <a href='<?php  echo tomedia($item['thumb'])?>' target='_blank'>
                    <img src="<?php  echo tomedia($item['thumb'])?>" style='width:100px;border:1px solid #ccc;padding:1px' />
                    </a>
                    <?php  } ?>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">直播视频链接</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if( ce('shop.live_video' ,$item) ) { ?>
                    <input type="text" name="advurl" class="form-control" value="<?php  echo $item['url'];?>"/>
                    <?php  } else { ?>
                    <div class='form-control-static'><?php  echo $item['url'];?></div>
                    <?php  } ?>
                    <div id="youkuview"></div>
                    <span class='help-block show_h'>这是优酷视频中分享的视频链接,不是url链接,是分享下面那个通用代码里面的链接,如:< iframe height=498 width=510 src='http://player.youku.com/embed/XMTY5ODMxODAyNA==' frameborder=0 'allowfullscreen'>< /iframe>,则在上面表单填写http://player.youku.com/embed/XMTY5ODMxODAyNA==即可,<b style="color: red;">如果链接正确,在失去鼠标焦点的时候可以预览到视频效果</b></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if( ce('shop.live_video' ,$item) ) { ?>
                    <label class='radio-inline'>
                        <input type='radio' name='enabled' value=1' <?php  if($item['enabled']==1) { ?>checked<?php  } ?> /> 是
                    </label>
                    <label class='radio-inline'>
                        <input type='radio' name='enabled' value=0' <?php  if($item['enabled']==0) { ?>checked<?php  } ?> /> 否
                    </label>
                    <?php  } else { ?>
                    <div class='form-control-static'><?php  if(empty($item['enabled'])) { ?>否<?php  } else { ?>是<?php  } ?></div>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">描述</label>
                <div class="col-sm-9 col-xs-12">
                    <?php if( ce('shop.live_video' ,$item) ) { ?>
                    <textarea class="form-control" name="description" id="description" cols="30" rows="10"><?php  echo $item['description'];?></textarea>
                    <?php  } else { ?>
                    <div class='form-control-static'><?php  echo $item['description'];?></div>
                    <?php  } ?>
                </div>
            </div>
            <div class="form-group"></div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <?php if( ce('shop.live_video' ,$item) ) { ?>
                    <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" onclick="return formcheck()" />
                    <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                    <?php  } ?>
                    <input type="button" name="back" onclick='history.back()' <?php if(cv('shop.live_video.add|shop.live_video.edit')) { ?>style='margin-left:10px;'<?php  } ?> value="返回列表" class="btn btn-default col-lg-1" />
                </div>
            </div>

        </div>
    </div>

    </form>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/shop/modal', TEMPLATE_INCLUDEPATH)) : (include template('web/shop/modal', TEMPLATE_INCLUDEPATH));?>
<script language='javascript'>
    require(['util'],function(u){
        $('#cp').each(function(){
            u.clip(this, $(this).text());
        });
    })
    $('form').submit(function(){
        if($(':input[name=name]').isEmpty()){
            Tip.focus(':input[name=name]','请输入直播视频名称!');
            return false;
        }
        return true;
    });
    $(function () {
        $('#chooseUrl').on('click', function () {
            $('#floating-link').modal();
        });
        $('input[name=advurl]').on('blur', function () {
            var url  = $(this).val();
            $('#youkuview').html("<iframe height=600 width=100% src='"+url+"' frameborder=0 'allowfullscreen'></iframe>");
        });
    })
</script>
<?php  } else if($operation == 'display') { ?>
<script language="javascript" src="../addons/manor_shop/static/js/dist/nestable/jquery.nestable.js"></script>
<link rel="stylesheet" type="text/css" href="../addons/manor_shop/static/js/dist/nestable/nestable.css" />
<style type='text/css'>
    .dd-handle { height: 40px; line-height: 30px}
</style>
<div class="main">
    <div class="live_video">
        <form action="" method="post">
            <div class="panel panel-default">
                <div class="panel panel-title" style="padding: 10px;">
                    直播视频列表
                </div>
                <div class="panel-body table-responsive">

                    <div class="dd" id="div_nestable" style="max-width: 100%">
                        <ol class="dd-list">

                            <?php  if(is_array($live_video)) { foreach($live_video as $row) { ?>
                            <?php  if(empty($row['parentid'])) { ?>
                            <li class="dd-item" data-id="<?php  echo $row['id'];?>">

                                <div class="dd-handle"  style='width:100%;'>
                                    <img src="<?php  echo tomedia($row['thumb']);?>" width='30' height="30" onerror="$(this).remove()" style='padding:1px;border: 1px solid #ccc;float:left;' /> &nbsp; <!--[ID: <?php  echo $row['id'];?>]--> <?php  echo $row['name'];?>
                                    <span class="pull-right">
                                           <!-- <?php if(cv('shop.live_video.add')) { ?><a class='btn btn-default btn-sm' href="<?php  echo $this->createWebUrl('shop/live_video', array('parentid' => $row['id'], 'op' => 'post'))?>" title='添加子直播视频' ><i class="fa fa-plus"></i></a><?php  } ?>-->
                                            <?php if(cv('shop.live_video.edit|shop.live_video.view')) { ?>
                                             <a class='btn btn-default btn-sm' href="<?php  echo $this->createWebUrl('shop/live_video', array('id' => $row['id'], 'op' => 'post'))?>" title="<?php if(cv('shop.live_video.edit')) { ?>修改<?php  } else { ?>查看<?php  } ?>" ><i class="fa fa-edit"></i></a>
                                            <?php  } ?>
                                            <?php if(cv('shop.live_video.delete')) { ?><a class='btn btn-default btn-sm' href="<?php  echo $this->createWebUrl('shop/live_video', array('id' => $row['id'], 'op' => 'delete'))?>" title='删除' onclick="return confirm('确认删除此直播视频吗？');return false;"><i class="fa fa-remove"></i></a><?php  } ?>
                                        </span>
                                    <?php  if($row['enabled'] == 1) { ?>
                                    <span style="margin-left:60px;padding: 3px;background: green;border-radius: 4px;color: #fff;">显示</span>
                                    <?php  } else { ?>
                                    <span style="margin-left:60px;padding: 3px;background: red;border-radius: 4px;color: #fff;">隐藏</span>
                                    <?php  } ?>

                                    <!--<span style="float:right;margin-right:60px;padding: 3px;">评论数: <?php  echo $row['comment'];?></span>-->
                                    <span style="float:right;margin-right:10px;padding: 3px;">查看数: <?php  echo $row['view'];?></span>
                                </div>
                            </li>
                            <?php  } ?>
                            <?php  } } ?>

                        </ol>
                        <table class='table' style="margin-top: 15px">
                            <tr>
                                <td>
                                    <?php if(cv('shop.live_video.add')) { ?>
                                    <a href="<?php  echo $this->createWebUrl('shop/live_video',array('op' => 'post'))?>" class="btn btn-default"><i class="fa fa-plus"></i> 添加新直播视频</a>
                                    <?php  } ?>
                                    <?php if(cv('shop.live_video.edit')) { ?>
                                    <input id="save_live_video" type="button" class="btn btn-primary" value="保存直播视频修改">
                                    <?php  } ?>
                                    <input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
                                    <input type="hidden" name="datas" value="" />
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
        </form>
    </div>
</div>
<script language='javascript'>



    $(function(){
        var depth = <?php  echo intval($shopset['catlevel'])?>;
        if(depth<=0) {
            depth =2;
        }
        $('#div_nestable').nestable({maxDepth: depth });

        $(".dd-handle a,dd-handle embed,dd-handle div").mousedown(function (e) {
            e.stopPropagation();
        });
        var $expand = false;
        $('#nestableMenu').on('click', function(e)
        {
            if ($expand) {
                $expand = false;
                $('.dd').nestable('expandAll');
            }else {
                $expand = true;
                $('.dd').nestable('collapseAll');
            }
        });

        $("#save_live_video").click(function(){
            var json = window.JSON.stringify($('#div_nestable').nestable("serialize"));
            $(':input[name=datas]').val(json);
            $('form').submit();
        })

    })
</script>

<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('web/_footer', TEMPLATE_INCLUDEPATH)) : (include template('web/_footer', TEMPLATE_INCLUDEPATH));?>

