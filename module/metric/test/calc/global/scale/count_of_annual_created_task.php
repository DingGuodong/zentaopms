#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('project')->config('project_close', $useCommon = true, $levels = 4)->gen(10);
zdTable('project')->config('execution', $useCommon = true, $levels = 4)->gen(20, false);
zdTable('task')->config('task', $useCommon = true, $levels = 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=count_of_annual_created_task
timeout=0
cid=1

- 测试分组数。 @10

- 测试2011年度新增任务数。第0条的value属性 @5

- 测试2012年度新增任务数。第0条的value属性 @26

- 测试2017年度新增任务数。第0条的value属性 @14

- 测试2018年度新增任务数。第0条的value属性 @6

- 测试不存在的年度新增任务数。 @0

*/

r(count($calc->getResult())) && p('') && e('10'); // 测试分组数。

r($calc->getResult(array('year' => '2011'))) && p('0:value') && e('5');  // 测试2011年度新增任务数。
r($calc->getResult(array('year' => '2012'))) && p('0:value') && e('26'); // 测试2012年度新增任务数。
r($calc->getResult(array('year' => '2017'))) && p('0:value') && e('14'); // 测试2017年度新增任务数。
r($calc->getResult(array('year' => '2018'))) && p('0:value') && e('6');  // 测试2018年度新增任务数。
r($calc->getResult(array('year' => '2022'))) && p('')        && e('0');  // 测试不存在的年度新增任务数。