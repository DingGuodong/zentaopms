<?php
declare(strict_types=1);
/**
 * The backup view file of upgrade module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tingting Dai <daitingting@easycorp.ltd>
 * @package     upgrade
 * @link        https://www.zentao.net
 */
namespace zin;

set::zui(true);

div
(
    set::id('main'),
    div
    (
        set::id('mainContent'),
        panel
        (
            set::style(array('margin' => '0 auto', 'width' => '600px')),
            div
            (
                set::className('article-h1 mb-4'),
                icon
                (
                    'exclamation-sign',
                    set::size('2x'),
                    set::className('text-danger mr-2')
                ),
                $lang->upgrade->warnning
            ),
            div
            (
                set::style(array('background-color' => 'var(--color-gray-100)')),
                set::className('p-5 mb-4 space-y-2'),
                html($lang->upgrade->warnningContent),
            ),
            div
            (
                set::className('text-center'),
                btn
                (
                    on::click("self.location.href='" . createLink('upgrade', 'consistency') . "'"),
                    set::type('primary'),
                    set::className('px-10'),
                    set('disabled', 'disabled'),
                    $lang->upgrade->common,
                )
            )
        )
    )
);

render('pagebase');
