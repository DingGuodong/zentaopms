<?php
declare(strict_types=1);
/**
* The welcome view file of block module of ZenTaoPMS.
* @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
* @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
* @author      LiuRuoGu <liuruogu@easycorp.ltd>
* @package     block
* @link        https://www.zentao.net
*/

namespace zin;

$doneReview = rand(0, 100);
$finishTask = rand(0, 100);
$fixBug     = rand(0, 100);

if($doneReview > $finishTask && $doneReview > $fixBug)
{
    $honorary = 'review';
}
else if($finishTask > $doneReview && $finishTask > $fixBug)
{
    $honorary = 'task';
}
else
{
    $honorary = 'bug';
}

$blockNavCode = 'nav-' . uniqid();
panel
(
    set('class', 'welcome-block'),
    to::heading
    (
        div
        (
            set('class', 'panel-title flex w-full'),
            cell
            (
                set('width', '22%'),
                set('class', 'center'),
                span($todaySummary),

            ),
            cell
            (
                set::class('pr-8'),
                span(set('class', 'text-sm font-normal'), html(sprintf($lang->block->summary->welcome, $usageDays, $doneReview, $finishTask, $fixBug)))
            )
        )
    ),
    div
    (
        set('class', 'flex h-32'),
        cell
        (
            set('width', '22%'),
            set('align', 'center'),
            set::class('border-right py-2'),
            center
            (
                set('class', 'font-bold'),
                sprintf($lang->block->welcomeList[$welcomeType], $app->user->realname)
            ),
            center
            (
                set::class('my-1'),
                center
                (
                    set::class('rounded-full avatar-border-one'),
                    center
                    (
                        set::class('rounded-full avatar-border-two'),
                        userAvatar
                        (
                            set::class('welcome-avatar ellipsis'),
                            set('user', $this->app->user)
                        )
                    )
                )
            ),
            center(span(set('class', 'label circle honorary text-xs'), $lang->block->honorary[$honorary]))
        ),
        cell
        (
            set('width', '78%'),
            set::class('px-8'),
            tabs
            (
                tabPane
                (
                    set::key("reviewByMe_$blockNavCode"),
                    set::title($lang->block->welcome->reviewByMe),
                    div
                    (
                        set::class('flex justify-around text-center'),
                        getMeasureItem($reviewByMe)
                    )
                ),
                tabPane
                (
                    set::key("assignToMe_$blockNavCode"),
                    set::title($lang->block->welcome->assignToMe),
                    set::active(true),
                    div
                    (
                        set::class('flex justify-around text-center'),
                        getMeasureItem($assignToMe)
                    )
                )
            )
        )
    )
);

render();

function getMeasureItem($data)
{
    global $lang;

    $welcomeLabel = array_merge($lang->block->welcome->assignList, $lang->block->welcome->reviewList);

    $items = array();
    foreach($data as $key => $info)
    {
        if(count($items) >= 5) break;
        $items[] = cell
        (
            div
            (
                set('class', 'text-3xl text-primary font-bold h-40px'),
                a(set('href', $info['href']), $info['number'])
            ),
            div(zget($welcomeLabel, $key, '')),
            !empty($info['delay']) ? div
            (
                set('class', 'label danger-pale circle size-sm'),
                $lang->block->delay . ' ' . $info['delay']
            ) : null
        );
    }
    return $items;
}
