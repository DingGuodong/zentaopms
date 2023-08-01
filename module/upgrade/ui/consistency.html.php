<?php
declare(strict_types=1);
/**
 * The consistency view file of upgrade module of ZenTaoPMS.
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
        set::style(array('margin' => '50px auto 0', 'width' => '800px')),
        set::class('bg-white p-4'),
        div
        (
            set::class('article-h1 mb-4'),
            $lang->upgrade->consistency
        ),
        div
        (
            set::class('border p-4 mb-4'),
            set::style(array('background-color' => 'var(--color-gray-100)')),
            div
            (
                set::class('article-h3 mb-2'),
                $lang->upgrade->noticeSQL
            ),
            div
            (
                set::class('text-danger leading-loose'),
                html("SET @@sql_mode= '';<br />"),
                html(nl2br($alterSQL)),
            )
        ),
        div
        (
            set::class('text-center'),
            btn
            (
                on::click('loadCurrentPage'),
                set::type('primary'),
                set::class('px-10'),
                $lang->refresh,
            )
        )
    )
);

render('pagebase');

