#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/task.class.php';
su('admin');

zdTable('project')->config('project', true)->gen(5);
zdTable('task')->config('task')->gen(11);
zdTable('taskteam')->config('taskteam')->gen(6);

/**

title=taskModel->getParentTaskPairs();
timeout=0
cid=1

*/

$executionIDList = array(3, 2, 1, 10);
$append = '1,2';

$taskModel = $tester->loadModel('task');

r($taskModel->getParentTaskPairs($executionIDList[0], $append)) && p('1')  && e('开发任务11'); // 查找有父任务的执行下的开发任务11
r($taskModel->getParentTaskPairs($executionIDList[0], $append)) && p('2')  && e('开发任务12'); // 查找有父任务的执行下的开发任务12
r($taskModel->getParentTaskPairs($executionIDList[0], $append)) && p('6')  && e('开发任务16'); // 查找有父任务的执行下的父任务开发任务16
r($taskModel->getParentTaskPairs($executionIDList[0]))          && p('6')  && e('开发任务16'); // 查找有父任务的执行下的父任务开发任务16
r($taskModel->getParentTaskPairs($executionIDList[0]))          && p('10') && e('`^$`');       // 查找有父任务的执行下的父任务开发任务20
r($taskModel->getParentTaskPairs($executionIDList[1]))          && p()     && e('0');          // 查找没有父任务的执行
r($taskModel->getParentTaskPairs($executionIDList[1], $append)) && p('1')  && e('开发任务11'); // 查找没有父任务的执行下的开发任务11
r($taskModel->getParentTaskPairs($executionIDList[1], $append)) && p('2')  && e('开发任务12'); // 查找没有父任务的执行下的开发任务12
r($taskModel->getParentTaskPairs($executionIDList[2]))          && p()     && e('0');          // 查找不是执行的父任务
r($taskModel->getParentTaskPairs($executionIDList[3]))          && p()     && e('0');          // 查找不存在的执行下的父任务
