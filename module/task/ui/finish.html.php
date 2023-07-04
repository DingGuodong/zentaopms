<?php
declare(strict_types=1);
/**
 * The finish view file of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Sun Guangming<sunguangming@easycorp.ltd>
 * @package     task
 * @link        http://www.zentao.net
 */

namespace zin;

if(!$canRecordEffort)
{
    modalHeader();
    if($task->assignedTo != $app->user->account && $task->mode == 'linear')
    {
        $deniedNotice = sprintf($lang->task->deniedNotice, $task->assignedToRealName, $lang->task->finish);
    }
    else
    {
        $deniedNotice = sprintf($lang->task->deniedNotice, $lang->task->teamMember, $lang->task->finish);
    }

    div
    (
        set::class('alert with-icon'),
        icon('exclamation-sign icon-3x'),
        div
        (
            set::class('content'),
            p
            (
                set::class('font-bold'),
                $deniedNotice
            )
        )
    );
}
else
{
    jsVar('consumed', empty($task->team) ? $task->consumed : (float)$task->myConsumed);
    jsVar('task', $task);
    jsVar('consumedEmpty', $lang->task->error->consumedEmptyAB);

    $realStarted = substr((string)$task->realStarted, 0, 19);
    if(helper::isZeroDate($realStarted)) $realStarted = '';

    if(!empty($task->team))
    {
        $consumedControl = formGroup
            (
                set::width('1/3'),
                set::label($lang->task->my . $lang->task->hasConsumed),
                div(
                    set::class('consumed'),
                    $task->myConsumed . $lang->task->suffixHour
                )
            );

        $assignedToControl = formGroup
            (
                set::width('1/3'),
                set::label($lang->story->assignTo),
                set::control('input'),
                set::disabled(true),
                set::value(zget($members, $task->nextBy)),
                formHidden('assignedTo', $task->nextBy)
            );
    }
    else
    {
        $consumedControl = null;

        $assignedToControl = formGroup
            (
                set::width('1/3'),
                set::name('assignedTo'),
                set::label($lang->story->assignTo),
                set::control('picker'),
                set::value($task->nextBy),
                set::items($members)
            );
    }

    modalHeader
    (
        to::suffix
        (
            span
            (
                setClass('flex gap-x-2 mr-3'),
                !empty($task->team) ? $lang->task->common . $lang->task->consumed : $lang->task->hasConsumed,
                span
                (
                    setClass('label secondary-pale'),
                    $task->consumed . $lang->task->suffixHour,
                ),
            ),
            span
            (
                setClass('flex gap-x-2'),
                $lang->task->consumed,
                span
                (
                    setClass('label warning-pale'),
                    span
                    (
                        setID('totalConsumed'),
                        $task->consumed
                    ),
                    $lang->task->suffixHour,
                )
            )
        )
    );

    formPanel
    (
        $consumedControl,
        formGroup
        (
            set::width('1/3'),
            set::label($lang->task->currentConsumed),
            inputControl
            (
                input
                (
                    set::name('currentConsumed'),
                    set::value(0),
                    set::type('text'),
                ),
                to::suffix($lang->task->suffixHour),
                set::suffixWidth(20),
            ),
        ),
        $assignedToControl,
        formGroup
        (
            set::width('1/3'),
            set::name('realStarted'),
            set::label($lang->project->realBeganAB),
            set::control('datetime'),
            set::value($realStarted),
            set('disabled', $realStarted)
        ),
        formGroup
        (
            set::width('1/3'),
            set::name('finishedDate'),
            set::label($lang->project->realEndAB),
            set::value(helper::now()),
            set::control('datetime')
        ),
        formGroup
        (
            set::width('2/3'),
            set::label($lang->story->files),
            upload
            (
                set::name('files'),
            )
        ),
        formGroup
        (
            set::name('comment'),
            set::label($lang->comment),
            set::control("editor")
        ),
    );
    history();
}

render();
