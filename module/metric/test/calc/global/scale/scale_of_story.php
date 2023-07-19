#!/usr/bin/env php
<?php
include dirname(__FILE__, 7) . '/test/lib/init.php';
include dirname(__FILE__, 4) . '/calc.class.php';

zdTable('story')->config('story_stage_closedreason', $useCommon = true, $levels = 4)->gen(100);

$metric = new metricTest();
$calc   = $metric->calcMetric(__FILE__);

/**

title=scale_of_story
timeout=0
cid=1

- 测试按全局统计的所有研发需求规模数 @280

*/

r($calc->getResult()) && p('') && e('280'); // 测试按全局统计的所有研发需求规模数