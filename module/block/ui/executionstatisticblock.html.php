<?php
declare(strict_types=1);
/**
* The execution statistic block view file of block module of ZenTaoPMS.
* @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
* @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
* @author      Yuting Wang <wangyuting@easycorp.ltd>
* @package     block
* @link        https://www.zentao.net
*/

namespace zin;

$active    = isset($params['active']) ? $params['active'] : key($executions);
$execution = new stdclass();
$execution->progress          = '';
$execution->doneStory         = '';
$execution->totalStory        = '';
$execution->totalTask         = '';
$execution->undoneTask        = '';
$execution->yesterdayDoneTask = '';

$items = array();
foreach($executions as $executionItem)
{
    $params  = helper::safe64Encode("module={$block->module}&projectID={$currentProjectID}&active={$executionItem->id}");
    $items[] = array
    (
        'id'        => $executionItem->id,
        'text'      => $executionItem->name,
        'url'       => createLink('execution', 'task', "executionID={$executionItem->id}"),
        'activeUrl' => createLink('block', 'printBlock', "blockID={$block->id}&params={$params}")
    );
    if($executionItem->id == $active) $execution = $executionItem;
}

$projectItems = array();
$projectItems[] = array('value' => '0', 'text' => $lang->block->executionstatistic->allProject, 'data-url' => createLink('block', 'printBlock', "blockID={$block->id}&params=" . helper::safe64Encode("module={$block->module}")), 'data-on' => 'click', 'data-do' => "loadBlock('$block->id', options.url)");
foreach($projects as $projectID => $projectName)
{
    $url = createLink('block', 'printBlock', "blockID={$block->id}&params=" . helper::safe64Encode("module={$block->module}&project={$projectID}"));
    $projectItems[] = array('value' => $projectID, 'text' => $projectName, 'data-url' => $url, 'data-on' => 'click', 'data-do' => "loadBlock('$block->id', options.url)");
}

/* 燃尽图。Burn chart. */
$burn = div
(
    div(setClass('font-bold mb-2'), $lang->block->executionstatistic->burn),
    echarts
    (
        set::color(array('#2B80FF', '#D2D6E5')),
        set::width('100%'),
        set::height(140),
        set::grid(array('left' => '30px', 'top' => '30px', 'right' => '10px', 'bottom' => '0',  'containLabel' => true)),
        set::legend(array('show' => true, 'right' => '0')),
        set::xAxis(array('type' => 'category', 'data' => $chartData['labels'], 'boundaryGap' => false, 'splitLine' => array('show' => false), 'axisTick' => array('alignWithLabel' => true, 'interval' => '0'), 'axisLabel' => array('rotate' => 45))),
        set::yAxis(array('type' => 'value', 'name' => 'H', 'minInterval' => $chartData['baseLine'][0], 'splitLine' => array('show' => false), 'axisLine' => array('show' => true, 'color' => '#DDD'))),
        set::series
        (
            array
            (
                array
                (
                    'type' => 'line',
                    'name' => $lang->block->productstatistic->done,
                    'data' => $chartData['burnLine'],
                    'emphasis' => array('label' => array('show' => true))
                ),
                array
                (
                    'type' => 'line',
                    'name' => $lang->block->productstatistic->opened,
                    'data' => $chartData['baseLine'],
                    'emphasis' => array('label' => array('show' => true))
                )
            )
        )
    )
);

/* 进度环。Progress circle. */
$progressCircle = div
(
    setClass('w-full'),
    row
    (
        setClass('font-bold items-center gap-1 mb-2'),
        $lang->block->executionstatistic->progress,
        icon
        (
            'help',
            setClass('opacity-50 text-sm cursor-pointer'),
            toggle::popover(array
            (
                'content'   => array('html' => $lang->block->tooltips['executionProgress']),
                'placement' => 'bottom',
                'width'     => 400,
                'trigger'   => 'hover',
                'closeBtn'  => false,
                'className' => 'leading-5'
            ))
        )
    ),
    div
    (
        setClass('w-full center'),
        progressCircle
        (
            setClass('relative w-28 h-28 hide-before-init opacity-0 transition-opacity'),
            set::percent($execution->progress),
            set::size(112),
            set::text(false),
            set::circleWidth(0.06),
            div(span(setClass('text-2xl font-bold'), $execution->progress), '%')
        )
    )
);

/* 工时信息。 Hours info. */
$hoursInfo = row
(
    setClass('justify-evenly w-full py-1'),
    cell
    (
        setClass('flex-1 text-center'),
        div
        (
            span(setClass('text-lg num font-bold'), !empty($execution->totalEstimate) ? $execution->totalEstimate : 0),
            span(setClass('text-gray'), 'h')
        ),
        div
        (
            setClass('text-sm text-gray'),
            $lang->block->executionstatistic->totalEstimate
        )
    ),
    cell
    (
        setClass('flex-1 text-center'),
        div
        (
            span(setClass('text-lg num font-bold'), !empty($execution->totalConsumed) ? $execution->totalConsumed : 0),
            span(setClass('text-gray'), 'h')
        ),
        div
        (
            setClass('text-sm text-gray'),
            $lang->block->executionstatistic->totalConsumed
        )
    ),
    cell
    (
        setClass('flex-1 text-center'),
        div
        (
            span(setClass('text-lg num font-bold'), !empty($execution->totalLeft) ? $execution->totalLeft : 0),
            span(setClass('text-gray'), 'h')
        ),
        div
        (
            setClass('text-sm text-gray'),
            $lang->block->executionstatistic->totalLeft
        )
    )
);

/* 任务故事信息。Task story info. */
$taskStoryInfo = col
(
    setClass('gap-2'),
    row
    (
        div(setClass('w-12 flex-none'), strong($lang->block->executionstatistic->task)),
        row
        (
            setClass('flex-auto'),
            cell
            (
                setClass('w-1/3'),
                span(setClass('text-sm text-gray'), $lang->block->executionstatistic->totalTask),
                strong(setClass('num ml-2'), $execution->totalTask)
            ),
            cell
            (
                setClass('w-1/3'),
                span(setClass('text-sm text-gray'), $lang->block->executionstatistic->undoneTask),
                strong(setClass('num ml-2'), $execution->undoneTask)
            ),
            cell
            (
                setClass('w-1/3'),
                span(setClass('text-sm text-gray'), $lang->block->executionstatistic->yesterdayDoneTask),
                strong(setClass('num ml-2'), $execution->yesterdayDoneTask)
            )
        )
    ),
    row
    (
        div(setClass('w-12 flex-none'), strong($lang->block->executionstatistic->story)),
        row
        (
            setClass('flex-auto'),
            cell
            (
                setClass('w-1/3'),
                span(setClass('text-sm text-gray'), $lang->block->executionstatistic->totalStory),
                strong(setClass('num ml-2'), $execution->totalStory)
            ),
            cell
            (
                setClass('w-1/3'),
                span(setClass('text-sm text-gray'), $lang->block->executionstatistic->doneStory),
                strong(setClass('num ml-2'), $execution->doneStory)
            )
        )
    )
);

$blockView = null;
if($longBlock)
{
    $blockView = row
    (
        setClass('gap-4 h-full items-center px-4'),
        col
        (
            setClass('gap-6 w-2/5 flex-none items-center pr-2'),
            $progressCircle,
            $hoursInfo
        ),
        col
        (
            setClass('gap-0 flex-auto'),
            $burn,
            $taskStoryInfo
        )
    );
}
else
{
    $blockView = col
    (
        setClass('gap-8 px-6 pt-10'),
        row
        (
            setClass('gap-2 items-center w-full'),
            cell
            (
                setClass('gap-2 w-2/5 flex-none items-center'),
                $progressCircle,
            ),
            cell
            (
                setClass('flex-auto min-w-0'),
                $burn
            )
        ),
        $hoursInfo,
        $taskStoryInfo
    );
}

statisticBlock
(
    to::titleSuffix
    (
        picker
        (
            setClass('font-normal gray-400-outline ml-3 text-base circle filter-project-pricker'),
            set::width('120px'),
            set::placeholder($lang->block->filterProject),
            set::name('project'),
            set::items($projectItems),
            set::value(isset($projects[$currentProjectID]) ? $currentProjectID : 0)
        )
    ),
    set::block($block),
    set::active($active),
    set::moreLink(createLink('execution', 'all', 'status=' . zget($block->params, 'type', ''))),
    set::items($items),
    $blockView,
);
