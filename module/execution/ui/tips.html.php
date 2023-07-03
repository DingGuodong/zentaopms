<?php
declare(strict_types=1);
/**
 * The yyy view file of xxx module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     xxx
 * @link        https://www.zentao.net
 */
namespace zin;

set::class('pt-6');
set::closeBtn(array('url' => createLink('execution', 'create'), 'class' => 'ghost'));
div
(
    set::class('flex items-center'),
    icon('check-circle text-success icon-2x mr-2'),
    span
    (
        set::class('article-h2 tip-title'),
        $lang->execution->afterInfo
    )
);

div
(
    set::class('my-4'),
    btn
    (
        set::class('mr-4 tipBtn ml-1'),
        $lang->execution->setTeam,
        set('data-url', createLink('execution', 'team', "executionID={$executionID}")),
    ),
    $execution->lifetime != 'ops' ? btn
    (
        set::class('mr-4 tipBtn'),
        $lang->execution->linkStory,
        set('data-url', createLink('execution', 'linkstory', "executionID=$executionID")),
    ) : null,
    btn
    (
        set::class('mr-4 tipBtn'),
        $lang->execution->createTask,
        set('data-url', createLink('task', 'create', "execution=$executionID")),
    ),
    btn
    (
        set::class('mr-4 tipBtn'),
        $lang->execution->goback,
        set('data-url', createLink('execution', 'task', "executionID={$executionID}")),
    ),
    btn
    (
        set::class('tipBtn'),
        $lang->execution->gobackExecution,
        set('data-url', createLink('execution', 'all')),
    ),
);

/* ====== Render page ====== */
render();
