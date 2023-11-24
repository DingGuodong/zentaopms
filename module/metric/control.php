<?php
/**
 * The control file of metric module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      zhouxin <zhouxin@easycorp.ltd>
 * @package     metric
 * @version     $Id: control.php 5145 2013-07-15 06:47:26Z zhouxin@easycorp.ltd $
 * @link        http://www.zentao.net
 */
class metric extends control
{
    /**
     * __construct.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 度量项预览列表。
     * Preview metric list.
     *
     * @param  string $scope
     * @param  string $viewType
     * @param  int    $metricID
     * @param  string $filtersBase64
     * @param  int    $recTotal
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return void
     */
    public function preview($scope = 'project', $viewType = 'single', $metricID = 0, $filtersBase64 = '', $recTotal = 0, $recPerPage = 100, $pageID = 1)
    {
        $this->metric->processScopeList('released');

        $this->app->loadClass('pager', true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $filters = array();
        if($scope == 'filter')
        {
            $filters = json_decode(base64_decode($filtersBase64), true);
            if(!is_array($filters)) $filters = array();
            $metrics = $this->metric->getListByFilter($filters, 'released');
        }
        elseif($scope == 'collect')
        {
            $metrics = $this->metric->getListByCollect('released');
        }
        else
        {
            $metrics = $this->metric->getList($scope, 'released');
        }
        $groupMetrics = $this->metric->groupMetricByObject($metrics);

        $current = $this->metric->getByID($metricID);
        if(empty($current) and $groupMetrics) $current = current(current($groupMetrics));

        $resultHeader = array();
        $resultData   = array();
        if(!empty($current))
        {
            $metric = $this->metric->getByID($current->id);
            $result = $this->metric->getResultByCode($metric->code, array(), 'cron', $pager);

            $resultHeader = $this->metricZen->getViewTableHeader($metric);
            $resultData   = $this->metricZen->getViewTableData($metric, $result);
        }

        //include 'test/groupData/' . 'time-week.php';

        list($groupHeader, $groupData) = $this->metricZen->getGroupTable($resultHeader, $resultData);
        $this->view->groupHeader   = $groupHeader;
        $this->view->groupData     = $groupData;
        $this->view->tableWidth    = $this->metricZen->getViewTableWidth($groupHeader);
        $this->view->headerGroup   = isset(current($groupHeader)['headerGroup']);

        $this->view->metrics       = $metrics;
        $this->view->groupMetrics  = $groupMetrics;
        $this->view->current       = $current;
        $this->view->metricList    = $this->lang->metric->metricList;
        $this->view->scope         = $scope;
        $this->view->title         = $this->lang->metric->common;
        $this->view->viewType      = $viewType;
        $this->view->recTotal      = count($metrics);
        $this->view->filters       = $filters;
        $this->view->filtersBase64 = $filtersBase64;
        $this->view->dtablePager   = $pager;
        $this->view->chartTypeList = $this->metric->getChartTypeList($resultHeader);
        $this->view->echartOptions = $this->metric->getEchartsOptions($resultHeader, $resultData);
        $this->view->metricRecordType = $this->metric->getMetricRecordType($resultHeader);
        $this->display();
    }

    /**
     * 获取度量项列表。
     * Get metric list by ajax.
     *
     * @param  string $scope
     * @param  string $filters
     * @access public
     * @return void
     */
    public function ajaxGetMetrics($scope, $filters)
    {
        if($scope == 'filter')
        {
            $filters = json_decode(base64_decode($filters), true);
            if(!is_array($filters)) $filters = array();
            $metrics = $this->metric->getListByFilter($filters, 'released');
        }
        elseif($scope == 'collect')
        {
            $metrics = $this->metric->getListByCollect('released');
        }
        else
        {
            $metrics = $this->metric->getList($scope, 'released');
        }

        echo(json_encode($metrics));
    }

    /**
     * 查看度量项的详情。
     * View metric details.
     *
     * @param  int    $metricID
     * @access public
     * @return void
     */
    public function details($metricID)
    {
        $metric = $this->metric->getByID($metricID);

        $this->view->metric         = $metric;
        $this->view->legendBasic    = $this->metricZen->getBasicInfo($this->view,'scope,object,purpose,name,code,unit,desc,definition');
        $this->view->createEditInfo = $this->metricZen->getCreateEditInfo($this->view, 'createdBy,implementedBy,lastEdited');

        $this->display();
    }

    /**
     * 计算度量项。
     * Execute metric.
     *
     * @access public
     * @return void
     */
    public function updateMetricLib()
    {
        // 保存当前的错误报告级别和显示错误的设置
        $originalDebug = $this->config->debug;

        // 开启调试模式
        $this->config->debug = 2;

        $this->metric->clearMetricLib();
        $calcList = $this->metric->getCalcInstanceList();
        $classifiedCalcGroup = $this->metric->classifyCalc($calcList);

        foreach($classifiedCalcGroup as $calcGroup)
        {
            if($this->config->edition == 'open' and in_array($calcGroup->dataset, array('getFeedbacks', 'getIssues', 'getRisks'))) continue;

            try
            {
                $statement = $this->metricZen->prepareDataset($calcGroup);
                if(empty($statement)) continue;

                $rows = $statement->fetchAll();
                $this->metricZen->calcMetric($rows, $calcGroup->calcList);

                $records = $this->metricZen->prepareMetricRecord($calcGroup->calcList);
                $this->metric->insertMetricLib($records);
            }
            catch(Exception $e)
            {
                a($e->getMessage());
            }
        }

        // 恢复之前的调试状态
        $this->config->debug = $originalDebug;

        if(dao::isError())
        {
            echo dao::getError();
            return false;
        }
        echo 'success';
    }

    /**
     * 获取数据表格的数据。
     * Get data of datatable.
     *
     * @param  int $metricID
     * @access public
     * @return string
     */
    public function ajaxCollectMetric($metricID)
    {
        $metric = $this->metric->getByID($metricID);

        $isCollect = strpos($metric->collector, ',' . $this->app->user->account . ',') !== false;
        $collector = explode(',', $metric->collector);

        if($isCollect)
        {
            $key = array_search($this->app->user->account, $collector);
            if($key) unset($collector[$key]);
        }
        else
        {
            $collector[] = $this->app->user->account;
        }
        $collector = array_filter($collector);

        $metric = new stdclass();
        $metric->collector = ',' . implode(',', $collector) . ',';
        $this->metric->updateMetricFields($metricID, $metric);

        $response = new stdclass();
        $response->result  = 'success';
        $response->collect = !$isCollect;

        echo json_encode($response);
    }

    /**
     * 获取度量项默认值和测试值的的下拉选项。
     * Get options of default value and query value.
     *
     * @param  string $optionType
     * @access public
     * @return string
     */
    public function ajaxGetControlOptions($optionType)
    {
        $options = $this->metric->getControlOptions($optionType);

        $optionList = array();
        foreach($options as $value => $option) $optionList[] = array('value' => $value, 'text' => $option, 'keys' => $option);

        return $this->send($optionList);
    }

    public function ajaxGetMetricSideTree()
    {
        $metricIDList = explode(',', $_POST['metricIDList']);
        $checkedList  = explode(',', $_POST['checkedList']);
        $metrics = $this->metric->getMetricsByIDList($metricIDList);

        $this->view->groupMetrics = $this->metric->groupMetricByObject($metrics);
        $this->view->checkedList  = $checkedList;
        $this->display();
    }

    public function ajaxGetMultipleMetricBox($metricID)
    {
        $metric = $this->metric->getByID($metricID);
        $result = $this->metric->getResultByCode($metric->code, array(), 'cron');
        $resultHeader = $this->metricZen->getViewTableHeader($metric);
        $resultData   = $this->metricZen->getViewTableData($metric, $result);

        $this->view->metric        = $metric;
        $this->view->resultHeader  = $resultHeader;
        $this->view->resultData    = $resultData;
        $this->view->chartTypeList = $this->metric->getChartTypeList($resultHeader);
        $this->view->echartOptions = $this->metric->getEchartsOptions($resultHeader, $resultData);

        $this->display();
    }

    public function ajaxGetTableAndCharts($metricID, $viewType = 'single')
    {
        $metric = $this->metric->getByID($metricID);
        $result = $this->metric->getResultByCode($metric->code, $_POST, 'cron');
        $resultHeader = $this->metricZen->getViewTableHeader($metric);
        $resultData   = $this->metricZen->getViewTableData($metric, $result);

        $this->view->viewType      = $viewType;
        $this->view->metric        = $metric;
        $this->view->resultHeader  = $resultHeader;
        $this->view->resultData    = $resultData;
        $this->view->chartTypeList = $this->metric->getChartTypeList($resultHeader);
        $this->view->echartOptions = $this->metric->getEchartsOptions($resultHeader, $resultData);

        $this->display();
    }

    /**
     * 获取数据表格的数据。
     * Get data of datatable.
     *
     * @param  int $metricID
     * @access public
     * @return string
     */
    public function ajaxGetEchartsOptions($metricID, $chartType = 'line')
    {
        $metric = $this->metric->getByID($metricID);
        $result = $this->metric->getResultByCode($metric->code, $_POST, 'cron');

        $resultHeader = $this->metricZen->getViewTableHeader($metric);
        $resultData   = $this->metricZen->getViewTableData($metric, $result);

        $echartOptions = $this->metric->getEchartsOptions($resultHeader, $resultData, $chartType);

        echo json_encode($echartOptions);
    }
}
