#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/my.class.php';
su('admin');

/**

title=测试 myModel->getTasksBySearch();
cid=1
pid=1

获取task状态的项目 >> 批量任务三,wait

*/

$my    = new myTest();
$account  = 'admin';
$limit    = 1;

$task = $my->getTasksBySearchTest($account, $limit);

r($task) && p('913:name,status') && e('批量任务三,wait');//获取task状态的项目