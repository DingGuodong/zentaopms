<?php
declare(strict_types=1);
/**
 * The start view file of task module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     task
 * @link        https://www.zentao.net
 */
namespace zin;
/* ====== Preparing and processing page data ====== */
jsVar('confirmFinish', $lang->task->confirmFinish);
jsVar('noticeTaskStart', $lang->task->noticeTaskStart);

/* zin: Set variables to define control for form. */
$assignedToControl = '';
if($task->mode == 'linear')
{
    $assignedToControl = inputGroup(
        set::className('no-background'),
        zget($members, $assignedTo),
        input
        (
            set::className('hidden'),
            set::name('assignedTo'),
            set::value($assignedTo),
        )
    );
}
elseif($canRecordEffort)
{
    $assignedToControl = select(
        set::name('assignedTo'),
        set::value($assignedTo),
        set::items($members),
    );
}

/* ====== Define the page structure with zin widgets ====== */

modalHeader();
if(!$canRecordEffort)
{
    if($task->assignedTo != $app->user->account && $task->mode == 'linear')
    {
        $deniedNotice = sprintf($lang->task->deniedNotice, $task->assignedToRealName, $lang->task->start);
    }
    else
    {
        $deniedNotice = sprintf($lang->task->deniedNotice, $lang->task->teamMember, $lang->task->start);
    }

    div
    (
        set::className('alert with-icon'),
        icon('exclamation-sign icon-3x'),
        div
        (
            set::className('content'),
            p
            (
                set::className('font-bold'),
                $deniedNotice
            )
        )
    );
}
else
{
    formPanel
    (
        setID('startForm'),
        formGroup
        (
            set::className($task->mode == 'multi' ? 'hidden' : ''),
            set::width('1/3'),
            set::label($lang->task->assignedTo),
            $assignedToControl,
        ),
        formGroup
        (
            set::width('1/3'),
            set::label($lang->task->realStarted),
            set::name('realStarted'),
            set::control('datetime-local'),
            set::value(helper::isZeroDate($task->realStarted) ? helper::now() : $task->realStarted)
        ),
        formRow
        (
            formGroup
            (
                set::width('1/3'),
                set::label($task->mode == 'linear' ? $lang->task->myConsumed : $lang->task->consumed),
                inputControl
                (
                    input
                    (
                        set::name('consumed'),
                        set::value(!empty($currentTeam) ? (float)$currentTeam->consumed : $task->consumed),
                    ),
                    to::suffix($lang->task->suffixHour),
                    set::suffixWidth(20),
                ),
            ),
            formGroup
            (
                set::width('1/3'),
                set::label($lang->task->left),
                inputControl
                (
                    input
                    (
                        set::name('left'),
                        set::value(!empty($currentTeam) ? (float)$currentTeam->left : $task->left),
                    ),
                    to::suffix($lang->task->suffixHour),
                    set::suffixWidth(20),
                ),
            ),
        ),
        formGroup
        (
            set::label($lang->comment),
            editor
            (
                set::name('comment'),
                set::rows('5'),
            )
        ),
    );
    hr();
    history();
}


/* ====== Render page ====== */
render();
