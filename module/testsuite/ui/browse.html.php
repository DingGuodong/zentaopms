<?php
declare(strict_types=1);
/**
 * The browse view file of testsuite module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     testsuite
 * @link        https://www.zentao.net
 */
namespace zin;

jsVar('authorList', $lang->testsuite->authorList);

$config->testsuite->dtable->fieldList['addedBy']['userMap'] = $users;

$tableData = initTableData($suites, $config->testsuite->dtable->fieldList, $this->testsuite);

$cols = array_values($config->testsuite->dtable->fieldList);
$data = array_values($tableData);

featureBar
(
    set::module('testsuite'),
    set::method('browse'),
    set::current('all'),
    set::linkParams("productID={$productID}&type=all"),
);

toolbar
(
    btngroup
    (
        btn
        (
            setClass('btn primary'),
            set::icon('plus'),
            set::url(helper::createLink('testsuite', 'create', "productID={$productID}")),
            $lang->testsuite->create
        )
    )
);

dtable
(
    set::cols($cols),
    set::data($data),
    set::fixedLeftWidth('0.2'),
    set::onRenderCell(jsRaw('window.renderCell')),
    set::footPager(usePager()),
);

render();

