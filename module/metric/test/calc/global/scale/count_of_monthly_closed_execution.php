#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('project')->config('project_type', $useCommon = true, $levels = 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=count_of_monthly_closed_execution
cid=1
pid=1

*/

r(count($calc->getResult())) && p('') && e('103'); // 测试分组数。

r($calc->getResult(array('year' => '2012', 'month' => '01'))) && p('0:value') && e('3'); // 测试2012-01新增的执行数。
r($calc->getResult(array('year' => '2018', 'month' => '03'))) && p('0:value') && e('2'); // 测试2018-03新增的执行数。
r($calc->getResult(array('year' => '9999', 'month' => '01'))) && p('')        && e('0'); // 测试不存在年份的新增执行数。
