<?php
declare(strict_types=1);
/**
 * The task block view file of block module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tingting Dai <daitingting@easycorp.ltd>
 * @package     block
 * @link        https://www.zentao.net
 */
namespace zin;

if(!$longBlock)
{
    unset($config->block->task->dtable->fieldList['status']);
    unset($config->block->task->dtable->fieldList['deadline']);
    unset($config->block->task->dtable->fieldList['estimate']);
}

panel
(
    set::class('task-block' . $longBlock ? 'block-long' : 'block-sm'),
    set::title($block->title),
    set('headingClass', 'border-b'),
    to::headingActions
    (
        a
        (
            set('class', 'text-gray'),
            set('href', $block->moreLink),
            $lang->more,
            icon('caret-right')
        )
    ),
    dtable
    (
        set::className('borderless'),
        set::fixedLeftWidth('50%'),
        set::cols(array_values($config->block->task->dtable->fieldList)),
        set::data(array_values($tasks)),
        set::horzScrollbarPos('inside'),
    ),
);

render();
