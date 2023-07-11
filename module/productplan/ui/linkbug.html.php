<?php
declare(strict_types=1);
/**
 * The linkBug view file of productplan module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Wang Yidong <yidong@easycorp.ltd>
 * @package     productplan
 * @link        https://www.zentao.net
 */
namespace zin;

$cols = array();
foreach($config->productplan->defaultFields['linkBug'] as $field) $cols[$field] = zget($config->bug->dtable->fieldList, $field, array());
$cols = array_map(function($col){$col['show'] = true; return $col;}, $cols);
$cols['assignedTo']['type'] = 'user';

div(setID('searchFormPanel'), set('data-module', 'bug'), searchToggle(set::open(true), set::module('bug')));
dtable
(
    set::id('unlinkBugList'),
    set::userMap($users),
    set::checkable(true),
    set::cols($cols),
    set::data(array_values($allBugs)),
    set::footToolbar(array
    (
        'items' => array(array
        (
            'text'      => $lang->productplan->linkBug,
            'btnType'   => 'primary',
            'className' => 'size-sm linkObjectBtn',
            'data-type' => 'bug',
            'data-url'  => inlink('linkBug', "planID=$plan->id&browseType=$browseType&param=$param&orderBy=$orderBy"),
        ))
    )),
    set::footer(array('checkbox', 'toolbar', array('html' => html::a(inlink('view', "planID=$plan->id&type=bug&orderBy=$orderBy"), $lang->goback, '', "class='btn size-sm'")), 'flex', 'pager')),
    set::footPager
    (
        usePager(array(
            'recPerPage' => $pager->recPerPage,
            'recTotal' => $pager->recTotal,
            'linkCreator' => helper::createLink('productplan', 'view', "planID={$plan->id}&type=bug&orderBy={$orderBy}&link=true&param=" . helper::safe64Encode("&browseType={$browseType}&param={$param}") . "&recTotal={$pager->recTotal}&recPerPage={recPerPage}&page={page}")
        ))
    ),
);

render();
