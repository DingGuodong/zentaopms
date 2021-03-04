<?php
$config->project = new stdclass();

$config->project->editor = new stdclass();
$config->project->editor->pgmcreate   = array('id' => 'desc',    'tools' => 'simpleTools');
$config->project->editor->pgmedit     = array('id' => 'desc',    'tools' => 'simpleTools');
$config->project->editor->pgmclose    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->pgmstart    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->pgmactivate = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->pgmfinish   = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->pgmsuspend  = array('id' => 'comment', 'tools' => 'simpleTools');

$config->project->editor->prjcreate   = array('id' => 'desc', 'tools' => 'simpleTools');
$config->project->editor->prjedit     = array('id' => 'desc', 'tools' => 'simpleTools');
$config->project->editor->prjclose    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->prjsuspend  = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->prjstart    = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->prjactivate = array('id' => 'comment', 'tools' => 'simpleTools');
$config->project->editor->prjview     = array('id' => 'comment', 'tools' => 'simpleTools');

$config->project->list = new stdclass();
$config->project->list->exportFields = 'id,name,code,template,product,status,begin,end,budget,PM,end,desc';

$config->project->create = new stdclass();
$config->project->edit   = new stdclass();
$config->project->create->requiredFields = 'name,code,begin,end';
$config->project->edit->requiredFields   = 'name,code,begin,end';

$config->project->sortFields         = new stdclass();
$config->project->sortFields->id     = 'id';
$config->project->sortFields->begin  = 'begin';
$config->project->sortFields->end    = 'end';
$config->project->sortFields->status = 'status';
$config->project->sortFields->budget = 'budget';

global $lang;
$config->project->datatable = new stdclass();
$config->project->datatable->defaultField = array('id', 'name', 'PM', 'status', 'begin', 'end', 'budget', 'teamCount','estimate','consume', 'progress', 'actions');

$config->project->datatable->fieldList['id']['title']    = 'ID';
$config->project->datatable->fieldList['id']['fixed']    = 'left';
$config->project->datatable->fieldList['id']['width']    = '60';
$config->project->datatable->fieldList['id']['required'] = 'yes';

$config->project->datatable->fieldList['name']['title']    = 'name';
$config->project->datatable->fieldList['name']['fixed']    = 'left';
$config->project->datatable->fieldList['name']['width']    = 'auto';
$config->project->datatable->fieldList['name']['required'] = 'yes';
$config->project->datatable->fieldList['name']['sort']     = 'no';

$config->project->datatable->fieldList['PM']['title']    = 'PM';
$config->project->datatable->fieldList['PM']['fixed']    = 'no';
$config->project->datatable->fieldList['PM']['width']    = '80';
$config->project->datatable->fieldList['PM']['required'] = 'yes';
$config->project->datatable->fieldList['PM']['sort']     = 'no';

$config->project->datatable->fieldList['status']['title']    = 'status';
$config->project->datatable->fieldList['status']['fixed']    = 'left';
$config->project->datatable->fieldList['status']['width']    = '80';
$config->project->datatable->fieldList['status']['required'] = 'no';
$config->project->datatable->fieldList['status']['sort']     = 'yes';

$config->project->datatable->fieldList['begin']['title']    = 'begin';
$config->project->datatable->fieldList['begin']['fixed']    = 'no';
$config->project->datatable->fieldList['begin']['width']    = '100';
$config->project->datatable->fieldList['begin']['required'] = 'no';

$config->project->datatable->fieldList['end']['title']    = 'end';
$config->project->datatable->fieldList['end']['fixed']    = 'no';
$config->project->datatable->fieldList['end']['width']    = '100';
$config->project->datatable->fieldList['end']['required'] = 'no';

$config->project->datatable->fieldList['budget']['title']    = 'budget';
$config->project->datatable->fieldList['budget']['fixed']    = 'no';
$config->project->datatable->fieldList['budget']['width']    = '80';
$config->project->datatable->fieldList['budget']['required'] = 'yes';

$config->project->datatable->fieldList['teamCount']['title']    = 'teamCount';
$config->project->datatable->fieldList['teamCount']['fixed']    = 'no';
$config->project->datatable->fieldList['teamCount']['width']    = '40';
$config->project->datatable->fieldList['teamCount']['required'] = 'no';
$config->project->datatable->fieldList['teamCount']['sort']     = 'no';

$config->project->datatable->fieldList['estimate']['title']    = 'estimate';
$config->project->datatable->fieldList['estimate']['fixed']    = 'no';
$config->project->datatable->fieldList['estimate']['width']    = '60';
$config->project->datatable->fieldList['estimate']['required'] = 'no';
$config->project->datatable->fieldList['estimate']['sort']     = 'no';

$config->project->datatable->fieldList['consume']['title']    = 'consume';
$config->project->datatable->fieldList['consume']['fixed']    = 'no';
$config->project->datatable->fieldList['consume']['width']    = '60';
$config->project->datatable->fieldList['consume']['required'] = 'no';
$config->project->datatable->fieldList['consume']['sort']     = 'no';

$config->project->datatable->fieldList['progress']['title']    = 'progress';
$config->project->datatable->fieldList['progress']['fixed']    = 'right';
$config->project->datatable->fieldList['progress']['width']    = '60';
$config->project->datatable->fieldList['progress']['required'] = 'no';
$config->project->datatable->fieldList['progress']['sort']     = 'no';

$config->project->datatable->fieldList['actions']['title']    = 'actions';
$config->project->datatable->fieldList['actions']['fixed']    = 'right';
$config->project->datatable->fieldList['actions']['width']    = '180';
$config->project->datatable->fieldList['actions']['required'] = 'yes';
