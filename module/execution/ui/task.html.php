<?php
declare(strict_types=1);
/**
 * The task view file of execution module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yanyi Cao<caoyanyi@easycorp.ltd>
 * @package     execution
 * @link        http://www.zentao.net
 */

namespace zin;

/* zin: Define the set::module('task') feature bar on main menu. */
featureBar
(
    set::current($browseType),
    set::linkParams("executionID={$execution->id}&status={key}&param={$param}&orderBy={$orderBy}&recTotal={$recTotal}&recPerPage={$recPerPage}"),
    li(searchToggle(set::module('task'), set::open($browseType == 'bysearch')))
);

/* zin: Define the toolbar on main menu. */
$canCreate      = hasPriv('task', 'create');
$canBatchCreate = hasPriv('task', 'batchCreate');
$canImportTask  = hasPriv('task', 'importTask');
$canImportBug   = hasPriv('task', 'importBug');

$this->loadModel('task');
if(common::canModify('execution', $execution))
{
    $params          = isset($moduleID) ? "&storyID=0&moduleID=$moduleID" : "";
    $batchCreateLink = $this->createLink('task', 'batchCreate', "executionID={$execution->id}{$params}");
    $createLink      = $this->createLink('task', 'create',      "executionID={$execution->id}{$params}");
    if(commonModel::isTutorialMode())
    {
        $wizardParams   = helper::safe64Encode("executionID={$execution->id}{$params}");
        $taskCreateLink = $this->createLink('tutorial', 'wizard', "module=task&method=create&params=$wizardParams");
    }

    $createItem      = array('text' => $lang->task->create,      'url' => $createLink);
    $batchCreateItem = array('text' => $lang->task->batchCreate, 'url' => $batchCreateLink);

    if($canImportTask && $execution->multiple) $importTaskItem = array('text' => $lang->execution->importTask, 'url' => $this->createLink('execution', 'importTask', "execution={$execution->id}"));
    if($canImportBug && $execution->lifetime != 'ops' && !in_array($execution->attribute, array('request', 'review')))
    {
        $importBugItem = array('text' => $lang->execution->importBug, 'url' => $this->createLink('execution', 'importBug', "execution={$execution->id}"));
    }
}

$cols = $this->loadModel('datatable')->getSetting('execution');
$tableData = initTableData($tasks, $cols, $this->task);

toolbar
(
    hasPriv('task', 'report') ? item(set(array
    (
        'icon'  => 'bar-chart',
        'class' => 'ghost',
        'url'   => createLink('task', 'report', "execution={$execution->id}&browseType={$browseType}")
    ))) : null,
    hasPriv('task', 'export') ? item(set(array
    (
        'icon'        => 'export',
        'class'       => 'ghost export',
        'url'         => createLink('task', 'export', "execution={$execution->id}&orderBy={$orderBy}&type={$browseType}"),
        'data-toggle' => 'modal'
    ))) : null,
    (!empty($importTaskItem) || !empty($importBugItem)) ? dropdown(
        btn(
            setClass('ghost btn square btn-default'),
            set::icon('import')
        ),
        set::items(array_filter(array($importTaskItem, $importBugItem))),
        set::placement('bottom-end'),
    ) : null,
    $canCreate && $canBatchCreate ? btngroup
    (
        btn(setClass('btn primary'), set::icon('plus'), set::url($createLink), $lang->task->create),
        dropdown
        (
            btn(setClass('btn primary dropdown-toggle'),
            setStyle(array('padding' => '6px', 'border-radius' => '0 2px 2px 0'))),
            set::items(array_filter(array($createItem, $batchCreateItem))),
            set::placement('bottom-end'),
        )
    ) : null,
    $canCreate && !$canBatchCreate ? item(set($createItem + array('class' => 'btn primary', 'icon' => 'plus'))) : null,
    $canBatchCreate && !$canCreate ? item(set($batchCreateItem + array('class' => 'btn primary', 'icon' => 'plus'))) : null,
);

/* zin: Define the sidebar in main content. */
sidebar
(
    moduleMenu(set(array(
        'modules'     => $moduleTree,
        'activeKey'   => $moduleID,
        'settingLink' => $this->createLink('tree', 'browsetask', "rootID=$execution->id&productID=0"),
        'closeLink'   => $this->createLink('execution', 'task'),
    )))
);

$firstTask            = reset($tasks);
$canBatchEdit         = common::hasPriv('firstTask', 'batchEdit', !empty($firstTask) ? $firstTask : null);
$canBatchClose        = common::hasPriv('firstTask', 'batchClose', !empty($firstTask) ? $firstTask : null) && strtolower($browseType) != 'closed';
$canBatchCancel       = common::hasPriv('firstTask', 'batchCancel', !empty($firstTask) ? $firstTask : null) && strtolower($browseType) != 'cancel';
$canBatchAssignTo     = common::hasPriv('firstTask', 'batchAssignTo', !empty($firstTask) ? $firstTask : null);
$canBatchChangeModule = common::hasPriv('firstTask', 'batchChangeModule', !empty($firstTask) ? $firstTask : null);
$canBatchAction       = in_array(true, array($canBatchEdit, $canBatchClose, $canBatchCancel, $canBatchChangeModule, $canBatchAssignTo));

$footToolbar = array();
if($canBatchAction)
{
    if($canBatchClose || $canBatchCancel)
    {
        menu
        (
            set::id('navActions'),
            set::class('dropdown-menu'),
            $canBatchClose ? item(set(array(
                'text'     => $lang->close,
                'class'    => 'batch-btn ajax-btn',
                'data-url' => createLink('task', 'batchClose')
            ))) : null,
            $canBatchCancel ? item(set(array(
                'text'     => $lang->task->cancel,
                'class'    => 'batch-btn ajax-btn',
                'data-url' => createLink('task', 'batchCancel')
            ))) : null,
        );
    }

    if($canBatchChangeModule)
    {
        $moduleItems = array();
        foreach($modules as $moduleID => $module)
        {
            $moduleItems[] = array('text' => $module, 'class' => 'batch-btn ajax-btn', 'data-url' => createLink('task', 'batchChangeModule', "moduleID=$moduleID"));
        }

        menu
        (
            set::id('navModule'),
            set::class('dropdown-menu'),
            set::items($moduleItems)
        );
    }

    if($canBatchAssignTo)
    {
        $assignedToItems = array();
        foreach ($memberPairs as $account => $name)
        {
            $assignedToItems[] = array('text' => $name, 'class' => 'batch-btn ajax-btn', 'data-url' => createLink('task', 'batchAssignTo', "executionID={$execution->id}&assignedTo={$account}"));
        }

        menu
        (
            set::id('navAssignedTo'),
            set::class('dropdown-menu'),
            set::items($assignedToItems)
        );
    }

    if($canBatchClose || $canBatchCancel || $canBatchEdit)
    {
        $editClass = $canBatchEdit ? 'batch-btn' : 'disabled';
        $footToolbar['items'][] = array(
            'type'  => 'btn-group',
            'items' => array(
                array('text' => $lang->edit, 'className' => "btn secondary size-sm {$editClass}", 'btnType' => 'primary', 'data-url' => createLink('task', 'batchEdit', "executionID={$execution->id}")),
                array('caret' => 'up', 'className' => 'btn btn-caret size-sm secondary', 'url' => '#navActions', 'data-toggle' => 'dropdown', 'data-placement' => 'top-start'),
            )
        );
    }

    if($canBatchChangeModule) $footToolbar['items'][] = array('caret' => 'up', 'text' => $lang->task->moduleAB,   'className' => 'btn btn-caret size-sm secondary', 'url' => '#navModule',    'data-toggle' => 'dropdown', 'data-placement' => 'top-start');
    if($canBatchAssignTo)     $footToolbar['items'][] = array('caret' => 'up', 'text' => $lang->task->assignedTo, 'className' => 'btn btn-caret size-sm secondary', 'url' => '#navAssignedTo','data-toggle' => 'dropdown');
}

jsVar('orderBy',        $orderBy);
jsVar('sortLink',       helper::createLink('execution', 'task', "executionID={$execution->id}&status={$status}&param={$param}&orderBy={orderBy}&recTotal={$recTotal}&recPerPage={$recPerPage}"));
jsVar('pageSummary',    $lang->execution->pageSummary);
jsVar('checkedSummary', $lang->execution->checkedSummary);
jsVar('multipleAB',     $lang->task->multipleAB);
jsVar('childrenAB',     $lang->task->childrenAB);
jsVar('todayLabel',     $lang->today);
jsVar('yesterdayLabel', $lang->yesterday);

dtable
(
    set::groupDivider(true),
    set::userMap($memberPairs),
    set::cols($cols),
    set::data($tableData),
    set::checkable($canBatchAction),
    set::sortLink(jsRaw('createSortLink')),
    set::onRenderCell(jsRaw('window.renderCell')),
    set::footToolbar($footToolbar),
    set::footPager(usePager(array
    (
        'recPerPage'  => $recPerPage,
        'recTotal'    => $recTotal,
        'linkCreator' => helper::createLink('execution', 'task', "executionID={$execution->id}&status={$status}&param={$param}&orderBy=$orderBy&recTotal={$recTotal}&recPerPage={recPerPage}&page={page}")
    ))),
    set::checkInfo(jsRaw('function(checkedIDList){return window.setStatistics(this, checkedIDList);}')),
    set::customCols(true)
);

render();
