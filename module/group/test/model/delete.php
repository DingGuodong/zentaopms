#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/group.class.php';
su('admin');

/**

title=测试 groupModel->delete();
cid=1
pid=1

删除id为10的组 >> 1
删除id为0的组 >> 1

*/

$groupID = 10;

$group = new groupTest();
r($group->deleteTest($groupID)) && p() && e('1'); // 删除id为10的组
r($group->deleteTest(0))        && p() && e('1'); // 删除id为0的组
