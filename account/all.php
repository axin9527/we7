<?php
require 'vendor/autoload.php';
date_default_timezone_set('Asia/Shanghai');

$db = db();

$phone   = $_REQUEST['phone'];
$start   = $_REQUEST['start'];
$end     = $_REQUEST['end'];

$start   = strtotime($start);
$end     = strtotime($end);

$costs   = getCosts($db, $start, $end);
$incomes = getIncomes($db, $start, $end);

list($totalIncome, $income_arr) = getIncomeAccount($incomes, $phone);
list($totalCost,   $cost_arr)   = getCostAccount($costs, $phone);
$ret = array(
    "totalCost"   => "" . $totalCost,
    "totalIncome" => "" . $totalIncome,
    "list"        => array_merge($income_arr, $cost_arr)
);
echo json_encode($ret);
if ($_GET['debug']){print_r($db->log());};
exit;


/**
 * @param $costs
 * @param $totalCost
 * @param $cost_arr
 * @return array
 */
function getCostAccount($costs, $phone)
{
    $totalCost = 0.0;
    $cost_arr  = array();
    foreach ($costs as $cost) {
        $address = unserialize($cost['address']);
        $mobile = $address["mobile"];
        if ($phone == $mobile) {
            $totalCost += floatval($cost['price']);
            $cost_arr[] = array(
                "id"     => $cost['id'] . '-' . 1,
                "type"   => 1,
                "money"  => $cost['price'],
                "mobile" => $mobile,
                "title"  => "唐盛庄园消费",
                "time"   => date("Y-m-d  H:i:s", $cost['time'])
            );
        }
    }
    return array($totalCost, $cost_arr);
}

/**
 * @param $incomes
 * @param $totalIncome
 * @param $income_arr
 * @return array
 */
function getIncomeAccount($incomes, $phone)
{
    $totalIncome = 0.0;
    $income_arr  = array();
    foreach ($incomes as $income) {
        $address = unserialize($income['address']);
        $mobile = $address['mobile'];
        if ($phone == $mobile) {
            $totalIncome += floatval($income['price']);
            $income_arr[] = array(
                "id"     => $income['id'] . '-' . 2,
                "type"   => 2,
                "mobile" => $mobile,
                "title"  => "唐盛庄园退款",
                "money"  => $income['price'],
                "time"   => date("Y-m-d  H:i:s", $income['time'])
            );
        }
    }
    return array($totalIncome, $income_arr);
}

/**
 * @param $db
 * @param $start
 * @param $end
 * @return mixed
 */
function getCosts($db, $start, $end)
{
    $query = $db->query("select paytime as time, id, (price + deductcredit2) as price, address from 
        ims_manor_shop_order
        where paytime > $start and paytime < $end and status in (1, 2, 3, -1)
        order by paytime desc");
    $costs = $query->fetchAll();
    return $costs;
}

/**
 * @param $db
 * @param $start
 * @param $end
 * @return mixed
 */
function getIncomes($db, $start, $end)
{
    $query = $db->query("select refundtime as time, id, (price + deductcredit2) as price, address from 
        ims_manor_shop_order
        where refundtime > $start and refundtime < $end and status = -1
        order by refundtime desc");
    $incomes = $query->fetchAll();
    return $incomes;
}
