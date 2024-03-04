#!/usr/bin/env php
<?php
/**

title=测试 holidayModel->updateProgramPlanDuration();
cid=1

- 测试插入id为 10 的节假日时迭代 11 项目的planDuration @250
- 测试插入id为 10 的节假日时迭代 60 项目的planDuration @252
- 测试插入id为 10 的节假日时迭代 101 项目的planDuration @191
- 测试插入id为 5 的节假日时迭代 11 项目的planDuration @250
- 测试插入id为 5 的节假日时迭代 60 项目的planDuration @252
- 测试插入id为 5 的节假日时迭代 101 项目的planDuration @191
- 测试插入id为 5 的节假日时迭代 11 项目的planDuration @250
- 测试插入id为 5 的节假日时迭代 60 项目的planDuration @252
- 测试插入id为 5 的节假日时迭代 101 项目的planDuration @191

*/
declare(strict_types=1);
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/holiday.class.php';

zdTable('holiday')->config('holiday')->gen(24);
zdTable('project')->config('execution')->gen(5);
zdTable('user')->gen(1);

su('admin');

$holidayIdList  = array(10, 5, 1);
$projectIdList  = array(11, 61, 101);
$updateDuration = array(true, false);

$holiday = new holidayTest();

r($holiday->updateProgramPlanDurationTest($projectIdList[0], $holidayIdList[0], $updateDuration[0])) && p() && e('250'); //测试插入id为 10 的节假日时迭代 11 项目的planDuration
r($holiday->updateProgramPlanDurationTest($projectIdList[1], $holidayIdList[0], $updateDuration[1])) && p() && e('252'); //测试插入id为 10 的节假日时迭代 60 项目的planDuration
r($holiday->updateProgramPlanDurationTest($projectIdList[2], $holidayIdList[0], $updateDuration[1])) && p() && e('191'); //测试插入id为 10 的节假日时迭代 101 项目的planDuration

r($holiday->updateProgramPlanDurationTest($projectIdList[0], $holidayIdList[1], $updateDuration[0])) && p() && e('250'); //测试插入id为 5 的节假日时迭代 11 项目的planDuration
r($holiday->updateProgramPlanDurationTest($projectIdList[1], $holidayIdList[1], $updateDuration[1])) && p() && e('252'); //测试插入id为 5 的节假日时迭代 60 项目的planDuration
r($holiday->updateProgramPlanDurationTest($projectIdList[2], $holidayIdList[1], $updateDuration[1])) && p() && e('191'); //测试插入id为 5 的节假日时迭代 101 项目的planDuration

r($holiday->updateProgramPlanDurationTest($projectIdList[0], $holidayIdList[2], $updateDuration[0])) && p() && e('250'); //测试插入id为 5 的节假日时迭代 11 项目的planDuration
r($holiday->updateProgramPlanDurationTest($projectIdList[1], $holidayIdList[2], $updateDuration[1])) && p() && e('252'); //测试插入id为 5 的节假日时迭代 60 项目的planDuration
r($holiday->updateProgramPlanDurationTest($projectIdList[2], $holidayIdList[2], $updateDuration[1])) && p() && e('191'); //测试插入id为 5 的节假日时迭代 101 项目的planDuration
