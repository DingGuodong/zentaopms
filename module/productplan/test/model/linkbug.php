#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/productplan.unittest.class.php';

/**

title=productplanModel->linkBug()
timeout=0
cid=1

*/

zenData('bug')->gen(10);
$planID = 1;

$bugIdList[0] = array(3);
$bugIdList[1] = array(1, 2);
$bugIdList[2] = array(10000);

global $tester,$app;
$app->moduleName = 'productplan';
$app->rawModule  = 'productplan';
$tester->loadModel('productplan');

r($tester->productplan->linkBug($planID, $bugIdList[0])) && p() && e('1'); // id为1的计划关联id为3的bug
r($tester->productplan->linkBug($planID, $bugIdList[1])) && p() && e('1'); // id为1的计划关联id为1和2的bug
r($tester->productplan->linkBug($planID, $bugIdList[2])) && p() && e('1'); // 传入不存在的id
