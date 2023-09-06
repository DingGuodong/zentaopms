<?php
declare(strict_types=1);
/**
 * The bug view file of execution module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     execution
 * @link        https://www.zentao.net
 */
namespace zin;

/* zin: Define the productMenu on main menu. */
$productDropdown = '';
if($execution->hasProduct)
{
    $productDropdown = productMenu(
        set::title($product ? $product->name : $productOption[$productID]),
        set::items($productOption),
        set::activeKey($productID),
        set::link(helper::createLink('execution', 'bug', "executionID={$execution->id}&productID={key}")),
    );
}

$branchDropdown = '';
if($showBranch)
{
    $branchDropdown = productMenu(
        set::title($branchOption[$branch]),
        set::items($branchOption),
        set::activeKey($branch),
        set::link(helper::createLink('execution', 'bug', "executionID={$execution->id}&productID={$productID}&branch=%s")),
    );
}

/* zin: Define the set::module('bug') feature bar on main menu. */
featureBar
(
    to::before($productDropdown),
    to::before($branchDropdown),
    set::current($type),
    set::linkParams("executionID={$execution->id}&productID={$productID}&branch={$branchID}&orderBy={$orderBy}&build={$buildID}&type={key}&param=$param&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}"),
    li(searchToggle(set::module('executionBug')))
);

/* zin: Define the toolbar on main menu. */
$canExportBug = hasPriv('bug', 'export');
$canCreateBug = hasPriv('bug', 'create') && common::canModify('execution', $execution);

if($canExportBug) $exportItem = array('icon' => 'export', 'class' => 'ghost', 'text' => $lang->bug->export, 'url' => $this->createLink('bug', 'export', "productID={$productID}&browseType=&executionID={$execution->id}"), 'data-toggle' => 'modal');
if($canCreateBug) $createItem = array('icon' => 'plus', 'class' => 'primary', 'text' => $lang->bug->create, 'url' => $this->createLink('bug', 'create', "productID={$defaultProduct}&branch=0&extras=executionID={$execution->id}"));
toolbar
(
    !empty($canExportBug) ? item(set($exportItem)) : null,
    !empty($createItem) ? item(set($createItem)) : null,
);

/* zin: Define the sidebar in main content. */
sidebar
(
    moduleMenu(set(array(
        'modules'   => $moduleTree,
        'activeKey' => $moduleID,
        'closeLink' => $this->createLink('execution', 'bug', "executionID={$execution->id}")
    )))
);

$bugs = initTableData($bugs, $this->config->bug->dtable->fieldList, $this->bug);
$canBatchAssignTo = common::hasPriv('bug', 'batchAssignTo');

if($canBatchAssignTo)
{
    $assignedToItems = array();
    foreach ($users as $account => $name)
    {
        $assignedToItems[] = array('text' => $name, 'class' => 'batch-btn ajax-btn', 'data-url' => helper::createLink('bug', 'batchAssignTo', "assignedTo={$account}&objectID={$execution->id}"));
    }

    menu(
        set::id('navAssignedTo'),
        set::className('dropdown-menu'),
        set::items($assignedToItems)
    );

    $footToolbar['items'][] = array('caret' => 'up', 'text' => $lang->task->assignedTo, 'btnType' => 'secondary', 'url' => '#navAssignedTo','data-toggle' => 'dropdown');
}

jsVar('orderBy', $orderBy);
jsVar('sortLink', helper::createLink('execution', 'bug', "executionID={$execution->id}&productID={$productID}&branch={$branchID}&orderBy={orderBy}&build=$buildID&type=$type&param=$param&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}"));
jsVar('pageSummary', $summary);
jsVar('checkedSummary', $lang->selectedItems);

$cols = $this->loadModel('datatable')->getSetting('execution');
if(isset($cols['module']))      $cols['module']['map']      = $modulePairs;
if(isset($cols['branch']))      $cols['branch']['map']      = $branchOption;
if(isset($cols['project']))     $cols['project']['map']     = $projectPairs;
if(isset($cols['openedBuild'])) $cols['openedBuild']['map'] = $builds;

$bugs = initTableData($bugs, $cols, $this->execution);

dtable
(
    set::userMap($users),
    set::cols($cols),
    set::data(array_values($bugs)),
    set::checkable($canBatchAssignTo),
    set::sortLink(jsRaw('createSortLink')),
    set::footToolbar($footToolbar),
    set::customCols(true),
    set::footPager(
        usePager(array('linkCreator' => helper::createLink('execution', 'bug', "executionID={$execution->id}&productID={$productID}&branch={$branchID}&orderBy={$orderBy}&build=$buildID&type=$type&param=$param&recTotal={$pager->recTotal}&recPerPage={recPerPage}&pageID={page}"))),
    ),
    set::checkInfo(jsRaw('function(checkedIDList){return window.setStatistics(this, checkedIDList);}'))
);
render();
