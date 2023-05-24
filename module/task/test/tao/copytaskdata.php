#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/task.class.php';

zdTable('task')->config('task')->gen(9);
zdTable('effort')->config('effort')->gen(3);

/**

title=taskModel->copyTaskData();
timeout=0
cid=2

*/

$taskIdList = array(0, 1, 2, 3, 4, 5);

$task = new taskTest();

r($task->copyTaskDataTest($taskIdList[1], 'subTaskEffort')) && p('objectID')      && e('1');                   // 测试父任务日志的objectID是否更改为子任务ID
r($task->copyTaskDataTest($taskIdList[2], 'subTaskEffort')) && p('objectType')    && e('task');                // 测试更新的日志类型是否为task
r($task->copyTaskDataTest($taskIdList[3], 'subTaskEffort')) && p('consumed')      && e('1');                   // 测试父任务日志消耗是否更改为子任务工时消耗
r($task->copyTaskDataTest($taskIdList[4], 'childrenTask'))  && p('consumed')      && e('1');                   // 测试子任务的消耗是否和父任务一致
r($task->copyTaskDataTest($taskIdList[5], 'childrenTask'))  && p('name,type,pri') && e('开发任务11,design,1'); // 测试子任务信息是否和父任务一致
