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

panel
(
    set('title', 'welcome'),
    set('class', 'welcome-block'),
    div
    (
        setClass('flex'),
        col
        (
            setStyle(['width' => '20%']),
            set('align', 'center'),
            set('class', 'border-right p-3'),
            center
            (
                set('class', 'font-bold'),
                sprintf($lang->block->welcomeList[$welcomeType], $app->user->realname)
            ),
            center
            (
                set('class', 'rounded-full avatar-border-one m-5'),
                center
                (
                    set('class', 'rounded-full avatar-border-two'),
                    userAvatar
                    (
                        set('size', 'lg'),
                        set('class', 'welcome-avatar'),
                        set('user', $this->app->user)
                    )
                )
            )
        ),
        cell
        (
            set('width', '45%'),
            set('class', 'border-right p-3'),
            div
            (
                set('class', 'font-bold'),
                '待我评审：'
            ),
            div
            (
                setClass('flex justify-around pt-6'),
                col
                (
                    set('class', 'text-center'),
                    span
                    (
                        set('class', 'tile-amount text-primary'),
                        58
                    ),
                    span('研发需求数')
                ),
                col
                (
                    set('class', 'text-center'),
                    span
                    (
                        set('class', 'tile-amount text-primary'),
                        39
                    ),
                    span('反馈数')
                ),
                col
                (
                    set('class', 'text-center'),
                    span
                    (
                        set('class', 'tile-amount text-primary'),
                        17
                    ),
                    span('研发需求数')
                )
            )
        ),
        cell
        (
            set('width', '35%'),
            set('class', 'border-right p-3'),
            div
            (
                set('class', 'font-bold'),
                '待我评审：'
            ),
            div
            (
                setClass('flex justify-around pt-6'),
                col
                (
                    set('class', 'text-center'),
                    span
                    (
                        set('class', 'tile-amount text-primary'),
                        16
                    ),
                    span('任务数'),
                    span
                    (
                        set('class', 'rounded-full welcome-label-delay px-1 text-sm'),
                        '延期 3'
                    )
                ),
                col
                (
                    set('class', 'text-center'),
                    span
                    (
                        set('class', 'tile-amount text-primary'),
                        30
                    ),
                    span('BUG数')
                )
            )
        )
    )
);

render('|fragment');
