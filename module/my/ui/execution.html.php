<?php
declare(strict_types=1);
/**
 * The execution view file of my module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Tingting Dai <daitingting@easycorp.ltd>
 * @package     execution
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('systemMode', $config->systemMode);
jsVar('typeList', $lang->execution->typeList);

featurebar
(
    set::current($type),
    set::linkParams("type={key}&orderBy=id_desc&recPerPage={$pager->recPerPage}"),
);

foreach($executions as $execution) $execution->isParent = isset($parentGroup[$execution->id]);

$executions = array_values($executions);

dtable
(
    set::cols($config->my->execution->dtable->fieldList),
    set::data($executions),
    set::onRenderCell(jsRaw('window.onRenderExecutionCell')),
    set::orderBy($orderBy),
    set::sortLink(createLink('my', 'execution', "type={$type}&orderBy={name}_{sortType}&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}")),
    set::footPager(usePager(array(
        'recPerPage'  => $pager->recPerPage,
        'recTotal'    => $pager->recTotal,
        'linkCreator' => createLink('my', 'execution', "type={$type}&orderBy={$orderBy}&recTotal={$pager->recTotal}&recPerPaga={recPerPage}&page={page}"),
    )))
);

render();
