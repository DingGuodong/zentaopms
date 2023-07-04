<?php
declare(strict_types=1);
/**
 * The bugblock view file of block module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tingting Dai <daitingting@easycorp.ltd>
 * @package     block
 * @link        https://www.zentao.net
 */
namespace zin;

if(!$longBlock)
{
    unset($config->block->bug->dtable->fieldList['id']);
    unset($config->block->bug->dtable->fieldList['pri']);
    unset($config->block->bug->dtable->fieldList['confirmed']);
    unset($config->block->bug->dtable->fieldList['deadline']);
}

panel
(
    set::title($block->title),
    set('class', 'bug-block ' . ($longBlock ? 'block-long' : 'block-sm')),
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
        set::cols(array_values($config->block->bug->dtable->fieldList)),
        set::data(array_values($bugs)),
        set::horzScrollbarPos('inside'),
    ),
);

render();
