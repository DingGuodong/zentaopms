#!/usr/bin/env php
<?php
/**

title=测试 customModel->hasScrumIssueData();
timeout=0
cid=1

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/custom.class.php';

$projectTable = zdTable('project');
$projectTable->id->range('1-5');
$projectTable->model->range('scrum');
$projectTable->gen(5);

zdTable('issue')->gen(0);
zdTable('user')->gen(5);
su('admin');

$editionList = array('open', 'ipd', 'max');

$customTester = new customTest();
r($customTester->hasScrumIssueDataTest($editionList[0])) && p() && e('0'); // 测试开源版中无问题数据
r($customTester->hasScrumIssueDataTest($editionList[1])) && p() && e('0'); // 测试ipd版中无问题数据
r($customTester->hasScrumIssueDataTest($editionList[2])) && p() && e('0'); // 测试旗舰版中无问题数据

$issueTable = zdTable('issue');
$issueTable->deleted->range('0');
$issueTable->project->range('1-5');
$issueTable->gen(5);
r($customTester->hasScrumIssueDataTest($editionList[0])) && p() && e('0'); // 测试开源版中有问题数据
r($customTester->hasScrumIssueDataTest($editionList[1])) && p() && e('5'); // 测试ipd版中有问题数据
r($customTester->hasScrumIssueDataTest($editionList[2])) && p() && e('5'); // 测试旗舰版中有问题数据
