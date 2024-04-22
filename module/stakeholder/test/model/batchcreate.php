#!/usr/bin/env php
<?php
/**

title=测试 stakeholderModel->batchCreate();
cid=1

- 测试项目ID为0、人员为空时，批量创建项目干系人 @0
- 测试项目ID为0、人员为admin、user1时，批量创建项目干系人 @1
- 测试项目ID为0、人员为user10、user11时，批量创建项目干系人 @3
- 测试项目ID为11、人员为空时，批量创建项目干系人 @0
- 测试项目ID为11、人员为admin、user1时，批量创建项目干系人 @5
- 测试项目ID为11、人员为user10、user11时，批量创建项目干系人 @7
- 测试项目ID不存在、人员为空时，批量创建项目干系人 @0
- 测试项目ID不存在、人员为admin、user1时，批量创建项目干系人 @9
- 测试项目ID不存在、人员为user10、user11时，批量创建项目干系人 @11

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/stakeholder.unittest.class.php';

su('admin', false);

zenData('user')->gen(5);
zenData('stakeholder')->gen(0);
zenData('product')->gen(0);
zenData('projectproduct')->gen(0);
zenData('group')->gen(0);
zenData('acl')->gen(0);
zenData('project')->loadYaml('project')->gen(15);
zenData('team')->loadYaml('team')->gen(10);

$projectID   = array(0, 11, 100);
$accounts[0] = array();
$accounts[1] = array('admin', 'user1');
$accounts[2] = array('user10', 'user11');

$stakeholderTester = new stakeholderTest();
r($stakeholderTester->batchCreateTest($projectID[0], $accounts[0])) && p()    && e('0');  // 测试项目ID为0、人员为空时，批量创建项目干系人
r($stakeholderTester->batchCreateTest($projectID[0], $accounts[1])) && p('0') && e('1');  // 测试项目ID为0、人员为admin、user1时，批量创建项目干系人
r($stakeholderTester->batchCreateTest($projectID[0], $accounts[2])) && p('0') && e('3');  // 测试项目ID为0、人员为user10、user11时，批量创建项目干系人
r($stakeholderTester->batchCreateTest($projectID[1], $accounts[0])) && p()    && e('0');  // 测试项目ID为11、人员为空时，批量创建项目干系人
r($stakeholderTester->batchCreateTest($projectID[1], $accounts[1])) && p('0') && e('5');  // 测试项目ID为11、人员为admin、user1时，批量创建项目干系人
r($stakeholderTester->batchCreateTest($projectID[1], $accounts[2])) && p('0') && e('7');  // 测试项目ID为11、人员为user10、user11时，批量创建项目干系人
r($stakeholderTester->batchCreateTest($projectID[2], $accounts[0])) && p()    && e('0');  // 测试项目ID不存在、人员为空时，批量创建项目干系人
r($stakeholderTester->batchCreateTest($projectID[2], $accounts[1])) && p('0') && e('9');  // 测试项目ID不存在、人员为admin、user1时，批量创建项目干系人
r($stakeholderTester->batchCreateTest($projectID[2], $accounts[2])) && p('0') && e('11'); // 测试项目ID不存在、人员为user10、user11时，批量创建项目干系人
