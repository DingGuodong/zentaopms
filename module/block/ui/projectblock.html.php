<?php
declare(strict_types=1);
/**
* The project block view file of block module of ZenTaoPMS.
* @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
* @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
* @author      Yuting Wang <wangyuting@easycorp.ltd>
* @package     block
* @link        https://www.zentao.net
*/

namespace zin;

jsVar('delayInfo', $lang->project->delayInfo);

foreach($projects as $project) $project->consumed .= $lang->execution->workHourUnit;
if(!$longBlock)
{
    unset($config->block->project->dtable->fieldList['PM']);
    unset($config->block->project->dtable->fieldList['status']);
    unset($config->block->project->dtable->fieldList['consumed']);
    unset($config->block->project->dtable->fieldList['storyCount']);
    unset($config->block->project->dtable->fieldList['leftTasks']);
    unset($config->block->project->dtable->fieldList['leftBugs']);
}

panel
(
    set('class', 'project-block ' . ($longBlock ? 'block-long' : 'block-sm')),
    set('headingClass', 'border-b'),
    to::heading
    (
        div
        (
            set('class', 'panel-title'),
            span($block->title),
        )
    ),
    to::headingActions
    (
        a
        (
            set('class', 'text-gray'),
            set('href', createLink('project', 'browse', 'program=0&browseType=' . $block->params->type)),
            $lang->more,
            icon('caret-right')
        )
    ),
    dtable
    (
        set::height(320),
        set::bordered(false),
        set::horzScrollbarPos('inside'),
        set::cols(array_values($config->block->project->dtable->fieldList)),
        set::data(array_values($projects)),
        set::userMap($users),
        set::onRenderCell(jsRaw('window.onRenderProjectNameCell')),
    )
);

render();
