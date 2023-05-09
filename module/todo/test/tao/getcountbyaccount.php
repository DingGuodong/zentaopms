#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
su('admin');

function initData()
{
    zdTable('todo')->config('getcountbyaccount')->gen(20);
}

/**

title=测试 todoModel->getCount();
timeout=0
cid=1

- 获取用户admin的所有待办个数 @4

- 获取用户user1的所有待办个数 @3

- 获取用户user2的所有待办个数 @3

- 获取不存在的用户所有待办个数 @0

- 获取用户admin的vision=>rand所有待办个数 @4

*/

initData();

global $tester;
$tester->loadModel('todo')->todoTao;

$accountList = array('admin', 'user1', 'user2', 'user10001');

r($tester->todo->getCountByAccount($accountList[0]))         && p() && e('4'); // 获取用户admin的所有待办个数
r($tester->todo->getCountByAccount($accountList[1]))         && p() && e('3'); // 获取用户user1的所有待办个数
r($tester->todo->getCountByAccount($accountList[2]))         && p() && e('3'); // 获取用户user2的所有待办个数
r($tester->todo->getCountByAccount($accountList[3]))         && p() && e('0'); // 获取不存在的用户所有待办个数
r($tester->todo->getCountByAccount($accountList[0],'rnd'))   && p() && e('4'); // 获取用户admin的vision=>rand所有待办个数
