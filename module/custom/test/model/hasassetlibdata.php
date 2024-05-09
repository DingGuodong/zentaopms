#!/usr/bin/env php
<?php
/**

title=测试 customModel->hasAssetlibData();
timeout=0
cid=1

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/custom.unittest.class.php';

zenData('assetlib')->gen(0);
zenData('user')->gen(5);
su('admin');

$editionList = array('open', 'ipd', 'max');

$customTester = new customTest();
r($customTester->hasAssetlibDataTest($editionList[0])) && p() && e('0'); // 测试开源版中无资产库数据
r($customTester->hasAssetlibDataTest($editionList[1])) && p() && e('0'); // 测试ipd版中无资产库数据
r($customTester->hasAssetlibDataTest($editionList[2])) && p() && e('0'); // 测试旗舰版中无资产库数据

$assetlibTable = zenData('assetlib');
$assetlibTable->deleted->range('0');
$assetlibTable->gen(5);
r($customTester->hasAssetlibDataTest($editionList[0])) && p() && e('0'); // 测试开源版中有资产库数据
r($customTester->hasAssetlibDataTest($editionList[1])) && p() && e('5'); // 测试ipd版中有资产库数据
r($customTester->hasAssetlibDataTest($editionList[2])) && p() && e('5'); // 测试旗舰版中有资产库数据
