#!/usr/bin/env php
<?php

/**

title=测试 userModel->checkBeforeCreateOrEdit();
cid=0

- 使用系统预留用户名返回 false。属性result @0
- 使用系统预留用户名提示信息。第errors条的account属性 @用户名已被系统预留
- 使用正常用户名返回 true。属性result @1

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/user.unittest.class.php';

zenData('user')->loadYaml('user')->gen(1);

su('admin');

$random = updateSessionRandom();

$user1 = (object)array('account' => 'guest', 'verifyPassword' => '');
$user2 = (object)array('account' => 'admin', 'verifyPassword' => md5(md5('123456') . $random));

$userTest = new userTest();

$result1 = $userTest->checkBeforeCreateOrEditTest($user1);
r($result1) && p('result') && e(0);                            // 使用系统预留用户名返回 false。
r($result1) && p('errors:account') && e('用户名已被系统预留'); // 使用系统预留用户名提示信息。

r($userTest->checkBeforeCreateOrEditTest($user2, true)) && p('result') && e(1); // 使用正常用户名返回 true。
