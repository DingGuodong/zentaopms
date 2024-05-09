#!/usr/bin/env php
<?php

/**

title=测试 programplanModel->getByID();
cid=0

- 传入0参数。 @0
- 传入不存在的ID。 @0
- 判断项目阶段id=2的name
 - 属性name @执行1-1
 - 属性code @sprint1-1
- 判断项目id=1走else的milestone,setMilestone
 - 属性milestone @0
 - 属性setMilestone @``

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
su('admin');

zenData('project')->loadYaml('project')->gen(10);

global $tester;
$tester->loadModel('programplan');

r($tester->programplan->getByID(0))  && p()            && e('0');                 // 传入0参数。
r($tester->programplan->getByID(20)) && p()            && e('0');                 // 传入不存在的ID。
r($tester->programplan->getByID(2))  && p('name,code') && e('执行1-1,sprint1-1'); // 判断项目阶段id=2的name
r($tester->programplan->getByID(1))  && p('milestone,setMilestone') && e('0,``'); // 判断项目id=1走else的milestone,setMilestone
