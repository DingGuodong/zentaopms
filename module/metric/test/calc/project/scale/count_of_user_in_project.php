#!/usr/bin/env php
<?php

/**

title=count_of_user_in_project
timeout=0
cid=1

- 测试分组数。 @6
- 测试分组数。第0条的value属性 @14

*/
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/lib/calc.unittest.class.php';

zendata('project')->loadYaml('project_close', true, 4)->gen(10);
zendata('project')->loadYaml('execution', true, 4)->gen(200, false);
zendata('team')->loadYaml('team', true, 4)->gen(2000);
zendata('user')->loadYaml('user', true, 4)->gen(41);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

r(count($calc->getResult())) && p('') && e('6'); // 测试分组数。

r($calc->getResult()) && p('0:value') && e('14'); // 测试分组数。