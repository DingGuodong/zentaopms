<?php
declare(strict_types=1);
/**
 * The tree view file of execution module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yanyi Cao <caoyanyi@easycorp.ltd>
 * @package     execution
 * @link        https://www.zentao.net
 */
namespace zin;

/* zin: Define the feature bar on main menu. */
featureBar
(
    set::current('all'),
    checkbox
    (
        set::rootClass('ml-2'),
        set::name('showStory'),
        set::text($lang->execution->treeLevel['story']),
        on::change('changeDisplay')
    ),
);

/* zin: Define the toolbar on main menu. */
if(!isset($browseType)) $browseType = 'all';
if(!isset($orderBy))    $orderBy = '';

$canImportTask  = hasPriv('task', 'importTask');
$canImportBug   = hasPriv('task', 'importBug');
if(common::canModify('execution', $execution))
{
    if($canImportTask && $execution->multiple) $importTaskItem = array('text' => $lang->execution->importTask, 'url' => $this->createLink('execution', 'importTask', "execution={$execution->id}"));
    if($canImportBug && $execution->lifetime != 'ops' && !in_array($execution->attribute, array('request', 'review')))
    {
        $importBugItem = array('text' => $lang->execution->importBug, 'url' => $this->createLink('execution', 'importBug', "execution={$execution->id}"));
    }
}
toolbar
(
    hasPriv('task', 'report') ? item(set(array
    (
        'icon'  => 'bar-chart',
        'text'  => $lang->task->reportChart,
        'class' => 'ghost',
        'url'   => createLink('task', 'report', "execution={$executionID}&browseType={$browseType}"),
    ))) : null,
    (!empty($importTaskItem) || !empty($importBugItem)) ? dropdown(
        btn(
            setClass('ghost btn square btn-default'),
            set::icon('import'),
            set::text($lang->import)
        ),
        set::items(array_filter(array($importTaskItem, $importBugItem))),
        set::placement('bottom-end'),
    ) : null,
    hasPriv('task', 'export') ? item(set(array
    (
        'icon'        => 'export',
        'text'        => $lang->export,
        'class'       => 'ghost',
        'url'         => createLink('task', 'export', "execution={$executionID}&orderBy={$orderBy}&type=tree"),
        'data-toggle' => 'modal'
    ))) : null,
    hasPriv('task', 'create') ? item(set(array
    (
        'icon' => 'plus',
        'text' => $lang->task->create,
        'class' => 'primary create-execution-btn',
        'url'   => createLink('task', 'create', "execution={$executionID}"),
    ))) : null
);

$noData = null;
if(empty($tree))
{

    $noData = div(
        setClass('table-empty-tip h-60 flex items-center justify-center'),
        span
        (
            setClass('text-gray'),
            $lang->task->noTask
        ),
        common::hasPriv('task', 'create', $checkObject) ? btn
        (
            set::text($lang->task->create),
            set::icon('plus'),
            set::url(createLink('task', 'create', "execution={$executionID}" . (isset($moduleID) ? "&storyID=&moduleID={$moduleID}" : ''))),
            setClass('primary ml-2'),
        ) : null
    );
}

div
(
    setClass('flex flex-nowrap'),
    panel
    (
        setClass('flex-auto'),
        $noData ? $noData : tree
        (
            set::id('taskTree'),
            set::items($tree),
            set::canSplit(false),
            set::hover(true),
            set::defaultNestedShow(true),
            set::onClickItem(jsRaw('loadObject'))
        )
    ),
    div
    (
        setID('detailBlock'),
        setClass('w-96 ml-4 hidden'),
    )
);
