#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/task.class.php';
su('admin');

zdTable('project')->config('execution')->gen(6);
zdTable('task')->config('task')->gen(6);

/**

title=taskModel->afterChangeStatus();
timeout=0
cid=1

*/

$taskIDList = range(1, 6);
$taskTester = new taskTest();

r($taskTester->afterChangeStatusTest($taskIDList[0], 'done')) && p('0:field,old,new') && e('status,wait,done');   // 测试任务状态为未开始的任务
r($taskTester->afterChangeStatusTest($taskIDList[1], 'done')) && p('0:field,old,new') && e('status,doing,done');  // 测试任务状态为进行中的任务
r($taskTester->afterChangeStatusTest($taskIDList[2], 'done')) && p()                  && e('0');                  // 测试任务状态为已完成的任务
r($taskTester->afterChangeStatusTest($taskIDList[3], 'done')) && p('0:field,old,new') && e('status,pause,done');  // 测试任务状态为已完成的任务
r($taskTester->afterChangeStatusTest($taskIDList[4], 'done')) && p('0:field,old,new') && e('status,cancel,done'); // 测试任务状态为已取消的任务
r($taskTester->afterChangeStatusTest($taskIDList[5], 'done')) && p('0:field,old,new') && e('status,closed,done'); // 测试任务状态为已关闭的任务
