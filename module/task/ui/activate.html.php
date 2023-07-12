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
/* ====== Preparing and processing page data ====== */
jsVar('oldConsumed', $task->consumed);
jsVar('currentUser', $app->user->account);
jsVar('members', $members);
jsVar('teamMemberError', $lang->task->error->teamMember);
jsVar('teamLeftEmpty', $lang->task->error->teamLeftEmpty);
jsVar('totalLeftError', sprintf($this->lang->task->error->leftEmptyAB, $this->lang->task->statusList[$task->status]));
jsVar('estimateNotEmpty', sprintf($lang->task->error->notempty, $lang->task->estimate));
jsVar('leftNotEmpty', sprintf($lang->task->error->notempty, $lang->task->left));
jsVar('teamNotEmpty', sprintf($lang->error->notempty, $lang->task->assignedTo));
jsVar('isMultiple', $isMultiple);
jsVar('taskMode', $task->mode);
if($isMultiple) jsVar('assignedToHtml', html::select('assignedTo', $teamMembers, '', "class='form-control' disabled"));

/* zin: Set variables to define control for form. */
modalHeader();
$taskModeBox = '';
if($isMultiple)
{
    $taskModeBox = formGroup
    (
        set::width('1/4'),
        set::label($lang->task->mode),
        inputGroup
        (
            set::class('no-background'),
            zget($lang->task->modeList, $task->mode),
            input
            (
                set::class('hidden'),
                set::name('mode'),
                set::value($task->mode),
            ),
        ),
    );
}

$manageTeamBox = '';
if($isMultiple)
{
    $manageTeamBox = formGroup(
        set::width('1/10'),
        setClass('items-center'),
        checkbox
        (
            set::id('multiple'),
            set::name('multiple'),
            set::text($lang->task->manageTeam),
            set::rootClass('ml-4'),
        )
    );
}

$leftBox = '';
if($task->parent != '-1')
{
    $leftBox = formGroup(
        set::width('1/3'),
        set::label($lang->task->left),
        set::name('left'),
        inputControl
        (
            to::suffix($lang->task->suffixHour),
            set::suffixWidth(20),
        ),
    );
}

$modalTeamBtn = array();
if($isMultiple)
{
    $modalTeamBtn = btn(
        set::text($lang->task->team),
        set::class('team-group hidden'),
        set::url('#modalTeam'),
        set('data-toggle', 'modal'),
    );
}

$teamData = array();
if($isMultiple)
{
    $index = 1;
    foreach($task->team as $member)
    {
        $member->id           = $index;
        $member->team         = $member->account;
        $member->teamSource   = $member->account;
        $member->teamEstimate = $member->estimate;
        $member->teamConsumed = $member->consumed;
        $member->teamLeft     = $member->left;

        $teamData[] = $member;
        $index ++;
    }
}

$rowCount = (!empty($task->team) and count($task->team) < 6) ? 6 : 1 + count($task->team);
$teamForm = array();
$i        = 1;
foreach($task->team as $member)
{
    $memberDisabled = false;
    if($member->status == 'done') $memberDisabled = true;

    $hourDisabled = $memberDisabled;
    if($task->mode == 'multi') $hourDisabled = false;

    $teamForm[] = h::tr
    (
        setClass("member member-{$member->status}"),
        set('data-estimate', (float)$member->estimate),
        set('data-consumed', (float)$member->consumed),
        set('data-left', (float)$member->left),
        h::td
        (
            setClass('team-index'),
            set::width('32px'),
            span
            (
                setClass('team-number'),
                $i
            ),
            $task->mode == 'linear' ? icon("angle-down") : null
        ),
        h::td
        (
            set::width('240px'),
            select
            (
                set::name('team[]'),
                set::value($member->account),
                set::items($members),
                set::placeholder($lang->task->assignedTo),
            ),
            input
            (
                set::type('hidden'),
                set::name('teamSource[]'),
                set::value($member->account),
            ),
            $memberDisabled ? input(
                set::type('hidden'),
                set::name('team[]'),
                set::value($member->account),
            ) : null
        ),
        h::td
        (
            inputControl
            (
                input
                (
                    set::name('teamEstimate[]'),
                    set::value((float)$member->estimate),
                    set::placeholder($lang->task->estimateAB),
                    set::readonly($hourDisabled),
                ),
                to::suffix($lang->task->suffixHour),
                set::suffixWidth(20),
            ),
        ),
        h::td
        (
            inputControl
            (
                input
                (
                    set::name('teamConsumed[]'),
                    set::value((float)$member->consumed),
                    set::placeholder($lang->task->consumed),
                    set::readonly($hourDisabled),
                ),
                to::suffix($lang->task->suffixHour),
                set::suffixWidth(20),
            ),
        ),
        h::td
        (
            setClass('required'),
            inputControl
            (
                input
                (
                    set::name('teamLeft[]'),
                    set::value((float)$member->left),
                    set::placeholder($lang->task->left),
                    set::readonly($hourDisabled),
                ),
                to::suffix($lang->task->suffixHour),
                set::suffixWidth(20),
            ),
        ),
        h::td
        (
            set::width('100px'),
            setClass('center'),
            btnGroup
            (
                set::items(array(
                    array('icon' => 'plus',  'class' => 'btn ghost btn-add text-gray', 'disabled' => $memberDisabled ? 'disabled' : ''),
                    array('icon' => 'trash', 'class' => 'btn ghost btn-delete text-gray', 'disabled' => $memberDisabled ? 'disabled' : ''),
                ))
            )
        )
    );
    $i ++;
}

for($i; $i <= $rowCount; $i ++)
{
    $teamForm[] = h::tr
    (
        setClass('member-wait'),
        h::td
        (
            setClass('team-index'),
            span
            (
                setClass("team-number"),
                $i
            ),
            $task->mode == 'linear' ? icon("angle-down") : null
        ),
        h::td
        (
            set::width('240px'),
            select
            (
                set::name('team[]'),
                set::items($members),
                set::placeholder($lang->task->assignedTo),
            ),
            input
            (
                set::type('hidden'),
                set::name('teamSource[]'),
                set::value(''),
            )
        ),
        h::td
        (
            inputControl
            (
                input
                (
                    set::name('teamEstimate[]'),
                    set::placeholder($lang->task->estimateAB),
                ),
                to::suffix($lang->task->suffixHour),
                set::suffixWidth(20),
            ),
        ),
        h::td
        (
            inputControl
            (
                input
                (
                    set::name('teamConsumed[]'),
                    set::placeholder($lang->task->consumed),
                ),
                to::suffix($lang->task->suffixHour),
                set::suffixWidth(20),
            ),
        ),
        h::td
        (
            inputControl
            (
                input
                (
                    set::name('teamLeft[]'),
                    set::placeholder($lang->task->left),
                ),
                to::suffix($lang->task->suffixHour),
                set::suffixWidth(20),
            ),
        ),
        h::td
        (
            set::width('100px'),
            setClass('center'),
            btnGroup
            (
                set::items(array(
                    array('icon' => 'plus',  'class' => 'btn ghost btn-add text-gray'),
                    array('icon' => 'trash', 'class' => 'btn ghost btn-delete text-gray'),
                ))
            )
        )
    );
}

/* ====== Define the page structure with zin widgets ====== */
formPanel
(
    $taskModeBox,
    formRow
    (
        formGroup
        (
            set::width('1/3'),
            set::label($lang->task->assignedTo),
            set::required($isMultiple),
            inputGroup
            (
                select
                (
                    set::name('assignedTo'),
                    set::items($isMultiple ? $teamMembers : $members),
                    set::value($isMultiple ? '' : $task->finishedBy),
                ),
                $modalTeamBtn,
            ),
        ),
        $manageTeamBox,
    ),
    $leftBox,
    formGroup
    (
        set::label($lang->comment),
        editor
        (
            set::name('comment'),
            set::rows('5'),
        )
    ),
    modalTrigger
    (
        modal
        (
            set::id('modalTeam'),
            set::title($lang->task->team),
            set::footerClass('flex-center'),
            set('data-backdrop', false),
            to::footer
            (
                btn
                (
                    setClass('primary btn-wide'),
                    set::id('confirmButton'),
                    set::text($lang->confirm),
                )
            ),
            h::table
            (
                setClass('table table-form'),
                set::id('teamForm'),
                $teamForm
            ),
        )
    ),
);

history();

/* ====== Render page ====== */
render();
