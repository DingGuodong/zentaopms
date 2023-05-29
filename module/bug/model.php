<?php
declare(strict_types=1);
/**
 * The model file of bug module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     bug
 * @version     $Id: model.php 5079 2013-07-10 00:44:34Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php
class bugModel extends model
{
    /**
     * Set menu.
     *
     * @param  array  $products
     * @param  int    $productID
     * @param  int    $branch
     * @param  int    $moduleID
     * @param  string $browseType
     * @param  string $orderBy
     * @access public
     * @return void
     */
    public function setMenu($products, $productID, $branch = 0, $moduleID = 0, $browseType = 'unclosed', $orderBy = '')
    {
        $this->loadModel('product')->setMenu($productID, $branch, $moduleID, 'bug');
        if($this->lang->navGroup->testcase == 'project' and $this->app->methodName == 'browse') $products = array(0 => $this->lang->bug->allProduct) + $products;
        $selectHtml = $this->product->select($products, $productID, 'bug', 'browse', '', $branch, $moduleID, 'bug');

        $pageNav     = '';
        $pageActions = '';
        $isMobile    = $this->app->viewType == 'mhtml';
        if($isMobile)
        {
            $this->app->loadLang('qa');
            $pageNav = html::a(helper::createLink('qa', 'index'), $this->lang->qa->index) . $this->lang->colon;
        }
        $pageNav .= $selectHtml;

        $this->lang->modulePageNav = $pageNav;
        $this->lang->TRActions     = $pageActions;
    }

    /**
     * bug的入库操作。
     * Insert bug into zt_bug.
     *
     * @param  object $bug
     * @access public
     * @return int|false
     */
    public function create(object $bug, string $action = 'Opened'): int|false
    {
        $this->dao->insert(TABLE_BUG)->data($bug, 'laneID')
            ->autoCheck()
            ->checkIF(!empty($bug->notifyEmail), 'notifyEmail', 'email')
            ->batchCheck($this->config->bug->create->requiredFields, 'notempty')
            ->checkFlow()
            ->exec();

        if(dao::isError()) return false;

        $bugID = $this->dao->lastInsertID();

        $this->loadModel('action')->create('bug', $bugID, $action);

        /* Add score for create. */
        if(!empty($bug->case))
        {
            $this->loadModel('score')->create('bug', 'createFormCase', $bug->case);
        }
        else
        {
            $this->loadModel('score')->create('bug', 'create', $bugID);
        }

        return $bugID;
    }

    /**
     * gitlab问题转为bug。
     * Create bug from gitlab issue.
     *
     * @param  object    $bug
     * @param  int       $executionID
     * @access public
     * @return int|bool
     */
    public function createBugFromGitlabIssue(object $bug, int $executionID): int|bool
    {
        $bug->openedBy     = $this->app->user->account;
        $bug->openedDate   = helper::now();
        $bug->assignedDate = isset($bug->assignedTo) ? helper::now() : null;
        $bug->openedBuild  = 'trunk';
        $bug->story        = 0;
        $bug->task         = 0;
        $bug->pri          = 3;
        $bug->severity     = 3;
        $bug->project      = $this->dao->select('project')->from(TABLE_EXECUTION)->where('id')->eq($executionID)->fetch('project');

        $this->dao->insert(TABLE_BUG)->data($bug, $skip = 'gitlab,gitlabProject')->autoCheck()->batchCheck($this->config->bug->create->requiredFields, 'notempty')->exec();
        if(!dao::isError()) return $this->dao->lastInsertID();

        return false;
    }

    /**
     * 根据浏览类型获取 bug 列表。
     * Get bug list by browse type.
     *
     * @param  string     $browseType
     * @param  array      $productIdList
     * @param  int        $projectID
     * @param  int[]      $executionIdList
     * @param  int|string $branch
     * @param  int        $moduleID
     * @param  int        $queryID
     * @param  string     $orderBy
     * @param  object     $pager
     * @access public
     * @return array
     */
    public function getList(string $browseType, array $productIdList, int $projectID, array $executionIdList, int|string $branch = 'all', int $moduleID = 0, int $queryID = 0, string $orderBy = 'id_desc', object $pager = null): array
    {
        if($browseType == 'bymodule' && $this->session->bugBrowseType && $this->session->bugBrowseType != 'bysearch') $browseType = $this->session->bugBrowseType;

        if(!in_array($browseType, $this->config->bug->browseTypeList)) return array();

        /* 处理排序。*/
        /* Process sort field. */
        if(strpos($orderBy, 'pri_') !== false)      $orderBy = str_replace('pri_', 'priOrder_', $orderBy);
        if(strpos($orderBy, 'severity_') !== false) $orderBy = str_replace('severity_', 'severityOrder_', $orderBy);

        $modules = $moduleID ? $this->loadModel('tree')->getAllChildID($moduleID) : array();
        $bugList = $this->bugTao->getListByBrowseType($browseType, $productIdList, $projectID, $executionIdList, $branch, $modules, $queryID, $orderBy, $pager);

        return $this->bugTao->batchAppendDelayedDays($bugList);
    }

    /**
     * Get bug list of a plan.
     *
     * @param  int    $planID
     * @param  string $status
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return void
     */
    public function getPlanBugs($planID, $status = 'all', $orderBy = 'id_desc', $pager = null)
    {
        if(strpos($orderBy, 'pri_') !== false) $orderBy = str_replace('pri_', 'priOrder_', $orderBy);
        $bugs = $this->dao->select("*, IF(`pri` = 0, {$this->config->maxPriValue}, `pri`) as priOrder")->from(TABLE_BUG)
            ->where('plan')->eq((int)$planID)
            ->beginIF(!$this->app->user->admin)->andWhere('execution')->in('0,' . $this->app->user->view->sprints)->fi()
            ->beginIF($status != 'all')->andWhere('status')->in($status)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)->page($pager)->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug');

        return $bugs;
    }

    /**
     * 获取Bug的基础数据。
     * Get base info of a bug.
     *
     * @param  int $bugID
     * @access protected
     * @return object|false
     */
    public function getBaseInfo(int $bugID): object|false
    {
        return $this->bugTao->fetchBaseInfo($bugID);
    }

    /**
     * Get info of a bug.
     *
     * @param  int    $bugID
     * @param  bool   $setImgSize
     * @access public
     * @return object|false
     */
    public function getByID(int $bugID, bool $setImgSize = false): object|false
    {
        $bug = $this->bugTao->fetchBugInfo($bugID);
        if(!$bug) return false;

        $this->loadModel('file');
        $this->loadModel('mr');

        $bug = $this->file->replaceImgURL($bug, 'steps');
        if($setImgSize) $bug->steps = $this->file->setImgSize($bug->steps);

        if($bug->project)      $bug->projectName       = $this->bugTao->getNameFromTable($bug->project, TABLE_PROJECT, 'name');
        if($bug->duplicateBug) $bug->duplicateBugTitle = $this->bugTao->getNameFromTable($bug->duplicateBug, TABLE_BUG, 'title');
        if($bug->case)         $bug->caseTitle         = $this->bugTao->getNameFromTable($bug->case, TABLE_CASE, 'title');
        if($bug->linkBug)      $bug->linkBugTitles     = $this->bugTao->getBugPairsByList($bug->linkBug);
        if($bug->toStory)      $bug->toStoryTitle      = $this->bugTao->getNameFromTable($bug->toStory, TABLE_STORY, 'title');
        if($bug->toTask)       $bug->toTaskTitle       = $this->bugTao->getNameFromTable($bug->toTask, TABLE_TASK, 'name');

        $bug->linkMRTitles = $this->mr->getLinkedMRPairs($bugID, 'bug');
        $bug->toCases      = $this->bugTao->getCasesFromBug($bugID);
        $bug->files        = $this->file->getByObject('bug', $bugID);
        return $this->bugTao->appendDelayedDays($bug);
    }

    /**
     * 获取指定字段的bug列表。
     * Get bugs by ID list.
     *
     * @param  int|array|string $bugIdList
     * @param  string           $fields
     * @access public
     * @return array
     */
    public function getByIdList(int|array|string $bugIdList = 0, string $fields = '*'): array
    {
        return $this->dao->select($fields)->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->beginIF($bugIdList)->andWhere('id')->in($bugIdList)->fi()
            ->fetchAll('id');
    }

    /**
     * 获取激活的未转为bug和任务的 bugs。
     * Get active bugs.
     *
     * @param  array|int  $products
     * @param  int|string $branch
     * @param  string     $executions
     * @param  array      $excludeBugs
     * @param  object     $pager
     * @access public
     * @return array
     */
    public function getActiveBugs(array|int $products, int|string $branch, string $executions, array $excludeBugs, object $pager = null): array
    {
        return $this->dao->select('*')->from(TABLE_BUG)
            ->where('status')->eq('active')
            ->andWhere('toStory')->eq(0)
            ->andWhere('toTask')->eq(0)
            ->beginIF(!empty($products))->andWhere('product')->in($products)->fi()
            ->beginIF($branch !== '' and $branch !== 'all')->andWhere('branch')->in("0,$branch")->fi()
            ->beginIF(!empty($executions))->andWhere('execution')->in($executions)->fi()
            ->beginIF($excludeBugs)->andWhere('id')->notIN($excludeBugs)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy('id desc')
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get active and postponed bugs.
     *
     * @param  int    $products
     * @param  int    $executionID
     * @param  int    $pager
     * @access public
     * @return void
     */
    public function getActiveAndPostponedBugs($products, $executionID, $pager = null)
    {
        return $this->dao->select('t1.*')->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_PROJECTPRODUCT)->alias('t2')->on('t1.product = t2.product')
            ->where("((t1.status = 'resolved' AND t1.resolution = 'postponed') OR (t1.status = 'active'))")
            ->andWhere('t1.toTask')->eq(0)
            ->andWhere('t1.toStory')->eq(0)
            ->beginIF(!empty($products))->andWhere('t1.product')->in($products)->fi()
            ->beginIF(empty($products))->andWhere('t1.execution')->eq($executionID)->fi()
            ->andWhere('t2.project')->eq($executionID)
            ->andWhere("(t2.branch = '0' OR t1.branch = '0' OR t2.branch = t1.branch)")
            ->andWhere('t1.deleted')->eq(0)
            ->orderBy('id desc')
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get module owner.
     *
     * @param  int    $moduleID
     * @param  int    $productID
     * @access public
     * @return string
     */
    public function getModuleOwner($moduleID, $productID)
    {
        $users = $this->loadModel('user')->getPairs('nodeleted');
        $owner = $this->dao->findByID($productID)->from(TABLE_PRODUCT)->fetch('QD');
        $owner = isset($users[$owner]) ? $owner : '';

        if($moduleID)
        {
            $module = $this->dao->findByID($moduleID)->from(TABLE_MODULE)->andWhere('root')->eq($productID)->fetch();
            if(empty($module)) return $owner;

            if($module->owner and isset($users[$module->owner])) return $module->owner;

            $moduleIdList = explode(',', trim(str_replace(",$module->id,", ',', $module->path), ','));
            krsort($moduleIdList);
            if($moduleIdList)
            {
                $modules = $this->dao->select('*')->from(TABLE_MODULE)->where('id')->in($moduleIdList)->andWhere('deleted')->eq(0)->fetchAll('id');
                foreach($moduleIdList as $moduleID)
                {
                    if(isset($modules[$moduleID]))
                    {
                        $module = $modules[$moduleID];
                        if($module->owner and isset($users[$module->owner])) return $module->owner;
                    }
                }
            }
        }

        return $owner;
    }

    /**
     * 获取 bug 列表页面左侧模块。
     * Get bug modules for side bar of browse page.
     *
     * @param  int    $productID
     * @param  string $branch
     * @access public
     * @return array
     */
    public function getModulesForSidebar(int $productID, string $branch): array
    {
        $stmt    = $this->dbh->query($this->loadModel('tree')->buildMenuQuery($productID, 'bug', 0, $branch));
        $modules = array();
        while($data = $stmt->fetch())
        {
            $module = new stdclass();
            $module->id     = $data->id;
            $module->parent = $data->parent;
            $module->name   = $data->name;
            $module->url    = helper::createLink('bug', 'browse', "product=$productID&branch=$branch&type=byModule&param=$data->id");

            $modules[] = $module;
        }

        return $modules;
    }

    /**
     * 更新 bug 信息。
     * Update a bug.
     *
     * @param  object      $bug
     * @param  string      $action
     * @access public
     * @return array|false
     */
    public function update(object $bug, string $action = 'Edited'): array|false
    {
        $oldBug = $this->getByID($bug->id);

        $this->dao->update(TABLE_BUG)->data($bug, 'deleteFiles,comment')
            ->autoCheck()
            ->batchCheck($this->config->bug->edit->requiredFields, 'notempty')
            ->checkIF(!empty($bug->resolvedBy), 'resolution',  'notempty')
            ->checkIF(!empty($bug->closedBy),   'resolution',  'notempty')
            ->checkIF(!empty($bug->notifyEmail),'notifyEmail', 'email')
            ->checkIF(!empty($bug->resolution) && $bug->resolution == 'duplicate', 'duplicateBug', 'notempty')
            ->checkIF(!empty($bug->resolution) && $bug->resolution == 'fixed',     'resolvedBuild','notempty')
            ->checkFlow()
            ->where('id')->eq($bug->id)
            ->exec();

        if(dao::isError()) return false;

        $changes = common::createChanges($oldBug, $bug);
        if($changes || $bug->comment)
        {
            $actionID = $this->loadModel('action')->create('bug', $bug->id, $changes ? $action : 'Commented', $bug->comment);
            if($changes) $this->action->logHistory($actionID, $changes);
        }

        if(dao::isError()) return false;

        return $changes;
    }

    /**
     * 批量更新 bugs。
     * Batch update bugs.
     *
     * @param  array       $bugs
     * @access public
     * @return array|false
     */
    public function batchUpdate(array $bugs): array|bool
    {
        if(empty($bugs)) return false;

        /* Check bugs for batch update. */
        $this->bugTao->checkBugsForBatchUpdate($bugs);
        if(dao::isError()) return false;

        $oldBugs = $this->getByIdList(array_column($bugs, 'id'));

        /* Update bugs. */
        $toTaskIdList = array();
        $unlinkPlans  = array();
        $link2Plans   = array();
        foreach($bugs as $bugID => $bug)
        {
            /* Update bug. */
            $this->dao->update(TABLE_BUG)->data($bug)
                ->autoCheck()
                ->checkFlow()
                ->where('id')->eq((int)$bugID)
                ->exec();
            if(dao::isError())
            {
                dao::$errors['message'][] = 'bug#' . ($bugID) . dao::getError(true);
                return false;
            }

            /* Processing other operations after update bug. */
            $oldBug = $oldBugs[$bugID];
            $this->afterBatchEdit($bug, $oldBug);

            if($oldBug->toTask != 0 && isset($bug->status) && $bug->status != $oldBug->status) $toTaskIdList[$oldBug->toTask] = $oldBug->toTask;

            /* Get changes of plan. */
            if($bug->plan != $oldBug->plan)
            {
                if(!empty($oldBug->plan)) $unlinkPlans[$oldBug->plan] = empty($unlinkPlans[$oldBug->plan]) ? $bugID : "{$unlinkPlans[$oldBug->plan]},{$bugID}";
                if(!empty($bug->plan))    $link2Plans[$bug->plan]  = empty($link2Plans[$bug->plan]) ? $bugID : "{$link2Plans[$bug->plan]},{$bugID}";
            }
        }

        $this->loadModel('score')->create('ajax', 'batchEdit');

        $this->loadModel('action');
        foreach($unlinkPlans as $planID => $bugs) $this->action->create('productplan', $planID, 'unlinkbug', '', $bugs);
        foreach($link2Plans as $planID => $bugs) $this->action->create('productplan', $planID, 'linkbug', '', $bugs);

        return $toTaskIdList;
    }

    /**
     * 批量激活bug。
     * Batch active bugs.
     *
     * @param  object $activateData
     * @param  array  $postExtendData
     * @access public
     * @return array|false
     */
    public function batchActivate(object $activateData, array $postExtendData): array|false
    {
        $activateBugs = array();
        $bugIdList    = $activateData->bugIdList ? $activateData->bugIdList : array();
        if(empty($bugIdList)) return $activateBugs;

        $now = helper::now();
        foreach($bugIdList as $bugID)
        {
            if($activateData->statusList[$bugID] == 'active') continue;

            $activateBugs[$bugID]['assignedTo']  = $activateData->assignedToList[$bugID];
            $activateBugs[$bugID]['openedBuild'] = $activateData->openedBuildList[$bugID];
            $activateBugs[$bugID]['comment']     = $activateData->commentList[$bugID];

            $activateBugs[$bugID]['activatedDate']  = $now;
            $activateBugs[$bugID]['assignedDate']   = $now;
            $activateBugs[$bugID]['resolution']     = '';
            $activateBugs[$bugID]['status']         = 'active';
            $activateBugs[$bugID]['resolvedDate']   = '0000-00-00';
            $activateBugs[$bugID]['resolvedBy']     = '';
            $activateBugs[$bugID]['resolvedBuild']  = '';
            $activateBugs[$bugID]['closedBy']       = '';
            $activateBugs[$bugID]['closedDate']     = '0000-00-00';
            $activateBugs[$bugID]['duplicateBug']   = 0;
            $activateBugs[$bugID]['toTask']         = 0;
            $activateBugs[$bugID]['toStory']        = 0;
            $activateBugs[$bugID]['lastEditedBy']   = $this->app->user->account;
            $activateBugs[$bugID]['lastEditedDate'] = $now;

            foreach($postExtendData as $field => $postFieldData)
            {
                if(is_array($postFieldData[$bugID])) $postFieldData[$bugID] = implode(',', $postFieldData[$bugID]);
                $activateBugs[$bugID][$field] = htmlSpecialString($postFieldData[$bugID]);
            }
        }

        /* Update bugs. */
        foreach($activateBugs as $bugID => $bug)
        {
            $this->dao->update(TABLE_BUG)->data($bug, $skipFields = 'comment')->autoCheck()->where('id')->eq((int)$bugID)->exec();
            if(dao::isError())
            {
                dao::$errors['message'][] = 'bug#' . $bugID . dao::getError(true);
                return false;
            }
            $this->loadModel('action')->create('bug', $bugID, 'Activated', $bug['comment']);

            $this->dao->update(TABLE_BUG)->set('activatedCount = activatedCount + 1')->where('id')->eq((int)$bugID)->exec();
            $this->executeHooks($bugID);
        }

        return $activateBugs;
    }

    /**
     * 将任务指派给一个用户。
     * Assign a bug to a user.
     *
     * @param  object $bug
     * @access public
     * @return bool
     */
    public function assign(object $bug): bool
    {
        /* Get old bug. */
        $oldBug = $this->getById($bug->id);

        /* Update assigned of the bug. */
        $this->dao->update(TABLE_BUG)->data($bug, 'comment')->autoCheck()->checkFlow()->where('id')->eq($bug->id)->exec();
        if(dao::isError()) return false;

        /* Record log. */
        $changes  = common::createChanges($oldBug, $bug);
        $actionID = $this->loadModel('action')->create('bug', $bug->id, 'Assigned', $bug->comment, $bug->assignedTo);
        if($changes) $this->action->logHistory($actionID, $changes);

        return !dao::isError();
    }

    /**
     * 确认 bug。
     * Confirm a bug.
     *
     * @param  object $bug
     * @param  array  $kanbanData
     * @access public
     * @return bool
     */
    public function confirm(object $bug, array $kanbanData = array()): bool
    {
        $oldBug = $this->getByID($bug->id);

        $this->dao->update(TABLE_BUG)->data($bug, 'comment')->autoCheck()->checkFlow()->where('id')->eq($bug->id)->exec();
        if(dao::isError()) return false;

        /* 确认 bug 后的积分变动。*/
        /* Record score after confirming bug. */
        $this->loadModel('score')->create('bug', 'confirm', $oldBug);

        /* 如果 bug 有所属执行，更新看板数据。*/
        /* Update kanban if the bug has execution. */
        if($oldBug->execution)
        {
            $this->loadModel('kanban');
            if(!isset($kanbanData['toColID'])) $this->kanban->updateLane($oldBug->execution, 'bug', $oldBug->id);
            if(isset($kanbanData['toColID']))  $this->kanban->moveCard($oldBug->id, $kanbanData['fromColID'], $kanbanData['toColID'], $kanbanData['fromLaneID'], $kanbanData['toLaneID'], $oldBug->execution);
        }

        /* 记录历史记录。*/
        /* Record history. */
        $changes  = common::createChanges($oldBug, $bug);
        $actionID = $this->loadModel('action')->create('bug', $oldBug->id, 'bugConfirmed', $bug->comment);
        if($changes) $this->action->logHistory($actionID, $changes);

        return !dao::isError();
    }

    /**
     * Batch confirm bugs.
     *
     * @param  array $bugIdList
     * @access public
     * @return void
     */
    public function batchConfirm($bugIdList)
    {
        $now  = helper::now();
        $bugs = $this->getByList($bugIdList);
        foreach($bugIdList as $bugID)
        {
            if($bugs[$bugID]->confirmed) continue;

            $bug = new stdclass();
            $bug->assignedTo     = $this->app->user->account;
            $bug->lastEditedBy   = $this->app->user->account;
            $bug->lastEditedDate = $now;
            $bug->confirmed      = 1;

            $this->dao->update(TABLE_BUG)->data($bug)->where('id')->eq($bugID)->exec();
            $this->executeHooks($bugID);
        }
    }

    /**
     * 解决一个bug。
     * Resolve a bug.
     *
     * @param  object      $bugID
     * @param  array       $output
     * @access public
     * @return bool
     */
    public function resolve(object $bug, array $output = array()): bool
    {
        /* Get old bug. */
        $oldBug = $this->getById($bug->id);

        /* Update bug. */
        $this->dao->update(TABLE_BUG)->data($bug, 'buildName,createBuild,buildExecution,comment')
            ->autoCheck()
            ->batchCheck($this->config->bug->resolve->requiredFields, 'notempty')
            ->checkIF($bug->resolution == 'duplicate', 'duplicateBug', 'notempty')
            ->checkIF($bug->resolution == 'fixed',     'resolvedBuild','notempty')
            ->checkFlow()
            ->where('id')->eq($bug->id)
            ->exec();

        if(dao::isError()) return false;

        /* Add score. */
        $this->loadModel('score')->create('bug', 'resolve', $bug);

        /* Move bug card in kanban. */
        if($bug->execution)
        {
            if(!isset($output['toColID'])) $this->loadModel('kanban')->updateLane($bug->execution, 'bug', $bug->id);
            if(isset($output['toColID'])) $this->loadModel('kanban')->moveCard($bug->id, $output['fromColID'], $output['toColID'], $output['fromLaneID'], $output['toLaneID']);
        }

        /* Link bug to build and release. */
        $this->linkBugToBuild($bug->id, $bug->resolvedBuild);

        /* Save files and record log. */
        $files      = $this->loadModel('file')->saveUpload('bug', $bug->id);
        $fileAction = !empty($files) ? $this->lang->addFiles . implode(',', $files) . "\n" : '';
        $changes    = common::createChanges($oldBug, $bug);
        $actionID   = $this->loadModel('action')->create('bug', $bug->id, 'Resolved', $fileAction . $bug->comment, $bug->resolution . (isset($bug->duplicateBug) ? ':' . $bug->duplicateBug : ''));
        if($changes) $this->action->logHistory($actionID, $changes);

        /* If the edition is not pms, update feedback. */
        if(($this->config->edition == 'biz' || $this->config->edition == 'max') && $oldBug->feedback) $this->loadModel('feedback')->updateStatus('bug', $oldBug->feedback, $bug->status, $oldBug->status);

        return !dao::isError();
    }

    /**
     * 在解决bug的时候创建一个版本。
     * Create build when resolving a bug.
     *
     * @param  object $bug
     * @param  object $oldBug
     * @access public
     * @return bool
     */
    public function createBuild(object $bug, object $oldBug): bool
    {
        /* Construct build data. */
        $buildData = new stdclass();
        $buildData->product     = (int)$oldBug->product;
        $buildData->branch      = (int)$oldBug->branch;
        $buildData->project     = $this->dao->select('project')->from(TABLE_EXECUTION)->where('id')->eq($bug->buildExecution)->fetch('project');
        $buildData->execution   = $bug->buildExecution;
        $buildData->name        = $bug->buildName;
        $buildData->date        = date('Y-m-d');
        $buildData->builder     = $this->app->user->account;
        $buildData->createdBy   = $this->app->user->account;
        $buildData->createdDate = helper::now();

        /* Create a build. */
        $this->lang->build->name = $this->lang->bug->placeholder->newBuildName;
        $this->dao->insert(TABLE_BUILD)->data($buildData)->autoCheck()
            ->check('name', 'unique', "product = {$buildData->product} AND branch = {$buildData->branch} AND deleted = '0'")
            ->exec();
        if(dao::isError())
        {
            $error = dao::getError();
            if(isset($error['name']))
            {
                $error['buildName'] = $error['name'];
                unset($error['name']);
            }
            dao::$errors = $error;
            return false;
        }

        /* Get build id, and record log. */
        $buildID = $this->dao->lastInsertID();
        $this->loadModel('action')->create('build', $buildID, 'opened');
        $bug->resolvedBuild = $buildID;

        return !dao::isError();
    }

    /**
     * 批量修改bug分支。
     * Batch change branch.
     *
     * @param  array  $bugIdList
     * @param  int    $branchID
     * @param  array  $oldBugs
     * @access public
     * @return array
     */
    public function batchChangeBranch(array $bugIdList, int $branchID, array $oldBugs): array
    {
        $allChanges = array();
        foreach($bugIdList as $bugID)
        {
            $oldBug = $oldBugs[$bugID];
            if($branchID == $oldBug->branch) continue;

            $bug = new stdclass();
            $bug->branch = $branchID;
            $this->bugTao->updateByID((int)$bugID, $bug);

            if(!dao::isError()) $allChanges[$bugID] = common::createChanges($oldBug, $bug);
        }
        return $allChanges;
    }



    /**
     * 激活一个bug。
     * Activate a bug.
     *
     * @param  object $bug
     * @param  array  $kanbanParams
     * @access public
     * @return bool
     */
    public function activate(object $bug, array $kanbanParams = array()): bool
    {
        $oldBug = $this->getBaseInfo($bug->id);
        if(!$oldBug)
        {
            dao::$errors[] = $this->lang->bug->error->notExist;
            return false;
        }
        if($oldBug->status != 'resolved' && $oldBug->status != 'closed')
        {
            dao::$errors[] = $this->lang->bug->error->cannotActivate;
            return false;
        }

        $bug->activatedCount = $oldBug->activatedCount + 1;

        $this->dao->update(TABLE_BUG)->data($bug, 'comment')->autoCheck()->checkFlow()->where('id')->eq($bug->id)->exec();
        if(dao::isError()) return false;

        /* Update build. */
        $solveBuild = $this->dao->select('id, bugs')->from(TABLE_BUILD)->where("FIND_IN_SET('{$bug->id}', bugs)")->limit(1)->fetch();
        if($solveBuild)
        {
            $buildBugs = trim(str_replace(",{$bug->id},", ',', ",$solveBuild->bugs,"), ',');
            $this->dao->update(TABLE_BUILD)->set('bugs')->eq($buildBugs)->where('id')->eq($solveBuild->id)->exec();
        }

        /* Update kanban. */
        if($oldBug->execution)
        {
            $this->loadModel('kanban');
            if(!isset($kanbanParams['toColID'])) $this->kanban->updateLane($oldBug->execution, 'bug', $bug->id);
            if(isset($kanbanParams['toColID'])) $this->kanban->moveCard($bug->id, $kanbanParams['fromColID'], $kanbanParams['toColID'], $kanbanParams['fromLaneID'], $kanbanParams['toLaneID']);
        }

        $changes = common::createChanges($oldBug, $bug);
        $files   = $this->loadModel('file')->saveUpload('bug', $bug->id);
        if($changes or $files)
        {
            $fileAction = !empty($files) ? $this->lang->addFiles . implode(',', $files) . "\n" : '';
            $actionID   = $this->loadModel('action')->create('bug', $bug->id, 'Activated', $fileAction . $bug->comment);
            $this->action->logHistory($actionID, $changes);
        }

        return !dao::isError();
    }

    /**
     * 关闭一个bug。
     * Close a bug.
     *
     * @param  object $bug
     * @access public
     * @return bool
     */
    public function close(object $bug, array $output): bool
    {
        $oldBug = $this->getById($bug->id);

        $this->dao->update(TABLE_BUG)->data($bug, 'comment')->autoCheck()->checkFlow()->where('id')->eq($bug->id)->exec();
        if(dao::isError()) return false;

        if(($this->config->edition == 'biz' || $this->config->edition == 'max') && $oldBug->feedback) $this->loadModel('feedback')->updateStatus('bug', $oldBug->feedback, $bug->status, $oldBug->status);

        $changes = common::createChanges($oldBug, $bug);
        $actionID = $this->loadModel('action')->create('bug', $bug->id, 'Closed', $bug->comment);
        if($changes) $this->action->logHistory($actionID, $changes);

        if($oldBug->execution)
        {
            $this->loadModel('kanban');
            if(!isset($output['toColID'])) $this->kanban->updateLane($oldBug->execution, 'bug', $bug->id);
            if(isset($output['toColID'])) $this->kanban->moveCard($bug->id, $output['fromColID'], $output['toColID'], $output['fromLaneID'], $output['toLaneID']);
        }

        /* 给原bug的抄送人发送完消息后，再处理它。 */
        /* After sending a message to the cc of the original bug, then process with it. */
        $this->dao->update(TABLE_BUG)->set('assignedTo')->eq('closed')->where('id')->eq($bug->id)->exec();

        return !dao::isError();
    }

    /**
     * 获取可以关联的 bug 列表。
     * Get bugs to link.
     *
     * @param  int    $bugID
     * @param  bool   $bySearch
     * @param  int    $queryID
     * @param  string $excludeBugs
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getBugs2Link(int $bugID, bool $bySearch = false, string $excludeBugs = '', int $queryID = 0, object $pager = null): array
    {
        $bug = $this->getByID($bugID);

        $excludeBugs .= ",{$bug->id},{$bug->linkBug}";

        if($bySearch) return $this->bugTao->getBySearch((array)$bug->product, $branch = 'all', $projectID = 0, $queryID, $excludeBugs, $orderBy = 'id desc', $pager);

        return $this->dao->select('*')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->andWhere('id')->notin($excludeBugs)
            ->andWhere('product')->eq($bug->product)
            ->beginIF($bug->project)->andWhere('project')->eq($bug->project)->fi()
            ->beginIF($bug->execution)->andWhere('execution')->eq($bug->execution)->fi()
            ->orderBy('id desc')
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get statistic.
     *
     * @param  int    $productID
     * @param  string $endDate
     * @param  int    $days
     * @access public
     * @return void
     */
    public function getStatistic($productID = 0, $endDate = '', $days = 30)
    {
        $startDate = '';
        if(empty($endDate)) $endDate = date('Y-m-d');

        $dateArr = array();
        for($day = $days - 1; $day >= 0; $day--)
        {
            $time = strtotime(-$day . ' day', strtotime($endDate));
            $date = date('m/d', $time);
            $dateArr[$date] = new stdClass();
            $dateArr[$date]->num  = 0;
            $dateArr[$date]->date = $date;

            if($day == $days -1) $startDate = date('Y-m-d', $time) . ' 00:00:00';
        }

        $dateFields = array('openedDate', 'resolvedDate', 'closedDate');
        $staticData = array();
        foreach($dateFields as $field)
        {
            $bugCount = $this->dao->select("count(id) as num, date_format($field, '%m/%d') as date")->from(TABLE_BUG)
                ->where('product')->eq($productID)
                ->andWhere($field)->ne('0000-00-00 00:00:00')
                ->andWhere('deleted')->eq(0)
                ->andWhere($field)->between($startDate, $endDate . ' 23:50:59')
                ->groupBy('date')
                ->fetchAll('date');
            $staticData[$field] = array_merge($dateArr, $bugCount);
        }
        return $staticData;
    }

    /**
     * Build search form.
     *
     * @param  int    $productID
     * @param  array  $products
     * @param  int    $queryID
     * @param  string $actionURL
     * @param  int    $branch
     * @access public
     * @return void
     */
    public function buildSearchForm($productID, $products, $queryID, $actionURL, $branch = 0)
    {
        $projectID     = $this->lang->navGroup->bug == 'qa' ? 0 : $this->session->project;
        $productParams = ($productID and isset($products[$productID])) ? array($productID => $products[$productID]) : $products;
        $productParams = $productParams + array('all' => $this->lang->all);
        $projectParams = $this->getProjects($productID);
        $projectParams = $projectParams + array('all' => $this->lang->bug->allProject);

        /* Get all modules. */
        $modules = array();
        $this->loadModel('tree');
        if($productID) $modules = $this->tree->getOptionMenu($productID, 'bug', 0, $branch);
        if(!$productID)
        {
            foreach($products as $id => $productName) $modules += $this->tree->getOptionMenu($id, 'bug');
        }

        $this->config->bug->search['actionURL'] = $actionURL;
        $this->config->bug->search['queryID']   = $queryID;
        $this->config->bug->search['params']['project']['values']       = $projectParams;
        $this->config->bug->search['params']['product']['values']       = $productParams;
        $this->config->bug->search['params']['plan']['values']          = $this->loadModel('productplan')->getPairs($productID);
        $this->config->bug->search['params']['module']['values']        = $modules;
        $this->config->bug->search['params']['execution']['values']     = $this->loadModel('product')->getExecutionPairsByProduct($productID, '0', (string)$projectID);
        $this->config->bug->search['params']['severity']['values']      = array(0 => '') + $this->lang->bug->severityList; //Fix bug #939.
        $this->config->bug->search['params']['openedBuild']['values']   = $this->loadModel('build')->getBuildPairs($productID, 'all', 'withbranch|releasetag');
        $this->config->bug->search['params']['resolvedBuild']['values'] = $this->config->bug->search['params']['openedBuild']['values'];
        if($this->session->currentProductType == 'normal')
        {
            unset($this->config->bug->search['fields']['branch']);
            unset($this->config->bug->search['params']['branch']);
        }
        else
        {
            $this->config->bug->search['fields']['branch'] = $this->lang->product->branch;
            $this->config->bug->search['params']['branch']['values']  = array('' => '', 0 => $this->lang->branch->main) + $this->loadModel('branch')->getPairs($productID, 'noempty') + array('all' => $this->lang->branch->all);
        }

        $this->loadModel('search')->setSearchParams($this->config->bug->search);
    }

    /**
     * Process the openedBuild and resolvedBuild fields for bugs.
     *
     * @param  array  $bugs
     * @access public
     * @return array
     */
    public function processBuildForBugs($bugs)
    {
        $productIdList = array();
        foreach($bugs as $bug) $productIdList[$bug->id] = $bug->product;
        $builds = $this->loadModel('build')->getBuildPairs(array_unique($productIdList), 'all', $params = '');

        /* Process the openedBuild and resolvedBuild fields. */
        foreach($bugs as $bug)
        {
            $openBuildIdList = explode(',', $bug->openedBuild);
            $openedBuild = '';
            foreach($openBuildIdList as $buildID)
            {
                $openedBuild .= isset($builds[$buildID]) ? $builds[$buildID] : $buildID;
                $openedBuild .= ',';
            }
            $bug->openedBuild   = rtrim($openedBuild, ',');
            $bug->resolvedBuild = isset($builds[$bug->resolvedBuild]) ? $builds[$bug->resolvedBuild] : $bug->resolvedBuild;
        }
        return $bugs;
    }

    /**
     * Get user bugs.
     *
     * @param  string $account
     * @param  string $type
     * @param  string $orderBy
     * @param  int    $limit
     * @param  object $pager
     * @param  int    $executionID
     * @param  int    $queryID
     * @access public
     * @return array
     */
    public function getUserBugs($account, $type = 'assignedTo', $orderBy = 'id_desc', $limit = 0, $pager = null, $executionID = 0, $queryID = 0)
    {
        $moduleName = $this->app->rawMethod == 'work' ? 'workBug' : 'contributeBug';
        $queryName  = $moduleName . 'Query';
        $formName   = $moduleName . 'Form';
        $bugIdList  = array();
        if($moduleName == 'contributeBug')
        {
            $bugsAssignedByMe = $this->loadModel('my')->getAssignedByMe($account, 0, '', $orderBy, 'bug');
            foreach($bugsAssignedByMe as $bugID => $bug) $bugIdList[$bugID] = $bugID;
        }

        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set($queryName, $query->sql);
                $this->session->set($formName, $query->form);
            }
            else
            {
                $this->session->set($queryName, ' 1 = 1');
            }
        }
        else
        {
            if($this->session->$queryName === false) $this->session->set($queryName, ' 1 = 1');
        }
        $query = $this->session->$queryName;
        $query = preg_replace('/`(\w+)`/', 't1.`$1`', $query);

        if($type != 'bySearch' and !$this->loadModel('common')->checkField(TABLE_BUG, $type)) return array();
        return $this->dao->select("t1.*, t2.name AS productName, t2.shadow, IF(t1.`pri` = 0, {$this->config->maxPriValue}, t1.`pri`) AS priOrder, IF(t1.`severity` = 0, {$this->config->maxPriValue}, t1.`severity`) AS severityOrder")->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq('0')
            ->beginIF($type == 'bySearch')->andWhere($query)->fi()
            ->beginIF($executionID)->andWhere('t1.execution')->eq($executionID)->fi()
            ->beginIF($type != 'closedBy' and $this->app->moduleName == 'block')->andWhere('t1.status')->ne('closed')->fi()
            ->beginIF($type != 'all' and $type != 'bySearch')->andWhere("t1.`$type`")->eq($account)->fi()
            ->beginIF($type == 'bySearch' and $moduleName == 'workBug')->andWhere("t1.assignedTo")->eq($account)->fi()
            ->beginIF($type == 'assignedTo' and $moduleName == 'workBug')->andWhere('t1.status')->ne('closed')->fi()
            ->beginIF($type == 'bySearch' and $moduleName == 'contributeBug')
            ->andWhere('t1.openedBy', 1)->eq($account)
            ->orWhere('t1.closedBy')->eq($account)
            ->orWhere('t1.resolvedBy')->eq($account)
            ->orWhere('t1.id')->in($bugIdList)
            ->markRight(1)
            ->fi()
            ->orderBy($orderBy)
            ->beginIF($limit > 0)->limit($limit)->fi()
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get bug pairs of a user.
     *
     * @param  int       $account
     * @param  bool      $appendProduct
     * @param  int       $limit
     * @param  array     $skipProductIdList
     * @param  array     $skipExecutionIdList
     * @param  int|array $appendBugID
     * @access public
     * @return array
     */
    public function getUserBugPairs($account, $appendProduct = true, $limit = 0, $skipProductIdList = array(), $skipExecutionIdList = array(), $appendBugID = 0)
    {
        $deletedProjectIdList = $this->dao->select('*')->from(TABLE_PROJECT)->where('deleted')->eq(1)->fetchPairs('id', 'id');

        $bugs = array();
        $stmt = $this->dao->select('t1.id, t1.title, t2.name as product')
            ->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')
            ->on('t1.product=t2.id')
            ->where('t1.assignedTo')->eq($account)
            ->andWhere('t1.status')->ne('closed')
            ->beginIF(!empty($deletedProjectIdList))->andWhere('t1.execution')->notin($deletedProjectIdList)->fi()
            ->beginIF(!empty($skipProductIdList))->andWhere('t1.product')->notin($skipProductIdList)->fi()
            ->beginIF(!empty($skipExecutionIdList))->andWhere('t1.execution')->notin($skipExecutionIdList)->fi()
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->beginIF(!empty($appendBugID))->orWhere('t1.id')->in($appendBugID)->fi()
            ->orderBy('id desc')
            ->beginIF($limit > 0)->limit($limit)->fi()
            ->query();
        while($bug = $stmt->fetch())
        {
            if($appendProduct) $bug->title = $bug->product . ' / ' . $bug->title;
            $bugs[$bug->id] = $bug->title;
        }
        return $bugs;
    }

    /**
     * Get bugs of a project.
     *
     * @param  int    $projectID
     * @param  int    $productID
     * @param  int    $branchID
     * @param  int    $build
     * @param  string $type
     * @param  int    $param
     * @param  string $orderBy
     * @param  string $excludeBugs
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getProjectBugs($projectID, $productID = 0, $branchID = 0, $build = 0, $type = '', $param = 0, $orderBy = 'id_desc', $excludeBugs = '', $pager = null)
    {
        $type = strtolower($type);
        if(strpos($orderBy, 'pri_') !== false) $orderBy = str_replace('pri_', 'priOrder_', $orderBy);
        if(strpos($orderBy, 'severity_') !== false) $orderBy = str_replace('severity_', 'severityOrder_', $orderBy);

        if($type == 'bysearch')
        {
            $queryID = (int)$param;
            if($this->session->projectBugQuery === false) $this->session->set('projectBugQuery', ' 1 = 1');
            if($queryID)
            {
                $query = $this->loadModel('search')->getQuery($queryID);
                if($query)
                {
                    $this->session->set('projectBugQuery', $query->sql);
                    $this->session->set('projectBugForm', $query->form);
                }
            }

            $bugQuery = $this->getBugQuery($this->session->projectBugQuery);

            $bugs = $this->dao->select("*, IF(`pri` = 0, {$this->config->maxPriValue}, `pri`) as priOrder, IF(`severity` = 0, {$this->config->maxPriValue}, `severity`) as severityOrder")->from(TABLE_BUG)
                ->where($bugQuery)
                ->andWhere('project')->eq((int)$projectID)
                ->andWhere('deleted')->eq(0)
                ->beginIF($excludeBugs)->andWhere('id')->notIN($excludeBugs)->fi()
                ->beginIF(!empty($productID) and strpos($bugQuery, 'product') === false and strpos($bugQuery, '`product` IN') === false)->andWhere('product')->eq($productID)->fi()
                ->beginIF(!empty($productID) and strpos($bugQuery, 'product') === false and strpos($bugQuery, '`product` IN') === false and $branchID != 'all')->andWhere('branch')->eq($branchID)->fi()
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }
        else
        {
            $bugs = $this->dao->select("t1.*, IF(t1.`pri` = 0, {$this->config->maxPriValue}, t1.`pri`) as priOrder, IF(t1.`severity` = 0, {$this->config->maxPriValue}, t1.`severity`) as severityOrder")->from(TABLE_BUG)->alias('t1')
                ->leftJoin(TABLE_MODULE)->alias('t2')->on('t1.module=t2.id')
                ->where('t1.deleted')->eq(0)
                ->beginIF(empty($build))->andWhere('t1.project')->eq($projectID)->fi()
                ->beginIF(!empty($productID))->andWhere('t1.product')->eq($productID)->fi()
                ->beginIF(!empty($productID) and $branchID != 'all')->andWhere('t1.branch')->eq($branchID)->fi()
                ->beginIF($type == 'unresolved')->andWhere('t1.status')->eq('active')->fi()
                ->beginIF($type == 'noclosed')->andWhere('t1.status')->ne('closed')->fi()
                ->beginIF($type == 'assignedtome')->andWhere('t1.assignedTo')->eq($this->app->user->account)->fi()
                ->beginIF($type == 'openedbyme')->andWhere('t1.openedBy')->eq($this->app->user->account)->fi()
                ->beginIF(!empty($param))->andWhere('t2.path')->like("%,$param,%")->andWhere('t2.deleted')->eq(0)->fi()
                ->beginIF($build)->andWhere("CONCAT(',', t1.openedBuild, ',') like '%,$build,%'")->fi()
                ->beginIF($excludeBugs)->andWhere('t1.id')->notIN($excludeBugs)->fi()
                ->orderBy($orderBy)->page($pager)->fetchAll();
        }

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug', false);

        return $bugs;
    }

    /**
     * Get bugs of a execution.
     *
     * @param  int          $executionID
     * @param  int          $productID
     * @param  int          $branchID
     * @param  string|array $builds
     * @param  string       $type
     * @param  int          $param
     * @param  string       $orderBy
     * @param  string       $excludeBugs
     * @param  object       $pager
     * @access public
     * @return array
     */
    public function getExecutionBugs($executionID, $productID = 0, $branchID = 'all', $builds = 0, $type = '', $param = 0, $orderBy = 'id_desc', $excludeBugs = '', $pager = null)
    {
        $type = strtolower($type);
        if(strpos($orderBy, 'pri_') !== false) $orderBy = str_replace('pri_', 'priOrder_', $orderBy);
        if(strpos($orderBy, 'severity_') !== false) $orderBy = str_replace('severity_', 'severityOrder_', $orderBy);

        if($type == 'bysearch')
        {
            $queryID = (int)$param;
            if($this->session->executionBugQuery === false) $this->session->set('executionBugQuery', ' 1 = 1');
            if($queryID)
            {
                $query = $this->loadModel('search')->getQuery($queryID);
                if($query)
                {
                    $this->session->set('executionBugQuery', $query->sql);
                    $this->session->set('executionBugForm', $query->form);
                }
            }

            $bugQuery = $this->getBugQuery($this->session->executionBugQuery);

            $bugs = $this->dao->select("*, IF(`pri` = 0, {$this->config->maxPriValue}, `pri`) as priOrder, IF(`severity` = 0, {$this->config->maxPriValue}, `severity`) as severityOrder")->from(TABLE_BUG)
                ->where($bugQuery)
                ->andWhere('execution')->eq((int)$executionID)
                ->andWhere('deleted')->eq(0)
                ->beginIF($excludeBugs)->andWhere('id')->notIN($excludeBugs)->fi()
                ->beginIF(!empty($productID) and strpos($bugQuery, 'product') === false and strpos($bugQuery, '`product` IN') === false)->andWhere('product')->eq($productID)->fi()
                ->beginIF(!empty($productID) and $branchID !== 'all' and strpos($bugQuery, 'product') === false and strpos($bugQuery, '`product` IN') === false)->andWhere('branch')->eq($branchID)->fi()
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }
        else
        {
            $condition = '';
            if($builds)
            {
                if(!is_array($builds)) $builds = explode(',', $builds);

                $conditions = array();
                foreach($builds as $build)
                {
                    if($build) $conditions[] = "FIND_IN_SET('$build', t1.openedBuild)";
                }
                $condition = implode(' OR ', $conditions);
                $condition = "($condition)";
            }
            $bugs = $this->dao->select("t1.*, IF(t1.`pri` = 0, {$this->config->maxPriValue}, t1.`pri`) as priOrder, IF(t1.`severity` = 0, {$this->config->maxPriValue}, t1.`severity`) as severityOrder")->from(TABLE_BUG)->alias('t1')
                ->leftJoin(TABLE_MODULE)->alias('t2')->on('t1.module=t2.id')
                ->where('t1.deleted')->eq(0)
                ->beginIF(!empty($productID) and $branchID !== 'all')->andWhere('t1.branch')->eq($branchID)->fi()
                ->beginIF(empty($builds))->andWhere('t1.execution')->eq($executionID)->fi()
                ->beginIF(!empty($productID))->andWhere('t1.product')->eq($productID)->fi()
                ->beginIF($type == 'unresolved')->andWhere('t1.status')->eq('active')->fi()
                ->beginIF($type == 'noclosed')->andWhere('t1.status')->ne('closed')->fi()
                ->beginIF($condition)->andWhere("$condition")->fi()
                ->beginIF(!empty($param))->andWhere('t2.path')->like("%,$param,%")->andWhere('t2.deleted')->eq(0)->fi()
                ->beginIF($excludeBugs)->andWhere('t1.id')->notIN($excludeBugs)->fi()
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');
        }

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'bug', false);

        return $bugs;
    }

    /**
     * Get product left bugs.
     *
     * @param  int|string $buildIdList
     * @param  int        $productID
     * @param  int        $branch
     * @param  string     $linkedBugs
     * @param  object     $pager
     * @access public
     * @return array
     */
    public function getProductLeftBugs($buildIdList, $productID, $branch = '', $linkedBugs = '', $pager = null)
    {
        $executionIdList = $this->getLinkedExecutionByIdList($buildIdList);
        if(empty($executionIdList)) return array();

        $executions = $this->dao->select('*')->from(TABLE_EXECUTION)->where('id')->in($executionIdList)->fetchAll();
        $minBegin   = '';
        $maxEnd     = '';
        foreach($executions as $execution)
        {
            if(empty($minBegin) or $minBegin > $execution->begin) $minBegin = $execution->begin;
            if(empty($maxEnd)   or $maxEnd   < $execution->end)   $maxEnd   = $execution->end;
        }

        $beforeBuilds = $this->dao->select('t1.id')->from(TABLE_BUILD)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution=t2.id')
            ->where('t1.product')->eq($productID)
            ->andWhere('t2.status')->ne('done')
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t1.date')->lt($minBegin)
            ->fetchPairs('id', 'id');

        return $this->dao->select('*')->from(TABLE_BUG)->where('deleted')->eq(0)
            ->andWhere('product')->eq($productID)
            ->andWhere('toStory')->eq(0)
            ->andWhere('openedDate')->ge($minBegin)
            ->andWhere('openedDate')->le($maxEnd)
            ->andWhere("(status = 'active' OR resolvedDate > '{$maxEnd}')")
            ->andWhere('openedBuild')->notin($beforeBuilds)
            ->beginIF($linkedBugs)->andWhere('id')->notIN($linkedBugs)->fi()
            ->beginIF($branch !== '')->andWhere('branch')->in("0,$branch")->fi()
            ->page($pager)
            ->fetchAll();
    }

    /**
     * get Product Bug Pairs
     *
     * @param  int        $productID
     * @param  int|string $branch
     * @access public
     * @return void
     */
    public function getProductBugPairs($productID, $branch = '')
    {
        $bugs = array('' => '');
        $data = $this->dao->select('id, title')->from(TABLE_BUG)
            ->where('product')->eq((int)$productID)
            ->beginIF(!$this->app->user->admin)->andWhere('execution')->in('0,' . $this->app->user->view->sprints)->fi()
            ->beginIF($branch !== '')->andWhere('branch')->in($branch)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy('id desc')
            ->fetchAll();
        foreach($data as $bug)
        {
            $bugs[$bug->id] = $bug->id . ':' . $bug->title;
        }
        return $bugs;
    }

    /**
     * 获取产品成员键对。
     * get Product member pairs.
     *
     * @param  int    $productID
     * @param  string $branchID
     * @access public
     * @return array
     */
    public function getProductMemberPairs(int $productID, string $branchID = ''): array
    {
        if(defined('TUTORIAL')) return $this->loadModel('tutorial')->getTeamMembersPairs();

        $projects = $this->loadModel('product')->getProjectPairsByProduct($productID, $branchID);

        return $this->loadModel('user')->getTeamMemberPairs(array_keys($projects));
    }

    /**
     * Get bugs according to buildID and productID.
     *
     * @param  int|string $buildIdList
     * @param  int        $productID
     * @param  string     $branch
     * @param  string     $linkedBugs
     * @param  object     $pager
     * @access public
     * @return array
     */
    public function getReleaseBugs($buildIdList, $productID, $branch = 0, $linkedBugs = '', $pager = null)
    {
        $executionIdList = $this->getLinkedExecutionByIdList($buildIdList);
        if(empty($executionIdList)) return array();

        $executions = $this->dao->select('id,type,begin')->from(TABLE_EXECUTION)->where('id')->in($executionIdList)->fetchAll('id');
        $condition  = 'execution NOT ' . helper::dbIN($executionIdList);
        $minBegin   = '';
        foreach($executions as $execution)
        {
            if(empty($minBegin) or $minBegin > $execution->begin) $minBegin = $execution->begin;
            $condition .= " OR (`execution` = '{$execution->id}' AND openedDate < '{$execution->begin}')";
        }

        return $this->dao->select('*')->from(TABLE_BUG)
            ->where('resolvedDate')->ge($minBegin)
            ->andWhere('resolution')->ne('postponed')
            ->andWhere('product')->eq($productID)
            ->beginIF($linkedBugs)->andWhere('id')->notIN($linkedBugs)->fi()
            ->beginIF($branch)->andWhere('branch')->in("0,$branch")->fi()
            ->andWhere("($condition)")
            ->andWhere('deleted')->eq(0)
            ->orderBy('openedDate ASC')
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get linked execution by build id list.
     *
     * @param  string $buildIdList
     * @access public
     * @return array
     */
    public function getLinkedExecutionByIdList($buildIdList)
    {
        $builds = $this->dao->select('id,execution,builds')->from(TABLE_BUILD)->where('id')->in($buildIdList)->fetchAll('id');

        $executionIdList   = array();
        $linkedBuildIdList = array();
        foreach($builds as $build)
        {
            if($build->builds) $linkedBuildIdList = array_merge($linkedBuildIdList, explode(',', $build->builds));

            if(empty($build->execution)) continue;
            $executionIdList[$build->execution] = $build->execution;
        }
        if($linkedBuildIdList)
        {
            $linkedBuilds = $this->dao->select('*')->from(TABLE_BUILD)->where('id')->in(array_unique($linkedBuildIdList))->fetchAll('id');
            foreach($linkedBuilds as $build)
            {
                if(empty($build->execution)) continue;
                $executionIdList[$build->execution] = $build->execution;
            }
        }
        return $executionIdList;
    }

    /**
     * Get bugs of a story.
     *
     * @param  int    $storyID
     * @param  int    $executionID
     * @access public
     * @return array
     */
    public function getStoryBugs($storyID, $executionID = 0)
    {
        return $this->dao->select('id, title, pri, type, status, assignedTo, resolvedBy, resolution')
            ->from(TABLE_BUG)
            ->where('story')->eq((int)$storyID)
            ->beginIF($executionID)->andWhere('execution')->eq($executionID)->fi()
            ->andWhere('deleted')->eq(0)
            ->fetchAll('id');
    }

    /**
     * Get case bugs.
     *
     * @param  int    $runID
     * @param  int    $caseID
     * @param  int    $version
     * @access public
     * @return void
     */
    public function getCaseBugs($runID, $caseID = 0, $version = 0)
    {
        return $this->dao->select('*')->from(TABLE_BUG)
            ->where('1=1')
            ->beginIF($runID)->andWhere('`result`')->eq($runID)->fi()
            ->beginIF($runID == 0 and $caseID)->andWhere('`case`')->eq($caseID)->fi()
            ->beginIF($version)->andWhere('`caseVersion`')->eq($version)->fi()
            ->andWhere('deleted')->eq(0)
            ->fetchAll('id');
    }

    /**
     * Get counts of some stories' bugs.
     *
     * @param  array  $stories
     * @param  int    $executionID
     * @access public
     * @return int
     */
    public function getStoryBugCounts($stories, $executionID = 0)
    {
        if(empty($stories)) return array();
        $bugCounts = $this->dao->select('story, COUNT(*) AS bugs')
            ->from(TABLE_BUG)
            ->where('story')->in($stories)
            ->andWhere('deleted')->eq(0)
            ->beginIF($executionID)->andWhere('execution')->eq($executionID)->fi()
            ->groupBy('story')
            ->fetchPairs();
        foreach($stories as $storyID) if(!isset($bugCounts[$storyID])) $bugCounts[$storyID] = 0;
        return $bugCounts;
    }

    /**
     * Get bug info from a result.
     *
     * @param  int    $resultID
     * @param  int    $caseID
     * @param  int    $version
     * @access public
     * @return array
     */
    public function getBugInfoFromResult($resultID, $caseID = 0, $version = 0, $stepIdList = '')
    {
        $title    = '';
        $bugSteps = '';
        $steps    = explode('_', trim($stepIdList, '_'));

        $result = $this->dao->findById($resultID)->from(TABLE_TESTRESULT)->fetch();
        if($caseID > 0)
        {
            $run = new stdclass();
            $run->case = $this->loadModel('testcase')->getById($caseID, $result->version);
        }
        else
        {
            $run = $this->loadModel('testtask')->getRunById($result->run);
        }

        $title       = $run->case->title;
        $caseSteps   = $run->case->steps;
        $stepResults = unserialize($result->stepResults);
        if($run->case->precondition != '')
        {
            $bugSteps = "<p>[" . $this->lang->testcase->precondition . "]</p>" . "\n" . $run->case->precondition;
        }

        if(!empty($stepResults))
        {
            $bugStep   = '';
            $bugResult = isset($stepResults[0]) ? $stepResults[0]['real'] : '';
            $bugExpect = '';
            foreach($steps as $stepId)
            {
                if(!isset($caseSteps[$stepId])) continue;
                $step = $caseSteps[$stepId];

                $i = $this->getCaseStepIndex($step);

                $stepDesc   = str_replace("\n", "<br />", $step->desc);
                $stepExpect = str_replace("\n", "<br />", $step->expect);
                $stepResult = (!isset($stepResults[$stepId]) or empty($stepResults[$stepId]['real'])) ? '' : $stepResults[$stepId]['real'];

                $bugStep   .= $i . '. ' . $stepDesc . "<br />";
                $bugResult .= $i . '. ' . $stepResult . "<br />";
                $bugExpect .= $i . '. ' . $stepExpect . "<br />";
            }

            $bugSteps .= $bugStep   ? str_replace('<br/>', '', $this->lang->bug->tplStep)   . $bugStep   : $this->lang->bug->tplStep;
            $bugSteps .= $bugResult ? str_replace('<br/>', '', $this->lang->bug->tplResult) . $bugResult : $this->lang->bug->tplResult;
            $bugSteps .= $bugExpect ? str_replace('<br/>', '', $this->lang->bug->tplExpect) . $bugExpect : $this->lang->bug->tplExpect;
        }
        else
        {
            $bugSteps .= $this->lang->bug->tplStep;
            $bugSteps .= $this->lang->bug->tplResult;
            $bugSteps .= $this->lang->bug->tplExpect;
        }

        if(!empty($run->task)) $testtask = $this->loadModel('testtask')->getById($run->task);
        $executionID = isset($testtask->execution) ? $testtask->execution : 0;

        if(!$executionID and $caseID > 0) $executionID = isset($run->case->execution) ? $run->case->execution : 0; // Fix feedback #1043.
        if(!$executionID and $this->app->tab == 'execution') $executionID = $this->session->execution;

        return array('title' => $title, 'steps' => $bugSteps, 'storyID' => $run->case->story, 'moduleID' => $run->case->module, 'version' => $run->case->version, 'executionID' => $executionID);
    }

    /**
     * 统计迭代 bug 数量。
     * Statistics by bug execution.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerExecution(): array
    {
        $datas = $this->dao->select('execution AS name, count(execution) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('execution')->orderBy('value_desc')->fetchAll('name');
        if(!$datas) return array();

        $executionPairs = $this->loadModel('execution')->getPairs($this->session->project);
        $maxLength      = common::checkNotCN() ? 22 : 12;
        foreach($datas as $executionID => $data)
        {
            $data->name  = zget($executionPairs, $executionID, $this->lang->report->undefined);
            $data->title = $data->name;

            if(mb_strlen($data->name, 'UTF-8') > $maxLength) $data->name = mb_substr($data->name, 0, $maxLength, 'UTF-8') . '...';
        }
        return $datas;
    }

    /**
     * 统计版本 Bug 数量。
     * Statistics by bug build.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerBuild(): array
    {
        $datas = $this->dao->select('openedBuild AS name, COUNT(openedBuild) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('openedBuild')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();

        foreach($datas as $buildIdList => $data)
        {
            if(is_int($buildIdList)) continue;
            if(strpos(trim($buildIdList, ','), ',') === false) continue;

            $openedBuildIdList = explode(',', $buildIdList);

            /* Bug 可以关联多个影响版本，一个版本被多个 bug 关联时，累加 bug 数量。*/
            /* Bugs can be associated with multiple builds. When a build is associated with multiple bugs, accumulated bugs.*/
            foreach($openedBuildIdList as $buildID)
            {
                if(!isset($datas[$buildID]))
                {
                    $datas[$buildID] = new stdclass();
                    $datas[$buildID]->name  = $buildID;
                    $datas[$buildID]->value = 0;
                }

                $datas[$buildID]->value += $data->value;
            }

            unset($datas[$buildIdList]);
        }

        /* 统计最后一次查询的所属产品列表。*/
        /* Get products in the last query. */
        $products = $this->session->product;
        if($this->reportCondition() !== true)
        {
            preg_match('/`product` IN \((?P<productIdList>.+)\)/', $this->reportCondition(), $matchs);
            if(!empty($matchs) && isset($matchs['productIdList'])) $products = str_replace('\'', '', $matchs['productIdList']);
        }

        $builds = $this->loadModel('build')->getBuildPairs($products, $branch = 0, $params = 'hasdeleted');

        $this->app->loadLang('report');
        foreach($datas as $buildID => $data) $data->name = zget($builds, $buildID, $this->lang->report->undefined);

        ksort($datas);

        return $datas;
    }

    /**
     * 统计模块 bug 数量。
     * Statistics by bug module.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerModule(): array
    {
        $datas = $this->dao->select('module AS name, COUNT(module) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('module')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();

        $modules = $this->loadModel('tree')->getModulesName(array_keys($datas), true, true);

        foreach($datas as $moduleID => $data) $data->name = zget($modules, $moduleID, '/');

        return $datas;
    }

    /**
     * 统计每天新增 bug 数量。
     * Statistics by the number of created bug daily.
     *
     * @access public
     * @return array
     */
    public function getDataOfOpenedBugsPerDay(): array
    {
        return $this->dao->select('DATE_FORMAT(openedDate, "%Y-%m-%d") AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('name')->fetchAll();
    }

    /**
     * 统计每天解决 bug 数量。
     * Statistics by the number of resolved bug.
     *
     * @access public
     * @return array
     */
    public function getDataOfResolvedBugsPerDay(): array
    {
        return $this->dao->select('DATE_FORMAT(resolvedDate, "%Y-%m-%d") AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where($this->reportCondition())
            ->groupBy('name')
            ->having('name != 0000-00-00')
            ->orderBy('name')
            ->fetchAll();
    }

    /**
     * 统计每天关闭 bug 数量。
     * Statistics by the number of closed bug.
     *
     * @access public
     * @return array
     */
    public function getDataOfClosedBugsPerDay(): array
    {
        return $this->dao->select('DATE_FORMAT(closedDate, "%Y-%m-%d") AS name, COUNT(*) AS value')->from(TABLE_BUG)
            ->where($this->reportCondition())
            ->groupBy('name')
            ->having('name != 0000-00-00')
            ->orderBy('name')
            ->fetchAll();
    }

    /**
     * 统计每人提交的 bug 数量。
     * Statistics by bug creator.
     *
     * @access public
     * @return array
     */
    public function getDataOfOpenedBugsPerUser(): array
    {
        $datas = $this->dao->select('openedBy AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();

        if(!isset($this->users)) $this->users = $this->loadModel('user')->getPairs('noletter');
        foreach($datas as $account => $data) $data->name = zget($this->users, $account);

        return $datas;
    }

    /**
     * 统计每人解决的 bug 数量。
     * Statistics by bug resolver.
     *
     * @access public
     * @return array
     */
    public function getDataOfResolvedBugsPerUser(): array
    {
        $datas = $this->dao->select('resolvedBy AS name, COUNT(*) AS value')
            ->from(TABLE_BUG)->where($this->reportCondition())
            ->andWhere('resolvedBy')->ne('')
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');
        if(!$datas) return array();

        if(!isset($this->users)) $this->users = $this->loadModel('user')->getPairs('noletter');
        foreach($datas as $account => $data) $data->name = zget($this->users, $account);

        return $datas;
    }

    /**
     * 统计每人关闭的 bug 数量。
     * Statistics by bug closer.
     *
     * @access public
     * @return array
     */
    public function getDataOfClosedBugsPerUser(): array
    {
        $datas = $this->dao->select('closedBy AS name, COUNT(*) AS value')
            ->from(TABLE_BUG)
            ->where($this->reportCondition())
            ->andWhere('closedBy')->ne('')
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');
        if(!$datas) return array();

        if(!isset($this->users)) $this->users = $this->loadModel('user')->getPairs('noletter');
        foreach($datas as $account => $data) $data->name = zget($this->users, $account);

        return $datas;
    }

    /**
     * 按照 bug 严重程度统计。
     * Statistics by bug severity
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerSeverity(): array
    {
        $datas = $this->dao->select('severity AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();

        foreach($datas as $severity => $data) $data->name = $this->lang->bug->report->bugsPerSeverity->graph->xAxisName . ':' . zget($this->lang->bug->severityList, $severity);
        return $datas;
    }

    /**
     * 按照解决方案统计。
     * Statistics by bug resolution.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerResolution(): array
    {
        $datas = $this->dao->select('resolution AS name, COUNT(*) AS value')
            ->from(TABLE_BUG)
            ->where($this->reportCondition())
            ->andWhere('resolution')->ne('')
            ->groupBy('name')
            ->orderBy('value DESC')
            ->fetchAll('name');
        if(!$datas) return array();

        foreach($datas as $resolution => $data) $data->name = zget($this->lang->bug->resolutionList, $resolution);

        return $datas;
    }

    /**
     * 按 bug 状态统计。
     * Statistics by bug status.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerStatus(): array
    {
        $datas = $this->dao->select('status AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();

        foreach($datas as $status => $data) $data->name = zget($this->lang->bug->statusList, $status);

        return $datas;
    }

    /**
     * 按照 bug 优先级统计。
     * Statistics by bug pri.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerPri(): array
    {
        $datas = $this->dao->select('pri AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();

        foreach($datas as $data) $data->name = $this->lang->bug->report->bugsPerPri->graph->xAxisName . ':' . zget($this->lang->bug->priList, $data->name);

        return $datas;
    }

    /**
     * 按照 bug 激活次数统计。
     * Statistics by bug activation times.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerActivatedCount(): array
    {
        $datas = $this->dao->select('activatedCount AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();

        foreach($datas as $data) $data->name = $this->lang->bug->report->bugsPerActivatedCount->graph->xAxisName . ':' . $data->name;

        return $datas;
    }

    /**
     * 按照 bug 类型统计。
     * Statistics by bug type.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerType(): array
    {
        $datas = $this->dao->select('type AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();

        foreach($datas as $type => $data) $data->name = zget($this->lang->bug->typeList, $type);

        return $datas;
    }

    /**
     * 按照 bug 指派给统计。
     * Statistics by bug assignment.
     *
     * @access public
     * @return array
     */
    public function getDataOfBugsPerAssignedTo(): array
    {
        $datas = $this->dao->select('assignedTo AS name, COUNT(*) AS value')->from(TABLE_BUG)->where($this->reportCondition())->groupBy('name')->orderBy('value DESC')->fetchAll('name');
        if(!$datas) return array();

        if(!isset($this->users)) $this->users = $this->loadModel('user')->getPairs('noletter');
        foreach($datas as $account => $data) $data->name = zget($this->users, $account);

        return $datas;
    }

    /**
     * Get by Sonarqube id.
     *
     * @param  int    $sonarqubeID
     * @access public
     * @return array
     */
    public function getBySonarqubeID($sonarqubeID)
    {
        return $this->dao->select('issueKey')->from(TABLE_BUG)
            ->where('issueKey')->like("$sonarqubeID:%")
            ->fetchPairs();
    }

    /**
     * Get bug query.
     *
     * @param  string $bugQuery
     * @access public
     * @return string
     */
    public function getBugQuery($bugQuery)
    {
        $allProduct = "`product` = 'all'";
        if(strpos($bugQuery, $allProduct) !== false)
        {
            $products = $this->app->user->view->products;
            $bugQuery = str_replace($allProduct, '1', $bugQuery);
            $bugQuery = $bugQuery . ' AND `product` ' . helper::dbIN($products);
        }

        $allProject = "`project` = 'all'";
        if(strpos($bugQuery, $allProject) !== false)
        {
            $projects = $this->loadModel('project')->getPairs();
            if(is_array($projects)) $projectIdList = implode(',', array_keys($projects));
            $bugQuery = str_replace($allProject, '1', $bugQuery);
            $bugQuery = $bugQuery . ' AND `project` in (' . $projectIdList . ')';
        }

        /* Fix bug #2878. */
        if(strpos($bugQuery, ' `resolvedDate` ') !== false) $bugQuery = str_replace(' `resolvedDate` ', " `resolvedDate` != '0000-00-00 00:00:00' AND `resolvedDate` ", $bugQuery);
        if(strpos($bugQuery, ' `closedDate` ') !== false)   $bugQuery = str_replace(' `closedDate` ', " `closedDate` != '0000-00-00 00:00:00' AND `closedDate` ", $bugQuery);
        if(strpos($bugQuery, ' `story` ') !== false)
        {
            preg_match_all("/`story`[ ]+(NOT *)?LIKE[ ]+'%([^%]*)%'/Ui", $bugQuery, $out);
            if(!empty($out[2]))
            {
                foreach($out[2] as $searchValue)
                {
                    $story = $this->dao->select('id')->from(TABLE_STORY)->alias('t1')
                        ->leftJoin(TABLE_STORYSPEC)->alias('t2')->on('t1.id=t2.story')
                        ->where('t1.title')->like("%$searchValue%")
                        ->orWhere('t1.keywords')->like("%$searchValue%")
                        ->orWhere('t2.spec')->like("%$searchValue%")
                        ->orWhere('t2.verify')->like("%$searchValue%")
                        ->fetchPairs('id');
                    if(empty($story)) $story = array(0);

                    $bugQuery = preg_replace("/`story`[ ]+(NOT[ ]*)?LIKE[ ]+'%$searchValue%'/Ui", '`story` $1 IN (' . implode(',', $story) .')', $bugQuery);
                }
            }
            $bugQuery .= ' AND `story` != 0';
        }
        return $bugQuery;
    }

    /**
     * Adjust the action is clickable.
     *
     * @param  string $bug
     * @param  string $action
     * @param  string $module
     * @access public
     * @return void
     */
    public static function isClickable($object, $action, $module = 'bug')
    {
        $action = strtolower($action);

        if($module == 'bug' && $action == 'confirm')  return $object->status == 'active' && $object->confirmed == 0;
        if($module == 'bug' && $action == 'resolve')  return $object->status == 'active';
        if($module == 'bug' && $action == 'close')    return $object->status == 'resolved';
        if($module == 'bug' && $action == 'activate') return $object->status != 'active';
        if($module == 'bug' && $action == 'tostory')  return $object->status == 'active';

        return true;
    }

    /**
     * Get report condition from session.
     *
     * @access public
     * @return void
     */
    public function reportCondition()
    {
        if(isset($_SESSION['bugQueryCondition']))
        {
            if(!$this->session->bugOnlyCondition) return 'id in (' . preg_replace('/SELECT .* FROM/', 'SELECT t1.id FROM', $this->session->bugQueryCondition) . ')';
            return $this->session->bugQueryCondition;
        }
        return '1=1';
    }

    /**
     * Link bug to build and release
     *
     * @param  string|array    $bugs
     * @param  int    $resolvedBuild
     * @access public
     * @return bool
     */
    public function linkBugToBuild($bugs, $resolvedBuild)
    {
        if(empty($resolvedBuild) or $resolvedBuild == 'trunk') return true;
        if(is_array($bugs)) $bugs = implode(',', $bugs);

        $build     = $this->dao->select('id,product,bugs')->from(TABLE_BUILD)->where('id')->eq($resolvedBuild)->fetch();
        $buildBugs = $build->bugs . ',' . $bugs;
        $buildBugs = explode(',', trim($buildBugs, ','));
        $buildBugs = array_unique($buildBugs);
        $this->dao->update(TABLE_BUILD)->set('bugs')->eq(implode(',', $buildBugs))->where('id')->eq($resolvedBuild)->exec();

        $release = $this->dao->select('id,bugs')->from(TABLE_RELEASE)->where('product')->eq($build->product)->andWhere("(FIND_IN_SET('$resolvedBuild', build) or shadow = $resolvedBuild)")->andWhere('deleted')->eq('0')->fetch();
        if($release)
        {
            $releaseBugs = $release->bugs . ',' . $bugs;
            $releaseBugs = explode(',', trim($releaseBugs, ','));
            $releaseBugs = array_unique($releaseBugs);
            $this->dao->update(TABLE_RELEASE)->set('bugs')->eq(implode(',', $releaseBugs))->where('id')->eq($release->id)->exec();
        }

        return true;
    }

    /**
     * 更新相关 bug。
     * Update the linked bug.
     *
     * @param  int       $bugID
     * @param  string    $linkBug
     * @param  string    $oldLinkBug
     * @access protected
     * @return bool
     */
    protected function updateLinkBug(int $bugID, string $linkBug, string $oldLinkBug): bool
    {
        $linkBugs        = explode(',', $linkBug);
        $oldLinkBugs     = explode(',', $oldLinkBug);
        $addedLinkBugs   = array_diff($linkBugs, $oldLinkBugs);
        $removedLinkBugs = array_diff($oldLinkBugs, $linkBugs);
        $changedLinkBugs = array_merge($addedLinkBugs, $removedLinkBugs);
        $changedLinkBugs = $this->dao->select('id, linkbug')->from(TABLE_BUG)->where('id')->in(array_filter($changedLinkBugs))->fetchPairs();

        foreach($changedLinkBugs as $changedBugID => $linkBugs)
        {
            if(in_array($changedBugID, $addedLinkBugs))
            {
                $linkBugs = explode(',', $linkBugs);
                if(!empty($linkBugs) && !in_array($bugID, $linkBugs)) $linkBugs[] = $bugID;
            }
            else
            {
                $linkBugs = explode(',', $linkBugs);
                unset($linkBugs[array_search($bugID, $linkBugs)]);
            }

            $currentLinkBug = implode(',', array_filter($linkBugs));

            $this->dao->update(TABLE_BUG)->set('linkBug')->eq($currentLinkBug)->where('id')->eq($changedBugID)->exec();
        }

        return !dao::isError();
    }

    /**
     * Print cell data.
     *
     * @param  object $col
     * @param  object $bug
     * @param  array  $users
     * @param  array  $builds
     * @param  array  $branches
     * @param  array  $modulePairs
     * @param  array  $executions
     * @param  array  $plans
     * @param  array  $stories
     * @param  array  $tasks
     * @param  string $mode
     * @param  array  $projectPairs
     *
     * @access public
     * @return void
     */
    public function printCell($col, $bug, $users, $builds, $branches, $modulePairs, $executions = array(), $plans = array(), $stories = array(), $tasks = array(), $mode = 'datatable', $projectPairs = array())
    {
        /* Check the product is closed. */
        $canBeChanged = common::canBeChanged('bug', $bug);

        $canBatchEdit         = ($canBeChanged and common::hasPriv('bug', 'batchEdit'));
        $canBatchConfirm      = ($canBeChanged and common::hasPriv('bug', 'batchConfirm'));
        $canBatchClose        = common::hasPriv('bug', 'batchClose');
        $canBatchActivate     = ($canBeChanged and common::hasPriv('bug', 'batchActivate'));
        $canBatchChangeBranch = ($canBeChanged and common::hasPriv('bug', 'batchChangeBranch'));
        $canBatchChangeModule = ($canBeChanged and common::hasPriv('bug', 'batchChangeModule'));
        $canBatchResolve      = ($canBeChanged and common::hasPriv('bug', 'batchResolve'));
        $canBatchAssignTo     = ($canBeChanged and common::hasPriv('bug', 'batchAssignTo'));

        $canBatchAction = ($canBatchEdit or $canBatchConfirm or $canBatchClose or $canBatchActivate or $canBatchChangeBranch or $canBatchChangeModule or $canBatchResolve or $canBatchAssignTo);

        $canView = common::hasPriv('bug', 'view');

        $hasCustomSeverity = false;
        foreach($this->lang->bug->severityList as $severityKey => $severityValue)
        {
            if(!empty($severityKey) and (string)$severityKey != (string)$severityValue)
            {
                $hasCustomSeverity = true;
                break;
            }
        }

        $bugLink     = helper::createLink('bug', 'view', "bugID=$bug->id");
        $account     = $this->app->user->account;
        $id          = $col->id;
        $os          = '';
        $browser     = '';
        $osList      = explode(',', $bug->os);
        $browserList = explode(',', $bug->browser);
        foreach($osList as $value)
        {
            if(empty($value)) continue;
            $os .= $this->lang->bug->osList[$value] . ',';
        }
        foreach($browserList as $value)
        {
            if(empty($value)) continue;
            $browser .= zget($this->lang->bug->browserList, $value) . ',';
        }
        $os      = trim($os, ',');
        $browser = trim($browser, ',');
        if($col->show)
        {
            $class = "c-$id";
            $title = '';
            switch($id)
            {
                case 'id':
                    $class .= ' cell-id';
                    break;
                case 'status':
                    $class .= ' bug-' . $bug->status;
                    $title  = "title='" . $this->processStatus('bug', $bug) . "'";
                    break;
                case 'confirmed':
                    $class .= ' text-center';
                    break;
                case 'title':
                    $class .= ' text-left';
                    $title  = "title='{$bug->title}'";
                    break;
                case 'type':
                    $title  = "title='" . zget($this->lang->bug->typeList, $bug->type) . "'";
                    break;
                case 'assignedTo':
                    $class .= ' has-btn text-left';
                    if($bug->assignedTo == $account) $class .= ' red';
                    break;
                case 'resolvedBy':
                    $class .= ' c-user';
                    $title  = "title='" . zget($users, $bug->resolvedBy) . "'";
                    break;
                case 'openedBy':
                    $class .= ' c-user';
                    $title  = "title='" . zget($users, $bug->openedBy) . "'";
                    break;
                case 'project':
                    $title = "title='" . zget($projectPairs, $bug->project, '') . "'";
                    break;
                case 'plan':
                    $title = "title='" . zget($plans, $bug->plan, '') . "'";
                    break;
                case 'execution':
                    $title = "title='" . zget($executions, $bug->execution) . "'";
                    break;
                case 'resolvedBuild':
                    $class .= ' text-ellipsis';
                    $title  = "title='" . $bug->resolvedBuild . "'";
                    break;
                case 'os':
                    $class .= ' text-ellipsis';
                    $title  = "title='" . $os . "'";
                    break;
                case 'keywords':
                    $class .= ' text-left';
                    $title  = "title='{$bug->keywords}'";
                    break;
                case 'browser':
                    $class .= ' text-ellipsis';
                    $title  = "title='" . $browser . "'";
                    break;
                case 'deadline':
                    $class .= ' text-center';
                    break;
            }

            if($id == 'deadline' && isset($bug->delay) && $bug->status == 'active') $class .= ' delayed';
            if(strpos(',type,execution,story,plan,task,openedBuild,', ",{$id},") !== false) $class .= ' text-ellipsis';

            echo "<td class='" . $class . "' $title>";
            if($this->config->edition != 'open') $this->loadModel('flow')->printFlowCell('bug', $bug, $id);
            switch($id)
            {
            case 'id':
                if($canBatchAction)
                {
                    echo html::checkbox('bugIDList', array($bug->id => '')) . html::a(helper::createLink('bug', 'view', "bugID=$bug->id"), sprintf('%03d', $bug->id), '', "data-app='{$this->app->tab}'");
                }
                else
                {
                    printf('%03d', $bug->id);
                }
                break;
            case 'severity':
                $severityValue     = zget($this->lang->bug->severityList, $bug->severity);
                $hasCustomSeverity = !is_numeric($severityValue);
                if($hasCustomSeverity)
                {
                    echo "<span class='label-severity-custom' data-severity='{$bug->severity}' title='" . $severityValue . "'>" . $severityValue . "</span>";
                }
                else
                {
                    echo "<span class='label-severity' data-severity='{$bug->severity}' title='" . $severityValue . "'></span>";
                }
                break;
            case 'pri':
                if($bug->pri)
                {
                    echo "<span class='label-pri label-pri-" . $bug->pri . "' title='" . zget($this->lang->bug->priList, $bug->pri, $bug->pri) . "'>";
                    echo zget($this->lang->bug->priList, $bug->pri, $bug->pri);
                    echo "</span>";
                }
                break;
            case 'confirmed':
                $class = 'confirm' . $bug->confirmed;
                echo "<span class='$class' title='" . zget($this->lang->bug->confirmedList, $bug->confirmed, $bug->confirmed) . "'>" . zget($this->lang->bug->confirmedList, $bug->confirmed, $bug->confirmed) . "</span> ";
                break;
            case 'title':
                $showBranch = isset($this->config->bug->browse->showBranch) ? $this->config->bug->browse->showBranch : 1;
                if(isset($branches[$bug->branch]) and $showBranch) echo "<span class='label label-outline label-badge' title={$branches[$bug->branch]}>{$branches[$bug->branch]}</span> ";
                if($bug->module and isset($modulePairs[$bug->module])) echo "<span class='label label-gray label-badge'>{$modulePairs[$bug->module]}</span> ";
                echo $canView ? html::a($bugLink, $bug->title, null, "style='color: $bug->color' data-app={$this->app->tab}") : "<span style='color: $bug->color'>{$bug->title}</span>";
                if($bug->case) echo html::a(helper::createLink('testcase', 'view', "caseID=$bug->case&version=$bug->caseVersion"), "[" . $this->lang->testcase->common  . "#$bug->case]", '', "class='bug' title='$bug->case'");
                break;
            case 'branch':
                echo zget($branches, $bug->branch, '');
                break;
            case 'project':
                echo zget($projectPairs, $bug->project, '');
                break;
            case 'execution':
                echo zget($executions, $bug->execution, '');
                break;
            case 'plan':
                echo zget($plans, $bug->plan, '');
                break;
            case 'story':
                if(isset($stories[$bug->story]))
                {
                    $story = $stories[$bug->story];
                    echo common::hasPriv('story', 'view') ? html::a(helper::createLink('story', 'view', "storyID=$story->id", 'html', true), $story->title, '', "class='iframe'") : $story->title;
                }
                break;
            case 'task':
                if(isset($tasks[$bug->task]))
                {
                    $task = $tasks[$bug->task];
                    echo common::hasPriv('task', 'view') ? html::a(helper::createLink('task', 'view', "taskID=$task->id", 'html', true), $task->name, '', "class='iframe'") : $task->name;
                }
                break;
            case 'toTask':
                if(isset($tasks[$bug->toTask]))
                {
                    $task = $tasks[$bug->toTask];
                    echo common::hasPriv('task', 'view') ? html::a(helper::createLink('task', 'view', "taskID=$task->id", 'html', true), $task->name, '', "class='iframe'") : $task->name;
                }
                break;
            case 'type':
                echo zget($this->lang->bug->typeList, $bug->type);
                break;
            case 'status':
                echo "<span class='status-bug status-{$bug->status}'>";
                echo $this->processStatus('bug', $bug);
                echo  '</span>';
                break;
            case 'activatedCount':
                echo $bug->activatedCount;
                break;
            case 'activatedDate':
                echo helper::isZeroDate($bug->activatedDate) ? '' : substr($bug->activatedDate, 5, 11);
                break;
            case 'keywords':
                echo $bug->keywords;
                break;
            case 'os':
                echo $os;
                break;
            case 'browser':
                echo $browser;
                break;
            case 'mailto':
                $mailto = explode(',', $bug->mailto);
                foreach($mailto as $account)
                {
                    $account = trim($account);
                    if(empty($account)) continue;
                    echo zget($users, $account) . " &nbsp;";
                }
                break;
            case 'found':
                echo zget($users, $bug->found);
                break;
            case 'openedBy':
                echo zget($users, $bug->openedBy);
                break;
            case 'openedDate':
                echo helper::isZeroDate($bug->openedDate) ? '' : substr($bug->openedDate, 5, 11);
                break;
            case 'openedBuild':
                echo $bug->openedBuild;
                break;
            case 'assignedTo':
                $this->printAssignedHtml($bug, $users);
                break;
            case 'assignedDate':
                echo helper::isZeroDate($bug->assignedDate) ? '' : substr($bug->assignedDate, 5, 11);
                break;
            case 'deadline':
                echo helper::isZeroDate($bug->deadline) ? '' : '<span>' . substr($bug->deadline, 5, 11) . '</span>';
                break;
            case 'resolvedBy':
                echo zget($users, $bug->resolvedBy, $bug->resolvedBy);
                break;
            case 'resolution':
                echo zget($this->lang->bug->resolutionList, $bug->resolution);
                break;
            case 'resolvedDate':
                echo helper::isZeroDate($bug->resolvedDate) ? '' : substr($bug->resolvedDate, 5, 11);
                break;
            case 'resolvedBuild':
                echo $bug->resolvedBuild;
                break;
            case 'closedBy':
                echo zget($users, $bug->closedBy);
                break;
            case 'closedDate':
                echo helper::isZeroDate($bug->closedDate) ? '' : substr($bug->closedDate, 5, 11);
                break;
            case 'lastEditedBy':
                echo zget($users, $bug->lastEditedBy);
                break;
            case 'lastEditedDate':
                echo helper::isZeroDate($bug->lastEditedDate) ? '' : substr($bug->lastEditedDate, 5, 11);
                break;
            case 'actions':
                echo $this->buildOperateMenu($bug, 'browse');
                break;
            }
            echo '</td>';
        }
    }

    /**
     * Print assigned html.
     *
     * @param  object $bug
     * @param  array  $users
     * @access public
     * @return void
     */
    public function printAssignedHtml($bug, $users)
    {
        $btnTextClass   = '';
        $btnClass       = '';
        $assignedToText = !empty($bug->assignedTo) ? zget($users, $bug->assignedTo) : $this->lang->bug->noAssigned;
        if(empty($bug->assignedTo)) $btnClass = $btnTextClass = 'assigned-none';
        if($bug->assignedTo == $this->app->user->account) $btnClass = $btnTextClass = 'assigned-current';
        if(!empty($bug->assignedTo) and $bug->assignedTo != $this->app->user->account) $btnClass = $btnTextClass = 'assigned-other';

        $btnClass    .= $bug->assignedTo == 'closed' ? ' disabled' : '';
        $btnClass    .= ' iframe btn btn-icon-left btn-sm';

        $assignToLink = helper::createLink('bug', 'assignTo', "bugID=$bug->id", '', true);
        $assignToHtml = html::a($assignToLink, "<i class='icon icon-hand-right'></i> <span title='" . zget($users, $bug->assignedTo) . "'>{$assignedToText}</span>", '', "class='$btnClass'");

        echo !common::hasPriv('bug', 'assignTo', $bug) ? "<span style='padding-left: 21px' class='{$btnTextClass}'>{$assignedToText}</span>" : $assignToHtml;
    }

    /**
     * Get toList and ccList.
     *
     * @param  object    $bug
     * @access public
     * @return bool|array
     */
    public function getToAndCcList($bug)
    {
        /* Set toList and ccList. */
        $toList = $bug->assignedTo ? $bug->assignedTo : '';
        $ccList = trim((string)$bug->mailto, ',');
        if(empty($toList))
        {
            if(empty($ccList)) return false;
            if(strpos($ccList, ',') === false)
            {
                $toList = $ccList;
                $ccList = '';
            }
            else
            {
                $commaPos = strpos($ccList, ',');
                $toList = substr($ccList, 0, $commaPos);
                $ccList = substr($ccList, $commaPos + 1);
            }
        }
        elseif($bug->status == 'closed')
        {
            $ccList .= ',' . $bug->resolvedBy;
        }

        return array($toList, $ccList);
    }

    /**
     * Summary
     *
     * @param  array    $bugs
     * @access public
     * @return string
     */
    public function summary($bugs)
    {
        $unresolved = 0;
        foreach($bugs as $bug)
        {
            if($bug->status != 'resolved' && $bug->status != 'closed') $unresolved++;
        }

        return sprintf($this->lang->bug->summary, count($bugs), $unresolved);
    }

    /**
     * Get project list.
     *
     * @param  int $productID
     * @access public
     * @return array
     */
    public function getProjects($productID)
    {
        return $this->dao->select('t1.id,t1.name')
            ->from(TABLE_PROJECT)->alias('t1')
            ->leftjoin(TABLE_PROJECTPRODUCT)->alias('t2')->on('t1.id = t2.project')
            ->where('t1.type')->eq('project')
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t2.product')->eq($productID)
            ->fetchPairs();
    }

    /**
     * 构造详情页或列表页需要的操作菜单。
     * Build action menu.
     *
     * @param  object $bug
     * @param  string $type
     * @access public
     * @return string
     */
    public function buildOperateMenu(object $bug = null, string $type = 'view'): array
    {
        $defaultParams = $bug ? "bugID={$bug->id}" : 'bugID={id}';
        $copyParams    = $bug ? "productID={$bug->product}&branch={$bug->branch}&extra=bugID={$bug->id}" : 'productID={product}&branch={branch}&extra=bugID={id}';
        if($this->app->tab == 'project')   $copyParams .= ',projectID={project}';
        if($this->app->tab == 'execution') $copyParams .= ',executionID={execution}';

        $actions = array();
        $actions['confirm']  = array('icon' => 'ok',         'text' => $this->lang->bug->confirmedAB, 'url' => helper::createLink('bug', 'confirm',  $defaultParams), 'data-toggle' => 'modal');
        $actions['assignTo'] = array('icon' => 'hand-right', 'text' => $this->lang->bug->assignTo,    'url' => helper::createLink('bug', 'assignTo', $defaultParams), 'data-toggle' => 'modal');
        $actions['resolve']  = array('icon' => 'checked',    'text' => $this->lang->bug->resolve,     'url' => helper::createLink('bug', 'resolve',  $defaultParams), 'data-toggle' => 'modal');
        $actions['close']    = array('icon' => 'off',        'text' => $this->lang->bug->close,       'url' => helper::createLink('bug', 'close',    $defaultParams), 'data-toggle' => 'modal');
        $actions['activate'] = array('icon' => 'magic',      'text' => $this->lang->bug->activate,    'url' => helper::createLink('bug', 'activate', $defaultParams), 'data-toggle' => 'modal');
        $actions['edit']     = array('icon' => 'edit',       'text' => $this->lang->bug->edit,        'url' => helper::createLink('bug', 'edit',     $defaultParams));
        $actions['copy']     = array('icon' => 'copy',       'text' => $this->lang->bug->copy,        'url' => helper::createLink('bug', 'create',   $copyParams));

        foreach($actions as $action => $actionData)
        {
            $actionsConfig = $this->config->bug->actions->{$type};
            if(strpos(",{$actionsConfig},", ",{$action},") === false)
            {
                unset($actions[$action]);
                continue;
            }
            $actions[$action]['hint'] = $actions[$action]['text'];
            if($type == 'browse') unset($actions[$action]['text']);
        }

        if($type == 'browse') $this->config->bug->dtable->fieldList['actions']['actionsMap'] = $actions;
        return $actions;
    }

    /**
     * Get related objects id lists.
     *
     * @param  int    $object
     * @param  string $pairs
     * @access public
     * @return void
     */
    public function getRelatedObjects($object, $pairs = '')
    {
        /* Get bugs. */
        $bugs = $this->loadModel('transfer')->getQueryDatas('bug');

        /* Get related objects id lists. */
        $relatedObjectIdList = array();
        $relatedObjects      = array();

        foreach($bugs as $bug) $relatedObjectIdList[$bug->$object]  = $bug->$object;

        if($object == 'openedBuild' or $object == 'resolvedBuild') $object = 'build';

        /* Get related objects title or names. */
        $table = $this->config->objectTables[$object];
        if($table) $relatedObjects = $this->dao->select($pairs)->from($table)->where('id')->in($relatedObjectIdList)->fetchPairs();

        if(in_array($object, array('build','resolvedBuild'))) $relatedObjects = array('trunk' => $this->lang->trunk) + $relatedObjects;
        return array('' => '', 0 => '') + $relatedObjects;
    }

    /**
     * Return index of a case's step.
     *
     * @param  object    $caseStep
     * @access public
     * @return int
     */
    public function getCaseStepIndex($caseStep)
    {
        static $index     = 0;
        static $stepIndex = 0;
        static $itemIndex = 0;
        static $groupID   = 0;

        if($caseStep->type == 'item')
        {
            if($groupID and $caseStep->parent == $groupID)
            {
                $itemIndex ++;
                $index = $stepIndex . '.' . $itemIndex;
            }
            else
            {
                $stepIndex ++;
                $index = $stepIndex;
            }
        }
        else
        {
            if($caseStep->type == 'group') $groupID = $caseStep->id;
            $stepIndex ++;
            $itemIndex = 0;
            $index = $stepIndex;
        }

        return $index;
    }

    /**
     * 批量编辑 bug 后的其他处理。
     * Processing after batch edit of bug.
     *
     * @param  object    $bug
     * @param  object    $oldBug
     * @access protected
     * @return bool
     */
    protected function afterBatchEdit(object $bug, object $oldBug): bool
    {
        $this->executeHooks($bug->id);

        /* Record log. */
        $changes  = common::createChanges($oldBug, $bug);
        $actionID = $this->loadModel('action')->create('bug', $bug->id, 'Edited');
        $this->action->logHistory($actionID, $changes);

        /* Record score when bug is resolved. */
        if(isset($bug->status) and $bug->status == 'resolved' and $oldBug->status == 'active') $this->loadModel('score')->create('bug', 'resolve', $bug, $bug->resolvedBy);

        if($this->config->edition != 'pms' && $oldBug->feedback) $this->loadModel('feedback')->updateStatus('bug', $oldBug->feedback, $bug->status, $oldBug->status);

        return !dao::isError();
    }
}
