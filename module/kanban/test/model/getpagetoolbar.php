#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';

zenData('kanban')->gen(2);
zenData('user')->gen(5);

/**

title=测试 kanbanModel->getPageToolBar();
timeout=0
cid=1

- 查看普通用户获取操作按钮的字符长度 @161
- 查看管理员获取操作按钮的字符长度 @1282

*/
global $tester;
$tester->loadModel('kanban');

$kanban1 = $tester->kanban->getByID(1);
$kanban2 = $tester->kanban->getByID(2);

$toolbar1 = $tester->kanban->getPageToolBar($kanban1);
su('admin');
$toolbar2 = $tester->kanban->getPageToolBar($kanban2);

r(strlen($toolbar1)) && p('') && e('161');  // 查看普通用户获取操作按钮的字符长度
r(strlen($toolbar2)) && p('') && e('1282'); // 查看管理员获取操作按钮的字符长度