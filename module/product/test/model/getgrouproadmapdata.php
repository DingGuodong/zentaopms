#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';

zdTable('user')->gen(5);
zdTable('productplan')->config('productplan')->gen(30);
zdTable('release')->config('release')->gen(30);
su('admin');

/**

title=测试productModel->getGroupRoadmapData();
timeout=0
cid=1

*/

$productIdList = array(1, 6);
$branchList    = array('all', '0', '1');
$countList     = array(0, 1);

global $tester;
$tester->loadModel('product');
r(count($tester->product->getGroupRoadmapData($productIdList[0], $branchList[0], $countList[0]))) && p() && e('2'); // 获取产品ID为1的产品路线图数据
r(count($tester->product->getGroupRoadmapData($productIdList[0], $branchList[1], $countList[0]))) && p() && e('2'); // 获取产品ID为1下主干的产品路线图数据
r(count($tester->product->getGroupRoadmapData($productIdList[0], $branchList[2], $countList[0]))) && p() && e('2'); // 获取产品ID为1下分支1的产品路线图数据
r(count($tester->product->getGroupRoadmapData($productIdList[0], $branchList[0], $countList[1]))) && p() && e('2'); // 获取产品ID为1的产品路线图数据
r(count($tester->product->getGroupRoadmapData($productIdList[1], $branchList[0], $countList[0]))) && p() && e('2'); // 获取产品ID为6的产品路线图数据
r(count($tester->product->getGroupRoadmapData($productIdList[1], $branchList[1], $countList[0]))) && p() && e('2'); // 获取产品ID为6下主干的产品路线图数据
r(count($tester->product->getGroupRoadmapData($productIdList[1], $branchList[2], $countList[0]))) && p() && e('2'); // 获取产品ID为6下分支1的产品路线图数据
r(count($tester->product->getGroupRoadmapData($productIdList[1], $branchList[0], $countList[1]))) && p() && e('2'); // 获取产品ID为6的产品路线图数据
