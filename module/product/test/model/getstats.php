#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/product.class.php';

/**
title=获取产品列表中每个产品的统计信息 productModel->getStats();
cid=1
pid=1
 */

zdTable('product')->gen(50);
zdTable('story')->gen(50);
zdTable('productplan')->gen(50);
zdTable('release')->gen(50);
zdTable('build')->gen(50);
zdTable('case')->gen(50);
zdTable('project')->gen(50);
zdTable('projectproduct')->gen(50);
zdTable('bug')->gen(50);
zdTable('doc')->gen(50);

$product = new productTest('admin');

$productIdList = array(1,2);

r($product->getStatsTest($productIdList)) && p('1:name')         && e('正常产品1'); // 获取第1个产品的名称
r($product->getStatsTest($productIdList)) && p('1:plans')        && e('0');         // 获取第1个产品的计划数
r($product->getStatsTest($productIdList)) && p('1:releases')     && e('25');        // 获取第1个产品的发布数
r($product->getStatsTest($productIdList)) && p('1:bugs')         && e('3');         // 获取第1个产品的bug数
r($product->getStatsTest($productIdList)) && p('1:unResolved')   && e('3');         // 获取第1个产品的未解决bug数
r($product->getStatsTest($productIdList)) && p('1:closedBugs')   && e('0');         // 获取第1个产品的关闭bug数
r($product->getStatsTest($productIdList)) && p('1:fixedBugs')    && e('0');         // 获取第1个产品的已解决bug数
r($product->getStatsTest($productIdList)) && p('1:thisWeekBugs') && e('0');         // 获取第1个产品的本周bug数
r($product->getStatsTest($productIdList)) && p('1:assignToNull') && e('0');         // 获取第1个产品的未指派bug数
r($product->getStatsTest($productIdList)) && p('1:progress')     && e('16.7');      // 获取第1个产品的完成需求的进度

r($product->getStatsTest($productIdList)) && p('2:name')         && e('正常产品2'); // 获取第2个产品的名称
r($product->getStatsTest($productIdList)) && p('2:plans')        && e('0');         // 获取第2个产品的计划数
r($product->getStatsTest($productIdList)) && p('2:releases')     && e('10');        // 获取第2个产品的发布数
r($product->getStatsTest($productIdList)) && p('2:bugs')         && e('3');         // 获取第2个产品的bug数
r($product->getStatsTest($productIdList)) && p('2:unResolved')   && e('3');         // 获取第2个产品的未解决bug数
r($product->getStatsTest($productIdList)) && p('2:closedBugs')   && e('0');         // 获取第2个产品的关闭bug数
r($product->getStatsTest($productIdList)) && p('2:fixedBugs')    && e('0');         // 获取第2个产品的已解决bug数
r($product->getStatsTest($productIdList)) && p('2:thisWeekBugs') && e('0');         // 获取第2个产品的本周bug数
r($product->getStatsTest($productIdList)) && p('2:assignToNull') && e('0');         // 获取第2个产品的未指派bug数
r($product->getStatsTest($productIdList)) && p('2:progress')     && e('16.7');      // 获取第2个产品的完成需求的进度

