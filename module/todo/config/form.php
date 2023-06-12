<?php
declare(strict_types=1);

$config->todo->create->form = array();
$config->todo->create->form['type']         = array('required' => true,  'type' => 'string');
$config->todo->create->form['name']         = array('required' => true,  'type' => 'string');
$config->todo->create->form['status']       = array('required' => true,  'type' => 'string');
$config->todo->create->form['pri']          = array('required' => true,  'type' => 'int');
$config->todo->create->form['date']         = array('required' => false, 'type' => 'string',  'default' => helper::today());
$config->todo->create->form['begin']        = array('required' => false, 'type' => 'int',     'default' => 2400);
$config->todo->create->form['end']          = array('required' => false, 'type' => 'int',     'default' => 2400);
$config->todo->create->form['private']      = array('required' => false, 'type' => 'int',     'default' => 0);
$config->todo->create->form['assignedDate'] = array('required' => false, 'type' => 'string',  'default' => helper::now());
$config->todo->create->form['assignedTo']   = array('required' => false, 'type' => 'string',  'default' => '');
$config->todo->create->form['assignedBy']   = array('required' => false, 'type' => 'string',  'default' => '');
$config->todo->create->form['vision']       = array('required' => false, 'type' => 'string',  'default' => $this->config->vision);
$config->todo->create->form['objectID']     = array('required' => false, 'type' => 'int',     'default' => 0);
$config->todo->create->form['desc']         = array('required' => false, 'type' => 'string',  'default' => '');
$config->todo->create->form['cycle']        = array('required' => false, 'type' => 'int',     'default' => 0);
$config->todo->create->form['uid']          = array('required' => false, 'type' => 'string',  'default' => '');
$config->todo->create->form['config']       = array('required' => false, 'type' => 'array',   'default' => array());

$config->todo->batchCreate = new stdclass();
$config->todo->batchCreate->form = array();
$config->todo->batchCreate->form['types']       = array('required' => false, 'type' => 'array');
$config->todo->batchCreate->form['pris']        = array('required' => false, 'type' => 'array');
$config->todo->batchCreate->form['names']       = array('required' => false, 'type' => 'array');
$config->todo->batchCreate->form['descs']       = array('required' => false, 'type' => 'array');
$config->todo->batchCreate->form['assignedTos'] = array('required' => false, 'type' => 'array');
$config->todo->batchCreate->form['begins']      = array('required' => false, 'type' => 'array');
$config->todo->batchCreate->form['ends']        = array('required' => false, 'type' => 'array');
$config->todo->batchCreate->form['switchTime']  = array('required' => false, 'type' => 'array');
$config->todo->batchCreate->form['date']        = array('required' => false, 'type' => 'string', 'default' => '');
$config->todo->batchCreate->form['switchDate']  = array('required' => false, 'type' => 'string', 'default' => '');

$config->todo->edit->form = array();
if($this->post->type && !in_array($this->post->type, $this->config->todo->moduleList)) $config->todo->edit->form['name'] = array('required' => true,  'type' => 'string');
$config->todo->edit->form['status']       = array('required' => true,  'type' => 'string');
$config->todo->edit->form['pri']          = array('required' => true,  'type' => 'int');
$config->todo->edit->form['type']         = array('required' => false, 'type' => 'string',  'default' => '');
$config->todo->edit->form['date']         = array('required' => false, 'type' => 'string',  'default' => helper::today());
$config->todo->edit->form['begin']        = array('required' => false, 'type' => 'int',     'default' => 2400);
$config->todo->edit->form['end']          = array('required' => false, 'type' => 'int',     'default' => 2400);
$config->todo->edit->form['private']      = array('required' => false, 'type' => 'int',     'default' => 0);
$config->todo->edit->form['assignedTo']   = array('required' => false, 'type' => 'string',  'default' => '');
$config->todo->edit->form['objectID']     = array('required' => false, 'type' => 'int',     'default' => 0);
$config->todo->edit->form['desc']         = array('required' => false, 'type' => 'string',  'default' => '');
$config->todo->edit->form['uid']          = array('required' => false, 'type' => 'string',  'default' => '');
$config->todo->edit->form['config']       = array('required' => false, 'type' => 'array',   'default' => array());

$config->todo->assignTo = new stdclass();
$config->todo->assignTo->form = array();
$config->todo->assignTo->form['assignedBy']   = array('required' => false, 'type' => 'string', 'default' => '');
$config->todo->assignTo->form['assignedDate'] = array('required' => false, 'type' => 'string', 'default' => helper::now());
$config->todo->assignTo->form['date']         = array('required' => false, 'type' => 'string', 'default' => '');
$config->todo->assignTo->form['begin']        = array('required' => false, 'type' => 'int',    'default' => 0);
$config->todo->assignTo->form['end']          = array('required' => false, 'type' => 'int',    'default' => 0);

$config->todo->assignTo->form['assignedTo']   = array('required' => true,  'type' => 'string');

$config->todo->batchClose = new stdclass();
$config->todo->batchClose->form = array();
$config->todo->batchClose->form['todoIdList'] = array('required' => true, 'type' => 'array');

$config->todo->batchEdit = new stdclass();
$config->todo->batchEdit->form = array();
$config->todo->batchEdit->form['todoIdList'] = array('required' => true, 'type' => 'array');

$config->todo->batchFinish = new stdclass();
$config->todo->batchFinish->form = array();
$config->todo->batchFinish->form['todoIdList'] = array('required' => true, 'type' => 'array');

$config->todo->editDate = new stdclass();
$config->todo->editDate->form = array();
$config->todo->editDate->form['date']       = array('required' => true, 'type' => 'string');
$config->todo->editDate->form['todoIdList'] = array('required' => true, 'type' => 'array');

$config->todo->export = new stdclass();
$config->todo->export->form = array();
$config->todo->export->form['exportType'] = array('required' => true, 'type' => 'string');
