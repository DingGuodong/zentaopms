<?php
class programModel extends model
{
    /**
     * Show accessDenied response.
     *
     * @access private
     * @return void
     */
    public function accessDenied()
    {
        echo(js::alert($this->lang->program->accessDenied));

        if(!$this->server->http_referer) return print(js::locate(helper::createLink('my', 'index')));

        $loginLink = $this->config->requestType == 'GET' ? "?{$this->config->moduleVar}=user&{$this->config->methodVar}=login" : "user{$this->config->requestFix}login";
        if(strpos($this->server->http_referer, $loginLink) !== false) return print(js::locate(helper::createLink('my', 'index')));

        echo js::locate('back');
    }

    /**
     * Save program state.
     *
     * @param  int    $programID
     * @param  array  $programs
     * @access public
     * @return int
     */
    public function saveState($programID = 0, $programs = array())
    {
        if($programID > 0) $this->session->set('program', (int)$programID);
        if($programID == 0 and $this->cookie->lastProgram) $this->session->set('program', (int)$this->cookie->lastProgram);
        if($programID == 0 and $this->session->program == '') $this->session->set('program', key($programs));
        if(!isset($programs[$this->session->program]))
        {
            $this->session->set('program', key($programs));
            if($programID && strpos(",{$this->app->user->view->programs},", ",{$this->session->program},") === false) $this->accessDenied();
        }

        return $this->session->program;
    }

    /**
     * Get program pairs.
     *
     * @param  bool   $isQueryAll
     * @param  string $orderBy
     * @access public
     * @return array
     */
    public function getPairs($isQueryAll = false, $orderBy = 'id_desc')
    {
        return $this->dao->select('id, name')->from(TABLE_PROGRAM)
            ->where('type')->eq('program')
            ->andWhere('deleted')->eq(0)
            ->beginIF(!$this->app->user->admin and !$isQueryAll)->andWhere('id')->in($this->app->user->view->programs)->fi()
            ->orderBy($orderBy)
            ->fetchPairs();
    }

    /**
     * Get the product associated with the program.
     *
     * @param  int          $programID
     * @param  string       $mode       all|assign
     * @param  string       $status     all|noclosed
     * @param  string|array $append
     * @access public
     * @return array
     */
    public function getProductPairs($programID = 0, $mode = 'assign', $status = 'all', $append = '')
    {
        /* Get the top programID. */
        if($programID)
        {
            $program   = $this->getByID($programID);
            $path      = explode(',', $program->path);
            $path      = array_filter($path);
            $programID = current($path);
        }

        /* When mode equals assign and programID equals 0, you can query the standalone product. */
        if(!empty($append) and is_array($append)) $append = implode(',', $append);

        $views    = empty($append) ? $this->app->user->view->products : $this->app->user->view->products . ",$append";
        $products = $this->dao->select('*')->from(TABLE_PRODUCT)
            ->where('deleted')->eq(0)
            ->andWhere('vision')->eq($this->config->vision)
            ->beginIF($mode == 'assign')->andWhere('program')->eq($programID)->fi()
            ->beginIF(strpos($status, 'noclosed') !== false)->andWhere('status')->ne('closed')->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($views)->fi()
            ->fetchPairs('id', 'name');
        return $products;
    }

    /**
     * Get program by id.
     *
     * @param  int  $programID
     * @access public
     * @return array
     */
    public function getByID($programID = 0)
    {
        $program = $this->dao->select('*')->from(TABLE_PROGRAM)->where('id')->eq($programID)->fetch();
        $program = $this->loadModel('file')->replaceImgURL($program, 'desc');
        return $program;
    }

    /**
     * Get program pairs by id list.
     *
     * @param  string|array $programIDList
     * @access public
     * @return array
     */
    public function getPairsByList($programIDList = '')
    {
        return $this->dao->select('id, name')->from(TABLE_PROGRAM)
            ->where('id')->in($programIDList)
            ->andWhere('`type`')->eq('program')
            ->andWhere('deleted')->eq(0)
            ->fetchPairs();
    }

    /**
     * Get program list.
     *
     * @param  string $status
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $type       top|child
     * @param  array  $topIdList
     * @access public
     * @return array
     */
    public function getList($status = 'all', $orderBy = 'id_asc', $pager = NULL, $type = '', $topIdList = array())
    {
        $userViewIdList = trim($this->app->user->view->programs, ',') . ',' . trim($this->app->user->view->projects, ',');
        $userViewIdList = array_filter(explode(',', $userViewIdList));

        $objectIdList = array();
        if($this->app->rawMethod == 'browse')
        {
            $pathList = $this->dao->select('id,path')->from(TABLE_PROJECT)
                 ->where('type')->in('program,project')
                 ->beginIF(!$this->app->user->admin)->andWhere('id')->in($userViewIdList)->fi()
                 ->andWhere('deleted')->eq(0)
                 ->orderBy('id_asc')
                 ->fetchPairs('id');

            foreach($pathList as $path)
            {
                if($type == 'child' and !empty($topIdList))
                {
                    $topID = $this->getTopByPath($path);
                    if(!in_array($topID, $topIdList)) continue;
                }

                foreach(explode(',', trim($path, ',')) as $pathID) $objectIdList[$pathID] = $pathID;
            }
        }

        return $this->dao->select('*')->from(TABLE_PROGRAM)
            ->where('type')->in('program,project')
            ->andWhere('deleted')->eq(0)
            ->andWhere('vision')->eq($this->config->vision)
            ->beginIF($status != 'all')->andWhere('status')->in($status)->fi()
            ->beginIF($this->app->rawMethod == 'browse' and $type === 'top')->andWhere('parent')->eq(0)->fi()
            ->beginIF($this->app->rawMethod == 'browse' and ($type === 'child' or !$this->app->user->admin))->andWhere('id')->in($objectIdList)->fi()
            ->beginIF(!$this->app->user->admin and $this->app->rawMethod != 'browse')->andWhere('id')->in($userViewIdList)->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * Get program list by search.
     *
     * @param string $orderBy
     * @param int $queryID
     * @access public
     * @return void
     */
    public function getListBySearch($orderBy = 'id_asc', $queryID = 0)
    {
        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('programQuery', $query->sql);
                $this->session->set('programForm', $query->form);
            }
            else
            {
                $this->session->set('programQuery', ' 1 = 1');
            }
        }
        else
        {
            if($this->session->programQuery == false) $this->session->set('programQuery', ' 1 = 1');
        }
        $query = $this->session->programQuery;

        $objectIdList = array();
        if(!$this->app->user->admin)
        {
            $objectIdList = trim($this->app->user->view->programs, ',') . ',' . trim($this->app->user->view->projects, ',');
            $objectIdList = array_filter(explode(',', $objectIdList));
            asort($objectIdList);

            if($this->app->rawMethod == 'browse')
            {
                $pathList = $this->dao->select('id,path')->from(TABLE_PROJECT)->where('id')->in($objectIdList)->andWhere('deleted')->eq(0)->fetchPairs('id');
                foreach($pathList as $path)
                {
                    foreach(explode(',', trim($path, ',')) as $pathID) $objectIdList[$pathID] = $pathID;
                }
            }
        }

        $programs = $this->dao->select('*')->from(TABLE_PROGRAM)
            ->where('deleted')->eq(0)
            ->andWhere('vision')->eq($this->config->vision)
            ->andWhere('type')->eq('program')
            ->beginIF($query)->andWhere($query)->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($objectIdList)->fi()
            ->orderBy($orderBy)
            ->fetchAll('id');

        return $programs;
    }

    /**
     * Get kanban group data.
     *
     * @access public
     * @return array
     */
    public function getKanbanGroup()
    {
        $kanbanGroup           = array();
        $kanbanGroup['my']     = array();
        $kanbanGroup['others'] = array();

        $programs         = $this->getTopPairs('', 'noclosed');
        $involvedPrograms = $this->getInvolvedPrograms($this->app->user->account);

        /* Get all products under programs. */
        $productGroup = $this->dao->select('id, program, name')->from(TABLE_PRODUCT)
            ->where('deleted')->eq(0)
            ->andWhere('program')->in(array_keys($programs))
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->products)->fi()
            ->andWhere('status')->ne('closed')
            ->orderBy('order_asc')
            ->fetchGroup('program');

        $productPairs = array();
        foreach($productGroup as $programID => $products)
        {
            foreach($products as $product) $productPairs[$product->id] = $product->id;
        }

        /* Get all plans under products. */
        $plans = $this->dao->select('id, product, title')->from(TABLE_PRODUCTPLAN)
            ->where('deleted')->eq(0)
            ->andWhere('product')->in($productPairs)
            ->andWhere('end')->ge(helper::today())
            ->fetchGroup('product');

        /* Get all products linked projects. */
        $projectGroup = $this->dao->select('DISTINCT t1.product, t2.id, t2.name, t2.status, t2.end')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')
            ->on('t1.project = t2.id')
            ->where('t2.deleted')->eq(0)
            ->andWhere('t1.product')->in($productPairs)
            ->andWhere('t2.type')->eq('project')
            ->andWhere('t2.status')->in('wait,doing')
            ->beginIF(!$this->app->user->admin)->andWhere('t2.id')->in($this->app->user->view->projects)->fi()
            ->fetchGroup('product');

        $projectPairs = array();
        foreach($projectGroup as $projects) $projectPairs = array_merge($projectPairs, array_keys($projects));

        /* Get all releases under products. */
        $releases = $this->dao->select('product, id, name, marker')->from(TABLE_RELEASE)
            ->where('product')->in($productPairs)
            ->andWhere('deleted')->eq(0)
            ->andWhere('status')->eq('normal')
            ->fetchGroup('product');

        /* Get doing executions. */
        $doingExecutions = $this->dao->select('id, project, name, end')->from(TABLE_EXECUTION)
            ->where('type')->in('sprint,stage,kanban')
            ->andWhere('status')->eq('doing')
            ->andWhere('deleted')->eq(0)
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->sprints)->fi()
            ->orderBy('id_asc')
            ->fetchAll('project');

        $executionPairs = array();
        foreach($doingExecutions as $execution) $executionPairs[$execution->id] = $execution->id;

        /* Compute executions and projects progress. */
        $tasks = $this->dao->select('id, project, estimate, consumed, `left`, status, closedReason, execution')
            ->from(TABLE_TASK)
            ->where('(execution')->in($executionPairs)
            ->orWhere('project')->in($projectPairs)
            ->markRight(1)
            ->andWhere('parent')->lt(1)
            ->andWhere('deleted')->eq(0)
            ->fetchGroup('execution', 'id');

        $hours        = $this->computeProgress($tasks);
        $projectHours = $this->getProgressList();

        /* Group data by product. */
        foreach($productGroup as $programID => $products)
        {
            foreach($products as $product)
            {
                $product->plans    = zget($plans, $product->id, array());

                /* Convert predefined HTML entities to characters. */
                !empty($product->plans) && array_map(function($planVal)
                {
                    return $planVal->title = htmlspecialchars_decode($planVal->title, ENT_QUOTES);
                },
                $product->plans);
                $product->name = htmlspecialchars_decode($product->name, ENT_QUOTES);

                $product->releases = zget($releases, $product->id, array());
                $projects          = zget($projectGroup, $product->id, array());
                foreach($projects as $project)
                {
                    if(helper::diffDate(helper::today(), $project->end) > 0) $project->delay = 1;

                    $status    = $project->status == 'wait' ? 'wait' : 'doing';
                    $execution = zget($doingExecutions, $project->id, array());

                    if(!empty($execution))
                    {
                        $execution->hours = zget($hours, $execution->id, array());
                        if(helper::diffDate(helper::today(), $execution->end) > 0) $execution->delay = 1;
                    }

                    $project->execution = $execution;
                    $project->hours['progress']   = zget($projectHours, $project->id, array());

                    /* Convert predefined HTML entities to characters. */
                    $project->name = htmlspecialchars_decode($project->name, ENT_QUOTES);
                    $product->projects[$status][] = $project;
                }
            }
        }

        /* Group data by program. */
        foreach($programs as $programID => $programName)
        {
            $programGroup = new stdclass();
            $programGroup->name     = $programName;
            $programGroup->products = zget($productGroup, $programID, array());

            if(in_array($programID, $involvedPrograms))
            {
                $kanbanGroup['my'][] = $programGroup;
            }
            else
            {
                $kanbanGroup['others'][] = $programGroup;
            }
        }

        return $kanbanGroup;
    }

    /**
     * Get involved programs by user.
     *
     * @param  string $account
     * @access public
     * @return array
     */
    public function getInvolvedPrograms($account)
    {
        $involvedPrograms = array();

        /* All objects in program table. */
        $objects = $this->dao->select('id,type,project,parent,path,openedBy,PM')->from(TABLE_PROGRAM)->where('deleted')->eq(0)->fetchAll('id');

        foreach($objects as $id => $object)
        {
            if($object->openedBy != $account and $object->PM != $account) continue;

            if($object->type == 'program') $involvedPrograms[$id] = $id;
            if($object->type == 'project')
            {
                $programID = $this->getTopByPath($object->path);
                $involvedPrograms[$programID] = $programID;
            }
            if($object->type == 'sprint' or $object->type == 'stage')
            {
                $parentProject = zget($objects, $object->parent, array());
                if(!$parentProject) continue;

                $programID = $this->getTopByPath($parentProject->path);
                $involvedPrograms[$programID] = $programID;
            }
        }

        /* All involves in stakeholder table. */
        $stakeholders = $this->dao->select('t1.objectID, t2.type')->from(TABLE_STAKEHOLDER)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')
            ->on('t1.objectID = t2.id')
            ->where('t1.objectType')->in("program,project")
            ->andWhere('t1.user')->eq($account)
            ->fetchAll('objectID');

        foreach($stakeholders as $objectID => $object)
        {
            if($object->type == 'program')
            {
                $involvedPrograms[$objectID] = $objectID;
            }
            if($object->type == 'project')
            {
                $project = zget($objects, $objectID, array());
                if(!$project) continue;

                $programID = $this->getTopByPath($project->path);
                $involvedPrograms[$programID] = $programID;
            }
        }

        /* All involves in team table. */
        $teams = $this->dao->select('t1.root, t2.project, t2.type')->from(TABLE_TEAM)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')
            ->on('t1.root = t2.id')
            ->where('t1.account')->eq($account)
            ->andWhere('t1.type')->in('project,execution')
            ->fetchAll('root');

        foreach($teams as $objectID => $object)
        {
            if($object->type == 'project')
            {
                $project = zget($objects, $objectID, array());
                if(!$project) continue;

                $programID = $this->getTopByPath($project->path);
                $involvedPrograms[$programID] = $programID;
            }
            if($object->type == 'sprint' or $object->type == 'stage')
            {
                $execution = zget($objects, $objectID, array());
                if(!$execution) continue;

                $project   = zget($objects, $execution->parent, array());
                if(!$project) continue;

                $programID = $this->getTopByPath($project->path);
                $involvedPrograms[$programID] = $programID;
            }
        }

        /* All involves in products table. */
        $products = $this->dao->select('id, program, createdBy, PO, QD, RD')->from(TABLE_PRODUCT)
            ->where('deleted')->eq(0)
            ->andWhere("(createdBy = '$account' or PO = '$account' or QD = '$account' or RD = '$account')")
            ->fetchAll('id');

        foreach($products as $id => $product) $involvedPrograms[$product->program] = $product->program;

        /* Check priv. */
        $involvedPrograms = $this->dao->select('id')->from(TABLE_PROGRAM)
            ->where('deleted')->eq(0)
            ->beginIF(!$this->app->user->admin)->andWhere('id')->in($this->app->user->view->programs)->fi()
            ->andWhere('id')->in($involvedPrograms)
            ->andWhere('grade')->eq(1)
            ->fetchPairs();

        return $involvedPrograms;
    }

    /**
     * Compute progress for project or execution.
     *
     * @param  array $tasks
     * @access public
     * @return void
     */
    public function computeProgress($tasks)
    {
        $hours = array();
        foreach($tasks as $projectID => $projectTasks)
        {
            $hour = new stdclass();
            $hour->totalConsumed = 0;
            $hour->totalEstimate = 0;
            $hour->totalLeft     = 0;

            foreach($projectTasks as $task)
            {
                $hour->totalConsumed += $task->consumed;
                $hour->totalEstimate += $task->estimate;
                if($task->status != 'cancel' and $task->status != 'closed') $hour->totalLeft += $task->left;
            }
            $hours[$projectID] = $hour;
        }

        foreach($hours as $hour)
        {
            $hour->totalEstimate = round($hour->totalEstimate, 1) ;
            $hour->totalConsumed = round($hour->totalConsumed, 1);
            $hour->totalLeft     = round($hour->totalLeft, 1);
            $hour->totalReal     = $hour->totalConsumed + $hour->totalLeft;
            $hour->progress      = $hour->totalReal ? round($hour->totalConsumed / $hour->totalReal, 2) * 100 : 0;
        }

        return $hours;
    }

    /**
     * Get project list data.
     *
     * @param  int       $programID
     * @param  string    $browseType
     * @param  string    $queryID
     * @param  string    $orderBy
     * @param  object    $pager
     * @param  int       $programTitle
     * @param  int       $involved
     * @param  bool      $queryAll
     * @access public
     * @return object
     */
    public function getProjectList($programID = 0, $browseType = 'all', $queryID = 0, $orderBy = 'id_desc', $pager = null, $programTitle = 0, $involved = 0, $queryAll = false)
    {
        $path = '';
        if($programID)
        {
            $program = $this->getByID($programID);
            $path    = $program->path;
        }

        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('projectQuery', $query->sql);
                $this->session->set('projectForm', $query->form);
            }
            else
            {
                $this->session->set('projectQuery', ' 1 = 1');
            }
        }
        else
        {
            if($browseType == 'bySearch' and $this->session->projectQuery == false) $this->session->set('projectQuery', ' 1 = 1');
        }

        $query       = str_replace('`id`','t1.id', $this->session->projectQuery);
        $projectList = $this->dao->select('distinct t1.*')->from(TABLE_PROJECT)->alias('t1')
            ->leftJoin(TABLE_TEAM)->alias('t2')->on('t1.id=t2.root')
            ->leftJoin(TABLE_STAKEHOLDER)->alias('t3')->on('t1.id=t3.objectID')
            ->where('t1.deleted')->eq('0')
            ->andWhere('t1.vision')->eq($this->config->vision)
            ->beginIF($browseType == 'bysearch')->andWhere($query)->fi()
            ->beginIF($this->config->systemMode == 'new')->andWhere('t1.type')->eq('project')->fi()
            ->beginIF($this->config->systemMode == 'new' and ($this->cookie->involved or $involved))->andWhere('t2.type')->eq('project')->fi()
            ->beginIF(!in_array($browseType, array('all', 'undone', 'bysearch', 'review', 'unclosed'), true))->andWhere('t1.status')->eq($browseType)->fi()
            ->beginIF($browseType == 'undone' or $browseType == 'unclosed')->andWhere('t1.status')->in('wait,doing')->fi()
            ->beginIF($browseType == 'review')
            ->andWhere("FIND_IN_SET('{$this->app->user->account}', t1.reviewers)")
            ->andWhere('t1.reviewStatus')->eq('doing')
            ->fi()
            ->beginIF($path)->andWhere('t1.path')->like($path . '%')->fi()
            ->beginIF(!$queryAll and !$this->app->user->admin and $this->config->systemMode == 'new')->andWhere('t1.id')->in($this->app->user->view->projects)->fi()
            ->beginIF(!$queryAll and !$this->app->user->admin and $this->config->systemMode == 'classic')->andWhere('t1.id')->in($this->app->user->view->sprints)->fi()
            ->beginIF($this->cookie->involved or $involved)
            ->andWhere('t1.openedBy', true)->eq($this->app->user->account)
            ->orWhere('t1.PM')->eq($this->app->user->account)
            ->orWhere('t2.account')->eq($this->app->user->account)
            ->orWhere('t3.user')->eq($this->app->user->account)
            ->orWhere("CONCAT(',', t1.whitelist, ',')")->like("%,{$this->app->user->account},%")
            ->markRight(1)
            ->fi()
            ->orderBy($orderBy)
            ->page($pager, 't1.id')
            ->fetchAll('id');

        /* Determine how to display the name of the program. */
        if($programTitle and $this->config->systemMode == 'new')
        {
            $programList = $this->getPairs();
            foreach($projectList as $id => $project)
            {
                $path = explode(',', $project->path);
                $path = array_filter($path);
                array_pop($path);
                $programID = $programTitle == 'base' ? current($path) : end($path);
                if(empty($path) || $programID == $id) continue;

                $programName = isset($programList[$programID]) ? $programList[$programID] : '';

                $projectList[$id]->name = $programName . '/' . $projectList[$id]->name;
            }
        }
        return $projectList;
    }

    /**
     * Get stakeholders by program id.
     *
     * @param  int     $programID
     * @param  string  $orderBy
     * @param  object  $paper
     * @access public
     * @return array
     */
    public function getStakeholders($programID = 0, $orderBy = 'id_desc', $pager = null)
    {
        return $this->dao->select('t2.account,t2.realname,t2.role,t2.qq,t2.mobile,t2.phone,t2.weixin,t2.email,t1.id,t1.type,t1.from,t1.key')->from(TABLE_STAKEHOLDER)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.user=t2.account')
            ->where('t1.objectID')->eq($programID)
            ->andWhere('t1.objectType')->eq('program')
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll();
    }

    /**
     * Get stakeholders by program id list.
     *
     * @param  string $programIdList
     * @access public
     * @return array
     */
    public function getStakeholdersByPrograms($programIdList = 0)
    {
        return $this->dao->select('distinct user as account')->from(TABLE_STAKEHOLDER)
            ->where('objectID')->in($programIdList)
            ->andWhere('objectType')->eq('program')
            ->fetchAll();
    }

    /**
     * Get program and project progress list.
     *
     * @access public
     * @return array
     */
    public function getProgressList()
    {
        $totalProgress = array();
        $projectCount  = array();
        $userPRJCount  = array();
        $progressList  = array();
        $programPairs  = $this->getPairs();
        $projectStats  = $this->getProjectStats(0, 'all', 0, 'id_desc', null, 0, 0, true);

        /* Add program progress. */
        foreach(array_keys($programPairs) as $programID)
        {
            $totalProgress[$programID] = 0;
            $projectCount[$programID]  = 0;
            $userPRJCount[$programID]  = 0;
            $progressList[$programID]  = 0;

            foreach($projectStats as $project)
            {
                if(strpos($project->path, ',' . $programID . ',') === false) continue;

                /* The number of projects under this program that the user can view. */
                if(strpos(',' . $this->app->user->view->projects . ',', ',' . $project->id . ',') !== false) $userPRJCount[$programID] ++;

                $totalProgress[$programID] += $project->hours->progress;
                $projectCount[$programID] ++;
            }

            if(empty($projectCount[$programID])) continue;

            /* Program progress can't see when this user don't have all projects priv. */
            if(!$this->app->user->admin and $userPRJCount[$programID] != $projectCount[$programID])
            {
                unset($progressList[$programID]);
                continue;
            }

            $progressList[$programID] = round($totalProgress[$programID] / $projectCount[$programID]);
        }

        /* Add project progress. */
        foreach($projectStats as $project) $progressList[$project->id] = $project->hours->progress;

        return $progressList;
    }

    /**
     * Create a program.
     *
     * @access private
     * @return int|bool
     */
    public function create()
    {
        $program = fixer::input('post')
            ->setDefault('status', 'wait')
            ->setDefault('openedBy', $this->app->user->account)
            ->setDefault('parent', 0)
            ->setDefault('openedDate', helper::now())
            ->setIF($this->post->acl == 'open', 'whitelist', '')
            ->setIF($this->post->delta == 999, 'end', LONG_TIME)
            ->setIF($this->post->budget != 0, 'budget', round((float)$this->post->budget, 2))
            ->setIF(!isset($_POST['whitelist']), 'whitelist', '')
            ->add('type', 'program')
            ->join('whitelist', ',')
            ->stripTags($this->config->program->editor->create['id'], $this->config->allowedTags)
            ->remove('delta,future,contactListMenu')
            ->get();

        /* Redefines the language entries for the fields in the project table. */
        foreach(explode(',', $this->config->program->create->requiredFields) as $field)
        {
            if(isset($this->lang->program->$field)) $this->lang->project->$field = $this->lang->program->$field;
        }

        $this->lang->error->unique = $this->lang->error->repeat;
        $program = $this->loadModel('file')->processImgURL($program, $this->config->program->editor->create['id'], $this->post->uid);
        $this->dao->insert(TABLE_PROGRAM)->data($program)
            ->autoCheck()
            ->batchcheck($this->config->program->create->requiredFields, 'notempty')
            ->checkIF($program->begin != '', 'begin', 'date')
            ->checkIF($program->end != '', 'end', 'date')
            ->checkIF($program->end != '', 'end', 'gt', $program->begin)
            ->checkIF(!empty($program->name), 'name', 'unique', "`type`='program' and `parent` = $program->parent and `deleted` = '0'")
            ->checkFlow()
            ->exec();

        if(!dao::isError())
        {
            $programID = $this->dao->lastInsertId();
            $this->dao->update(TABLE_PROGRAM)->set('`order`')->eq($programID * 5)->where('id')->eq($programID)->exec(); // Save order.

            $whitelist = explode(',', $program->whitelist);
            $this->loadModel('personnel')->updateWhitelist($whitelist, 'program', $programID);

            $this->file->updateObjectID($this->post->uid, $programID, 'program');
            $this->setTreePath($programID);

            if($program->acl != 'open') $this->loadModel('user')->updateUserView($programID, 'program');

            return $programID;
        }
    }

    /**
     * Update program.
     *
     * @param  int    $programID
     * @access public
     * @return array|bool
     */
    public function update($programID)
    {
        $this->app->loadLang('project');
        $programID  = (int)$programID;
        $oldProgram = $this->dao->findById($programID)->from(TABLE_PROGRAM)->fetch();

        $program = fixer::input('post')
            ->add('id', $programID)
            ->setDefault('team', $this->post->name)
            ->setDefault('end', '')
            ->setDefault('lastEditedBy', $this->app->user->account)
            ->setDefault('lastEditedDate', helper::now())
            ->setIF($this->post->begin == '0000-00-00', 'begin', '')
            ->setIF($this->post->end   == '0000-00-00', 'end', '')
            ->setIF($this->post->delta == 999, 'end', LONG_TIME)
            ->setIF($this->post->realBegan != '' and $oldProgram->status == 'wait', 'status', 'doing')
            ->setIF($this->post->future, 'budget', 0)
            ->setIF($this->post->budget != 0, 'budget', round($this->post->budget, 2))
            ->setIF(!isset($_POST['budgetUnit']), 'budgetUnit', $oldProgram->budgetUnit)
            ->setIF(!isset($_POST['whitelist']), 'whitelist', '')
            ->join('whitelist', ',')
            ->stripTags($this->config->program->editor->edit['id'], $this->config->allowedTags)
            ->remove('uid,delta,future,syncPRJUnit,exchangeRate,contactListMenu')
            ->get();

        $program  = $this->loadModel('file')->processImgURL($program, $this->config->program->editor->edit['id'], $this->post->uid);
        $children = $this->getChildren($programID);


        if($program->parent)
        {
            $this->dao->update(TABLE_MODULE)
                ->set('root')->eq($program->parent)
                ->where('root')->eq($programID)
                ->andwhere('type')->eq('line')
                ->exec();
        }
        if(dao::isError()) return false;

        /* Redefines the language entries for the fields in the project table. */
        foreach(explode(',', $this->config->program->edit->requiredFields) as $field)
        {
            if(isset($this->lang->program->$field)) $this->lang->project->$field = $this->lang->program->$field;
        }

        $this->lang->error->unique = $this->lang->error->repeat;
        $this->dao->update(TABLE_PROGRAM)->data($program)
            ->autoCheck($skipFields = 'begin,end')
            ->batchcheck($this->config->program->edit->requiredFields, 'notempty')
            ->checkIF($program->begin != '', 'begin', 'date')
            ->checkIF($program->end != '', 'end', 'date')
            ->checkIF($program->end != '', 'end', 'gt', $program->begin)
            ->checkIF(!empty($program->name), 'name', 'unique', "id!=$programID and `type`='program' and `parent` = $program->parent and `deleted` = '0'")
            ->checkFlow()
            ->where('id')->eq($programID)
            ->limit(1)
            ->exec();

        if(!dao::isError())
        {
            /* If the program changes, the budget unit will be updated to the project and sub-programs simultaneously. */
            if($program->budgetUnit != $oldProgram->budgetUnit and $_POST['syncPRJUnit'] == 'true')
            {
                $this->dao->update(TABLE_PROJECT)
                    ->set('budgetUnit')->eq($program->budgetUnit)
                    ->beginIF(!empty($_POST['exchangeRate']))->set("budget = {$_POST['exchangeRate']} * `budget`")->fi()
                    ->where('path')->like(",{$programID},%")
                    ->andWhere('type')->in('program,project')
                    ->exec();
            }

            $this->file->updateObjectID($this->post->uid, $programID, 'project');
            $whitelist = explode(',', $program->whitelist);
            $this->loadModel('personnel')->updateWhitelist($whitelist, 'program', $programID);
            $this->loadModel('user');
            if($program->acl != 'open') $this->user->updateUserView($programID, 'program');

	    /* If the program changes, the authorities of programs and projects under the program should be refreshed. */
	    $children = $this->dao->select('id, type')->from(TABLE_PROGRAM)->where('path')->like("%,{$programID},%")->andWhere('id')->ne($programID)->andWhere('acl')->eq('program')->fetchPairs('id', 'type');
            foreach($children as $id => $type) $this->user->updateUserView($id, $type);
            if(isset($program->PM) and $program->PM != $oldProgram->PM)
            {
                $productIdList = $this->dao->select('id')->from(TABLE_PRODUCT)->where('program')->eq($programID)->fetchPairs('id');
                foreach($productIdList as $productID) $this->user->updateUserView($productID, 'product');
            }

            if($oldProgram->parent != $program->parent)
            {
                $this->processNode($programID, $program->parent, $oldProgram->path, $oldProgram->grade);

                /* Move product to new top program. */
                if($oldProgram->parent == 0)
                {
                    $oldTopProgram = $this->getTopByPath($oldProgram->path);
                    $newTopProgram = $this->getTopByID($programID);
                    if($oldTopProgram != $newTopProgram) $this->dao->update(TABLE_PRODUCT)->set('program')->eq($newTopProgram)->where('program')->eq($oldTopProgram)->exec();
                }
            }

            return common::createChanges($oldProgram, $program);
        }
    }

    /*
     * Get program swapper.
     *
     * @param  int     $programID
     * @access private
     * @return string
     */
    public function getSwitcher($programID = 0)
    {
        $currentProgramName = '';
        $currentModule      = $this->app->moduleName;
        $currentMethod      = $this->app->methodName;

        if($programID)
        {
            setCookie("lastProgram", $programID, $this->config->cookieLife, $this->config->webRoot, '', false, true);
            $currentProgram     = $this->getById($programID);
            $currentProgramName = $currentProgram->name;
        }
        else
        {
            $currentProgramName = $this->lang->program->all;
        }

        if($this->app->viewType == 'mhtml' and $programID)
        {
            $output  = $this->lang->program->common . $this->lang->colon;
            $output .= "<a id='currentItem' href=\"javascript:showSearchMenu('program', '$programID', '$currentModule', '$currentMethod', '')\">{$currentProgramName} <span class='icon-caret-down'></span></a><div id='currentItemDropMenu' class='hidden affix enter-from-bottom layer'></div>";
            return $output;
        }

        $dropMenuLink = helper::createLink('program', 'ajaxGetDropMenu', "objectID=$programID&module=$currentModule&method=$currentMethod");
        $output  = "<div class='btn-group header-btn' id='swapper'><button data-toggle='dropdown' type='button' class='btn' id='currentItem' title='{$currentProgramName}'><span class='text'>{$currentProgramName}</span> <span class='caret' style='margin-bottom: -1px'></span></button><div id='dropMenu' class='dropdown-menu search-list' data-ride='searchList' data-url='$dropMenuLink'>";
        $output .= '<div class="input-control search-box has-icon-left has-icon-right search-example"><input type="search" class="form-control search-input" /><label class="input-control-icon-left search-icon"><i class="icon icon-search"></i></label><a class="input-control-icon-right search-clear-btn"><i class="icon icon-close icon-sm"></i></a></div>'; $output .= "</div></div>";

        return $output;
    }

    /**
     * Get the tree menu of program.
     *
     * @param  int    $programID
     * @param  string $from
     * @param  string $vars
     * @param  string $moduleName
     * @param  string $methodName
     * @access public
     * @return string
     */
    public function getTreeMenu($programID = 0, $from = 'program', $vars = '', $moduleName = '', $methodName = '')
    {
        $programMenu = array();
        $query = $this->dao->select('*')->from(TABLE_PROJECT)
            ->where('deleted')->eq('0')
            ->beginIF($from == 'program')
            ->andWhere('type')->eq('program')
            ->andWhere('id')->in($this->app->user->view->programs)
            ->fi()
            ->beginIF($from == 'product')
            ->andWhere('type')->eq('program')
            ->andWhere('grade')->eq(1)
            ->andWhere('id')->in($this->app->user->view->programs)
            ->fi()
            ->beginIF(!$this->cookie->showClosed)->andWhere('status')->ne('closed')->fi()
            ->orderBy('grade desc, `order`')->get();
        $stmt = $this->dbh->query($query);

        while($program = $stmt->fetch())
        {
            $link     = $this->getLink($moduleName, $methodName, $program->id, $vars, $from);
            $linkHtml = html::a($link, $program->name, '', "id='program$program->id' class='text-ellipsis programName' title=$program->name");

            if(isset($programMenu[$program->id]) and !empty($programMenu[$program->id]))
            {
                if(!isset($programMenu[$program->parent])) $programMenu[$program->parent] = '';
                $programMenu[$program->parent] .= "<li>$linkHtml";
                $programMenu[$program->parent] .= "<ul>" . $programMenu[$program->id] . "</ul>\n";
            }
            else
            {
                if(empty($programMenu[$program->parent])) $programMenu[$program->parent] = '';
                $programMenu[$program->parent] .= "<li>$linkHtml\n";
            }
            $programMenu[$program->parent] .= "</li>\n";
        }

        krsort($programMenu);
        $programMenu = array_pop($programMenu);
        $lastMenu    = "<ul class='tree tree-simple' data-ride='tree' id='programTree' data-name='tree-program'>{$programMenu}</ul>\n";

        return $lastMenu;
    }

    /**
     * Get link for drop tree menu.
     *
     * @param  string $moduleName
     * @param  string $methodName
     * @param  int    $programID
     * @param  string $vars
     * @param  string $from
     * @access public
     * @return string
     */
    public function getLink($moduleName, $methodName, $programID, $vars = '', $from = 'program')
    {
        if($from != 'program') return helper::createLink('product', 'all', "programID=$programID" . $vars);

        if($moduleName == 'project')
        {
            $moduleName = 'program';
            $methodName = 'project';
        }
        if($moduleName == 'product')
        {
            $moduleName = 'program';
            $methodName = 'product';
        }

        return helper::createLink($moduleName, $methodName, "programID=$programID");
    }

    /**
     * Get top program pairs.
     *
     * @param  string $model
     * @param  string $mode
     * @param  bool   $isQueryAll
     * @access public
     * @return array
     */
    public function getTopPairs($model = '', $mode = '', $isQueryAll = false)
    {
        return $this->dao->select('id,name')->from(TABLE_PROGRAM)
            ->where('type')->eq('program')
            ->andWhere('grade')->eq(1)
            ->beginIF(strpos($mode, 'noclosed') !== false)->andWhere('status')->ne('closed')->fi()
            ->beginIF(!$isQueryAll)->andWhere('id')->in($this->app->user->view->programs)->fi()
            ->andWhere('deleted')->eq(0)
            ->beginIF($model)->andWhere('model')->eq($model)->fi()
            ->orderBy('`order` asc')
            ->fetchPairs();
    }

    /**
     * Get top program by id.
     *
     * @param  int    $programID
     * @access public
     * @return int
     */
    public function getTopByID($programID)
    {
        if(empty($programID)) return 0;

        $program = $this->getByID($programID);
        if(empty($program)) return 0;

        return $this->getTopByPath($program->path);
    }

    /**
     * Get top program by path.
     *
     * @param  string  $path
     * @access public
     * @return string
     */
    public function getTopByPath($path)
    {
        $paths = explode(',', trim($path, ','));
        return $paths[0];
    }

    /**
     * Get children by program id.
     *
     * @param  int  $programID
     * @access public
     * @return int
     */
    public function getChildren($programID = 0)
    {
        return $this->dao->select('count(*) as count')->from(TABLE_PROGRAM)->where('parent')->eq($programID)->fetch('count');
    }

    /**
     * Judge whether there is an unclosed programs or projects.
     *
     * @param  object  $program
     * @access public
     * @return int
     */
    public function hasUnfinished($program)
    {
        $unfinished = $this->dao->select("count(IF(id != {$program->id}, true, null)) as count")->from(TABLE_PROJECT)
            ->where('type')->in('program, project')
            ->andWhere('path')->like($program->path . '%')
            ->andWhere('status')->ne('closed')
            ->andWhere('deleted')->eq('0')
            ->fetch('count');

        return $unfinished;
    }

    /**
     * Create stakeholder for a program.
     *
     * @param  int     $programID
     * @access public
     * @return void
     */
    public function createStakeholder($programID = 0)
    {
        $data = (array)fixer::input('post')->get();

        $accounts = array_unique($data['accounts']);
        $oldJoin  = $this->dao->select('`user`, createdDate')->from(TABLE_STAKEHOLDER)->where('objectID')->eq((int)$programID)->andWhere('objectType')->eq('program')->fetchPairs();
        $this->dao->delete()->from(TABLE_STAKEHOLDER)->where('objectID')->eq((int)$programID)->andWhere('objectType')->eq('program')->exec();

        foreach($accounts as $key => $account)
        {
            if(empty($account)) continue;

            $stakeholder = new stdclass();
            $stakeholder->objectID    = $programID;
            $stakeholder->objectType  = 'program';
            $stakeholder->user        = $account;
            $stakeholder->createdBy   = $this->app->user->account;
            $stakeholder->createdDate = isset($oldJoin[$account]) ? $oldJoin[$account] : helper::today();

            $this->dao->insert(TABLE_STAKEHOLDER)->data($stakeholder)->exec();
        }

        /* If any account changed, update his view. */
        $oldAccounts     = array_keys($oldJoin);
        $changedAccounts = array_diff($accounts, $oldAccounts);
        $changedAccounts = array_merge($changedAccounts, array_diff($oldAccounts, $accounts));
        $changedAccounts = array_unique($changedAccounts);

        $this->loadModel('user')->updateUserView($programID, 'program', $changedAccounts);

        /* Update children user view. */
        $childPrograms = $this->dao->select('id')->from(TABLE_PROJECT)->where('path')->like("%,$programID,%")->andWhere('type')->eq('program')->fetchPairs();
        $childProjects = $this->dao->select('id')->from(TABLE_PROJECT)->where('path')->like("%,$programID,%")->andWhere('type')->eq('project')->fetchPairs();
        $childProducts = $this->dao->select('id')->from(TABLE_PRODUCT)->where('program')->eq($programID)->fetchPairs();

        if(!empty($childPrograms)) $this->user->updateUserView($childPrograms, 'program', $changedAccounts);
        if(!empty($childProjects)) $this->user->updateUserView($childProjects, 'project', $changedAccounts);
        if(!empty($childProducts)) $this->user->updateUserView($childProducts, 'product', $changedAccounts);
    }

    /**
     * Judge an action is clickable or not.
     *
     * @param  object    $program
     * @param  string    $action
     * @access public
     * @return bool
     */
    public static function isClickable($program, $action)
    {
        $action = strtolower($action);

        if(empty($program)) return true;
        if(!isset($program->type)) return true;

        if($action == 'close')    return $program->status != 'closed';
        if($action == 'activate') return $program->status == 'done' or $program->status == 'closed';
        if($action == 'suspend')  return $program->status == 'wait' or $program->status == 'doing';

        return true;
    }

    /**
     * Set program tree path.
     *
     * @param  int    $programID
     * @access public
     * @return bool
     */
    public function setTreePath($programID)
    {
        $program = $this->dao->select('*')->from(TABLE_PROGRAM)->where('id')->eq($programID)->fetch();

        $path['path']  = ",{$program->id},";
        $path['grade'] = 1;

        if($program->parent)
        {
            $parent = $this->dao->select('*')->from(TABLE_PROGRAM)->where('id')->eq($program->parent)->fetch();

            $path['path']  = $parent->path . "{$program->id},";
            $path['grade'] = $parent->grade + 1;
        }
        $this->dao->update(TABLE_PROGRAM)->set('path')->eq($path['path'])->set('grade')->eq($path['grade'])->where('id')->eq($program->id)->exec();

        return !dao::isError();
    }

    /**
     * Get budget left.
     *
     * @param  int    $parentProgram
     * @param  int    $leftBudget
     * @access public
     * @return int
     */
    public function getBudgetLeft($parentProgram, $leftBudget = 0)
    {
        if(empty($parentProgram->id)) return;

        $childGrade     = $parentProgram->grade + 1;
        $childSumBudget = $this->dao->select("sum(budget) as sumBudget")->from(TABLE_PROGRAM)
            ->where('path')->like("%,{$parentProgram->id},%")
            ->andWhere('grade')->eq($childGrade)
            ->andWhere('deleted')->eq('0')
            ->fetch('sumBudget');

        $leftBudget += (float)$parentProgram->budget - (float)$childSumBudget;

        if($parentProgram->budget == 0 and $parentProgram->parent)
        {
            $parentParent = $this->getById($parentProgram->parent);
            return $this->getBudgetLeft($parentParent, $leftBudget);
        }
        else
        {
            return $leftBudget;
        }
    }

    /**
     * Get program parent pairs
     *
     * @param  string $model
     * @param  string $mode
     * @param  bool   $showRoot
     * @access public
     * @return array
     */
    public function getParentPairs($model = '', $mode = 'noclosed', $showRoot = true)
    {
        $modules = $this->dao->select('id,name,parent,path,grade')->from(TABLE_PROGRAM)
            ->where('type')->eq('program')
            ->beginIF(strpos($mode, 'noclosed') !== false)->andWhere('status')->ne('closed')->fi()
            ->andWhere('deleted')->eq(0)
            ->beginIF($model)->andWhere('model')->eq($model)->fi()
            ->orderBy('grade desc, `order`')
            ->fetchAll('id');

        $treeMenu = array();
        foreach($modules as $module)
        {
            if(strpos(",{$this->app->user->view->programs},", ",{$module->id},") === false and (!$this->app->user->admin)) continue;

            $moduleName    = $showRoot ? '/' : '';
            $parentModules = explode(',', $module->path);
            foreach($parentModules as $parentModuleID)
            {
                if(empty($parentModuleID)) continue;
                if(empty($modules[$parentModuleID])) continue;
                $moduleName .= $modules[$parentModuleID]->name . '/';
            }
            $moduleName  = str_replace('|', '&#166;', rtrim($moduleName, '/'));
            $moduleName .= "|$module->id\n";

            if(!isset($treeMenu[$module->parent])) $treeMenu[$module->parent] = '';
            $treeMenu[$module->parent] .= $moduleName;

            if(isset($treeMenu[$module->id]) and !empty($treeMenu[$module->id])) $treeMenu[$module->parent] .= $treeMenu[$module->id];
        }

        ksort($treeMenu);
        $topMenu = array_shift($treeMenu);
        $topMenu = empty($topMenu) ? '' : $topMenu;
        $topMenu = explode("\n", trim($topMenu));
        $showRoot ? $lastMenu[] = '/' : $lastMenu = array();
        foreach($topMenu as $menu)
        {
            if(strpos($menu, '|') === false) continue;
            list($label, $moduleID) = explode('|', $menu);
            $lastMenu[$moduleID] = str_replace('&#166;', '|', $label);
        }

        return $lastMenu;
    }

    /**
     * Get parent PM by programIdList.
     *
     * @param  array $programIdList
     * @access public
     * @return void
     */
    public function getParentPM($programIdList)
    {
        $objects = $this->dao->select('id, path, parent')->from(TABLE_PROGRAM)->where('id')->in($programIdList)->andWhere('acl')->ne('open')->fetchAll('id');

        $parents = array();
        foreach($objects as $object)
        {
            if($object->parent == 0) continue;
            foreach(explode(',', $object->path) as $objectID)
            {
                if(empty($objectID) || $objectID == $object->id) continue;
                $parents[$objectID][] = $object->id;
            }
        }

        /* Get all parent PM.*/
        $parentPM = $this->dao->select('id, PM')->from(TABLE_PROGRAM)->where('id')->in(array_keys($parents))->andWhere('deleted')->eq('0')->fetchAll();

        $parentPMGroup = array();
        foreach($parentPM as $PM)
        {
            $subPrograms = zget($parents, $PM->id, array());
            foreach($subPrograms as $subProgramID) $parentPMGroup[$subProgramID][$PM->PM] = $PM->PM;
        }

        return $parentPMGroup;
    }

    /**
     * Move project node.
     *
     * @param  int       $programID
     * @param  int       $parentID
     * @param  string    $oldPath
     * @param  int       $oldGrade
     * @access public
     * @return bool
     */
    public function processNode($programID, $parentID, $oldPath, $oldGrade)
    {
        $parent = $this->dao->select('id,parent,path,grade')->from(TABLE_PROGRAM)->where('id')->eq($parentID)->fetch();

        $childNodes = $this->dao->select('id,parent,path,grade,type')->from(TABLE_PROGRAM)
            ->where('path')->like("{$oldPath}%")
            ->andWhere('deleted')->eq(0)
            ->orderBy('grade')
            ->fetchAll();

        /* Process child node path and grade field. */
        foreach($childNodes as $childNode)
        {
            $path = substr($childNode->path, strpos($childNode->path, ",{$programID},"));

            /* Only program and project sets update grade. */
            $grade = in_array($childNode->type, array('program', 'project')) ? $childNode->grade - $oldGrade + 1 : $childNode->grade;
            if($parent)
            {
                $path  = rtrim($parent->path, ',') . $path;
                $grade =  in_array($childNode->type, array('program', 'project'))? $parent->grade + $grade : $grade;
            }

            $this->dao->update(TABLE_PROGRAM)->set('path')->eq($path)->set('grade')->eq($grade)->where('id')->eq($childNode->id)->exec();
        }

        return !dao::isError();
    }

    /**
     * Get project stats.
     *
     * @param  int    $programID
     * @param  string $browseType
     * @param  int    $queryID
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $programTitle
     * @param  int    $involved
     * @param  bool   $queryAll
     * @access public
     * @return array
     */
    public function getProjectStats($programID = 0, $browseType = 'undone', $queryID = 0, $orderBy = 'id_desc', $pager = null, $programTitle = 0, $involved = 0, $queryAll = false)
    {
        if(defined('TUTORIAL')) return $this->loadModel('tutorial')->getProjectStats($browseType);

        /* Init vars. */
        $projects = $this->getProjectList($programID, $browseType, $queryID, $orderBy, $pager, $programTitle, $involved, $queryAll);

        if(empty($projects)) return array();

        $projectKeys = array_keys($projects);
        $stats       = array();
        $hours       = array();
        $emptyHour   = array('totalEstimate' => 0, 'totalConsumed' => 0, 'totalLeft' => 0, 'progress' => 0);
        $leftTasks   = array();
        $teamMembers = array();

        $executions = $this->dao->select('id')->from(TABLE_EXECUTION)
            ->where('deleted')->eq(0)
            ->andWhere('project')->in($projectKeys)
            ->andWhere('type')->in('sprint,stage,kanban')
            ->fetchAll('id');

        /* Get all tasks and compute totalEstimate, totalConsumed, totalLeft, progress according to them. */
        $tasks = $this->dao->select('id, project, estimate, consumed, `left`, status, closedReason, execution')
            ->from(TABLE_TASK)
            ->where('project')->in($projectKeys)
            ->andWhere('parent')->lt(1)
            ->andWhere('deleted')->eq(0)
            ->fetchGroup('project', 'id');

        /* Compute totalEstimate, totalConsumed, totalLeft. */
        foreach($tasks as $projectID => $projectTasks)
        {
            $hour = (object)$emptyHour;
            foreach($projectTasks as $task)
            {
                if(isset($executions[$task->execution]))
                {
                    $hour->totalConsumed += $task->consumed;
                    $hour->totalEstimate += $task->estimate;

                    if(strpos('cancel,closed', $task->status) === false) $hour->totalLeft += $task->left;
                }
            }
            $hours[$projectID] = $hour;
        }

        /* Compute totalReal and progress. */
        $progressList = $this->loadModel('project')->getWaterfallProgress(array_keys($hours));
        foreach($hours as $projectID => $hour)
        {
            $hour->totalEstimate = round($hour->totalEstimate, 1) ;
            $hour->totalConsumed = round($hour->totalConsumed, 1);
            $hour->totalLeft     = round($hour->totalLeft, 1);
            $hour->totalReal     = $hour->totalConsumed + $hour->totalLeft;
            $hour->progress      = $projects[$projectID]->model == 'waterfall' ? $progressList[$projectID] : ($hour->totalReal ? round($hour->totalConsumed / $hour->totalReal, 2) * 100 : 0);
        }

        /* Get the number of left tasks. */
        if($this->cookie->projectType and $this->cookie->projectType == 'bycard')
        {
            $leftTasks = $this->dao->select('t2.parent as project, count(*) as tasks')->from(TABLE_TASK)->alias('t1')
                ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.execution = t2.id')
                ->where('t1.execution')->in(array_keys($executions))
                ->andWhere('t1.status')->notIn('cancel,closed')
                ->groupBy('t2.parent')
                ->fetchAll('project');
        }

        /* Get the number of project teams. */
        $teams = $this->dao->select('t1.root,count(t1.id) as teams')->from(TABLE_TEAM)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account=t2.account')
            ->where('t1.root')->in($projectKeys)
            ->andWhere('t1.type')->eq('project')
            ->andWhere('t2.deleted')->eq(0)
            ->groupBy('t1.root')
            ->fetchAll('root');

        /* Get the members of project teams. */
        if($this->cookie->projectType and $this->cookie->projectType == 'bycard')
        {
            $teamMembers = $this->dao->select('t1.root,t1.account')->from(TABLE_TEAM)->alias('t1')
                ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account=t2.account')
                ->where('t1.root')->in($projectKeys)
                ->andWhere('t1.type')->eq('project')
                ->andWhere('t2.deleted')->eq(0)
                ->fetchGroup('root', 'account');
        }

        /* Process projects. */
        foreach($projects as $key => $project)
        {
            if($project->end == '0000-00-00') $project->end = '';

            /* Judge whether the project is delayed. */
            if($project->status != 'done' and $project->status != 'closed' and $project->status != 'suspended')
            {
                $delay = helper::diffDate(helper::today(), $project->end);
                if($delay > 0) $project->delay = $delay;
            }

            /* Process the hours. */
            $project->hours = isset($hours[$project->id]) ? $hours[$project->id] : (object)$emptyHour;

            $project->teamCount   = isset($teams[$project->id]) ? $teams[$project->id]->teams : 0;
            $project->leftTasks   = isset($leftTasks[$project->id]) ? $leftTasks[$project->id]->tasks : '—';
            $project->teamMembers = isset($teamMembers[$project->id]) ? array_keys($teamMembers[$project->id]) : array();

            /* Convert predefined HTML entities to characters. */
            $project->name = htmlspecialchars_decode($project->name, ENT_QUOTES);

            $stats[$key] = $project;
        }
        return $stats;
    }

    /**
     * Get program team member pairs.
     *
     * @param  int  $programID
     * @access public
     * @return array
     */
    public function getTeamMemberPairs($programID = 0)
    {
      $projectList = $this->getProjectList($programID);
      if(!$projectList) return array('' => '');

      $users = $this->dao->select("t2.id, t2.account, t2.realname")->from(TABLE_TEAM)->alias('t1')
          ->leftJoin(TABLE_USER)->alias('t2')->on('t1.account = t2.account')
          ->where('t1.root')->in(array_keys($projectList))
          ->andWhere('t1.type')->eq('project')
          ->andWhere('t2.deleted')->eq(0)
          ->fetchAll('account');
      if(!$users) return array('' => '');

      foreach($users as $account => $user)
      {
        $firstLetter = ucfirst(substr($user->account, 0, 1)) . ':';
        if(!empty($this->config->isINT)) $firstLetter = '';
        $users[$account] = $firstLetter . ($user->realname ? $user->realname : $user->account);
      }

      return array('' => '') + $users;
    }

    /*
     * Set program menu.
     *
     * @param  int    $programID
     * @access public
     * @return void
     */
    public function setMenu($programID)
    {
        $this->lang->switcherMenu = $this->getSwitcher($programID);
        common::setMenuVars('program', $programID);
    }

    /**
     * Build program action menu.
     *
     * @param  object $program
     * @param  string $type
     * @access public
     * @return string
     */
    public function buildOperateMenu($program, $type = 'view')
    {
        $menu   = '';
        $params = "programID=$program->id";
        if($program->type == 'program' && strpos(",{$this->app->user->view->programs},", ",$program->id,") !== false)
        {
            if($program->status == 'wait' || $program->status == 'suspended')
            {
                $menu .= $this->buildMenu('program', 'start', $params, $program, $type, 'play', '', 'iframe', true, '', $this->lang->program->start);
            }
            if($program->status == 'doing')
            {
                $menu .= $this->buildMenu('program', 'close', $params, $program, $type, 'off', '', 'iframe', true);
            }
            if($program->status == 'closed')
            {
                $menu .= $this->buildMenu('program', 'activate', $params, $program, $type, 'magic', '', 'iframe', true);
            }
            if(common::hasPriv('program', 'suspend') || (common::hasPriv('program', 'close') && $program->status != 'doing') || (common::hasPriv('program', 'activate') && $program->status != 'closed'))
            {
                $menu .= "<div class='btn-group'>";
                $menu .= "<button type='button' class='btn icon-caret-down dropdown-toggle' data-toggle='context-dropdown' title='{$this->lang->more}' style='width: 16px; padding-left: 0px;'></button>";
                $menu .= "<ul class='dropdown-menu pull-right text-center' role='menu'>";
                $menu .= $this->buildMenu('program', 'suspend', $params, $program, $type, 'pause', '', 'iframe btn-action btn', true, '', $this->lang->program->suspend);
                if($program->status != 'doing')  $menu .= $this->buildMenu('program', 'close',    $params, $program, $type, 'off', '',   'iframe btn-action btn', true);
                if($program->status != 'closed') $menu .= $this->buildMenu('program', 'activate', $params, $program, $type, 'magic', '', 'iframe btn-action btn', true);
                $menu .= "</ul>";
                $menu .= "</div>";
            }

            $disabled = $program->status == 'closed' ? " disabled='disabled' style='pointer-events: none;'" : '';
            $menu .= $this->buildMenu('program', 'edit',   $params, $program, $type, 'edit');
            $menu .= $this->buildMenu('program', 'create', $params, $program, $type, 'split', '', '', '', $disabled, $this->lang->program->children);
            if(common::hasPriv('program', 'delete'))
            {
                $menu .= $this->buildMenu('program', 'delete', $params, $program, $type, 'trash', 'hiddenwin', '', '', '', $this->lang->program->delete);
            }
        }
        else if($program->type == 'project')
        {
            if($program->status == 'wait' || $program->status == 'suspended')
            {
                $menu .= $this->buildMenu('project', 'start', $params, $program, $type, 'play', '', 'iframe', true);
            }
            if($program->status == 'doing')  $menu .= $this->buildMenu('project', 'close',    $params, $program, $type, 'off',   '', 'iframe', true);
            if($program->status == 'closed') $menu .= $this->buildMenu('project', 'activate', $params, $program, $type, 'magic', '', 'iframe', true);
            if(common::hasPriv('project', 'suspend') || (common::hasPriv('project', 'close') && $program->status != 'doing') || (common::hasPriv('project', 'activate') && $program->status != 'closed'))
            {
                $menu .= "<div class='btn-group'>";
                $menu .= "<button type='button' class='btn icon-caret-down dropdown-toggle' data-toggle='context-dropdown' title='{$this->lang->more}' style='width: 16px; padding-left: 0px;'></button>";
                $menu .= "<ul class='dropdown-menu pull-right text-center' role='menu'>";
                $menu .= $this->buildMenu('project', 'suspend', $params, $program, $type, 'pause', '', 'iframe btn-action btn', true);
                if($program->status != 'doing')  $menu .= $this->buildMenu('project', 'close',    $params, $program, $type, 'off',   '', 'iframe btn-action btn', true);
                if($program->status != 'closed') $menu .= $this->buildMenu('project', 'activate', $params, $program, $type, 'magic', '', 'iframe btn-action btn', true);
                $menu .= "</ul>";
                $menu .= "</div>";
            }

            $menu .= $this->buildMenu('project', 'edit',  $params, $program, $type, 'edit',  '', 'iframe', true);
            $menu .= $this->buildMenu('project', 'team',  $params, $program, $type, 'group', '', '',   '', 'data-app="project"');

            $disabledGroup = $program->model == 'kanban' ? " disabled='disabled' style='pointer-events: none;'" : '';
            $menu         .= $this->buildMenu('project', 'group', $params, $program, $type, 'lock',  '', '',   '', 'data-app="project"' . $disabledGroup);
            if(common::hasPriv('project', 'manageProducts') || common::hasPriv('project', 'whitelist') || common::hasPriv('project', 'delete'))
            {
                $menu .= "<div class='btn-group'>";
                $menu .= "<button type='button' class='btn dropdown-toggle' data-toggle='context-dropdown' title='{$this->lang->more}'><i class='icon-ellipsis-v'></i></button>";
                $menu .= "<ul class='dropdown-menu pull-right text-center' role='menu'>";
                $menu .= $this->buildMenu('project', 'manageProducts', "$params&from=program", $program, $type, 'link', '', 'btn-action', '', "data-app='project'");

                $disabledWhitelist = $program->acl == 'open' ? " disabled='disabled' style='pointer-events: none;'" : '';
                $menu             .= $this->buildMenu('project', 'whitelist', "$params&module=project&from=browse", $program, $type, 'shield-check', '', 'btn-action', '', "data-app='project'" . $disabledWhitelist);
                if(common::hasPriv('project','delete'))
                {
                    $menu .= $this->buildMenu("project", "delete", $params, $program, $type, 'trash', 'hiddenwin', 'btn-action', '', "data-group='program'", $this->lang->delete);
                }
                $menu .= "</ul>";
                $menu .= "</div>";
            }
        }
        return $menu;
    }

    /**
     * Build datatable cell.
     *
     * @param  array  $field
     * @param  object $program
     * @param  array  $users
     * @param  array  $userIdPairs
     * @param  array  $PMList
     * @param  array  $progressList
     * @access public
     * @return string
     */
    public function buildCell($field, $program, $users, $usersAvatar, $userIdPairs, $PMList, $progressList)
    {
        $id   = $field['name'];
        $html = '';
        switch($id)
        {
        case 'name':
            if($program->type == 'program') $icon = ' icon-program';
            if($program->type == 'project') $icon = ' icon-common icon-' . $program->model;
            $class = $program->type == 'program' ? ' table-nest-toggle' : '';
            $html  = "<span class='table-nest-icon icon $class $icon'></span>";

            if($program->type == 'program')
            {
                $html .= "<span class='icon icon-cards-view' style='color: #888fa1;'></span>";
                $html .= ($this->app->user->admin or strpos(",{$this->app->user->view->programs},", ",$program->id,") !== false) ? html::a(helper::createLink('program', 'product', "programID=$program->id"), $program->name) : $program->name;
            }
            else
            {
                $projectType = $this->lang->project->{$program->model};
                $html .= html::a(helper::createLink('project', 'index', "projectID=$program->id", '', '', $program->id), $program->name, '', "class='text-ellipsis text-primary' title='{$program->name} ($projectType)'");

                if($program->status != 'done' and $program->status != 'closed' and $program->status != 'suspended')
                {
                    $delay = helper::diffDate(helper::today(), $program->end);
                    if($delay > 0) $html .= "<span class='label label-danger label-badge'>{$this->lang->project->statusList['delay']}</span>";
                }
            }
        break;
        case 'status':
            $html = "<span class='status-program status-{$program->status}'>" . zget($this->lang->project->statusList, $program->status, '') . '</span>';
        break;
        case 'pm':
            if(!empty($program->PM))
            {
                $userName = zget($users, $program->PM);
                $html     = html::smallAvatar(array('avatar' => $usersAvatar[$program->PM], 'account' => $program->PM, 'name' => $userName), (($program->type == 'program' and $program->grade == 1 )? 'avatar-circle avatar-top avatar-' : 'avatar-circle avatar-') . zget($userIdPairs, $program->PM));
                $userID   = isset($PMList[$program->PM]) ? $PMList[$program->PM]->id : '';
                $html    .= html::a(helper::createLink('user', 'profile', "userID=$userID", '', true), $userName, '', "title='{$userName}' data-toggle='modal' data-type='iframe' data-width='600'");
            }
        break;
        case 'budget':
            $programBudget = $this->project->getBudgetWithUnit($program->budget);
            $html          = $program->budget != 0 ? zget($this->lang->project->currencySymbol, $program->budgetUnit) . ' ' . $programBudget : $this->lang->project->future;
        break;
        case 'begin':
            $html = $program->begin;
        break;
        case 'end':
            $html = $program->end == LONG_TIME ? $this->lang->program->longTime : $program->end;
        break;
        case 'progress':
            $html = isset($progressList[$program->id]) ? round($progressList[$program->id]) : 0;
        break;
        case 'actions':
            $html = $this->buildOperateMenu($program, 'browse');
        break;
        }
        return $html;
    }
}
