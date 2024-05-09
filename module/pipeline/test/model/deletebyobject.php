#!/usr/bin/env php
<?php

/**

title=测试 pipelineModel->deleteByObject();
timeout=0
cid=1

- 测试删除id为0的流水线 @0
- 测试删除id为1的流水线属性deleted @0
- 测试删除id为2的流水线属性deleted @0
- 测试删除id为5的流水线属性deleted @1

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/pipeline.unittest.class.php';

zenData('user')->gen(5);
zenData('pipeline')->loadYaml('pipeline')->gen(5);
zenData('repo')->gen(1);

$jobTable = zenData('job');
$jobTable->server->range('2');
$jobTable->gen(1);

$idList = array(0, 1, 2, 5);
$types  = array('gitlab', 'sonarqube', 'jenkins');

$pipelineTester = new pipelineTest();
r($pipelineTester->deleteByObjectTest($idList[0], $types[0])) && p()          && e('0'); // 测试删除id为0的流水线
r($pipelineTester->deleteByObjectTest($idList[1], $types[0])) && p('deleted') && e('0'); // 测试删除id为1的流水线
r($pipelineTester->deleteByObjectTest($idList[2], $types[1])) && p('deleted') && e('0'); // 测试删除id为2的流水线
r($pipelineTester->deleteByObjectTest($idList[3], $types[2])) && p('deleted') && e('1'); // 测试删除id为5的流水线