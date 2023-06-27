<?php
declare(strict_types=1);
/**
* The UI file of story module of ZenTaoPMS.
*
* @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
* @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
* @author      Wang Yidong <yidong@easycorp.ltd>
* @package     story
* @link        https://www.zentao.net
*/

namespace zin;

data('title', $lang->story->assignTo);
modalHeader();
formPanel
(
    set::submitBtnText($lang->story->assignTo),
    formGroup
    (
        set::name('assignedTo'),
        set::label($lang->story->assign),
        set::width('1/3'),
        set::strong(false),
        set::value($story->assignedTo),
        set::items($users),
    ),
    empty($story->twins) ? null : formGroup
    (
        set::width('full'),
        set::label(' '),
        icon('exclamation-sign'),
        $lang->story->assignSyncTip,
    ),
    input(set::type('hidden'), set::name('status'), set::value($story->status)),
    formGroup
    (
        set::label($lang->comment),
        set::name('comment'),
        set::control('editor'),
        set::rows(6),
    ),
);

history();

render();
