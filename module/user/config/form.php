<?php
global $lang;
$config->user->form = new stdclass();
$config->user->form->create = common::formConfig('user', 'create');
$config->user->form->create['type']             = array('required' => true,  'type' => 'string', 'default' => 'inside');
$config->user->form->create['company']          = array('required' => false, 'type' => 'int',    'default' => 0);
$config->user->form->create['new']              = array('required' => false, 'type' => 'int',    'default' => 0);
$config->user->form->create['newCompany']       = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->create['dept']             = array('required' => false, 'type' => 'int',    'default' => 0);
$config->user->form->create['account']          = array('required' => true,  'type' => 'string', 'default' => '');
$config->user->form->create['password1']        = array('required' => true,  'type' => 'string', 'default' => '');
$config->user->form->create['password2']        = array('required' => true,  'type' => 'string', 'default' => '');
$config->user->form->create['visions']          = array('required' => true,  'type' => 'array',  'default' => $config->vision, 'filter' => 'join');
$config->user->form->create['realname']         = array('required' => true,  'type' => 'string', 'default' => '');
$config->user->form->create['join']             = array('required' => false, 'type' => 'date',   'default' => null);
$config->user->form->create['role']             = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->create['group']            = array('required' => false, 'type' => 'array',  'default' => []);
$config->user->form->create['email']            = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->create['commiter']         = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->create['gender']           = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->create['verifyPassword']   = array('required' => true,  'type' => 'string', 'default' => '');
$config->user->form->create['passwordLength']   = array('required' => true,  'type' => 'int',    'default' => 0);
$config->user->form->create['passwordStrength'] = array('required' => true,  'type' => 'int',    'default' => 0);

$config->user->form->batchCreate = common::formConfig('user', 'batchCreate');
$config->user->form->batchCreate['type']       = array('required' => true,  'type' => 'string', 'default' => 'inside');
$config->user->form->batchCreate['dept']       = array('required' => false, 'type' => 'int',    'default' => 0);
$config->user->form->batchCreate['company']    = array('required' => false, 'type' => 'int',    'default' => 0);
$config->user->form->batchCreate['new']        = array('required' => false, 'type' => 'int',    'default' => 0);
$config->user->form->batchCreate['newCompany'] = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->batchCreate['account']    = array('required' => true,  'type' => 'string', 'default' => '', 'base' => true);
$config->user->form->batchCreate['realname']   = array('required' => true,  'type' => 'string', 'default' => '');
$config->user->form->batchCreate['visions']    = array('required' => true,  'type' => 'array',  'default' => $config->vision, 'filter' => 'join');
$config->user->form->batchCreate['role']       = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->batchCreate['group']      = array('required' => false, 'type' => 'array',  'default' => []);
$config->user->form->batchCreate['email']      = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->batchCreate['commiter']   = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->batchCreate['gender']     = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->batchCreate['password']   = array('required' => true,  'type' => 'string', 'default' => '');
$config->user->form->batchCreate['join']       = array('required' => false, 'type' => 'date',   'default' => null);
$config->user->form->batchCreate['mobile']     = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->batchCreate['phone']      = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->batchCreate['qq']         = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->batchCreate['dingding']   = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->batchCreate['weixin']     = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->batchCreate['skype']      = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->batchCreate['whatsapp']   = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->batchCreate['slack']      = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->batchCreate['address']    = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->batchCreate['zipcode']    = array('required' => false, 'type' => 'string', 'default' => '');

$config->user->form->edit = common::formConfig('user', 'edit');
$config->user->form->edit['type']             = array('required' => true,  'type' => 'string', 'default' => 'inside');
$config->user->form->edit['company']          = array('required' => false, 'type' => 'int',    'default' => 0);
$config->user->form->edit['new']              = array('required' => false, 'type' => 'int',    'default' => 0);
$config->user->form->edit['newCompany']       = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['dept']             = array('required' => false, 'type' => 'int',    'default' => 0);
$config->user->form->edit['account']          = array('required' => true,  'type' => 'string', 'default' => '');
$config->user->form->edit['password1']        = array('required' => true,  'type' => 'string', 'default' => '');
$config->user->form->edit['password2']        = array('required' => true,  'type' => 'string', 'default' => '');
$config->user->form->edit['visions']          = array('required' => true,  'type' => 'array',  'default' => $config->vision, 'filter' => 'join');
$config->user->form->edit['realname']         = array('required' => true,  'type' => 'string', 'default' => '');
$config->user->form->edit['join']             = array('required' => false, 'type' => 'date',   'default' => null);
$config->user->form->edit['role']             = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['group']            = array('required' => false, 'type' => 'array',  'default' => []);
$config->user->form->edit['email']            = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['gender']           = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['commiter']         = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['mobile']           = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['phone']            = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['qq']               = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['dingding']         = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['weixin']           = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['skype']            = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['whatsapp']         = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['slack']            = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['address']          = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['zipcode']          = array('required' => false, 'type' => 'string', 'default' => '');
$config->user->form->edit['verifyPassword']   = array('required' => true,  'type' => 'string', 'default' => '');
$config->user->form->edit['passwordLength']   = array('required' => true,  'type' => 'int',    'default' => 0);
$config->user->form->edit['passwordStrength'] = array('required' => true,  'type' => 'int',    'default' => 0);

$config->user->form->batchEdit = array();
$config->user->form->batchEdit['dept']     = array('required' => false, 'type' => 'int',    'width' => '200px', 'name' => 'dept',     'label' => $lang->user->dept,     'control' => array('type' => 'picker', 'required' => true), 'default' => 0, 'ditto' => true);
$config->user->form->batchEdit['company']  = array('required' => false, 'type' => 'int',    'width' => '200px', 'name' => 'company',  'label' => $lang->user->company,  'control' => 'picker',    'default' => 0);
$config->user->form->batchEdit['account']  = array('required' => true,  'type' => 'string', 'width' => '140px', 'name' => 'account',  'label' => $lang->user->account,  'control' => 'text',      'default' => '', 'readonly' => true, 'base' => true);
$config->user->form->batchEdit['realname'] = array('required' => true,  'type' => 'string', 'width' => '96px',  'name' => 'realname', 'label' => $lang->user->realname, 'control' => 'text',      'default' => '');
$config->user->form->batchEdit['visions']  = array('required' => true,  'type' => 'array',  'width' => '240px', 'name' => 'visions',  'label' => $lang->user->visions,  'control' => 'picker',    'default' => $config->vision, 'multiple' => true, 'filter' => 'join');
$config->user->form->batchEdit['role']     = array('required' => false, 'type' => 'string', 'width' => '160px', 'name' => 'role',     'label' => $lang->user->role,     'control' => 'picker',    'default' => '', 'ditto' => true, 'items' => $lang->user->roleList);
$config->user->form->batchEdit['email']    = array('required' => false, 'type' => 'string', 'width' => '160px', 'name' => 'email',    'label' => $lang->user->email,    'control' => 'text',      'default' => '');
$config->user->form->batchEdit['gender']   = array('required' => false, 'type' => 'string', 'width' => '100px', 'name' => 'gender',   'label' => $lang->user->gender,   'control' => array('type' => 'radioList', 'inline' => true), 'default' => 'm', 'items' => $lang->user->genderList);
$config->user->form->batchEdit['commiter'] = array('required' => false, 'type' => 'string', 'width' => '120px', 'name' => 'commiter', 'label' => $lang->user->commiter, 'control' => 'text',      'default' => '');
$config->user->form->batchEdit['join']     = array('required' => false, 'type' => 'date',   'width' => '120px', 'name' => 'join',     'label' => $lang->user->join,     'control' => 'date',      'default' => '');
$config->user->form->batchEdit['mobile']   = array('required' => false, 'type' => 'string', 'width' => '120px', 'name' => 'mobile',   'label' => $lang->user->mobile,   'control' => 'text',      'default' => '');
$config->user->form->batchEdit['phone']    = array('required' => false, 'type' => 'string', 'width' => '120px', 'name' => 'phone',    'label' => $lang->user->phone,    'control' => 'text',      'default' => '');
$config->user->form->batchEdit['qq']       = array('required' => false, 'type' => 'string', 'width' => '120px', 'name' => 'qq',       'label' => $lang->user->qq,       'control' => 'text',      'default' => '');
$config->user->form->batchEdit['dingding'] = array('required' => false, 'type' => 'string', 'width' => '120px', 'name' => 'dingding', 'label' => $lang->user->dingding, 'control' => 'text',      'default' => '');
$config->user->form->batchEdit['weixin']   = array('required' => false, 'type' => 'string', 'width' => '120px', 'name' => 'weixin',   'label' => $lang->user->weixin,   'control' => 'text',      'default' => '');
$config->user->form->batchEdit['skype']    = array('required' => false, 'type' => 'string', 'width' => '120px', 'name' => 'skype',    'label' => $lang->user->skype,    'control' => 'text',      'default' => '');
$config->user->form->batchEdit['whatsapp'] = array('required' => false, 'type' => 'string', 'width' => '120px', 'name' => 'whatsapp', 'label' => $lang->user->whatsapp, 'control' => 'text',      'default' => '');
$config->user->form->batchEdit['slack']    = array('required' => false, 'type' => 'string', 'width' => '120px', 'name' => 'slack',    'label' => $lang->user->slack,    'control' => 'text',      'default' => '');
$config->user->form->batchEdit['address']  = array('required' => false, 'type' => 'string', 'width' => '160px', 'name' => 'address',  'label' => $lang->user->address,  'control' => 'text',      'default' => '');
$config->user->form->batchEdit['zipcode']  = array('required' => false, 'type' => 'string', 'width' => '120px', 'name' => 'zipcode',  'label' => $lang->user->zipcode,  'control' => 'text',      'default' => '');
