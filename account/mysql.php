<?php
function db()
{
    $database = new medoo([
        'database_type' => 'mysql',
        'database_name' => 'we7',
        'server' => '123.56.143.169',
        'username' => 'readts',
        'password' => 'zL9nR0qdbuiwcsg',
        'port' => 4406,
//        'server' => 'test.tangshengmanor.com',
//        'username' => 'root',
//        'password' => 'TszYp@PwL23O5P0w',
//        'port' => 3306,
        'charset' => 'utf8',
        'prefix' => '',
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ]);
    return $database;
}
