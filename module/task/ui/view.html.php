<?php
declare(strict_types=1);
/**
 * The activate view file of task module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     task
 * @link        https://www.zentao.net
 */
namespace zin;
global $lang;

detailHeader
(
    to::title(entityLabel(set(array('entityID' => $task->id, 'level' => 1, 'text' => $task->name)))),
    common::hasPriv('task', 'create') ? to::suffix(btn(set::icon('plus'), set::url(createLink('task', 'create', "executionID={$task->execution}")), set::type('primary'), $lang->task->create)) : null
);

/* Construct suitable actions for the current task. */
$operateMenus = array();
foreach($config->task->view->operateList['main'] as $operate)
{
    if(!common::hasPriv('task', $operate)) continue;
    if(!$this->task->isClickable($task, $operate)) continue;

    $operateMenus[] = $config->task->actionList[$operate];
}

/* Construct common actions for task. */
$commonActions = array();
foreach($config->task->view->operateList['common'] as $operate)
{
    if(!common::hasPriv('task', $operate)) continue;
    if($operate == 'view' && $task->parent <= 0) continue;

    $settings = $config->task->actionList[$operate];
    $settings['text'] = '';

    $commonActions[] = $settings;
}

detailBody
(
    sectionList
    (
        section
        (
            set::title($lang->task->legendDesc),
            set::content(empty($task->desc) ? $lang->noData : $task->desc),
            set::useHtml(true)
        ),
        section
        (
            set::title($lang->task->story),
            sectionCard
            (
                entityLabel
                (
                    set::entityID($task->storyID),
                    set::text($task->storyTitle),
                ),
                item
                (
                    set::title($lang->story->legendSpec),
                    empty($task->storySpec) && empty($task->storyFiles) ? $lang->noData : html($task->storySpec)
                ),
                item
                (
                    set::title($lang->task->storyVerify),
                    empty($task->storyVerify) ? $lang->noData : html($task->storyVerify)
                ),
            )
        ),
    ),
    history(),
    floatToolbar
    (
        set::prefix
        (
            array(array('icon' => 'back', 'text' => $lang->goback))
        ),
        set::main($operateMenus),
        set::suffix($commonActions),
        set::object($task)
    ),
    detailSide
    (
        tabs
        (
            set::collapse(true),
            tabPane
            (
                set::key('legend-basic'),
                set::title($lang->task->legendBasic),
                set::active(true),
                tableData
                (
                    item
                    (
                        set::name($lang->task->execution),
                        $execution->name
                    ),
                    item
                    (
                        set::name($lang->task->module),
                        ''
                    ),
                    item
                    (
                        set::name($lang->task->assignedTo),
                        $task->assignedTo ? $task->assignedToRealName : $lang->noData
                    ),
                    item
                    (
                        set::name($lang->task->type),
                        zget($this->lang->task->typeList, $task->type, $task->type)
                    ),
                    item
                    (
                        set::name($lang->task->status),
                        $this->processStatus('task', $task)
                    ),
                    item
                    (
                        set::name($lang->task->progress),
                        $task->progress . ' %'
                    ),
                    item
                    (
                        set::name($lang->task->pri),
                        priLabel(1)
                    )
                )
            ),
            tabPane
            (
                set::key('legend-life'),
                set::title($lang->task->legendLife),
                tableData
                (
                    item
                    (
                        set::name($lang->task->openedBy),
                        $task->openedBy ? zget($users, $task->openedBy, $task->openedBy) . $lang->at . $task->openedDate : $lang->noData
                    ),
                    item
                    (
                        set::name($lang->task->finishedBy),
                        $task->finishedBy ? zget($users, $task->finishedBy, $task->finishedBy) . $lang->at . $task->finishedDate : $lang->noData
                    ),
                    item
                    (
                        set::name($lang->task->canceledBy),
                        $task->canceledBy ? zget($users, $task->canceledBy, $task->canceledBy) . $lang->at . $task->canceledDate : $lang->noData,
                    ),
                    item
                    (
                        set::name($lang->task->closedBy),
                        $task->closedBy ? zget($users, $task->closedBy, $task->closedBy) . $lang->at . $task->closedDate : $lang->noData,
                    ),
                    item
                    (
                        set::name($lang->task->closedReason),
                        $task->closedReason ? $lang->task->reasonList[$task->closedReason] : $lang->noData
                    ),
                    item
                    (
                        set::name($lang->task->lastEdited),
                        $task->lastEditedBy ? zget($users, $task->lastEditedBy, $task->lastEditedBy) . $lang->at . $task->lastEditedDate : $lang->noData
                    ),
                )
            ),
        ),
        tabs
        (
            set::collapse(true),
            tabPane
            (
                set::key('legend-effort'),
                set::title($lang->task->legendEffort),
                set::active(true),
                tableData
                (
                    item
                    (
                        set::name($lang->task->estimate),
                        $task->estimate . $lang->workingHour
                    ),
                    item
                    (
                        set::name($lang->task->consumed),
                        round($task->consumed, 2) . $lang->workingHour,
                    ),
                    item
                    (
                        set::name($lang->task->left),
                        $task->left . $lang->workingHour
                    ),
                    item
                    (
                        set::name($lang->task->estStarted),
                        $task->estStarted
                    ),
                    item
                    (
                        set::name($lang->task->realStarted),
                        helper::isZeroDate($task->realStarted) ? '' : substr($task->realStarted, 0, 19),
                    ),
                    item
                    (
                        set::name($lang->task->deadline),
                        $task->deadline
                    ),
                )
            ),
            tabPane
            (
                set::key('legend-misc'),
                set::title($lang->task->legendMisc),
                tableData
                (
                    item
                    (
                        set::name($lang->task->linkMR),
                        $task->openedBy ? zget($users, $task->openedBy, $task->openedBy) . $lang->at . $task->openedDate : $lang->noData
                    ),
                    item
                    (
                        set::name($lang->task->linkCommit),
                        $task->finishedBy ? zget($users, $task->finishedBy, $task->finishedBy) . $lang->at . $task->finishedDate : $lang->noData
                    )
                )
            )
        )
    )
);

render();
