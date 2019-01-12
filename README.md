# SWOOLE协程 MYSQL数据库连接池

### 使用Swoole的\Swoole\Coroutine\MySQL创建连接，通过静态类和静态成员属性维护连接池，不同协程可以共享该连接池。

排队机制（先进先出）使用协程的特殊功能实现：

\Swoole\Coroutine::resume($cid)：从$name队列中恢复一个挂起的协程执行；
\Swoole\Coroutine::suspend($cid)：将当前协程挂起到$name队列上。
限制
每个worker都有各自的MySQL连接池，且不同worker之间无法共享彼此的MySQL连接池；
可能存在各个worker进程连接池利用率不同（依赖业务实现）。
优点
与独立的连接池（worker进程间可共享的连接池实现）对比，无进程间通信开销；
独立的连接池需要增加运维成本。
# 使用方法
- 环境要求swoole4.0 + 开启携程
- 运行MysqlServer.php
- 更改测试代码数据库连接信息
- 运行test.php测试
