#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
su('admin');

zdTable('product')->config('product')->gen(10);
zdTable('release')->config('release')->gen(100);

/**

title=taskModel->statisticRecentReleases();
timeout=0
cid=0

*/

$productIdList        = range(1, 8);
$noExistProductIdList = range(200, 203);
$today                = helper::today();
$yesterday            = date('Y-m-d', strtotime('-1 day', strtotime($today)));
$tomorrow             = date('Y-m-d', strtotime('+1 day', strtotime($today)));
$orderByList          = array('id_desc', 'product_asc');

global $tester;
$releaseModel = $tester->loadModel('release');

r($releaseModel->statisticRecentReleases(array()))               && p()            && e('0');       // 测试空数据的情况
r($releaseModel->statisticRecentReleases($productIdList))        && p('2:id,name') && e('1,发布1'); // 获取产品1的最新发布信息
r($releaseModel->statisticRecentReleases($noExistProductIdList)) && p()            && e('0');       // 获取不存在的产品的最新发布信息

r(count($releaseModel->statisticRecentReleases($productIdList, $today)))     && p() && e('7'); // 获取所有产品的最新发布信息，并且发布日期在今天之前
r(count($releaseModel->statisticRecentReleases($productIdList, $yesterday))) && p() && e('7'); // 获取所有产品的最新发布信息，并且发布日期在昨天之前
r(count($releaseModel->statisticRecentReleases($productIdList, $tomorrow)))  && p() && e('7'); // 获取所有产品的最新发布信息，并且发布日期在明天之前

r($releaseModel->statisticRecentReleases($productIdList, $today))     && p('1')         && e('`^$`');      // 获取产品1的最新发布信息，并且发布日期在今天之前
r($releaseModel->statisticRecentReleases($productIdList, $yesterday)) && p('2:id,name') && e('10,发布10'); // 获取产品2的最新发布信息，并且发布日期在今天之前
r($releaseModel->statisticRecentReleases($productIdList, $tomorrow))  && p('3:id,name') && e('2,发布2');   // 获取产品3的最新发布信息，并且发布日期在明天之前

r($releaseModel->statisticRecentReleases($productIdList, $today, $orderByList[0])) && p('2:id,name') && e('10,发布10'); // 获取产品1的最新发布信息，并且发布日期在今天之前按照id正序排序
r($releaseModel->statisticRecentReleases($productIdList, $today, $orderByList[1])) && p('3:id,name') && e('74,发布74'); // 获取产品3的最新发布信息，并且发布日期在今天之前按照产品倒序排序
