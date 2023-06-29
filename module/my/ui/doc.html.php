<?php
declare(strict_types=1);
/**
 * The doc view file of my module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yuting Wang <wangyuting@easycorp.ltd>
 * @package     my
 * @link        https://www.zentao.net
 */
namespace zin;
jsVar('account', $app->user->account);
jsVar('draftLabel', $lang->doc->draft);
jsVar('canCollect', common::hasPriv('doc', 'collect'));
jsVar('objectIconList', $config->doc->objectIconList);

featureBar
(
    set::current($type),
    set::linkParams("mode=doc&type={key}&param={$param}"),
    li(searchToggle(set::module('doc')))
);

if($type == 'openedbyme') unset($config->my->doc->dtable->fieldList['addedBy']);
if($type == 'editedbyme') unset($config->my->doc->dtable->fieldList['editedBy']);

$docs = initTableData($docs, $config->my->doc->dtable->fieldList, $this->doc);
$cols = array_values($config->my->doc->dtable->fieldList);
$data = array_values($docs);
dtable
(
    set::cols($cols),
    set::data($data),
    set::userMap($users),
    set::fixedLeftWidth('44%'),
    set::onRenderCell(jsRaw('window.renderCell')),
    set::footPager(usePager()),
);

render();
