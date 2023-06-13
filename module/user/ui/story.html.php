<?php
declare(strict_types=1);
/**
 * The story view file of user module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Wang Yidong <yidong@easycorp.ltd>
 * @package     user
 * @link        https://www.zentao.net
 */
namespace zin;
include './featurebar.html.php';

$that = zget($lang->user->thirdPerson, $user->gender);
$storyNavs['assignedTo'] = array('text' => sprintf($lang->user->assignedTo, $that), 'url' => inlink('story', "userID={$user->id}&storyType={$storyType}&type=assignedTo"));
$storyNavs['openedBy']   = array('text' => sprintf($lang->user->openedBy,   $that), 'url' => inlink('story', "userID={$user->id}&storyType={$storyType}&type=openedBy"));
$storyNavs['reviewedBy'] = array('text' => sprintf($lang->user->reviewedBy, $that), 'url' => inlink('story', "userID={$user->id}&storyType={$storyType}&type=reviewedBy"));
$storyNavs['closedBy']   = array('text' => sprintf($lang->user->closedBy,   $that), 'url' => inlink('story', "userID={$user->id}&storyType={$storyType}&type=closedBy"));
if(isset($storyNavs[$type])) $storyNavs[$type]['active'] = true;

$this->loadModel('my');
$cols = array();
foreach($config->user->defaultFields['story'] as $field) $cols[$field] = $config->my->story->dtable->fieldList[$field];
$cols['id']['checkbox']        = false;
$cols['title']['nestedToggle'] = false;
$cols['plan']['name']          = 'planTitle';
$cols['product']['name']       = 'productTitle';
if($storyType == 'requirement' || $this->config->vision == 'lite') unset($cols['plan']);
if($this->config->vision == 'lite') unset($cols['stage']);
if($this->config->vision == 'lite') $cols['product']['title'] = $lang->story->project;

foreach($stories as $story)
{
    $story->statusLabel = $this->processStatus('story', $story);
    $story->estimate   .= $config->hourUnit;
}

panel
(
    setClass('list'),
    set::title(null),
    set::headingActions(array(nav(set::items($storyNavs)))),
    dtable
    (
        set::userMap($users),
        set::bordered(true),
        set::cols($cols),
        set::data(array_values($stories)),
        set::onRenderCell(jsRaw('window.renderCell')),
        set::footPager(usePager()),
    )
);

render();
