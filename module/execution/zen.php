<?php
declare(strict_types=1);
/**
 * The zen file of execution module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     xxx
 * @link        https://www.zentao.net
 */
class executionZen extends execution
{
    /**
     * 处理版本列表展示数据。
     * Process build list display data.
     *
     * @param  array     $buildList
     * @param  string    $executionID
     * @access protected
     * @return object[]
     */
    protected function processBuildListData(array $buildList, int $executionID = 0): array
    {
        $this->loadModel('build');

        $productIdList = array();
        foreach($buildList as $build) $productIdList[$build->product] = $build->product;

        /* Get branch name. */
        $showBranch   = false;
        $branchGroups = $this->loadModel('branch')->getByProducts($productIdList);
        $builds       = array();
        foreach($buildList as $build)
        {
            $build->branchName = '';
            if(isset($branchGroups[$build->product]))
            {
                $showBranch  = true;
                $branchPairs = $branchGroups[$build->product];
                foreach(explode(',', trim($build->branch, ',')) as $branchID)
                {
                    if(isset($branchPairs[$branchID])) $build->branchName .= "{$branchPairs[$branchID]},";
                }
                $build->branchName = trim($build->branchName, ',');
            }
            $build->actions = $this->build->buildActionList($build, $executionID, 'execution');

            if($build->scmPath && $build->filePath)
            {
                $build->rowspan = 2;

                $buildInfo = clone $build;
                $buildInfo->pathType = 'scmPath';
                $buildInfo->path     = $build->scmPath;
                $builds[]  = $buildInfo;

                $buildInfo = clone $build;
                $buildInfo->pathType = 'filePath';
                $buildInfo->path     = $build->filePath;
                $builds[]  = $buildInfo;
            }
            else
            {
                $build->pathType = empty($build->scmPath) ? 'filePath' : 'scmPath';
                $build->path     = empty($build->scmPath) ? $build->filePath : $build->scmPath;

                $builds[] = $build;
            }
        }

        if(!$showBranch) unset($this->config->build->dtable->fieldList['branch']);
        unset($this->config->build->dtable->fieldList['execution']);

        return $builds;
    }

    /**
     * 构建产品下拉选择数据。
     * Build product drop-down select data.
     *
     * @param  int       $productID
     * @param  object[]  $products
     * @access protected
     * @return array
     */
    protected function buildProductSwitcher(int $productID, array $products)
    {
        $productOption = array();
        $branchOption  = array();
        $programIdList = array();
        if(count($products) > 1) $productOption[0] = $this->lang->product->all;
        foreach($products as $productData) $programIdList[$productData->program] = $productData->program;
        $programPairs = $this->loadModel('program')->getPairsByList($programIdList);
        $linePairs    = $this->loadModel('product')->getLinePairs($programIdList);

        foreach($products as $productData)
        {
            $programName = isset($programPairs[$productData->program]) ? $programPairs[$productData->program] . ' / ' : '';
            $lineName    = isset($linePairs[$productData->line]) ? $linePairs[$productData->line] . ' / ' : '';
            $productOption[$productData->id] = $programName . $lineName . $productData->name;
        }

        $product = $this->product->getById((int)$productID);
        if($product and $product->type != 'normal')
        {
            /* Display status of branch. */
            $branches = $this->branch->getList($productID, $executionID, 'all');
            foreach($branches as $branchInfo)
            {
                $branchOption[$branchInfo->id] = $branchInfo->name . ($branchInfo->status == 'closed' ? ' (' . $this->lang->branch->statusList['closed'] . ')' : '');
            }
        }
        return array($productOption, $branchOption);
    }

    /**
     * 构建执行团队成员信息。
     * Build execution team member information.
     *
     * @param  array  $currentMembers
     * @param  array  $members2Import
     * @param  array  $deptUsers
     * @param  int    $days
     * @access public
     * @return array
     */
    public function buildMembers(array $currentMembers, array $members2Import, array $deptUsers, int $days): array
    {
        $teamMembers = array();
        foreach($currentMembers as $account => $member)
        {
            $member->memberType = 'default';
            $teamMembers[$account] = $member;
        }

        foreach($members2Import as $account => $member2Import)
        {
            $member2Import->memberType = 'import';
            $member2Import->days       = $days;
            $member2Import->limited    = 'no';
            $teamMembers[$account] = $member2Import;
        }

        $roles = $this->loadModel('user')->getUserRoles(array_keys($deptUsers));
        foreach($deptUsers as $deptAccount => $userName)
        {
            if(isset($currentMembers[$deptAccount]) || isset($members2Import[$deptAccount])) continue;

            $deptMember = new stdclass();
            $deptMember->memberType = 'dept';
            $deptMember->account    = $deptAccount;
            $deptMember->role       = zget($roles, $deptAccount, '');
            $deptMember->days       = $days;
            $deptMember->hours      = $this->config->execution->defaultWorkhours;
            $deptMember->limited    = 'no';

            $teamMembers[$deptAccount] = $deptMember;
        }

        for($j = 0; $j < 5; $j ++)
        {
            $newMember = new stdclass();
            $newMember->memberType = 'add';
            $newMember->account    = '';
            $newMember->role       = '';
            $newMember->days       = $days;
            $newMember->hours      = $this->config->execution->defaultWorkhours;
            $newMember->limited    = 'no';

            $teamMembers[] = $newMember;
        }

        return $teamMembers;
    }

    /**
     * 获取打印看板的数据。
     * Get printed kanban data.
     *
     * @param  array     $stories
     * @access protected
     * @return array
     */
    protected function getPrintKanbanData(array $stories): array
    {
        $kanbanTasks = $this->execution->getKanbanTasks($executionID, "id");
        $kanbanBugs  = $this->loadModel('bug')->getExecutionBugs($executionID);

        $users       = array();
        $taskAndBugs = array();
        foreach($kanbanTasks as $task)
        {
            $status  = $task->status;
            $users[] = $task->assignedTo;

            $taskAndBugs[$status]["task{$task->id}"] = $task;
        }
        foreach($kanbanBugs as $bug)
        {
            $status  = $bug->status;
            $status  = $status == 'active' ? 'wait' : ($status == 'resolved' ? ($bug->resolution == 'postponed' ? 'cancel' : 'done') : $status);
            $users[] = $bug->assignedTo;

            $taskAndBugs[$status]["bug{$bug->id}"] = $bug;
        }

        $dataList = array();
        $contents = array('story', 'wait', 'doing', 'done', 'cancel');
        foreach($contents as $content)
        {
            if($content != 'story' and !isset($taskAndBugs[$content])) continue;
            $dataList[$content] = $content == 'story' ? $stories : $taskAndBugs[$content];
        }

        return array($dataList, $users);
    }

    /**
     * 处理打印的看板数据。
     * Process printed Kanban data.
     *
     * @param  int       $executionID
     * @param  array     $dataList
     * @access protected
     * @return array
     */
    protected function processPrintKanbanData(int $executionID, array $dataList): array
    {
        $prevKanbans = $this->execution->getPrevKanban($executionID);
        foreach($dataList as $type => $data)
        {
            if(isset($prevKanbans[$type]))
            {
                $prevData = $prevKanbans[$type];
                foreach($prevData as $id)
                {
                    if(isset($data[$id])) unset($dataList[$type][$id]);
                }
            }
        }

        return $dataList;
    }
}
