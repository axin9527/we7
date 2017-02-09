<?php
global $_W;
$sql = "
drop table if exists  " . tablename('alan_qrcode') . ";
drop table if exists  " . tablename('alan_qrcode_fans') . ";
drop table if exists  " . tablename('alan_qrcode_fans_group') . ";
drop table if exists  " . tablename('alan_qrcode_stat') . ";
drop table if exists  " . tablename('alan_qrcode_record') . ";
drop table if exists  " . tablename('alan_qrcode_cash_record') . ";
";
pdo_query($sql);