<?php
$config->execution->form = new stdclass();

$config->execution->form->importTask = array();

$config->execution->form->importTask['taskIdList'] = array('type' => 'int', 'required' => false, 'base' => true);

$config->execution->form->setkanban = array();
$config->execution->form->setkanban['colWidth']     = array('type' => 'int',    'required' => false);
$config->execution->form->setkanban['heightType']   = array('type' => 'string', 'required' => false);
$config->execution->form->setkanban['displayCards'] = array('type' => 'int',    'required' => false);
$config->execution->form->setkanban['fluidBoard']   = array('type' => 'string', 'required' => false);
$config->execution->form->setkanban['minColWidth']  = array('type' => 'int',    'required' => false);
$config->execution->form->setkanban['maxColWidth']  = array('type' => 'int',    'required' => false);

$config->execution->form->fixfirst['estimate'] = array('type' => 'float', 'required' => false);

$config->execution->form->managemembers['account'] = array('type' => 'string', 'required' => false, 'default' => '', 'base' => true);
$config->execution->form->managemembers['role']    = array('type' => 'string', 'required' => false, 'default' => '');
$config->execution->form->managemembers['days']    = array('type' => 'int', 'required' => false, 'default' => 0);
$config->execution->form->managemembers['hours']   = array('type' => 'float', 'required' => false, 'default' => 0);
$config->execution->form->managemembers['limited'] = array('type' => 'string', 'required' => false, 'default' => 'no');
$config->execution->form->managemembers['type']    = array('type' => 'string', 'required' => false, 'default' => 'execution');
$config->execution->form->managemembers['root']    = array('type' => 'int', 'required' => false, 'default' => 0);
