#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/compile.unittest.class.php';

zenData('compile')->gen(1);
zenData('job')->loadYaml('job')->gen(1);
su('admin');

/**

title=测试 compileModel->getLastResult();
timeout=0
cid=1

- 检查id存在的时候是否能拿到数据属性createdBy @admin
- 检查id不存在的时候返回的结果属性createdBy @0

*/

$compile = new compileTest();

r($compile->getLastResultTest(1)) && p('createdBy') && e('admin'); //检查id存在的时候是否能拿到数据
r($compile->getLastResultTest(3)) && p('createdBy') && e('0');     //检查id不存在的时候返回的结果