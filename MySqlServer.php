<?php
require dirname(__FILE__) . '/MySQLPool.php';

use Swoole\Coroutine\Pool\MySQLPool;

$1_connect = [
    'host' => '1',
    'user' => 'root',
    'password' => '123456',
    'database' => 'db1',
    'charset' => 'utf8mb4', //指定字符集
];

$2_connect = [
    'host' => '2',
    'user' => 'root',
    'password' => '123456',
    'database' => 'db2',
    'charset' => 'utf8mb4', //指定字符集
];

$3_connect = [
    'host' => '3',
    'user' => 'root',
    'password' => '123456',
    'database' => 'db3',
    'charset' => 'utf8mb4', //指定字符集
];

$server = new \Swoole\Server("127.0.0.1", 9510, SWOOLE_BASE);
$server->set([
    'worker_num' => 8,
    'daemonize' => 1,
    'max_coro_num' => 16000,
    'log_file' => '/var/www/mysql.log',
]);
$server->on('connect', function ($server, $fd){});
$server->on('receive', function ($server, $fd, $from_id,$data) use($1_connect,$2_connect,$3_connect){
    $data = json_decode($data,true);
    if (isset($data['database']) && $data['query']){
        MySQLPool::init([
            '1_connect' => [
                'serverInfo' => $1_connect,
                'maxSpareConns' => 10,
                'maxConns' => 20
            ],
            '2_connect' => [
                'serverInfo' => $2_connect,
                'maxSpareConns' => 10,
                'maxConns' => 20
            ],
            '3_connect' => [
                'serverInfo' => $3_connect,
                'maxSpareConns' => 10,
                'maxConns' => 20
            ],
        ]);
        $swoole_mysql = MySQLPool::fetch($data['database']);
        $swoole_mysql = MySQLPool::reconnect($swoole_mysql,$data['database']);
        $ret = $swoole_mysql->query($data['query'],60);
        MySQLPool::recycle($swoole_mysql);
        if ($server->exist($fd)){
            $server->send($fd, json_encode($ret));
        }
    }
});

$server->on('close', function ($server, $fd) {});
$server->start();
