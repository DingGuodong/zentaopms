#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";

$taskTeam = zdTable('taskteam');
$taskTeam->id->range('1-5');
$taskTeam->task->range('1{2},2{3}');
$taskTeam->account->range('admin,dev01,admin,dev01,dev02');
$taskTeam->estimate->range('1{2},2{3}');
$taskTeam->left->range('1{2},1{3}');
$taskTeam->status->range('wait{2},doing{3}');
$taskTeam->gen(5);
su('admin');

/**

title=taskModel->getTeamMembersByIdList();
timeout=0
cid=1

- 执行$emptyData属性 @0
- 执行count($taskTeamGroup)属性 @2
- 执行$firstTaskTeam第0条的account属性 @admin

*/

global $tester;
$tester->loadModel('task');

$taskIdList    = array(1, 2);
$emptyData     = $tester->task->getTeamMembersByIdList(array());
$taskTeamGroup = $tester->task->getTeamMembersByIdList($taskIdList);
$firstTaskTeam = current($taskTeamGroup);

r($emptyData)            && p()               && e('0');     // 测试传入空的taskIdList
r(count($taskTeamGroup)) && p()               && e('2');     // 测试查询给定taskIdList的任务数量
r($firstTaskTeam)        && p('0:account')    && e('admin'); // 测试查询任务id为1团队中第一个人的用户名
