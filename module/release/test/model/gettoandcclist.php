#!/usr/bin/env php
<?php

/**

title=测试 releaseModel->getToAndCcList();
timeout=0
cid=1

- 测试获取状态为正常的发布的通知人员属性1 @,admin,dev1
- 测试获取状态为停止维护的发布的通知人员属性1 @,admin,dev1
- 测试获取已删除的发布的通知人员属性1 @,admin,dev1

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/release.unittest.class.php';

$release = zenData('release')->loadYaml('release');
$release->stories->range('`1,2,3`,[]');
$release->bugs->range('`1,2,3`,[]');
$release->notify->range('`PO,QD,feedback`,`SC,ET,PT,CT`');
$release->status->range('normal,terminate');
$release->deleted->range('0{2},1');
$release->gen(5);

zenData('story')->loadYaml('story')->gen(5);
zenData('bug')->loadYaml('bug')->gen(5);
zenData('product')->loadYaml('product')->gen(5);
zenData('build')->loadYaml('build')->gen(5);
zenData('team')->gen(0);
zenData('user')->gen(5);
su('admin');

$releases = array(1, 2, 3);

$releaseTester = new releaseTest();
r($releaseTester->getToAndCcListTest($releases[0])) && p('1', ';') && e(',admin,dev1'); // 测试获取状态为正常的发布的通知人员
r($releaseTester->getToAndCcListTest($releases[1])) && p('1', ';') && e(',admin,dev1'); // 测试获取状态为停止维护的发布的通知人员
r($releaseTester->getToAndCcListTest($releases[2])) && p('1', ';') && e(',admin,dev1'); // 测试获取已删除的发布的通知人员