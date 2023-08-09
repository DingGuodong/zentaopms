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

$statusMap  = array();
$canInstall = hasPriv('instance', 'create');

foreach($instances as $instance) if(!$instance->externalID) $statusMap[$instance->id] = $instance->status;
jsVar('statusMap', $statusMap);
jsVar('idList',    array_keys($statusMap));

$instances = initTableData($instances, $config->space->dtable->fieldList, $this->instance);

featureBar
(
    set::current($browseType),
    set::linkParams("spaceID=&browseType={key}"),
);

toolBar
(
    $config->inQuickon && $canInstall ? item(set(array
    (
        'text'  => $lang->store->cloudStore,
        'icon'  => 'program',
        'class' => 'btn ghost',
        'url'   => createLink('store', 'browse'),
    ))) : null,
    $canInstall ? item(set(array
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

a(setStyle('display', 'none'), setID('editLinkContainer'), setData('toggle', 'modal'));

render();

