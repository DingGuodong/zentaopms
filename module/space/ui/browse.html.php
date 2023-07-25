<?php
declare(strict_types=1);
/**
 * The browse view file of space module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Zeng Gang<zenggang@easycorp.ltd>
 * @package     space
 * @link        https://www.zentao.net
 */
namespace zin;

$instances = initTableData($instances, $config->space->dtable->fieldList, $this->instance);

featureBar
(
    set::current($type),
    set::linkParams("spaceID=&browseType={key}"),
);

toolBar
(
    hasPriv('instance', 'create') ? item(set(array
    (
        'text'  => $lang->space->install,
        'icon'  => 'plus',
        'class' => 'btn primary',
        'url'   => createLink('space', 'createApplication'),
    ))) : null,
);

dtable
(
    set::cols($config->space->dtable->fieldList),
    set::data($instances),
    set::onRenderCell(jsRaw('window.renderInstanceList')),
    set::footPager(usePager()),
);

render();

