<?php
declare(strict_types=1);
/**
* The dashboard view file of block module of ZenTaoPMS.
* @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
* @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
* @author      Yuting Wang <wangyuting@easycorp.ltd>
* @package     block
* @link        https://www.zentao.net
*/

namespace zin;

$mainBlocks = array();
foreach($longBlocks as $index => $block)
{
    $mainBlocks[] =
    div
    (
        set('class', "panel rounded shadow ring-0 canvas block-item {$block->code}" . (isset($block->params->color) ? 'panel-' . $block->params->color : '')),
        set('data-id', $block->id),
        set('data-name', $block->title),
        set('data-order', $block->order),
        set('data-url', $block->blockLink),
        div
        (
            set('class', 'panel-heading'),
            div
            (
                set('class', 'panel-title'),
                $block->title
            ),
            nav
            (
                set('class', 'panel-actions nav nav-default'),
                dropdown
                (
                    icon('ellipsis-v'),
                    set::items
                    ([
                        ['text' => $lang->block->refresh, 'url' => ''],
                        ['text' => $lang->edit, 'url' => $this->createLink('block', 'edit', "blockID=$block->id"), 'data-toggle' => 'modal'],
                        ['text' => $lang->block->hidden, 'url' => $this->createLink('block', 'delete', "blockID=$block->id")],
                        ['text' => $lang->block->closeForever, 'url' => $this->createLink('block', 'close', "blockID=$block->id")],
                        ['text' => $lang->block->createBlock, 'url' => $this->createLink('block', 'create', "dashboard=$dashboard"), 'data-toggle' => 'modal'],
                        ['text' => $lang->block->reset, 'url' => $this->createLink('block', 'reset', "dashboard=$dashboard")],
                    ]),
                )
            )
        ),
        div
        (
            set('id', 'block' . $block->id),
            set('class', 'panel-body scrollbar-hover')
        )
    );
}

$sideBlocks = array();
foreach($shortBlocks as $index => $block)
{
    $sideBlocks[] =
    div
    (
        set('id', 'block' . $block->id),
        set('class', "panel rounded shadow ring-0 canvas {$block->code}" . (isset($block->params->color) ? 'panel-' . $block->params->color : '')),
        set('data-id', $block->id),
        set('data-name', $block->title),
        set('data-order', $block->order),
        set('data-url', $block->blockLink),
        div
        (
            set('class', 'panel-heading'),
            div
            (
                set('class', 'panel-title'),
                $block->title
            ),
            nav
            (
                set('class', 'panel-actions nav nav-default'),
                dropdown
                (
                    icon('ellipsis-v'),
                    set::items
                    ([
                        ['text' => $lang->block->refresh, 'url' => ''],
                        ['text' => $lang->edit, 'url' => $this->createLink('block', 'edit', "blockID=$block->id"), 'data-toggle' => 'modal'],
                        ['text' => $lang->block->hidden, 'url' => $this->createLink('block', 'delete', "blockID=$block->id&type=hidden")],
                        ['text' => $lang->block->createBlock, 'url' => $this->createLink('block', 'create', "dashboard=$dashboard"), 'data-toggle' => 'modal'],
                        ['text' => $lang->block->reset, 'url' => $this->createLink('block', 'reset', "dashboard=$dashboard")],
                    ]),
                )
            )
        ),
        div
        (
            set('class', 'panel-body scrollbar-hover')
        )
    );
}

div
(
    set('class', 'dashboard'),
    set('id', 'dashboard'),
    div
    (
        set('class', 'row'),
        div
        (
            set('class', 'col-main'),
            $mainBlocks
        ),
        div
        (
            set('class', 'col-side'),
            $sideBlocks
        )
    )
);

render();
