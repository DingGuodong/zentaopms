<?php
declare(strict_types=1);
/**
 * The bug view file of my module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tingting Dai <daitingting@easycorp.ltd>
 * @package     my
 * @link        https://www.zentao.net
 */
namespace zin;

$testcaseTitle = "[" . $lang->testcase->common . "#{case}]";
$testcaseLink = createLink('testcase', 'view', "caseID={case}&version={caseVersion}");

jsVar('testcaseTitle', $testcaseTitle);
jsVar('testcaseLink', $testcaseLink);

$linkParam = 'type={key}';
if($app->rawMethod == 'contribute') $linkParam = "mode=$mode&$linkParam";
featurebar
(
    set::current($type),
    set::linkParams($linkParam),
    li(searchToggle(set::module('bug')))
);

$canBatchEdit     = common::hasPriv('bug', 'batchEdit')    && $type == 'assignedTo';
$canBatchConfirm  = common::hasPriv('bug', 'batchConfirm') && $type != 'closedBy';
$canBatchClose    = common::hasPriv('bug', 'batchClose')   && strtolower($type) != 'closedby';
$canBatchAssignTo = common::hasPriv('bug', 'batchAssignTo');
$canBatchAction   = $canBatchEdit || $canBatchConfirm || $canBatchClose || $canBatchAssignTo;

if($type == 'openedBy')       unset($config->bug->dtable->fieldList['openedBy']);
if($type == 'assignedTo')     unset($config->bug->dtable->fieldList['assignedTo']);
if($type == 'resolvedBy')     unset($config->bug->dtable->fieldList['resolvedBy']);
if($app->rawMethod != 'work') unset($config->bug->dtable->fieldList['deadline']);
if(!$canBatchAction) $config->bug->dtable->fieldList['id']['type'] = 'id';

$projectBrowseLink = createLink('project', 'browse');
$productLink       = explode('-', $config->productLink);
$param             = $config->productLink == 'product-all' ? '' : "productID={product}";
$productBrowseLink = createLink('product', $productLink[1], $param);
$config->bug->dtable->fieldList['product']['link'] = 'RAWJS<function(info){ if(info.row.data.shadow) return \'' . $projectBrowseLink . '\'; else return \'' . $productBrowseLink . '\'; }>RAWJS';

foreach($bugs as $bug) $bug->canBeChanged = common::canBeChanged('bug', $bug);

$footToolbar = $canBatchAction ? array('items' => array
(
    array('text' => $lang->edit, 'className' => 'batch-btn ' . ($canBatchEdit ? '' : 'hidden'), 'data-url' => createLink('bug', 'batchEdit')),
    array('text' => $lang->confirm, 'className' => 'batch-btn ajax-btn ' . ($canBatchConfirm ? '' : 'hidden'), 'data-url' => createLink('bug', 'batchConfirm')),
    array('text' => $lang->close, 'className' => 'batch-btn ajax-btn ' . ($canBatchClose ? '' : 'hidden'), 'data-url' => createLink('bug', 'batchClose')),
    array('text' => $lang->bug->assignedTo, 'className' => ($canBatchAssignTo ? '' : 'hidden'), 'caret' => 'up', 'url' => '#navAssignedTo','data-toggle' => 'dropdown', 'data-placement' => 'top-start'),
), 'btnProps' => array('size' => 'sm', 'btnType' => 'secondary')) : null;

$assignedToItems = array();
foreach ($memberPairs as $key => $value)
{
    $assignedToItems[] = array('text' => $value, 'class' => 'batch-btn ajax-btn', 'data-url' => createLink('bug', 'batchAssignTo', "assignedTo=$key&productID=0&type=my"));
}

menu
(
    set::id('navAssignedTo'),
    set::class('dropdown-menu'),
    set::items($assignedToItems)
);

$cols = $this->loadModel('datatable')->getSetting('my');
$bugs = initTableData($bugs, $cols, $this->bug);

dtable
(
    set::cols($cols),
    set::data(array_values($bugs)),
    set::customCols(array('url' => createLink('datatable', 'ajaxcustom', "module=my&method=bug"), 'hint' => $app->lang->datatable->custom)),
    set::userMap($users),
    set::onRenderCell(jsRaw('window.onRenderBugNameCell')),
    set::checkable($canBatchAction),
    set::canRowCheckable(jsRaw('function(rowID){return this.getRowInfo(rowID).data.canBeChanged;}')),
    set::footToolbar($footToolbar),
    set::footPager(usePager()),
);

render();
