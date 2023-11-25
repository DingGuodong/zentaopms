<?php

use function zin\wg;

/**
 * The model file of metric module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      zhouxin <zhouxin@easycorp.ltd>
 * @package     metric
 * @version     $Id: model.php 5145 2013-07-15 06:47:26Z zhouxin@easycorp.ltd $
 * @link        http://www.zentao.net
 */
class metricModel extends model
{
    public $errorInfo = '';

    /**
     * 获取度量项数据列表。
     * Get metric data list.
     *
     * @param  string $scope
     * @param  string $stage
     * @param  int    $param
     * @param  string $type
     * @param  int    $queryID
     * @param  string $sort
     * @param  object $pager
     * @access public
     * @return array|false
     */
    public function getList($scope, $stage = 'all', $param = 0, $type = '', $queryID = 0, $sort = 'id_desc', $pager = null)
    {
        if($queryID)
        {
            $query = $this->loadModel('search')->getQuery($queryID);
            if($query)
            {
                $this->session->set('metricQuery', $query->sql);
                $this->session->set('metricForm', $query->form);
            }
            else
            {
                $this->session->set('metricQuery', ' 1 = 1');
            }
        }

        $object = null;
        $purpose = null;
        if($type == 'byTree')
        {
            $object_purpose = explode('_', $param);
            $object = $object_purpose[0];
            if(count($object_purpose) == 2) $purpose = $object_purpose[1];
        }

        $query = $type == 'bysearch' ? $this->session->metricQuery : '';

        $metrics = $this->metricTao->fetchMetrics($scope, $stage, $object, $purpose, $query, $sort, $pager);
        $metrics = $this->processOldMetrics($metrics);

        return $metrics;
    }

    public function groupMetricByObject($metrics)
    {
        $groupMetrics = array_fill_keys(array_keys($this->lang->metric->objectList), array());
        foreach($metrics as $metric)
        {
            $group = isset($groupMetrics[$metric->object]) ? $metric->object : 'other';
            $groupMetrics[$group][] = $metric;
        }

        $purposes = array_keys($this->lang->metric->purposeList);
        foreach($groupMetrics as $key => $metrics)
        {
            if(empty($metrics))
            {
                unset($groupMetrics[$key]);
                continue;
            }

            usort($metrics, function($a, $b) use ($purposes) {
                $aIndex = array_search($a->purpose, $purposes);
                $bIndex = array_search($b->purpose, $purposes);

                if($aIndex === $bIndex) return 0;
                return ($aIndex < $bIndex) ? -1 : 1;
            });

            $groupMetrics[$key] = $metrics;
        }

        return $groupMetrics;
    }

    public function getMetricsByIDList($metricIDList)
    {
        return $this->metricTao->fetchMetricsByIDList($metricIDList);
    }

    /**
     * 获取旧度量项列表。
     * Get old metric list.
     *
     * @param  string $orderBy
     * @access public
     * @return array
     */
    public function getOldMetricList(string $orderBy = 'id_desc'): array
    {
        return $this->dao->select('*')->from(TABLE_BASICMEAS)->where('deleted')->eq(0)->orderby($orderBy)->fetchAll('id');
    }

    public function getListByFilter($filters, $stage)
    {
        return $this->metricTao->fetchMetricsWithFilter($filters, $stage);
    }

    public function getListByCollect($stage)
    {
        return $this->metricTao->fetchMetricsByCollect($stage);
    }

    /**
     * 获取模块树数据。
     * Get module tree data.
     *
     * @param  string $scope
     * @access public
     * @return void
     */
    public function getModuleTreeList($scope)
    {
        return $this->metricTao->fetchModules($scope);
    }

    /**
     * 根据代号获取度量项信息。
     * Get metric info by code.
     *
     * @param  string       $code
     * @param  string|array $fieldList
     * @access public
     * @return object|false
     */
    public function getByCode(string $code, string|array $fieldList = '*')
    {
        if(is_array($fieldList)) $fieldList = implode(',', $fieldList);
        return $this->dao->select($fieldList)->from(TABLE_METRIC)->where('code')->eq($code)->fetch();
    }

    /**
     * 根据ID获取度量项信息。
     * Get metric info by id.
     *
     * @param  int          $metricID
     * @param  string|array $fieldList
     * @access public
     * @return object|false
     */
    public function getByID(int $metricID, string|array $fieldList = '*')
    {
        if(is_array($fieldList)) $fieldList = implode(',', $fieldList);
        $metric = $this->dao->select($fieldList)->from(TABLE_METRIC)->where('id')->eq($metricID)->fetch();

        if(!$metric) return false;

        if($this->isOldMetric($metric))
        {
            $oldMetric = $this->getOldMetricByID($metric->fromID);

            $metric->sql         = $oldMetric->configure;
            $metric->params      = $oldMetric->params;
            $metric->oldUnit     = $oldMetric->unit;
            $metric->collectType = $oldMetric->collectType;
            $metric->collectConf = $oldMetric->collectConf;
            $metric->execTime    = $oldMetric->execTime;
        }

        return $metric;
    }

    /**
     * 根据ID获取旧版度量项信息。
     * Get old metric info by id.
     *
     * @param  int $measurementID
     * @access public
     * @return object|false
     */
    public function getOldMetricByID($measurementID)
    {
        $measurement = $this->dao->select('*')->from(TABLE_BASICMEAS)->where('id')->eq($measurementID)->fetch();
        if(!$measurement) return false;

        if($measurement->collectType == 'action')
        {
            $collectConf = json_decode($measurement->collectConf);
            $measurement->collectConf         = new stdclass();
            $measurement->collectConf->action = $collectConf->action;
            $measurement->collectConf->type   = '';
            $measurement->collectConf->week   = '';
        }
        else
        {
            $measurement->collectConf = json_decode($measurement->collectConf);
            if(is_object($measurement->collectConf))
            {
                $measurement->collectConf->module = '';
                $measurement->collectConf->action = '';
            }
        }

        return $measurement;
    }

    /**
     * 获取度量项数据源句柄。
     * Get data source statement of calculator.
     *
     * @param  object $calculator
     * @param  string $returnType
     * @access public
     * @return PDOStatement|string
     */
    public function getDataStatement($calculator, $returnType = 'statement')
    {
        if(!empty($calculator->dataset))
        {
            include_once $this->getDatasetPath();

            $dataset    = new dataset($this->dao);
            $dataSource = $calculator->dataset;
            $fieldList  = implode(',', $calculator->fieldList);

            $statement = $dataset->$dataSource($fieldList);
            $sql       = $dataset->dao->get();
        }
        else
        {
            $calculator->setDAO($this->dao);
            $scm = $this->app->loadClass('scm');
            $calculator->setSCM($scm);

            $statement = $calculator->getStatement();
            $sql       = $calculator->dao->get();
        }

        return $returnType == 'sql' ? $sql : $statement;
    }

    /**
     * 获取度量数据的日期字段。
     * Get date field of metric data.
     *
     * @param  string $code
     * @access protected
     * @return array
     */
    protected function getMetricRecordDateField(string $code): array
    {
        $record = $this->dao->select("year, month, week, day")
            ->from(TABLE_METRICLIB)
            ->where('metricCode')->eq($code)
            ->fetch();

        if(!$record) return array();

        $dataFields = array();
        $recordKeys = array_keys((array)$record);
        foreach($recordKeys as $recordKey)
        {
            if(!empty($record->$recordKey)) $dataFields[] = $recordKey;
        }

        return $dataFields;
    }
    /**
     * 根据代号获取计算实时度量项的结果。
     * Get result of calculate metric by code.
     *
     * @param  string      $code
     * @param  array       $options e.g. array('product' => '1,2,3', 'year' => '2023')
     * @param  string      $type
     * @param  object|null $pager
     * @access public
     * @return array
     */
    public function getResultByCode($code, $options = array(), $type = 'realtime', $pager = null)
    {
        if($type == 'cron')
        {
            $metric     = $this->metricTao->fetchMetricByCode($code);
            $dataFields = $this->getMetricRecordDateField($code);

            if($metric->scope != 'system') $dataFields[] = $metric->scope;

            return $this->metricTao->fetchMetricRecords($code, $dataFields, $options, $pager);
        }

        $metric = $this->metricTao->fetchMetricByCode($code);
        if(!$metric) return false;

        $calcPath = $this->getCalcRoot() . $metric->scope . DS . $metric->purpose . DS . $metric->code . '.php';
        if(!is_file($calcPath)) return false;

        include_once $this->getBaseCalcPath();
        include_once $calcPath;
        $calculator = new $metric->code;

        $statement = $this->getDataStatement($calculator);
        $rows = $statement->fetchAll();

        foreach($rows as $row) $calculator->calculate($row);
        return $calculator->getResult($options);
    }

    /**
     * 根据代号列表批量获取度量项的结果。
     * Get result of calculate metric by code list.
     *
     * @param  array $codes   e.g. array('code1', 'code2')
     * @param  array $options e.g. array('product' => '1,2,3', 'year' => '2023')
     * @access public
     * @return array
     */
    public function getResultByCodes($codes, $options = array())
    {
        $results = array();
        foreach($codes as $code)
        {
            $result = $this->getResultByCode($code, $options);
            if($result) $results[$code] = $result;
        }

        return $results;
    }

    /**
     * 获取可计算的度量项列表。
     * Get executable metric list.
     *
     * @access public
     * @return array
     */
    public function getExecutableMetric()
    {
        $metricList = $this->dao->select('id,code,time')
            ->from(TABLE_METRIC)
            ->where('deleted')->eq('0')
            ->fetchAll();

        $excutableMetrics = array();
        foreach($metricList as $metric) $excutableMetrics[$metric->id] = $metric->code;

        return $excutableMetrics;
    }

    public function clearMetricLib()
    {
        $nowDate = date('Y-m-d');

        $this->dao->delete()->from(TABLE_METRICLIB)
            ->where('date')->like("$nowDate%")
            ->exec();

        return dao::isError();
    }

    /**
     * 根据度量项收集周期来清理过期的度量库数据。
     * Clear outdated metric records by cycle.
     *
     * @param  string $code
     * @param  string $cycle
     * @access public
     * @return array
     */
    public function clearOutDatedRecords($code, $cycle)
    {
        $year    = date('Y');
        $month   = date('n');
        $week    = date('W');
        $day     = date('j');

        if($cycle == 'year')
        {
            $this->dao->delete()->from(TABLE_METRICLIB)
                ->where('metricCode')->eq($code)
                ->andWhere('year')->eq($year)
                ->exec();
        }
        elseif($cycle == 'month')
        {
            $this->dao->delete()->from(TABLE_METRICLIB)
                ->where('metricCode')->eq($code)
                ->andWhere('year')->eq($year)
                ->andWhere('month')->eq($month)
                ->exec();
        }
        elseif($cycle == 'week')
        {
            $this->dao->delete()->from(TABLE_METRICLIB)
                ->where('metricCode')->eq($code)
                ->andWhere('year')->eq($year)
                ->andWhere('week')->eq($week)
                ->exec();
        }
        elseif($cycle == 'day')
        {
            $this->dao->delete()->from(TABLE_METRICLIB)
                ->where('metricCode')->eq($code)
                ->andWhere('year')->eq($year)
                ->andWhere('month')->eq($month)
                ->andWhere('day')->eq($day)
                ->exec();
        }
    }

    /**
     * 插入度量库数据。
     * Insert into metric lib.
     *
     * @param  array  $records
     * @access public
     * @return void
     */
    public function insertMetricLib($records)
    {
        $this->dao->begin();
        foreach($records as $record)
        {
            if(empty($record)) continue;
            $this->dao->insert(TABLE_METRICLIB)
                ->data($record)
                ->exec();
        }
        $this->dao->commit();

        return dao::isError();
    }

    /**
     * 获取可计算的度量项对象列表。
     * Get executable calculator list.
     *
     * @access public
     * @return array
     */
    public function getExecutableCalcList()
    {
        $funcRoot = $this->getCalcRoot();

        $fileList = array();
        foreach($this->config->metric->scopeList as $scope)
        {
            foreach($this->config->metric->purposeList as $purpose)
            {
                $pattern = $funcRoot . $scope . DS . $purpose . DS . '*.php';
                $matchedFiles = glob($pattern);
                if($matchedFiles !== false) $fileList = array_merge($fileList, $matchedFiles);
            }
        }

        $calcList = array();
        $excutableMetric = $this->getExecutableMetric();
        foreach($fileList as $file)
        {
            $code = rtrim(basename($file), '.php');
            if(!in_array($code, $excutableMetric)) continue;
            $id = array_search($code, $excutableMetric);

            $calc = new stdclass();
            $calc->code = $code;
            $calc->file = $file;
            $calcList[$id] = $calc;
        }

        return $calcList;
    }

    /**
     * 获取度量项计算实例列表。
     * Get calculator instance list.
     *
     * @access public
     * @return array
     */
    public function getCalcInstanceList()
    {
        $calcList = $this->getExecutableCalcList();

        include_once $this->getBaseCalcPath();
        $calcInstances = array();
        foreach($calcList as $id => $calc)
        {
            $file      = $calc->file;
            $className = $calc->code;

            require_once $file;
            $metricInstance = new $className;
            $metricInstance->id = $id;

            $calcInstances[$className] = $metricInstance;
        }

        return $calcInstances;
    }

    /**
     * 获取通用数据集对象。
     * Get instance of data set object.
     *
     * @access public
     * @return dataset
     */
    public function getDataset()
    {
        $datasetPath = $this->getDatasetPath();
        include_once $datasetPath;
        return new dataset($this->dao);
    }

    /**
     * 对度量项按照通用数据集进行归类，没有数据集不做归类。
     * Classify calculator instance list by its data set.
     *
     * @param  array  $calcList
     * @access public
     * @return array
     */
    public function classifyCalc($calcList)
    {
        $datasetCalcGroup = array();
        $otherCalcList    = array();
        foreach($calcList as $code => $calc)
        {
            if(empty($calc->dataset))
            {
                $otherCalcList[$code] = $calc;
                continue;
            }

            $dataset = $calc->dataset;
            if(!isset($datasetCalcGroup[$dataset])) $datasetCalcGroup[$dataset] = array();
            $datasetCalcGroup[$dataset][$code] = $calc;
        }

        $classifiedCalcGroup = array();
        foreach($datasetCalcGroup as $dataset => $calcList) $classifiedCalcGroup[] = (object)array('dataset' => $dataset, 'calcList' => $calcList);

        foreach($otherCalcList as $code => $calc) $classifiedCalcGroup[] = (object)array('dataset' => '', 'calcList' => array($code => $calc));
        return $classifiedCalcGroup;
    }

    /**
     * 对度量项的字段列表取并集。
     * Unite field list of each calculator.
     *
     * @param  array  $calcList
     * @access public
     * @return string
     */
    public function uniteFieldList($calcList)
    {
        $fieldList = array();
        foreach($calcList as $calcInstance) $fieldList  = array_merge($fieldList, $calcInstance->fieldList);
        $uniqueList = array_unique($fieldList);
        $aliasList  = array();
        foreach($uniqueList as $field)
        {
            if(strpos($field, '.') === false || strpos(strtoupper($field), ' AS ') !== false)
            {
                $aliasList[] = $field;
                continue;
            }
            $alias = str_replace('.', '_', $field);
            $aliasList[] = $field . ' AS ' . $alias;
        }
        return implode(',', $aliasList);
    }

    /**
     * Build search form.
     *
     * @param  int    $queryID
     * @param  string $actionURL
     * @access public
     * @return void
     */
    public function buildSearchForm($queryID, $actionURL)
    {
        $this->config->metric->browse->search['actionURL'] = $actionURL;
        $this->config->metric->browse->search['queryID']   = $queryID;
        $this->config->metric->browse->search['params']['dept']['values']    = $this->loadModel('dept')->getOptionMenu();
        $this->config->metric->browse->search['params']['visions']['values'] = $this->loadModel('user')->getVisionList();

        $this->loadModel('search')->setSearchParams($this->config->metric->browse->search);
    }

    /**
     * 为度量详情页构建操作按钮
     * Build operate menu.
     *
     * @param  object $metric
     * @access public
     * @return array
     */
    public function buildOperateMenu(object $metric): array
    {
        $menuList = array
        (
            'main'   => array(),
            'suffix' => array()
        );

        if($metric->stage == 'wait')
        {
            $this->config->metric->actionList['edit']['icon']        = 'edit';
            $this->config->metric->actionList['edit']['text']        = $this->lang->edit;
            $this->config->metric->actionList['edit']['hint']        = $this->lang->edit;
            $this->config->metric->actionList['edit']['data-toggle'] = 'modal';
            $this->config->metric->actionList['edit']['url']         = helper::createLink('metric', 'edit', "metricID={$metric->id}");

            $menuList['suffix'][] = $this->config->metric->actionList['edit'];
            if($metric->stage == 'wait' && !$this->isOldMetric($metric) && $metric->builtin === '0') $menuList['main'][]   = $this->config->metric->actionList['implement'];
        }
        else
        {
            $menuList['main'][] = $this->config->metric->actionList['delist'];
        }

        if(!$metric->builtin)
        {
            $menuList['suffix'][] = $this->config->metric->actionList['delete'];
        }

        return $menuList;
    }

    /**
     * 获取范围的对象列表。
     * Get object pairs by scope.
     *
     * @param  string $scope
     * @access public
     * @return array
     */
    public function getPairsByScope($scope)
    {
        if(empty($scope) || $scope == 'system') return array();

        $objectPairs = array();
        switch($scope)
        {
            case 'dept':
                $objectPairs = $this->loadModel('dept')->getDeptPairs();
                break;
            case 'user':
                $objectPairs = $this->loadModel('user')->getPairs('noletter');
                break;
            case 'program':
                $objectPairs = $this->dao->select('id, name')->from(TABLE_PROGRAM)
                    ->where('deleted')->eq(0)
                    ->andWhere('type')->eq('program')
                    ->fetchPairs();
                break;
            case 'product':
                $objectPairs = $this->dao->select('id, name')->from(TABLE_PRODUCT)
                    ->where('deleted')->eq(0)->fetchPairs();
                break;
            case 'project':
                $objectPairs = $this->dao->select('id, name')->from(TABLE_PROJECT)
                    ->where('deleted')->eq(0)
                    ->andWhere('type')->eq('project')
                    ->fetchPairs();
                break;
            case 'execution':
                $objectPairs = $this->dao->select('id, name')->from(TABLE_PROJECT)
                    ->where('deleted')->eq(0)
                    ->andWhere('type')->in('sprint,stage')
                    ->fetchPairs();
                break;
            case 'code':
                $objectPairs = $this->loadModel('repo')->getRepoPairs('repo');
                break;
            default:
                $objectPairs = $this->loadModel($scope)->getPairs();
                break;
        }

        return $objectPairs;
    }

    /**
     * 获取度量项的日期字符串。
     * Build date cell.
     *
     * @param  object $row
     * @param  array  $object
     * @access public
     * @return string
     */
    public function buildDateCell($row, $record)
    {
        extract($record);

        $date = $dateString =  false;
        $dateType = 'day';
        if(isset($year, $month, $day))
        {
            $date     = $dateString = "{$year}-{$month}-{$day}";
            $dateType = 'day';
        }
        elseif(isset($year, $week))
        {
            $date       = sprintf($this->lang->metric->weekCell, $year, $week);
            $dateString = "{$year}-{$week}";
            $dateType   = 'week';
        }
        elseif(isset($year, $month))
        {
            $date       = $year . $this->lang->year . $month . $this->lang->month;
            $dateString = "{$year}-{$month}";
            $dateType   = 'month';
        }
        elseif(isset($year))
        {
            $date       = $year . $this->lang->year;
            $dateString = $year;
            $dateType   = 'year';
        }

        $row->date       = $date;
        $row->dateString = $dateString;
        $row->dateType   = $dateType;
        return $row;
    }

    public function checkCalcExists($metric)
    {
        $calcName = $this->getCalcRoot() . $metric->scope . DS . $metric->purpose . DS . $metric->code . '.php';
        return file_exists($calcName);
    }

    /**
     * 没有度量的显示范围不做显示。
     * Unset scope item that have no metric.
     *
     * @param  string $stage
     * @access public
     * @return void
     */
    public function processScopeList($stage = 'all')
    {
        foreach($this->lang->metric->scopeList as $scope => $name)
        {
            $metrics = $this->metricTao->fetchMetrics($scope, $stage);
            if(empty($metrics))
            {
                unset($this->lang->metric->scopeList[$scope]);

                unset($this->lang->metric->featureBar['preview'][$scope]);
            }
        }

        if(count($this->lang->metric->featureBar['preview']) >= 7)
        {
            $i = 0;
            foreach($this->lang->metric->featureBar['preview'] as $key => $name)
            {
                $i ++;
                if($i >= 7)
                {
                    unset($this->lang->metric->featureBar['preview'][$key]);
                    $this->lang->metric->moreSelects[$key] = $name;
                }
            }

            $this->lang->metric->featureBar['preview']['more'] = $this->lang->metric->more;
        }

        $this->lang->metric->featureBar['preview']['collect'] = $this->lang->metric->collect;
    }

    /**
     * 根据后台配置的估算单位对列表赋值。
     * Assign unitList['measure'] by custom hourPoint.
     *
     * @access public
     * @return void
     */
    public function processUnitList()
    {
        $this->app->loadLang('custom');
        $key = zget($this->config->custom, 'hourPoint', '0');

        $this->lang->metric->unitList['measure'] = $this->lang->custom->conceptOptions->hourPoint[$key];
    }

    /**
     * 根据后台配置的是否开启用户需求设置对象列表。
     * Unset objectList['requirement'] if custom requirement is close.
     *
     * @access public
     * @return void
     */
    public function processObjectList()
    {
        if(!isset($this->config->custom->URAndSR) or !$this->config->custom->URAndSR) unset($this->lang->metric->objectList['requirement']);
    }

    public function buildFilterCheckList($filters)
    {
        $filterItems = array();

        $onchange = 'window.handleFilterCheck(this)';

        $scopeItems = array();
        foreach($this->lang->metric->scopeList as $value => $text)
        {
            $isChecked = (isset($filters['scope']) and in_array($value, $filters['scope']));
            $scopeItems[] = array('text' => $text, 'value' => $value, 'onchange' => $onchange, 'checked' => $isChecked);
        }
        $filterItems['scope'] = array
        (
            'class' => 'flex3 divider',
            'items' => $scopeItems
        );

        $objectItems = array();
        foreach($this->lang->metric->objectList as $value => $text)
        {
            $isChecked = (isset($filters['object']) and in_array($value, $filters['object']));
            $objectItems[] = array('text' => $text, 'value' => $value, 'onchange' => $onchange, 'checked' => $isChecked);
        }
        $filterItems['object'] = array
        (
            'class' => 'flex5 divider',
            'items' => $objectItems
        );

        $purposeItems = array();
        foreach($this->lang->metric->purposeList as $value => $text)
        {
            $isChecked = (isset($filters['purpose']) and in_array($value, $filters['purpose']));
            $purposeItems[] = array('text' => $text, 'value' => $value, 'onchange' => $onchange, 'checked' => $isChecked);
        }
        $filterItems['purpose'] = array
        (
            'class' => 'flex2',
            'items' => $purposeItems
        );

        return $filterItems;
    }

    /**
     * 根据数据初始化操作按钮。
     * Init action button by data.
     *
     * @param  array  $metrics
     * @access public
     * @return array
     */
    public function initActionBtn(array $metrics): array
    {
        foreach($metrics as $metric)
        {
            foreach($metric->actions as $key => $action)
            {
                $isClick = true;

                if($action['name'] == 'edit')      $isClick = $metric->canEdit;
                if($action['name'] == 'implement') $isClick = $metric->canImplement;
                if($action['name'] == 'delist')    $isClick = $metric->canDelist;

                $metric->actions[$key]['disabled'] = !$isClick;
            }
        }

        return $metrics;
    }

    /**
     * 判断度量项是否是旧版度量项。
     * Judge if the metric is old.
     *
     * @param  object $metric
     * @access public
     * @return bool
     */
    public function isOldMetric($metric)
    {
        return $metric->type == 'sql';
    }

    /**
     * 创建SQL函数。
     * Create sql function.
     *
     * @param  string $sql
     * @param  object $measurement
     * @access public
     * @return array
     */
    public function createSqlFunction($sql, $measurement)
    {
        $measFunction = $this->getSqlFunctionName($measurement);
        $postFunction = $this->parseSqlFunction($sql);
        if(!$measFunction || !$postFunction) return array('result' => 'fail', 'errors' => $this->lang->metric->tips->nameError);

        $sql = str_replace($postFunction, $measFunction, $sql);

        try
        {
            $this->dbh->exec("DROP FUNCTION IF EXISTS `$measFunction`");
            $result = $this->dbh->exec($sql);
        }
        catch(PDOException $exception)
        {
            $message = sprintf($this->lang->metric->tips->createError, $exception->getMessage());
            return array('result' => 'fail', 'errors' => $message);
        }

        return array('result' => 'success');
    }

    /**
     * 获取旧度量项的SQL函数名。
     * Get sql function name of a old metric.
     *
     * @param  object $measurement
     * @access public
     * @return string
     */
    public function getSqlFunctionName($measurement)
    {
        if(!$measurement) return '';
        return strtolower("qc_{$measurement->code}");
    }

    /**
     * 处理请求参数。
     * Process post params.
     *
     * @access public
     * @return array
     */
    public function processPostParams()
    {
        return array_combine($this->post->varName, $this->post->queryValue);
    }

    /**
     * 执行旧版度量项。
     * Execute a sql metric.
     *
     * @param  object $measurement
     * @param  array  $vars
     * @access public
     * @return int|string
     */
    public function execSqlMeasurement($measurement, $vars)
    {
        $function = $this->getSqlFunctionName($measurement);
        if(!$function)
        {
            $this->errorInfo = $this->lang->metric->tips->nameError;
            return false;
        }

        $vars = (array) $vars;
        foreach($vars as $key => $param)
        {
            if(is_object($param))
            {
                unset($vars[$key]);
                continue;
            }

            $vars[$key] = $this->dbh->quote($param);
        }

        $params = join(',', $vars);

        try
        {
            $result = $this->dbh->query("select $function($params)")->fetch(PDO::FETCH_NUM);
        }
        catch(PDOException $exception)
        {
            $this->errorInfo = $exception->getMessage();
            return false;
        }

        $queryResult = isset($result[0]) . $measurement->unit ? $result[0] : null;
        return $queryResult;
    }

    /**
     * 获取度量筛选器的下拉选项。
     * Get options of a control.
     *
     * @param  string $optionType
     * @access public
     * @return array
     */
    public function getControlOptions($optionType)
    {
        $optionList = array();

        if($optionType == 'project')
        {
            $options = $this->loadModel('project')->getPairsByProgram();
        }
        elseif($optionType == 'product')
        {
            $options = $this->loadModel('product')->getPairs('nocode');
        }
        elseif($optionType == 'sprint')
        {
            $options = $this->loadModel('execution')->getPairs();
        }
        elseif($optionType == 'user')
        {
            $options = $this->loadModel('user')->getPairs('noletter');
        }
        elseif(strpos($optionType, '.') !== false)
        {
            list($moduleName, $varListName) = explode('.', $optionType);
            $this->app->loadLang($moduleName);
            $varListName .= 'List';
            $options = $this->lang->$moduleName->$varListName;
            unset($options[0]);
            unset($options['']);
        }
        else
        {
            $options = array('' => '');
        }

        return $options;
    }

    /**
     * 处理实现提示文本信息。
     * Process implement tips.
     *
     * @param  string $code
     * @access public
     * @return void
     */
    public function processImplementTips(string $code): void
    {
        $tmpRoot = $this->app->getTmpRoot();

        $instructionTips = $this->lang->metric->implement->instructionTips;

        foreach($instructionTips as $index => $tip)
        {
            $instructionTips[$index] = str_replace("{code}", $code, $instructionTips[$index]);
            $instructionTips[$index] = str_replace("{tmpRoot}", $tmpRoot, $instructionTips[$index]);
        }

        $this->lang->metric->implement->instructionTips = $instructionTips;
    }

    /**
     * 更新一个度量项的字段内容。
     * Update metric fields
     *
     * @param  string $metricID
     * @param  object $metric
     * @access public
     * @return void
     */
    public function updateMetricFields(string $metricID, object $metric): void
    {
        $this->dao->update(TABLE_METRIC)->data($metric)->where('id')->eq($metricID)->exec();
    }

    /**
     * 将旧度量项的信息附加到度量项列表中。
     * Append old metric info to metric list.
     *
     * @param  array $metrics
     * @access public
     * @return array
     */
    public function processOldMetrics($metrics)
    {
        $metricList = array();
        if(!in_array($this->config->edition, array('max', 'ipd')))
        {
            foreach($metrics as $metric)
            {
                $metric->isOldMetric = false;
                $metricList[] = $metric;
            }
        }
        else
        {
            $oldMetricList = $this->getOldMetricList();

            foreach($metrics as $metric)
            {
                $metric->isOldMetric = $this->isOldMetric($metric);
                if($metric->isOldMetric) $metric->unit = $oldMetricList[$metric->fromID]->unit;

                $metricList[] = $metric;
            }
        }

        return $metricList;
    }

    /**
     * 获取定时设置的标签。
     * Get label of collect configure.
     *
     * @param  object $metric
     * @access public
     * @return string
     */
    public function getCollectConfText($metric)
    {
        $collectConf = $metric->collectConf;
        $dateType    = $this->lang->metric->dateList[$collectConf->type];
        $dateConf    = $collectConf->type == 'week' ? $collectConf->week : $collectConf->month;

        $dateListText = '';
        if($collectConf->type == 'week')
        {
            foreach(explode(',', $dateConf) as $date) $dateListText .= $this->lang->metric->weekList[$date] . ',';
        }
        else
        {
            foreach(explode(',', $dateConf) as $date) $dateListText .= sprintf($this->lang->metric->monthText, $date) . ',';
        }

        return sprintf($this->lang->metric->collectConfText, $dateType, rtrim($dateListText, ','), $metric->execTime);
    }

    /**
     * 获取度量项的数据类型。
     * Get data type of metric.
     *
     * @param  array    $tableData
     * @access public
     * @return string|false
     */
    public function getMetricRecordType(array|bool $tableData): string|false
    {
        if(!$tableData) return false;
        $fields = array_column($tableData, 'name');

        $type = array();
        if(in_array('scope', $fields)) $type[] = 'scope';
        if(in_array('date', $fields)) $type[] = 'date';

        if(empty($type)) $type[] = 'system';
        return implode('-', $type);
    }

    /**
     * 获取一个echarts的legend配置。
     * Get lengend options of echarts by head and series.
     *
     * @param  array    $series
     * @param  string   $range object|time
     * @access public
     * @return array
     */
    public function getEchartLegend(array $series, string $range = 'time')
    {
        $legend = array('type' => 'scroll');

        if($range == 'object')
        {
            $selectedScope = array();
            foreach($series as $index => $se)
            {
                $selectedScope[$se['name']] = $index == 0 ? true : false;
            }

            $legend['selector'] = true;
            $legend['selected'] = $selectedScope;
        }

        return $legend;
    }

    /**
     * 获取一个echarts的配置项。
     * Get options of echarts by head and data.
     *
     * @param  array    $head 表头
     * @param  array    $data 数据
     * @param  string   $chartType 图表类型 barX|barY|line|pie
     * @access public
     * @return array|false
     */
    public function getEchartsOptions(array $header, array $data, string $chartType = 'line'): array|false
    {
        if(!$header || !$data) return false;
        $type = in_array($chartType, array('barX', 'barY')) ? 'bar' : $chartType;
        //if($chartType == 'pie') return $this->getPieEchartsOptions($head, $datas);

        $headLength = count($header);
        if($headLength == 2)
        {
            return $this->getTimeOptions($header, $data, $type);
        }
        elseif($headLength == 3)
        {
            if(in_array('scope', array_column($header, 'name')))
            {
                return $this->getObjectOptions($data, $type);
            }
            else
            {
                return $this->getTimeOptions($header, $data, $type);
            }
        }
        elseif($headLength == 4)
        {
            return $this->getObjectOptions($data, $type);
        }
    }

    public function getObjectOptions($data, $type)
    {
        $dateField = !isset(current($data)->dateString) ? 'calcTime' : 'dateString';
        usort($data, function($a, $b) use ($dateField)
        {
            $keyA = $a->$dateField;
            $keyB = $b->$dateField;

            if($keyA == $keyB) return 0;

            return $keyA > $keyB ? -1 : 1;
        });

        $times   = array();
        $objects = array();
        foreach($data as $dataInfo)
        {
            $time   = substr($dataInfo->$dateField, 0, 10);
            $object = $dataInfo->scope;
            $value  = $dataInfo->value;

            if(!isset($times[$time]))     $times[$time] = $time;
            if(!isset($objects[$object])) $objects[$object] = array();
            $objects[$object][$time] = $value;
        }

        $xAxis  = array('type' => 'category', 'data' => $times);
        $yAxis  = array('type' => 'value');
        $series = array();
        foreach($objects as $object => $datas)
        {
            $seriesData = array();
            foreach($times as $time)
            {
                $seriesData[] = isset($datas[$time]) ? $datas[$time] : 0;
            }

            $series[] = array('type' => $type, 'name' => $object, 'data' => $seriesData);
        }

        $legend = $this->getEchartLegend($series, 'object');

        $options = array();
        $options['xAxis']  = $xAxis;
        $options['yAxis']  = $yAxis;
        $options['legend'] = $legend;
        $options['series'] = $series;

        return $options;
    }

    public function getTimeOptions($header, $data, $type)
    {
        $headLength = count($header);

        if($headLength == 2)
        {
            $x = $header[1]['name'];
            $y = $header[0]['name'];
        }
        elseif($headLength == 3)
        {
            $x = $header[0]['name'];
            $y = $header[1]['name'];
        }

        usort($data, function($a, $b) use ($x)
        {
            $keyA = $a->$x;
            $keyB = $b->$x;

            if($keyA == $keyB) return 0;

            return $keyA > $keyB ? -1 : 1;
        });

        $xTime = array_column($data, $x);
        foreach($xTime as $key => $time)
        {
            $xTime[$key] = substr($time, 0, 10);
        }
        $xAxis = array('type' => 'category', 'data' => $xTime);
        $yAxis = array('type' => 'value');

        $series = array('type' => $type, 'data' => array_column($data, $y));
        $legend = $this->getEchartLegend($series);

        $options = array();
        $options['xAxis']  = $xAxis;
        $options['yAxis']  = $yAxis;
        $options['legend'] = $legend;
        $options['series'] = $series;

        return $options;
    }

    /**
     * 获取一个echarts pie的配置项。
     * Get options of echarts pie by head and data.
     *
     * @param  array    $head 表头
     * @param  array    $datas 数据
     * @access public
     * @return array|false
     */
    public function getPieEchartsOptions(array $head, array $datas): array|false
    {
        if(!$head || !$datas) return false;

        list($x, $y) = $this->getEchartXY($head);
        if(!$x || !$y) return false;

        $seriesData = array();
        foreach($datas as $data)
        {
            $seriesData[] = array('name' => $data->$x, 'value' => $data->$y);
        }

        $options = array();
        $options['data'] = $seriesData;

        return $options;
    }

    public function getChartTypeList($head)
    {
        $chartTypeList = $this->lang->metric->chartTypeList;
        if(count($head) != 3) unset($chartTypeList['pie']);

        return $chartTypeList;
    }

    /**
     * 更新度量项的创建时间
     * Update created date of metrics.
     *
     * @access public
     * @return void
     */
    public function updateMetricDate()
    {
        $now = helper::now();
        $metricIDList = $this->dao->select('id')->from(TABLE_METRIC)->where('deleted')->eq('0')->fetchPairs();
        foreach($metricIDList as $metricID) $this->dao->update(TABLE_METRIC)->set('createdDate')->eq($now)->exec();
    }

    /**
     * 获取度量项计算文件的根目录。
     * Get root of metric calculator.
     *
     * @access public
     * @return string
     */
    public function getCalcRoot()
    {
        return $this->app->getModuleRoot() . 'metric' . DS . 'calc' . DS;
    }

    /**
     * 获取数据集文件的路径
     * Get path of calculator data set.
     *
     * @access public
     * @return string
     */
    public function getDatasetPath()
    {
        return $this->app->getModuleRoot() . 'metric' . DS . 'dataset.php';
    }

    /**
     * 获取度量项基类文件的路径。
     * Get path of base calculator class.
     *
     * @access public
     * @return string
     */
    public function getBaseCalcPath()
    {
        return $this->app->getModuleRoot() . 'metric' . DS . 'calc.class.php';
    }

    /**
     * 处理度量数据查询条件。
     * Process metric data query.
     *
     * @param  array     $query
     * @param  string    $key
     * @param  string    $type
     * @access public
     * @return object|string|false
     */
    public function processRecordQuery(array $query, string $key, string $type = 'common'): object|string|false
    {
        if(!isset($query[$key]) || empty($query[$key])) return false;

        if($type == 'date')
        {
            list($year, $month, $day) = explode('-', $query[$key]);

            $timestamp = strtotime($query[$key]);
            $week      = date('W', $timestamp);

            $dateParse = new stdClass();
            $dateParse->year  = $year;
            $dateParse->month = "{$year}{$month}";
            $dateParse->week  = "{$year}{$week}";
            $dateParse->day   = "{$year}{$month}{$day}";

            return $dateParse;
        }
        return $query[$key];
    }

    /**
     * 获取度量数据的日期类型。
     * Get date type of metric data.
     *
     * @param  array    $dateFields
     * @access public
     * @return string
     */
    public function getDateType(array $dateFields): string
    {
        if(in_array('day', $dateFields)) return 'day';
        if(in_array('week', $dateFields)) return 'week';
        if(in_array('month', $dateFields)) return 'month';
        if(in_array('year', $dateFields)) return 'year';

        return 'day';
    }

    /**
     * 获取度量数据的日期类型。
     * Get date type of metric data.
     *
     * @param  array    $dateFields
     * @access public
     * @return string
     */
    public function getDateByDateType(string $dateType): string
    {
        if($dateType == 'day')   $sub = '-7 days';
        if($dateType == 'week')  $sub = '-1 month';
        if($dateType == 'month') $sub = '-1 year';
        if($dateType == 'year')  $sub = '-3 years';

        return date('Y-m-d', strtotime($sub));
    }

    /**
     * 解析SQL函数。
     * Parsing SQL function.
     *
     * @param  string $sql
     * @access public
     * @return string
     */
    public function parseSqlFunction($sql)
    {
        $pattern = "/create\s+function\s+`{0,1}([\$,a-z,A-z,_,0-9,\(,|)]+`{0,1})\(+/Ui";
        preg_match_all($pattern, $sql, $matches);

        if(empty($matches[1][0])) return null;
        return trim($matches[1][0], '`');
    }

    /**
     * 替换换行符和回车符为指定字符。
     * Replace CR and LF to char.
     *
     * @param  string $str
     * @param  string $replace
     * @access public
     * @return string
     */
    public function replaceCRLF(string $str, string $replace = ';'): string
    {
        $str = trim($str);
        if(strpos($str, "\n\r") !== false) $str = str_replace("\n\r", $replace, $str);
        if(strpos($str, "\r\n") !== false) $str = str_replace("\r\n", $replace, $str);
        if(strpos($str, "\n") !== false)   $str = str_replace("\n",   $replace, $str);
        if(strpos($str, "\r") !== false)   $str = str_replace("\r",   $replace, $str);

        return $str;
    }

    /**
     * 判断 header 是否有分组（合并单元格）。
     * Judge header whether there are merges cell.
     *
     * @param  array     $header
     * @access protected
     * @return array
     */
    protected function isHeaderGroup($header)
    {
        if(!$header) return false;

        foreach($header as $head)
        {
            if(isset($head['headerGroup'])) return true;
        }

        return false;
    }

    /**
     * 判断是否是按系统统计的度量项。
     * Determine whether it is metric in system.
     *
     * @param  array  $results
     * @access public
     * @return bool
     */
    public function isSystemMetric($results)
    {
        $firstRecord = (object)current($results);

        foreach($this->config->metric->excludeGlobal as $exclude)
        {
            if(isset($firstRecord->$exclude)) return false;
        }
        return true;
    }

    /**
     * 获取度量项的收集周期。
     * Get collect cycle of metric.
     *
     * @param  array|object $record
     * @access public
     * @return string|null
     */
    public function getMetricCycle($record)
    {
        $record = (object)$record;

        if(isset($record->year) && !isset($record->month) && !isset($record->week)) return 'year';
        if(isset($record->year, $record->month) && !isset($record->day)) return 'month';
        if(isset($record->year, $record->month, $record->day)) return 'day';
        if(isset($record->year, $record->week)) return 'week';

        return null;
    }
}
