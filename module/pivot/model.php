<?php
/**
 * The model file of pivot module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     pivot
 * @version     $Id: model.php 4726 2013-05-03 05:51:27Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class pivotModel extends model
{
     /**
     * Construct.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadBIDAO();
    }

    /**
     * Get pivot.
     *
     * @param  int    $pivotID
     * @access public
     * @return object
     */
    public function getByID($pivotID)
    {
        $pivot = $this->dao->select('*')->from(TABLE_PIVOT)->where('id')->eq($pivotID)->fetch();
        if(!$pivot) return false;

        if(!empty($pivot->fields) and $pivot->fields != 'null')
        {
            $pivot->fieldSettings = json_decode($pivot->fields);
            $pivot->fields        = array();

            foreach($pivot->fieldSettings as $field => $settings) $pivot->fields[] = $field;
        }
        else
        {
            $pivot->fieldSettings = array();
        }

        if(!empty($pivot->filters))
        {
            $filters = json_decode($pivot->filters, true);
            foreach($filters as $key => $filter)
            {
                if(empty($filter['default'])) continue;
                $filters[$key]['default'] = $this->processDateVar($filter['default']);
            }
            $pivot->filters = $filters;
        }

        return $this->processPivot($pivot);
    }

    /**
     * Get pivots.
     *
     * @param  int    $dimensionID
     * @param  int    $groupID
     * @param  string $orderBy
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getList($dimensionID = 0, $groupID = 0, $orderBy = 'id_desc', $pager = null)
    {
        $this->loadModel('screen');
        if($groupID)
        {
            $groups = $this->dao->select('id')->from(TABLE_MODULE)
                ->where('deleted')->eq(0)
                ->andWhere('type')->eq('pivot')
                ->andWhere('path')->like("%,$groupID,%")
                ->fetchPairs('id');

            $conditions = '';
            foreach($groups as $groupID)
            {
                $conditions .= " FIND_IN_SET($groupID, `group`) or";
            }
            $conditions = trim($conditions, 'or');

            $pivots = $this->dao->select('*')->from(TABLE_PIVOT)
                ->where('deleted')->eq(0)
                ->beginIF($conditions)->andWhere("({$conditions})")->fi()
                ->beginIF(!empty($dimensionID))->andWhere('dimension')->eq($dimensionID)->fi()
                ->orderBy($orderBy)
                ->fetchAll();

            $charts = $this->dao->select('*')->from(TABLE_CHART)
                ->where('deleted')->eq(0)
                ->andWhere('type')->eq('table')
                ->beginIF($conditions)->andWhere("({$conditions})")->fi()
                ->beginIF(!empty($dimensionID))->andWhere('dimension')->eq($dimensionID)->fi()
                ->orderBy($orderBy)
                ->fetchAll();

            $pivots = array_merge($pivots, $charts);
        }
        else
        {
            $pivots = $this->dao->select('*')->from(TABLE_PIVOT)
                ->where('deleted')->eq(0)
                ->beginIF(!empty($dimensionID))->andWhere('dimension')->eq($dimensionID)->fi()
                ->orderBy($orderBy)
                ->fetchAll();

            $charts = $this->dao->select('*')->from(TABLE_CHART)
                ->where('deleted')->eq(0)
                ->andWhere('type')->eq('table')
                ->beginIF(!empty($dimensionID))->andWhere('dimension')->eq($dimensionID)->fi()
                ->orderBy($orderBy)
                ->fetchAll();

            $pivots = array_merge($pivots, $charts);
        }

        if(!empty($pager))
        {
            $pager->setRecTotal(count($pivots));
            $pager->setPageTotal();
            if($pager->pageID > $pager->pageTotal) $pager->setPageID($pager->pageTotal);

            if($pivots)
            {
                $pivots = array_chunk($pivots, $pager->recPerPage);
                $pivots = $pivots[$pager->pageID - 1];
            }

        }

        return $this->processPivot($pivots, false);
    }

    /**
     * Process date vars in sql.
     *
     * @param  string $var
     * @param  string $type
     * @access public
     * @return string
     */
    public function processDateVar($var, $type = 'date')
    {
        if(empty($var)) return null;

        $format = $type == 'datetime' ? 'Y-m-d H:i:s' : 'Y-m-d';
        switch($var)
        {
            case '$MONDAY':     $var = date($format, time() - (date('N') - 1) * 24 * 3600); break;
            case '$SUNDAY':     $var = date($format, time() + (7 - date('N')) * 24 * 3600); break;
            case '$MONTHBEGIN': $var = date($format, time() - (date('j') - 1) * 24 * 3600); break;
            case '$MONTHEND':   $var = date($format, time() + (date('t') - date('j')) * 24 * 3600); break;
        }
        return $var;
    }

    /**
     * Process sql and correct type.
     *
     * @param  object|array $pivots
     * @param  bool         $isObject
     * @access public
     * @return object|array
     */
    public function processPivot($pivots, $isObject = true)
    {
        if($isObject) $pivots = array($pivots);

        $this->loadModel('screen');
        $screenList = $this->dao->select('scheme')->from(TABLE_SCREEN)->where('deleted')->eq(0)->andWhere('status')->eq('published')->fetchAll();
        foreach($pivots as $index => $pivot)
        {
            if(!empty($pivot->sql))      $pivots[$index]->sql = trim(str_replace(';', '', $pivot->sql));
            if(!empty($pivot->settings)) $pivots[$index]->settings = json_decode($pivot->settings, true);

            if(empty($pivot->type))
            {
                $pivot->names = '';
                $pivot->descs = '';
                if(!empty($pivot->name))
                {
                    $pivotNames   = json_decode($pivot->name, true);
                    $pivot->name  = zget($pivotNames, $this->app->getClientLang(), '');
                    $pivot->names = $pivotNames;

                    if(!$pivot->name)
                    {
                        $pivotNames  = array_filter($pivotNames);
                        $pivot->name = reset($pivotNames);
                    }
                }

                if(!empty($pivot->desc))
                {
                    $pivotDescs   = json_decode($pivot->desc, true);
                    $pivot->desc  = zget($pivotDescs, $this->app->getClientLang(), '');
                    $pivot->descs = $pivotDescs;
                }
                $pivots[$index]->used = $this->screen->checkIFChartInUse($pivot->id, 'pivot', $screenList);
            }
        }

        return $isObject ? reset($pivots) : $pivots;
    }

    /**
     * Compute percent of every item.
     *
     * @param  array    $datas
     * @access public
     * @return array
     */
    public function computePercent($datas)
    {
        $sum = 0;
        foreach($datas as $data) $sum += $data->value;

        $totalPercent = 0;
        foreach($datas as $i => $data)
        {
            $data->percent = round($data->value / $sum, 4);
            $totalPercent += $data->percent;
        }
        if(isset($i)) $datas[$i]->percent = round(1 - $totalPercent + $datas[$i]->percent, 4);
        return $datas;
    }

    /**
     * Create json data of single charts
     * @param  array $sets
     * @param  array $dateList
     * @return string the json string
     */
    public function createSingleJSON($sets, $dateList)
    {
        $data = '[';
        $now  = date('Y-m-d');
        $preValue = 0;
        $setsDate = array_keys($sets);
        foreach($dateList as $date)
        {
            $date  = date('Y-m-d', strtotime($date));
            if($date > $now) break;
            if(!isset($sets[$date]) and $sets)
            {
                $tmpDate = $setsDate;
                $tmpDate[] = $date;
                sort($tmpDate);
                $tmpDateStr = ',' . implode(',', $tmpDate);
                $preDate = rtrim(substr($tmpDateStr, 0, strpos($tmpDateStr, $date)), ',');
                $preDate = substr($preDate, strrpos($preDate, ',') + 1);

                if($preDate)
                {
                    $preValue = $sets[$preDate];
                    $preValue = $preValue->value;
                }
            }

            $data .= isset($sets[$date]) ? "{$sets[$date]->value}," : "{$preValue},";
        }
        $data = rtrim($data, ',');
        $data .= ']';
        return $data;
    }

    /**
     * Convert date format.
     *
     * @param  array  $dateList
     * @param  string $format
     * @access public
     * @return array
     */
    public function convertFormat($dateList, $format = 'Y-m-d')
    {
        foreach($dateList as $i => $date) $dateList[$i] = date($format, strtotime($date));
        return $dateList;
    }

    /**
     * Get executions.
     *
     * @param  string $begin
     * @param  string $end
     * @access public
     * @return array
     */
    public function getExecutions(string $begin = '', string $end = ''): array
    {
        $permission = common::hasPriv('pivot', 'showProject') or $this->app->user->admin;
        $executions = $this->dao->select("t1.project AS projectID, t1.execution AS executionID, t2.multiple, IF(t3.multiple = '1', t2.name, '') AS executionName, t3.name AS projectName, ROUND(SUM(t1.estimate), 2) AS estimate, ROUND(SUM(t1.consumed), 2) AS consumed")->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution = t2.id')
            ->leftJoin(TABLE_PROJECT)->alias('t3')->on('t1.project = t3.id')
            ->where('t1.status')->ne('cancel')
            ->andWhere('t1.deleted')->eq('0')
            ->andWhere('t1.parent')->lt(1)
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere('t2.status')->eq('closed')
            ->beginIF($begin)->andWhere('t2.begin')->ge($begin)->fi()
            ->beginIF($end)->andWhere('t2.end')->le($end)->fi()
            ->beginIF(!$permission)->andWhere('t2.id')->in($this->app->user->view->sprints)->fi()
            ->groupBy('t1.project, t1.execution, t2.multiple')
            ->orderBy('t2.end_desc')
            ->fetchAll();

        foreach($executions as $execution)
        {
            $execution->deviation     = round($execution->consumed - $execution->estimate, 2);
            $execution->deviationRate = $execution->estimate ? round($execution->deviation / $execution->estimate * 100, 2) : 'n/a';
        }

        return $executions;
    }

    /**
     * Get products.
     *
     * @param  string $conditions
     * @param  string $storyType
     * @access public
     * @return array
     */
    public function getProducts(string $conditions, string $storyType = 'story'): array
    {
        $permission = common::hasPriv('pivot', 'showProduct') || $this->app->user->admin;
        $products   = $this->dao->select('t1.id, t1.code, t1.name, t1.PO')->from(TABLE_PRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROGRAM)->alias('t2')->on('t1.program = t2.id')
            ->where('t1.deleted')->eq('0')
            ->andWhere('t1.shadow')->eq('0')
            ->beginIF(strpos($conditions, 'closedProduct') === false)->andWhere('t1.status')->ne('closed')->fi()
            ->beginIF(!$permission)->andWhere('t1.id')->in($this->app->user->view->products)->fi()
            ->orderBy('t2.order_asc, t1.line_desc, t1.order_asc')
            ->fetchAll('id');

        $plans = $this->dao->select('id, product, branch, parent, title, begin, end')->from(TABLE_PRODUCTPLAN)
            ->where('deleted')->eq('0')
            ->andWhere('product')->in(array_keys($products))
            ->beginIF(strpos($conditions, 'overduePlan') === false)->andWhere('end')->gt(date('Y-m-d'))->fi()
            ->orderBy('product, parent_desc, begin')
            ->fetchAll('id');
        foreach($plans as $plan)
        {
            if($plan->parent > 0)
            {
                $parentPlan = zget($plans, $plan->parent, null);
                if($parentPlan)
                {
                    $products[$plan->product]->plans[$parentPlan->id] = $parentPlan;
                    unset($plans[$parentPlan->id]);
                }
                $plan->title = '>>' . $plan->title;
            }
            $products[$plan->product]->plans[$plan->id] = $plan;
        }

        $planStories      = array();
        $unplannedStories = array();
        $stmt = $this->dao->select('id, plan, product, status')->from(TABLE_STORY)
            ->where('deleted')->eq('0')
            ->andWhere('parent')->ge(0)
            ->beginIF($storyType)->andWhere('type')->eq($storyType)->fi()
            ->query();
        while($story = $stmt->fetch())
        {
            if(empty($story->plan))
            {
                $unplannedStories[$story->id] = $story;
                continue;
            }

            $storyPlans   = array();
            $storyPlans[] = $story->plan;
            if(strpos($story->plan, ',') !== false) $storyPlans = explode(',', trim($story->plan, ','));
            foreach($storyPlans as $planID)
            {
                if(isset($plans[$planID]))
                {
                    $planStories[$story->id] = $story;
                    break;
                }
            }
        }

        foreach($planStories as $story)
        {
            $storyPlans = array();
            $storyPlans[] = $story->plan;
            if(strpos($story->plan, ',') !== false) $storyPlans = explode(',', trim($story->plan, ','));
            foreach($storyPlans as $planID)
            {
                if(!isset($plans[$planID])) continue;
                $plan = $plans[$planID];
                $products[$plan->product]->plans[$planID]->status[$story->status] = isset($products[$plan->product]->plans[$planID]->status[$story->status]) ? $products[$plan->product]->plans[$planID]->status[$story->status] + 1 : 1;
            }
        }

        foreach($unplannedStories as $story)
        {
            $product = $story->product;
            if(isset($products[$product]))
            {
                if(!isset($products[$product]->plans[0]))
                {
                    $products[$product]->plans[0] = new stdClass();
                    $products[$product]->plans[0]->title = $this->lang->pivot->unplanned;
                    $products[$product]->plans[0]->begin = '';
                    $products[$product]->plans[0]->end   = '';
                }
                $products[$product]->plans[0]->status[$story->status] = isset($products[$product]->plans[0]->status[$story->status]) ? $products[$product]->plans[0]->status[$story->status] + 1 : 1;
            }
        }

        unset($products['']);
        return $products;
    }

    /**
     * Get bugs
     *
     * @param  int    $begin
     * @param  int    $end
     * @param  int    $product
     * @param  int    $execution
     * @access public
     * @return array
     */
    public function getBugs($begin, $end, $product, $execution)
    {
        $end       = date('Y-m-d', strtotime("{$end} +1 day"));
        $bugGroups = $this->dao->select("IF(resolution = '', 'unResolved', resolution) AS resolution, openedBy, status")->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->andWhere('openedDate')->ge($begin)
            ->andWhere('openedDate')->le($end)
            ->beginIF($product)->andWhere('product')->eq($product)->fi()
            ->beginIF($execution)->andWhere('execution')->eq($execution)->fi()
            ->fetchGroup('openedBy');

        $bugs = array();
        foreach($bugGroups as $account => $userBugs)
        {
            $bug = array();
            $bug['openedBy']   = $account;
            $bug['unResolved'] = 0;
            $bug['validRate']  = 0;
            $bug['total']      = 0;

            foreach(array_keys($this->lang->bug->resolutionList) as $resolution)
            {
                if($resolution) $bug[$resolution] = 0;
            }

            $resolvedCount = 0;
            $validCount    = 0;
            foreach($userBugs as $userBug)
            {
                if(!isset($bug[$userBug->resolution])) continue;

                $bug[$userBug->resolution]++;
                $bug['total']++;

                if($userBug->status == 'resolved' || $userBug->status == 'closed') $resolvedCount++;
                if($userBug->resolution == 'fixed' || $userBug->resolution == 'postponed') $validCount++;
            }

            if(!$bug['total']) continue;

            $bug['validRate'] = $resolvedCount ? round($validCount / $resolvedCount * 100, 2) . '%' : '0%';

            $bugs[] = $bug;
        }

        uasort($bugs, 'sortSummary');

        return $bugs;
    }

    /**
     * Get workload.
     *
     * @param  int    $dept
     * @param  string $assign   assign|noassign
     * @param  array  $users
     * @param  float  $allHour
     * @access public
     * @return array
     */
    public function getWorkload(int $dept, string $assign, array $users, float $allHour): array
    {
        $deptUsers = array();
        if($dept)
        {
            $deptUsers = $this->loadModel('dept')->getDeptUserPairs($dept);
            if(!$deptUsers) return array();
        }

        $canViewExecution = common::hasPriv('execution', 'view');

        if($assign == 'noassign')
        {
            $executions = $this->dao->select('t1.account AS user, t2.multiple, t2.id AS executionID, t2.name AS executionName, t3.id AS projectID, t3.name AS projectName')->from(TABLE_TEAM)->alias('t1')
                ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t2.id = t1.root')
                ->leftJoin(TABLE_PROJECT)->alias('t3')->on('t3.id = t2.project')
                ->where('t1.type')->eq('execution')
                ->andWhere('t1.account NOT IN (SELECT DISTINCT `assignedTo` FROM ' . TABLE_TASK . " WHERE `execution` = t1.`root` AND `status` NOT IN ('cancel', 'closed', 'done', 'pause') AND assignedTo != '')")
                ->andWhere('t2.deleted')->eq('0')
                ->andWhere('t2.status')->notin('cancel, closed, done, suspended')
                ->beginIF($deptUsers)->andWhere('t1.account')->in(array_keys($deptUsers))->fi()
                ->fetchAll();
            if(empty($executions)) return array();

            $executionGroups = array();
            foreach($executions as $execution)
            {
                if(!isset($users[$execution->user])) continue;
                $executionGroups[$execution->user][$execution->projectID][$execution->executionID] = $execution;
            }

            $workload = array();
            foreach($executionGroups as $account => $projects)
            {
                if(!isset($users[$account])) continue;

                $totalExecutions = 0;
                foreach($projects as $executions) $totalExecutions += count($executions);

                $userFirstRow = true;
                foreach($projects as $executions)
                {
                    $projectFirstRow = true;
                    foreach($executions as $execution)
                    {
                        $execution->executionTasks = 0;
                        $execution->executionHours = 0;
                        $execution->totalTasks     = 0;
                        $execution->totalHours     = 0;
                        $execution->workload       = '0%';

                        if($execution->multiple)
                        {
                            $execution->executionName = $canViewExecution ? html::a(helper::createLink('execution', 'view', "executionID={$execution->executionID}"), $execution->executionName) : $execution->executionName;
                        }
                        else
                        {
                            $execution->executionName = $this->lang->null;
                        }

                        if($userFirstRow)    $execution->userRowspan    = $totalExecutions;
                        if($projectFirstRow) $execution->projectRowspan = count($executions);

                        $workload[] = $execution;

                        $userFirstRow    = false;
                        $projectFirstRow = false;
                    }
                }
            }
            return $workload;
        }

        $tasks = $this->dao->select('t1.id, t1.assignedTo AS user, t1.left, t2.multiple, t2.id AS executionID, t2.name AS executionName, t3.id AS projectID, t3.name AS projectName')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution = t2.id')
            ->leftJoin(TABLE_PROJECT)->alias('t3')->on('t3.id = t2.project')
            ->where('t1.deleted')->eq('0')
            ->andWhere('t1.parent')->ge(0)
            ->andWhere('t1.status')->in('wait,pause,doing')
            ->andWhere('t1.assignedTo')->ne('')
            ->beginIF($deptUsers)->andWhere('t1.assignedTo')->in(array_keys($deptUsers))->fi()
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere('t2.status')->in('wait,suspended,doing')
            ->fetchAll();
        if(empty($tasks)) return array();

        $taskGroups = array();
        foreach($tasks as $task)
        {
            if(!isset($users[$task->user])) continue;
            $taskGroups[$task->user][$task->projectID][$task->executionID][$task->id] = $task;
        }

        $teamTasks = $this->dao->select('task, SUM(`left`) AS `left`')->from(TABLE_TASKTEAM)
            ->where('task')->in(array_keys($tasks))
            ->groupBy('task')
            ->fetchPairs('task');

        $workload = array();
        foreach($taskGroups as $projects)
        {
            $totalTasks      = 0;
            $totalHours      = 0;
            $totalExecutions = 0;
            foreach($projects as $executions)
            {
                $totalExecutions += count($executions);

                foreach($executions as $tasks)
                {
                    $totalTasks += count($tasks);
                    foreach($tasks as $task)
                    {
                        if(isset($teamTasks[$task->id])) $task->left = $teamTasks[$task->id]->left;

                        $totalHours += $task->left;
                    }
                }
            }

            $userWorkload = $allHour ? round($totalHours / $allHour * 100, 2) . '%' : '0%';

            $userFirstRow = true;
            foreach($projects as $executions)
            {
                $projectFirstRow = true;
                foreach($executions as $tasks)
                {
                    $execution = current($tasks);
                    $execution->executionTasks = count($tasks);
                    $execution->executionHours = array_sum(array_map(function($task){return $task->left;}, $tasks));
                    $execution->totalTasks     = $totalTasks;
                    $execution->totalHours     = $totalHours;
                    $execution->workload       = $userWorkload;

                    if($execution->multiple)
                    {
                        $execution->executionName = $canViewExecution ? html::a(helper::createLink('execution', 'view', "executionID={$execution->executionID}"), $execution->executionName) : $execution->executionName;
                    }
                    else
                    {
                        $execution->executionName = $this->lang->null;
                    }

                    if($userFirstRow)    $execution->userRowspan    = $totalExecutions;
                    if($projectFirstRow) $execution->projectRowspan = count($executions);

                    $workload[] = $execution;

                    $userFirstRow    = false;
                    $projectFirstRow = false;
                }
            }
        }
        return $workload;
    }

    /**
     * Get bug assign.
     *
     * @access public
     * @return array
     */
    public function getBugAssign()
    {
        $bugGroups = $this->dao->select('product, assignedTo, COUNT(*) AS bugCount')->from(TABLE_BUG)
            ->where('deleted')->eq('0')
            ->andWhere('status')->eq('active')
            ->andWhere('assignedTo')->ne('')
            ->andWhere('assignedTo')->ne('closed')
            ->groupBy('product, assignedTo')
            ->fetchGroup('assignedTo');

        $products = $this->dao->select('id, name')->from(TABLE_PRODUCT)->where('deleted')->eq('0')->fetchPairs();

        $productProjects = $this->dao->select('t2.product, t2.project')->from(TABLE_PROJECT)->alias('t1')
            ->leftJoin(TABLE_PROJECTPRODUCT)->alias('t2')->on('t1.id = t2.project')
            ->where('t1.type')->eq('project')
            ->andWhere('t1.hasProduct')->eq(0)
            ->fetchPairs();

        $canViewProduct = common::hasPriv('product', 'view');
        $canViewProject = common::hasPriv('project', 'view');

        $bugs = array();
        foreach($bugGroups as $userBugs)
        {
            $totalBugs = array_sum(array_map(function($bug){return $bug->bugCount;}, $userBugs));

            $first = true;
            foreach($userBugs as $bug)
            {
                if(!isset($products[$bug->product])) continue;

                $bug->productName = $products[$bug->product];
                if($bug->productName)
                {
                    if($canViewProject && !empty($productProjects[$bug->product]))
                    {
                        $bug->productName = html::a(helper::createLink('project', 'view', "projectID={$productProjects[$bug->product]}"), $bug->productName);
                    }
                    elseif($canViewProduct)
                    {
                        $bug->productName = html::a(helper::createLink('product', 'view', "product={$bug->product}"), $bug->productName);
                    }
                }

                $bug->total = $totalBugs;

                if($first) $bug->rowspan = count($userBugs);

                $bugs[] = $bug;

                $first = false;
            }
        }
        return $bugs;
    }

    /**
     * Get System URL.
     *
     * @access public
     * @return void
     */
    public function getSysURL()
    {
        if(isset($this->config->mail->domain)) return $this->config->mail->domain;

        /* Ger URL when run in shell. */
        if(PHP_SAPI == 'cli')
        {
            $url = parse_url(trim($this->server->argv[1]));
            $port = (empty($url['port']) or $url['port'] == 80) ? '' : $url['port'];
            $host = empty($port) ? $url['host'] : $url['host'] . ':' . $port;
            return $url['scheme'] . '://' . $host;
        }
        else
        {
            return common::getSysURL();
        }
    }

    /**
     * Get user bugs.
     *
     * @access public
     * @return void
     */
    public function getUserBugs()
    {
        return $this->dao->select('t1.id, t1.title, t2.account as user, t1.deadline')
            ->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')
            ->on('t1.assignedTo = t2.account')
            ->where('t1.assignedTo')->ne('')
            ->andWhere('t1.assignedTo')->ne('closed')
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.deadline', true)->eq('0000-00-00')
            ->orWhere('t1.deadline')->lt(date(DT_DATE1, strtotime('+4 day')))
            ->markRight(1)
            ->fetchGroup('user');
    }

    /**
     * Get user tasks.
     *
     * @access public
     * @return void
     */
    public function getUserTasks()
    {
        return $this->dao->select('t1.id, t1.name, t2.account as user, t1.deadline')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.assignedTo = t2.account')
            ->leftJoin(TABLE_EXECUTION)->alias('t3')->on('t1.execution = t3.id')
            ->leftJoin(TABLE_PROJECT)->alias('t4')->on('t1.project = t4.id')
            ->where('t1.assignedTo')->ne('')
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t3.deleted')->eq(0)
            ->andWhere('t4.deleted')->eq(0)
            ->andWhere('t1.status')->in('wait,doing')
            ->andWhere('t3.status')->ne('suspended')
            ->andWhere('t1.deadline', true)->eq('0000-00-00')
            ->orWhere('t1.deadline')->lt(date(DT_DATE1, strtotime('+4 day')))
            ->markRight(1)
            ->fetchGroup('user');
    }

    /**
     * Get user todos.
     *
     * @access public
     * @return array
     */
    public function getUserTodos()
    {
        $stmt = $this->dao->select('t1.*, t2.account as user')
            ->from(TABLE_TODO)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')
            ->on('t1.account = t2.account')
            ->where('t1.cycle')->eq(0)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t1.status')->in('wait,doing')
            ->query();

        $todos = array();
        while($todo = $stmt->fetch())
        {
            if($todo->type == 'task') $todo->name = $this->dao->findById($todo->objectID)->from(TABLE_TASK)->fetch('name');
            if($todo->type == 'bug')  $todo->name = $this->dao->findById($todo->objectID)->from(TABLE_BUG)->fetch('title');
            $todos[$todo->user][] = $todo;
        }
        return $todos;
    }

    /**
     * Get user testTasks.
     *
     * @access public
     * @return array
     */
    public function getUserTestTasks()
    {
        return $this->dao->select('t1.*, t2.account as user')->from(TABLE_TESTTASK)->alias('t1')
            ->leftJoin(TABLE_USER)->alias('t2')->on('t1.owner = t2.account')
            ->where('t1.deleted')->eq('0')
            ->andWhere('t2.deleted')->eq('0')
            ->andWhere("(t1.status='wait' OR t1.status='doing')")
            ->fetchGroup('user');
    }

    /**
     * Get user login count in this year.
     *
     * @param  array  $accounts
     * @param  int    $year
     * @access public
     * @return int
     */
    public function getUserYearLogins($accounts, $year)
    {
        return $this->dao->select('count(*) as count')->from(TABLE_ACTION)->where('actor')->in($accounts)->andWhere('LEFT(date, 4)')->eq($year)->andWhere('action')->eq('login')->fetch('count');
    }

    /**
     * Get user action count in this year.
     *
     * @param  array  $accounts
     * @param  int    $year
     * @access public
     * @return int
     */
    public function getUserYearActions($accounts, $year)
    {
        return $this->dao->select('count(*) as count')->from(TABLE_ACTION)
            ->where('LEFT(date, 4)')->eq($year)
            ->beginIF($accounts)->andWhere('actor')->in($accounts)->fi()
            ->fetch('count');
    }

    /**
     * Get user contributions in this year.
     *
     * @param  array  $accounts
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getUserYearContributions($accounts, $year)
    {
        $stmt = $this->dao->select('*')->from(TABLE_ACTION)
            ->where('LEFT(date, 4)')->eq($year)
            ->andWhere('objectType')->in(array_keys($this->config->pivot->annualData['contributions']))
            ->beginIF($accounts)->andWhere('actor')->in($accounts)->fi()
            ->orderBy('objectType,objectID,id')
            ->query();

        $filterActions = array();
        $objectIdList  = array();
        while($action = $stmt->fetch())
        {
            $objectType  = $action->objectType;
            $objectID    = $action->objectID;
            $lowerAction = strtolower($action->action);
            if(!isset($this->config->pivot->annualData['contributions'][$objectType][$lowerAction])) continue;

            $objectIdList[$objectType][$objectID] = $objectID;
            $filterActions[$objectType][$objectID][$action->id] = $action;
        }

        foreach($objectIdList as $objectType => $idList)
        {
            $deletedIdList = $this->dao->select('id')->from($this->config->objectTables[$objectType])->where('deleted')->eq(1)->andWhere('id')->in($idList)->fetchPairs('id', 'id');
            foreach($deletedIdList as $id) unset($filterActions[$objectType][$id]);
        }

        $actionGroups = array();
        foreach($filterActions as $objectType => $objectActions)
        {
            foreach($objectActions as $objectID => $actions)
            {
                foreach($actions as $action) $actionGroups[$objectType][$action->id] = $action;
            }
        }

        $contributions = array();
        foreach($actionGroups as $objectType => $actions)
        {
            foreach($actions as $action)
            {
                $lowerAction = strtolower($action->action);
                $actionName  = $this->config->pivot->annualData['contributions'][$objectType][$lowerAction];

                $type = ($actionName == 'svnCommit' or $actionName == 'gitCommit') ? 'repo' : $objectType;
                if(!isset($contributions[$type][$actionName])) $contributions[$type][$actionName] = 0;
                $contributions[$type][$actionName] += 1;
            }
        }

        $contributions['case']['run'] = $this->dao->select('count(*) as count')->from(TABLE_TESTRESULT)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case=t2.id')
            ->where('LEFT(t1.date, 4)')->eq($year)
            ->andWhere('t2.deleted')->eq(0)
            ->beginIF($accounts)->andWhere('t1.lastRunner')->in($accounts)->fi()
            ->fetch('count');

        return $contributions;
    }

    /**
     * Get user todo stat in this year.
     *
     * @param  array  $accounts
     * @param  int    $year
     * @access public
     * @return object
     */
    public function getUserYearTodos($accounts, $year)
    {
        return $this->dao->select("count(*) as count, sum(if((`status` != 'done'), 1, 0)) AS `undone`, sum(if((`status` = 'done'), 1, 0)) AS `done`")->from(TABLE_TODO)
            ->where('LEFT(date, 4)')->eq($year)
            ->andWhere('deleted')->eq('0')
            ->andWhere('vision')->eq($this->config->vision)
            ->beginIF($accounts)->andWhere('account')->in($accounts)->fi()
            ->fetch();
    }

    /**
     * Get user effort stat in this error.
     *
     * @param  array  $accounts
     * @param  int    $year
     * @access public
     * @return object
     */
    public function getUserYearEfforts($accounts, $year)
    {
        $effort = $this->dao->select('count(*) as count, sum(consumed) as consumed')->from(TABLE_EFFORT)
            ->where('LEFT(date, 4)')->eq($year)
            ->andWhere('deleted')->eq(0)
            ->beginIF($accounts)->andWhere('account')->in($accounts)->fi()
            ->fetch();

        $effort->consumed = !empty($effort->consumed) ? round($effort->consumed, 2) : 0;
        return $effort;
    }

    /**
     * Get count of created story,plan and closed story by accounts every product in this year.
     *
     * @param  array  $accounts
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getUserYearProducts($accounts, $year)
    {
        /* Get changed products in this year. */
        $products = $this->dao->select('id,name')->from(TABLE_PRODUCT)
            ->where('deleted')->eq(0)
            ->andWhere('LEFT(createdDate, 4)')->eq($year)
            ->beginIF($accounts)
            ->andWhere('createdBy', true)->in($accounts)
            ->orWhere('PO')->in($accounts)
            ->orWhere('QD')->in($accounts)
            ->orWhere('RD')->in($accounts)
            ->markRight(1)
            ->fi()
            ->andWhere('shadow')->eq(0)
            ->fetchAll('id');

        /* Get created plans in this year. */
        $plans = $this->dao->select('t1.id,t1.product')->from(TABLE_PRODUCTPLAN)->alias('t1')
            ->leftJoin(TABLE_ACTION)->alias('t2')->on("t1.id=t2.objectID and t2.objectType='productplan'")
            ->where('LEFT(t2.date, 4)')->eq($year)
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('t1.product')->in(array_keys($products))
            ->beginIF($accounts)
            ->andWhere('t2.actor')->in($accounts)
            ->fi()
            ->andWhere('t2.action')->eq('opened')
            ->fetchAll();

        $planProducts = array();
        $planGroups   = array();
        foreach($plans as $plan)
        {
            $planProducts[$plan->product] = $plan->product;
            $planGroups[$plan->product][$plan->id] = $plan->id;
        }

        $createStoryProducts = $this->dao->select('DISTINCT product')->from(TABLE_STORY)
            ->where('LEFT(openedDate, 4)')->eq($year)
            ->andWhere('deleted')->eq(0)
            ->andWhere('product')->in(array_keys($products))
            ->beginIF($accounts)->andWhere('openedBy')->in($accounts)->fi()
            ->fetchPairs('product', 'product');
        $closeStoryProducts  = $this->dao->select('DISTINCT product')->from(TABLE_STORY)
            ->where('LEFT(closedDate, 4)')->eq($year)
            ->andWhere('deleted')->eq(0)
            ->andWhere('product')->in(array_keys($products))
            ->beginIF($accounts)->andWhere('closedBy')->in($accounts)->fi()
            ->fetchPairs('product', 'product');
        if($createStoryProducts or $closeStoryProducts)
        {
            $products += $this->dao->select('id,name')->from(TABLE_PRODUCT)
                ->where('id')->in($createStoryProducts + $closeStoryProducts + $planProducts)
                ->andWhere('deleted')->eq(0)
                ->fetchAll('id');
        }

        $createdStoryStats = $this->dao->select("product,sum(if((type = 'requirement'), 1, 0)) as requirement, sum(if((type = 'story'), 1, 0)) as story")->from(TABLE_STORY)
            ->where('product')->in(array_keys($products))
            ->andWhere('deleted')->eq(0)
            ->andWhere('LEFT(openedDate, 4)')->eq($year)
            ->beginIF($accounts)->andWhere('openedBy')->in($accounts)->fi()
            ->groupBy('product')
            ->fetchAll('product');

        $closedStoryStats = $this->dao->select("product,sum(if((status = 'closed'), 1, 0)) as closed")->from(TABLE_STORY)
            ->where('product')->in(array_keys($products))
            ->andWhere('deleted')->eq(0)
            ->andWhere('LEFT(closedDate, 4)')->eq($year)
            ->beginIF($accounts)->andWhere('closedBy')->in($accounts)->fi()
            ->groupBy('product')
            ->fetchAll('product');

        /* Merge created plan, created story and closed story in every product. */
        foreach($products as $productID => $product)
        {
            $product->plan        = 0;
            $product->requirement = 0;
            $product->story       = 0;
            $product->closed      = 0;

            $plans = zget($planGroups, $productID, array());
            if($plans) $product->plan = count($plans);

            $createdStoryStat = zget($createdStoryStats, $productID, '');
            if($createdStoryStat)
            {
                $product->requirement = $createdStoryStat->requirement;
                $product->story       = $createdStoryStat->story;
            }

            $closedStoryStat = zget($closedStoryStats, $productID, '');
            if($closedStoryStat) $product->closed = $closedStoryStat->closed;
        }

        return $products;
    }

    /**
     * Get count of finished task, story and resolved bug by accounts every executions in this year.
     *
     * @param  array  $accounts
     * @param  int    $year
     * @access public
     * @return array
     */
    public function getUserYearExecutions($accounts, $year)
    {
        /* Get changed executions in this year. */
        $executions = $this->dao->select('id,name')->from(TABLE_EXECUTION)->where('deleted')->eq(0)
            ->andwhere('type')->eq('sprint')
            ->andwhere('multiple')->eq('1')
            ->andWhere('LEFT(begin, 4)', true)->eq($year)
            ->orWhere('LEFT(end, 4)')->eq($year)
            ->markRight(1)
            ->beginIF($accounts)
            ->andWhere('openedBy', true)->in($accounts)
            ->orWhere('PO')->in($accounts)
            ->orWhere('PM')->in($accounts)
            ->orWhere('QD')->in($accounts)
            ->orWhere('RD')->in($accounts)
            ->markRight(1)
            ->fi()
            ->orderBy('`order` desc')
            ->fetchAll('id');

        $teamExecutions = $this->dao->select('t1.root')->from(TABLE_TEAM)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.root=t2.id')
            ->where('t1.type')->eq('execution')
            ->beginIF($accounts)->andWhere('account')->in($accounts)->fi()
            ->andWhere('t2.multiple')->eq('1')
            ->andWhere('LEFT(`join`, 4)')->eq($year)
            ->fetchPairs('root', 'root');

        $taskExecutions = $this->dao->select('t1.execution')->from(TABLE_TASK)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.execution=t2.id')
            ->where('LEFT(t1.finishedDate, 4)')->eq($year)
            ->beginIF($accounts)->andWhere('finishedBy')->in($accounts)->fi()
            ->andWhere('t2.multiple')->eq('1')
            ->andWhere('t1.deleted')->eq(0)
            ->fetchPairs('execution', 'execution');

        if($teamExecutions or $taskExecutions)
        {
            $executions += $this->dao->select('id,name')->from(TABLE_EXECUTION)
                ->where('id')->in($teamExecutions + $taskExecutions)
                ->andWhere('deleted')->eq(0)
                ->andWhere('type')->eq('sprint')
                ->orderBy('`order` desc')
                ->fetchAll('id');
        }

        /* Get count of finished task, story and resolved bug in this year. */
        $taskStats = $this->dao->select('execution, count(*) as finishedTask, sum(if((story != 0), 1, 0)) as finishedStory')->from(TABLE_TASK)
            ->where('execution')->in(array_keys($executions))
            ->andWhere('finishedBy')->ne('')
            ->andWhere('LEFT(finishedDate, 4)')->eq($year)
            ->andWhere('deleted')->eq(0)
            ->beginIF($accounts)->andWhere('finishedBy')->in($accounts)->fi()
            ->groupBy('execution')
            ->fetchAll('execution');
        $resolvedBugs = $this->dao->select('t2.execution, count(*) as count')->from(TABLE_BUG)->alias('t1')
            ->leftJoin(TABLE_BUILD)->alias('t2')->on('t1.resolvedBuild=t2.id')
            ->where('t2.execution')->in(array_keys($executions))
            ->andWhere('t1.resolvedBy')->ne('')
            ->andWhere('t1.deleted')->eq(0)
            ->andWhere('LEFT(t1.resolvedDate, 4)')->eq($year)
            ->beginIF($accounts)->andWhere('t1.resolvedBy')->in($accounts)->fi()
            ->groupBy('t2.execution')
            ->fetchAll('execution');

        foreach($executions as $executionID => $execution)
        {
            $execution->task  = 0;
            $execution->story = 0;
            $execution->bug   = 0;

            $taskStat = zget($taskStats, $executionID, '');
            if($taskStat)
            {
                $execution->task  = $taskStat->finishedTask;
                $execution->story = $taskStat->finishedStory;
            }

            $resolvedBug = zget($resolvedBugs, $executionID, '');
            if($resolvedBug) $execution->bug = $resolvedBug->count;
        }

        return $executions;
    }

    /**
     * Get status stat that is all time, include story, task and bug.
     *
     * @access public
     * @return array
     */
    public function getAllTimeStatusStat()
    {
        $statusStat = array();
        $statusStat['story'] = $this->dao->select('status, count(status) as count')->from(TABLE_STORY)->where('deleted')->eq(0)->andWhere('type')->eq('story')->groupBy('status')->fetchPairs('status', 'count');
        $statusStat['task']  = $this->dao->select('status, count(status) as count')->from(TABLE_TASK)->where('deleted')->eq(0)->groupBy('status')->fetchPairs('status', 'count');
        $statusStat['bug']   = $this->dao->select('status, count(status) as count')->from(TABLE_BUG)->where('deleted')->eq(0)->groupBy('status')->fetchPairs('status', 'count');

        return $statusStat;
    }

    /**
     * Get year object stat, include status and action stat
     *
     * @param  array  $accounts
     * @param  string $year
     * @param  string $objectType   story|task|bug
     * @access public
     * @return array
     */
    public function getYearObjectStat($accounts, $year, $objectType)
    {
        $table = '';
        if($objectType == 'story') $table = TABLE_STORY;
        if($objectType == 'task')  $table = TABLE_TASK;
        if($objectType == 'bug')   $table = TABLE_BUG;
        if(empty($table)) return array();

        $months = $this->getYearMonths($year);
        $stmt   = $this->dao->select('t1.*, t2.status')->from(TABLE_ACTION)->alias('t1')
            ->leftJoin($table)->alias('t2')->on('t1.objectID=t2.id')
            ->where('t1.objectType')->eq($objectType)
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('LEFT(t1.date, 4)')->eq($year)
            ->andWhere('t1.action')->in($this->config->pivot->annualData['monthAction'][$objectType])
            ->beginIF($accounts)->andWhere('t1.actor')->in($accounts)->fi()
            ->query();

        /* Build object action stat and get status group. */
        $statuses   = array();
        $actionStat = array();
        while($action = $stmt->fetch())
        {
            $statuses[$action->objectID] = $action->status;

            $lowerAction = strtolower($action->action);
            /* Story,bug can from feedback and ticket, task can from feedback, boil this action down to opened. */
            if(in_array($lowerAction, array('fromfeedback', 'fromticket'))) $lowerAction = 'opened';
            if(!isset($actionStat[$lowerAction]))
            {
                foreach($months as $month) $actionStat[$lowerAction][$month] = 0;
            }

            $month = substr($action->date, 0, 7);
            $actionStat[$lowerAction][$month] += 1;
        }

        /* Build status stat. */
        $statusStat = array();
        foreach($statuses as $status)
        {
            if(!isset($statusStat[$status])) $statusStat[$status] = 0;
            $statusStat[$status] += 1;
        }

        return array('statusStat' => $statusStat, 'actionStat' => $actionStat);
    }

    /**
     * Get year case stat, include result and action stat.
     *
     * @param  array  $accounts
     * @param  string $year
     * @access public
     * @return array
     */
    public function getYearCaseStat($accounts, $year)
    {
        $months = $this->getYearMonths($year);
        $stmt   = $this->dao->select('t1.*')->from(TABLE_ACTION)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.objectID=t2.id')
            ->where('t1.objectType')->eq('case')
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('t1.action')->eq('opened')
            ->andWhere('LEFT(t1.date, 4)')->eq($year)
            ->beginIF($accounts)->andWhere('t1.actor')->in($accounts)->fi()
            ->query();

        /* Build create case stat. */
        $resultStat = array();
        $actionStat = array();
        foreach($months as $month)
        {
            $actionStat['opened'][$month]    = 0;
            $actionStat['run'][$month]       = 0;
            $actionStat['createBug'][$month] = 0;
        }

        while($action = $stmt->fetch())
        {
            $month = substr($action->date, 0, 7);
            $actionStat['opened'][$month] += 1;
        }

        /* Build testcase result stat and run case stat. */
        $stmt = $this->dao->select('t1.*')->from(TABLE_TESTRESULT)->alias('t1')
            ->leftJoin(TABLE_CASE)->alias('t2')->on('t1.case=t2.id')
            ->where('LEFT(t1.date, 4)')->eq($year)
            ->andWhere('t2.deleted')->eq(0)
            ->beginIF($accounts)->andWhere('t1.lastRunner')->in($accounts)->fi()
            ->query();
        while($testResult = $stmt->fetch())
        {
            if(!isset($resultStat[$testResult->caseResult])) $resultStat[$testResult->caseResult] = 0;
            $resultStat[$testResult->caseResult] += 1;

            $month = substr($testResult->date, 0, 7);
            $actionStat['run'][$month] += 1;
        }

        /* Build testcase create bug stat. */
        $stmt = $this->dao->select('t1.*')->from(TABLE_ACTION)->alias('t1')
            ->leftJoin(TABLE_BUG)->alias('t2')->on('t1.objectID=t2.id')
            ->where('t1.objectType')->eq('bug')
            ->andWhere('t2.deleted')->eq(0)
            ->andWhere('LEFT(t1.date, 4)')->eq($year)
            ->andWhere('t1.action')->eq('opened')
            ->andWhere('t2.case')->ne('0')
            ->beginIF($accounts)->andWhere('t1.actor')->in($accounts)->fi()
            ->query();
        while($action = $stmt->fetch())
        {
            $month = substr($action->date, 0, 7);
            $actionStat['createBug'][$month] += 1;
        }

        return array('resultStat' => $resultStat, 'actionStat' => $actionStat);
    }

    /**
     * Get year months.
     *
     * @param  string $year
     * @access public
     * @return array
     */
    public function getYearMonths($year)
    {
        $months = array();
        for($i = 1; $i <= 12; $i ++) $months[] = $year . '-' . sprintf('%02d', $i);

        return $months;
    }

    /**
     * Get project and execution name.
     *
     * @access public
     * @return array
     */
    public function getProjectExecutions()
    {
        $executions = $this->dao->select('t1.id, t1.name, t2.name as projectname, t1.status, t1.multiple')
            ->from(TABLE_EXECUTION)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project=t2.id')
            ->where('t1.deleted')->eq(0)
            ->andWhere('t1.type')->in('stage,sprint')
            ->fetchAll();

        $pairs = array();
        foreach($executions as $execution)
        {
            if($execution->multiple)  $pairs[$execution->id] = $execution->projectname . '/' . $execution->name;
            if(!$execution->multiple) $pairs[$execution->id] = $execution->projectname;
        }

        return $pairs;
    }

    /**
     * Get filter format.
     *
     * @param  string $sql
     * @param  array  $filters
     * @access public
     * @return string
     */
    public function getFilterFormat($sql, $filters)
    {
        $filterFormat = array();
        if(empty($filters)) return array($sql, $filterFormat);

        foreach($filters as $filter)
        {
            $field = $filter['field'];

            if(isset($filter['from']) and $filter['from'] == 'query')
            {
                $queryDefault = isset($filter['default']) ? $this->processDateVar($filter['default']) : '';
                $sql          = str_replace('$' . $filter['field'], "'{$queryDefault}'", $sql);
            }
            else
            {
                if(!isset($filter['default'])) continue;

                $default = $filter['default'];
                switch($filter['type'])
                {
                    case 'select':
                        if(empty($default)) break;
                        if(is_array($default)) $default = array_filter($default, function($val){return trim($val) != '';});
                        $value = "('" . implode("', '", $default) . "')";
                        $filterFormat[$field] = array('operator' => 'IN', 'value' => $value);
                        break;
                    case 'input':
                        $filterFormat[$field] = array('operator' => 'like', 'value' => "'%$default%'");
                        break;
                    case 'date':
                    case 'datetime':
                        $begin = $default['begin'];
                        $end   = $default['end'];

                        if(!empty($begin) and empty($end))  $filterFormat[$field] = array('operator' => '>', 'value' => "'$begin'");
                        if(empty($begin) and !empty($end))  $filterFormat[$field] = array('operator' => '<', 'value' => "'$end'");
                        if(!empty($begin) and !empty($end)) $filterFormat[$field] = array('operator' => 'BETWEEN', 'value' => "'$begin' and '$end'");
                        break;
                }
            }
        }
        return array($sql, $filterFormat);
    }

    /**
     * Gen sheet.
     *
     * @param  array  $fields
     * @param  array  $settings
     * @param  string $sql
     * @param  array  $filters
     * @param  array  $langs
     * @access public
     * @return array
     */
    public function genSheet($fields, $settings, $sql, $filters, $langs = array())
    {
        $groups    = array();
        $sqlGroups = array();

        foreach($settings as $key => $value)
        {
            if(strpos($key, 'group') !== false && $value) $groups[] = $value;
        }

        $groups = array_unique($groups);

        /* Add tt for sql. */
        foreach($groups as $key => $group) $sqlGroups[$key] = (!empty($settings['filterType']) and $settings['filterType'] == 'query') ? "`$group`" : "tt.`$group`";

        $groupList = implode(',', $sqlGroups);

        $cols       = array();
        $rows       = array();
        $configs    = array();
        $slices     = array();
        $clientLang = $this->app->getClientLang();
        /* Build cols. */
        foreach($groups as $group)
        {
            $col = new stdclass();
            $col->name    = $group;
            $col->isGroup = true;

            $fieldObject  = $fields[$group]['object'];
            $relatedField = $fields[$group]['field'];

            $colLabel = $group;
            if($fieldObject)
            {
                $this->app->loadLang($fieldObject);
                if(isset($this->lang->$fieldObject->$relatedField)) $colLabel = $this->lang->$fieldObject->$relatedField;
            }

            if(isset($langs[$group]) and !empty($langs[$group][$clientLang])) $colLabel = $langs[$group][$clientLang];
            $col->label = $colLabel;

            $cols[0][] = $col;
        }

        /* Replace the variable with the default value. */
        $sql = $this->initVarFilter($filters, $sql);

        /* Create sql. */
        $sql = str_replace(';', '', $sql);

        if(preg_match_all("/[\$]+[a-zA-Z0-9]+/", $sql, $out))
        {
            foreach($out[0] as $match) $sql = str_replace($match, "''", $sql);
        }

        /* Process rows. */
        $connectSQL = '';
        if(!empty($filters) && !isset($filters[0]['from']))
        {
            $wheres = array();
            foreach($filters as $field => $filter)
            {
                $wheres[] = "tt.`$field` {$filter['operator']} {$filter['value']}";
            }

            $whereStr    = implode(' and ', $wheres);
            $connectSQL .= " where $whereStr";
        }

        $groupSQL = " group by $groupList";
        $orderSQL = " order by $groupList";

        $number       = 0;
        $groupsRow    = array();
        $showColTotal = zget($settings, 'columnTotal', 'noShow');
        if(isset($settings['columns']))
        {
            foreach($settings['columns'] as $column)
            {
                $stat   = $column['stat'];
                $field  = $column['field'];
                $slice  = zget($column, 'slice', 'noSlice');
                $uuName = $field . $number;
                $number ++;

                if($stat == 'distinct')
                {
                    $columnSQL = "count(distinct tt.`$field`) as `$uuName`";
                }
                else
                {
                    $columnSQL = "$stat(tt.`$field`) as `$uuName`";
                }

                if($slice != 'noSlice') $columnSQL = "select $groupList,`$slice`,$columnSQL from ($sql) tt" . $connectSQL . $groupSQL . ",tt.`$slice`" . $orderSQL . ",tt.`$slice`";
                if($slice == 'noSlice') $columnSQL = "select $groupList,$columnSQL from ($sql) tt" . $connectSQL . $groupSQL . $orderSQL;

                $columnRows = $this->dao->query($columnSQL)->fetchAll();

                $cols = $this->getTableHeader($columnRows, $column, $fields, $cols, $sql, $langs);
                if($slice != 'noSlice') $columnRows = $this->processSliceData($columnRows, $groups, $slice, $uuName);
                $columnRows = $this->processShowData($columnRows, $groups, $column, $showColTotal, $uuName);

                foreach($columnRows as $key => $row)
                {
                    if(!isset($groupsRow[$key])) $groupsRow[$key] = new stdclass();
                    $groupsRow[$key] = (object)array_merge((array)$groupsRow[$key], (array)$row);
                }
            }
        }

        /* Get configs. */
        foreach($groups as $index => $group)
        {
            if($index == 0)
            {
                $groupRows = array();
                foreach($groupsRow as $row) $groupRows[$row->$group][] = $row;
            }

            $haveNext = isset($groups[$index + 1]);
            $this->getColumnConfig($groupRows, $configs, $groups, $index, $haveNext);
        }

        /* Get group field lang */
        foreach($groups as $group)
        {
            $options = $this->getSysOptions($fields[$group]['type'], $fields[$group]['object'], $fields[$group]['field'], $sql);
            foreach($groupsRow as $row)
            {
                if(isset($row->$group)) $row->$group = zget($options, $row->$group);
            }
        }

        $data              = new stdclass();
        $data->groups      = $groups;
        $data->cols        = $cols;
        $data->array       = json_decode(json_encode($groupsRow), true);
        $data->columnTotal = isset($settings['columnTotal']) ? $settings['columnTotal'] : '';

        /* $data->groups  array 代表分组，最多三个
         * $data->cols    array thead数据，其中对象有三个属性：name：分组，label：列的名字，isGroup：标识是不是分组
         * $data->arrays  array tbody数据, 其中每一个数组内是一行td的数据
         *
         * $configs, eg: array(0 => array(0 => 2, 1 => 1), 2 => array(0 => 2))
         * 代表在整个tbody中，位于[0,0]坐标的td rowspan为2，位于[0,1]坐标的td rowspan为1, 位于[2,0]坐标的td rowspan为2
         */
        return array($data, $configs);
    }

    /**
     * InitVarFilter.
     *
     * @param  string $filters
     * @param  string $sql
     * @access public
     * @return void
     */
    public function initVarFilter($filters = '', $sql = '')
    {
        if(empty($filters)) return $sql;
        foreach($filters as $filter)
        {
            if(empty($filter['from'])) continue;
            $default = isset($filter['default']) ? $filter['default'] : '';
            if(is_array($default))
            {
                $default = array_filter($default, function($val){return !empty($val);});
                $default = implode("', '", $default);
            }
            $sql  = str_replace('$' . $filter['field'], "'{$default}'", $sql);
        }

        if(preg_match_all("/[\$]+[a-zA-Z0-9]+/", $sql, $out))
        {
            foreach($out[0] as $match) $sql = str_replace($match, "''", $sql);
        }
        return $sql;
    }

    /**
     * Get column config to merge table cell.
     *
     * @param  array $groupRows
     * @param  array $configs
     * @param  array $groups
     * @param  int   $index
     * @access public
     * @return void
     *
     * Init $groupRows like this: array('people1' => [0 => ['create' => 'people1', 'product' => 'product1']], 'people2' => ['create' => 'people2', 'product' => 'product2']])
     * The second time the function is executed, groupRows is passed in array('people1_product1' => [0 => ['create' => 'people1', 'product' => 'product1']], 'people2_product2' => ['create' => 'people2', 'product' => 'product2']])
     *
     * The key value of this array is unique;
     */
    public function getColumnConfig(&$groupRows, &$configs, $groups, $index, $haveNext)
    {
        $newRows = array();
        $start = 1;
        $next  = $index + 1;
        foreach($groupRows as $key => $datas)
        {
            $number = count($datas);

            $configs[$start - 1][$next - 1] = $number;
            $start += $number;

            if($haveNext)
            {
                $nextGroup = $groups[$next];
                foreach($datas as $data)
                {
                    $newKey = $key . '_' . $data->$nextGroup;
                    $newRows[$newKey][] = $data;
                }
            }
        }

        if($haveNext) $groupRows = $newRows;
    }

    /**
     * Get the header of the table.
     *
     * @param  array  $columnRows
     * @param  array  $column
     * @param  array  $fields
     * @param  array  $cols
     * @param  array  $langs
     * @access public
     * @return array
     */
    public function getTableHeader($columnRows, $column, $fields, $cols, $sql, $langs = array())
    {
        $stat       = zget($column, 'stat', '');
        $showMode   = zget($column, 'showMode', 'default');
        $monopolize = $showMode == 'default' ? '' : zget($column, 'monopolize', '');

        $col = new stdclass();
        $col->name    = $column['field'];
        $col->isGroup = false;

        $fieldObject  = $fields[$column['field']]['object'];
        $relatedField = $fields[$column['field']]['field'];

        $colLabel = $column['field'];
        if($fieldObject)
        {
            $this->app->loadLang($fieldObject);
            if(isset($this->lang->$fieldObject->$relatedField)) $colLabel = $this->lang->$fieldObject->$relatedField;
        }

        $clientLang = $this->app->getClientLang();
        if(isset($langs[$column['field']]) and !empty($langs[$column['field']][$clientLang])) $colLabel = $langs[$column['field']][$clientLang];

        $colLabel = str_replace('{$field}', $colLabel, $this->lang->pivot->colLabel);
        $colLabel = str_replace('{$stat}', zget($this->lang->pivot->step2->statList, $stat), $colLabel);
        if($showMode != 'default') $colLabel .= sprintf($this->lang->pivot->colShowMode, zget($this->lang->pivot->step2->showModeList, $showMode));
        $col->label = $colLabel;

        $slice = zget($column, 'slice', 'noSlice');
        if($slice != 'noSlice')
        {
            if(!isset($cols[1]))
            {
                foreach($cols[0] as $colData) $colData->rowspan = '2';
                $cols[1] = array();
            }
            $sliceList = array();
            foreach($columnRows as $rows) $sliceList[$rows->{$slice}] = $rows->{$slice};

            $optionList = $this->getSysOptions($fields[$slice]['type'], $fields[$slice]['object'], $fields[$slice]['field'], $sql);
            foreach($sliceList as $field)
            {
                $childCol = new stdclass();
                $childCol->name    = $field;
                $childCol->isGroup = false;
                $childCol->label   = isset($optionList[$field]) ? $optionList[$field] : $field;
                $childCol->colspan = $monopolize ? 2 : 1;
                $cols[1][] = $childCol;
            }
            $col->colspan = count($sliceList);
            if($monopolize) $col->colspan *= 2;

            if(zget($column, 'showTotal', 'noShow') !== 'noShow')
            {
                $childCol = new stdclass();
                $childCol->name    = 'sum';
                $childCol->isGroup = false;
                $childCol->label   = $this->lang->pivot->step2->total;
                $childCol->colspan = $monopolize ? 2 : 1;
                $cols[1][] = $childCol;
                $col->colspan += $childCol->colspan;
            }

            $cols[0][] = $col;
        }
        else
        {
            $col->rowspan = !isset($cols[1]) ? '1' : '2';
            $col->colspan = $monopolize ? 2 : 1;
            $cols[0][] = $col;
        }

        return $cols;
    }

    /**
     * Process column show mode.
     *
     * @param  array   $columnRows
     * @param  array   $groups
     * @param  array   $column
     * @param  string  $showColTotal
     * @param  int     $uuName
     * @access public
     * @return array
     */
    public function processShowData($columnRows, $groups, $column, $showColTotal, $uuName)
    {
        $slice      = zget($column, 'slice', 'noSlice');
        $showMode   = zget($column, 'showMode', 'default');
        $showTotal  = $slice == 'noSlice' ? 'noShow' : zget($column, 'showTotal', 'noShow');
        $monopolize = $showMode == 'default' ? '' : zget($column, 'monopolize', '');

        $colTotal = array();
        $rowTotal = array();
        $allTotal = 0;
        foreach($columnRows as $index => $row)
        {
            if(!isset($rowTotal[$index])) $rowTotal[$index] = 0;

            foreach($row as $field => $value)
            {
                if(in_array($field, $groups)) continue;
                if(!isset($colTotal[$field])) $colTotal[$field] = 0;

                if($monopolize)
                {
                    if(!isset($colTotal["self_$field"])) $colTotal["self_$field"] = 0;
                    $colTotal["self_$field"] += (float)$value;
                }

                $colTotal[$field] += (float)$value;
                $rowTotal[$index] += (float)$value;
                $allTotal += (float)$value;
            }
        }

        if($showMode == 'total')
        {
            foreach($columnRows as $index => $row)
            {
                $columnRow = new stdclass();
                foreach($row as $field => $value)
                {
                    if(in_array($field, $groups))
                    {
                        $columnRow->$field = $value;
                        continue;
                    }
                    if($monopolize) $columnRow->{"self_$field"} = $value;
                    $columnRow->{$field} = round((float)$value / $allTotal * 100, 2) . '%';
                }
                if($showTotal == 'sum')
                {
                    if($monopolize) $columnRow->{"sum_self_$uuName"} = (float)$rowTotal[$index];
                    $columnRow->{"sum_$uuName"} = round((float)$rowTotal[$index] / $allTotal * 100, 2) . '%';
                }
                $columnRows[$index] = $columnRow;
            }
        }

        if($showMode == 'row')
        {
            foreach($columnRows as $index => $row)
            {
                $columnRow = new stdclass();
                foreach($row as $field => $value)
                {
                    if(in_array($field, $groups))
                    {
                        $columnRow->$field = $value;
                        continue;
                    }
                    if($monopolize) $columnRow->{"self_$field"} = $value;
                    $columnRow->{$field} = round((float)$value / $rowTotal[$index] * 100, 2) . '%';
                }
                if($showTotal == 'sum')
                {
                    if($monopolize) $columnRow->{"sum_self_$uuName"} = (float)$rowTotal[$index];
                    $columnRow->{'sum_' . $uuName} = round((float)$rowTotal[$index] / $rowTotal[$index] * 100, 2) . '%';
                }
                $columnRows[$index] = $columnRow;
            }
        }

        if($showMode == 'column')
        {
            foreach($columnRows as $index => $row)
            {
                $columnRow = new stdclass();
                foreach($row as $field => $value)
                {
                    if(in_array($field, $groups))
                    {
                        $columnRow->$field = $value;
                        continue;
                    }
                    if($monopolize) $columnRow->{"self_$field"} = $value;
                    $columnRow->{$field} = round((float)$value / $colTotal[$field] * 100, 2) . '%';
                }
                if($showTotal == 'sum')
                {
                    if($monopolize) $columnRow->{"sum_self_$uuName"} = (float)$rowTotal[$index];
                    $columnRow->{'sum_' . $uuName} = round((float)$rowTotal[$index] / $allTotal * 100, 2) . '%';
                }
                $columnRows[$index] = $columnRow;
            }
        }

        if($showMode == 'default' and $showTotal == 'sum')
        {
            foreach($columnRows as $index => $row) $row->{'sum_' . $uuName} = $rowTotal[$index];
        }

        if($showColTotal == 'sum')
        {
            if(empty($columnRows)) return $columnRows;

            $colTotalRow = new stdClass();
            foreach($columnRows[0] as $field => $value)
            {
                if(in_array($field, $groups))
                {
                    $colTotalRow->$field = '$togalGroup$';
                }
                else
                {
                    if($showTotal == 'sum' and $field == "sum_$uuName")
                    {
                        if($showMode == 'default')
                        {
                            $colTotalRow->{$field} = $allTotal;
                        }
                        else
                        {
                            $colTotalRow->{$field} = round((float)$allTotal / $allTotal * 100, 2) . '%';
                        }
                        continue;
                    }

                    if(strpos($field, 'sum_self_') !== false)
                    {
                        $colTotalRow->{$field} = $allTotal;
                        continue;
                    }

                    if(strpos($field, 'self_') !== false)
                    {
                        $colTotalRow->{$field} = $colTotal[$field];
                        continue;
                    }

                    if($showMode == 'default') $colTotalRow->$field = $colTotal[$field];
                    if($showMode == 'column')  $colTotalRow->$field = round((float)$colTotal[$field] / $colTotal[$field] * 100, 2) . '%';
                    if(strpos(',total,row,', ",$showMode,") !== false) $colTotalRow->$field = round((float)$colTotal[$field] / $allTotal * 100, 2) . '%';
                }
            }
            $columnRows[] = $colTotalRow;
        }

        return $columnRows;
    }

    /**
     * Process data as slice table data.
     *
     * @param  array  $columnRows
     * @param  array  $groups
     * @param  string $slice
     * @param  string $uuName
     * @access public
     * @return array
     */
    public function processSliceData($columnRows, $groups, $slice, $uuName)
    {
        $sliceList = array();
        foreach($columnRows as $rows) $sliceList[$rows->{$slice}] = $rows->{$slice};

        $index     = 0;
        $sliceRows = array();
        foreach($columnRows as $key => $columnRow)
        {
            $field = $columnRow->$slice . '_slice_' . $uuName;
            $columnRow->$field = $columnRow->$uuName;
            if(!in_array($slice, $groups)) unset($columnRow->{$slice});
            unset($columnRow->{$uuName});

            if($key == 0)
            {
                $index             = $key;
                $sliceRows[$index] = $columnRow;
                continue;
            }
            $sliceFlag = true;
            foreach($groups as $group)
            {
                if($columnRow->{$group} != $sliceRows[$index]->{$group}) $sliceFlag = false;
            }
            if(!$sliceFlag)
            {
                $index ++;
                $sliceRows[$index] = $columnRow;
            }
            else
            {
                $sliceRows[$index]->{$field} = $columnRow->{$field};
            }
        }

        foreach($sliceRows as $key => $row)
        {
            $sliceRow = array();
            foreach($row as $field => $value)
            {
                if(strpos($field, '_slice_' . $uuName) === false) $sliceRow[$field] = $value;
            }
            foreach($sliceList as $field)
            {
                $field = $field . '_slice_' . $uuName;
                $sliceRow[$field] = !empty($row->{$field}) ? $row->{$field} : '';
            }
            $sliceRows[$key] = (object)$sliceRow;
        }

        return $sliceRows;
    }

    /**
     * Get sys options.
     *
     * @param  string $type
     * @param  string $object
     * @param  string $field
     * @access public
     * @return array
     */
    public function getSysOptions($type, $object = '', $field = '', $sql = '')
    {
        $options = array();
        switch($type)
        {
            case 'user':
                $options = $this->loadModel('user')->getPairs();
                break;
            case 'product':
                $options = $this->loadModel('product')->getPairs();
                break;
            case 'project':
                $options = $this->loadModel('project')->getPairsByProgram();
                break;
            case 'execution':
                $options = $this->loadModel('execution')->getPairs();
                break;
            case 'dept':
                $options = $this->loadModel('dept')->getOptionMenu(0);
                break;
            case 'project.status':
                $this->app->loadLang('project');
                $options = $this->lang->project->statusList;
                break;
            case 'option':
                if($field)
                {
                    $path = $this->app->getModuleRoot() . 'dataview' . DS . 'table' . DS . "$object.php";
                    if(is_file($path))
                    {
                        include $path;
                        $options = $schema->fields[$field]['options'];
                    }
                }
                break;
            case 'object':
                if($field)
                {
                    $table = zget($this->config->objectTables, $object, '');
                    if($table) $options = $this->dao->select("id, {$field}")->from($table)->fetchPairs();
                }
                break;
            case 'string':
                if($field)
                {
                    $options = array();
                    if($sql)
                    {
                        $cols = $this->dbh->query($sql)->fetchAll();
                        foreach($cols as $col)
                        {
                            $data = $col->$field;
                            $options[$data] = $data;
                        }
                    }
                }
                break;
        }
        return $options;
    }

    /**
     * Get tree of pivots and groups.
     *
     * @param  int    $groupID
     * @param  string $orderBy
     * @access public
     * @return array
     */
    public function getPreviewPivots($dimensionID, $groupID, $pivotID = 0, $orderBy = 'id_desc')
    {
        if(!$groupID) return array(array(), array(), $groupID);

        $group = $this->loadModel('tree')->getByID($groupID);
        if(empty($group)) return array(array(), array(), $groupID);

        $groups = $this->dao->select('id, name')->from(TABLE_MODULE)
            ->where('deleted')->eq('0')
            ->andWhere('type')->eq('pivot')
            ->beginIF($group->grade == '1')->andWhere('path')->like("%,$group->id,%")->fi()
            ->beginIF($group->grade == '2')->andWhere('path')->like("%,$group->parent,%")->fi()
            ->orderBy('`order`')
            ->fetchPairs();

        if(empty($groups)) return array(array(), array(), $groupID);

        $pivotGroups = array();
        foreach($groups as $id => $groupName)
        {
            $pivotGroups[$id] = $this->dao->select('*')->from(TABLE_PIVOT)
                ->where('deleted')->eq(0)
                ->andWhere("FIND_IN_SET($id, `group`)")
                ->andWhere('stage')->ne('draft')
                ->orderBy($orderBy)
                ->fetchAll();
        }

        if(!$pivotGroups) return array(array(), array(), $groupID);

        $pivotTree = '';
        foreach($pivotGroups as $id => $pivots)
        {
            if(!$pivots) continue;
            $pivots     = $this->processPivot($pivots, false);

            $groupName  = zget($groups, $id, $id);
            $title      = "title='{$groupName}'";
            $pivotTree .= "<li class='closed' $title><a>$groupName</a>";
            $pivotTree .= "<ul>";

            foreach($pivots as $pivot)
            {
                $className  = "pivot-{$id}-{$pivot->id}";
                $params     = helper::safe64Encode("pivotID=$pivot->id");
                $linkHtml   = html::a(helper::createLink('pivot', 'preview', "dimensionID=$dimensionID&group=$id&module=pivot&medhot=show&params=$params"), $pivot->name, '_self', "id='module{$pivot->id}' title='{$pivot->name}'");
                $pivotTree .= "<li class='$className'>$linkHtml</li>";
                if(!$pivotID)
                {
                    $pivotID = $pivot->id;
                    $groupID = $id;
                }
            }
            $pivotTree .= "</ul></li>";
        }

        if($pivotTree) $pivotTree = "<ul id='pivotGroups' class='tree' data-ride='tree'>$pivotTree</ul>";

        $pivot = $pivotID ? $this->getByID($pivotID) : array();

        return array($pivotTree, $pivot, $groupID);
    }

    /**
     * Adjust the action is clickable.
     *
     * @param  object $pivot
     * @param  string $action
     * @static
     * @access public
     * @return bool
     */
    public static function isClickable($pivot, $action)
    {
        if($pivot->builtin) return false;
        return true;
    }

    /**
     * Build table use data and rowspan.
     *
     * @param  object $data
     * @param  array  $configs
     * @param  array  $fields
     * @access public
     * @return void
     *
     */
    public function buildPivotTable($data, $configs, $fields = array(), $sql = '')
    {
        $clientLang  = $this->app->getClientLang();
        $width       = 128;

        /* Init table. */
        $table  = "<table class='reportData table table-condensed table-striped table-bordered table-fixed datatable' style='width: auto; min-width: 100%' data-fixed-left-width='400'>";

        /* Init table thead. */
        $table .= "<thead>";
        foreach($data->cols as $lineCols)
        {
            $table .= "<tr>";
            foreach($lineCols as $col)
            {
                $isGroup = $col->isGroup;
                $thName  = $col->label;
                $colspan = zget($col, 'colspan', 1);
                $rowspan = zget($col, 'rowspan', 1);

                if($isGroup) $thHtml = "<th data-flex='false' rowspan='$rowspan' colspan='$colspan' data-width='auto' class='text-center'>$thName</th>";
                else         $thHtml = "<th data-flex='true' rowspan='$rowspan' colspan='$colspan' data-type='number' data-width=$width class='text-center'>$thName</th>";

                $table .= $thHtml;
            }
            $table .= "</tr>";
        }
        $table .= "</thead>";

        /* Init table tbody. */
        $table .= "<tbody>";
        $rowCount = 0;
        for($i = 0; $i < count($data->array); $i ++)
        {
            $rowCount ++;
            if(!empty($data->columnTotal) and $data->columnTotal === 'sum' and $rowCount == count($data->array)) continue;

            $line   = array_values($data->array[$i]);
            $table .= "<tr class='text-center'>";
            for($j = 0; $j < count($line); $j ++)
            {
                $isGroup = !empty($data->cols[0][$j]) ? $data->cols[0][$j]->isGroup : false;
                $rowspan = isset($configs[$i][$j]) ? $configs[$i][$j] : 1;
                $hidden  = isset($configs[$i][$j]) ? false : (!$isGroup ? false : true);

                $lineValue = $line[$j];
                if($isGroup)
                {
                    $groupName = $data->cols[0][$j]->name;
                }
                if(is_numeric($lineValue)) $lineValue = round($lineValue, 2);

                if(!$hidden) $table .= "<td rowspan='$rowspan'>$lineValue</td>";
            }
            $table .= "</tr>";
        }

        /* Add column total. */
        if(!empty($data->columnTotal) and $data->columnTotal === 'sum' and !empty($data->array))
        {
            $table .= "<tr class='text-center'>";
            $table .= "<td colspan='" . count($data->groups) . "'>{$this->lang->pivot->step2->total}</td>";
            foreach(end($data->array) as $field => $total)
            {
                if(in_array($field, $data->groups)) continue;
                if(is_numeric($total)) $total = round($total, 2);
                $table .= "<td>$total</td>";
            }
            $table .= "</tr>";
        }

        $table .= "</tbody>";
        $table .= "</table>";

        echo $table;
    }

    /**
     * replace defined table names.
     *
     * @param  string $sql
     * @access public
     * @return string
     */
    public function replaceTableNames($sql)
    {
        if(preg_match_all("/TABLE_[A-Z]+/", $sql, $out))
        {
            rsort($out[0]);
            foreach($out[0] as $table)
            {
                if(!defined($table)) continue;
                $sql = str_replace($table, trim(constant($table), '`'), $sql);
            }
        }
        $sql = preg_replace("/= *'\!/U", "!='", $sql);
        return $sql;
    }

}

/**
 * Sort summary
 *
 * @param  array  $pre
 * @param  array  $next
 * @access public
 * @return mixed
 */
function sortSummary($pre, $next)
{
    if($pre['validRate'] == $next['validRate']) return 0;
    return $pre['validRate'] > $next['validRate'] ? -1 : 1;
}
