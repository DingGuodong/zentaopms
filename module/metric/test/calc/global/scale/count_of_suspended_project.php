#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('project')->config('project_status', $useCommon = true, $levels = 4)->gen(200);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=count_of_suspended_project
timeout=0
cid=1

*/

r($calc->getResult()) && p('') && e('25');
