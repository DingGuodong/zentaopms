#!/usr/bin/env php
<?php
declare(strict_types=1);
/**

title=测试 upgradeModel->processDeployStepAction();
timeout=0
cid=1

- 测试存在的上线步骤ID1不能删除
 - 属性objectID @1
 - 属性objectType @deploystep
- 测试存在的上线步骤ID2不能删除
 - 属性objectID @10
 - 属性objectType @deploystep
- 测试不存在的上线步骤ID0是否被删除 @0
- 测试不存在的上线步骤ID11是否被删除 @0

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/upgrade.unittest.class.php';

zenData('deploystep')->loadYaml('deploystep')->gen(10);
zenData('action')->loadYaml('action')->gen(20);
zenData('user')->gen(5);
su('admin');

$upgrade = new upgradeTest();
r($upgrade->processDeployStepActionTest(1))  && p('objectID,objectType') && e('1,deploystep');  //测试存在的上线步骤ID1不能删除
r($upgrade->processDeployStepActionTest(10)) && p('objectID,objectType') && e('10,deploystep'); //测试存在的上线步骤ID2不能删除
r($upgrade->processDeployStepActionTest(0))  && p('')                    && e('0');             //测试不存在的上线步骤ID0是否被删除
r($upgrade->processDeployStepActionTest(11)) && p('')                    && e('0');             //测试不存在的上线步骤ID11是否被删除
