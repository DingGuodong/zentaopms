#!/usr/bin/env php
<?php

/**

title=测试 loadModel->insertProjectSpec()
cid=0

- 传入空参数 @0
- 检查插入的spec数据。
 - 属性name @Test plan
 - 属性version @3
 - 属性milestone @1
 - 属性begin @2023-09-28
 - 属性end @2024-04-28

*/

include dirname(__FILE__, 5). '/test/lib/init.php';
su('admin');

zenData('projectspec')->gen(0);

global $tester;
$tester->loadModel('programplan');

$plan =  new stdclass();
$plan->version   = '3';
$plan->name      = 'Test plan';
$plan->milestone = '1';
$plan->begin     = '2023-09-28';
$plan->end       = '2024-04-28';

r($tester->programplan->insertProjectSpec(0, $plan)) && p() && e('0'); //传入空参数

$planID = 2;
$tester->programplan->insertProjectSpec(2, $plan);
$spec = $tester->programplan->dao->select('*')->from(TABLE_PROJECTSPEC)->where('project')->eq($planID)->andWhere('version')->eq(3)->fetch();
r($spec) && p('name,version,milestone,begin,end')  && e('Test plan,3,1,2023-09-28,2024-04-28'); //检查插入的spec数据。
