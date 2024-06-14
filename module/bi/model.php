<?php

class biModel extends model
{
    /**
     * Get object options.
     *
     * @param  string $type user|product|project|execution|dept
     * @access public
     * @return array
     */
    public function getScopeOptions($type)
    {
        $options = array();
        switch($type)
        {
            case 'user':
                $options = $this->loadModel('user')->getPairs('noletter');
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
        }

        return $options;
    }

    /**
     * Get object options.
     *
     * @param  string $object
     * @param  string $field
     * @access public
     * @return array
     */
    public function getDataviewOptions($object, $field)
    {
        $options = array();
        $path    = $this->app->getModuleRoot() . 'dataview' . DS . 'table' . DS . "$object.php";
        if(is_file($path))
        {
            include $path;
            $options = $schema->fields[$field]['options'];
        }

        return $options;
    }

    /**
     * Get object options.
     *
     * @param  string $object
     * @param  string $field
     * @access public
     * @return array
     */
    public function getObjectOptions($object, $field)
    {
        $options = array();
        $useTable = $object;
        $useField = $field;

        $path = $this->app->getModuleRoot() . 'dataview' . DS . 'table' . DS . "$object.php";
        if(is_file($path))
        {
            include $path;
            $fieldObject = isset($schema->fields[$field]['object']) ? $schema->fields[$field]['object'] : '';
            $fieldShow   = isset($schema->fields[$field]['show']) ? explode('.', $schema->fields[$field]['show']) : array();

            if($fieldObject) $useTable = $fieldObject;
            if(count($fieldShow) == 2) $useField = $show[1];
        }

        $table = isset($this->config->objectTables[$useTable]) ? $this->config->objectTables[$useTable] : zget($this->config->objectTables, $object, '');
        if($table)
        {
            $columns = $this->dbh->query("SHOW COLUMNS FROM $table")->fetchAll();
            foreach($columns as $id => $column) $columns[$id] = (array)$column;
            $fieldList = array_column($columns, 'Field');

            $useField = in_array($useField, $fieldList) ? $useField : 'id';
            $options = $this->dao->select("id, {$useField}")->from($table)->fetchPairs();
        }

        return $options;
    }

    /**
     * Get pairs from column by keyField and valueField.
     *
     * @param  string $sql
     * @param  string $keyField
     * @param  string $valueField
     * @access public
     * @return array
     */
    public function getOptionsFromSql(string $sql, string $keyField, string $valueField): array
    {
        $options = array();
        $cols    = $this->dbh->query($sql)->fetchAll();
        $sample  = current($cols);

        if(!isset($sample->$keyField) or !isset($sample->$valueField)) return $options;

        foreach($cols as $col)
        {
            $key   = $col->$keyField;
            $value = $col->$valueField;
            $options[$key] = $value;
        }

        return $options;
    }

    /**
     * 生成水球图参数。
     * Generate water polo options.
     *
     * @param  array $fields
     * @param  array $settings
     * @param  string $sql
     * @param  array $filters
     * @access public
     * @return array
     */
    public function genWaterpolo(array $fields, array $settings, string $sql, array $filters): array
    {
        $this->loadModel('chart');
        $operate = "{$settings['calc']}({$settings['goal']})";
        $sql = "select $operate count from ($sql) tt ";

        $moleculeSQL    = $sql;
        $denominatorSQL = $sql;

        $moleculeWheres    = array();
        $denominatorWheres = array();
        foreach($settings['conditions'] as $condition)
        {
            $where = "{$condition['field']} {$this->lang->chart->conditionList[$condition['condition']]} '{$condition['value']}'";
            $moleculeWheres[]    = $where;
        }

        if(!empty($filters))
        {
            $wheres = array();
            foreach($filters as $field => $filter)
            {
                $wheres[] = "$field {$filter['operator']} {$filter['value']}";
            }
            $moleculeWheres    = array_merge($moleculeWheres, $wheres);
            $denominatorWheres = $wheres;
        }

        if($moleculeWheres)    $moleculeSQL    .= 'where ' . implode(' and ', $moleculeWheres);
        if($denominatorWheres) $denominatorSQL .= 'where ' . implode(' and ', $denominatorWheres);

        $molecule    = $this->dao->query($moleculeSQL)->fetch();
        $denominator = $this->dao->query($denominatorSQL)->fetch();

        $percent = $denominator->count ? round((int)$molecule->count / (int)$denominator->count, 4) : 0;

        $series  = array(array('type' => 'liquidFill', 'data' => array($percent), 'color' => array('#2e7fff'), 'outline' => array('show' => false), 'label' => array('fontSize' => 26)));
        $tooltip = array('show' => true);
        $options = array('series' => $series, 'tooltip' => $tooltip);

        return $options;
    }

    /**
     * Get multi data.
     *
     * @param  int    $settings
     * @param  int    $defaultSql
     * @param  int    $filters
     * @access public
     * @return void
     */
    public function getMultiData($settings, $defaultSql, $filters, $sort = false)
    {
        $this->loadModel('chart');

        $group   = isset($settings['xaxis'][0]['field']) ? $settings['xaxis'][0]['field'] : '';
        $date    = isset($settings['xaxis'][0]['group']) ? zget($this->config->chart->dateConvert, $settings['xaxis'][0]['group']) : '';
        $metrics = array();
        $aggs    = array();
        foreach($settings['yaxis'] as $yaxis)
        {
            $metrics[] = $yaxis['field'];
            $aggs[]    = $yaxis['valOrAgg'];
        }
        $yCount  = count($metrics);

        $xLabels = array();
        $yStats  = array();

        for($i = 0; $i < $yCount; $i ++)
        {
            $metric   = $metrics[$i];
            $agg      = $aggs[$i];

            $groupSql   = $groupBySql = "tt.`$group`";
            if(!empty($date))
            {
                $groupSql   = $date == 'MONTH' ? "YEAR(tt.`$group`) as ttyear, $date(tt.`$group`) as ttgroup" : "$date(tt.`$group`) as $group";
                $groupBySql = $date == 'MONTH' ? "YEAR(tt.`$group`), $date(tt.`$group`)" : "$date(tt.`$group`)";
            }

            if($agg == 'distinct')
            {
                $aggSQL = "count($agg tt.`$metric`) as `$metric`";
            }
            else
            {
                $aggSQL = "$agg(tt.`$metric`) as `$metric`";
            }

            $sql = "select $groupSql,$aggSQL from ($defaultSql) tt";
            if(!empty($filters))
            {
                $wheres = array();
                foreach($filters as $field => $filter)
                {
                    $wheres[] = "`$field` {$filter['operator']} {$filter['value']}";
                }

                $whereStr = implode(' and ', $wheres);
                $sql .= " where $whereStr";
            }
            $sql .= " group by $groupBySql";
            $rows = $this->dao->query($sql)->fetchAll();
            $stat = $this->processRows($rows, $date, $group, $metric);

            $maxCount = 50;
            if($sort) arsort($stat);
            $yStats[] = $stat;

            $xLabels = array_merge($xLabels, array_keys($stat));
            $xLabels = array_unique($xLabels);
        }

        return array($group, $metrics, $aggs, $xLabels, $yStats);
    }

    /**
     * Process rows.
     *
     * @param  array  $rows
     * @param  string $date
     * @param  string $group
     * @param  string $metric
     * @access public
     * @return array
     */
    public function processRows($rows, $date, $group, $metric)
    {
        $this->loadModel('chart');

        $stat = array();
        foreach($rows as $row)
        {
            if(!empty($date) and $date == 'MONTH')
            {
                $stat[sprintf("%04d", $row->ttyear) . '-' . sprintf("%02d", $row->ttgroup)] = $row->$metric;
            }
            elseif(!empty($date) and $date == 'YEARWEEK')
            {
                $yearweek  = sprintf("%06d", $row->$group);
                $year = substr($yearweek, 0, strlen($yearweek) - 2);
                $week = substr($yearweek, -2);

                $weekIndex = in_array($this->app->getClientLang(), array('zh-cn', 'zh-tw')) ? sprintf($this->lang->chart->groupWeek, $year, $week) : sprintf($this->lang->chart->groupWeek, $week, $year);
                $stat[$weekIndex] = $row->$metric;
            }
            elseif(!empty($date) and $date == 'YEAR')
            {
                $stat[sprintf("%04d", $row->$group)] = $row->$metric;
            }
            else
            {
                $stat[$row->$group] = $row->$metric;
            }
        }

        return $stat;
    }

    /**
     * 准备内置的图表sql语句。
     * Prepare builtin chart sql.
     *
     * @access public
     * @return array
     */
    public function prepareBuiltinChartSQL($operate = 'insert')
    {
        $charts = $this->config->bi->builtin->charts;

        $chartSQLs = array();
        foreach($charts as $chart)
        {
            $currentOperate = $operate;
            $chart = (object)$chart;
            if(isset($chart->settings)) $chart->settings = $this->jsonEncode($chart->settings);
            if(isset($chart->filters))  $chart->filters  = $this->jsonEncode($chart->filters);
            if(isset($chart->fields))   $chart->fields   = $this->jsonEncode($chart->fields);
            if(isset($chart->langs))    $chart->langs    = $this->jsonEncode($chart->langs);

            $exists = $this->dao->select('id')->from(TABLE_CHART)->where('id')->eq($chart->id)->fetch();
            if(!$exists) $currentOperate = 'insert';

            $stmt = null;
            if($currentOperate == 'insert')
            {
                $chart->createdBy   = 'system';
                $chart->createdDate = helper::now();
                $stmt = $this->dao->insert(TABLE_CHART)->data($chart);
            }
            if($currentOperate == 'update')
            {
                $id = $chart->id;
                unset($chart->group);
                unset($chart->id);
                $stmt = $this->dao->update(TABLE_CHART)->data($chart)->where('id')->eq($id);
            }

            if(isset($stmt)) $chartSQLs[] = $stmt->get();
        }

        return $chartSQLs;
    }

    /**
     * 准备内置的透视表sql语句。
     * Prepare builtin pivot sql.
     *
     * @param  string  $operate
     * @access public
     * @return array
     */
    public function prepareBuiltinPivotSQL($operate = 'insert')
    {
        $pivots = $this->config->bi->builtin->pivots;

        $pivotSQLs = array();
        foreach($pivots as $pivot)
        {
            $currentOperate = $operate;
            $pivot = (object)$pivot;
            $pivot->name     = $this->jsonEncode($pivot->name);
            if(isset($pivot->desc))     $pivot->desc     = $this->jsonEncode($pivot->desc);
            if(isset($pivot->settings)) $pivot->settings = $this->jsonEncode($pivot->settings);
            if(isset($pivot->filters))  $pivot->filters  = $this->jsonEncode($pivot->filters);
            if(isset($pivot->fields))   $pivot->fields   = $this->jsonEncode($pivot->fields);
            if(isset($pivot->langs))    $pivot->langs    = $this->jsonEncode($pivot->langs);
            if(isset($pivot->vars))     $pivot->vars     = $this->jsonEncode($pivot->vars);

            $exists = $this->dao->select('id')->from(TABLE_PIVOT)->where('id')->eq($pivot->id)->fetch();
            if(!$exists) $currentOperate = 'insert';

            $stmt = null;
            if($currentOperate == 'insert')
            {
                $pivot->createdBy   = 'system';
                $pivot->createdDate = helper::now();
                $stmt = $this->dao->insert(TABLE_PIVOT)->data($pivot);
            }
            if($currentOperate == 'update')
            {
                $id = $pivot->id;
                unset($pivot->group);
                unset($pivot->id);
                $stmt = $this->dao->update(TABLE_PIVOT)->data($pivot)->where('id')->eq($id);
            }

            if(isset($stmt)) $pivotSQLs[] = $stmt->get();
        }

        return $pivotSQLs;
    }

    /**
     * 准备内置的度量项sql语句。
     * Prepare builtin metric sql.
     *
     * @param  string  $operate
     * @access public
     * @return array
     */
    public function prepareBuiltinMetricSQL($operate = 'insert')
    {
        $metrics = $this->config->bi->builtin->metrics;

        $metricSQLs = array();
        $this->dao->delete()->from(TABLE_METRIC)
            ->where('builtin')->eq('1')
            ->andWhere('code')->notIn(array_column($metrics, 'code'))
            ->andWhere('type')->eq('php')
            ->exec();
        foreach($metrics as $metric)
        {
            $currentOperate = $operate;
            $metric = (object)$metric;
            $metric->stage   = 'released';
            $metric->type    = 'php';
            $metric->builtin = '1';

            $exists = $this->dao->select('code')->from(TABLE_METRIC)->where('code')->eq($metric->code)->fetch();
            if(!$exists) $currentOperate = 'insert';

            $stmt = null;
            if($currentOperate == 'insert')
            {
                $metric->createdBy   = 'system';
                $metric->createdDate = helper::now();
                $stmt = $this->dao->insert(TABLE_METRIC)->data($metric);
            }
            if($currentOperate == 'update')
            {
                $code = $metric->code;
                unset($metric->code);
                $stmt = $this->dao->update(TABLE_METRIC)->data($metric)->where('code')->eq($code);
            }

            if(isset($stmt)) $metricSQLs[] = $stmt->get();
        }

        return $metricSQLs;
    }

    /**
     * 准备内置的大屏sql语句。
     * Prepare builtin screen sql.
     *
     * @param  string  $operate
     * @access public
     * @return array
     */
    public function prepareBuiltinScreenSQL($operate = 'insert')
    {
        $screens = $this->config->bi->builtin->screens;

        $screenSQLs = array();
        foreach($screens as $screenID)
        {
            $currentOperate = $operate;
            $screenJson = file_get_contents(__DIR__ . DS . 'json' . DS . "screen{$screenID}.json");
            $screen = json_decode($screenJson);
            if(isset($screen->scheme)) $screen->scheme = json_encode($screen->scheme, JSON_UNESCAPED_UNICODE);

            $exists = $this->dao->select('id')->from(TABLE_SCREEN)->where('id')->eq($screenID)->fetch();

            if(!$exists) $currentOperate = 'insert';

            $screen->status = 'published';

            $stmt = null;
            if($currentOperate == 'insert')
            {
                $screen->createdBy   = 'system';
                $screen->createdDate = helper::now();
                $stmt = $this->dao->insert(TABLE_SCREEN)->data($screen);
            }
            if($currentOperate == 'update')
            {
                $id = $screen->id;
                unset($screen->id);
                $stmt = $this->dao->update(TABLE_SCREEN)->data($screen)->where('id')->eq($id);
            }

            if(isset($stmt)) $screenSQLs[] = $stmt->get();
        }

        return $screenSQLs;
    }

    /**
     * Process filter variables in sql.
     *
     * @param  string    $sql
     * @param  array  $filters
     * @access public
     * @return string
     */
    public function processVars($sql, $filters = array())
    {
        return $sql;
    }

    /**
     * Build statement object from sql.
     *
     * @param  string    $sql
     * @access public
     * @return object
     */
    public function sql2Statement($sql)
    {
        $this->app->loadClass('sqlparser', true);
        $parser = new sqlparser($sql);

        if(count($parser->statements) == 0) return $this->lang->dataview->empty;
        if(count($parser->statements) > 1)  return $this->lang->dataview->onlyOne;

        $statement = $parser->statements[0];
        if($statement instanceof PhpMyAdmin\SqlParser\Statements\SelectStatement == false) return $this->lang->dataview->allowSelect;

        return $statement;
    }

    /**
     * Validate sql.
     *
     * @param  string    $sql
     * @access public
     * @return string|true
     */
    public function validateSql($sql)
    {
        $this->loadModel('dataview');

        if(empty($sql)) return $this->lang->dataview->empty;
        try
        {
            $rows = $this->dbh->query("EXPLAIN $sql")->fetchAll();
        }
        catch(Exception $e)
        {
            return $e->getMessage();
        }

        $sqlColumns = $this->dao->getColumns($sql);
        list($isUnique, $repeatColumn) = $this->dataview->checkUniColumn($sql, true, $sqlColumns);

        if(!$isUnique) return sprintf($this->lang->dataview->duplicateField, implode(',', $repeatColumn));

        return true;
    }

    /**
     * Prepare pager from sql.
     *
     * @param  object    $statement
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return string
     */
    public function prepareSqlPager($statement, $recPerPage, $pageID)
    {
        if(!$statement->limit)
        {
            $statement->limit = new stdclass();
        }
        $statement->limit->offset   = $recPerPage * ($pageID - 1);
        $statement->limit->rowCount = $recPerPage;

        $statement->options->options[] = 'SQL_CALC_FOUND_ROWS';

        $limitSql = $statement->build();

        return $limitSql;
    }

    /**
     * Prepare columns setting from sql.
     *
     * @param  string    $sql
     * @param  object    $statement
     * @access public
     * @return array
     */
    public function prepareColumns($sql, $statement)
    {
        $this->loadModel('chart');
        $this->loadModel('dataview');
        $sqlColumns   = $this->dao->getColumns($sql);
        $columnTypes  = $this->dataview->getColumns($sql, $sqlColumns);
        $columnFields = array();
        foreach($columnTypes as $column => $type) $columnFields[$column] = $column;

        $tableAndFields = $this->chart->getTables($sql);
        $tables   = $tableAndFields['tables'];
        $fields   = $tableAndFields['fields'];
        $querySQL = $tableAndFields['sql'];

        $moduleNames = array();
        $aliasNames  = array();
        if($tables)
        {
            $moduleNames = $this->dataview->getModuleNames($tables);
            $aliasNames  = $this->dataview->getAliasNames($statement, $moduleNames);
        }

        list($fields, $objects) = $this->dataview->mergeFields($columnFields, $fields, $moduleNames, $aliasNames);

        $columns = array();
        foreach($fields as $field => $name)
        {
            $columns[$field] = array('name' => $name, 'field' => $field, 'type' => $columnTypes->$field, 'object' => $objects[$field]);
        }

        return $columns;
    }

    /**
     * Query sql.
     *
     * @param  string    $sql
     * @param  int    $recPerPage
     * @param  int    $pageID
     * @access public
     * @return object
     */
    public function querySql($sql, $recPerPage = 100, $pageID = 1)
    {
        $queryResult = new stdclass();
        $queryResult->error    = false;
        $queryResult->errorMsg = '';
        $queryResult->cols     = array();
        $queryResult->rows     = array();
        $queryResult->sql      = '';
        $queryResult->fieldSettings = array();

        $sql = $this->processVars($sql);

        $statement = $this->sql2Statement($sql);
        if(is_string($statement))
        {
            $queryResult->error    = true;
            $queryResult->errorMsg = $statement;

            return $queryResult;
        }

        $checked = $this->validateSql($sql);
        if($checked !== true)
        {
            $queryResult->error    = true;
            $queryResult->errorMsg = $checked;

            return $queryResult;
        }

        $limitSql = $this->prepareSqlPager($statement, $recPerPage, $pageID);

        try
        {
            $queryResult->rows      = $this->dbh->query($limitSql)->fetchAll();
            $queryResult->rowsCount = $this->dbh->query("SELECT FOUND_ROWS() as count")->fetch()->count;
        }
        catch(Exception $e)
        {
            $queryResult->error = true;
            $queryResult->errorMsg = $e;

            return $queryResult;
        }

        $columns    = $this->prepareColumns($limitSql, $statement);
        $clientLang = $this->app->getClientLang();

        $fieldSettings = array();
        foreach($columns as $field => $settings)
        {
            $title = $settings['name'];

            if(!isset($settings[$clientLang]) || empty($settings[$clientLang])) $settings[$clientLang] = $title;
            $fieldSettings[$field] = $settings;
        }

        $queryResult->cols          = $this->buildQueryResultTableColumns($fieldSettings);
        $queryResult->fieldSettings = $fieldSettings;
        $queryResult->sql           = $limitSql;

        return $queryResult;
    }

    /**
     * Build table columns from query result.
     *
     * @param  array    $fieldSettings
     * @access public
     * @return array
     */
    public function buildQueryResultTableColumns($fieldSettings)
    {
        $cols = array();
        foreach($fieldSettings as $field => $settings)
        {
            $title = $settings['name'];
            $type  = $settings['type'];
            $cols[] = array('name' => $field, 'title' => $title, 'type' => $type, 'sortType' => false);
        }

        return $cols;
    }

    /**
     * Prepare field objects.
     *
     * @access public
     * @return array
     */
    public function prepareFieldObjects()
    {
        $this->loadModel('dataview');
        $options = array();
        foreach($this->lang->dataview->objects as $table => $name)
        {
            $fields = $this->dataview->getTypeOptions($table);
            $options[] = array('text' => $name, 'value' => $table, 'fields' => $fields);
        }

        return $options;
    }

    /**
     * Encode json.
     *
     * @param  object|array  $object
     * @access private
     * @return string|null
     */
    private function jsonEncode($object)
    {
        if(empty($object)) return null;
        if(is_scalar($object)) return $object;
        return json_encode($object);
    }
}
