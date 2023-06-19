<?php
declare(strict_types=1);
/**
 * The assignTo view file of bug module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     bug
 * @link        https://www.zentao.net
 */
namespace zin;

include 'common.html.php';

jsVar('page', 'assignedto');

/* zin: Define the form in main content. */
formPanel
(
    setCommonProps($bug, $lang->bug->assignTo),
    formGroup
    (
        set::width('1/3'),
        set::name('assignedTo'),
        set::label($lang->bug->assignedTo),
        set::value($bug->assignedTo),
        set::items($users),
    ),
    formGroup
    (
        set::label($lang->bug->mailto),
        set::name('mailto[]'),
        set::value(str_replace(' ', '', $bug->mailto ?: '')),
        set::multiple(true),
        set::items($users),
    ),
    formGroup
    (
        set::label($lang->comment),
        set::name('comment'),
        set::control('editor'),
        set::rows(6),
    ),
);

history();

render('modalDialog');
