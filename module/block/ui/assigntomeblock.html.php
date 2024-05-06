<?php
declare(strict_types=1);
/**
* The assigntome view file of block module of ZenTaoPMS.
* @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
* @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
* @author      Yuting Wang <wangyuting@easycorp.ltd>
* @package     block
* @link        https://www.zentao.net
*/

namespace zin;
jsVar('moreLabel', $lang->more);
jsVar('todayLabel', $lang->today);
jsVar('yesterdayLabel', $lang->yesterday);

$blockNavCode = 'nav-' . uniqid();

$menus = array();
$moreMenus = array();
foreach($hasViewPriv as $type => $bool)
{
    $data = $type == 'story' ? $stories : ${"{$type}s"};
    if($type != 'todo' && empty($data)) unset($hasViewPriv[$type]);
}

$index = 1;
$count = count($hasViewPriv);
foreach($hasViewPriv as $type => $bool)
{
    $selected = key($hasViewPriv);
    if(($longBlock && $count > 5 && $index > 5) || (!$longBlock && $count > 1 && $index > 1))
    {
        $moreMenus[] = array('text' => $type == 'review' ? $lang->my->audit : zget($lang->block->availableBlocks, $type), 'data-toggle' => 'tab', 'href' => "#assigntome{$type}Tab{$blockNavCode}");
    }
    else
    {
        $menus[]  = li
        (
            setClass('nav-item nav-switch'),
            a
            (
                setClass($type == $selected ? 'active' : ''),
                setData(array('toggle' => 'tab')),
                set('href', "#assigntome{$type}Tab{$blockNavCode}"),
                $type == 'review' ? $lang->my->audit : zget($lang->block->availableBlocks, $type)
            )
        );
    }
    $index ++;
}

if(($longBlock && $count > 5) || (!$longBlock && $count > 1))
{
    $menus[]  = li
    (
        setClass('nav-item nav-switch'),
        a
        (
            setData(array('toggle' => 'dropdown')),
            set('href', "#assigntomeMenuMore{$blockNavCode}"),
            span($lang->more),
            icon('caret-down')
        ),
        menu
        (
            setID("assigntomeMenuMore{$blockNavCode}"),
            setClass('dropdown-menu'),
            set::items($moreMenus)
        )
    );
}

$contents = array();
foreach($hasViewPriv as $type => $bool)
{
    $configType = $type;
    if($type == 'testcase')    $configType = 'case';
    if($type == 'requirement') $configType = 'story';

    $data = $type == 'story' ? $stories : ${"{$type}s"};

    if(empty($config->block->{$configType}->dtable->fieldList)) continue;
    if(!$longBlock && !empty($config->block->{$configType}->dtable->short->fieldList)) $config->block->{$configType}->dtable->fieldList = $config->block->{$configType}->dtable->short->fieldList;

    if($type == 'review')
    {
        $statusList = array();
        foreach($data as $review)
        {
            $reviewType = $review->type;
            if($reviewType == 'projectreview') $reviewType = 'review';

            $review->module = $reviewType;

            $typeName = '';
            if(isset($lang->{$review->type}->common)) $typeName = $lang->{$review->type}->common;
            if($reviewType == 'story')                $typeName = $review->storyType == 'story' ? $lang->SRCommon : $lang->URCommon;
            if($review->type == 'projectreview')      $typeName = $lang->project->common;

            if(isset($lang->$reviewType->statusList)) $statusList = array_merge($statusList, $lang->$reviewType->statusList);
            if($reviewType == 'attend')               $statusList = array_merge($statusList, $lang->attend->reviewStatusList);
            if(!in_array($reviewType, array('story', 'testcase', 'feedback', 'review')) and strpos(",{$config->my->oaObjectType},", ",$reviewType,") === false) $statusList = array_merge($statusList, $lang->approval->nodeList);

            $review->type = $typeName;
        }
        $config->block->review->dtable->fieldList['status']['statusMap'] = $statusList;
    }
    if($type == 'requirement') $config->block->story->dtable->fieldList['title']['title']    = str_replace($lang->story->story, $lang->story->requirement, $lang->story->title);
    if($type == 'ticket')      $config->block->ticket->dtable->fieldList['product']['map']   = $products;
    if($type == 'feedback')    $config->block->feedback->dtable->fieldList['product']['map'] = $products;
    if($type == 'meeting')     $config->block->meeting->dtable->fieldList['dept']['map']     = $depts;

    $selected  = key($hasViewPriv);
    $contents[] = div
    (
        setClass("assigntome-{$type} tab-pane " . ($type == $selected ? 'active' : '')),
        setID("assigntome{$type}Tab{$blockNavCode}"),
        dtable
        (
            set::height(318),
            set::bordered(false),
            $type == 'todo'  || $type == 'task' ? set::fixedLeftWidth('0.44') : '',
            set::horzScrollbarPos('inside'),
            set::onRenderCell(jsRaw('window.renderCell')),
            set::cols(array_values($config->block->{$configType}->dtable->fieldList)),
            set::data(array_values($data)),
            $type == 'bug' ? set::priList($lang->{$type}->priList) : null,
            $type == 'bug' ? set::severityList($lang->{$type}->severityList) : null,
            set::userMap($users)
        )
    );
}

blockPanel
(
    setClass('assigntome-block list-block'),
    to::heading
    (
        ul
        (
            setClass('nav'),
            $menus,
            on::show()->call('handleAssignToMeTabShow')
        )
    ),
    div($contents),
    h::css
    (
        '.block-assigntome .panel-heading .panel-title {overflow: hidden; text-overflow: clip; white-space: nowrap; width : 40%}',
        '.block-assigntome .nav > .nav-item > a {padding: 0 16px; border-radius: 4px; height: 28px; color: var(--color-gray-700)}',
        '.block-assigntome .nav > .nav-item > a.active {font-weight: bold; color: var(--color-gray-900); background: var(--color-primary-50)}'
    )
);

render();
