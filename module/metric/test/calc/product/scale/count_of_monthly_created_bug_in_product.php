#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('product')->config('product', $useCommon = true, $levels = 4)->gen(10);
zdTable('bug')->config('bug_resolution_status', $useCommon = true, $levels = 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=count_of_monthly_created_bug_in_product
cid=1
pid=1

*/

r(count($calc->getResult())) && p('') && e('93'); // 测试分组数。

r($calc->getResult(array('product' => '9', 'year' => '2012', 'month' => '01'))) && p('0:value') && e('4'); // 测试2012.01。
r($calc->getResult(array('product' => '9', 'year' => '2012', 'month' => '02'))) && p('0:value') && e('2'); // 测试2012.02。
r($calc->getResult(array('product' => '9', 'year' => '2012', 'month' => '03'))) && p('0:value') && e('4'); // 测试2012.03。
