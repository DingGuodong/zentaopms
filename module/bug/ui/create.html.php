<?php
declare(strict_types=1);
/**
 * The create file of bug module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Yuting Wang<wangyuting@easycorp.ltd>
 * @package     bug
 * @link        https://www.zentao.net
 */
namespace zin;

$fields          = useFields('bug.create');
$isShadowProduct = $product->shadow;

if(!empty($executionType) && $executionType == 'kanban') $fields->merge('bug.kanban');

$fields->autoLoad('product', 'product,module,openedBuild,execution,project,story,task,assignedTo')
   ->autoLoad('branch',  'module,openedBuild,execution,project,story,task,assignedTo')
   ->autoLoad('module',  'assignedTo,story')
   ->autoLoad('project', 'project,openedBuild,execution,story,task,assignedTo')
   ->autoLoad('execution', 'execution,openedBuild,story,task,assignedTo')
   ->autoLoad('allBuilds', 'openedBuild')
   ->autoLoad('allUsers',  'assignedTo')
   ->autoLoad('region', 'lane');

jsVar('bug',                   $bug);
jsVar('moduleID',              $bug->moduleID);
jsVar('tab',                   $this->app->tab);
jsVar('createRelease',         $lang->release->create);
jsVar('refresh',               $lang->refreshIcon);
jsVar('projectExecutionPairs', $projectExecutionPairs);

formGridPanel
(
    set::title($lang->bug->create),
    set::fields($fields),
    set::fullModeOrders($isShadowProduct ? '' : 'module,project,execution'),
    set::loadUrl($loadUrl)
);
