<?php
declare(strict_types=1);
class reportTest
{
    public function __construct()
    {
         global $tester;
         $this->objectModel = $tester->loadModel('report');
         $tester->dao->delete()->from(TABLE_ACTION)->where('id')->gt(100)->exec();
    }

    /**
     * 测试计算每项数据的百分比。
     * Test compute percent of every item.
     *
     * @param  array       $datas
     * @access public
     * @return string|array
     */
    public function computePercentTest(array $datas): string|array
    {
        $objects = $this->objectModel->computePercent($datas);

        if(dao::isError()) return dao::getError();

        $percents = '';
        foreach($objects as $moduleID => $object) $percents .= "$moduleID:$object->percent;";
        return $percents;
    }

    /**
     * 测试为单个图表创建json数据。
     * Test create json data of single charts.
     *
     * @param  int          $executionID
     * @access public
     * @return string|array
     */
    public function createSingleJSONTest(int $executionID): string|array
    {
        global $tester;
        $this->execution = $tester->loadModel('execution');

        $execution = $this->execution->getByID($executionID);
        $sets      = $this->execution->getBurnDataFlot($executionID, 'left');
        $dateList  = $this->execution->getDateList($execution->begin, $execution->end, 'noweekend', 0, 'Y-m-d');

        $objects = $this->objectModel->createSingleJSON($sets, $dateList[0]);

        if(dao::isError()) return dao::getError();

        return implode(',', $objects);
    }

    /**
     * 测试转换日期格式。
     * Test convert date format.
     *
     * @param  array        $dateList
     * @param  string       $format
     * @access public
     * @return string|array
     */
    public function convertFormatTest(array $dateList, string $format = 'Y-m-d'): string|array
    {
        $objects = $this->objectModel->convertFormat($dateList, $format);

        if(dao::isError()) return dao::getError();

        return implode(',', $objects);
    }

    /**
     * 测试获取系统的 URL。
     * Test get system URL.
     *
     * @param  string       $domain
     * @param  stringi      $argv1
     * @access public
     * @return string|array
     */
    public function getSysURLTest(string $domain = '', string $argv1 = ''): string|array
    {
        global $tester;
        if(!empty($domain))
        {
            if(!isset($tester->config->mail)) $tester->config->mail = new stdclass();
            $tester->config->mail->domain = $domain;
        }
        else
        {
            unset($tester->config->mail->domain);
        }
        $_SERVER['argv'] = array('argv0', $argv1);

        $url = $this->objectModel->getSysURL();

        unset($tester->config->mail->domain);
        unset($_SERVER['argv']);

        if(dao::isError()) return dao::getError();

        return $url;
    }

    /**
     * Test get executions.
     *
     * @param int $begin
     * @param int $end
     * @access public
     * @return void
     */
    public function getExecutionsTest($begin = 0, $end = 0)
    {
        global $tester;
        $tester->dao->update(TABLE_EXECUTION)->set('`status`')->eq('closed')->where('`id`')->in('101,102,103,107,111,121,151,183')->exec();

        $begin = $begin != 0 ? date('Y-m-d', strtotime(date('Y-m-d') . $begin)) : 0;
        $end   = $end != 0 ? date('Y-m-d', strtotime(date('Y-m-d') . $end)) : 0;
        $objects = $this->objectModel->getExecutions($begin, $end);

        $tester->dao->update(TABLE_EXECUTION)->set('`status`')->eq('wait')->where('`id`')->in('101,103,107,111,121,151,183')->exec();
        $tester->dao->update(TABLE_EXECUTION)->set('`status`')->eq('doing')->where('`id`')->in('102')->exec();

        if(dao::isError()) return dao::getError();

        $executions = '';
        foreach($objects as $executionID => $execution) $executions .= "$executionID:$execution->estimate,$execution->consumed,$execution->projectName;";
        return $executions;
    }

    /**
     * Test get products.
     *
     * @param  string $conditions
     * @param  string $storyType
     * @access public
     * @return string
     */
    public function getProductsTest($conditions, $storyType = 'story')
    {
        $objects = $this->objectModel->getProducts($conditions, $storyType);

        if(dao::isError()) return dao::getError();

        $planCount = 0;
        foreach($objects as $object)
        {
            if(isset($object->plans)) $planCount += count($object->plans);
        }
        return 'product:' . count($objects) . ';plan:' . $planCount;
    }

    /**
     * Test get bugs.
     *
     * @param  string $begin
     * @param  string $end
     * @param  int    $product
     * @param  int    $execution
     * @access public
     * @return array
     */
    public function getBugsTest($begin, $end, $product, $execution)
    {
        $begin = date('Y-m-d', strtotime(date('Y-m-d') . $begin));
        $end   = date('Y-m-d', strtotime(date('Y-m-d') . $end));
        $objects = $this->objectModel->getBugs($begin, $end, $product, $execution);

        if(dao::isError()) return dao::getError();

        $count = array();
        foreach($objects as $user => $types)
        {
            $count[$user] = '';
            foreach($types as $type => $typeCount) $count[$user] .= "$type:$typeCount;";
        }
        return $count;
    }

    /**
     * Test get workload.
     *
     * @param  int    $dept
     * @param  string $assign
     * @access public
     * @return string
     */
    public function getWorkloadTest($dept = 0, $assign = 'assign')
    {
        $objects = $this->objectModel->getWorkload($dept, $assign);

        if(dao::isError()) return dao::getError();

        $workload = '';
        foreach($objects as $user => $work)
        {
            if(strlen($workload) > 40) break;

            $workload .= "$user:";
            foreach($work['total'] as $key => $value) $workload .= "$key:$value,";
            $workload = trim($workload, ',');
            $workload .= ';';
        }
        return $workload;
    }

    /**
     * Test get bug assign.
     *
     * @access public
     * @return void
     */
    public function getBugAssignTest()
    {
        $objects = $this->objectModel->getBugAssign();

        if(dao::isError()) return dao::getError();

        $count = array();
        foreach($objects as $user => $object) $count[$user] = $object['total']['count'];
        return $count;
    }

    /**
     * 测试获取用户的 bugs。
     * Test get user bugs.
     *
     * @access public
     * @return string|array
     */
    public function getUserBugsTest(): string|array
    {
        $objects = $this->objectModel->getUserBugs();

        if(dao::isError()) return dao::getError();

        $counts = '';
        foreach($objects as $user => $bugs) $counts .= "{$user}:" . count($bugs) . ';';
        return $counts;
    }

    /**
     * 测试获取用户的任务。
     * Test get user tasks.
     *
     * @access public
     * @return string|array
     */
    public function getUserTasksTest(): string|array
    {
        $objects = $this->objectModel->getUserTasks();

        if(dao::isError()) return dao::getError();

        $counts = '';
        foreach($objects as $user => $tasks) $counts .= "$user:" . count($tasks) . ';';
        return $counts;
    }

    /**
     * 测试获取用户的待办。
     * Test get user todos.
     *
     * @param  string       $userType
     * @access public
     * @return string|array
     */
    public function getUserTodosTest(string $userType): string|array
    {
        $objects = $this->objectModel->getUserTodos();

        if(dao::isError()) return dao::getError();

        $counts = '';
        foreach($objects as $user => $todos)
        {
            if(strpos($user, $userType) !== false) $counts .= "$user:" . count($todos) . ';';
        }
        return $counts;
    }

    /**
     * 测试获取用户的测试单。
     * Test get user test tasks.
     *
     * @access public
     * @return string|array
     */
    public function getUserTestTasksTest(): string|array
    {
        $objects = $this->objectModel->getUserTestTasks();

        if(dao::isError()) return dao::getError();

        $counts = '';
        foreach($objects as $user => $testtasks) $counts .= "$user:" . count($testtasks) . ';';
        return $counts;
    }

    /**
     * 测试获取当前年的用户登录次数。
     * Test get user login count in this year.
     *
     * @param  string    $accounts
     * @access public
     * @return int|array
     */
    public function getUserYearLoginsTest(array $accounts): int|array
    {
        $count = $this->objectModel->getUserYearLogins($accounts, date('Y'));

        if(dao::isError()) return dao::getError();

        return $count;
    }

    /**
     * 测试获取当前年的用户操作数。
     * Test get user action count in this year.
     *
     * @param  string    $accounts
     * @access public
     * @return int|array
     */
    public function getUserYearActionsTest(array $accounts): int|array
    {
        $count = $this->objectModel->getUserYearActions($accounts, date('Y'));

        if(dao::isError()) return dao::getError();

        return $count;
    }

    /**
     * 测试获取用户某年的动态数。
     * Test get user contributions in this year.
     *
     * @param  array        $accounts
     * @access public
     * @return string|array
     */
    public function getUserYearContributionsTest(array $accounts): string|array
    {
        $objects = $this->objectModel->getUserYearContributions($accounts, date('Y'));

        if(dao::isError()) return dao::getError();

        $contributions = '';
        foreach($objects as $type => $contributionTypes)
        {
            $contributions .= "{$type}:";
            foreach($contributionTypes as $contributionType => $count) $contributions .= "{$contributionType}:{$count},";
            $contributions = trim($contributions, ',') . ';';
        }
        return $contributions;
    }

    /**
     * 测试获取本年度用户的待办统计。
     * Test get user todo stat in this year.
     *
     * @param  array        $accounts
     * @access public
     * @return string|array
     */
    public function getUserYearTodosTest(array $accounts): string|array
    {
        $objects = $this->objectModel->getUserYearTodos($accounts, date('Y'));

        if(dao::isError()) return dao::getError();

        $count = '';
        foreach($objects as $type => $value) $count .= "{$type}:{$value};";
        return $count;
    }

    /**
     * 测试获取本年度用户的工时统计。
     * Test get user effort stat in this error.
     *
     * @param  string $accounts
     * @access public
     * @return object
     */
    public function getUserYearEffortsTest(array $accounts): object|array
    {
        $object = $this->objectModel->getUserYearEfforts($accounts, date('Y'));

        if(dao::isError()) return dao::getError();

        return $object;
    }

    /**
     * 测试获取本年度用户相关的每个产品的创建的需求和计划，关闭的需求数据。
     * Test get count of created story,plan and closed story by accounts every product in this year.
     *
     * @param  array        $accounts
     * @access public
     * @return string|array
     */
    public function getUserYearProductsTest(array $accounts): string|array
    {
        $objects = $this->objectModel->getUserYearProducts($accounts, date('Y'));

        if(dao::isError()) return dao::getError();

        return implode(',', array_keys($objects));
    }

    /**
     * 测试获取本年度用户相关的每个迭代的创建的需求和计划，关闭的需求数据。
     * Test get count of finished task, story and resolved bug by accounts every executions in this years.
     *
     * @param  array        $accounts
     * @access public
     * @return string|array
     */
    public function getUserYearExecutionsTest(array $accounts): string|array
    {
        $year = date('Y');

        $objects = $this->objectModel->getUserYearExecutions($accounts, $year);

        if(dao::isError()) return dao::getError();

        return implode(',', array_keys($objects));
    }

    /**
     * 测试获取所有时间的状态，包括需求、任务和 bug。
     * Test get status stat that is all time, include story, task and bug.
     *
     * @access public
     * @return array
     */
    public function getAllTimeStatusStatTest(): array
    {
        $objects = $this->objectModel->getAllTimeStatusStat();

        if(dao::isError()) return dao::getError();

        $types = array();
        foreach($objects as $type => $status)
        {
            $types[$type] = '';
            foreach($status as $statusType => $statusCount) $types[$type] .= "{$statusType}:{$statusCount};";
        }
        return $types;
    }

    /**
     * 测试获取年度需求、任务或者 bug 的状态统计。
     * Test get year object stat, include status and action stat.
     *
     * @param  array        $accounts
     * @param  string       $objectType
     * @access public
     * @return string|array
     */
    public function getYearObjectStatTest(array $accounts, string $objectType): string|array
    {
        $objects = $this->objectModel->getYearObjectStat($accounts, date('Y'), $objectType);

        if(dao::isError()) return dao::getError();

        $stats = '';
        foreach($objects['statusStat'] as $stat => $count) $stats .= "$stat:$count;";
        return $stats;
    }

    /**
     * 测试获取用例的年度统计，包括结果和操作统计。
     * Test get year case stat, include result and action stat.
     *
     * @param  array        $accounts
     * @access public
     * @return string|array
     */
    public function getYearCaseStatTest(array $accounts): string|array
    {
        $objects = $this->objectModel->getYearCaseStat($accounts, date('Y'));

        if(dao::isError()) return dao::getError();

        $result = '';
        foreach($objects['resultStat'] as $type => $value) $result .= "{$type}:{$value};";
        return $result;
    }

    /**
     * 测试获取本年的月份。
     * Test get year months.
     *
     * @param  string       $year
     * @access public
     * @return string|array
     */
    public function getYearMonthsTest(string $year): string|array
    {
        $objects = $this->objectModel->getYearMonths($year);

        if(dao::isError()) return dao::getError();

        return implode(',', $objects);
    }

    /**
     * 测试获取状态总览。
     * Test get status overview.
     *
     * @param  string       $objectType
     * @param  array        $statusStat
     * @access public
     * @return string|array
     */
    public function getStatusOverviewTest(string $objectType, array $statusStat): string|array
    {
        $return = $this->objectModel->getStatusOverview($objectType, $statusStat);

        if(dao::isError()) return dao::getError();

        return $return;
    }

    /**
     * 测试获取项目状态总览。
     * Test get project status overview.
     *
     * @param  array        $accounts
     * @access public
     * @return string|array
     */
    public function getProjectStatusOverviewTest(array $accounts = array()): string|array
    {
        $objects = $this->objectModel->getProjectStatusOverview($accounts);

        if(dao::isError()) return dao::getError();

        $counts = '';
        foreach($objects as $type => $count) $counts .= "{$type}:{$count};";
        return $counts;
    }

    /**
     * Test get output data for API.
     *
     * @param  string $accounts
     * @param  string $year
     * @access public
     * @return string
     */
    public function getOutput4APITest($accounts)
    {
        $year = date('Y');
        $objects = $this->objectModel->getOutput4API($accounts, $year);

        if(dao::isError()) return dao::getError();

        $output = '';
        foreach($objects as $objectType => $object) $output .= "$objectType:" . $object['total'] . ";";
        return $output;
    }

    /**
     * Test get project and execution name.
     *
     * @param  bool   $count
     * @access public
     * @return int|array
     */
    public function getProjectExecutionsTest($count = false)
    {
        $objects = $this->objectModel->getProjectExecutions();

        if(dao::isError()) return dao::getError();

        return $count ? count($objects) : $objects;
    }
}
