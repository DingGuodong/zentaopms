#!/usr/bin/env php
<?php
declare(strict_types=1);

/**

title=测试 zanodeModel->createDefaultSnapshot().
timeout=0
cid=1

- 测试不是运行中的执行节点创建默认快照属性name @请检查执行节点状态

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/zanode.unittest.class.php';

zenData('host')->loadYaml('host')->gen(5);
zenData('user')->gen(5);
su('admin');

$zanode = new zanodeTest();
r($zanode->createDefaultSnapshotTest(2)) && p('name') && e('请检查执行节点状态'); //测试不是运行中的执行节点创建默认快照