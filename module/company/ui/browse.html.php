<?php
declare(strict_types=1);
/**
 * The browse view file of company module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Mengyi Liu <liumengyi@easycorp.ltd>
 * @package     company
 * @link        https://www.zentao.net
 */
namespace zin;

featureBar
(
    set::current($browseType),
    set::linkParams("browseType={key}"),
    li(searchToggle(set::open($browseType == 'bysearch'))),
);

toolbar
(
    btngroup
    (
        btn
        (
            setClass('btn primary'),
            set::icon('plus'),
            set::url(helper::createLink('user', 'create', "deptID={$deptID}")),
            $lang->user->create
        ),
        dropdown
        (
            btn(setClass('btn primary dropdown-toggle'), setStyle(array('padding' => '6px', 'border-radius' => '0 2px 2px 0'))),
            set::items
            (
                array
                (
                    array('text' => $lang->user->create,      'url' => helper::createLink('user', 'create', "deptID={$deptID}")),
                    array('text' => $lang->user->batchCreate, 'url' => helper::createLink('user', 'batchCreate', "deptID={$deptID}")),
                )
            ),
            set::placement('bottom-end'),
        )
    )
);

$settingLink = createLink('dept', 'browse');
$closeLink   = createLink('company', 'browse', "browseType={$browseType}&param=0&type={$type}");
sidebar
(
    moduleMenu(set(array
    (
        'modules'   => $deptTree,
        'activeKey' => $type == 'bydept' ? $param : 0,
        'settingLink' => $settingLink,
        'closeLink' => $closeLink
    )))
);

$tableData = initTableData($users, $this->config->company->user->dtable->fieldList, $this->loadModel('user'));
dtable
(
    set::cols($this->config->company->user->dtable->fieldList),
    set::data($tableData),
    set::footPager(usePager()),
);

render();

