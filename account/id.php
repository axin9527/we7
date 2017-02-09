<?php
require 'vendor/autoload.php';
date_default_timezone_set('Asia/Shanghai');
$db = db();

$the_id   = $_REQUEST['id'];
list($id, $type) = explode("-",$the_id);
$query = $db->query("select status, refundtime, paytime, id, (price + deductcredit2) as price, address from 
        ims_manor_shop_order where id = $id");
if ($query) {
    $rowset = $query->fetchAll();
    $result = $rowset[0];
} else {
    die('404');
}

if ($result['status'] == -1) {
    $ret = [
        "id"     => $result['id'] . '-' . $type,
        "type"   => $type,
        "title"  => "唐盛庄园退款",
        "money"  => $result['price'],
        "time"   => date("Y-m-d  H:i:s", $result['refundtime'])
    ];
} else if ($result['status'] == 3 ||$result['status'] == 2 || $result['status'] == 1) {
    $ret = [
        "id"     => $result['id'] . '-' . 1,
        "type"   => 1,
        "title"  => "唐盛庄园消费",
        "money"  => $result['price'],
        "time"   => date("Y-m-d  H:i:s", $result['paytime'])
    ];
};
echo json_encode($ret);