#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/task.class.php';
su('admin');

zdTable('project')->config('project', true)->gen(5);
zdTable('task')->config('task', true)->gen(5);

/**

title=taskModel->addTaskEffort();
timeout=0
cid=1

*/

$record1 = new stdclass();
$record1->account  = 'po82';
$record1->task     = 1;
$record1->left     = 0;
$record1->consumed = 3;

$record2 = new stdclass();
$record2->account  = 'po82';
$record2->task     = 2;
$record2->left     = 0;
$record2->consumed = 3;

$record3 = new stdclass();
$record3->account  = 'po82';
$record3->task     = 3;
$record3->left     = 1;
$record3->consumed = 4;

$record4 = new stdclass();
$record4->account  = 'po82';
$record4->task     = 4;
$record4->left     = 3;
$record4->consumed = 6;

$record5 = new stdclass();
$record5->account  = 'po82';
$record5->task     = 5;
$record5->left     = 6;
$record5->consumed = 9;

$task = new taskTest();
r($task->addTaskEffortTest($record1)) && p('objectID,left,consumed') && e("1,0,3"); // 插入task为1 left为0 consumed为3的任务
r($task->addTaskEffortTest($record2)) && p('objectID,left,consumed') && e("2,0,3"); // 插入task为601 left为0 consumed为3的任务
r($task->addTaskEffortTest($record3)) && p('objectID,left,consumed') && e("3,1,4"); // 插入task为601 left为1 consumed为4的任务
r($task->addTaskEffortTest($record4)) && p('objectID,left,consumed') && e("4,3,6"); // 插入task为904 left为3 consumed为6的任务
r($task->addTaskEffortTest($record5)) && p('objectID,left,consumed') && e("5,6,9"); // 插入task为907 left为6 consumed为9的任务
