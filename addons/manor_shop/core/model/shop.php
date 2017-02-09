<?php
 if (!defined('IN_IA')){
    exit('Access Denied');
}
class Ewei_DShop_Shop{
    public function getCategory(){
        global $_W;
        $shopset = m('common') -> getSysset('shop');
        if($shopset['catlevel'] == 1) {
            $category = pdo_fetchall("SELECT * FROM " . tablename('manor_shop_category') . " WHERE uniacid=:uniacid and enabled=1 and parentid >0  ORDER BY parentid ASC, displayorder DESC", array(':uniacid' => $_W['uniacid']));
            $category = set_medias($category, array('thumb', 'advimg'));
            foreach($category as $k=>$item) {
                //获取该分类下的所有物品， 包括物品的其他分类
                $list = pdo_fetchall('select * from '.tablename('manor_shop_goods'). "where uniacid=:uniacid  and status=1 and deleted=0 and total>=0 order by displayorder desc",array(':uniacid' => $_W['uniacid']));
                $in_other_category = array();
                foreach($list as $goods) {
                    $ccats = $goods['ccates'];
                    $cat_arr = explode(',', $ccats);
                    $cat_arr[] = $goods['ccate'];
                    if (in_array($item['id'], $cat_arr)) {
                        $in_other_category[] = $goods;
                    }
                }
                $category[$k]['children'] = set_medias($in_other_category, 'thumb');
            }
            return $category;
        }
        $allcategory = array();
        $category = pdo_fetchall("SELECT * FROM " . tablename('manor_shop_category') . " WHERE uniacid=:uniacid and enabled=1 ORDER BY parentid ASC, displayorder DESC", array(':uniacid' => $_W['uniacid']));
        $category = set_medias($category, array('thumb', 'advimg'));
        foreach($category as $c){
            if(empty($c['parentid'])){
                $children = array();
                foreach($category as $c1){
                    if($c1['parentid'] == $c['id']){
                        if(intval($shopset['catlevel']) == 3){
                            $children2 = array();
                            foreach($category as $c2){
                                if($c2['parentid'] == $c1['id']){
                                    $children2[] = $c2;
                                }
                            }
                            $c1['children'] = $children2;
                        }
                        $children[] = $c1;
                    }
                }
                $c['children'] = $children;
                $allcategory[] = $c;
            }
        }
        return $allcategory;
    }
}
