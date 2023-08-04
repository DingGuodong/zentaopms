<?php
namespace zin;

$opts = $this->search->buildSearchFormOptions($module, $fieldParams, $fields, $queries, $actionURL);

$opts->groupName       = array($lang->search->group1, $lang->search->group2);
$opts->savedQueryTitle = $lang->search->savedQuery;
$opts->formSession     = $formSession;
$opts->module          = $module;
$opts->actionURL       = $actionURL;
$opts->groupItems      = $groupItems;
$opts->onSubmit        = jsRaw('window.onSubmit');

if(empty($opts->savedQuery)) unset($opts->savedQuery);

$formName = empty($formName) ? '#searchFormPanel[data-module="' . $module . '"]' : $formName;
zui::searchform(set((array)$opts), set::_to($formName), set::className('shadow'));

jsVar('options',          isset($options) ? $options : null);
jsVar('canSaveQuery',     !empty($_SESSION[$module . 'Query']));
jsVar('formSession',      $_SESSION[$module . 'Form']);
jsVar('onMenuBar',        $onMenuBar);

js($pageJS);

render();
