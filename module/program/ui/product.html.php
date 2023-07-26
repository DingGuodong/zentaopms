<?php
declare(strict_types=1);
/**
 * The product view file of program module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      wangyidong<wangyidong@cnezsoft.com>
 * @package     program
 * @link        https://www.zentao.net
 */

namespace zin;

dropmenu();

featureBar
(
    set::current($browseType),
    set::linkParams("programID={$programID}&browseType={key}&orderBy=$orderBy"),
);

toolbar
(
    item(set
    (array(
        'text' => $lang->product->create,
        'icon' => 'plus',
        'class'=> 'btn primary',
        'url'  => $this->createLink('product', 'create', "programID={$programID}")
    ))),
);

$cols = $this->config->product->dtable->fieldList;

$data = array();
$products = initTableData($products, $cols, $this->product);
foreach($products as $product) $data[] = $this->product->formatDataForList($product, $users, $usersAvatar);

$summary = sprintf($lang->product->pageSummary, count($data));
$footToolbar = common::hasPriv('product', 'batchEdit') ?
    array(
        'type'  => 'btn-group',
        'items' => array(
            array(
                'text'     => $lang->edit,
                'btnType'  => 'secondary size-sm',
                'data-url' => $this->createLink('product', 'batchEdit', "programID={$programID}"),
                'onClick'  => jsRaw('window.onClickBatchEdit')
            )
        )
    ) :
    null;

dtable
(
    set::userMap($users),
    set::cols($cols),
    set::data($data),
    set::nested(false),
    set::footToolbar($footToolbar),
    set::footPager(usePager()),
    set::footer(array('checkbox', 'toolbar', array('html' => $summary, 'className' => "text-dark"), 'flex', 'pager')),
);

render();
