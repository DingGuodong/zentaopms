#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/task.class.php';

zdTable('effort')->config('effort_managetaskteam')->gen(10);
zdTable('taskteam')->config('taskteam_managetaskteam')->gen(10);

/**

title=taskModel->manageTaskTeam();
cid=1
pid=1

*/

$taskIdList       = array(1, 2, 3);
$taskStatusList   = array('wait', 'doing', 'done', 'closed');
$modeList         = array('linear', 'multi');
$teamList         = array(array('admin', 'user1', 'user2', 'user3'), array('admin', 'user1', 'user2', 'user4'), array('user4', 'user5', 'user6', 'user7', 'user9'), array('user4', 'user5', 'user6', 'user7', 'user8'));
$teamSourceList   = array(array('admin', 'user1', 'user2', 'user3'), array('admin', 'user1', 'user2', 'user4'), array('user4', 'user5', 'user6', 'user7', 'user9'), array('user4', 'user5', 'user6', 'user7', 'user8'));
$teamEstimateList = array(array(1, 2, 3, 4), array(2, 3, 4, 5, 7));
$teamConsumedList = array(array(0, 0, 0, 0), array(4, 3, 2, 1), array(4, 3, 2, 1, 0));
$teamLeftList     = array(array(0, 0, 0, 0), array(0, 1, 3, 5, 7));
$teamData[]       = array('team' => $teamList[1], 'teamSourceList' => $teamSourceList[0], 'teamEstimateList' => $teamEstimateList[0], 'teamConsumedList' => $teamConsumedList[0], 'teamLeftList' => $teamLeftList[0]);
$teamData[]       = array('team' => $teamList[1], 'teamSourceList' => $teamSourceList[0], 'teamEstimateList' => $teamEstimateList[0], 'teamConsumedList' => $teamConsumedList[1], 'teamLeftList' => $teamLeftList[0]);
$teamData[]       = array('team' => $teamList[0], 'teamSourceList' => $teamSourceList[1], 'teamEstimateList' => $teamEstimateList[0], 'teamConsumedList' => $teamConsumedList[0], 'teamLeftList' => $teamLeftList[0]);
$teamData[]       = array('team' => $teamList[0], 'teamSourceList' => $teamSourceList[1], 'teamEstimateList' => $teamEstimateList[0], 'teamConsumedList' => $teamConsumedList[1], 'teamLeftList' => $teamLeftList[0]);
$teamData[]       = array('team' => $teamList[0], 'teamSourceList' => $teamSourceList[0], 'teamEstimateList' => $teamEstimateList[0], 'teamConsumedList' => $teamConsumedList[0], 'teamLeftList' => $teamLeftList[0]);
$teamData[]       = array('team' => $teamList[0], 'teamSourceList' => $teamSourceList[0], 'teamEstimateList' => $teamEstimateList[0], 'teamConsumedList' => $teamConsumedList[1], 'teamLeftList' => $teamLeftList[0]);
$teamData[]       = array('team' => $teamList[2], 'teamSourceList' => $teamSourceList[2], 'teamEstimateList' => $teamEstimateList[0], 'teamConsumedList' => $teamConsumedList[0], 'teamLeftList' => $teamLeftList[0]);
$teamData[]       = array('team' => $teamList[2], 'teamSourceList' => $teamSourceList[2], 'teamEstimateList' => $teamEstimateList[0], 'teamConsumedList' => $teamConsumedList[1], 'teamLeftList' => $teamLeftList[0]);
$teamData[]       = array('team' => $teamList[2], 'teamSourceList' => $teamSourceList[3], 'teamEstimateList' => $teamEstimateList[0], 'teamConsumedList' => $teamConsumedList[0], 'teamLeftList' => $teamLeftList[0]);
$teamData[]       = array('team' => $teamList[2], 'teamSourceList' => $teamSourceList[3], 'teamEstimateList' => $teamEstimateList[0], 'teamConsumedList' => $teamConsumedList[1], 'teamLeftList' => $teamLeftList[0]);

$task = new taskTest();

$result1 = $task->manageTaskTeamTest($taskIdList[0], $taskStatusList[0], $modeList[0], $teamData[0]);
$result2 = $task->manageTaskTeamTest($taskIdList[0], $taskStatusList[0], $modeList[0], $teamData[1]);
r(implode(',', $result1)) && p() && e('admin,user1,user2,user4'); // 测试获取任务ID 1 状态为 wait 的串行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('4');                       // 测试获取任务ID 1 状态为 wait 的串行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('admin,user1,user2,user4'); // 测试获取任务ID 1 状态为 wait 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('4');                       // 测试获取任务ID 1 状态为 wait 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[0], $taskStatusList[0], $modeList[1], $teamData[2]);
$result2 = $task->manageTaskTeamTest($taskIdList[0], $taskStatusList[0], $modeList[1], $teamData[3]);
r(implode(',', $result1)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 1 状态为 wait 的并行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('4');                       // 测试获取任务ID 1 状态为 wait 的并行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 1 状态为 wait 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('4');                       // 测试获取任务ID 1 状态为 wait 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[0], $taskStatusList[1], $modeList[0], $teamData[4]);
$result2 = $task->manageTaskTeamTest($taskIdList[0], $taskStatusList[1], $modeList[0], $teamData[5]);
r(implode(',', $result1)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 1 状态为 doing 的串行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('4');                       // 测试获取任务ID 1 状态为 doing 的串行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 1 状态为 doing 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('4');                       // 测试获取任务ID 1 状态为 doing 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[0], $taskStatusList[1], $modeList[1], $teamData[2]);
$result2 = $task->manageTaskTeamTest($taskIdList[0], $taskStatusList[1], $modeList[1], $teamData[3]);
r(implode(',', $result1)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 1 状态为 doing 的并行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('4');                       // 测试获取任务ID 1 状态为 doing 的并行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 1 状态为 doing 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('4');                       // 测试获取任务ID 1 状态为 doing 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[0], $taskStatusList[1], $modeList[0], $teamData[4]);
$result2 = $task->manageTaskTeamTest($taskIdList[0], $taskStatusList[1], $modeList[0], $teamData[5]);
r(implode(',', $result1)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 1 状态为 done 的串行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('4');                       // 测试获取任务ID 1 状态为 done 的串行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 1 状态为 done 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('4');                       // 测试获取任务ID 1 状态为 done 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[0], $taskStatusList[1], $modeList[1], $teamData[2]);
$result2 = $task->manageTaskTeamTest($taskIdList[0], $taskStatusList[1], $modeList[1], $teamData[3]);
r(implode(',', $result1)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 1 状态为 done 的并行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('4');                       // 测试获取任务ID 1 状态为 done 的并行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 1 状态为 done 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('4');                       // 测试获取任务ID 1 状态为 done 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[1], $taskStatusList[0], $modeList[0], $teamData[4]);
$result2 = $task->manageTaskTeamTest($taskIdList[1], $taskStatusList[0], $modeList[0], $teamData[5]);
r(implode(',', $result1)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 2 状态为 wait 的串行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('4');                       // 测试获取任务ID 2 状态为 wait 的串行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 2 状态为 wait 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('4');                       // 测试获取任务ID 2 状态为 wait 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[1], $taskStatusList[0], $modeList[1], $teamData[2]);
$result2 = $task->manageTaskTeamTest($taskIdList[1], $taskStatusList[0], $modeList[1], $teamData[3]);
r(implode(',', $result1)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 2 状态为 wait 的并行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('4');                       // 测试获取任务ID 2 状态为 wait 的并行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 2 状态为 wait 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('4');                       // 测试获取任务ID 2 状态为 wait 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[1], $taskStatusList[1], $modeList[0], $teamData[4]);
$result2 = $task->manageTaskTeamTest($taskIdList[1], $taskStatusList[1], $modeList[0], $teamData[5]);
r(implode(',', $result1)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 2 状态为 doing 的串行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('4');                       // 测试获取任务ID 2 状态为 doing 的串行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 2 状态为 doing 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('4');                       // 测试获取任务ID 2 状态为 doing 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[1], $taskStatusList[1], $modeList[1], $teamData[2]);
$result2 = $task->manageTaskTeamTest($taskIdList[1], $taskStatusList[1], $modeList[1], $teamData[3]);
r(implode(',', $result1)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 2 状态为 doing 的并行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('4');                       // 测试获取任务ID 2 状态为 doing 的并行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('admin,user1,user2,user3'); // 测试获取任务ID 2 状态为 doing 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('4');                       // 测试获取任务ID 2 状态为 doing 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[2], $taskStatusList[0], $modeList[0], $teamData[6]);
$result2 = $task->manageTaskTeamTest($taskIdList[2], $taskStatusList[0], $modeList[0], $teamData[7]);
r(implode(',', $result1)) && p() && e('user4,user5,user6,user7,user9'); // 测试获取任务ID 3 状态为 wait 的串行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('5');                             // 测试获取任务ID 3 状态为 wait 的串行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('user4,user5,user6,user7,user9'); // 测试获取任务ID 3 状态为 wait 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('5');                             // 测试获取任务ID 3 状态为 wait 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[2], $taskStatusList[0], $modeList[1], $teamData[6]);
$result2 = $task->manageTaskTeamTest($taskIdList[2], $taskStatusList[0], $modeList[1], $teamData[7]);
r(implode(',', $result1)) && p() && e('user4,user5,user6,user7,user9'); // 测试获取任务ID 3 状态为 wait 的并行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('5');                             // 测试获取任务ID 3 状态为 wait 的并行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('user4,user5,user6,user7,user9'); // 测试获取任务ID 3 状态为 wait 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('5');                             // 测试获取任务ID 3 状态为 wait 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[2], $taskStatusList[1], $modeList[0], $teamData[6]);
$result2 = $task->manageTaskTeamTest($taskIdList[2], $taskStatusList[1], $modeList[0], $teamData[7]);
r(implode(',', $result1)) && p() && e('user4,user5,user6,user7,user9'); // 测试获取任务ID 3 状态为 doing 的串行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('5');                             // 测试获取任务ID 3 状态为 doing 的串行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('user4,user5,user6,user7,user9'); // 测试获取任务ID 3 状态为 doing 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('5');                             // 测试获取任务ID 3 状态为 doing 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[2], $taskStatusList[1], $modeList[1], $teamData[6]);
$result2 = $task->manageTaskTeamTest($taskIdList[2], $taskStatusList[1], $modeList[1], $teamData[7]);
r(implode(',', $result1)) && p() && e('user4,user5,user6,user7,user9'); // 测试获取任务ID 3 状态为 doing 的并行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('5');                             // 测试获取任务ID 3 状态为 doing 的并行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('user4,user5,user6,user7,user9'); // 测试获取任务ID 3 状态为 doing 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('5');                             // 测试获取任务ID 3 状态为 doing 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[2], $taskStatusList[1], $modeList[0], $teamData[6]);
$result2 = $task->manageTaskTeamTest($taskIdList[2], $taskStatusList[1], $modeList[0], $teamData[7]);
r(implode(',', $result1)) && p() && e('user4,user5,user6,user7,user9'); // 测试获取任务ID 3 状态为 done 的串行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('5');                             // 测试获取任务ID 3 状态为 done 的串行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('user4,user5,user6,user7,user9'); // 测试获取任务ID 3 状态为 done 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('5');                             // 测试获取任务ID 3 状态为 done 的串行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队

$result1 = $task->manageTaskTeamTest($taskIdList[2], $taskStatusList[1], $modeList[1], $teamData[8]);
$result2 = $task->manageTaskTeamTest($taskIdList[2], $taskStatusList[1], $modeList[1], $teamData[9]);
r(implode(',', $result1)) && p() && e('user4,user5,user6,user7,user9'); // 测试获取任务ID 3 状态为 done 的并行多人任务 相关人员未改变的多人任务团队
r(count($result1))        && p() && e('5');                             // 测试获取任务ID 3 状态为 done 的并行多人任务 相关人员未改变的多人任务团队
r(implode(',', $result2)) && p() && e('user4,user5,user6,user7,user9'); // 测试获取任务ID 3 状态为 done 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
r(count($result2))        && p() && e('5');                             // 测试获取任务ID 3 状态为 done 的并行多人任务 相关人员未改变 存在消耗和剩余工时的的多人任务团队
