#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('project')->config('project_close', $useCommon = true, $levels = 4)->gen(10);
zdTable('project')->config('execution', $useCommon = true, $levels = 4)->gen(1000, false);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=count_annual_closed_execution_in_project
cid=1
pid=1

*/

r(count($calc->getResult())) && p('') && e('32'); // 测试分组数。

r($calc->getResult(array('project' => '10', 'year' => '2011'))) && p('0:value') && e('10'); // 测试项目2。
