#!/usr/bin/env php
<?php
declare(strict_types=1);

include dirname(__FILE__, 5). '/test/lib/init.php';
su('admin');

/**

title=测试 loadModel->getStageListBy()
timeout=0
cid=1

- 获取id为3下的阶段名称属性name @阶段a子1子1

- 获取的不存的id和项目产品id 的阶段信息 @0

- 获取id为1下的阶段条数 @2

*/

zdTable('project')->config('project')->gen(5);
zdTable('projectproduct')->config('projectproduct')->gen(5);

global $tester;
$tester->loadModel('programplan')->programplanTao;

$browseType = 'parent';
$IDList = array(1, 2, 3, 4, 5, 100);

$result = $tester->programplan->getStageList($IDList[2], 2, $browseType);
r($result[4]) && p('name') && e('阶段a子1子1'); // 获取id为3下的阶段名称

$result = $tester->programplan->getStageList($IDList[5], 100, $browseType);
r($result) && p() && e('0'); // 获取的不存的id和项目产品id 的阶段信息

$result = $tester->programplan->getStageList($IDList[0], 2, $browseType);
r(count($result)) && p() && e('2'); // 获取id为1下的阶段条数
