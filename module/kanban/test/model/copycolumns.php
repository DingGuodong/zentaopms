#!/usr/bin/env php
<?php

/**

title=测试 kanbanModel->copyColumns();
timeout=0
cid=1

- 正常复制看板列，查看所有看板列数量 @9
- 正常复制看板列，最后一个看板列的名字第8条的name属性 @复制看板列4
- 复制空看板列，没有条目被插入 @0
- 复制空看板列，应该返回错误信息第name条的0属性 @『看板列名称』不能为空。

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/kanban.unittest.class.php';
su('admin');

zenData('kanbancolumn')->gen(5);

$kanban     = (object)array('id' => 1, 'name' => '测试看板');
$regionID   = 1;
$newGroupID = 100001;

$copyColumn1  = (object)array('id' => 1, 'title' => '复制看板列1', 'parent' => 0, 'limit' => -1);
$copyColumn2  = (object)array('id' => 2, 'title' => '复制看板列2', 'parent' => 0, 'limit' => -1);
$copyColumn3  = (object)array('id' => 3, 'title' => '复制看板列3', 'parent' => 0, 'limit' => -1);
$copyColumn4  = (object)array('id' => 4, 'title' => '复制看板列4', 'parent' => 0, 'limit' => -1);
$emptyColumn  = (object)array('id' => 5, 'title' => '', 'parent' => 0, 'limit' => -1);

$kanban = new kanbanTest();

$copyCols = $kanban->copyColumnsTest([$copyColumn1, $copyColumn2, $copyColumn3, $copyColumn4], $regionID, $newGroupID);

r(count($copyCols)) && p("")       && e('9');           // 正常复制看板列，查看所有看板列数量
r($copyCols)        && p("8:name") && e('复制看板列4'); // 正常复制看板列，最后一个看板列的名字
r($kanban->copyColumnsTest([$emptyColumn], $regionID, $newGroupID)) && p('') && e('0'); //复制空看板列，没有条目被插入
r(dao::getError()) && p('name:0') && e('『看板列名称』不能为空。'); //复制空看板列，应该返回错误信息