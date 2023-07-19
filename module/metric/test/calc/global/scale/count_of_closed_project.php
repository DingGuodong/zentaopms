#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('project')->config('project_status', $useCommon = true, $levels = 4)->gen(200);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=count_of_closed_project
timeout=0
cid=1

*/

r($calc->getResult()) && p('') && e('25'); // 测试全局范围内关闭的项目数
