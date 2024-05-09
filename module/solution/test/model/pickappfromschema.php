#!/usr/bin/env php
<?php

/**

title=测试 solutionModel->pickAppFromSchema();
timeout=0
cid=1

- 类别和应用不对应 @0
- 类别和应用对应
 - 属性id @58
 - 属性alias @GitLab
 - 属性status @waiting

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/solution.unittest.class.php';

zenData('user')->gen(5);
zenData('solution')->loadYaml('solution')->gen(0);
zenData('instance')->loadYaml('instance')->gen(0);

$solutionModel = new solutionTest();
r($solutionModel->pickAppFromSchemaTest('git', 'gitee')) && p() && e('0'); // 类别和应用不对应

r($solutionModel->pickAppFromSchemaTest('git', 'gitlab')) && p('id,alias,status') && e('58,GitLab,waiting'); // 类别和应用对应