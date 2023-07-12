<?php
declare(strict_types=1);
/**
 * The start view file of project module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     project
 * @link        https://www.zentao.net
 */
namespace zin;

modalHeader();
formPanel
(
    formGroup
    (
        set::width('1/2'),
        set::label($lang->project->realBegan),
        set::name('realBegan'),
        set::control('date'),
        set::value(helper::today()),
    ),
    formGroup
    (
        set::label($lang->comment),
        editor
        (
            set::name('comment'),
            set::rows('6'),
        )
    ),
);
hr();
history();

/* ====== Render page ====== */
render();
