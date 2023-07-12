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

modalHeader();
formPanel
(
    set::submitBtnText($lang->story->activate),
    formGroup
    (
        set::name('assignedTo'),
        set::label($lang->story->assignedTo),
        set::width('1/3'),
        set::value($story->closedBy),
        set::items($users),
    ),
    empty($story->twins) ? null : formGroup
    (
        set::width('full'),
        set::label(' '),
        icon('exclamation-sign'),
        $lang->story->assignSyncTip,
    ),
    formGroup
    (
        set::label($lang->comment),
        set::name('comment'),
        set::control('editor'),
        set::rows(6),
    ),
);
hr();
history();

render();
