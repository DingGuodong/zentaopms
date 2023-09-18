#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/testcase.class.php';

zdTable('case')->gen('5');
zdTable('casestep')->gen('5');
zdTable('user')->gen('1');

su('admin');

/**

title=测试 testcaseModel->update();
timeout=0
cid=1

*/

$changeTitle        = array('title' => '修改后的名称');
$changePrecondition = array('precondition' => '修改后的前置条件');
$changeStatus       = array('status' => 'normal');
$changePri          = array('pri' => '3');

$testcaseIdList = array(1, 2);

$title      = array('title' => '修改后的用例');
$emptyTitle = array('title' => '');
$type       = array('type'  => 'install');
$emptyType  = array('type'  => '');
$steps      = array('steps' => array('步骤1'), 'stepType' => array('step'), 'expects' => array('预期1'), 'stepChanged' => true, 'version' => 2);

$testcase = new testCaseTest();
r($testcase->updateTest($testcaseIdList[0], $title))      && p('0:field,old,new') && e('title,这个是测试用例1,修改后的用例'); // 测试更新用例名称
r($testcase->updateTest($testcaseIdList[0], $emptyTitle)) && p('title:0')         && e('『用例名称』不能为空。');             // 测试名称不能为空
r($testcase->updateTest($testcaseIdList[0], $type))       && p('0:field,old,new') && e('type,feature,install');               // 测试更新用例类型
r($testcase->updateTest($testcaseIdList[0], $emptyType))  && p('type:0')          && e('『类型』不能为空。');                 // 测试类型不能为空
r($testcase->updateTest($testcaseIdList[0], $steps))      && p('0:field,new')     && e('steps,步骤1 EXPECT:预期1');           // 测试更新用例步骤
