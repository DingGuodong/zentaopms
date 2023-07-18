#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('product')->config('product', $useCommon = true, $levels = 4)->gen(10);
zdTable('feedback')->config('feedback', $useCommon = true, $levels = 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=count_of_annual_closed_feedback_in_product
cid=1
pid=1

*/

r(count($calc->getResult()))                                    && p('')        && e('50'); // 测试按产品的年度关闭反馈分组数。
r($calc->getResult(array('product' => '5',  'year' => '2019'))) && p('0:value') && e('4');  // 测试2019年产品5关闭的反馈数。
r($calc->getResult(array('product' => '5',  'year' => '2020'))) && p('0:value') && e('3');  // 测试2020年产品5关闭的反馈数。
r($calc->getResult(array('product' => '5',  'year' => '2005'))) && p('')        && e('0');  // 测试2005年产品5关闭的反馈数。

r($calc->getResult(array('product' => '4',  'year' => '2020')))  && p('')        && e('0'); // 测试已删除产品4关闭的反馈数。
r($calc->getResult(array('product' => '999', 'year' => '2021'))) && p('')        && e('0'); // 测试不存在的产品的反馈数。
