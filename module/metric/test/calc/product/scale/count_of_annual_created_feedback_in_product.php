#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('product')->config('product', $useCommon = true, $levels = 4)->gen(10);
zdTable('feedback')->config('feedback', $useCommon = true, $levels = 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=count_of_annual_created_feedback_in_product
cid=1
pid=1

*/

r(count($calc->getResult()))                                     && p('')        && e('55'); // 测试按产品的年度新增反馈分组数。
r($calc->getResult(array('product' => '9', 'year' => '2015')))   && p('0:value') && e('5');  // 测试2015年产品9新增的反馈数。
r($calc->getResult(array('product' => '9', 'year' => '2016')))   && p('0:value') && e('3');  // 测试2016年产品9新增的反馈数。
r($calc->getResult(array('product' => '8', 'year' => '2022')))   && p('')        && e('0');  // 测试已删除产品8新增的反馈数。
r($calc->getResult(array('product' => '999', 'year' => '2021'))) && p('')        && e('0');  // 测试不存在的产品的反馈数。
