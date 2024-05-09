#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/kanban.unittest.class.php';
su('admin');

zenData('kanbancolumn')->gen(5);

/**

title=测试 kanbanModel->archiveColumn();
timeout=0
cid=1

- 归档看板列1
 - 属性name @未开始
 - 属性archived @1
- 归档看板列1
 - 属性name @进行中
 - 属性archived @1
- 归档看板列2
 - 属性name @已完成
 - 属性archived @1
- 归档看板列3
 - 属性name @已关闭
 - 属性archived @1
- 归档看板列4
 - 属性name @未开始
 - 属性archived @1
- 测试归档不存在的看板列 @0

*/

$columnIDList = array('1', '2', '3', '4', '5', '1000001');

$kanban = new kanbanTest();

r($kanban->archiveColumnTest($columnIDList[0])) && p('name,archived') && e('未开始,1'); // 归档看板列1
r($kanban->archiveColumnTest($columnIDList[1])) && p('name,archived') && e('进行中,1'); // 归档看板列1
r($kanban->archiveColumnTest($columnIDList[2])) && p('name,archived') && e('已完成,1'); // 归档看板列2
r($kanban->archiveColumnTest($columnIDList[3])) && p('name,archived') && e('已关闭,1'); // 归档看板列3
r($kanban->archiveColumnTest($columnIDList[4])) && p('name,archived') && e('未开始,1'); // 归档看板列4
r($kanban->archiveColumnTest($columnIDList[5])) && p('')              && e('0');        // 测试归档不存在的看板列