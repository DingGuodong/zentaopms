#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/dept.class.php';
su('admin');

/**

title=测试 deptModel->updateOrder();
cid=1
pid=1

修改部门排序 >> 17

*/

$orders = array('17');

$dept = new deptTest();
r($dept->updateOrderTest($orders)) && p('17:order') && e('17'); //修改部门排序