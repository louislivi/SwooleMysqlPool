<?php

/**
 * @param string $query
 *
 * @return array|bool
 */
function mysqlpool_query(string $query,string $db_name)
{
    $swoole_mysql = new \Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
    if (!$swoole_mysql->connect('127.0.0.1', 9510,0.5)) {
        print_r("connect mysql pool failed. Error: {$swoole_mysql ->errCode}\n");
        \Swoole\Coroutine::sleep(0.2);
        $this ->query($query,$db_name);
    }else{
        $swoole_mysql->send(json_encode(['database' => $db_name,'query' => $query]));
        $result = $swoole_mysql->recv();
        $swoole_mysql->close();
        return json_decode($result,true);
    }
}

mysqlpool_query('select * from test','1_connect');
