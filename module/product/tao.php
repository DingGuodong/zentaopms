<?php
declare(strict_types=1);
/**
 * The model file of product module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      chen.tao<chentao@easycorp.ltd>
 * @package     product
 * @link        http://www.zentao.net
 */

class productTao extends productModel
{
    /**
     * 初始化类的时候，根据是否有产品，还有其他条件，判断是否需要跳转到创建产品页面
     * Check locate create product page whether or not, by exist products and other.
     *
     * @param array $products
     * @access protected
     * @return bool
     */
    protected function checkLocateCreate(array $products): bool
    {
        if(!empty($products)) return false;

        $methodName = $this->app->getMethodName();
        if(strpos($this->config->product->skipRedirectMethod, ",{$methodName},") !== false) return false;

        $viewType = $this->app->getViewType();
        if($viewType == 'mhtml') return false;  //If client device is mobile, then not locate.
        if($viewType == 'json' or (defined('RUN_MODE') and RUN_MODE == 'api')) return false; //If request type is api, then not locate.

        return true;
    }

    /**
     * 从数据表获取符合条件的id=>name的键值对。
     * Fetch pairs like id=>name.
     *
     * @param  string       $mode      all|noclosed
     * @param  int          $programID
     * @param  string|array $append
     * @param  string|int   $shadow    all|0|1
     * @access protected
     * @return int[]
     */
    protected function fetchPairs(string $mode = '', int $programID = 0, string|array $append = '', string|int $shadow = 0): array
    {
        $append = $this->formatAppendParam($append);
        return $this->dao->select("t1.*, IF(INSTR(' closed', t1.status) < 2, 0, 1) AS isClosed")->from(TABLE_PRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROGRAM)->alias('t2')->on('t1.program = t2.id')
            ->where('t1.vision')->eq($this->config->vision)
            ->beginIF($shadow !== 'all')->andWhere('t1.shadow')->eq((int)$shadow)->fi()
            ->andWhere('((1=1')
            ->beginIF(strpos($mode, 'all') === false)->andWhere('t1.deleted')->eq(0)->fi()
            ->beginIF($programID)->andWhere('t1.program')->eq($programID)->fi()
            ->beginIF(strpos($mode, 'noclosed') !== false)->andWhere('t1.status')->ne('closed')->fi()
            ->beginIF(!$this->app->user->admin and $this->config->vision == 'rnd')->andWhere('t1.id')->in($this->app->user->view->products)->fi()
            ->markRight(1)
            ->beginIF($append)->orWhere('(t1.id')->in($append)->markRight(1)->fi()
            ->markRight(1)
            ->orderBy("isClosed,t2.order_asc,t1.line_desc,t1.order_asc")
            ->fetchPairs('id', 'name');
    }

    /**
     * 获取该产品下，所有符合参数条件的关联项目。
     * Fetch all projects link this product.
     *
     * @param  int $productID
     * @param  string $browseType    all|undone|wait|doing|done
     * @param  string $branch
     * @param  string $orderBy
     * @param  object|null $pager
     * @access protected
     * @return array
     */
    protected function fetchAllProductProjects(int $productID, string $browseType = 'all', string $branch = '0', string $orderBy = 'order_desc', object|null $pager = null): array
    {
        return $this->dao->select('t2.*')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t1.product')->eq($productID)
            ->andWhere('t2.type')->eq('project')
            ->beginIF($browseType == 'undone')->andWhere('t2.status')->in('wait,doing')->fi()
            ->beginIF(strpos(",all,undone,", ",$browseType,") === false)->andWhere('t2.status')->eq($browseType)->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('t2.id')->in($this->app->user->view->projects)->fi()
            ->beginIF($branch !== '' and $branch !== 'all')->andWhere('t1.branch')->in($branch)->fi()
            ->andWhere('t2.deleted')->eq('0')
            ->orderBy($orderBy)
            ->page($pager, 't2.id')
            ->fetchAll('id');
    }

    /**
     * 只获取该产品下，我参与的，符合参数条件的关联项目。
     * Fetch involved projects link product.
     *
     * @param  int $productID
     * @param  string $browseType    all|undone|wait|doing|done
     * @param  string $branch
     * @param  string $orderBy
     * @param  object|null $pager
     * @access protected
     * @return array
     */
    protected function fetchInvolvedProductProjects(int $productID, string $browseType = 'all', string $branch = '0', string $orderBy = 'order_desc', object|null $pager = null): array
    {
        return $this->dao->select('t2.*')->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->leftJoin(TABLE_TEAM)->alias('t3')->on('t2.id=t3.root')
            ->leftJoin(TABLE_STAKEHOLDER)->alias('t4')->on('t2.id=t4.objectID')
            ->where('t1.product')->eq($productID)
            ->andWhere('t2.type')->eq('project')
            ->andWhere('t3.type')->eq('project')
            ->beginIF($browseType == 'undone')->andWhere('t2.status')->in('wait,doing')->fi()
            ->beginIF(strpos(",all,undone,", ",$browseType,") === false)->andWhere('t2.status')->eq($browseType)->fi()
            ->beginIF(!$this->app->user->admin)->andWhere('t2.id')->in($this->app->user->view->projects)->fi()
            ->andWhere('t2.openedBy', true)->eq($this->app->user->account)
            ->orWhere('t2.PM')->eq($this->app->user->account)
            ->orWhere('t3.account')->eq($this->app->user->account)
            ->orWhere('(t4.user')->eq($this->app->user->account)
            ->andWhere('t4.deleted')->eq(0)
            ->markRight(1)
            ->orWhere("CONCAT(',', t2.whitelist, ',')")->like("%,{$this->app->user->account},%")
            ->markRight(1)
            ->beginIF($branch !== '' and $branch !== 'all')->andWhere('t1.branch')->in($branch)->fi()
            ->andWhere('t2.deleted')->eq('0')
            ->orderBy($orderBy)
            ->page($pager, 't2.id')
            ->fetchAll('id');
    }

    /**
     * 获取产品ID数组中带有项目集信息的产品分页列表。
     * Get products with program data that in the ID list.
     *
     * @param  array     $productIDs
     * @param  object    $pager
     * @access protected
     * @return array
     */
    protected function getPagerProductsWithProgramIn(array $productIDs, object|null $pager) :array
    {
        return $this->dao->select('t1.*')->from(TABLE_PRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROGRAM)->alias('t2')->on('t1.program = t2.id')
            ->where('t1.id')->in($productIDs)
            ->orderBy('t2.order_asc, t1.line_desc, t1.order_asc')
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * 获取产品ID数组中的产品排序分页列表。
     * Get products in the ID list.
     *
     * @param  array     $productIDs
     * @param  object    $pager
     * @param  string    $orderBy
     * @access protected
     * @return array
     */
    protected function getPagerProductsIn(array $productIDs, object|null $pager, string $orderBy)
    {
        return $this->dao->select('*')->from(TABLE_PRODUCT)
            ->where('id')->in($productIDs)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * 获取在需求列表页面的搜索表单中，模块字段的下拉选项。包括产品、项目下的需求页面。
     * Get modules for search form, in product, project story page.
     *
     * @param  int       $productID
     * @param  array     $products
     * @param  string    $branch
     * @param  int       $projectID
     * @access protected
     * @return array
     */
    protected function getModulesForSearchForm(int $productID, array $products, string $branch = '', int $projectID = 0): array
    {
        $this->loadModel('tree');
        if($this->app->tab != 'project') return $this->tree->getOptionMenu($productID, 'story', 0, $branch);

        if($productID)
        {
            $modules    = array();
            $branchList = $this->loadModel('branch')->getPairs($productID, '', $projectID);
            unset($branchList['all']);

            $branchModuleList = $this->tree->getOptionMenu($productID, 'story', 0, array_keys($branchList));
            foreach($branchModuleList as $branchModules) $modules += $branchModules;
            return $modules;
        }

        /* 在项目需求页面，获取项目关联产品的模块。 */
        $moduleList  = array();
        $modules     = array('/');
        $branchGroup = $this->loadModel('execution')->getBranchByProduct(array_keys($products), $projectID, '');
        foreach($products as $productID => $productName)
        {
            if(isset($branchGroup[$productID]))
            {
                $branchModuleList = $this->tree->getOptionMenu($productID, 'story', 0, array_keys($branchGroup[$productID]));
                foreach($branchModuleList as $branchModules)
                {
                    if(is_array($branchModules)) $moduleList += $branchModules;
                }
            }
            else
            {
                $moduleList = $this->tree->getOptionMenu($productID, 'story', 0, $branch);
            }

            foreach($moduleList as $moduleID => $moduleName)
            {
                if(empty($moduleID)) continue;
                $modules[$moduleID] = $productName . $moduleName;
            }
        }
        return $modules;
    }

    /**
     * 根据产品ID列表和类型，获取统计数据。
     * Get statistic data by product ID list and type.
     *
     * @param  array     $productIdList
     * @param  string    $type
     * @access protected
     * @return array
     */
    protected function getStatisticByType(array $productIdList, string $type): array
    {
        $this->app->loadClass('date', true);
        $weekDate = date::getThisWeek();

        $fields    = 'product,count(*) AS count';
        $tableName = zget($this->config->objectTables, $type, TABLE_BUG);
        if($type == 'plans') $tableName = TABLE_PRODUCTPLAN;
        if($type == 'latestReleases')
        {
            $fields    = 'product, name, date';
            $tableName = TABLE_RELEASE;
        }

        $dao = $this->dao->select($fields)
            ->from($tableName)
            ->where('product')->in($productIdList)
            ->andWhere('deleted')->eq(0)
            ->beginIF($type == 'plans')->andWhere('end')->gt(helper::now())->fi()
            ->beginIF($type == 'assignToNull')->andWhere('assignedto')->eq('')->fi()
            ->beginIF($type == 'fixedBugs')->andWhere('resolution')->eq('fixed')->fi()
            ->beginIF($type == 'unResolved')->orWhere('resolution')->eq('postponed')->fi()
            ->beginIF($type == 'closedBugs' || $type == 'fixedBugs')->andWhere('status')->eq('closed')->fi()
            ->beginIF($type == 'unResolved' || $type == 'activeBugs')->andWhere('status')->eq('active')->fi()
            ->beginIF($type == 'thisWeekBugs')->andWhere('openedDate')->between($weekDate['begin'], $weekDate['end'])->fi()
            ->beginIF($type != 'latestReleases')->groupBy('product')->fi()
            ->beginIF($type == 'latestReleases')->orderBy('id_desc')->fi();

        return $type == 'latestReleases' ? $dao->fetchGroup('product') : $dao->fetchPairs();
    }

    /**
     * 计算在点击1.5级导航下拉选项后，跳转的模块名和方法名。
     * Compute locate for drop menu in PC.
     *
     * @access protected
     * @return array
     */
    protected function computeLocate4DropMenu(): array
    {
        $currentModule = (string)$this->app->moduleName;
        $currentMethod = (string)$this->app->methodName;

        /* Init currentModule and currentMethod for report and story. */
        if($currentModule == 'story')
        {
            if(strpos(",track,create,batchcreate,batchclose,", ",{$currentMethod},") === false) $currentModule = 'product';
            if($currentMethod == 'view' or $currentMethod == 'change' or $currentMethod == 'review') $currentMethod = 'browse';
        }
        if($currentModule == 'testcase' and strpos(',view,edit,', ",$currentMethod,") !== false) $currentMethod = 'browse';
        if($currentModule == 'bug' and $currentMethod == 'edit') $currentMethod = 'browse';
        if($currentMethod == 'report') $currentMethod = 'browse';

        return array($currentModule, $currentMethod);
    }

    /**
     * 格式化append参数，保证输出用逗号间隔的id列表。
     * Format append param.
     *
     * @param string|array append
     * @access protected
     * @return string
     */
    protected function formatAppendParam(string|array $append = ''): string
    {
        if(empty($append)) return '';

        if(is_string($append)) $append = explode(',', $append);

        $append = array_map(function($item){return (int)$item;}, $append);
        $append = array_unique(array_filter($append));
        sort($append);

        return implode(',', $append);
    }

    /**
     * 创建产品线。
     * Create product line.
     *
     * @param int    programID
     * @param string lineName
     * @access protected
     * @return int|false
     */
    protected function createLine(int $programID, string $lineName): int|false
    {
        if($programID <= 0) return false;
        if(empty($lineName)) return false;

        $line = new stdClass();
        $line->type   = 'line';
        $line->parent = 0;
        $line->grade  = 1;
        $line->name   = htmlSpecialString($lineName);
        $line->root   = $programID;

        $existedLineID = (int)$this->dao->select('id')->from(TABLE_MODULE)->where('type')->eq('line')->andWhere('root')->eq($line->root)->andWhere('name')->eq($line->name)->fetch('id');
        if($existedLineID) return $existedLineID;

        $this->dao->insert(TABLE_MODULE)->data($line)->exec();
        if(dao::isError()) return false;

        $lineID = $this->dao->lastInsertID();
        $path   = ",$lineID,";
        $this->dao->update(TABLE_MODULE)->set('path')->eq($path)->set('`order`')->eq($lineID)->where('id')->eq($lineID)->exec();

        return $lineID;
    }

    /**
     * 关联创建产品主库
     * Create main lib for product
     *
     * @param int productID
     * @access protected
     * @return int|false
     */
    protected function createMainLib(int $productID): int|false
    {
        if($productID <= 0) return false;

        $existedLibID = (int)$this->dao->select('id')->from(TABLE_DOCLIB)->where('product')->eq($productID)
            ->andWhere('type')->eq('product')
            ->andWhere('main')->eq('1')
            ->fetch('id');
        if($existedLibID) return $existedLibID;

        $this->app->loadLang('doc');
        $lib = new stdclass();
        $lib->product   = $productID;
        $lib->name      = $this->lang->doclib->main['product'];
        $lib->type      = 'product';
        $lib->main      = '1';
        $lib->acl       = 'default';
        $lib->addedBy   = $this->app->user->account;
        $lib->addedDate = helper::now();
        $this->dao->insert(TABLE_DOCLIB)->data($lib)->exec();

        if(dao::isError())return false;
        return $this->dao->lastInsertID();
    }

    /**
     * 执行SQL，更新到对应的产品信息。
     * Do update this product.
     *
     * @param  object $product
     * @param  int    $productID
     * @param  int    $programID
     * @access protected
     * @return bool
     */
    protected function doUpdate(object $product, int $productID, int $programID): bool
    {
        if(empty($productID)) return false;
        if(count(get_object_vars($product)) == 0) return false;

        $this->dao->update(TABLE_PRODUCT)->data($product)->autoCheck()
            ->checkIF(!empty($product->name), 'name', 'unique', "id != {$productID} and `program` = {$programID} and `deleted` = '0'")
            ->checkIF(!empty($product->code), 'code', 'unique', "id != {$productID} and `deleted` = '0'")
            ->checkFlow()
            ->where('id')->eq($productID)
            ->exec();

        return !dao::isError();
    }

    /**
     * 获取需求列表关联的用例总数。
     * Get cases count of stories.
     *
     * @param  array     $storyIdList
     * @access protected
     * @return int
     */
    protected function getStoryCasesCount(array $storyIdList): int
    {
        if(empty($storyIdList)) return 0;

        return $this->dao->select('COUNT(story) AS count')->from(TABLE_CASE)->where('story')->in($storyIdList)->andWhere('deleted')->eq('0')->fetch('count');
    }

    /**
     * 获取项目关联的产品。
     * Get products by project ID.
     *
     * @param  int          $projectID
     * @param  string|array $append    '1,2,3'
     * @param  string       $status
     * @param  string       $orderBy
     * @param  bool         $withDeleted
     * @access protected
     * @return int[]
     */
    protected function getProductsByProjectID(int $projectID, string|array $append, string $status, string $orderBy, bool $withDeleted = false): array
    {
        /* 处理要用的到变量信息。 */
        $append  = $this->formatAppendParam($append);
        $orderBy = ($orderBy ? "{$orderBy}," : '') . 't2.order asc';

        return $this->dao->select("t1.branch, t1.plan, t2.*")->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PRODUCT)->alias('t2')->on('t1.product = t2.id')
            ->where('(t2.vision')->eq($this->config->vision)
            ->beginIF(!$withDeleted)->andWhere('t2.deleted')->eq(0)->fi()
            ->beginIF(!empty($projectID))->andWhere('t1.project')->eq($projectID)->fi()
            ->beginIF(!$this->app->user->admin and $this->config->vision == 'rnd')->andWhere('t2.id')->in($this->app->user->view->products)->fi()
            ->beginIF(strpos($status, 'noclosed') !== false)->andWhere('t2.status')->ne('closed')->fi()
            ->markRight(1)
            ->beginIF($append)->orWhere('t2.id')->in($append)->fi()
            ->orderBy($orderBy)
            ->fetchAll();
    }

    /**
     * 获取产品路线图的分组数据。
     * Get group roadmap data of product.
     *
     * @param  int       $productID
     * @param  string    $branch    all|0|1
     * @param  int       $count
     * @access protected
     * @return [array, bool]
     */
    protected function getGroupRoadmapData(int $productID, string $branch, int $count): array
    {
        $roadmap = array();
        $return  = false;

        /* Get product plans. */
        $planList = $this->loadModel('productplan')->getList($productID, $branch);

        /* Filter the valid plans, then get the ordered and parents plans. */
        list($orderedPlans, $parentPlans) = $this->filterOrderedAndParentPlans($planList);

        /* Get roadmaps of product plans. */
        list($roadmap, $total, $return) = $this->getRoadmapOfPlans($orderedPlans, $parentPlans, $branch, $count);
        if($return) return array($roadmap, $return);

        /* Get roadmpas of product releases. */
        $releases = $this->loadModel('release')->getList($productID, $branch);
        list($roadmap, $subTotal, $return) = $this->getRoadmapOfReleases($roadmap, $releases, $branch, $count);
        if($return) return array($roadmap, $return);
        $total += $subTotal;

        /* Re-group with branch ID. */
        $groupRoadmap = array();
        foreach($roadmap as $year => $branchRoadmap)
        {
            foreach($branchRoadmap as $branch => $roadmapItems)
            {
                /* Split roadmap items into multiple lines. */
                $totalData = count($roadmapItems);
                $rows      = ceil($totalData / 8);
                $maxPerRow = ceil($totalData / $rows);

                $groupRoadmap[$year][$branch] = array_chunk($roadmapItems, (int)$maxPerRow);
                foreach(array_keys($groupRoadmap[$year][$branch]) as $row) krsort($groupRoadmap[$year][$branch][$row]);
            }
        }

        return array($groupRoadmap, $return);
    }

    /**
     * 过滤有效的产品计划, 并返回所有父级计划。
     * Filter valid product plans.
     *
     * @param  array   $planList
     * @access private
     * @return array[]
     */
    private function filterOrderedAndParentPlans(array $planList): array
    {
        $parentPlans  = array();
        $orderedPlans = array();

        foreach($planList as $planID => $plan)
        {
            /* Collect and remove parent plan. */
            if($plan->parent == '-1')
            {
                $parentPlans[$planID] = $plan->title;
                unset($planList[$planID]);
                continue;
            }

            /* Remove exceeded plan and long-time plan. */
            if((!helper::isZeroDate($plan->end) and $plan->end < date('Y-m-d')) or $plan->end == '2030-01-01') continue;

            $orderedPlans[$plan->end][] = $plan;
        }

        krsort($orderedPlans);

        return array($orderedPlans, $parentPlans);
    }

    /**
     * 获取计划的路线图数据。
     * Get roadmap of plans.
     *
     * @param  array   $orderedPlans
     * @param  array   $parents
     * @param  string  $branch
     * @param  int     $count
     * @access private
     * @return [array, int, bool]
     */
    private function getRoadmapOfPlans(array $orderedPlans, array $parents, string $branch, int $count): array
    {
        $return  = false;
        $total   = 0;
        $roadmap = array();

        foreach($orderedPlans as $plans)
        {
            krsort($plans);
            foreach($plans as $plan)
            {
                /* Attach parent plan. */
                if($plan->parent > 0 and isset($parents[$plan->parent])) $plan->title = $parents[$plan->parent] . ' / ' . $plan->title;

                $year         = substr($plan->end, 0, 4);
                $branchIdList = explode(',', trim($plan->branch, ','));
                $branchIdList = array_unique($branchIdList);
                foreach($branchIdList as $branchID)
                {
                    if($branchID === '') continue;
                    $roadmap[$year][$branchID][] = $plan;
                }
                $total++;

                /* Exceed requested count. */
                if($count > 0 and $total >= $count)
                {
                    $roadmap = $this->processRoadmap($roadmap, $branch);
                    $return  = true;
                    break;
                }
            }
        }

        return array($roadmap, $total, $return);
    }

    /**
     * 获取计划的路线图数据。
     * Get roadmap of plans.
     *
     * @param  array   $roadmap
     * @param  array   $parents
     * @param  string  $branch
     * @param  int     $count
     * @access private
     * @return [array, int, bool]
     */
    private function getRoadmapOfReleases(array $roadmap, array $releases, string $branch, int $count): array
    {
        $total           = 0;
        $return          = false;
        $orderedReleases = array();

        /* Collect releases. */
        foreach($releases as $release) $orderedReleases[$release->date][] = $release;

        krsort($orderedReleases);
        foreach($orderedReleases as $releases)
        {
            krsort($releases);
            foreach($releases as $release)
            {
                $year         = substr($release->date, 0, 4);
                $branchIdList = explode(',', trim($release->branch, ','));
                $branchIdList = array_unique($branchIdList);
                foreach($branchIdList as $branchID)
                {
                    if($branchID === '') continue;
                    $roadmap[$year][$branchID][] = $release;
                }
                $total++;

                /* Exceed required count .*/
                if($count > 0 and $total >= $count)
                {
                    $roadmap = $this->processRoadmap($roadmap, $branch);
                    $return  = true;
                    return array($roadmap, $total, $return);
                }
            }
        }

        if($count > 0)
        {
            $roadmap = $this->processRoadmap($roadmap, $branch);
            $return = true;
        }

        return array($roadmap, $total, $return);
    }

    /**
     * 通过产品ID查询产品关联需求的各状态统计总数。每一个需求状态对应一个数量。
     * Get stories total count of each status by product ID. Every story status has a count value.
     *
     * @param  int       $productID
     * @param  string    $storyType
     * @access protected
     * @return object[]
     */
    protected function getStoryStatusCountByID(int $productID, string $storyType = 'story'): array
    {
        /* 通过产品ID获取每一个需求状态对应的数量。Get count of each story status of the product. */
        $statusCountList = $this->loadModel('story')->getStoriesCountByProductID($productID, $storyType);
        foreach($statusCountList as $status => $stat) $statusCountList[$status] = $stat->count;

        /* 确保每一种需求状态都是有值的。Padding the stories to make sure all status have records. */
        foreach(array_keys($this->lang->story->statusList) as $status)
        {
            if(!isset($statusCountList[$status])) $statusCountList[$status] = 0;
        }

        return $statusCountList;
    }

    /**
     * 通过产品ID查询产品关联的统计数据。
     * Get stat count by product ID.
     *
     * @param  int       $productID
     * @access protected
     * @return int|float
     */
    protected function getStatCountByID(string $tableName, int $productID): int|float
    {
        switch($tableName)
        {
            case TABLE_PRODUCTPLAN:
                /* Get unclosed plans count. */
                return $this->dao->select('COUNT(*) AS count')->from(TABLE_PRODUCTPLAN)->where('deleted')->eq('0')->andWhere('product')->eq("$productID")->andWhere('end')->gt(helper::now())->fetch('count');
            case TABLE_BUILD:
                /* Get builds count. */
                return $this->dao->select('COUNT(*) AS count')->from(TABLE_BUILD)->where('product')->eq("$productID")->andWhere('deleted')->eq('0')->fetch('count');
            case TABLE_CASE:
                /* Get cases count. */
                return $this->dao->select('COUNT(*) AS count')->from(TABLE_CASE)->where('product')->eq("$productID")->andWhere('deleted')->eq('0')->fetch('count');
            case TABLE_BUG:
                /* Get bugs count. */
                return $this->dao->select('COUNT(*) AS count')->from(TABLE_BUG)->where('product')->eq("$productID")->andWhere('deleted')->eq('0')->fetch('count');
            case TABLE_DOC:
                /* Get docs count. */
                return $this->dao->select('COUNT(*) AS count')->from(TABLE_DOC)->where('product')->eq("$productID")->andWhere('deleted')->eq('0')->fetch('count');
            case TABLE_RELEASE:
                /* Get releases count. */
                return $this->dao->select('COUNT(*) AS count')->from(TABLE_RELEASE)->where('deleted')->eq('0')->andWhere('product')->eq("$productID")->fetch('count');
            case TABLE_PROJECTPRODUCT:
                return $this->dao->select('COUNT(*) AS count')
                    ->from(TABLE_PROJECTPRODUCT)->alias('t1')
                    ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
                    ->where('t2.deleted')->eq('0')
                    ->andWhere('t1.product')->eq("$productID")
                    ->andWhere('t2.type')->eq('project')
                    ->fetch('count');
            case 'executions':
                return $this->dao->select('COUNT(*) AS count')
                    ->from(TABLE_PROJECTPRODUCT)->alias('t1')
                    ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
                    ->where('t2.deleted')->eq('0')
                    ->andWhere('t1.product')->eq("$productID")
                    ->andWhere('t2.type')->in('sprint,stage,kanban')
                    ->fetch('count');
            case 'progress':
                $closedTotal = $this->dao->select('COUNT(id) AS count')->from(TABLE_STORY)->where('deleted')->eq('0')->andWhere('product')->eq("$productID")->andWhere('status')->eq('closed')->fetch('count');
                if(empty($closedTotal)) return 0;

                $allTotal = $this->dao->select('COUNT(id) AS count')->from(TABLE_STORY)->where('deleted')->eq('0')->andWhere('product')->eq("$productID")->fetch('count');
                return round($closedTotal / $allTotal * 100, 1);
            default:
                return 0;
        }
    }

    /**
     * 获取产品与项目关联的项目集数据列表。
     * Get the progam info of the releated project and product by product list.
     *
     * @param  array     $productList
     * @access protected
     * @return array
     */
    protected function getProjectProductList(array $productList): array
    {
        $projectProductList = $this->dao->select('t1.product,t1.project,t2.parent,t2.path')
            ->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project=t2.id')
            ->where('t1.product')->in(array_keys($productList))
            ->andWhere('t1.project')->in($this->app->user->view->projects)
            ->andWhere('t2.type')->eq('project')
            ->andWhere('t2.status')->eq('doing')
            ->andWhere('t2.deleted')->eq('0')
            ->fetchGroup('product', 'project');

        if($this->config->systemMode != 'ALM' || $this->config->product->showAllProjects) return $projectProductList;

        /* ALM mode and don't show all projects. */
        foreach($projectProductList as $productID => $projects)
        {
            if(!isset($productList[$productID])) continue;

            $product = $productList[$productID];
            foreach($projects as $projectID => $project)
            {
                if($project->parent == $product->program || strpos($project->path, ",{$product->program},") === 0) continue;
                /* Filter invalid product. */
                unset($projectProductList[$productID][$projectID]);
            }
        }

        return $projectProductList;
    }

    /**
     * 获取产品相关计划列表。
     * Get plan list by product ID list.
     *
     * @param  array     $productIdList
     * @access protected
     * @return array
     */
    protected function getPlanList(array $productIdList): array
    {
        $date = date('Y-m-d');
        return $this->dao->select('id,product,title,parent,begin,end')
            ->from(TABLE_PRODUCTPLAN)
            ->where('product')->in($productIdList)
            ->andWhere('deleted')->eq('0')
            ->andWhere('end')->ge($date)
            ->andWhere('parent')->ne(-1)
            ->orderBy('begin desc')
            ->fetchGroup('product', 'id');
    }

    /**
     * 获取产品相关执行列表。
     * Get execution list by project and  product ID list.
     *
     * @param  array     $projectIdList
     * @param  array     $productIdList
     * @access protected
     * @return array
     */
    protected function getExecutionList(array $projectIdList, array $productIdList): array
    {
        return $this->dao->select('t1.product as productID,t2.*')
            ->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_EXECUTION)->alias('t2')->on('t1.project=t2.id')
            ->where('t2.type')->in('stage,sprint,kanban')
            ->andWhere('t2.project')->in($projectIdList)
            ->beginIF(!$this->app->user->admin)
            ->andWhere('t1.project')->in($this->app->user->view->sprints)
            ->fi()
            ->andWhere('t1.product')->in($productIdList)
            ->andWhere('t2.status')->eq('doing')
            ->andWhere('t2.multiple')->ne('0')
            ->andWhere('t2.deleted')->eq('0')
            ->orderBy('t2.id_desc')
            ->fetchGroup('project', 'id');
    }

    /**
     * 获取产品相关发布列表。
     * Get release list by product ID list.
     *
     * @param  array     $productIdList
     * @access protected
     * @return array
     */
    protected function getReleaseList(array $productIdList): array
    {
        return $this->dao->select('id,product,name,marker')
            ->from(TABLE_RELEASE)
            ->where('deleted')->eq('0')
            ->andWhere('product')->in($productIdList)
            ->andWhere('status')->eq('normal')
            ->fetchGroup('product', 'id');
    }

    /**
     * 获取多个产品下进行中的执行数的键值对。
     * Get K-V pairs of product ID and doing executions count.
     *
     * @param  array     $productIdList
     * @access protected
     * @return array
     */
    protected function getExecutionCountPairs(array $productIdList): array
    {
        return $this->dao->select('t1.product, COUNT(*) AS count')
            ->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t2.deleted')->eq('0')
            ->andWhere('t1.product')->in($productIdList)
            ->andWhere('t2.type')->in('sprint,stage,kanban')
            ->andWhere('t2.status')->eq('doing')
            ->groupBy('t1.product')
            ->fetchPairs('product');
    }

    /**
     * 获取多个产品关联的项目数的键值对。
     * Retrieve key-value pairs of product IDs and their corresponding project counts.
     *
     * @param  array     $productIdList
     * @access protected
     * @return array
     */
    protected function getProjectCountPairs(array $productIdList): array
    {
        return $this->dao->select('t1.product, COUNT(*) AS count')
            ->from(TABLE_PROJECTPRODUCT)->alias('t1')
            ->leftJoin(TABLE_PROJECT)->alias('t2')->on('t1.project = t2.id')
            ->where('t2.deleted')->eq('0')
            ->andWhere('t1.product')->in($productIdList)
            ->andWhere('t2.type')->eq('project')
            ->andWhere('t2.status')->eq('doing')
            ->groupBy('t1.product')
            ->fetchPairs('product');
    }

    /**
     * 通过需求ID列表获取每个需求关联的用例数。
     * Get case count of each story in the story list.
     *
     * @param  int[]     $storyIdList
     * @access protected
     * @return array
     */
    protected function getCaseCountByStoryIdList(array $storyIdList): array
    {
        return $this->dao->select('story, COUNT(*) AS count')
            ->from(TABLE_CASE)
            ->where('deleted')->eq('0')
            ->andWhere('story')->in($storyIdList)
            ->groupBy('story')
            ->fetchPairs('story');
    }

    /**
     * 构建执行键值对，主要处理子阶段的情况。
     * Build execution pairs.
     *
     * @param  array     $executions
     * @param  string    $mode            stagefilter|hasparent
     * @param  bool      $withProjectName
     * @access protected
     * @return array
     */
    protected function buildExecutionPairs(array $executions, string $mode = '', bool $withProjectName = false): array
    {
        $projectIdList = array();
        foreach($executions as $id => $execution) $projectIdList[$execution->project] = $execution->project;

        /* 现在只有阶段有子阶段，所以只查询阶段类型的执行。 */
        $stages      = $this->dao->select('id,name,attribute,parent')->from(TABLE_EXECUTION)->where('type')->eq('stage')->andWhere('project')->in($projectIdList)->andWhere('deleted')->eq('0')->fetchAll('id');
        $stageFilter = str_contains($mode, 'stagefilter');

        /* 根据条件整理数据，将子阶段放到父阶段中。 */
        foreach($executions as $id => $execution)
        {
            if($execution->grade == 2 && isset($stages[$execution->parent]))
            {
                $executionName = (!$withProjectName ? '' : $execution->projectName) . '/' . $stages[$execution->parent]->name . '/' . $execution->name;
                if(isset($executions[$execution->parent]))
                {
                    $executions[$execution->parent]->children[$id] = $executionName;
                    unset($executions[$id]);
                }
            }
        }

        /* Build execution pairs. */
        $this->app->loadLang('project');
        $executionPairs = array();
        foreach($executions as $executionID => $execution)
        {
            if($stageFilter && in_array($execution->attribute, array('request', 'design', 'review'))) continue; // Some stages of waterfall not need.

            if(isset($execution->children))
            {
                $executionPairs = $executionPairs + $execution->children;
                continue;
            }

            $executionPairs[$executionID] = (!$withProjectName ? '' : $execution->projectName) . '/' . $execution->name;
            if(empty($execution->multiple)) $executionPairs[$executionID] = $execution->projectName . "({$this->lang->project->disableExecution})";
        }

        return $executionPairs;
    }

    /**
     * 统计项目集内的产品数据
     * Statistic product data.
     *
     * @param  string    $type line|program
     * @param  array     $programStructure
     * @param  object    $product
     * @access protected
     * @return array
     */
    protected function statisticProductData(string $type, array $programStructure, object|null $product): array
    {
        if(empty($programStructure)) return $programStructure;

        /* Init vars. */
        $data = $type == 'program' ? $programStructure[$product->program] : $programStructure[$product->program][$product->line];
        foreach($this->config->product->statisticFields as $key => $fields)
        {
            /* Get the total number of requirements and stories. */
            if(strpos('stories|requirements', $key) !== false)
            {
                $totalObjects = 0;
                foreach($product->$key as $status => $number) if(isset($this->lang->story->statusList[$status])) $totalObjects += $number;

                $fieldType = $key == 'stories' ? 'Stories' : 'Requirements';
                if(!isset($data['total' . $fieldType])) $data['total' . $fieldType] = 0;
                $data['total' . $fieldType] += $totalObjects;
            }
            elseif($key == 'bugs')
            {
                $fieldType = 'Bugs';
            }

            foreach($fields as $field)
            {
                if(!isset($data[$field])) $data[$field] = 0;

                $status = $field;
                if(strpos($field, 'Requirements') !== false or strpos($field, 'Stories') !== false or $field == 'unResolvedBugs')
                {
                    $length = strpos($field, $fieldType);
                    $status = substr($field, 0, $length);
                }

                if(strpos('requirements|stories', $key) !== false)
                {
                    $objects = $product->$key;
                    $data[$field] += $objects[$status];
                }
                else
                {
                    $data[$field] += $product->$status;
                }
            }
        }

        return $data;
    }
}
