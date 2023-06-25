<?php
declare(strict_types=1);
/**
 * The manageMembers view file of execution module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     execution
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('users', $users);
jsVar('roles', $roles);
jsVar('team2Import', $team2Import);
jsVar('executionID', $execution->id);

/* zin: Define the set::module('team') feature bar on main menu. */
$copyTeamBox = '';
if(count($teams2Import) != 1)
{
    $copyTeamBox = div
        (
            setClass('select-team-box ml-4'),
            span
            (
                set::class('flex items-center team-title'),
                $lang->execution->copyTeam
            ),
            select
            (
                set::name('execution'),
                set::value($team2Import),
                set::items($teams2Import),
                on::change('choseTeam2Copy'),
            ),
        );
}
featureBar
(
    set::current('all'),
    set::linkParams("executionID={$execution->id}"),
    div
    (
        setClass('select-dept-box ml-4'),
        span
        (
            set::class('flex items-center dept-title'),
            $lang->execution->selectDept
        ),
        select
        (
            set::id('dept'),
            set::name('dept'),
            set::value($dept),
            set::items($depts),
            on::change('setDeptUsers'),
        ),
        $copyTeamBox,
    ),
);

$memberTR = array();
$i        = 0;
foreach($teamMembers as $member)
{
    if($member->memberType == 'dept' && !isset($users[$member->account])) continue;
    $memberTR[] = h::tr
        (
            $member->memberType == 'default' ? h::td(
                input
                (
                    set::id("realnames{$i}"),
                    set::name("realnames[$i]"),
                    set::value($member->realname),
                    set::readonly(true),
                ),
                input
                (
                    set::id("accounts{$i}"),
                    set::name("accounts[$i]"),
                    set::value($member->account),
                    set::type('hidden'),
                ),
            ) : h::td(
                select
                (
                    set::id("account{$i}"),
                    set::name("accounts[$i]"),
                    set::value($member->account),
                    set::items($users),
                    set('data-max-list-count', $config->maxCount),
                    set('onchange', "setRole(this.value, '{$i}')"),
                ),
            ),
            h::td
            (
                input
                (
                    set::id("role{$i}"),
                    set::name("roles[$i]"),
                    set::value($member->role),
                )
            ),
            h::td
            (
                input
                (
                    set::id("days{$i}"),
                    set::name("days[$i]"),
                    set::value($member->days),
                )
            ),
            h::td
            (
                input
                (
                    set::id("hours{$i}"),
                    set::name("hours[$i]"),
                    set::value($member->hours),
                )
            ),
            h::td
            (
                radioList
                (
                    set::id("limited{$i}"),
                    set::name("limited[$i]"),
                    set::items($lang->team->limitedList),
                    set::value($member->limited),
                    set::inline(true),
                )
            ),
            h::td
            (
                set::class('actions-list'),
                btnGroup
                (
                    set::items(array(
                        array('class' => 'btn btn-link text-gray', 'icon' => 'plus', 'onclick' => 'addItem(this)'),
                        array('class' => 'btn btn-link text-gray', 'icon' => 'trash', 'onclick' => 'deleteItem(this)'),
                    ))
                )
            )
        );

    if(in_array($member->memberType, array('default', 'dept'))) unset($users[$member->account]);
    $i ++;
}

jsVar('+itemIndex', $i);

$i = '%i%';
h::table
(
    set::class('hidden'),
    set::id('addItem'),
    h::tr(
        h::td(
            select
            (
                set::id("account{$i}"),
                set::name("accounts[$i]"),
                set::items($users),
                set('data-max-list-count', $config->maxCount),
                set('onchange', "setRole(this.value, '{$i}')"),
            ),
        ),
        h::td
        (
            input
            (
                set::id("role{$i}"),
                set::name("roles[$i]"),
            )
        ),
        h::td
        (
            input
            (
                set::id("days{$i}"),
                set::name("days[$i]"),
                set::value($execution->days),
            )
        ),
        h::td
        (
            input
            (
                set::id("hours{$i}"),
                set::name("hours[$i]"),
                set::value($config->execution->defaultWorkhours),
            )
        ),
        h::td
        (
            radioList
            (
                set::id("limited{$i}"),
                set::name("limited[$i]"),
                set::items($lang->team->limitedList),
                set::value('no'),
                set::inline(true),
            )
        ),
        h::td
        (
            set::class('actions-list'),
            btnGroup
            (
                set::items(array(
                    array('class' => 'btn btn-link text-gray', 'icon' => 'plus', 'onclick' => 'addItem(this)'),
                    array('class' => 'btn btn-link text-gray', 'icon' => 'trash', 'onclick' => 'deleteItem(this)'),
                ))
            )
        )
    )
);

div
(
    setClass('main-content'),
    form
    (
        setClass('main-form'),
        set::id('teamForm'),
        h::table
        (
            set::class('table table-form'),
            h::thead
            (
                h::tr
                (
                    h::th
                    (
                        $lang->team->account,
                        set::width('240px'),
                    ),
                    h::th
                    (
                        $lang->team->role,
                        set::width('240px'),
                    ),
                    h::th
                    (
                        $lang->team->days,
                        set::width('76px'),
                    ),
                    h::th
                    (
                        $lang->team->hours,
                        set::width('136px'),
                    ),
                    h::th
                    (
                        $lang->team->limited,
                        set::width('96px'),
                    ),
                    h::th
                    (
                        $lang->actions,
                        set::width('48px'),
                    ),
                ),
            ),
            h::tbody
            (
                $memberTR
            )
        )
    ),
);

/* ====== Render page ====== */
render();
