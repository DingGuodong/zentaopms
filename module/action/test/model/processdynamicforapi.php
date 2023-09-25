#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/action.class.php';
su('admin');

zdTable('action')->gen(10);

/**

title=测试 actionModel->processDynamicForAPI();
cid=1
pid=1

测试处理空数据 >> 0
处理所有动态   >> 1,admin,admin

*/

$action = new actionTest();

$dynamics = $tester->dao->select('*')->from(TABLE_ACTION)->fetchAll();

r($action->processDynamicForAPITest(array()))   && p()                            && e('0');              // 测试处理空数据
r($action->processDynamicForAPITest($dynamics)) && p('actor:id,account,realname') && e('1,admin,admin');  // 处理所有动态
