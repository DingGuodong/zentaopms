<?php
/**
 * The model file of test suite module of ZenTaoPMS.
 *
 * @copyright Copyright 2009-2015 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license   ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author    Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package   testsuite
 * @link      http://www.zentao.net
 */
?>
<?php
class testsuiteModel extends model
{
    /**
     * 创建一个测试套件。
     * Create a test suite.
     *
     * @param  object   $suite
     * @access public
     * @return bool|int
     */
    public function create(object $suite): bool|int
    {
        $this->dao->insert(TABLE_TESTSUITE)->data($suite)
            ->batchcheck($this->config->testsuite->create->requiredFields, 'notempty')
            ->checkFlow()
            ->exec();

        if(dao::isError()) return false;

        $suiteID = $this->dao->lastInsertID();
        $this->loadModel('action')->create('testsuite', $suiteID, 'opened');
        return $suiteID;
    }

    /**
     * 获取一个产品下的测试套件。
     * Get test suites of a product.
     *
     * @param  int    $productID
     * @param  string $orderBy
     * @param  object $pager
     * @param  string $param
     * @access public
     * @return array
     */
    public function getSuites(int $productID, string $orderBy = 'id_desc', object $pager = null, string $param = ''): array
    {
        return $this->dao->select('*')->from(TABLE_TESTSUITE)
            ->where('product')->eq($productID)
            ->beginIF($this->lang->navGroup->testsuite != 'qa')->andWhere('project')->eq($this->session->project)->fi()
            ->andWhere('deleted')->eq('0')
            ->beginIF(strpos($param, 'all') === false)->andWhere("(`type` = 'public' OR (`type` = 'private' AND `addedBy` = '{$this->app->user->account}'))")->fi()
            ->beginIF(strpos($param, 'review') !== false)->andWhere("FIND_IN_SET('{$this->app->user->account}', `reviewers`)")->fi()
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * 获取一个产品下的测试套件对。
     * Get test suites pairs of a product.
     *
     * @param  int    $productID
     * @access public
     * @return array
     */
    public function getSuitePairs(int $productID): array
    {
        return $this->dao->select('id, name')->from(TABLE_TESTSUITE)
            ->where('product')->eq($productID)
            ->beginIF($this->lang->navGroup->testsuite != 'qa')->andWhere('project')->eq($this->session->project)->fi()
            ->andWhere('deleted')->eq('0')
            ->andWhere("(`type` = 'public' OR (`type` = 'private' AND `addedBy` = '{$this->app->user->account}'))")
            ->orderBy('id_desc')
            ->fetchPairs();
    }

    /**
     * 获取单元测试的套件。
     * Get suites of unit test.
     *
     * @param  int    $productID
     * @param  string $orderBy
     * @access public
     * @return array
     */
    public function getUnitSuites(int $productID, string $orderBy = 'id_desc'): array
    {
        return $this->dao->select('*')->from(TABLE_TESTSUITE)
            ->where('product')->eq($productID)
            ->andWhere('deleted')->eq('0')
            ->andWhere('type')->eq('unit')
            ->orderBy($orderBy)
            ->fetchAll('id');
    }

    /**
     * 通过id获取测试套件的详情。
     * Get test suite info by id.
     *
     * @param  int   $suiteID
     * @param  bool  $setImgSize
     * @access public
     * @return object|bool
     */
    public function getById(int $suiteID, bool $setImgSize = false): object|bool
    {
        $suite = $this->dao->select('*')->from(TABLE_TESTSUITE)->where('id')->eq((int)$suiteID)->fetch();
        if(!$suite) return false;

        $suite = $this->loadModel('file')->replaceImgURL($suite, 'desc');
        if($setImgSize) $suite->desc = $this->file->setImgSize($suite->desc);
        return $suite;
    }

    /**
     * 更新一个测试套件。
     * Update a test suite.
     *
     * @param  object $suite
     * @param  string $uid
     * @access public
     * @return array|bool
     */
    public function update(object $suite, string $uid): array|bool
    {
        $oldSuite = $this->dao->select('*')->from(TABLE_TESTSUITE)->where('id')->eq((int)$suite->id)->fetch();
        $suite    = $this->loadModel('file')->processImgURL($suite, $this->config->testsuite->editor->edit['id'], $uid);

        $this->dao->update(TABLE_TESTSUITE)->data($suite)
            ->autoCheck()
            ->batchcheck($this->config->testsuite->edit->requiredFields, 'notempty')
            ->checkFlow()
            ->where('id')->eq((int)$suite->id)
            ->exec();

        if(dao::isError()) return false;

        $this->file->updateObjectID($uid, $suite->id, 'testsuite');
        return common::createChanges($oldSuite, $suite);
    }

    /**
     * 关联测试用例。
     * Link cases.
     *
     * @param  int    $suiteID
     * @param  array  $cases
     * @param  array  $versions
     * @access public
     * @return bool
     */
    public function linkCase(int $suiteID, array $cases, array $versions): bool
    {
        if(empty($cases)) return false;

        $suiteCase = new stdclass();
        $suiteCase->suite = $suiteID;
        foreach($cases as $case)
        {
            $suiteCase->case    = $case;
            $suiteCase->version = zget($versions, $case, 0);
            $this->dao->replace(TABLE_SUITECASE)->data($suiteCase)->exec();
        }
        return !dao::isError();
    }

    /**
     * 获取套件下关联的测试用例。
     * Get linked cases of the suite.
     *
     * @param  int    $suiteID
     * @param  string $orderBy
     * @param  object $pager
     * @param  bool   $append
     * @access public
     * @return array
     */
    public function getLinkedCases(int $suiteID, string $orderBy = 'id_desc', object $pager = null, bool $append = true): array
    {
        $suite = $this->getById($suiteID);
        if(!$suite) return array();

        $cases = $this->dao->select('t1.*, t2.version AS caseVersion, t2.suite')->from(TABLE_CASE)->alias('t1')
            ->leftJoin(TABLE_SUITECASE)->alias('t2')->on('t1.id=t2.case')
            ->where('t2.suite')->eq($suiteID)
            ->beginIF($this->lang->navGroup->testsuite != 'qa')->andWhere('t1.project')->eq($this->session->project)->fi()
            ->andWhere('t1.product')->eq($suite->product)
            ->andWhere('t1.deleted')->eq('0')
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');

        $this->loadModel('common')->saveQueryCondition($this->dao->get(), 'testcase', false);

        if(!$append) return $cases;

        return $this->loadModel('testcase')->appendData($cases);
    }

    /**
     * 获取套件下关联的测试用例对。
     * Get linked cases pairs of the suite.
     *
     * @param  int    $suiteID
     * @access public
     * @return array
     */
    public function getLinkedCasePairs(int $suiteID): array
    {
        $suite = $this->getById($suiteID);
        if(!$suite) return array();

        return $this->dao->select('t1.id, t1.title')->from(TABLE_CASE)->alias('t1')
            ->leftJoin(TABLE_SUITECASE)->alias('t2')->on('t1.id=t2.case')
            ->where('t2.suite')->eq($suiteID)
            ->beginIF($this->lang->navGroup->testsuite != 'qa')->andWhere('t1.project')->eq($this->session->project)->fi()
            ->andWhere('t1.product')->eq($suite->product)
            ->andWhere('t1.deleted')->eq('0')
            ->orderBy('id_desc')
            ->fetchPairs('id');
    }

    /**
     * 获取套件下未关联的测试用例。
     * Get unlinked cases for suite.
     *
     * @param  object $suite
     * @param  int    $param
     * @param  object $pager
     * @access public
     * @return array
     */
    public function getUnlinkedCases(object $suite, int $param = 0, object $pager = null): array
    {
        if($this->session->testsuiteQuery == false) $this->session->set('testsuiteQuery', ' 1 = 1');
        if($param)
        {
            $query = $this->loadModel('search')->getQuery($param);
            if($query)
            {
                $this->session->set('testsuiteQuery', $query->sql);
                $this->session->set('testsuiteForm', $query->form);
            }
        }

        $query = $this->session->testsuiteQuery;
        $allProduct = "`product` = 'all'";
        if(strpos($query, '`product` =') === false) $query .= " AND `product` = {$suite->product}";
        if(strpos($query, $allProduct) !== false) $query = str_replace($allProduct, '1', $query);

        $linkedCases = $this->getLinkedCases($suite->id, 'id_desc', null, $append = false);

        return $this->dao->select('*')->from(TABLE_CASE)
            ->where($query)
            ->beginIF($linkedCases)->andWhere('id')->notIN(array_keys($linkedCases))->fi()
            ->beginIF($this->lang->navGroup->testsuite != 'qa')->andWhere('project')->eq($this->session->project)->fi()
            ->andWhere('deleted')->eq('0')
            ->orderBy('id desc')
            ->page($pager)
            ->fetchAll();
    }

    /**
     * 删除套件和所有的关联用例。
     * Delete suite and all assiociated use cases.
     *
     * @param  int    $suiteID
     * @access public
     * @return bool
     */
    public function deleteSuiteByID(int $suiteID): bool
    {
        parent::delete(TABLE_TESTSUITE, $suiteID);
        $this->dao->delete()->from(TABLE_SUITECASE)->where('suite')->eq($suiteID)->exec();
        return !dao::isError();
    }

    /**
     * 获取可以导入的测试用例。
     * Get can import cases.
     *
     * @param  int    $productID
     * @param  int    $libID
     * @param  int    $branch
     * @param  string $orderBy
     * @param  object $pager
     * @param  int    $queryID
     * @access public
     * @return array
     */
    public function getCanImportCases(int $productID, int $libID, int|string $branch, string $orderBy = 'id_desc', object $pager = null, string $browseType = '', int $queryID = 0): array
    {
        $query = '';
        if($browseType == 'bysearch')
        {
            if($queryID)
            {
                $this->session->set('testsuiteQuery', ' 1 = 1');
                $query = $this->loadModel('search')->getQuery($queryID);
                if($query)
                {
                    $this->session->set('testsuiteQuery', $query->sql);
                    $this->session->set('testsuiteForm', $query->form);
                }
            }
            else
            {
                if($this->session->testsuiteQuery === false) $this->session->set('testsuiteQuery', ' 1 = 1');
            }

            $query  = $this->session->testsuiteQuery;
            $allLib = "`lib` = 'all'";
            $query  = strpos($query, $allLib) !== false ? str_replace($allLib, '1 = 1', $query) : "{$query} AND `lib` = '{$libID}'";
        }

        $this->loadModel('branch');
        $product   = $this->loadModel('product')->getById($productID);
        $branches  = $branch === 'all' && $product->type != 'normal' ? array(BRANCH_MAIN => $this->lang->branch->main) + $this->branch->getPairs($productID, 'active') : array($product->type == 'normal' ? 0 : $branch => '');
        $canImport = array();
        foreach($branches as $branchID => $branchName) $canImport += $this->getCanImportModules($productID, $libID, $branchID);

        return $this->dao->select('*')->from(TABLE_CASE)
            ->where('deleted')->eq('0')
            ->beginIF($browseType != 'bysearch')->andWhere('lib')->eq($libID)->fi()
            ->beginIF($browseType == 'bysearch')->andWhere($query)->fi()
            ->andWhere('id')->in(array_keys($canImport))
            ->andWhere('product')->eq(0)
            ->orderBy($orderBy)
            ->page($pager)
            ->fetchAll('id');
    }

    /**
     * 获取。
     * Get imported case modules.
     *
     * @param  int        $productID
     * @param  int        $libID
     * @param  int|string $branch
     * @param  string     $returnType
     * @access public
     * @return array
     */
    public function getCanImportModules(int$productID, int $libID, int|string $branch, string $returnType = 'pairs'): array
    {
        $importedModules = $this->dao->select('fromCaseID,module')->from(TABLE_CASE)
            ->where('product')->eq($productID)
            ->andWhere('lib')->eq($libID)
            ->beginIF($branch != 'all')->andWhere('branch')->eq($branch)->fi()
            ->andWhere('fromCaseID')->ne('')
            ->andWhere('deleted')->eq('0')
            ->fetchGroup('fromCaseID', 'module');
        foreach($importedModules as $fromCaseID => $modules) $importedModules[$fromCaseID] = array_combine(array_keys($modules), array_keys($modules));

        $libCases = $this->loadModel('caselib')->getLibCases($libID, 'all');
        $modules  = $this->loadModel('tree')->getOptionMenu($productID, 'case', 0, $branch);
        if($returnType == 'items')
        {
            $moduleItems = array();
            foreach($modules as $moduleID => $moduleName) $moduleItems[$moduleID] = array('text' => $moduleName, 'value' => $moduleID);
        }

        $canImportModules = array();
        foreach($libCases as $caseID => $case){
            $caseModules = !empty($importedModules[$caseID]) ? $importedModules[$caseID] : array();
            $canImportModules[$caseID] = $returnType == 'pairs' ? array_diff_key($modules, $caseModules) : array_diff_key($moduleItems, $caseModules);
            if(!empty($canImportModules[$caseID]))
            {
                if($returnType == 'pairs')
                {
                    $canImportModules[$caseID]['ditto'] = $this->lang->testcase->ditto;
                }
                else
                {
                    $canImportModules[$caseID]['ditto'] = array('text' => $this->lang->testcase->ditto, 'value' => 'ditto');
                    $canImportModules[$caseID] = array_values($canImportModules[$caseID]);
                }
            }
            if(empty($canImportModules[$caseID])) unset($canImportModules[$caseID]);
        }

        return $canImportModules;
    }

    /**
     * 创建测试套件菜单。
     * Build testsuite menu.
     *
     * @param  object $suite
     * @param  string $type
     * @access public
     * @return string
     */
    public function buildOperateMenu(object $suite, string $type = 'view'): string
    {
        $menu   = '';
        $params = "suiteID=$suite->id";

        if($type == 'view') $menu .= $this->buildFlowMenu('testsuite', $suite, $type, 'direct');

        $menu .= $this->buildMenu('testsuite', 'linkCase', $params, $suite, $type, 'link', '', '', '', '', $this->lang->testsuite->linkCase);
        $menu .= $this->buildMenu('testsuite', 'edit',     $params, $suite, $type);
        $menu .= $this->buildMenu('testsuite', 'delete',   $params, $suite, $type, 'trash', 'hiddenwin');

        return $menu;
    }

    /**
     * 判断操作是否可以点击。
     * Judge an action is clickable or not.
     *
     * @param  object $report
     * @param  string $action
     * @access public
     * @return bool
     */
    public function isClickable(object $report, string $action): bool
    {
        return true;
    }

    /**
     * 移除一个套件下的所有用例。
     * Remove all use cases under a suite.
     *
     * @param  array $cases
     * @param  int   $suiteID
     * @access public
     * @return void
     */
    public function deleteCaseBySuiteID(array $cases, int $suiteID)
    {
        return $this->dao->delete()->from(TABLE_SUITECASE)->where('`case`')->in($cases)->andWhere('`suite`')->eq($suiteID)->exec();
    }
}
