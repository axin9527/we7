<?php
require 'vendor/autoload.php';
$db = db();

$t   = $_REQUEST['t'];
$result = $db->update('ims_ticket_user',
    ['nickname' => $t],
    ["id" => 1]);

$ret = $db->get('ims_ticket_user',
    ['nickname'],
    ['id' => 1]);
var_dump($ret["nickname"]);
var_dump($db->log());
var_dump($ret);
