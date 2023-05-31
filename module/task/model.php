<?php
/**
 * The model file of task module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     task
 * @version     $Id: model.php 5154 2013-07-16 05:51:02Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class taskModel extends model
{
    /**
     * 创建测试类型的任务。
     * Create a test type task.
     *
     * @param  object    $task
     * @param  array     $testTasks
     * @access public
     * @return false|int
     */
    public function createTaskOfTest(object $task, array $testTasks): false|int
    {
        if(!empty($testTasks))
        {
            $this->config->task->create->requiredFields = str_replace(array(',estimate,', ',story,', ',estStarted,', ',deadline,', ',module,'), ',', $this->config->task->create->requiredFields);
        }

        $taskID = $this->create($task);
        if(!$taskID) return false;

        /* If the current task has test subtasks, create test subtasks and update the task information. */
        if(!empty($testTasks))
        {
            $this->createTestChildTasks($taskID, $testTasks);
            $this->computeWorkingHours($taskID);
            $this->computeBeginAndEnd($taskID);
            $this->dao->update(TABLE_TASK)->set('`parent`')->eq(-1)->where('id')->eq($taskID)->exec();
        }

        if(dao::isError()) return false;
        return $taskID;
    }

    /**
     * 创建事务类型的任务。
     * Create a task of affair type.
     *
     * @param  object      $task
     * @param  array       $assignedToList
     * @access public
     * @return false|array
     */
    public function createTaskOfAffair(object $task, array $assignedToList): false|array
    {
        $taskIdList = array();
        foreach($assignedToList as $assignedTo)
        {
            /* If the type of task is affair and assignedTo is empty, skip it. */
            if(count($assignedToList) > 1 && empty($assignedTo)) continue;

            $task->assignedTo = $assignedTo;
            $taskID = $this->create($task);
            if(!$taskID) return false;

            $taskIdList[] = $taskID;
        }

        if(dao::isError()) return false;

        return $taskIdList;
    }

    /**
     * 创建多人任务。
     * Create a multiplayer task.
     *
     * @param  object    $task
     * @param  object    $teamData
     * @access public
     * @return false|int
     */
    public function createMultiTask(object $task, object $teamData): false|int
    {
        $task->assignedTo = '';
        $taskID = $this->create($task);
        if(!$taskID) return false;

        if(count(array_filter($teamData->team)) < 2) return $taskID;

        /* Manage the team of task and calculate the team hours. */
        $task->id = $taskID;
        $teams    = $this->manageTaskTeam($task->mode, $task, $teamData);
        if($teams) $this->computeMultipleHours($task);

        return $taskID;
    }

    /**
     * 创建一个任务。
     * Create a task.
     *
     * @param  object    $task
     * @access public
     * @return false|int
     */
    public function create(object $task): false|int
    {
        /* If the lifetime if the execution is ops and the attribute of execution is request or review, remove story from required fields. */
        $execution      = $this->dao->findByID($task->execution)->from(TABLE_PROJECT)->fetch();
        $requiredFields = ',' . $this->config->task->create->requiredFields . ',';
        if($execution && $this->isNoStoryExecution($execution)) $requiredFields = str_replace(',story,', ',', $requiredFields);

        /* Insert task data. */
        if(empty($task->assignedTo)) unset($task->assignedDate);
        $this->dao->insert(TABLE_TASK)->data($task)
            ->checkIF($task->estimate != '', 'estimate', 'float')
            ->autoCheck()
            ->batchCheck($requiredFields, 'notempty')
            ->checkFlow()
            ->exec();

        if(dao::isError()) return false;

        /* Get task id. */
        $taskID = $this->dao->lastInsertID();

        /* Insert task desc data. */
        $taskSpec = new stdclass();
        $taskSpec->task       = $taskID;
        $taskSpec->version    = $task->version;
        $taskSpec->name       = $task->name;
        if(!empty($task->estStarted)) $taskSpec->estStarted = $task->estStarted;
        if(!empty($task->deadline)) $taskSpec->deadline = $task->deadline;
        $this->dao->insert(TABLE_TASKSPEC)->data($taskSpec)->autoCheck()->exec();

        if(dao::isError()) return false;

        $this->loadModel('action')->create('task', $taskID, 'Opened', '');
        $this->loadModel('file')->updateObjectID($this->post->uid, $taskID, 'task');
        $this->loadModel('score')->create('task', 'create', $taskID);
        if(dao::isError()) return false;

        return $taskID;
    }

    /**
     * 批量创建任务。
     * Batch create tasks.
     *
     * @param  array       $tasks
     * @param  array       $output
     * @access public
     * @return array|false
     */
    public function batchCreate(array $tasks, array $output): array|false
    {
        $this->loadModel('story');

        $executionID = !empty($tasks) ? current($tasks)->execution : 0;
        $taskIdList  = array();
        foreach($tasks as $task)
        {
            /* Get the lane and column of the current task. */
            $laneID   = $task->lane;
            $columnID = isset($output['columnID']) ? $output['columnID'] : 0;
            unset($task->lane);

            /* Create a task. */
            $taskID = $this->create($task);
            if(!$taskID) return false;

            /* Update Kanban and story stage. */
            if($task->story) $this->story->setStage($task->story);
            $this->updateKanbanForBatchCreate($taskID, $executionID, $laneID, $columnID);

            $taskIdList[$taskID] = $taskID;
        }
        return $taskIdList;
    }

    /**
     * 批量创建任务后的其他数据处理。
     * other data process after task batch create.
     *
     * @param  array  $taskIdList
     * @param  int    $parentID
     * @access public
     * @return bool
     */
    public function afterBatchCreate(array $taskIdList, int $parentID = 0): bool
    {
        $this->loadModel('action');
        $this->loadModel('score');
        foreach($taskIdList as $taskID)
        {
            $this->executeHooks($taskID);
            $this->action->create('task', $taskID, 'Opened', '');
            $this->score->create('task', 'create', $taskID);
        }

        /* Process other data after split task. */
        if($parentID && empty($taskIdList))
        {
            $childrenIdList = !empty($parentID) ? $taskIdList : array();
            $parentTask     = $this->getByID($parentID);
            $this->afterSplitTask($parentTask, $childrenIdList);
        }

        return !dao::isError();
    }

    /**
     * 批量编辑任务后的其他数据处理。
     * other data process after task batch edit.
     *
     * @param  object[] $tasks
     * @access public
     * @return bool
     */
    public function afterBatchUpdate(array $tasks): bool
    {
        $this->loadModel('story');
        $this->loadModel('kanban');
        $this->loadModel('feedback');

        $today          = helper::today();
        $currentAccount = $this->app->user->account;
        $oldTasks       = $tasks ? $this->getByIdList(array_keys($tasks)) : array();
        foreach($tasks as $taskID => $task)
        {
            $oldTask = zget($oldTasks, $taskID);

            /* Record effort. */
            $record = new stdclass();
            $record->account  = $currentAccount;
            $record->task     = $taskID;
            $record->date     = $today;
            $record->left     = $task->left;
            $record->consumed = $task->consumed;
            $this->addTaskEffort($record);

            /* Update Kanban and story stage. */
            if($oldTask->story) $this->story->setStage($oldTask->story);
            if($task->status != $oldTask->status) $this->kanban->updateLane($oldTask->execution, 'task', $taskID);

            /* Udate parent task's status, date and hour. */
            if($oldTask->parent > 0)
            {
                $this->updateParentStatus($taskID);
                $this->computeBeginAndEnd($oldTask->parent);
            }

            if($this->config->edition != 'open' && $oldTask->feedback && !isset($feedbacks[$oldTask->feedback]))
            {
                $feedbacks[$oldTask->feedback] = $oldTask->feedback;
                $this->feedback->updateStatus('task', $oldTask->feedback, $task->status, $oldTask->status);
            }
        }

        return !dao::isError();
    }

    /**
     * Create task from gitlab issue.
     *
     * @param  object    $task
     * @param  int       $executionID
     * @access public
     * @return int
     */
    public function createTaskFromGitlabIssue($task, $executionID)
    {
        $task->version      = 1;
        $task->openedBy     = $this->app->user->account;
        $task->lastEditedBy = $this->app->user->account;
        $task->assignedDate = isset($task->assignedTo) ? helper::now() : 0;
        $task->story        = 0;
        $task->module       = 0;
        $task->estimate     = 0;
        $task->estStarted   = '0000-00-00';
        $task->left         = 0;
        $task->pri          = 3;
        $task->type         = 'devel';
        $task->project      = $this->dao->select('project')->from(TABLE_PROJECT)->where('id')->eq($executionID)->fetch('project');

        $this->dao->insert(TABLE_TASK)->data($task, 'id,product')
             ->autoCheck()
             ->batchCheck($this->config->task->create->requiredFields, 'notempty')
             ->checkIF(!helper::isZeroDate($task->deadline), 'deadline', 'ge', $task->estStarted)
             ->exec();

        if(dao::isError()) return false;

        return $this->dao->lastInsertID();
    }

    /**
     * 计算多人任务工时。
     * Compute hours for multiple task.
     *
     * @param  object      $oldTask
     * @param  object      $task
     * @param  array       $team
     * @param  bool        $autoStatus
     * @access public
     * @return object|bool
     */
    public function computeMultipleHours(object $oldTask, object $task = null, array $team = array(), bool $autoStatus = true): object|bool
    {
        if(!$oldTask) return false;

        if(empty($team)) $team = $this->dao->select('*')->from(TABLE_TASKTEAM)->where('task')->eq($oldTask->id)->orderBy('order')->fetchAll(); // If the team is empty, get the team from the task team table.

        /* If the team is not empty, compute the team hours. */
        if(!empty($team))
        {
            /* Get members, old team and current task. */
            $members     = array_column($team, 'account');
            $oldTeam     = zget($oldTask, 'team', array());
            $currentTask = !empty($task) ? $task : new stdclass();
            if(!isset($currentTask->status)) $currentTask->status = $oldTask->status;
            $oldTask->team = $team;

            /* If the assignedTo is not empty, the current task assignedTo is assignedTo. */
            if(!empty($_POST['assignedTo']) && is_string($_POST['assignedTo']))
            {
                $currentTask->assignedTo = $this->post->assignedTo;
            }
            /* If assignedTo is empty, get the assignedTo for the multiply linear task. */
            else
            {
                $currentTask->assignedTo = $this->getAssignedTo4Multi($members, $oldTask);
                if($oldTask->assignedTo != $currentTask->assignedTo) $currentTask->assignedDate = helper::now();
                $oldTask->team = $oldTeam;
            }

            /* Compute estimate and left. */
            $currentTask->estimate = 0;
            $currentTask->left     = 0;
            foreach($team as $member)
            {
                $currentTask->estimate += (float)$member->estimate;
                $currentTask->left     += (float)$member->left;
            }

            /* Get task efforts, and compute consumed. */
            $efforts = $this->getTaskEfforts($oldTask->id);
            $currentTask->consumed = 0;
            foreach($efforts as $effort) $currentTask->consumed += (float)$effort->consumed;

            /* If task is not empty, the task status is computed and the task is returned. */
            if(!empty($task)) return $this->taskTao->computeTaskStatus($currentTask, $oldTask, $task, $autoStatus, empty($efforts), $members);

            /* If task is empty, update the current task. */
            $this->dao->update(TABLE_TASK)->data($currentTask)->autoCheck()->where('id')->eq($oldTask->id)->exec();
        }
        return !dao::isError();
    }

    /**
     * 根据父任务ID计算父任务的预计、消耗和剩余工时。
     * Compute parent task working hours.
     *
     * @param  int|bool $taskID
     * @access public
     * @return bool
     */
    public function computeWorkingHours(int|bool $taskID): bool
    {
        if(!$taskID) return true;

        /* Get sub-tasks. */
        $tasks = $this->dao->select('`id`,`estimate`,`consumed`,`left`, status')->from(TABLE_TASK)->where('parent')->eq($taskID)->andWhere('status')->ne('cancel')->andWhere('deleted')->eq(0)->fetchAll('id');
        /* If task doesn't have sub-tasks, clear out the consumed hours. */
        if(empty($tasks))
        {
            $this->dao->update(TABLE_TASK)->set('consumed')->eq(0)->where('id')->eq($taskID)->exec();
            return !dao::isError();
        }

        /* Compute task estimate, consumed and left through sub-tasks. */
        $estimate = 0;
        $consumed = 0;
        $left     = 0;
        foreach($tasks as $task)
        {
            $estimate += $task->estimate;
            $consumed += $task->consumed;
            if($task->status != 'closed') $left += $task->left;
        }

        /* Initialize task data to update. */
        $newTask = new stdclass();
        $newTask->estimate       = $estimate;
        $newTask->consumed       = $consumed;
        $newTask->left           = $left;
        $newTask->lastEditedBy   = $this->app->user->account;
        $newTask->lastEditedDate = helper::now();

        /* Update task data. */
        $this->dao->update(TABLE_TASK)->data($newTask)->autoCheck()->where('id')->eq($taskID)->exec();
        return !dao::isError();
    }

    /**
     * 根据父任务ID计算父任务的预计开始 实际开始 截止日期。
     * Compute begin and end for parent task.
     *
     * @param  int    $taskID
     * @access public
     * @return bool
     */
    public function computeBeginAndEnd(int $taskID): bool
    {
        /* Get estStarted realStarted and deadline of the sub-tasks. */
        $tasks = $this->dao->select('estStarted, realStarted, deadline')->from(TABLE_TASK)->where('parent')->eq($taskID)->andWhere('status')->ne('cancel')->andWhere('deleted')->eq(0)->fetchAll();
        if(empty($tasks)) return !dao::isError();

        /* Compute the earliest estStarted, the earliest realStarted and the latest deadline. */
        $earliestEstStarted  = '';
        $earliestRealStarted = '';
        $latestDeadline      = '';
        foreach($tasks as $task)
        {
            if(!helper::isZeroDate($task->estStarted)  && (empty($earliestEstStarted)  || $earliestEstStarted  > $task->estStarted))  $earliestEstStarted  = $task->estStarted;
            if(!helper::isZeroDate($task->realStarted) && (empty($earliestRealStarted) || $earliestRealStarted > $task->realStarted)) $earliestRealStarted = $task->realStarted;
            if(!helper::isZeroDate($task->deadline)    && (empty($latestDeadline)      || $latestDeadline      < $task->deadline))    $latestDeadline      = $task->deadline;
        }

        /* Initialize task data and update it. */
        $newTask = array();
        if(!empty($earliestEstStarted))  $newTask['estStarted']  = $earliestEstStarted;
        if(!empty($earliestRealStarted)) $newTask['realStarted'] = $earliestRealStarted;
        if(!empty($latestDeadline))      $newTask['deadline']    = $latestDeadline;
        if(!empty($newTask)) $this->dao->update(TABLE_TASK)->data($newTask)->autoCheck()->where('id')->eq($taskID)->exec();

        return !dao::isError();
    }

    /**
     * 更新父任务的状态.
     * Update parent status by taskID.
     *
     * @param int $taskID
     *
     * @access public
     * @return bool
     */
    public function updateParentStatus($taskID, $parentID = 0, $createAction = true)
    {
        $childTask = $this->dao->select('id,assignedTo,parent')->from(TABLE_TASK)->where('id')->eq($taskID)->fetch();
        if(empty($parentID)) $parentID = $childTask->parent;
        if($parentID <= 0) return true;

        $oldParentTask = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($parentID)->fetch();
        if($oldParentTask->parent != '-1') $this->dao->update(TABLE_TASK)->set('parent')->eq('-1')->where('id')->eq($parentID)->exec();
        $this->computeWorkingHours($parentID);

        $childrenStatus       = $this->dao->select('id,status')->from(TABLE_TASK)->where('parent')->eq($parentID)->andWhere('deleted')->eq('0')->fetchPairs('status', 'status');
        $childrenClosedReason = $this->dao->select('closedReason')->from(TABLE_TASK)->where('parent')->eq($parentID)->andWhere('deleted')->eq('0')->fetchPairs('closedReason');
        if(empty($childrenStatus)) return $this->dao->update(TABLE_TASK)->set('parent')->eq('0')->where('id')->eq($parentID)->exec();

        $status = '';
        if(count($childrenStatus) == 1)
        {
            $status = current($childrenStatus);
        }
        else
        {
            if(isset($childrenStatus['doing']) or isset($childrenStatus['pause']))
            {
                $status = 'doing';
            }
            elseif((isset($childrenStatus['done']) or isset($childrenClosedReason['done'])) && isset($childrenStatus['wait']))
            {
                $status = 'doing';
            }
            elseif(isset($childrenStatus['wait']))
            {
                $status = 'wait';
            }
            elseif(isset($childrenStatus['done']))
            {
                $status = 'done';
            }
            elseif(isset($childrenStatus['closed']))
            {
                $status = 'closed';
            }
            elseif(isset($childrenStatus['cancel']))
            {
                $status = 'cancel';
            }
        }

        $parentTask = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($parentID)->andWhere('deleted')->eq(0)->fetch();
        if(empty($parentTask)) return $this->dao->update(TABLE_TASK)->set('parent')->eq('0')->where('id')->eq($taskID)->exec();

        if($status and $parentTask->status != $status)
        {
            $now  = helper::now();
            $task = new stdclass();
            $task->status = $status;
            if($status == 'done')
            {
                $task->assignedTo   = $parentTask->openedBy;
                $task->assignedDate = $now;
                $task->finishedBy   = $this->app->user->account;
                $task->finishedDate = $now;
            }

            if($status == 'cancel')
            {
                $task->assignedTo   = $parentTask->openedBy;
                $task->assignedDate = $now;
                $task->finishedBy   = '';
                $task->finishedDate = null;
                $task->canceledBy   = $this->app->user->account;
                $task->canceledDate = $now;
            }

            if($status == 'closed')
            {
                $task->assignedTo   = 'closed';
                $task->assignedDate = $now;
                $task->closedBy     = $this->app->user->account;
                $task->closedDate   = $now;
                $task->closedReason = 'done';
            }

            if($status == 'doing' or $status == 'wait')
            {
                if($parentTask->assignedTo == 'closed')
                {
                    $task->assignedTo   = $childTask->assignedTo;
                    $task->assignedDate = $now;
                }
                $task->finishedBy   = '';
                $task->finishedDate = null;
                $task->closedBy     = '';
                $task->closedDate   = null;
                $task->closedReason = '';
            }

            $task->lastEditedBy   = $this->app->user->account;
            $task->lastEditedDate = $now;
            $task->parent         = '-1';
            $this->dao->update(TABLE_TASK)->data($task)->where('id')->eq($parentID)->exec();
            if(!dao::isError())
            {
                if(!$createAction) return $task;

                if($parentTask->story) $this->loadModel('story')->setStage($parentTask->story);
                $newParentTask = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($parentID)->fetch();

                unset($oldParentTask->subStatus);
                unset($newParentTask->subStatus);
                $changes = common::createChanges($oldParentTask, $newParentTask);
                $action  = '';
                if($status == 'done' and $parentTask->status != 'done')     $action = 'Finished';
                if($status == 'closed' and $parentTask->status != 'closed') $action = 'Closed';
                if($status == 'pause' and $parentTask->status != 'paused')  $action = 'Paused';
                if($status == 'cancel' and $parentTask->status != 'cancel') $action = 'Canceled';
                if($status == 'doing' and $parentTask->status == 'wait')    $action = 'Started';
                if($status == 'doing' and $parentTask->status == 'pause')   $action = 'Restarted';
                if($status == 'doing' and $parentTask->status != 'wait' and $parentTask->status != 'pause') $action = 'Activated';
                if($status == 'wait' and $parentTask->status != 'wait')     $action = 'Adjusttasktowait';
                if($action)
                {
                    $actionID = $this->loadModel('action')->create('task', $parentID, $action, '', '', '', false);
                    $this->action->logHistory($actionID, $changes);
                }

                if(($this->config->edition == 'biz' || $this->config->edition == 'max') && $oldParentTask->feedback) $this->loadModel('feedback')->updateStatus('task', $oldParentTask->feedback, $newParentTask->status, $oldParentTask->status);
            }
        }
        else
        {
            if(!dao::isError())
            {
                $newParentTask = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($parentID)->fetch();
                $changes = common::createChanges($oldParentTask, $newParentTask);
                if($changes)
                {
                    $actionID = $this->loadModel('action')->create('task', $parentID, 'Edited', '', '', '', false);
                    $this->action->logHistory($actionID, $changes);
                }
            }
        }
    }

    /**
     * 维护多人任务的团队信息。
     * Manage multi task team.
     *
     * @param  string $mode
     * @param  object $task
     * @param  object $teamData
     * @access public
     * @return array|false
     */
    public function manageTaskTeam(string $mode, object $task, object $teamData): array|false
    {
        /* Get old team member, and delete old task team. */
        $oldTeams   = $this->dao->select('*')->from(TABLE_TASKTEAM)->where('task')->eq($task->id)->fetchAll();
        $oldMembers = array_column($oldTeams, 'account');
        $this->dao->delete()->from(TABLE_TASKTEAM)->where('task')->eq($task->id)->exec();

        /* Set effort left = 0 when linear task members be changed. */
        $changeUsers = array();
        $teamData->team = array_filter($teamData->team);
        foreach($teamData->team as $index => $account)
        {
            if($mode == 'linear' && isset($oldTeams[$index]) && $oldTeams[$index]->account != $account) $changeUsers[] = $oldTeams[$index]->account;
        }

        /* Manage task team member. */
        $teams = $this->manageTaskTeamMember($mode, $task, $teamData);
        if(dao::isError()) return false;

        /* Set effort left = 0 when multi task members be removed. */
        if($mode == 'multi' && $oldMembers)
        {
            $removedMembers = array_diff($oldMembers, $teams);
            $changeUsers    = array_merge($changeUsers, $removedMembers);
        }
        if($changeUsers) $this->resetEffortLeft($task->id, $changeUsers);

        return $teams;
    }

    /**
     * 维护多人任务团队成员信息。
     * Manage multi-task team member information.
     *
     * @param  string      $mode
     * @param  object      $task
     * @param  object      $teamData
     * @access public
     * @return false|array
     */
    public function manageTaskTeamMember(string $mode, object $task, object $teamData): false|array
    {
        /* If status of the task is doing, get the person who did not complete the task. */
        $undoneUsers = array();
        if($task->status == 'doing')
        {
            $efforts = $this->getTaskEfforts($task->id);
            foreach($efforts as $effort)
            {
                if($effort->left != 0) $undoneUsers[$effort->account] = $effort->account;
                else unset($undoneUsers[$effort->account]);
            }
        }

        $minStatus = 'done';
        $teamList  = array_filter($teamData->team);
        $teams     = array();
        foreach($teamList as $index => $account)
        {
            /* Set member information. */
            $member = new stdclass();
            $member->task     = $task->id;
            $member->order    = $index;
            $member->account  = $account;
            $member->estimate = zget($teamData->teamEstimateList, $index, 0);
            $member->consumed = isset($teamData->teamConsumedList) ? zget($teamData->teamConsumedList, $index, 0) : 0;
            $member->left     = isset($teamData->teamLeftList) ? zget($teamData->teamLeftList, $index, 0) : 0;
            $member->status   = 'wait';
            if($task->status == 'wait' && $member->estimate > 0 && $member->left == 0) $member->left = $member->estimate;
            if($task->status == 'done') $member->left = 0;

            /* Compute task status of member. */
            if($member->left == 0 && $member->consumed > 0)
            {
                $member->status = 'done';
            }
            elseif($task->status == 'doing')
            {
                $teamSource = zget($teamData->teamSourceList, $index);
                if(!empty($teamSource) && $teamSource != $account && isset($undoneUsers[$teamSource])) $member->transfer = $teamSource;
                if(isset($undoneUsers[$account]) && ($mode == 'multi' || ($mode == 'linear' && $minStatus != 'wait'))) $member->status = 'doing';
            }

            /* Compute multi-task status, and in a linear task, there is only one doing status. */
            if(($mode == 'linear' && $member->status == 'doing') || $member->status == 'wait') $minStatus = 'wait';
            if($minStatus != 'wait' && $member->status == 'doing') $minStatus = 'doing';

            /* Insert or update team member. */
            $this->taskTao->setTeamMember($member, $mode, isset($teams[$account]));
            if(dao::isError()) return false;
            $teams[$account] = $account;
        }
        return $teams;
    }

    /**
     * 获取用户所在多人任务的团队工序。
     * Get the team process of the multi-task by account.
     *
     * @param  array  $teams
     * @param  string $account
     * @param  array  $extra
     * @access public
     * @return object
     */
    public function getTeamByAccount(array $teams, string $account = '', array $extra = array('filter' => 'done')): object|bool
    {
        if(empty($account)) $account = $this->app->user->account;

        $filterStatus = zget($extra, 'filter', '');
        $effortID     = zget($extra, 'effortID', '');

        $repeatUsers = array();
        $taskID      = 0;
        foreach($teams as $team)
        {
            if(isset($extra['order']) and $team->order == $extra['order'] and $team->account == $account) return $team;

            if(empty($taskID) and $effortID) $taskID = $team->task;

            if(isset($repeatUsers[$team->account]))
            {
                $repeatUsers[$team->account] = 1;
            }
            else
            {
                $repeatUsers[$team->account] = 0;
            }
        }

        /*
         * 1. No repeat account or account is not repeat account;
         * 2. Not by effort;
         * Then direct get team by account.
         */
        if(empty($repeatUsers[$account]))
        {
            foreach($teams as $team)
            {
                if($team->account == $account) return $team;
            }
        }
        elseif(empty($effortID))
        {
            foreach($teams as $team)
            {
                if($filterStatus and $team->status == $filterStatus) continue;
                if($team->account == $account) return $team;
            }
        }
        elseif($effortID)
        {
            $efforts  = $this->getTaskEfforts($taskID, '', $effortID);
            $prevTeam = $currentTeam = null;
            foreach($efforts as $effort)
            {
                $currentTeam = reset($teams);
                if($effort->id == $effortID)
                {
                    if($effort->account == $currentTeam->account) return $currentTeam;
                    if($effort->account == $prevTeam->account)    return $prevTeam;
                    return false;
                }

                if($effort->left == 0 and $currentTeam->account == $effort->account) $prevTeam = array_shift($teams);
            }
        }

        return false;
    }

    /**
     * Update a task.
     *
     * @param  object             $task
     * @access public
     * @return array|string|false
     */
    public function update(object $task): array|string|false
    {
        $taskID  = $task->id;
        $oldTask = $this->getByID($taskID);

        if($task->consumed < $oldTask->consumed)
        {
            dao::$errors[] = $this->lang->task->error->consumedSmall;
            return false;
        }

        /* Compute hours and manage team for multi-task. */
        if($this->post->team and count(array_filter($this->post->team)) > 1)
        {
            $teams = $this->manageTaskTeam($oldTask->mode, $taskID, $task->status);
            if(!empty($teams)) $task = $this->computeMultipleHours($oldTask, $task, array(), false);
        }

        $requiredFields = $this->taskTao->getRequiredFields4Edit($task);
        $this->taskTao->doUpdate($task, $oldTask, $requiredFields);

        if(dao::isError()) return false;

        $this->afterUpdate($oldTask, $task);

        unset($oldTask->parent, $task->parent);

        /* Logging history when multi-task team members have changed. */
        if(isset($oldTask->team)) list($oldTask, $task) = $this->taskTao->createChangesForTeam($oldTask, $task);

        $this->loadModel('file')->processFile4Object('task', $oldTask, $task);
        $changes = common::createChanges($oldTask, $task);

        /* Record log. */
        if($this->post->comment != '' or !empty($changes))
        {
            $action   = !empty($changes) ? 'Edited' : 'Commented';
            $actionID = $this->loadModel('action')->create('task', $taskID, $action, $this->post->comment);
            if(!empty($changes)) $this->action->logHistory($actionID, $changes);
        }

        return $changes;
    }

    /**
     * 编辑任务时更新父任务的信息。
     * Update parent of a task.
     *
     * @param  object $task
     * @param  bool   $isParentChanged
     * @access public
     * @return void
     */
    public function updateParent(object $task, bool $isParentChanged): void
    {
        $parentTask = $this->taskTao->fetchByID($task->parent);

        $this->dao->update(TABLE_TASK)->set('parent')->eq(-1)->where('id')->eq($task->parent)->exec();

        $this->updateParentStatus($task->id, $task->parent, !$isParentChanged);
        $this->computeBeginAndEnd($task->parent);

        if($isParentChanged)
        {
            $this->loadModel('action')->create('task', $task->id, 'linkParentTask', '', $task->parent, '', false);
            $actionID = $this->action->create('task', $task->parent, 'linkChildTask', '', $task->id, '', false);

            $newParentTask = $this->taskTao->fetchByID($task->parent);
            $changes = common::createChanges($parentTask, $newParentTask);
            if(!empty($changes)) $this->action->logHistory($actionID, $changes);
        }
    }

    /**
     * 批量更新任务。
     * Batch update tasks.
     *
     * @param  array $taskData
     * @access public
     * @return array[]|false
     */
    public function batchUpdate(array $taskData): array|false
    {
        $this->loadModel('action');
        $this->loadModel('score');

        $allChanges = array();
        $oldTasks   = $taskData ? $this->getByIdList(array_keys($taskData)) : array();
        foreach($taskData as $taskID => $task)
        {
            /* Update a task.*/
            $oldTask = zget($oldTasks, $taskID);
            $this->dao->update(TABLE_TASK)->data($task)
                ->autoCheck()
                ->batchCheck($this->config->task->edit->requiredFields, 'notempty')
                ->checkFlow()
                ->where('id')->eq($taskID)
                ->exec();

            if(dao::isError())
            {
                foreach(dao::getError() as $field => $error) dao::$errors["{$field}[{$taskID}]"] = $error;
                return false;
            }

            /* Create the task description of the current version in the database. */
            if($task->version > $oldTask->version)
            {
                $taskSpec = new stdclass();
                $taskSpec->task       = $taskID;
                $taskSpec->version    = $task->version;
                $taskSpec->name       = $task->name;
                $taskSpec->estStarted = $task->estStarted;
                $taskSpec->deadline   = $task->deadline;

                $this->dao->insert(TABLE_TASKSPEC)->data($taskSpec)->autoCheck()->exec();
            }

            if($task->status == 'done')   $this->score->create('task', 'finish', $taskID);
            if($task->status == 'closed') $this->score->create('task', 'close', $taskID);
            $actionID = $this->action->create('task', $taskID, 'Edited');
            $changes  = common::createChanges($oldTask, $task);
            if(!empty($changes)) $this->action->logHistory($actionID, $changes);

            $allChanges[$taskID] = $changes;
        }
        if(!dao::isError()) $this->score->create('ajax', 'batchEdit');
        return $allChanges;
    }

    /**
     * 批量更改任务所属模块。
     * Batch change the module of task.
     *
     * @param  array  $taskIdList
     * @param  int    $moduleID
     * @access public
     * @return void
     */
    public function batchChangeModule(array $taskIdList, int $moduleID): void
    {
        $now      = helper::now();
        $oldTasks = $this->getByIdList($taskIdList);

        $this->loadModel('action');
        foreach($taskIdList as $taskID)
        {
            $oldTask = zget($oldTasks, $taskID, null);
            if(!$oldTask || $moduleID == $oldTask->module) continue;

            $task = new stdclass();
            $task->lastEditedBy   = $this->app->user->account;
            $task->lastEditedDate = $now;
            $task->module         = $moduleID;

            $this->dao->update(TABLE_TASK)->data($task)->autoCheck()->where('id')->eq((int)$taskID)->exec();
            if(!dao::isError())
            {
                $changes  = common::createChanges($oldTask, $task);
                $actionID = $this->action->create('task', $taskID, 'Edited');
                $this->action->logHistory($actionID, $changes);
            }
        }
    }

    /**
     * 任务指派用户。
     * Assign a task to a user again.
     *
     * @param  object $task
     * @param  int    $taskID
     * @access public
     * @return array|false
     */
    public function assign(object $task): array|false
    {
        $oldTask = $this->getById($task->id);

        /* Check task left. */
        if(!in_array($oldTask->status, array('done', 'closed')) && isset($task->left) && $task->left == 0)
        {
            dao::$errors['left'] = sprintf($this->lang->error->notempty, $this->lang->task->left);
            return false;
        }

        /* Update parent task status. */
        if($oldTask->parent > 0) $this->updateParentStatus($task->id);

        $this->dao->update(TABLE_TASK)
            ->data($task)
            ->autoCheck()
            ->checkFlow()
            ->where('id')->eq($task->id)
            ->exec();

        if(dao::isError()) return false;

        $changes = common::createChanges($oldTask, $task);

        /* Record log. */
        $actionID = $this->loadModel('action')->create('task', $task->id, 'Assigned', $this->post->comment, $task->assignedTo);
        $this->action->logHistory($actionID, $changes);

        return $changes;
    }

    /**
     * 更新团队信息。
     * Update team.
     *
     * @param  object $task
     * @param  array  $team
     * @param  array  $teamSource
     * @param  array  $teamEstimate
     * @param  array  $teamConsumed
     * @param  array  $teamLeft
     * @access public
     * @return array|false
     */
    public function updateTeam(object $task, array $team, array $teamSource, array $teamEstimate, array $teamConsumed, array $teamLeft): array|false
    {
        $taskID  = $task->id;
        $oldTask = $this->getById($taskID);

        /* Check team data. */
        $team = array_filter($team);
        foreach($team as $i => $account)
        {
            if($teamConsumed[$i] == 0 and $teamLeft[$i] == 0)
            {
                dao::$errors[] = $this->lang->task->noticeTaskStart;
                return false;
            }
        }
        if(count($team) <= 1)
        {
            dao::$errors[] = $this->lang->task->error->teamMember;
            return false;
        }

        /* Manage the team and calculate task work information. */
        $teams = $this->manageTaskTeam($oldTask->mode, $task, $team, $teamSource, $teamEstimate, $teamConsumed, $teamLeft);
        !empty($teams) ? $task = $this->computeMultipleHours($oldTask, $task) : $task->mode = '';

        /* Update parent task status. */
        if($oldTask->parent > 0) $this->updateParentStatus($taskID);

        $this->dao->update(TABLE_TASK)
            ->data($task)
            ->autoCheck()
            ->checkFlow()
            ->where('id')->eq($taskID)
            ->exec();

        if(!dao::isError()) return common::createChanges($oldTask, $task);
    }

    /**
     * 开始一个任务。
     * Start a task.
     *
     * @param  object      $oldTask
     * @param  object      $task
     * @access public
     * @return false|array
     */
    public function start(object $oldTask, object $task): false|array
    {
        /* Process data for multiple tasks. */
        $currentTeam = !empty($oldTask->team) ? $this->getTeamByAccount($oldTask->team) : array();
        if($currentTeam)
        {
            /* Update task team. */
            $team = new stdclass();
            $team->consumed = $task->consumed;
            $team->left     = $task->left;
            $team->status   = empty($team->left) ? 'done' : 'doing';
            $this->dao->update(TABLE_TASKTEAM)->data($team)->where('id')->eq($currentTeam->id)->exec();

            /* Compute hours for multiple task. */
            $task = $this->computeMultipleHours($oldTask, $task);

            /* Set the assigner for the task. */
            $now = helper::now();
            if($team->status == 'done')
            {
                $task->assignedTo   = $this->getAssignedTo4Multi($oldTask->team, $oldTask, 'next');
                $task->assignedDate = $now;
            }

            /* Set the task finisher. */
            $finishedUsers = $this->getFinishedUsers($oldTask->id, array_keys($oldTask->members));
            if(count($finishedUsers) == count($oldTask->team))
            {
                $task->status       = 'done';
                $task->finishedBy   = $this->app->user->account;
                $task->finishedDate = $now;
            }
        }

        $this->dao->update(TABLE_TASK)->data($task)->autoCheck()
            ->checkFlow()
            ->where('id')->eq($oldTask->id)
            ->exec();

        if(dao::isError()) return false;
        return common::createChanges($oldTask, $task);
    }

    /**
     * 开始任务后的其他数据处理。
     * Other data process after task start.
     *
     * @param  object     $oldTask
     * @param  array      $changes
     * @param  int        $left
     * @param  array      $output
     * @access public
     * @return array|bool
     */
    public function afterStart(object $oldTask, array $changes, int $left, array $output): array|bool
    {
        /* Update the data of the parent task. */
        if($oldTask->parent > 0) $this->computeBeginAndEnd($oldTask->parent);

        /* Create related dynamic and record. */
        $action = $left == 0 ? 'Finished' : 'Started';
        $this->afterChangeStatus($oldTask, $changes, $action, $output);

        /* Send Webhook notifications and Synchornize status to execution, project and program. */
        $this->executeHooks($oldTask->id);
        $this->loadModel('common')->syncPPEStatus($oldTask->id);

        /* Remind whether to update status of the bug, if task which from that bug has been finished. */
        if($changes && $this->needUpdateBugStatus($oldTask))
        {
            $response = $this->taskTao->getRemindBugLink($task, $changes);
            if($response) return $response;
        }

        return true;
    }

    /**
     * Record workhour and left of task.
     *
     * @param  int    $taskID
     * @param  array  $workhour
     * @access public
     * @return array|false
     */
    public function recordWorkhour(int $taskID, array $workhour): array|false
    {
        $task       = $this->taskTao->fetchByID($taskID);
        $task->team = $this->dao->select('*')->from(TABLE_TASKTEAM)->where('task')->eq($taskID)->orderBy('order')->fetchAll('id');

        /* Check if field is valid. */
        $result = $this->taskTao->checkWorkhour($task, $workhour);
        if(!$result) return false;

        /* Add field to workhour. */
        $workhour = $this->taskTao->buildWorkhour($taskID, $workhour);
        if(empty($workhour)) return false;

        $allChanges  = array();
        $now         = helper::now();
        $oldStatus   = $task->status;
        $lastDate    = $this->dao->select('*')->from(TABLE_EFFORT)->where('objectID')->eq($taskID)->andWhere('objectType')->eq('task')->orderBy('date_desc,id_desc')->limit(1)->fetch('date');
        $currentTeam = !empty($task->team) ? $this->getTeamByAccount($task->team) : array();

        foreach($workhour as $record)
        {
            $this->addTaskEffort($record);
            $effortID = $this->dao->lastInsertID();

            $isFinishTask = (empty($currentTeam) && !in_array($task->status, $this->config->task->unfinishedStatus)) || (!empty($currentTeam) && $currentTeam->status != 'done');
            /* Change the workhour and status of tasks through effort. */
            list($newTask, $actionID) = $this->taskTao->buildTaskForEffort($record, $task, (string)$lastDate, $isFinishTask);
            if($lastDate <= $record->date) $lastDate = $record->date;

            /* Process multi-person task. Update consumed on team table. */
            if(!empty($currentTeam))
            {
                $currentTeam->status = $record->left == 0 ? 'done' : 'doing';
                $this->taskTao->updateTeamByEffort($effortID, $record, $currentTeam, $task);
                $newTask = $this->computeMultipleHours($task, $newTask, $task->team);
            }

            $changes = common::createChanges($task, $newTask, 'task');
            if($changes and $actionID) $this->loadModel('action')->logHistory($actionID, $changes);
            if($changes) $allChanges = array_merge($allChanges, $changes);
            $task = $newTask;
        }

        /* Update task and do other operations. */
        if($allChanges)
        {
            $this->dao->update(TABLE_TASK)->data($task, 'team')->where('id')->eq($taskID)->exec();

            if($task->parent > 0) $this->updateParentStatus($task->id);
            if($task->story)  $this->loadModel('story')->setStage($task->story);
            if($task->status != $oldStatus) $this->loadModel('kanban')->updateLane($task->execution, 'task', $taskID);
            if($task->status == 'done' and !dao::isError()) $this->loadModel('score')->create('task', 'finish', $taskID);
        }

        return $allChanges;
    }

    /**
     * Set effort left to 0.
     *
     * @param  int    $taskID
     * @param  array  $members
     * @access public
     * @return void
     */
    public function resetEffortLeft($taskID, $members)
    {
        foreach($members as $account)
        {
            $this->dao->update(TABLE_EFFORT)->set('`left`')->eq(0)
                ->where('account')->eq($account)
                ->andWhere('objectID')->eq($taskID)
                ->andWhere('objectType')->eq('task')
                ->orderBy('date_desc,id_desc')
                ->limit('1')
                ->exec();
        }
    }

    /**
     * 完成任务。
     * Finish a task.
     *
     * @param  object     $oldTask
     * @param  object     $task
     * @access public
     * @return bool|array
     */
    public function finish(object $oldTask, object $task): bool|array
    {
        $currentTeam = !empty($oldTask->team) ? $this->getTeamByAccount($oldTask->team) : array();
        if($currentTeam) $task = $this->computeMultipleHours($oldTask, $task);

        $this->dao->update(TABLE_TASK)->data($task)->autoCheck()->checkFlow()
            ->where('id')->eq((int)$oldTask->id)
            ->exec();

        if(dao::isError()) return false;

        if($task->status == 'done') $this->loadModel('score')->create('task', 'finish', $oldTask->id);
        return common::createChanges($oldTask, $task);
    }

    /**
     * Pause task
     *
     * @param  object $task
     * @param  array  $output
     * @access public
     * @return array|bool
     */
    public function pause(object $task, array $output = array()): bool|array
    {
        /* Get old task. */
        $oldTask = $this->getById($task->id);

        /* Update kanban status. */
        $this->dao->update(TABLE_TASK)->data($task)->autoCheck()->checkFlow()->where('id')->eq($task->id)->exec();

        /* If task has parent task, update status of the parent task. */
        if($oldTask->parent > 0) $this->updateParentStatus($task->id);

        /* If output is not empty, update kanban cell. */
        $this->updateKanbanCell($task->id, $output, $oldTask->execution);

        if(dao::isError()) return false;
        return common::createChanges($oldTask, $task);
    }

    /**
     * 关闭任务。
     * Close a task.
     *
     * @param  object     $oldTask
     * @param  object     $task
     * @param  string     $output
     * @access public
     * @return bool|array
     */
    public function close(object $oldTask, object $task, array $output = array()): bool|array
    {
        $this->dao->update(TABLE_TASK)->data($task)->autoCheck()->checkFlow()->where('id')->eq((int)$oldTask->id)->exec();
        if(dao::isError()) return false;

        $changes = common::createChanges($oldTask, $task);
        $this->afterChangeStatus($oldTask, $changes, 'Closed', $output);
        $this->loadModel('score')->create('task', 'close', $task->id);

        /* Confirm need update issue status. */
        if(isset($oldTask->fromIssue) and $oldTask->fromIssue > 0)
        {
            $fromIssue = $this->loadModel('issue')->getByID($oldTask->fromIssue);
            if($fromIssue->status != 'closed')
            {
                $confirmURL = $this->createLink('issue', 'close', "id=$oldTask->fromIssue");
                $cancelURL  = $this->createLink('task', 'view', "taskID=$oldTask->id");
                return array('result' => 'success', 'load' => array('confirm' => sprintf($this->lang->task->remindIssue, $oldTask->fromIssue), 'confirmed' => $confirmURL, 'canceled' => $cancelURL));
            }
        }
        return true;
    }

    /**
     * Cancel a task.
     *
     * @param object  $task
     * @param string  $extra
     *
     * @access public
     * @return array
     */
    public function cancel(object $task, string $extra = ''): bool
    {
        $oldTask = $this->getById($task->id);
        $this->dao->update(TABLE_TASK)->data($task)
             ->autoCheck()
             ->checkFlow()
             ->where('id')->eq($task->id)
             ->exec();

        if(dao::isError()) return false;

        if($oldTask->fromBug) $this->dao->update(TABLE_BUG)->set('toTask')->eq(0)->where('id')->eq($oldTask->fromBug)->exec();
        if($oldTask->parent > 0) $this->updateParentStatus($task->id);

        /* Cancel a parent task. */
        if($oldTask->parent == '-1') $this->taskTao->cancelParentTask($task);

        if($oldTask->story) $this->loadModel('story')->setStage($oldTask->story);

        $extra = str_replace(array(',', ' '), array('&', ''), $extra);
        parse_str($extra, $output);

        $this->updateKanbanCell($task->id, $output, $oldTask->execution);

        $changes = common::createChanges($oldTask, $task);
        if($changes || $this->post->comment != '')
        {
            $actionID = $this->loadModel('action')->create('task', $task->id, 'Canceled', $this->post->comment);
            $this->action->logHistory($actionID, $changes);
        }

        return true;
    }


    /**
     * 激活任务。
     * Activate task.
     *
     * @param  object      $task
     * @param  string      $comment
     * @param  object      $teamData
     * @param  string      $drag
     * @access public
     * @return array|false
     */
    public function activate(object $task, string $comment, object $teamData, array $drag = array()): array|false
    {
        $taskID = $task->id;

        if(strpos($this->config->task->activate->requiredFields, 'comment') !== false and !$comment)
        {
            dao::$errors[] = sprintf($this->lang->error->notempty, $this->lang->comment);
            return false;
        }

        $oldTask = $this->getById($taskID);
        if($oldTask->parent == '-1') $this->config->task->activate->requiredFields = '';

        if(!empty($oldTask->team))
        {
            $this->manageTaskTeam($oldTask->mode, $task, $teamData);
            $task = $this->computeMultipleHours($oldTask, $task);
        }

        $this->dao->update(TABLE_TASK)->data($task)
            ->autoCheck()
            ->batchCheck($this->config->task->activate->requiredFields, 'notempty')
            ->checkFlow()
            ->where('id')->eq((int)$taskID)
            ->exec();

        if($oldTask->parent > 0) $this->updateParentStatus($taskID);
        if($oldTask->parent == '-1')
        {
            unset($task->left);
            unset($task->id);
            $this->taskTao->updateChildrenByParent($taskID, $task, 'Activated', $comment);
        }
        if($oldTask->story)  $this->loadModel('story')->setStage($oldTask->story);

        $this->updateKanbanCell($taskID, $drag, $oldTask->execution);

        if(!dao::isError()) return common::createChanges($oldTask, $task);
    }

    /**
     * 通过任务ID获取任务的信息。
     * Get task info by ID.
     *
     * @param  int          $taskID
     * @param  bool         $setImgSize
     * @access public
     * @return false|object
     */
    public function getByID(int $taskID, bool $setImgSize = false): false|object
    {
        $task = $this->dao->select('t1.*, t2.id AS storyID, t2.title AS storyTitle, t2.version AS latestStoryVersion, t2.status AS storyStatus, t3.realname AS assignedToRealName')
            ->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
            ->leftJoin(TABLE_USER)->alias('t3')->on('t1.assignedTo = t3.account')
            ->where('t1.id')->eq((int)$taskID)
            ->andWhere('t1.vision')->eq($this->config->vision)
            ->fetch();
        if(!$task) return false;

        /* Format data. */
        $task->openedDate     = !empty($task->openedDate)     ? substr($task->openedDate, 0, 19)     : null;
        $task->finishedDate   = !empty($task->finishedDate)   ? substr($task->finishedDate, 0, 19)   : null;
        $task->canceledDate   = !empty($task->canceledDate)   ? substr($task->canceledDate, 0, 19)   : null;
        $task->closedDate     = !empty($task->closedDate)     ? substr($task->closedDate, 0, 19)     : null;
        $task->lastEditedDate = !empty($task->lastEditedDate) ? substr($task->lastEditedDate, 0, 19) : null;
        $task->realStarted    = !empty($task->realStarted)    ? substr($task->realStarted, 0, 19)    : null;
        $task->mailto         = !empty($task->mailto)         ? $task->mailto                        : null;

        /* Get the child tasks of the parent task. */
        $children = $this->dao->select('*')->from(TABLE_TASK)->where('parent')->eq($taskID)->andWhere('deleted')->eq(0)->fetchAll('id');
        foreach($children as $child)
        {
            $child->team    = array();
            $child->members = array();
        }
        $task->children = $children;

        if($task->parent > 0) $task->parentName = $this->dao->findById($task->parent)->from(TABLE_TASK)->fetch('name');

        /* Get task team and team members. */
        $task->members = array();
        $task->team    = $this->taskTao->getTeamByTask($taskID);
        foreach($task->team as $member) $task->members[$member->account] = $member->account;

        $task = $this->loadModel('file')->replaceImgURL($task, 'desc');
        $task->files = $this->file->getByObject('task', $taskID);
        if($setImgSize) $task->desc = $this->file->setImgSize($task->desc);

        if($task->assignedTo == 'closed') $task->assignedToRealName = 'Closed';

        return $this->processTask($task);
    }

    /**
     * 通过任务ID列表批量获取任务信息。
     * Get the task information from the task ID list.
     *
     * @param  array    $taskIdList
     * @access public
     * @return object[]
     */
    public function getByIdList(array $taskIdList = array()): array
    {
        if(empty($taskIdList)) return array();
        return $this->dao->select('*')->from(TABLE_TASK)
            ->where('deleted')->eq(0)
            ->andWhere('id')->in($taskIdList)
            ->fetchAll('id');
    }

    /**
     * 获取执行下属于moduleIdList的任务信息。
     * Get task information belong to module ID list under execution.
     *
     * @param  int      $executionID
     * @param  array    $moduleIdList
     * @param  string   $orderBy
     * @param  object   $pager
     * @access public
     * @return object[]
     */
    public function getTasksByModule(int $executionID = 0, array $moduleIdList = array(), string $orderBy = 'id_desc', object $pager = null): array
    {
        $tasks = $this->dao->select('t1.*, t2.id AS storyID, t2.title AS storyTitle, t2.product, t2.branch, t2.version AS latestStoryVersion, t2.status AS storyStatus, t3.realname AS assignedToRealName')
            ->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_STORY)->alias('t2')->on('t1.story = t2.id')
            ->leftJoin(TABLE_USER)->alias('t3')->on('t1.assignedTo = t3.account')
            ->where('t1.execution')->eq($executionID)
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($moduleIdList)->andWhere('t1.module')->in($moduleIdList)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'task');

        if(empty($tasks)) return $tasks;
        return $this->processTasks($tasks);
    }

    /**
     * 获取执行下的任务列表信息。
     * Get the task list under a execution.
     *
     * @param  int          $executionID
     * @param  int          $productID
     * @param  string|array $type        all|assignedbyme|myinvolved|undone|needconfirm|assignedtome|finishedbyme|delayed|review|wait|doing|done|pause|cancel|closed|array('wait','doing','done','pause','cancel','closed')
     * @param  array        $modules
     * @param  string       $orderBy
     * @param  object       $pager
     * @access public
     * @return array
     */
    public function getExecutionTasks(int $executionID, int $productID = 0, string|array $type = 'all', array $modules = array(), string $orderBy = 'status_asc, id_desc', object $pager = null): array
    {
        $tasks = $this->taskTao->fetchExecutionTasks($executionID, $productID, $type, $modules, $orderBy, $pager);
        if(empty($tasks)) return array();

        $taskTeam = $this->taskTao->getTeamMembersByIdList(array_keys($tasks));
        foreach($tasks as $task)
        {
            if(isset($taskTeam[$task->id])) $tasks[$task->id]->team = $taskTeam[$task->id];
        }

        if($this->config->vision == 'lite') $tasks = $this->appendLane($tasks);

        $parentIdList = array();
        foreach($tasks as $task)
        {
            if($task->parent <= 0 or isset($tasks[$task->parent]) or isset($parentIdList[$task->parent])) continue;
            $parentIdList[$task->parent] = $task->parent;
        }

        return $this->processTasks($tasks);
    }

    /**
     * 获取任务 id:name 的数组。
     * Get an array of task id:name.
     *
     * @param  int    $executionID
     * @param  string $status
     * @param  string $orderBy
     * @access public
     * @return array
     */
    public function getExecutionTaskPairs(int $executionID, string $status = 'all', string $orderBy = 'finishedBy, id_desc'): array
    {
        $tasks = $this->dao->select('t1.id,t1.name,t1.parent,t2.realname AS finishedByRealName')
            ->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.finishedBy = t2.account')
            ->where('t1.execution')->eq((int)$executionID)
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($status != 'all')->andWhere('t1.status')->in($status)->fi()
            ->orderBy($orderBy)
            ->fetchAll('id');

        $taskPairs = array();
        foreach($tasks as $taskID => $task)
        {
            $prefix = $task->parent > 0 ? "[{$this->lang->task->childrenAB}] " : '';
            $taskPairs[$taskID] = $prefix . "{$taskID}:{$task->finishedByRealName}:{$task->name}";
        }
        return $taskPairs;
    }

    /**
     * 获取父任务 id:name 的数组。
     * Get an array of parent task id:name.
     *
     * @param  int    $executionID
     * @param  string $appendIdList
     * @access public
     * @return array
     */
    public function getParentTaskPairs(int $executionID, string $appendIdList = ''): array
    {
        $taskPairs = $this->dao->select('id, name')->from(TABLE_TASK)
            ->where('deleted')->eq(0)
            ->andWhere('parent')->le(0)
            ->andWhere('status')->notin('cancel,closed')
            ->andWhere('consumed')->eq('0')
            ->andWhere('execution')->eq($executionID)
            ->beginIF($appendIdList)->orWhere('id')->in($appendIdList)->fi()
            ->fetchPairs();

        $taskTeams = $this->dao->select('task')->from(TABLE_TASKTEAM)->where('task')->in(array_keys($taskPairs))->fetchPairs('task', 'task');
        foreach($taskPairs as $id => $name)
        {
            if(!empty($taskTeams[$id])) unset($taskPairs[$id]);
        }
        return $taskPairs;
    }

    /**
     * 通过任务类型获取用户的任务。
     * Get user tasks by type.
     *
     * @param  string   $account
     * @param  string   $type
     * @param  int      $limit
     * @param  object   $pager
     * @param  string   $orderBy
     * @param  int      $projectID
     * @access public
     * @return object[]
     */
    public function getUserTasks(string $account, string $type = 'assignedTo', int $limit = 0, object $pager = null, string $orderBy = 'id_desc', int $projectID = 0): array
    {
        if(!$this->loadModel('common')->checkField(TABLE_TASK, $type)) return array();

        $tasks = $this->taskTao->fetchUserTasksByType($account, $type, $orderBy, $projectID, $limit, $pager);

        if(empty($tasks)) return $tasks;

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'task', false);

        $taskTeam = $this->taskTao->getTeamMembersByIdList(array_keys($tasks));
        foreach($taskTeam as $taskID => $team) $tasks[$taskID]->team = $team;

        return $this->processTasks($tasks);
    }

    /**
     * 获取指派给用户的任务 id:name 数组。
     * Get the task id:name array assigned to the user.
     *
     * @param  string  $account
     * @param  string  $status
     * @param  array   $skipExecutionIDList
     * @param  array   $appendTaskID
     * @access public
     * @return array
     */
    public function getUserTaskPairs(string $account, string $status = 'all', array $skipExecutionIDList = array(), array $appendTaskID = array()): array
    {
        $tasks = $this->dao->select('t1.id, t1.name, t2.name as executionName')
            ->from(TABLE_TASK)->alias('t1')
            ->leftjoin(TABLE_PROJECT)->alias('t2')->on('t1.execution = t2.id')
            ->where('t1.assignedTo')->eq($account)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->beginIF($this->config->vision)->andWhere('t1.vision')->eq($this->config->vision)->fi()
            ->beginIF($status != 'all')->andWhere('t1.status')->in($status)->fi()
            ->beginIF(!empty($skipExecutionIDList))->andWhere('t1.execution')->notin($skipExecutionIDList)->fi()
            ->beginIF(!empty($appendTaskID))->orWhere('t1.id')->in($appendTaskID)->fi()
            ->fetchAll('id');

        $taskPairs = array();
        foreach($tasks as $task) $taskPairs[$task->id] = $task->executionName . ' / ' . $task->name;

        return $taskPairs;
    }

    /**
     * Get suspended tasks of a user.
     *
     * @param  string $account
     * @access public
     * @return object[]
     */
    public function getUserSuspendedTasks($account): array
    {
        return $this->dao->select('t1.*')
            ->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on("t1.execution = t2.id")
            ->leftJoin(TABLE_PROJECT)->alias('t3')->on("t1.project = t3.id")
            ->where('t1.assignedTo')->eq($account)
            ->andWhere('(t2.status')->eq('suspended')
            ->orWhere('t3.status')->eq('suspended')
            ->markRight(1)
            ->andWhere('t1.deleted')->eq(0)
            ->beginIF($this->config->vision)->andWhere('t1.vision')->eq($this->config->vision)->fi()
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t3.deleted')->eq(0)
            ->fetchAll('id');
    }

    /**
     * Get task list of a story.
     *
     * @param  int   $storyID
     * @param  int   $executionID
     * @param  int   $projectID
     * @access public
     * @return object[]
     */
    public function getListByStory(int $storyID, int $executionID = 0, int $projectID = 0): array
    {
        $tasks = $this->dao->select('id, parent, name, assignedTo, pri, status, estimate, consumed, closedReason, `left`')
            ->from(TABLE_TASK)
            ->where('story')->eq($storyID)
            ->andWhere('deleted')->eq(0)
            ->beginIF($executionID)->andWhere('execution')->eq($executionID)->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->fetchAll('id');

        $parentIdList = array();
        foreach($tasks as $task)
        {
            if($task->parent <= 0 or isset($tasks[$task->parent]) or isset($parentIdList[$task->parent])) continue;
            $parentIdList[$task->parent] = $task->parent;
        }

        $parentTasks = $this->getByIdList($parentIdList);
        $tasks       = $this->taskTao->buildTaskTree($tasks, $parentTasks);
        return $this->taskTao->batchComputeProgress($tasks);
    }

    /**
     * Get task list of stories.
     *
     * @param  int[]    $storyIdList
     * @param  int      $executionID
     * @param  int      $projectID
     * @access public
     * @return object[]
     */
    public function getListByStories(array $storyIdList, int $executionID = 0, int $projectID = 0): array
    {
        return $this->dao->select('id, story, parent, name, assignedTo, pri, status, estimate, consumed, closedReason, `left`')
            ->from(TABLE_TASK)
            ->where('story')->in($storyIdList)
            ->andWhere('deleted')->eq(0)
            ->beginIF($executionID)->andWhere('execution')->eq($executionID)->fi()
            ->beginIF($projectID)->andWhere('project')->eq($projectID)->fi()
            ->fetchAll('id');
    }

    /**
     * Get counts of some stories' tasks.
     *
     * @param  array  $stories
     * @param  int    $executionID
     * @access public
     * @return int
     */
    public function getStoryTaskCounts($stories, $executionID = 0)
    {
        if(empty($stories)) return array();
        $taskCounts = $this->dao->select('story, COUNT(*) AS tasks')
            ->from(TABLE_TASK)
            ->where('story')->in($stories)
            ->andWhere('deleted')->eq(0)
            ->beginIF($executionID)->andWhere('execution')->eq($executionID)->fi()
            ->groupBy('story')
            ->fetchPairs();
        foreach($stories as $storyID)
        {
            if(!isset($taskCounts[$storyID])) $taskCounts[$storyID] = 0;
        }
        return $taskCounts;
    }

    /**
     * Update estimate order for linear task team.
     *
     * @param  int    $effortID
     * @param  int    $order
     * @access public
     * @return void
     */
    public function updateEstimateOrder($effortID, $order)
    {
        $this->dao->update(TABLE_EFFORT)->set('`order`')->eq((int)$order)->where('id')->eq($effortID)->exec();
    }

    /**
     * Get task efforts.
     *
     * @param  int    $taskID
     * @param  string $account
     * @param  string $append
     * @param  string $orderBy
     * @access public
     * @return array
     */
    public function getTaskEfforts($taskID, $account = '', $append = '', $orderBy = 'date,id')
    {
        return $this->dao->select('*')->from(TABLE_EFFORT)->where('objectID')->eq($taskID)
            ->andWhere('objectType')->eq('task')
            ->andWhere('deleted')->eq('0')
            ->beginIF($account)->andWhere('account')->eq($account)->fi()
            ->beginIF($append)->orWhere('id')->eq($append)->fi()
            ->orderBy($orderBy)
            ->fetchAll();
    }

    /**
     * Get taskList date record.
     *
     * @param  int|array $taskID
     * @access public
     * @return array
     */
    public function getTaskDateRecord($taskID)
    {
        return $this->dao->select('id,date')->from(TABLE_EFFORT)->where('objectID')->in($taskID)
            ->andWhere('objectType')->eq('task')
            ->andWhere('deleted')->eq('0')
            ->orderBy('date')
            ->fetchAll('id');
    }

    /**
     * Get estimate by id.
     *
     * @param  int    $effortID
     * @access public
     * @return object.
     */
    public function getEstimateById($effortID)
    {
        $estimate = $this->dao->select('*')->from(TABLE_EFFORT)
            ->where('id')->eq($effortID)
            ->fetch();

        /* If the estimate is the last of its task, status of task will be checked. */
        $lastID = $this->dao->select('id')->from(TABLE_EFFORT)
            ->where('objectID')->eq($estimate->objectID)
            ->andWhere('objectType')->eq('task')
            ->orderBy('date_desc,id_desc')->limit(1)->fetch('id');

        $estimate->isLast = $lastID == $estimate->id;
        return $estimate;
    }

    /**
     * Check operate effort.
     *
     * @param  object    $task
     * @param  object    $effort
     * @access public
     * @return bool
     */
    public function canOperateEffort($task, $effort = null)
    {
        if(empty($task->team)) return true;

        /* Check for add effort. */
        if(empty($effort))
        {
            $members = array_column($task->team, 'account');
            if(!in_array($this->app->user->account, $members)) return false;
            if($task->mode == 'linear' and $this->app->user->account != $task->assignedTo) return false;
            return true;
        }

        /* Check for edit and delete effort. */
        if($task->mode == 'linear')
        {
            if(strpos('|closed|cancel|pause|', "|{$task->status}|") !== false) return false;
            if($task->status == 'doing') return $effort->account == $this->app->user->account;
        }
        if($this->app->user->account == $effort->account) return true;
        return false;
    }

    /**
     * Update estimate.
     *
     * @param  int    $estimateID
     * @access public
     * @return void
     */
    public function updateEstimate($estimateID)
    {
        $oldEstimate = $this->getEstimateById($estimateID);
        $today       = helper::today();
        $estimate    = fixer::input('post')
            ->setIF(is_numeric($this->post->consumed), 'consumed', (float)$this->post->consumed)
            ->setIF(is_numeric($this->post->left), 'left', (float)$this->post->left)
            ->get();

        if(helper::isZeroDate($estimate->date)) return dao::$errors[] = $this->lang->task->error->dateEmpty;
        if($estimate->date > $today)            return dao::$errors[] = $this->lang->task->error->date;
        if($estimate->consumed <= 0)            return dao::$errors[] = sprintf($this->lang->error->gt, $this->lang->task->record, '0');
        if($estimate->left < 0)                 return dao::$errors[] = sprintf($this->lang->error->ge, $this->lang->task->left, '0');

        $task = $this->getById($oldEstimate->objectID);
        $this->dao->update(TABLE_EFFORT)->data($estimate)
            ->autoCheck()
            ->where('id')->eq((int)$estimateID)
            ->exec();

        $lastEstimate = $this->dao->select('*')->from(TABLE_EFFORT)
            ->where('objectID')->eq($task->id)
            ->andWhere('objectType')->eq('task')
            ->orderBy('date_desc,id_desc')->limit(1)->fetch();

        $consumed = $task->consumed + $estimate->consumed - $oldEstimate->consumed;
        $left     = ($lastEstimate and $estimateID == $lastEstimate->id) ? $estimate->left : $task->left;

        $now  = helper::now();
        $data = new stdclass();
        $data->consumed       = $consumed;
        $data->left           = $left;
        $data->status         = $task->status;
        $data->lastEditedBy   = $this->app->user->account;
        $data->lastEditedDate = $now;
        if(empty($left) and strpos('wait,doing,pause', $task->status) !== false)
        {
            $data->status       = 'done';
            $data->finishedBy   = $this->app->user->account;
            $data->finishedDate = $now;
            $data->assignedTo   = $task->openedBy;
        }

        if(!empty($task->team))
        {
            $currentTeam = $this->getTeamByAccount($task->team, $oldEstimate->account, array('order' => $oldEstimate->order));
            if($currentTeam)
            {
                $newTeamInfo = new stdclass();
                $newTeamInfo->consumed = $currentTeam->consumed + $estimate->consumed - $oldEstimate->consumed;
                if($currentTeam->status != 'done') $newTeamInfo->left = $left;
                if($currentTeam->status != 'done' and $newTeamInfo->consumed > 0 and $left == 0) $newTeamInfo->status = 'done';
                $this->dao->update(TABLE_TASKTEAM)->data($newTeamInfo)->where('id')->eq($currentTeam->id)->exec();

                $data = $this->computeMultipleHours($task, $data);
            }
        }

        $this->dao->update(TABLE_TASK)->data($data)->where('id')->eq($task->id)->exec();
        if(!dao::isError())
        {
            if($task->parent > 0) $this->updateParentStatus($task->id);
            if($task->story)  $this->loadModel('story')->setStage($task->story);

            $oldTask = new stdclass();
            $oldTask->consumed = $task->consumed;
            $oldTask->left     = $task->left;
            $oldTask->status   = $task->status;

            $newTask = new stdclass();
            $newTask->consumed = $data->consumed;
            $newTask->left     = $data->left;
            $newTask->status   = $data->status;

            return common::createChanges($oldTask, $newTask);
        }
    }

    /**
     * Delete estimate.
     *
     * @param  int    $estimateID
     * @access public
     * @return void
     */
    public function deleteEstimate($estimateID)
    {
        $estimate = $this->getEstimateById($estimateID);
        $task     = $this->getById($estimate->objectID);
        $now      = helper::now();

        $consumed = $task->consumed - $estimate->consumed;
        $left     = $task->left;
        if($estimate->isLast)
        {
            $lastTwoEstimates = $this->dao->select('*')->from(TABLE_EFFORT)
                ->where('objectID')->eq($estimate->objectID)
                ->andWhere('objectType')->eq('task')
                ->orderBy('date desc,id desc')->limit(2)->fetchAll();
            $lastTwoEstimate  = isset($lastTwoEstimates[1]) ? $lastTwoEstimates[1] : '';
            if($lastTwoEstimate) $left = $lastTwoEstimate->left;
            if(empty($lastTwoEstimate) and $left == 0) $left = $task->estimate;
        }

        $data = new stdclass();
        $data->consumed = $consumed;
        $data->left     = $left;
        $data->status   = ($left == 0 && $consumed != 0) ? 'done' : $task->status;
        if($estimate->isLast and $consumed == 0 and $task->status != 'wait')
        {
            $data->status       = 'wait';
            $data->left         = $task->estimate;
            $data->finishedBy   = '';
            $data->canceledBy   = '';
            $data->closedBy     = '';
            $data->closedReason = '';
            $data->finishedDate = '0000-00-00 00:00:00';
            $data->canceledDate = '0000-00-00 00:00:00';
            $data->closedDate   = '0000-00-00 00:00:00';
            if($task->assignedTo == 'closed') $data->assignedTo = $this->app->user->account;
        }
        elseif($consumed != 0 and $left == 0 and strpos('done,pause,cancel,closed', $task->status) === false)
        {
            $data->status         = 'done';
            $data->assignedTo     = $task->openedBy;
            $data->assignedDate   = $now;
            $data->finishedBy     = $this->app->user->account;
            $data->finishedDate   = $now;
        }
        elseif($estimate->isLast and $left != 0 and strpos('done,pause,cancel,closed', $task->status) !== false)
        {
            $data->status         = 'doing';
            $data->finishedBy     = '';
            $data->canceledBy     = '';
            $data->closedBy       = '';
            $data->closedReason   = '';
            $data->finishedDate   = '0000-00-00 00:00:00';
            $data->canceledDate   = '0000-00-00 00:00:00';
            $data->closedDate     = '0000-00-00 00:00:00';
        }
        else
        {
            $data->status = $task->status;
        }

        if(!empty($task->team))
        {
            $currentTeam = $this->getTeamByAccount($task->team, $estimate->account, array('effortID' => $estimateID, 'order' => $estimate->order));
            if($currentTeam)
            {
                $left = $currentTeam->left;
                if($task->mode == 'multi')
                {
                    $accountEstimates = $this->getTaskEfforts($currentTeam->task, $estimate->account, $estimateID);
                    $lastEstimate     = array_pop($accountEstimates);
                    if($lastEstimate->id == $estimateID)
                    {
                        $lastTwoEstimate = array_pop($accountEstimates);
                        if($lastTwoEstimate) $left = $lastTwoEstimate->left;
                    }
                }

                $newTeamInfo = new stdclass();
                $newTeamInfo->consumed = $currentTeam->consumed - $estimate->consumed;
                if($currentTeam->status != 'done') $newTeamInfo->left = $left;
                if($currentTeam->status == 'done' and $left > 0 and $task->mode == 'multi') $newTeamInfo->left = $left;

                if($currentTeam->status != 'done' and $newTeamInfo->consumed > 0 and $left == 0) $newTeamInfo->status = 'done';
                if($task->mode == 'multi' and $currentTeam->status == 'done' and $left > 0) $newTeamInfo->status = 'doing';
                if($task->mode == 'multi' and $currentTeam->status == 'done' and ($newTeamInfo->consumed == 0 and $left == 0))
                {
                    $newTeamInfo->status = 'doing';
                    $newTeamInfo->left   = $currentTeam->estimate;
                }
                $this->dao->update(TABLE_TASKTEAM)->data($newTeamInfo)->where('id')->eq($currentTeam->id)->exec();
            }
        }

        $this->dao->update(TABLE_EFFORT)->set('deleted')->eq('1')->where('id')->eq($estimateID)->exec();
        if(!empty($task->team)) $data = $this->computeMultipleHours($task, $data);

        $this->dao->update(TABLE_TASK)->data($data) ->where('id')->eq($estimate->objectID)->exec();
        if($task->parent > 0) $this->updateParentStatus($task->id);
        if($task->story)  $this->loadModel('story')->setStage($task->story);

        $oldTask = new stdclass();
        $oldTask->consumed = $task->consumed;
        $oldTask->left     = $task->left;
        $oldTask->status   = $task->status;

        $newTask = new stdclass();
        $newTask->consumed = $data->consumed;
        $newTask->left     = $data->left;
        $newTask->status   = $data->status;

        if(!dao::isError()) return common::createChanges($oldTask, $newTask);
    }

    /**
     * Append lane field to tasks;
     *
     * @param  array  $tasks
     * @access public
     * @return array
     */
    public function appendLane($tasks)
    {
        $executionIdList = array();
        foreach($tasks as $task)
        {
            $task->lane = '';
            if(!isset($executionIdList[$task->execution])) $executionIdList[$task->execution] = $task->execution;
        }

        $lanes = $this->dao->select('t1.kanban,t1.lane,t2.name,t1.cards')->from(TABLE_KANBANCELL)->alias('t1')
            ->leftJoin(TABLE_KANBANLANE)->alias('t2')->on('t1.lane = t2.id')
            ->where('t1.kanban')->in($executionIdList)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.type')->eq('task')
            ->andWhere("t1.cards")->ne('')
            ->fetchAll();

        if(empty($lanes)) return $tasks;

        foreach($tasks as $task)
        {
            foreach($lanes as $lane)
            {
                if($lane->kanban != $task->execution) continue;
                if(strpos(",{$lane->cards},", ",{$task->id},") !== false)
                {
                    $task->lane = $lane->name;
                    break;
                }
            }
        }

        return $tasks;
    }

    /**
     * Batch process tasks.
     *
     * @param  int    $tasks
     * @access public
     * @return void
     */
    public function processTasks($tasks)
    {
        foreach($tasks as $task)
        {
            $task = $this->processTask($task);
            if(!empty($task->children))
            {
                foreach($task->children as $child)
                {
                    $tasks[$task->id]->children[$child->id] = $this->processTask($child);
                }
            }
        }
        return $tasks;
    }

    /**
     * Process a task, judge it's status.
     *
     * @param  object    $task
     * @access public
     * @return object
     */
    public function processTask($task)
    {
        $today = helper::today();

        /* Delayed or not?. */
        if(in_array($task->status, $this->config->task->unfinishedStatus) && !empty($task->deadline) && !helper::isZeroDate($task->deadline))
        {
            $delay = helper::diffDate($today, $task->deadline);
            if($delay > 0) $task->delay = $delay;
        }

        /* Story changed or not. */
        $task->needConfirm = false;
        if(!empty($task->storyStatus) and $task->storyStatus == 'active' and $task->latestStoryVersion > $task->storyVersion) $task->needConfirm = true;

        /* Set product type for task. */
        if(!empty($task->product))
        {
            $product = $this->loadModel('product')->getById($task->product);
            if($product) $task->productType = $product->type;
        }

        /* Get related test cases. */
        if($task->story) $task->cases = $this->dao->select('id, title')->from(TABLE_CASE)->where('story')->eq($task->story)->andWhere('storyVersion')->eq($task->storyVersion)->andWhere('deleted')->eq('0')->fetchPairs();

        /* Set closed realname. */
        if($task->assignedTo == 'closed') $task->assignedToRealName = 'Closed';

        $task->progress = $this->taskTao->computeTaskProgress($task);

        if($task->mode == 'multi')
        {
            $teamMembers = $this->dao->select('t1.realname')->from(TABLE_USER)->alias('t1')
                ->leftJoin(TABLE_TASKTEAM)->alias('t2')
                ->on('t1.account = t2.account')
                ->where('t2.task')->eq($task->id)
                ->fetchPairs();
            $task->teamMembers = implode(',', array_keys($teamMembers));
        }

        return $task;
    }

    /**
     * Check whether need update status of bug.
     *
     * @param  object  $task
     * @access public
     * @return void
     */
    public function needUpdateBugStatus($task)
    {
        /* If task is not from bug, return false. */
        if($task->fromBug == 0) return false;

        /* If bug has been resolved, return false. */
        $bug = $this->loadModel('bug')->getById($task->fromBug);
        if($bug->status == 'resolved') return false;

        return true;
    }

    /**
     * Merge the default chart settings and the settings of current chart.
     *
     * @param  string    $chartType
     * @access public
     * @return void
     */
    public function mergeChartOption($chartType)
    {
        $chartOption  = $this->lang->task->report->$chartType;
        $commonOption = $this->lang->task->report->options;

        $chartOption->graph->caption = $this->lang->task->report->charts[$chartType];
        if(!isset($chartOption->type))   $chartOption->type   = $commonOption->type;
        if(!isset($chartOption->width))  $chartOption->width  = $commonOption->width;
        if(!isset($chartOption->height)) $chartOption->height = $commonOption->height;

        /* merge configuration */
        foreach($commonOption->graph as $key => $value)
        {
            if(!isset($chartOption->graph->$key)) $chartOption->graph->$key = $value;
        }
    }

    /**
     * 获取执行任务的报表数据。
     * Get report data of tasks per execution.
     *
     * @access public
     * @return object[]
     */
    public function getDataOfTasksPerExecution(): array
    {
        $tasks = $this->taskTao->getListByReportCondition('execution', $this->reportCondition());
        if(!$tasks) return array();

        $datas = $this->processData4Report($tasks, '', 'execution');

        /* Get execution names for these tasks. */
        $executions = $this->loadModel('execution')->getPairs(0, 'all', 'all');
        foreach($datas as $executionID => $data) $data->name  = isset($executions[$executionID]) ? $executions[$executionID] : $this->lang->report->undefined;
        return $datas;
    }

    /**
     * Get report data of tasks per module
     *
     * @access public
     * @return array
     */
    public function getDataOfTasksPerModule()
    {
        $tasks = $this->dao->select('id,module')->from(TABLE_TASK)->alias('t1')
            ->where($this->reportCondition())
            ->fetchAll('id');
        if(!$tasks) return array();

        $datas    = $this->processData4Report($tasks, '', 'module');

        $modules = $this->loadModel('tree')->getModulesName(array_keys($datas), true, true);
        foreach($datas as $moduleID => $data)
        {
            $data->name = isset($modules[$moduleID]) ? $modules[$moduleID] : '/';
        }
        return $datas;
    }

    /**
     * Get report data of tasks per assignedTo
     *
     * @access public
     * @return array
     */
    public function getDataOfTasksPerAssignedTo()
    {
        $tasks = $this->dao->select('id,assignedTo')->from(TABLE_TASK)->alias('t1')
            ->where($this->reportCondition())
            ->fetchAll('id');
        if(!$tasks) return array();

        $datas    = $this->processData4Report($tasks, '', 'assignedTo');

        if(!isset($this->users)) $this->users = $this->loadModel('user')->getPairs('noletter');
        foreach($datas as $account => $data)
        {
            if(isset($this->users[$account])) $data->name = $this->users[$account];
        }
        return $datas;
    }

    /**
     * Get report data of tasks per type
     *
     * @access public
     * @return array
     */
    public function getDataOfTasksPerType()
    {
        $tasks = $this->dao->select('id,type')->from(TABLE_TASK)->alias('t1')
            ->where($this->reportCondition())
            ->fetchAll('id');
        if(!$tasks) return array();

        $datas    = $this->processData4Report($tasks, '', 'type');

        foreach($datas as $type => $data)
        {
            if(isset($this->lang->task->typeList[$type])) $data->name = $this->lang->task->typeList[$type];
        }
        return $datas;
    }

    /**
     * Get report data of tasks per priority
     *
     * @access public
     * @return array
     */
    public function getDataOfTasksPerPri()
    {
        $tasks = $this->dao->select('id,pri')->from(TABLE_TASK)->alias('t1')
            ->where($this->reportCondition())
            ->fetchAll('id');
        if(!$tasks) return array();

        $datas    = $this->processData4Report($tasks, '', 'pri');

        foreach($datas as $pri) $pri->name = $this->lang->task->priList[$pri->name];
        return $datas;
    }

    /**
     * Get report data of tasks per deadline
     *
     * @access public
     * @return array
     */
    public function getDataOfTasksPerDeadline()
    {
        $tasks = $this->dao->select('id,deadline')->from(TABLE_TASK)->alias('t1')
            ->where($this->reportCondition())
            ->orderBy('deadline asc')
            ->fetchAll('id');
        if(!$tasks) return array();

        return $this->processData4Report($tasks, '', 'deadline');
    }

    /**
     * Get report data of tasks per estimate
     *
     * @access public
     * @return array
     */
    public function getDataOfTasksPerEstimate()
    {
        $tasks = $this->dao->select('id,estimate')->from(TABLE_TASK)->alias('t1')
            ->where($this->reportCondition())
            ->fetchAll('id');
        if(!$tasks) return array();

        $children = $this->dao->select('id,parent,estimate')->from(TABLE_TASK)->where('parent')->in(array_keys($tasks))->fetchAll('id');
        return $this->processData4Report($tasks, $children, 'estimate');
    }

    /**
     * Get report data of tasks per left
     *
     * @access public
     * @return array
     */
    public function getDataOfTasksPerLeft()
    {
        $tasks = $this->dao->select('id,`left`')->from(TABLE_TASK)->alias('t1')
            ->where($this->reportCondition())
            ->fetchAll('id');
        if(!$tasks) return array();

        $children = $this->dao->select('id,parent,`left`')->from(TABLE_TASK)->where('parent')->in(array_keys($tasks))->fetchAll('id');
        return $this->processData4Report($tasks, $children, 'left');
    }

    /**
     * Get report data of tasks per consumed
     *
     * @access public
     * @return array
     */
    public function getDataOfTasksPerConsumed()
    {
        $tasks = $this->dao->select('id,consumed')->from(TABLE_TASK)->alias('t1')
            ->where($this->reportCondition())
            ->fetchAll('id');
        if(!$tasks) return array();

        $children = $this->dao->select('id,parent,consumed')->from(TABLE_TASK)->where('parent')->in(array_keys($tasks))->fetchAll('id');
        return $this->processData4Report($tasks, $children, 'consumed');
    }

    /**
     * Get report data of tasks per finishedBy
     *
     * @access public
     * @return array
     */
    public function getDataOfTasksPerFinishedBy()
    {
        $tasks = $this->dao->select('id,finishedBy')->from(TABLE_TASK)->alias('t1')
            ->where($this->reportCondition())
            ->andWhere('finishedBy')->ne('')
            ->fetchAll('id');
        if(!$tasks) return array();

        $datas    = $this->processData4Report($tasks, '', 'finishedBy');

        if(!isset($this->users)) $this->users = $this->loadModel('user')->getPairs('noletter');
        foreach($datas as $account => $data)
        {
            if(isset($this->users[$account])) $data->name = $this->users[$account];
        }
        return $datas;
    }

    /**
     * Get report data of tasks per closed reason
     *
     * @access public
     * @return array
     */
    public function getDataOfTasksPerClosedReason()
    {
        $tasks = $this->dao->select('id,closedReason')->from(TABLE_TASK)->alias('t1')
            ->where($this->reportCondition())
            ->andWhere('closedReason')->ne('')
            ->fetchAll('id');
        if(!$tasks) return array();

        $datas    = $this->processData4Report($tasks, '', 'closedReason');

        foreach($datas as $closedReason => $data)
        {
            if(isset($this->lang->task->reasonList[$closedReason])) $data->name = $this->lang->task->reasonList[$closedReason];
        }
        return $datas;
    }

    /**
     * Get report data of finished tasks per day
     *
     * @access public
     * @return object[]
     */
    public function getDataOffinishedTasksPerDay(): array
    {
        $tasks = $this->dao->select("id, DATE_FORMAT(`finishedDate`, '%Y-%m-%d') AS `date`")->from(TABLE_TASK)
            ->where($this->reportCondition())
            ->andWhere('finishedDate')->notZeroDatetime()
            ->orderBy('finishedDate asc')
            ->fetchAll('id');
        if(!$tasks) return array();

        return $this->processData4Report($tasks, '', 'date');
    }

    /**
     * Get report data of status
     *
     * @access public
     * @return array
     */
    public function getDataOfTasksPerStatus()
    {
        $tasks = $this->dao->select('id,status')->from(TABLE_TASK)->alias('t1')
            ->where($this->reportCondition())
            ->fetchAll('id');
        if(!$tasks) return array();

        $datas    = $this->processData4Report($tasks, '', 'status');

        foreach($datas as $status => $data) $data->name = $this->lang->task->statusList[$status];
        return $datas;
    }

    /**
     * Process data for report.
     *
     * @param  array    $tasks
     * @param  array    $children
     * @param  string   $field
     * @access public
     * @return array
     */
    public function processData4Report($tasks, $children, $field)
    {
        if(is_array($children))
        {
            /* Remove the parent task from the tasks. */
            foreach($children as $childTask) unset($tasks[$childTask->parent]);
        }

        $fields = array();
        $datas  = array();
        foreach($tasks as $task)
        {
            if(!isset($fields[$task->$field])) $fields[$task->$field] = 0;
            $fields[$task->$field] ++;
        }
        if($field != 'date' and $field != 'deadline') asort($fields);
        foreach($fields as $field => $count)
        {
            $data = new stdclass();
            $data->name  = $field;
            $data->value = $count;
            $datas[$field] = $data;
        }

        return $datas;
    }

    /**
     * Judge an action is clickable or not.
     *
     * @param  object    $task
     * @param  string    $action
     * @access public
     * @return bool
     */
    public static function isClickable($task, $action)
    {
        $action = strtolower($action);

        if($action == 'start'          and $task->parent < 0) return false;
        if($action == 'finish'         and $task->parent < 0) return false;
        if($action == 'pause'          and $task->parent < 0) return false;
        if($action == 'assignto'       and $task->parent < 0) return false;
        if($action == 'close'          and $task->parent < 0) return false;
        if($action == 'batchcreate'    and !empty($task->team))     return false;
        if($action == 'batchcreate'    and $task->parent > 0)       return false;
        if($action == 'recordworkhour' and $task->parent == -1)     return false;
        if($action == 'delete'         and $task->parent < 0)       return false;

        if(!empty($task->team))
        {
            global $app;
            $myself = new self();
            if($task->mode == 'linear')
            {
                if($action == 'assignto' and strpos('done,cencel,closed', $task->status) === false) return false;
                if($action == 'start' and strpos('wait,doing', $task->status) !== false)
                {
                    if($task->assignedTo != $app->user->account) return false;

                    $currentTeam = $myself->getTeamByAccount($task->team, $app->user->account);
                    if($currentTeam and $currentTeam->status == 'wait') return true;
                }
                if($action == 'finish' and $task->assignedTo != $app->user->account) return false;
            }
            elseif($task->mode == 'multi')
            {
                $currentTeam = $myself->getTeamByAccount($task->team, $app->user->account);
                if($action == 'start' and strpos('wait,doing', $task->status) !== false and $currentTeam and $currentTeam->status == 'wait') return true;
                if($action == 'finish' and (empty($currentTeam) or $currentTeam->status == 'done')) return false;
            }
        }

        if($action == 'start')    return $task->status == 'wait';
        if($action == 'restart')  return $task->status == 'pause';
        if($action == 'pause')    return $task->status == 'doing';
        if($action == 'assignto') return $task->status != 'closed' and $task->status != 'cancel';
        if($action == 'close')    return $task->status == 'done'   or  $task->status == 'cancel';
        if($action == 'activate') return $task->status == 'done'   or  $task->status == 'closed'  or $task->status  == 'cancel';
        if($action == 'finish')   return $task->status != 'done'   and $task->status != 'closed'  and $task->status != 'cancel';
        if($action == 'cancel')   return $task->status != 'done'   and $task->status != 'closed'  and $task->status != 'cancel';

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
        if(isset($_SESSION['taskQueryCondition']))
        {
            if(!$this->session->taskOnlyCondition) return 'id in (' . preg_replace('/SELECT .* FROM/', 'SELECT t1.id FROM', $this->session->taskQueryCondition) . ')';
            return $this->session->taskQueryCondition;
        }
        return true;
    }

    /**
     * Add task effort.
     *
     * @param  object    $data
     * @access public
     * @return int
     */
    public function addTaskEffort($data)
    {
        $oldTask = $this->getById($data->task);

        $relation = $this->loadModel('action')->getRelatedFields('task', $data->task);

        $effort = new stdclass();
        $effort->objectType = 'task';
        $effort->objectID   = $data->task;
        $effort->execution  = $oldTask->execution;
        $effort->product    = $relation['product'];
        $effort->project    = (int)$relation['project'];
        $effort->account    = $data->account;
        $effort->date       = $data->date;
        $effort->consumed   = $data->consumed;
        $effort->left       = $data->left;
        $effort->work       = isset($data->work) ? $data->work : '';
        $effort->vision     = $this->config->vision;
        $effort->order      = isset($data->order) ? $data->order : 0;
        $this->dao->insert(TABLE_EFFORT)->data($effort)->autoCheck()->exec();

        return $this->dao->lastInsertID();
    }

    /**
     * Print cell data.
     *
     * @param object $col
     * @param object $task
     * @param array  $users
     * @param string $browseType
     * @param array  $branchGroups
     * @param array  $modulePairs
     * @param bool   $showBranch
     * @access public
     * @return void
     */
    public function printCell($col, $task, $users, $browseType, $branchGroups, $modulePairs = array(), $showBranch = false)
    {
        $canBatchEdit         = common::hasPriv('task', 'batchEdit', !empty($task) ? $task : null);
        $canBatchClose        = (common::hasPriv('task', 'batchClose', !empty($task) ? $task : null) and strtolower($browseType) != 'closed');
        $canBatchCancel       = common::hasPriv('task', 'batchCancel', !empty($task) ? $task : null);
        $canBatchChangeModule = common::hasPriv('task', 'batchChangeModule', !empty($task) ? $task : null);
        $canBatchAssignTo     = common::hasPriv('task', 'batchAssignTo', !empty($task) ? $task : null);

        $canBatchAction = in_array(true, array($canBatchEdit, $canBatchClose, $canBatchCancel, $canBatchChangeModule, $canBatchAssignTo));
        $storyChanged   = (!empty($task->storyStatus) and $task->storyStatus == 'active' and $task->latestStoryVersion > $task->storyVersion and !in_array($task->status, array('cancel', 'closed')));

        $canView  = common::hasPriv('task', 'view');
        $taskLink = helper::createLink('task', 'view', "taskID=$task->id");
        $account  = $this->app->user->account;
        $id       = $col->id;
        if($col->show)
        {
            $class = "c-{$id}";
            if($id == 'status') $class .= ' task-' . $task->status;
            if($id == 'id')     $class .= ' cell-id';
            if($id == 'name')   $class .= ' text-left';
            if($id == 'deadline') $class .= ' text-center';
            if($id == 'deadline' and isset($task->delay)) $class .= ' delayed';
            if($id == 'assignedTo') $class .= ' has-btn text-left';
            if($id == 'lane') $class .= ' text-left';
            if(strpos('progress', $id) !== false) $class .= ' text-right';

            $title = '';
            if($id == 'name')
            {
                $title = " title='{$task->name}'";
                if(!empty($task->children)) $class .= ' has-child';
            }
            if($id == 'story') $title = " title='{$task->storyTitle}'";
            if($id == 'estimate' || $id == 'consumed' || $id == 'left')
            {
                $value = round($task->$id, 1);
                $title = " title='{$value} {$this->lang->execution->workHour}'";
            }
            if($id == 'lane') $title = " title='{$task->lane}'";
            if($id == 'finishedBy') $title  = " title='" . zget($users, $task->finishedBy) . "'";
            if($id == 'openedBy') $title  = " title='" . zget($users, $task->openedBy) . "'";
            if($id == 'lastEditedBy') $title  = " title='" . zget($users, $task->lastEditedBy) . "'";

            echo "<td class='" . $class . "'" . $title . ">";
            if($this->config->edition != 'open') $this->loadModel('flow')->printFlowCell('task', $task, $id);
            switch($id)
            {
            case 'id':
                if($canBatchAction)
                {
                    echo html::checkbox('taskIdList', array($task->id => '')) . html::a(helper::createLink('task', 'view', "taskID=$task->id"), sprintf('%03d', $task->id));
                }
                else
                {
                    printf('%03d', $task->id);
                }
                break;
            case 'pri':
                $priClass = $task->pri ? "label-pri label-pri-{$task->pri}" : '';
                echo "<span class='$priClass' title='" . zget($this->lang->task->priList, $task->pri, $task->pri) . "'>";
                echo zget($this->lang->task->priList, $task->pri, $task->pri);
                echo "</span>";
                break;
            case 'name':
                if($showBranch) $showBranch = isset($this->config->execution->task->showBranch) ? $this->config->execution->task->showBranch : 1;
                if($task->parent > 0 and isset($task->parentName)) $task->name = "{$task->parentName} / {$task->name}";
                if(!empty($task->product) and isset($branchGroups[$task->product][$task->branch]) and $showBranch) echo "<span class='label label-badge label-outline'>" . $branchGroups[$task->product][$task->branch] . '</span> ';
                if($task->module and isset($modulePairs[$task->module])) echo "<span class='label label-gray label-badge'>" . $modulePairs[$task->module] . '</span> ';
                if($task->parent > 0) echo '<span class="label label-badge label-light" title="' . $this->lang->task->children . '">' . $this->lang->task->childrenAB . '</span> ';
                if(!empty($task->team)) echo '<span class="label label-badge label-light" title="' . $this->lang->task->multiple . '">' . $this->lang->task->multipleAB . '</span> ';
                echo $canView ? html::a($taskLink, $task->name, null, "style='color: $task->color' title='$task->name'") : "<span style='color: $task->color'>$task->name</span>";
                if(!empty($task->children)) echo '<a class="task-toggle" data-id="' . $task->id . '"><i class="icon icon-angle-double-right"></i></a>';
                if($task->fromBug) echo html::a(helper::createLink('bug', 'view', "id=$task->fromBug"), "[BUG#$task->fromBug]", '', "class='bug'");
                break;
            case 'type':
                echo zget($this->lang->task->typeList, $task->type, $task->type);
                break;
            case 'status':
                $storyChanged ? print("<span class='status-story status-changed' title='{$this->lang->story->changed}'>{$this->lang->story->changed}</span>") : print("<span class='status-task status-{$task->status}' title='{$this->processStatus('task', $task)}'> " . $this->processStatus('task', $task) . "</span>");
                break;
            case 'estimate':
                echo round($task->estimate, 1) . $this->lang->execution->workHourUnit;
                break;
            case 'consumed':
                echo round($task->consumed, 1) . $this->lang->execution->workHourUnit;
                break;
            case 'left':
                echo round($task->left, 1)     . $this->lang->execution->workHourUnit;
                break;
            case 'progress':
                echo round($task->progress, 2) . '%';
                break;
            case 'deadline':
                if($task->deadline && substr($task->deadline, 0, 4) > 0) echo '<span>' . substr($task->deadline, 5, 6) . '</span>';
                break;
            case 'openedBy':
                echo zget($users, $task->openedBy);
                break;
            case 'openedDate':
                echo substr($task->openedDate, 5, 11);
                break;
            case 'estStarted':
                echo helper::isZeroDate($task->estStarted) ? '' : substr($task->estStarted, 5, 11);
                break;
            case 'realStarted':
                echo helper::isZeroDate($task->realStarted) ? '' : substr($task->realStarted, 5, 11);
                break;
            case 'assignedTo':
                $this->printAssignedHtml($task, $users);
                break;
            case 'lane':
                echo mb_substr($task->lane, 0, 8);
                break;
            case 'assignedDate':
                echo helper::isZeroDate($task->assignedDate) ? '' : substr($task->assignedDate, 5, 11);
                break;
            case 'finishedBy':
                echo zget($users, $task->finishedBy);
                break;
            case 'finishedDate':
                echo helper::isZeroDate($task->finishedDate) ? '' : substr($task->finishedDate, 5, 11);
                break;
            case 'canceledBy':
                echo zget($users, $task->canceledBy);
                break;
            case 'canceledDate':
                echo helper::isZeroDate($task->canceledDate) ? '' : substr($task->canceledDate, 5, 11);
                break;
            case 'closedBy':
                echo zget($users, $task->closedBy);
                break;
            case 'closedDate':
                echo helper::isZeroDate($task->closedDate) ? '' : substr($task->closedDate, 5, 11);
                break;
            case 'closedReason':
                echo $this->lang->task->reasonList[$task->closedReason];
                break;
            case 'story':
                if(!empty($task->storyID))
                {
                    if(common::hasPriv('story', 'view'))
                    {
                        echo html::a(helper::createLink('story', 'view', "storyid=$task->storyID", 'html', true), "<i class='icon icon-{$this->lang->icons['story']}'></i>", '', "class='iframe' data-width='1050' title='{$task->storyTitle}'");
                    }
                    else
                    {
                        echo "<i class='icon icon-{$this->lang->icons['story']}' title='{$task->storyTitle}'></i>";
                    }
                }
                break;
            case 'mailto':
                $mailto = explode(',', $task->mailto);
                foreach($mailto as $account)
                {
                    $account = trim($account);
                    if(empty($account)) continue;
                    echo zget($users, $account) . ' &nbsp;';
                }
                break;
            case 'lastEditedBy':
                echo zget($users, $task->lastEditedBy);
                break;
            case 'lastEditedDate':
                echo helper::isZeroDate($task->lastEditedDate) ? '' : substr($task->lastEditedDate, 5, 11);
                break;
            case 'activatedDate':
                echo helper::isZeroDate($task->activatedDate) ? '' : substr($task->activatedDate, 5, 11);
                break;
            case 'actions':
                echo $this->buildOperateMenu($task, 'browse');
                break;
            default:
                echo '';
                break;
            }
            echo '</td>';
        }
    }

    /**
     * Print assigned html
     *
     * @param  object $task
     * @param  array  $users
     * @access public
     * @return void
     */
    public function printAssignedHtml($task, $users)
    {
        $btnTextClass   = '';
        $btnClass       = '';
        $assignedToText = $assignedToTitle = zget($users, $task->assignedTo);
        if(!empty($task->team) and $task->mode == 'multi' and strpos('done,closed', $task->status) === false)
        {
            $assignedToText = $this->lang->task->team;

            $teamMembers = array();
            foreach($task->team as $teamMember)
            {
                $realname = zget($users, $teamMember->account);
                if($this->app->user->account == $teamMember->account and $teamMember->status != 'done')
                {
                    $task->assignedTo = $this->app->user->account;
                    $assignedToText   = $realname;
                }
                $teamMembers[] = $realname;
            }

            $assignedToTitle = implode($this->lang->comma, $teamMembers);
        }
        elseif(empty($task->assignedTo))
        {
            $btnClass       = $btnTextClass = 'assigned-none';
            $assignedToText = $this->lang->task->noAssigned;
        }
        if($task->assignedTo == $this->app->user->account) $btnClass = $btnTextClass = 'assigned-current';
        if(!empty($task->assignedTo) and $task->assignedTo != $this->app->user->account) $btnClass = $btnTextClass = 'assigned-other';

        $btnClass    .= $task->assignedTo == 'closed' ? ' disabled' : '';
        $btnClass    .= ' iframe btn btn-icon-left btn-sm';
        if(!empty($task->team) and $task->mode == 'multi' and strpos('done,cencel,closed', $task->status) === false)
        {
            $assignToLink = $task->assignedTo == 'closed' ? '#' : helper::createLink('task', 'manageTeam', "executionID=$task->execution&taskID=$task->id", '', true);
        }
        else
        {
            $assignToLink = $task->assignedTo == 'closed' ? '#' : helper::createLink('task', 'assignTo', "executionID=$task->execution&taskID=$task->id", '', true);
        }
        $assignToHtml = html::a($assignToLink, "<i class='icon icon-hand-right'></i> <span title='" . $assignedToTitle . "'>{$assignedToText}</span>", '', "class='$btnClass'");

        echo !common::hasPriv('task', 'assignTo', $task) ? "<span style='padding-left: 21px' class='{$btnTextClass}'>{$assignedToText}</span>" : $assignToHtml;
    }

    /**
     * Get toList and ccList.
     *
     * @param  object    $task
     * @access public
     * @return bool|array
     */
    public function getToAndCcList($task)
    {
        /* Set toList and ccList. */
        $toList         = $task->assignedTo;
        $ccList         = trim($task->mailto, ',');
        $toTeamTaskList = '';
        if($task->mode == 'multi')
        {
            $toTeamTaskList = $this->getTeamMembers($task->id);
            $toTeamTaskList = implode(',', $toTeamTaskList);
            $toList         = $toTeamTaskList;
        }

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
                $toList   = substr($ccList, 0, $commaPos);
                $ccList   = substr($ccList, $commaPos + 1);
            }
        }
        elseif(strtolower($toList) == 'closed')
        {
            $toList = $task->finishedBy;
        }

        return array($toList, $ccList);
    }

    /**
     * Get task's team member pairs.
     *
     * @param  object $task
     *
     * @access public
     * @return array
     */
    public function getMemberPairs($task)
    {
        $users   = $this->loadModel('user')->getTeamMemberPairs($task->execution, 'execution', 'nodeleted');
        $members = array('');
        foreach($task->team as $member)
        {
            if(isset($users[$member->account])) $members[$member->account] = $users[$member->account];
        }
        return $members;
    }

    /**
     * Get the users who finished the multiple task.
     *
     * @param  int          $taskID
     * @param  string|array $team
     * @access public
     * @return array
     */
    public function getFinishedUsers($taskID = 0, $team = array())
    {
        return $this->dao->select('id,account')->from(TABLE_TASKTEAM)
            ->where('task')->eq($taskID)
            ->andWhere('status')->eq('done')
            ->beginIF($team)->andWhere('account')->in($team)->fi()
            ->fetchPairs('id', 'account');
    }

    /**
     * Build nested list.
     *
     * @param  object $execution
     * @param  object $task
     * @param  bool   $isChild
     * @param  bool   $showmore
     * @access public
     * @return string
     */
    public function buildNestedList($execution, $task, $isChild = false, $showmore = false, $users = array())
    {
        $this->app->loadLang('execution');

        $today    = helper::today();
        $showmore = $showmore ? 'showmore' : '';
        $trAttrs  = "data-id='t$task->id'";

        /* Remove projectID in execution path. */
        $executionPath = $execution->path;
        $executionPath = trim($executionPath, ',');
        $executionPath = substr($executionPath, strpos($executionPath, ',') + 1);

        if(!$isChild)
        {

            $path     = ",{$executionPath},t{$task->id},";
            $trAttrs .= " data-parent='$execution->id' data-nest-parent='$execution->id' data-nest-path='$path'";
            if(empty($task->children)) $trAttrs .= " data-nested='false'";
            $trClass  = empty($task->children) ? '' : " has-nest-child";
        }
        else
        {
            $path     = ",{$executionPath},{$task->parent},t{$task->id},";
            $trClass  = 'is-nest-child no-nest';
            $trAttrs .= " data-nested='false' data-parent='t$task->parent' data-nest-parent='t$task->parent' data-nest-path='$path'";
        }

        $list  = "<tr $trAttrs class='$trClass $showmore'>";
        $list .= '<td>';
        if($task->parent > 0) $list .= '<span class="label label-badge label-light" title="' . $this->lang->task->children . '">' . $this->lang->task->childrenAB . '</span> ';
        $list .= common::hasPriv('task', 'view') ? html::a(helper::createLink('task', 'view', "id=$task->id"), $task->name, '', "style='color: $task->color'", "data-app='project'") : "<span style='color:$task->color'>$task->name</span>";
        if(!helper::isZeroDate($task->deadline) && $task->status != 'done')
        {
            $list .= strtotime($today) > strtotime($task->deadline) ? '<span class="label label-danger label-badge">' . $this->lang->execution->delayed . '</span>' : '';
        }
        $list .= '</td>';
        if($execution->stageBy == 'product' and $execution->hasProduct) $list .= '<td></td>';
        $list .= "<td class='status-{$task->status} text-center'>" . $this->processStatus('task', $task) . '</td>';
        $list .= '<td>' . zget($users, $task->assignedTo, '') . '</td>';
        $list .= helper::isZeroDate($task->estStarted) ? '<td class="c-date"></td>' : '<td class="c-date">' . $task->estStarted . '</td>';
        $list .= helper::isZeroDate($task->deadline) ? '<td class="c-date"></td>' : '<td class="c-date">' . $task->deadline . '</td>';
        $list .= '<td class="hours text-right">' . $task->estimate . $this->lang->execution->workHourUnit . '</td>';
        $list .= '<td class="hours text-right">' . $task->consumed . $this->lang->execution->workHourUnit . '</td>';
        $list .= '<td class="hours text-right">' . $task->left . $this->lang->execution->workHourUnit . '</td>';
        $list .= '<td></td>';
        $list .= '<td></td>';
        $list .= '<td class="c-actions">';
        $list .= $this->buildOperateMenu($task, 'browse');
        $list .= '</td></tr>';

        if(!empty($task->children))
        {
            foreach($task->children as $child)
            {
                $showmore = (count($task->children) == 50) && ($child == end($task->children));
                $list .= $this->buildNestedList($execution, $child, true, $showmore, $users);
            }
        }

        return $list;
    }

    /**
     * Build task menu.
     *
     * @param  object $task
     * @param  string $type
     * @access public
     * @return string
     */
    public function buildOperateMenu($task, $type = 'view')
    {
        $function = 'buildOperate' . ucfirst($type) . 'Menu';
        return $this->$function($task);
    }

    /**
     * Build task view menu.
     *
     * @param  object $task
     * @access public
     * @return string
     */
    public function buildOperateViewMenu($task)
    {
        if($task->deleted) return '';

        $menu   = '';
        $params = "taskID=$task->id";
        if((empty($task->team) || empty($task->children)) && $task->executionList->type != 'kanban')
        {
            $menu .= $this->buildMenu('task', 'batchCreate', "execution=$task->execution&storyID=$task->story&moduleID=$task->module&taskID=$task->id", $task, 'view', 'split', '', '', '', "title='{$this->lang->task->children}'", $this->lang->task->children);
        }

        $menu .= $this->buildMenu('task', 'assignTo', "executionID=$task->execution&taskID=$task->id", $task, 'button', '', '', 'iframe', true, '', $this->lang->task->assignTo);

        $menu .= $this->buildMenu('task', 'start',          $params, $task, 'view', '', '', 'iframe showinonlybody', true);
        $menu .= $this->buildMenu('task', 'restart',        $params, $task, 'view', '', '', 'iframe showinonlybody', true);
        $menu .= $this->buildMenu('task', 'recordWorkhour', $params, $task, 'view', '', '', 'iframe showinonlybody', true);
        $menu .= $this->buildMenu('task', 'pause',          $params, $task, 'view', '', '', 'iframe showinonlybody', true);
        $menu .= $this->buildMenu('task', 'finish',         $params, $task, 'view', '', '', 'iframe showinonlybody text-success', true);
        $menu .= $this->buildMenu('task', 'activate',       $params, $task, 'view', '', '', 'iframe showinonlybody text-success', true);
        $menu .= $this->buildMenu('task', 'close',          $params, $task, 'view', '', '', 'iframe showinonlybody', true);
        $menu .= $this->buildMenu('task', 'cancel',         $params, $task, 'view', '', '', 'iframe showinonlybody', true);

        $menu .= "<div class='divider'></div>";
        $menu .= $this->buildFlowMenu('task', $task, 'view', 'direct');
        $menu .= "<div class='divider'></div>";

        $menu .= $this->buildMenu('task', 'edit', $params, $task, 'view', '', '', 'showinonlybody');
        $menu .= $this->buildMenu('task', 'create', "projctID={$task->execution}&storyID=0&moduleID=0&taskID=$task->id", $task, 'view', 'copy');
        $menu .= $this->buildMenu('task', 'delete', "executionID=$task->execution&taskID=$task->id", $task, 'view', 'trash', 'hiddenwin', 'showinonlybody', true);
        if($task->parent > 0) $menu .= $this->buildMenu('task', 'view', "taskID=$task->parent", $task, 'view', 'chevron-double-up', '', '', '', '', $this->lang->task->parent);

        return $menu;
    }

    /**
     * Build task browse action menu.
     *
     * @param  object $task
     * @access public
     * @return string
     */
    public function buildOperateBrowseMenu($task)
    {
        $menu   = '';
        $params = "taskID=$task->id";

        $storyChanged = !empty($task->storyStatus) && $task->storyStatus == 'active' && $task->latestStoryVersion > $task->storyVersion && !in_array($task->status, array('cancel', 'closed'));
        if($storyChanged) return $this->buildMenu('task', 'confirmStoryChange', $params, $task, 'browse', '', 'hiddenwin');

        $canStart          = ($task->status != 'pause' and common::hasPriv('task', 'start'));
        $canRestart        = ($task->status == 'pause' and common::hasPriv('task', 'restart'));
        $canFinish         = common::hasPriv('task', 'finish');
        $canClose          = common::hasPriv('task', 'close');
        $canRecordWorkhour = common::hasPriv('task', 'recordWorkhour');
        $canEdit           = common::hasPriv('task', 'edit');
        $canBatchCreate    = ($this->config->vision == 'rnd' and common::hasPriv('task', 'batchCreate'));

        if($task->status != 'pause') $menu .= $this->buildMenu('task', 'start',   $params, $task, 'browse', '', '', 'iframe', true);
        if($task->status == 'pause') $menu .= $this->buildMenu('task', 'restart', $params, $task, 'browse', '', '', 'iframe', true);
        $menu .= $this->buildMenu('task', 'finish',         $params, $task, 'browse', '', '', 'iframe', true);
        $menu .= $this->buildMenu('task', 'close',          $params, $task, 'browse', '', '', 'iframe', true);

        if(in_array(true, array($canStart, $canRestart, $canFinish, $canClose)) and in_array(true, array($canRecordWorkhour, $canEdit, $canBatchCreate)))
        {
            $menu .= "<div class='dividing-line'></div>";
        }

        $menu .= $this->buildMenu('task', 'recordWorkhour', $params, $task, 'browse', 'time', '', 'iframe', true);
        $menu .= $this->buildMenu('task', 'edit',           $params, $task, 'browse');
        if($this->config->vision == 'rnd')
        {
            $menu .= $this->buildMenu('task', 'batchCreate', "execution=$task->execution&storyID=$task->story&moduleID=$task->module&taskID=$task->id&ifame=0", $task, 'browse', 'split', '', '', '', '', $this->lang->task->children);
        }

        return $menu;
    }

    /**
     * Update estimate date by gantt.
     *
     * @param  int     $objectID
     * @param  string  $objectType
     * @access public
     * @return bool
     */
    public function updateEsDateByGantt($objectID, $objectType)
    {
        $this->app->loadLang('project');
        $post = fixer::input('post')->get();
        $post->endDate = date('Y-m-d', strtotime('-1 day', strtotime($post->endDate)));
        $changeTable = $objectType == 'task' ? TABLE_TASK : TABLE_PROJECT;
        $actionType  = $objectType == 'task' ? 'task' : 'execution';
        $oldObject   = $this->dao->select('*')->from($changeTable)->where('id')->eq($objectID)->fetch();
        if($objectType == 'task')
        {
            $this->taskTao->updateTaskEsDateByGantt($objectID, $post);
        }
        elseif($objectType == 'plan')
        {
            $this->updateExecutionEsDateByGantt($objectID, $post);
        }

        if(dao::isError()) return false;

        $newObject = $this->dao->select('*')->from($changeTable)->where('id')->eq($objectID)->fetch();
        $changes   = common::createChanges($oldObject, $newObject);
        $actionID  = $this->loadModel('action')->create($actionType, $objectID, 'edited');
        if(!empty($changes)) $this->loadModel('action')->logHistory($actionID, $changes);

        return true;
    }

    /**
     * Update Execution estimate date by gantt.
     *
     * @param  int     $objectID
     * @param  string  $objectType
     * @param  object  $postData
     * @access public
     * @return bool
     */
    public function updateExecutionEsDateByGantt($objectID, $postData)
    {
        $objectData = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($objectID)->fetch();
        $project    = $objectData->project;
        $parent     = '';
        if($objectData->project != $objectData->parent) $parent = $objectData->parent;

        if(empty($parent))
        {
            $parentData = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($project)->fetch();
        }
        else
        {
            $parentData = $this->dao->select('*')->from(TABLE_PROJECT)->where('id')->eq($parent)->fetch();
        }

        $start      = helper::isZeroDate($parentData->begin) ? '' : $parentData->begin;
        $end        = helper::isZeroDate($parentData->end)   ? '' : $parentData->end;

        if(helper::diffDate($start, $postData->startDate) > 0)
        {
            $arg = !empty($parent) ? $this->lang->programplan->parent : $this->lang->project->common;
            dao::$errors = sprintf($this->lang->task->overEsStartDate, $arg, $arg);
        }

        if(helper::diffDate($end, $postData->endDate) < 0)
        {
            $arg = !empty($parent) ? $this->lang->programplan->parent : $this->lang->project->common;
            return dao::$errors = sprintf($this->lang->task->overEsEndDate, $arg, $arg);
        }

        $this->dao->update(TABLE_PROJECT)
            ->set('begin')->eq($postData->startDate)
            ->set('end')->eq($postData->endDate)
            ->set('lastEditedBy')->eq($this->app->user->account)
            ->where('id')->eq($objectID)
            ->exec();
    }

    /**
     * Update order by gantt.
     *
     * @access public
     * @return void
     */
    public function updateOrderByGantt()
    {
        $data = fixer::input('post')->get();

        $order = 1;
        foreach($data->tasks as $task)
        {
            $idList = explode('-', $task);
            $taskID = $idList[1];
            $this->dao->update(TABLE_TASK)->set('`order`')->eq($order)->where('id')->eq($taskID)->exec();
            $order ++;
        }
    }

    /**
     * Get task list by conditions.
     *
     * @param  object           $conds
     * @param  string           $orderBy
     * @param  object           $pager
     * @access public
     * @return array
     */
    public function getListByConds($conds, $orderBy = 'id_desc', $pager = null)
    {
        foreach(array('priList' => array(), 'assignedToList' => array(), 'statusList' => array(), 'idList' => array(), 'taskName' => '') as $condKey => $defaultValue)
        {
            if(!isset($conds->$condKey))
            {
                $conds->$condKey = $defaultValue;
                continue;
            }
            if(strripos($condKey, 'list') === strlen($condKey) - 4 && !is_array($conds->$condKey)) $conds->$condKey = array_filter(explode(',', $conds->$condKey));
        }

        return $this->dao->select('*')->from(TABLE_TASK)
            ->where('deleted')->eq(0)
            ->beginIF(!empty($conds->priList))->andWhere('pri')->in($conds->priList)->fi()
            ->beginIF(!empty($conds->assignedToList))->andWhere('assignedTo')->in($conds->assignedToList)->fi()
            ->beginIF(!empty($conds->statusList))->andWhere('status')->in($conds->statusList)->fi()
            ->beginIF(!empty($conds->idList))->andWhere('id')->in($conds->idList)->fi()
            ->beginIF(!empty($conds->taskName))->andWhere('name')->like("%{$conds->taskName}%")
            ->beginIF(!$this->app->user->admin)->andWhere('execution')->in($this->app->user->view->sprints)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get teamTask members.
     *
     * @param  int    $taskID
     * @access public
     * @return array
     */
    public function getTeamMembers($taskID)
    {
        $taskType = $this->dao->select('mode')->from(TABLE_TASK)->where('id')->eq($taskID)->fetch('mode');
        if($taskType != 'multi') return array();
        $teamMembers = $this->dao->select('account')->from(TABLE_TASKTEAM)->where('task')->eq($taskID)->fetchPairs();
        return empty($teamMembers) ? $teamMembers : array_keys($teamMembers);
    }

    /**
     * Check estStarted and deadline date.
     *
     * @param  int    $executionID
     * @param  string $estStarted
     * @param  string $deadline
     * @param  string $pre
     * @access public
     * @return void
     */
    public function checkEstStartedAndDeadline($executionID, $estStarted, $deadline, $pre = '')
    {
        $execution = $this->loadModel('execution')->getByID($executionID);
        if(empty($execution) || empty($this->config->limitTaskDate)) return false;
        if(empty($execution->multiple)) $this->lang->execution->common = $this->lang->project->common;

        if(!empty($estStarted) && !helper::isZeroDate($estStarted))
        {
            if($estStarted < $execution->begin) dao::$errors['estStarted'][] = $pre . sprintf($this->lang->task->error->beginLtExecution, $this->lang->execution->common, $execution->begin);
            if($estStarted > $execution->end)   dao::$errors['estStarted'][] = $pre . sprintf($this->lang->task->error->beginGtExecution, $this->lang->execution->common, $execution->end);
        }

        if(!empty($deadline) && !helper::isZeroDate($deadline))
        {
            if($deadline > $execution->end)   dao::$errors['deadline'][] = $pre . sprintf($this->lang->task->error->endGtExecution, $this->lang->execution->common, $execution->end);
            if($deadline < $execution->begin) dao::$errors['deadline'][] = $pre . sprintf($this->lang->task->error->endLtExecution, $this->lang->execution->common, $execution->begin);
        }
    }

    /**
     * 创建任务后的其他数据处理。
     * Other data process after task create.
     *
     * @param  object $task
     * @param  array  $taskIdList
     * @param  int    $bugID
     * @param  int    $todoID
     * @access public
     * @return bool
     */
    public function afterCreate(object $task, array $taskIdList, int $bugID, int $todoID): bool
    {
        $this->loadModel('file');

        $this->setTaskFiles($taskIdList); // Set attachments for tasks.
        foreach($taskIdList as $taskID)
        {
            /* If the task comes from a bug, update the task and bug information. */
            if($bugID > 0)
            {
                $this->dao->update(TABLE_TASK)->set('fromBug')->eq($bugID)->where('id')->eq($taskID)->exec();
                $this->dao->update(TABLE_BUG)->set('toTask')->eq($taskID)->where('id')->eq($bugID)->exec();
                $this->loadModel('action')->create('bug', $bugID, 'converttotask', '', $taskID);
            }

            /* If the task comes from a todo, update the todo information. */
            if($todoID > 0)
            {
                $this->dao->update(TABLE_TODO)->set('status')->eq('done')->where('id')->eq($todoID)->exec();
                $this->loadModel('action')->create('todo', $todoID, 'finished', '', "TASK:$taskID");

                /* If the todo comes from a feedback, update the feedback information. */
                $todo = $this->dao->findByID($todoID)->from(TABLE_TODO)->fetch();
                if($this->config->edition != 'open' && $todo->type == 'feedback' && $todo->objectID) $this->loadModel('feedback')->updateStatus('todo', $todo->idvalue, 'done');
            }

            /* If the task comes from a design, update the task information. */
            if(!empty($task->design))
            {
                $design = $this->loadModel('design')->getByID($task->design);
                $this->dao->update(TABLE_TASK)->set('designVersion')->eq($design->version)->where('id')->eq($taskID)->exec();
            }

            /* If the task comes from a story, update the stage of the story. */
            if($task->story) $this->loadModel('story')->setStage($task->story);
        }
        return !dao::isError();
    }

    /**
     * 编辑任务后的其他数据处理:记录分数、更改需求阶段、处理父任务变更、更改反馈状态等。
     * Additional data processing after updating tasks: record scores, change story stage, handle parent task changes, change feedback status.
     *
     * @param  object $oldTask
     * @param  object $task
     * @access public
     * @return void
     */
    public function afterUpdate(object $oldTask, object $task): void
    {
        /* Multi-task change to normal task. */
        if($task->mode == 'single') $this->dao->delete()->from(TABLE_TASKTEAM)->where('task')->eq($task->id)->exec();

        if(isset($task->version) && $task->version > $oldTask->version) $this->taskTao->recordTaskVersion($task);

        /* Compute task's story stage. */
        $this->loadModel('story')->setStage($task->story);
        if($task->story != $oldTask->story) $this->story->setStage($oldTask->story);

        /* Create score. */
        if($task->status == 'done')   $this->loadModel('score')->create('task', 'finish', $task->id);
        if($task->status == 'closed') $this->loadModel('score')->create('task', 'close', $task->id);

        if($task->status != $oldTask->status) $this->loadModel('kanban')->updateLane($task->execution, 'task', $task->id);

        $isParentChanged = $task->parent != $oldTask->parent;

        /* If there is a parent task before updating the task, update the parent. */
        if($oldTask->parent > 0)
        {
            $oldParentTask = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($oldTask->parent)->fetch();
            $this->updateParentStatus($task->id, $oldTask->parent, !$isParentChanged);
            $this->computeBeginAndEnd($oldTask->parent);

            if($isParentChanged)
            {
                $oldChildCount = $this->dao->select('count(*) as count')->from(TABLE_TASK)->where('parent')->eq($oldTask->parent)->fetch('count');
                if(!$oldChildCount) $this->dao->update(TABLE_TASK)->set('parent')->eq(0)->where('id')->eq($oldTask->parent)->exec();
                $this->dao->update(TABLE_TASK)->set('lastEditedBy')->eq($this->app->user->account)->set('lastEditedDate')->eq(helper::now())->where('id')->eq($oldTask->parent)->exec();
                $this->loadModel('action')->create('task', $task->id, 'unlinkParentTask', '', $oldTask->parent, '', false);

                $actionID = $this->action->create('task', $oldTask->parent, 'unLinkChildrenTask', '', $task->id, '', false);

                $newParentTask = $this->dao->select('*')->from(TABLE_TASK)->where('id')->eq($oldTask->parent)->fetch();

                $changes = common::createChanges($oldParentTask, $newParentTask);
                if(!empty($changes)) $this->action->logHistory($actionID, $changes);
            }
        }

        if(!empty($task->parent)) $this->updateParent($task, $isParentChanged);
        if($this->config->edition != 'open' && $oldTask->feedback) $this->loadModel('feedback')->updateStatus('task', $oldTask->feedback, $task->status, $oldTask->status);
    }

    /**
     * 拆分任务后更新其他数据。
     * Process other data after split task.
     *
     * @param  object $oldParentTask
     * @param  array  $childrenIdList
     * @access public
     * @return bool
     */
    public function afterSplitTask(object $oldParentTask, array $childrenIdList = array()): bool
    {
        $parentID = (int)$oldParentTask->id;

        /* When a normal task is consumed, create the subtask and update the parent task status. */
        if($oldParentTask->parent == 0 && $oldParentTask->consumed > 0)
        {
            $this->taskTao->copyTaskData($oldParentTask);
            if(dao::isError()) return false;
        }

        $parentTask = new stdclass();
        $parentTask->parent         = '-1';
        $parentTask->lastEditedBy   = $this->app->user->account;
        $parentTask->lastEditedDate = helper::now();
        $this->dao->update(TABLE_TASK)->data($parentTask)->where('id')->eq($parentID)->exec();

        $this->updateParentStatus(current($childrenIdList));
        $this->computeBeginAndEnd($parentID);

        /* Create a action. */
        $extra    = implode(',', $childrenIdList);
        $actionID = $this->action->create('task', $parentID, 'createChildren', '', trim($extra, ','));

        /* Create a log history. */
        $newParentTask = $this->getByID($parentID);
        $changes       = common::createChanges($oldParentTask, $newParentTask);
        if(!empty($changes)) $this->action->logHistory($actionID, $changes);

        return !dao::isError();
    }

    /**
     * 批量创建任务后的看板数据处理。
     * Kanban data processing after batch create tasks.
     *
     * @param  int    $taskID
     * @param  int    $executionID
     * @param  int    $laneID
     * @param  int    $columnID
     * @access public
     * @return bool
     */
    public function updateKanbanForBatchCreate(int $taskID, int $executionID, int $laneID, int $columnID): bool
    {
        $this->loadModel('kanban');

        if($this->config->vision == 'lite')
        {
            $this->kanban->addKanbanCell($executionID, $laneID, $columnID, 'task', $taskID);
        }
        else
        {
            $columnID = $this->kanban->getColumnIDByLaneID($laneID, 'wait');
            if(!empty($laneID) && !empty($columnID)) $this->kanban->addKanbanCell($executionID, $laneID, $columnID, 'task', $taskID);
        }
        return !dao::isError();
    }

    /**
     * 更新看板中的任务泳道数据。
     * Update the task lane data in Kanban.
     *
     * @param  int    $executionID
     * @param  array  $taskIdList
     * @param  int    $laneID
     * @param  int    $oldColumnID
     * @access public
     * @return bool
     */
    public function updateKanbanData(int $executionID, array $taskIdList, int $laneID, int $oldColumnID): bool
    {
        if(!$executionID) return false;

        $this->loadModel('kanban');

        /* Get kanban id, lane id and column id. */
        $laneID   = !empty($laneID) ? $laneID : 0;
        $columnID = $this->kanban->getColumnIDByLaneID($laneID, 'wait');
        if(empty($columnID)) $columnID = $oldColumnID;

        /* If both of lane id and column id are not empty, add task to the kanban cell. */
        if($laneID && $columnID)
        {
            foreach($taskIdList as $taskID) $this->kanban->addKanbanCell($executionID, $laneID, $columnID, 'task', $taskID);
        }

        /* If lane id or column id is empty, update the task type lane of the kanban. */
        if(!$laneID || !$columnID) $this->kanban->updateLane($executionID, 'task');

        return true;
    }

    /**
     * 设置任务的附件。
     * Set attachments for tasks.
     *
     * @param  int|array $taskIdList
     * @access public
     * @return bool
     */
    public function setTaskFiles(array $taskIdList): bool
    {
        if(empty($taskIdList)) return true;

        $taskID        = array_shift($taskIdList);
        $taskFilePairs = $this->loadModel('file')->saveUpload('task', $taskID);
        if(!empty($taskIdList))
        {
            $taskFiles = $taskFilePairs ? $this->dao->select('*')->from(TABLE_FILE)->where('id')->in(array_keys($taskFilePairs))->fetchAll('id') : array();
            foreach($taskIdList as $objectID)
            {
                foreach($taskFiles as $taskFile)
                {
                    $taskFile->objectID = $objectID;
                    $this->dao->insert(TABLE_FILE)->data($taskFile)->exec();
                }
            }
        }
        return !dao::isError();
    }

    /**
     * 创建关联需求的测试类型的子任务。
     * Create a subtask for the test type story with the story.
     *
     * @param  int      $taskID
     * @param  object[] $testTasks
     * @access public
     * @return void
     */
    public function createTestChildTasks(int $taskID, array $testTasks): void
    {
        $this->loadModel('action');

        /* Get the stories of the test tasks. */
        $testStoryIdList = array_keys($testTasks);
        $testStories     = $this->dao->select('id,title,version,module')->from(TABLE_STORY)->where('id')->in($testStoryIdList)->fetchAll('id');
        foreach($testStoryIdList as $storyID)
        {
            /* If the story id is not exist, skip it. */
            if(!isset($testStories[$storyID])) continue;

            /* Construct a task and create it. */
            $task               = $testTasks[$storyID];
            $task->parent       = $taskID;
            $task->storyVersion = $testStories[$storyID]->version;
            $task->name         = $this->lang->task->lblTestStory . " #{$storyID} " . $testStories[$storyID]->title;
            $task->module       = $testStories[$storyID]->module;
            $this->dao->insert(TABLE_TASK)->data($task)->exec();

            /* Get task id and create a action. */
            $childTaskID = $this->dao->lastInsertID();
            $this->action->create('task', $childTaskID, 'Opened');
        }
    }

    /**
     * 更新看板单元格。
     * Update kanban cell.
     *
     * @param  int    $taskID
     * @param  array  $output
     * @param  int    $executionID
     * @access public
     * @return void
     */
    public function updateKanbanCell(int $taskID, array $output, int $executionID): void
    {
        if(!isset($output['toColID'])) $this->loadModel('kanban')->updateLane($executionID, 'task', $taskID);
        if(isset($output['toColID'])) $this->loadModel('kanban')->moveCard($taskID, $output['fromColID'], $output['toColID'], $output['fromLaneID'], $output['toLaneID']);
    }

    /**
     * 获取多人串行任务的指派人。
     * Get the assignedTo for the multiply linear task.
     *
     * @param  string|array $members
     * @param  object       $task
     * @param  string       $type    current|next
     * @access public
     * @return string
     */
    public function getAssignedTo4Multi(string|array $members, object $task, string $type = 'current'): string
    {
        if(empty($task->team) || $task->mode != 'linear') return $task->assignedTo;

        /* Format task team members. */
        if(!is_array($members)) $members = explode(',', trim($members, ','));
        $members = array_values($members);
        if(is_object($members[0])) $members = array_column($members, 'account');

        /* Get the member of the first unfinished task. */
        $teamHours = array_values($task->team);
        foreach($members as $i => $account)
        {
            if(isset($teamHours[$i]) && $teamHours[$i]->status == 'done') continue;
            if($type == 'current') return $account;
            break;
        }

        /* Get the member of the second unfinished task. */
        if($type == 'next' && isset($members[$i + 1])) return $members[$i + 1];

        return $task->openedBy;
    }

    /**
     * 检查执行是否有需求列表。
     * Check whether execution has story list.
     *
     * @param  object    $execution
     * @access public
     * @return bool
     */
    public function isNoStoryExecution(object $execution): bool
    {
        if(empty($execution)) return false;
        return $execution->lifetime == 'ops' || in_array($execution->attribute, array('request', 'review'));
    }

    /**
     * 处理状态变化之后的操作。
     * Process other data after task status changed.
     *
     * @param  object $task
     * @param  array  $changes
     * @param  string $action  Finished|Closed|Started
     * @param  array  $output
     * @access public
     * @return bool
     */
    public function afterChangeStatus(object $task, array $changes, string $action, array $output): bool
    {
        /* Process other data. */
        if($task->parent > 0) $this->updateParentStatus($task->id);
        if($task->story) $this->loadModel('story')->setStage($task->story);
        if($this->config->edition != 'open' && $task->feedback) $this->loadModel('feedback')->updateStatus('task', $task->feedback, $task->status, $task->status);

        $this->updateKanbanCell($task->id, $output, $task->execution);

        $files = $this->loadModel('file')->saveUpload('task', $task->id);
        if($changes || $this->post->comment)
        {
            $fileAction = !empty($files) ? $this->lang->addFiles . implode(',', $files) . "\n" : '';
            $actionID   = $this->loadModel('action')->create('task', $task->id, $action, $fileAction . $this->post->comment);
            $this->action->logHistory($actionID, $changes);
        }
        return !dao::isError();
    }
}
