<?php
global $app, $lang;
$app->loadLang('testtask');

$config->testcase->menu       = array(array('confirmStoryChange'), array('review', 'runCase', 'runResult', 'edit', 'createBug', 'create', 'showScript'));
$config->testcase->actionList = array();
$config->testcase->actionList['confirmStoryChange']['icon'] = 'ok';
$config->testcase->actionList['confirmStoryChange']['text'] = $lang->confirm;
$config->testcase->actionList['confirmStoryChange']['hint'] = $lang->confirm;
$config->testcase->actionList['confirmStoryChange']['url']  = array('module' => 'testcase', 'method' => 'confirmStoryChange', 'params' => 'caseID={id}');

$config->testcase->actionList['runCase']['icon']       = 'play';
$config->testcase->actionList['runCase']['text']       = $lang->testtask->runCase;
$config->testcase->actionList['runCase']['hint']       = $lang->testtask->runCase;
$config->testcase->actionList['runCase']['url']        = array('module' => 'testtask', 'method' => 'runCase', 'params' => 'runID=0&caseID={id}&version={version}');
$config->testcase->actionList['runCase']['data-width'] = 0.95;

$config->testcase->actionList['runResult']['icon']       = 'list-alt';
$config->testcase->actionList['runResult']['text']       = $lang->testtask->results;
$config->testcase->actionList['runResult']['hint']       = $lang->testtask->results;
$config->testcase->actionList['runResult']['url']        = array('module' => 'testtask', 'method' => 'results', 'params' => 'runID=0&caseID={id}');
$config->testcase->actionList['runResult']['data-width'] = 0.95;

$config->testcase->actionList['edit']['icon'] = 'edit';
$config->testcase->actionList['edit']['text'] = $lang->testcase->edit;
$config->testcase->actionList['edit']['hint'] = $lang->testcase->edit;
$config->testcase->actionList['edit']['url']  = array('module' => 'testcase', 'method' => 'edit', 'params' => 'caseID={id}&comment=false&executionID=%executionID%');

$config->testcase->actionList['review']['icon'] = 'glasses';
$config->testcase->actionList['review']['text'] = $lang->testcase->review;
$config->testcase->actionList['review']['hint'] = $lang->testcase->review;
$config->testcase->actionList['review']['url']  = array('module' => 'testcase', 'method' => 'review', 'params' => 'caseID={id}');

$config->testcase->actionList['createBug']['icon'] = 'bug';
$config->testcase->actionList['createBug']['text'] = $lang->testcase->createBug;
$config->testcase->actionList['createBug']['hint'] = $lang->testcase->createBug;
$config->testcase->actionList['createBug']['url']  = array('module' => 'testcase', 'method' => 'createBug', 'params' => 'product={product}&branch={branch}&extra=caseID={id},version={version},runID=');

$config->testcase->actionList['create']['icon'] = 'copy';
$config->testcase->actionList['create']['text'] = $lang->testcase->copy;
$config->testcase->actionList['create']['hint'] = $lang->testcase->copy;
$config->testcase->actionList['create']['url']  = array('module' => 'testcase', 'method' => 'create', 'params' => 'productID={product}&branch={branch}&moduleID={module}&from=testcase&param={id}');

$config->testcase->actionList['unlinkCase']['icon'] = 'unlink';
$config->testcase->actionList['unlinkCase']['text'] = $lang->testtask->unlinkCase;
$config->testcase->actionList['unlinkCase']['hint'] = $lang->testtask->unlinkCase;
$config->testcase->actionList['unlinkCase']['url']  = array('module' => 'testtask', 'method' => 'unlinkCase', 'params' => 'caseID={id}&confirm=yes');

$config->testcase->actionList['showScript']['icon'] = 'file-code';
$config->testcase->actionList['showScript']['text'] = $lang->testcase->showScript;
$config->testcase->actionList['showScript']['hint'] = $lang->testcase->showScript;
$config->testcase->actionList['showScript']['url']  = array('module' => 'testcase', 'method' => 'showScript', 'params' => 'caseID={id}');

$config->testcase->actionList['editScene']['icon'] = 'edit';
$config->testcase->actionList['editScene']['text'] = $lang->testcase->editScene;
$config->testcase->actionList['editScene']['hint'] = $lang->testcase->editScene;
$config->testcase->actionList['editScene']['url']  = array('module' => 'testcase', 'method' => 'editScene', 'params' => 'sceneID={id}&executionID={session_execution}');

$config->testcase->actionList['deleteScene']['icon'] = 'trash';
$config->testcase->actionList['deleteScene']['text'] = $lang->testcase->deleteScene;
$config->testcase->actionList['deleteScene']['hint'] = $lang->testcase->deleteScene;
$config->testcase->actionList['deleteScene']['url']  = array('module' => 'testcase', 'method' => 'deleteScene', 'params' => 'sceneID={id}');

$config->scene = new stdclass();
$config->scene->menu = array('editScene', 'deleteScene');
