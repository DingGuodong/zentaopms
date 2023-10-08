#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('product')->config('product', $useCommon = true, $levels = 4)->gen(10);
zdTable('bug')->config('bug_resolution_status', $useCommon = true, $levels = 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=count_of_monthly_closed_bug_in_product
cid=1
pid=1

*/

r(count($calc->getResult())) && p('') && e('95'); // 测试分组数。

r($calc->getResult(array('product' => '9', 'year' => '2015', 'month' => '11'))) && p('0:value') && e('3'); // 测试2015.11。
r($calc->getResult(array('product' => '9', 'year' => '2015', 'month' => '12'))) && p('0:value') && e('4'); // 测试2015.12。
r($calc->getResult(array('product' => '9', 'year' => '2016', 'month' => '02'))) && p('0:value') && e('3'); // 测试2016.02。
