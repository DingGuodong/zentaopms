<?php
global $lang, $app;
$app->loadLang('testcase');
$app->loadModuleConfig('testcase');

$config->block->case = new stdclass();
$config->block->case->dtable = new stdclass();
$config->block->case->dtable->fieldList = array();
$config->block->case->dtable->fieldList['id']            = array('name' => 'id',    'title' => $lang->idAB,            'type' => 'id', 'fixed' => false);
$config->block->case->dtable->fieldList['title']         = array('name' => 'title', 'title' => $lang->testcase->title, 'type' => 'title', 'link' => array('module' => 'testcase', 'method' => 'view', 'params' => "caseID={id}"), 'fixed' => false, 'width' => '50%');
$config->block->case->dtable->fieldList['pri']           = $config->testcase->dtable->fieldList['pri'];
$config->block->case->dtable->fieldList['status']        = $config->testcase->dtable->fieldList['status'];
$config->block->case->dtable->fieldList['lastRunDate']   = $config->testcase->dtable->fieldList['lastRunDate'];
$config->block->case->dtable->fieldList['lastRunResult'] = $config->testcase->dtable->fieldList['lastRunResult'];

$config->block->case->dtable->fieldList['pri']['name']           = 'pri';
$config->block->case->dtable->fieldList['pri']['title']          = $lang->priAB;
$config->block->case->dtable->fieldList['status']['name']        = 'status';
$config->block->case->dtable->fieldList['status']['title']       = $lang->statusAB;
$config->block->case->dtable->fieldList['lastRunDate']['name']   = 'lastRunDate';
$config->block->case->dtable->fieldList['lastRunDate']['type']   = 'date';
$config->block->case->dtable->fieldList['lastRunResult']['name'] = 'lastRunResult';

unset($config->block->case->dtable->fieldList['pri']['group']);
unset($config->block->case->dtable->fieldList['status']['group']);
unset($config->block->case->dtable->fieldList['lastRunDate']['group']);
unset($config->block->case->dtable->fieldList['lastRunResult']['group']);

$config->block->case->dtable->short = new stdclass();
$config->block->case->dtable->short->fieldList['id']            = $config->block->case->dtable->fieldList['id'];
$config->block->case->dtable->short->fieldList['title']         = $config->block->case->dtable->fieldList['title'];
$config->block->case->dtable->short->fieldList['lastRunResult'] = $config->block->case->dtable->fieldList['lastRunDate'];
