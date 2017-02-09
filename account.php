<?php

/**
 * Created by PhpStorm.
 * User: Zhang Ye
 * Date: 2016/12/13
 * Time: 10:22
 */

require './framework/bootstrap.inc.php';
error_reporting(E_ALL|E_NOTICE);
$host = $_SERVER['HTTP_HOST'];

$phone   = $_GPC['phone'];
$start   = $_GPC['start'];
$end     = $_GPC['end'];
$start   = strtotime($start);
$end     = strtotime($end);
$costs   = getCosts($end, $start);
$incomes = getIncomes($end, $start);

list($totalIncome, $income_arr) = getIncomeAccount($incomes, $phone);
list($totalCost,   $cost_arr)   = getCostAccount($costs, $phone);
$ret = array(
    "totalCost"   => $totalCost,
    "totalIncome" => $totalIncome,
    "list" => array_merge($income_arr, $cost_arr)
);
echo json_encode($ret);
exit;


/**
 * @param $costs
 * @param $totalCost
 * @param $cost_arr
 * @return array
 */
function getCostAccount($costs, $phone)
{
    $totalCost = 0;
    $cost_arr  = array();
    foreach ($costs as $cost) {
        $totalCost += intval($cost['price']);
        $address = unserialize($cost['address']);
        $mobile = $address["mobile"];
        if ($phone == $mobile) {
            $cost_arr[] = array(
                "id"    => $cost['id'],
                "type"  => 1,
                "money" => $cost['price'],
                "mobile" => $mobile,
                "title" => "唐盛庄园消费",
                "time"  => date("Y-m-d  h:i:s", $cost['time'])
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
    $totalIncome = 0;
    $income_arr  = array();
    foreach ($incomes as $income) {
        $totalIncome += intval($income['price']);
        $address = unserialize($income['address']);
        $mobile = $address['mobile'];
        if ($phone == $mobile) {
            $income_arr[] = array(
                "id"     => $income['id'],
                "type"   => 2,
                "mobile" => $mobile,
                "title"  => "唐盛庄园退款",
                "money"  => $income['price'],
                "time"   => date("Y-m-d  h:i:s", $income['time'])
            );
        }
    }
    return array($totalIncome, $income_arr);
}



/**
 * @param $next
 * @param $current
 * @return array
 */
function getCosts($next, $current)
{
    $costs = pdo_fetchall("select id, price, address, paytime as time from ims_manor_shop_order where status = :status and paytime > :current and paytime < :next",
        array(
            ":status" => 3,
            ":next" => $next,
            ":current" => $current
        ));
    return $costs;
}



/**
 * @param $next
 * @param $current
 * @return array
 */
function getIncomes($next, $current)
{
    $incomes = pdo_fetchall("select id, price, address, refundtime as time from ims_manor_shop_order where status = :status and refundtime > :current and refundtime < :next",
        array(
            ":status" => -1,
            ":next"    => $next,
            ":current" => $current
        ));
    return $incomes;
}


