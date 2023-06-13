<?php
declare(strict_types=1);
/**
 * The bug view file of user module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Wang Yidong <yidong@easycorp.ltd>
 * @package     user
 * @link        https://www.zentao.net
 */
namespace zin;
include './featurebar.html.php';

$that = zget($lang->user->thirdPerson, $user->gender);
$bugNavs['assignedTo'] = array('text' => sprintf($lang->user->assignedTo, $that), 'url' => inlink('bug', "userID={$user->id}&type=assignedTo"));
$bugNavs['openedBy']   = array('text' => sprintf($lang->user->openedBy,   $that), 'url' => inlink('bug', "userID={$user->id}&type=openedBy"));
$bugNavs['resolvedBy'] = array('text' => sprintf($lang->user->resolvedBy, $that), 'url' => inlink('bug', "userID={$user->id}&type=resolvedBy"));
$bugNavs['closedBy']   = array('text' => sprintf($lang->user->closedBy,   $that), 'url' => inlink('bug', "userID={$user->id}&type=closedBy"));
if(isset($bugNavs[$type])) $bugNavs[$type]['active'] = true;

$cols = array();
foreach($config->user->defaultFields['bug'] as $field) $cols[$field] = $config->bug->dtable->fieldList[$field];
$cols['id']['checkbox'] = false;

$cols = array_map(function($col)
{
    unset($col['fixed'], $col['group']);
    $col['sortType'] = false;
    return $col;
}, $cols);

panel
(
    setClass('list'),
    set::title(null),
    set::headingActions(array(nav(set::items($bugNavs)))),
    dtable
    (
        set::userMap($users),
        set::bordered(true),
        set::cols($cols),
        set::data(array_values($bugs)),
        set::footPager(usePager()),
    )
);

render();
