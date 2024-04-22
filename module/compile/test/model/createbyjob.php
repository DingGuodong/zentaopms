#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/compile.unittest.class.php';

zenData('compile')->gen(10);
zenData('job')->loadYaml('job')->gen(1);
su('admin');

/**

title=测试 compileModel->createByJob();
timeout=0
cid=1

- 检查是否可以拿到通过id为1的job数据创建的compile属性name @这是一个Job1
- 检查是否可以拿到通过不存在的job数据创建的compile属性name @0

*/

$compile = new compileTest();
r($compile->createByJobTest(1, '123')) && p('name') && e('这是一个Job1'); //检查是否可以拿到通过id为1的job数据创建的compile
r($compile->createByJobTest(3, '123')) && p('name') && e('0');            //检查是否可以拿到通过不存在的job数据创建的compile