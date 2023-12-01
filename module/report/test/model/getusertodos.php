#!/usr/bin/env php
<?php
declare(strict_types=1);
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/report.class.php';

zdTable('todo')->config('todo')->gen(50);
zdTable('bug')->config('bug')->gen(20);
zdTable('task')->config('task')->gen(20);
zdTable('user')->config('user')->gen(31);

su('admin');

/**

title=测试 reportModel->getUserTodos();
cid=1
pid=1

*/

$userType = array('admin', 'dev', 'test', 'user', 'pm', 'po');

$report = new reportTest();

r($report->getUserTodosTest($userType[0])) && p() && e('admin:1;');                 // 测试获取用户待办数
r($report->getUserTodosTest($userType[1])) && p() && e('dev2:1;dev3:2;');           // 测试获取用户待办数
r($report->getUserTodosTest($userType[2])) && p() && e('test1:1;test2:2;test3:1;'); // 测试获取用户待办数
r($report->getUserTodosTest($userType[3])) && p() && e('user1:2;user2:1;');         // 测试获取用户待办数
r($report->getUserTodosTest($userType[4])) && p() && e('pm3:1;pm1:1;');             // 测试获取用户待办数
r($report->getUserTodosTest($userType[5])) && p() && e('po1:2;po2:1;');             // 测试获取用户待办数
