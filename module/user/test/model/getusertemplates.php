#!/usr/bin/env php
<?php

/**

title=测试 userModel::getUserTemplates();
cid=0

- admin 用户可以查看导出任务的模板有 2 个。 @2
- 第一个模板的 id 是 1，创建者是 admin。
 - 第0条的id属性 @1
 - 第0条的account属性 @admin
- 第二个模板的 id 是 5，创建者是 admin。
 - 第1条的id属性 @5
 - 第1条的account属性 @admin
- admin 用户可以查看需求的模板有 3 个。 @3
- 第一个模板的 id 是 2，创建者是 admin。
 - 第0条的id属性 @2
 - 第0条的account属性 @admin
- 第二个模板的 id 是 6，创建者是 user1。
 - 第1条的id属性 @6
 - 第1条的account属性 @user1
- 第三个模板的 id 是 10，创建者是 user1。
 - 第2条的id属性 @10
 - 第2条的account属性 @user1
- admin 用户可以查看导出 bug 的模板有 1 个。 @1
- 第一个模板的 id 是 3，创建者是 admin。
 - 第0条的id属性 @3
 - 第0条的account属性 @admin
- admin 用户可以查看导出需求的模板有 2 个。 @2
- 第一个模板的 id 是 4，创建者是 admin。
 - 第0条的id属性 @4
 - 第0条的account属性 @admin
- 第二个模板的 id 是 8，创建者是 user1。
 - 第1条的id属性 @8
 - 第1条的account属性 @user1
- user1 用户可以查看导出任务的模板有 1 个。 @1
- 第一个模板的 id 是 9，创建者是 user1。
 - 第0条的id属性 @9
 - 第0条的account属性 @user1
- user1 用户可以查看需求的模板有 3 个。 @3
- 第一个模板的 id 是 2，创建者是 admin。
 - 第0条的id属性 @2
 - 第0条的account属性 @admin
- 第二个模板的 id 是 6，创建者是 user1。
 - 第1条的id属性 @6
 - 第1条的account属性 @user1
- 第三个模板的 id 是 10，创建者是 user1。
 - 第2条的id属性 @10
 - 第2条的account属性 @user1
- user1 用户可以查看导出 bug 的模板有 1 个。 @1
- 第一个模板的 id 是 7，创建者是 user1。
 - 第0条的id属性 @7
 - 第0条的account属性 @user1
- user1 用户可以查看导出需求的模板有 2 个。 @2
- 第一个模板的 id 是 4，创建者是 admin。
 - 第0条的id属性 @4
 - 第0条的account属性 @admin
- 第二个模板的 id 是 8，创建者是 user1。
 - 第1条的id属性 @8
 - 第1条的account属性 @user1

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/user.unittest.class.php';

zenData('user')->gen(2);

$table = zenData('usertpl');
$table->account->range('admin{5},user1{5}');
$table->gen(10);

su('admin'); // 当前用户切换为 admin。

$userTest = new userTest();

$templates = $userTest->getUserTemplatesTest('exporttask');
r(count($templates)) && p()               && e(2);         // admin 用户可以查看导出任务的模板有 2 个。
r($templates)        && p('0:id,account') && e('1,admin'); // 第一个模板的 id 是 1，创建者是 admin。
r($templates)        && p('1:id,account') && e('5,admin'); // 第二个模板的 id 是 5，创建者是 admin。

$templates = $userTest->getUserTemplatesTest('story');
r(count($templates)) && p()               && e(3);          // admin 用户可以查看需求的模板有 3 个。
r($templates)        && p('0:id,account') && e('2,admin');  // 第一个模板的 id 是 2，创建者是 admin。
r($templates)        && p('1:id,account') && e('6,user1');  // 第二个模板的 id 是 6，创建者是 user1。
r($templates)        && p('2:id,account') && e('10,user1'); // 第三个模板的 id 是 10，创建者是 user1。

$templates = $userTest->getUserTemplatesTest('exportbug');
r(count($templates)) && p()               && e(1);         // admin 用户可以查看导出 bug 的模板有 1 个。
r($templates)        && p('0:id,account') && e('3,admin'); // 第一个模板的 id 是 3，创建者是 admin。

$templates = $userTest->getUserTemplatesTest('exportstory');
r(count($templates)) && p()               && e(2);         // admin 用户可以查看导出需求的模板有 2 个。
r($templates)        && p('0:id,account') && e('4,admin'); // 第一个模板的 id 是 4，创建者是 admin。
r($templates)        && p('1:id,account') && e('8,user1'); // 第二个模板的 id 是 8，创建者是 user1。

su('user1'); // 当前用户切换为 user1。

$templates = $userTest->getUserTemplatesTest('exporttask');
r(count($templates)) && p()               && e(1);         // user1 用户可以查看导出任务的模板有 1 个。
r($templates)        && p('0:id,account') && e('9,user1'); // 第一个模板的 id 是 9，创建者是 user1。

$templates = $userTest->getUserTemplatesTest('story');
r(count($templates)) && p()               && e(3);          // user1 用户可以查看需求的模板有 3 个。
r($templates)        && p('0:id,account') && e('2,admin');  // 第一个模板的 id 是 2，创建者是 admin。
r($templates)        && p('1:id,account') && e('6,user1');  // 第二个模板的 id 是 6，创建者是 user1。
r($templates)        && p('2:id,account') && e('10,user1'); // 第三个模板的 id 是 10，创建者是 user1。

$templates = $userTest->getUserTemplatesTest('exportbug');
r(count($templates)) && p()               && e(1);         // user1 用户可以查看导出 bug 的模板有 1 个。
r($templates)        && p('0:id,account') && e('7,user1'); // 第一个模板的 id 是 7，创建者是 user1。

$templates = $userTest->getUserTemplatesTest('exportstory');
r(count($templates)) && p()               && e(2);         // user1 用户可以查看导出需求的模板有 2 个。
r($templates)        && p('0:id,account') && e('4,admin'); // 第一个模板的 id 是 4，创建者是 admin。
r($templates)        && p('1:id,account') && e('8,user1'); // 第二个模板的 id 是 8，创建者是 user1。
