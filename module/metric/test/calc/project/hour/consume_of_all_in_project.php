#!/usr/bin/env php
<?php

/**

title=consume_of_all_in_project
timeout=0
cid=1

- 测试分组数。 @5
- 测试项目7。第0条的value属性 @88

*/
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zendata('project')->loadYaml('project', true, 4)->gen(10);
zendata('project')->loadYaml('sprint', true, 4)->gen(40, false);
zendata('effort')->loadYaml('effort', true, 4)->gen(1000);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

r(count($calc->getResult())) && p('') && e('5'); // 测试分组数。

r($calc->getResult(array('project' => '7'))) && p('0:value') && e('88'); // 测试项目7。