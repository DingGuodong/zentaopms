<?php
global $lang, $app;
$app->loadLang('sonarqube');

$config->repo->dtable = new stdclass();

$config->repo->dtable->fieldList['name']['name']  = 'name';
$config->repo->dtable->fieldList['name']['title'] = $lang->repo->name;
$config->repo->dtable->fieldList['name']['type']  = 'title';
$config->repo->dtable->fieldList['name']['width'] = '0.2';

$config->repo->dtable->fieldList['product']['name']     = 'productNames';
$config->repo->dtable->fieldList['product']['title']    = $lang->repo->product;
$config->repo->dtable->fieldList['product']['type']     = 'text';
$config->repo->dtable->fieldList['product']['sortType'] = true;
$config->repo->dtable->fieldList['product']['width']    = '136';

$config->repo->dtable->fieldList['project']['name']     = 'projectNames';
$config->repo->dtable->fieldList['project']['title']    = $lang->repo->projects;
$config->repo->dtable->fieldList['project']['type']     = 'text';
$config->repo->dtable->fieldList['project']['sortType'] = true;
$config->repo->dtable->fieldList['project']['width']    = '136';

$config->repo->dtable->fieldList['scm']['name']  = 'SCM';
$config->repo->dtable->fieldList['scm']['title'] = $lang->repo->type;
$config->repo->dtable->fieldList['scm']['type']  = 'scm';
$config->repo->dtable->fieldList['scm']['map']   = $lang->repo->scmList;
$config->repo->dtable->fieldList['scm']['group'] = 1;

$config->repo->dtable->fieldList['path']['name']  = 'codePath';
$config->repo->dtable->fieldList['path']['title'] = $lang->repo->path;
$config->repo->dtable->fieldList['path']['type']  = 'text';
$config->repo->dtable->fieldList['path']['width'] = '260';
$config->repo->dtable->fieldList['path']['group'] = 1;

$config->repo->dtable->fieldList['lastSubmit']['name']  = 'lastSubmitTime';
$config->repo->dtable->fieldList['lastSubmit']['title'] = $lang->repo->lastSubmitTime;
$config->repo->dtable->fieldList['lastSubmit']['type']  = 'datetime';
$config->repo->dtable->fieldList['lastSubmit']['width'] = '128';

$config->repo->dtable->fieldList['job']['name']  = 'job';
$config->repo->dtable->fieldList['job']['hidden'] = true;

$config->repo->dtable->fieldList['actions']['name']  = 'actions';
$config->repo->dtable->fieldList['actions']['title'] = $lang->actions;
$config->repo->dtable->fieldList['actions']['type']  = 'actions';
$config->repo->dtable->fieldList['actions']['width'] = '132';
$config->repo->dtable->fieldList['actions']['menu']  = array('execJob', 'reportView', 'edit', 'delete');

$config->repo->dtable->fieldList['actions']['list']['edit']['icon'] = 'edit';
$config->repo->dtable->fieldList['actions']['list']['edit']['hint'] = $lang->repo->edit;

$config->repo->dtable->fieldList['actions']['list']['execJob']['icon']        = 'sonarqube';
$config->repo->dtable->fieldList['actions']['list']['execJob']['hint']        = $lang->sonarqube->execJob;
$config->repo->dtable->fieldList['actions']['list']['execJob']['url']         = helper::createLink('sonarqube', 'execJob', "jobID={jobID}");
$config->repo->dtable->fieldList['actions']['list']['execJob']['data-toggle'] = 'modal';

$config->repo->dtable->fieldList['actions']['list']['reportView']['icon']        = 'audit';
$config->repo->dtable->fieldList['actions']['list']['reportView']['hint']        = $lang->sonarqube->reportView;
$config->repo->dtable->fieldList['actions']['list']['reportView']['url']         = helper::createLink('sonarqube', 'reportView', "jobID={jobID}");
$config->repo->dtable->fieldList['actions']['list']['reportView']['data-toggle'] = 'modal';

$config->repo->dtable->fieldList['actions']['list']['delete']['icon']        = 'trash';
$config->repo->dtable->fieldList['actions']['list']['delete']['hint']        = $lang->repo->delete;
$config->repo->dtable->fieldList['actions']['list']['delete']['data-toggle'] = 'modal';

$config->repo->repoDtable = new stdclass();

$config->repo->repoDtable->fieldList['name']['name']     = 'name';
$config->repo->repoDtable->fieldList['name']['title']    = $lang->repo->name;
$config->repo->repoDtable->fieldList['name']['minWidth'] = '160';
$config->repo->repoDtable->fieldList['name']['sortType'] = false;
$config->repo->repoDtable->fieldList['name']['type']     = 'shortTitle';
$config->repo->repoDtable->fieldList['name']['fixed']    = 'left';
$config->repo->repoDtable->fieldList['name']['hint']     = true;
$config->repo->repoDtable->fieldList['name']['checkbox'] = false;

$config->repo->repoDtable->fieldList['revision']['name']     = 'revision';
$config->repo->repoDtable->fieldList['revision']['title']    = $lang->repo->revisions;
$config->repo->repoDtable->fieldList['revision']['sortType'] = false;
$config->repo->repoDtable->fieldList['revision']['width']    = '90';
$config->repo->repoDtable->fieldList['revision']['hint']     = true;

$config->repo->repoDtable->fieldList['time']['name']     = 'date';
$config->repo->repoDtable->fieldList['time']['title']    = $lang->repo->time;
$config->repo->repoDtable->fieldList['time']['sortType'] = false;
$config->repo->repoDtable->fieldList['time']['type']     = 'date';
$config->repo->repoDtable->fieldList['time']['hint']     = true;

$config->repo->repoDtable->fieldList['committer']['name']     = 'account';
$config->repo->repoDtable->fieldList['committer']['title']    = $lang->repo->committer;
$config->repo->repoDtable->fieldList['committer']['sortType'] = false;
$config->repo->repoDtable->fieldList['committer']['type']     = 'user';
$config->repo->repoDtable->fieldList['committer']['hint']     = true;

$config->repo->repoDtable->fieldList['comment']['name']     = 'originalComment';
$config->repo->repoDtable->fieldList['comment']['title']    = $lang->repo->comment;
$config->repo->repoDtable->fieldList['comment']['sortType'] = false;
$config->repo->repoDtable->fieldList['comment']['type']     = 'text';
$config->repo->repoDtable->fieldList['comment']['hint']     = true;

$config->repo->commentDtable = new stdclass();

$config->repo->commentDtable->fieldList['id']['title']    = '';
$config->repo->commentDtable->fieldList['id']['name']     = '';
$config->repo->commentDtable->fieldList['id']['type']     = 'checkID';
$config->repo->commentDtable->fieldList['id']['sortType'] = false;
$config->repo->commentDtable->fieldList['id']['checkbox'] = true;
$config->repo->commentDtable->fieldList['id']['width']    = '40';

$config->repo->commentDtable->fieldList['revision']['name']     = 'revision';
$config->repo->commentDtable->fieldList['revision']['title']    = $lang->repo->revisions;
$config->repo->commentDtable->fieldList['revision']['sortType'] = false;
$config->repo->commentDtable->fieldList['revision']['type']     = 'title';
$config->repo->commentDtable->fieldList['revision']['width']    = '100';
$config->repo->commentDtable->fieldList['revision']['hint']     = true;

$config->repo->commentDtable->fieldList['commit']['name']     = 'commit';
$config->repo->commentDtable->fieldList['commit']['title']    = $lang->repo->commit;
$config->repo->commentDtable->fieldList['commit']['sortType'] = false;
$config->repo->commentDtable->fieldList['commit']['width']    = '40';
$config->repo->commentDtable->fieldList ['commit']['hint']    = true;

$config->repo->commentDtable->fieldList['time']      = $config->repo->repoDtable->fieldList['time'];
$config->repo->commentDtable->fieldList['committer'] = $config->repo->repoDtable->fieldList['committer'];
$config->repo->commentDtable->fieldList['comment']   = $config->repo->repoDtable->fieldList['comment'];

$config->repo->commentDtable->fieldList['comment']['name']   = 'originalComment';
$config->repo->commentDtable->fieldList['committer']['name'] = 'committer';

$config->repo->logDtable = new stdclass();

$config->repo->logDtable->fieldList['id']['hidden'] = true;

$config->repo->logDtable->fieldList['revision']['type']  = 'revision';
$config->repo->logDtable->fieldList['revision']['width'] = '100';
$config->repo->logDtable->fieldList['revision']['checkbox'] = true;
$config->repo->logDtable->fieldList['revision']['nestedToggle'] = false;

$config->repo->logDtable->fieldList['commit']['type']     = 'number';
$config->repo->logDtable->fieldList['commit']['width']    = '50';
$config->repo->logDtable->fieldList['commit']['sortType'] = false;

$config->repo->logDtable->fieldList['date']['name']     = 'time';
$config->repo->logDtable->fieldList['date']['type']     = 'datetime';
$config->repo->logDtable->fieldList['date']['sortType'] = false;

$config->repo->logDtable->fieldList['committer']['name'] = 'committer';

$config->repo->logDtable->fieldList['comment']['type']  = 'html';
$config->repo->logDtable->fieldList['comment']['width'] = '600';

$config->repo->blameDtable = new stdclass();

$config->repo->blameDtable->fieldList['revision']['type'] = 'revision';
$config->repo->blameDtable->fieldList['revision']['width'] = '120';

$config->repo->blameDtable->fieldList['commit']['type']     = 'number';
$config->repo->blameDtable->fieldList['commit']['sortType'] = false;
$config->repo->blameDtable->fieldList['commit']['width'] = '80';

$config->repo->blameDtable->fieldList['committer']['name'] = 'committer';
$config->repo->blameDtable->fieldList['committer']['width'] = '150';

$config->repo->blameDtable->fieldList['line']['type']     = 'number';
$config->repo->blameDtable->fieldList['line']['sortType'] = false;
$config->repo->blameDtable->fieldList['line']['width'] = '80';

$config->repo->blameDtable->fieldList['content']['title'] = $lang->repo->code;
$config->repo->blameDtable->fieldList['content']['type']  = 'html';
$config->repo->blameDtable->fieldList['content']['width'] = '700';

$app->loadLang('story');
$app->loadModuleConfig('story');

$config->repo->storyDtable = new stdclass();
$config->repo->storyDtable->fieldList = array();
$config->repo->storyDtable->fieldList['id']['name']     = 'id';
$config->repo->storyDtable->fieldList['id']['title']    = $lang->idAB;
$config->repo->storyDtable->fieldList['id']['type']     = 'id';
$config->repo->storyDtable->fieldList['id']['checkbox'] = true;

$config->repo->storyDtable->fieldList['pri']        = $config->story->dtable->fieldList['pri'];
$config->repo->storyDtable->fieldList['plan']       = $config->story->dtable->fieldList['plan'];
$config->repo->storyDtable->fieldList['module']     = $config->story->dtable->fieldList['module'];
$config->repo->storyDtable->fieldList['title']      = $config->story->dtable->fieldList['title'];
$config->repo->storyDtable->fieldList['openedBy']   = $config->story->dtable->fieldList['openedBy'];
$config->repo->storyDtable->fieldList['assignedTo'] = $config->story->dtable->fieldList['assignedTo'];
$config->repo->storyDtable->fieldList['estimate']   = $config->story->dtable->fieldList['estimate'];
$config->repo->storyDtable->fieldList['status']     = $config->story->dtable->fieldList['status'];
$config->repo->storyDtable->fieldList['stage']      = $config->story->dtable->fieldList['stage'];

$config->repo->storyDtable->fieldList['title']['nestedToggle'] = false;
$config->repo->storyDtable->fieldList['title']['type']         = 'text';
$config->repo->storyDtable->fieldList['title']['fixed']        = '';
$config->repo->storyDtable->fieldList['title']['minWidth']     = '200';
$config->repo->storyDtable->fieldList['title']['sortType']     = true;
$config->repo->storyDtable->fieldList['id']['fixed']           = '';
$config->repo->storyDtable->fieldList['pri']['fixed']          = '';
$config->repo->storyDtable->fieldList['pri']['sortType']       = true;
$config->repo->storyDtable->fieldList['plan']['name']          = 'planTitle';
$config->repo->storyDtable->fieldList['plan']['width']         = 'auto';
$config->repo->storyDtable->fieldList['assignedTo']['type']    = 'user';

$app->loadLang('bug');
$app->loadModuleConfig('bug');

$config->repo->bugDtable = new stdclass();
$config->repo->bugDtable->fieldList = array();
$config->repo->bugDtable->fieldList['id']['name']     = 'id';
$config->repo->bugDtable->fieldList['id']['title']    = $lang->idAB;
$config->repo->bugDtable->fieldList['id']['type']     = 'id';
$config->repo->bugDtable->fieldList['id']['checkbox'] = true;

$config->repo->bugDtable->fieldList['pri']        = $config->bug->dtable->fieldList['pri'];
$config->repo->bugDtable->fieldList['title']      = $config->bug->dtable->fieldList['title'];
$config->repo->bugDtable->fieldList['openedBy']   = $config->bug->dtable->fieldList['openedBy'];
$config->repo->bugDtable->fieldList['assignedTo'] = $config->bug->dtable->fieldList['assignedTo'];
$config->repo->bugDtable->fieldList['status']     = $config->bug->dtable->fieldList['status'];

$config->repo->bugDtable->fieldList['pri']['fixed'] = 'left';

$app->loadLang('task');
$app->loadModuleConfig('task');

$config->repo->taskDtable = new stdclass();
$config->repo->taskDtable->fieldList = array();
$config->repo->taskDtable->fieldList['id']['name']     = 'id';
$config->repo->taskDtable->fieldList['id']['title']    = $lang->idAB;
$config->repo->taskDtable->fieldList['id']['type']     = 'id';
$config->repo->taskDtable->fieldList['id']['checkbox'] = true;

$config->repo->taskDtable->fieldList['pri']        = $config->task->dtable->fieldList['pri'];
$config->repo->taskDtable->fieldList['name']       = $config->task->dtable->fieldList['name'];
$config->repo->taskDtable->fieldList['finishedBy'] = $config->task->dtable->fieldList['finishedBy'];
$config->repo->taskDtable->fieldList['assignedTo'] = $config->task->dtable->fieldList['assignedTo'];
$config->repo->taskDtable->fieldList['status']     = $config->task->dtable->fieldList['status'];

$config->repo->taskDtable->fieldList['pri']['fixed'] = 'left';
