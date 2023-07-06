<?php
declare(strict_types=1);
/**
 * The edit view of task of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      zenggang<zenggang@easycorp.ltd>
 * @package     task
 * @link        http://www.zentao.net
 */

namespace zin;

include './taskteam.html.php';

/* ====== Preparing and processing page data ====== */
jsVar('oldStoryID', $task->story);
jsVar('oldAssignedTo', $task->assignedTo);
jsVar('oldExecutionID', $task->execution);
jsVar('oldConsumed', $task->consumed);
jsVar('taskStatus', $task->status);
jsVar('currentUser', $app->user->account);
jsVar('team', array_values($task->members));
jsVar('members', $members);
jsVar('page', 'edit');
jsVar('confirmChangeExecution', $lang->task->confirmChangeExecution);
jsVar('teamMemberError', $lang->task->error->teamMember);
jsVar('totalLeftError', sprintf($this->lang->task->error->leftEmptyAB, $this->lang->task->statusList[$task->status]));
jsVar('estimateNotEmpty', sprintf($lang->error->gt, $lang->task->estimate, '0'));
jsVar('leftNotEmpty', sprintf($lang->error->gt, $lang->task->left, '0'));
jsVar('requiredFields', $config->task->edit->requiredFields);

/* zin: Set variables to define picker options for form */
$formTitle        = $task->name;
$executionOptions = $executions;
$moduleOptions    = $modules;
$storyOptions     = $stories;
if($task->status == 'wait' and $task->parent == 0)
{
    $modeOptions = $lang->task->editModeList;
}
else
{
    $modeText = $task->mode == '' ? $lang->task->editModeList['single'] : zget($lang->task->editModeList, $task->mode);
}
$assignedToOptions      = $taskMembers;
$typeOptions            = $lang->task->typeList;
$statusOptions          = (array)$lang->task->statusList;
$priOptions             = $lang->task->priList;
$mailtoOptions          = $users;
$contactListMenuOptions = $contactLists;
$finishedByOptions      = $members;
$canceledByOptions      = $users;
$closedByOptions        = $users;
$closedReasonOptions    = $lang->task->reasonList;
$teamOptions            = $members;
$hiddenTeam             = $task->mode != '' ? '' : 'hidden';

/* ====== Define the page structure with zin widgets ====== */

detailHeader
(
    to::prefix(null),
    to::title
    (
        entityLabel
        (
            set::entityID($task->id),
            set::level(1),
            set::text($task->name),
            set::reverse(true),
        )
    ),
);

detailBody
(
    set::isForm(true),
    sectionList
    (
        section
        (
            set::title($lang->task->name),
            input
            (
                set::label($lang->task->name),
                set::name('name'),
                set::value($task->name),
                set::placeholder($lang->task->name),
                set::control('input'),
                set::required(true),
                set::autofocus(true),
            ),
        ),
        section
        (
            set::title($lang->task->desc),
            textarea
            (
                set::label($lang->task->desc),
                set::name('desc'),
                set::value($task->desc ? htmlSpecialString($task->desc) : ''),
                set::control('textarea'),
            )
        ),
        section
        (
            set::title($lang->task->story),
            formGroup
            (
                set::width('1/2'),
                control
                (
                    set::type('picker'),
                    set::name('story'),
                    set::value($task->story),
                    set::items($storyOptions)
                )
            ),
        ),
        section
        (
            set::title($lang->files),
            upload()
        ),
    ),
    history
    (
        set::actions($actions),
        set::users($users),
        set::methodName($methodName),
        set::commentBtn(true)
    ),
    detailSide
    (
        tableData
        (
            set::title($lang->task->legendBasic),
            item
            (
                set::name($lang->task->execution),
                select
                (
                    set::name('execution'),
                    set::required(true),
                    set::value($task->execution),
                    set::items($executionOptions),
                    on::change('loadAll(this.value)'),
                )
            ),
            item
            (
                set::name($lang->task->module),
                row
                (
                    control(set(array
                    (
                        'name' => 'module',
                        'value' => $task->module,
                        'type' => 'picker',
                        'items' => $moduleOptions
                    ))),
                    checkList
                    (
                        set::name('showAllModule'),
                        set::items(array('1' => $lang->all)),
                        set::value($showAllModule ? '1' : ''),
                        set::inline(true),
                    ),
                )
            ),
            ($task->parent >= 0 and empty($task->team))
                ? item
                (
                    set::name($lang->task->parent),
                    select
                    (
                        set::name('parent'),
                        set::value($task->parent),
                        set::items($tasks)
                    ),
                )
                : null,
            empty($modeText)
                ? item
                (
                    set::name($lang->task->mode),
                    select
                    (
                        set::name('mode'),
                        set::value($task->mode),
                        set::items($modeOptions),
                        set::required(true),
                        on::change('changeMode')
                    )
                )
                : item
                (
                    set::name($lang->task->mode),
                    inputGroup
                    (
                        $modeText
                    )
                ),
            item
            (
                set::name($lang->task->assignedTo),
                row
                (
                    control(set(array
                    (
                        'name' => 'assignedTo',
                        'id' => 'assignedTo',
                        'value' => $task->assignedTo,
                        'disabled' => !empty($task->team) && $task->mode == 'linear',
                        'type' => 'picker',
                        'items' => $assignedToOptions
                    ))),
                    btn(set(array
                    (
                        'type' => 'btn',
                        'text' => $lang->task->team,
                        'class' => "input-group-btn team-group $hiddenTeam",
                        'url' => '#modalTeam',
                        'data-toggle' => 'modal',
                    )))
                )
            ),
            item
            (
                set::name($lang->task->type),
                select
                (
                    set::name('type'),
                    set::required(true),
                    set::value($task->type),
                    set::items($typeOptions)
                ),
            ),
            empty($task->children)
                ? item
                (
                    set::name($lang->task->status),
                    select
                    (
                        set::name('status'),
                        set::value($task->status),
                        set::items($statusOptions)
                    ),
                )
                : null,
            item
            (
                set::name($lang->task->pri),
                select
                (
                    set::name('pri'),
                    set::value($task->pri),
                    set::items($priOptions)
                )
            ),
            item
            (
                set::name($lang->task->mailto),
                inputGroup
                (
                    control(set(array
                    (
                        'name' => 'mailto[]',
                        'id' => 'mailto',
                        'value' => $task->mailto,
                        'type' => 'picker',
                        'items' => $mailtoOptions,
                        'multiple' => true
                    ))),
                    control
                    (
                        setStyle('width', '30%'),
                        set::name('contactListMenu'),
                        set::type('picker'),
                        set::items($contactListMenuOptions),
                        on::change('setMailto')
                    ),
                )
            ),
        ),
        modalTrigger
        (
            modal
            (
                set::id('modalTeam'),
                set::title($lang->task->teamMember),
                h::table
                (
                    set::id('teamTable'),
                    setClass('table table-form'),
                    $teamForm,
                    h::tr
                    (
                        h::td
                        (
                            setClass('team-saveBtn'),
                            set(array('colspan' => 6)),
                            btn
                            (
                                setClass('toolbar-item btn primary'),
                                $lang->save
                            )
                        )
                    )
                )
            )
        ),
        tableData
        (
            set::title($lang->task->legendEffort),
            item
            (
                set::name($lang->task->estStarted),
                control
                (
                    set::name('estStarted'),
                    set::value($task->estStarted),
                    set::type('date')
                )
            ),
            item
            (
                set::name($lang->task->deadline),
                control
                (
                    set::name('deadline'),
                    set::value($task->deadline),
                    set::type('date')
                )
            ),
            item
            (
                set::name($lang->task->estimate),
                inputGroup
                (
                    input
                    (
                        set::name('estimate'),
                        set::value($task->estimate),
                        !empty($task->team) ? set::readonly(true) : null
                    ),
                    div
                    (
                        setClass('input-group-btn'),
                        btn
                        (
                            setClass('btn btn-default'),
                            'H'
                        )
                    )
                )
            ),
            item
            (
                set::name($lang->task->consumed),
                row
                (
                    span
                    (
                        setClass('span-text mr-1'),
                        set::id('consumedSpan'),
                        $task->consumed . 'H'
                    ),
                    h::a
                    (
                        setClass('span-text'),
                        icon('time')
                    )
                )
            ),
            item
            (
                set::name($lang->task->left),
                inputGroup
                (
                    input
                    (
                        set::name('left'),
                        set::value($task->left),
                        !empty($task->team) ? set::readonly(true) : null
                    ),
                    div
                    (
                        setClass('input-group-btn'),
                        btn
                        (
                            setClass('btn btn-default'),
                            'H'
                        )
                    )
                )
            ),
        ),
        tableData
        (
            set::title($lang->task->legendLife),
            item
            (
                set::name($lang->task->realStarted),
                control
                (
                    set::name('realStarted'),
                    set::value(helper::isZeroDate($task->realStarted) ? '' : $task->realStarted),
                    set::type('datetime-local')
                )
            ),
            item
            (
                set::name($lang->task->finishedBy),
                control
                (
                    set::name('finishedBy'),
                    set::value($task->finishedBy),
                    set::type('picker'),
                    set::items($finishedByOptions)
                )
            ),
            item
            (
                set::name($lang->task->finishedDate),
                control
                (
                    set::name('finishedDate'),
                    set::value($task->finishedDate),
                    set::type('datetime-local')
                ),
            ),
            item
            (
                set::name($lang->task->canceledBy),
                control
                (
                    set::name('canceledBy'),
                    set::value($task->canceledBy),
                    set::type('picker'),
                    set::items($canceledByOptions)
                )
            ),
            item
            (
                set::name($lang->task->canceledDate),
                control
                (
                    set::name('canceledDate'),
                    set::value($task->canceledDate),
                    set::type('datetime-local')
                )
            ),
            item
            (
                set::name($lang->task->closedBy),
                control
                (
                    set::name('closedBy'),
                    set::value($task->closedBy),
                    set::type('picker'),
                    set::items($closedByOptions)
                )
            ),
            item
            (
                set::name($lang->task->closedReason),
                control
                (
                    set::name('closedReason'),
                    set::value($task->closedReason),
                    set::type('picker'),
                    set::items($closedReasonOptions)
                )
            ),
            item
            (
                set::name($lang->task->closedDate),
                control
                (
                    set::name('closedDate'),
                    set::value($task->closedDate),
                )
            ),
            item
            (
                set::trClass('hidden'),
                control
                (
                    set::name('lastEditedDate'),
                    set::value($task->lastEditedDate),
                    set::id('lastEditedDate'),
                )
            ),
        )
    )
);
