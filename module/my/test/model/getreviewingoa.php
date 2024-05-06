#!/usr/bin/env php
<?php
declare(strict_types=1);
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/my.unittest.class.php';

zenData('user')->gen('2');

su('admin');

/**

title=测试 myModel->getReviewingOAs();
cid=1
pid=1

*/

$my = new myTest();

$orderBy    = array('id_desc', 'id_asc');
$checkExist = array(false, true);

r($my->getReviewingOATest($orderBy[0], $checkExist[0])) && p() && e('empty'); // 测试获取排序 id_desc 的审批id。
r($my->getReviewingOATest($orderBy[0], $checkExist[1])) && p() && e('empty'); // 测试获取排序 id_desc 的审批是否存在。
r($my->getReviewingOATest($orderBy[1], $checkExist[0])) && p() && e('empty'); // 测试获取排序 id_asc 的审批id。
r($my->getReviewingOATest($orderBy[1], $checkExist[1])) && p() && e('empty'); // 测试获取排序 id_asc 的审批是否存在。
