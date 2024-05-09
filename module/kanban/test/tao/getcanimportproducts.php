#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';

zenData('product')->gen(100);
zenData('project')->gen(100);
zenData('projectproduct')->gen(100);

su('admin');

/**

title=测试 kanbanTao->getCanImportProducts();
timeout=0
cid=1

- 获取可导入计划的产品数量 @101
- 获取可导入发布的产品数量 @101
- 获取可导入需求的产品数量 @101
- 获取可导入计划的第一个产品的名称 @所有产品
- 获取可导入计划的第二个产品的名称属性1 @正常产品1

*/
global $tester;
$tester->loadModel('kanban');

r(count($tester->kanban->getCanImportProducts('productplan'))) && p('')  && e('101');        // 获取可导入计划的产品数量
r(count($tester->kanban->getCanImportProducts('release')))     && p('')  && e('101');        // 获取可导入发布的产品数量
r(count($tester->kanban->getCanImportProducts('story')))       && p('')  && e('101');        // 获取可导入需求的产品数量
r($tester->kanban->getCanImportProducts('productplan'))        && p('0') && e('所有产品');  // 获取可导入计划的第一个产品的名称
r($tester->kanban->getCanImportProducts('productplan'))        && p('1') && e('正常产品1'); // 获取可导入计划的第二个产品的名称
