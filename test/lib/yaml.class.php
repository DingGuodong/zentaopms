<?php
/**
 * 本文件主要进行生成每个脚本文件对应的测试数据yaml文件。
 *
 * All request of entries should be routed by this router.
 *
 * @copyright   Copyright 2009-2017 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      liyang <liyang@easycorp.ltd>
 * @package     ZenTaoPMS
 * @version     1.0
 * @link        http://www.zentao.net/
 */
class fields
{
    /**
     * Field Arr.
     *
     * @var array
     * @access public
     */
    public $fieldArr = array();

    /**
     * Field.
     *
     * @var int
     * @access private
     */
    private $field;

    /**
     * Set yaml field.
     *
     * @param  string    $value
     * @access public
     * @return object
     */
    public function setField($value)
    {
        $this->fieldArr[$value] = array();
        $this->field = $value;
        return $this;
    }

    /**
     * Set field rang.
     *
     * @param  string    $range
     * @access public
     * @return object
     */
    public function range($range)
    {
        $this->fieldArr[$this->field]['range'] = $range;
        return $this;
    }

    /**
     * Set field prefix.
     *
     * @param  string    $prefix
     * @access public
     * @return object
     */
    public function prefix($prefix)
    {
        $this->fieldArr[$this->field]['prefix'] = $prefix;
        return $this;
    }

    /**
     * Set field postfix.
     *
     * @param  string    $postfix
     * @access public
     * @return object
     */
    public function postfix($postfix)
    {
        $this->fieldArr[$this->field]['postfix'] = $postfix;
        return $this;
    }

    /**
     * Set field type.
     *
     * @param  string    $type
     * @access public
     * @return object
     */
    public function type($type)
    {
        $this->fieldArr[$this->field]['type'] = $type;
        return $this;
    }

    /**
     * Set field format.
     *
     * @param  string    $format
     * @access public
     * @return object
     */
    public function format($format)
    {
        $this->fieldArr[$this->field]['format'] = $format;
        return $this;
    }

    /**
     * Set field fields.
     *
     * @param  array    $fields
     * @access public
     * @return object
     */
    public function setFields($fields)
    {
        if(!is_array($fields))
        {
            echo "fields must be an array";
            return;
        }

        $this->fieldArr[$this->field]['fields'] = $fields;
        return $this;
    }

    /**
     * Get field array.
     *
     * @access public
     * @return array
     */
    public function getFields()
    {
        return $this->fieldArr;
    }

    /**
     * Assembly field generation rules.
     *
     * @param  array     $fieldArr
     * @access public
     * @return array
     */
    public function setFieldRule($fieldArr)
    {
        $ruleArr = array();
        $index   = 0;

        foreach($fieldArr as $field => $rule)
        {
            $ruleArr[$index]['field'] = $field;

            if(array_key_exists('fields', $rule))
            {
                $ruleArr[$index]['fields'] = $this->setFieldRule($rule['fields']);
            }
            else
            {
                if(isset($rule['range'])) $ruleArr[$index]['range'] = $rule['range'];
            }

            if(isset($rule['prefix']))  $ruleArr[$index]['prefix']  = $rule['prefix'];
            if(isset($rule['postfix'])) $ruleArr[$index]['postfix'] = $rule['postfix'];
            if(isset($rule['type']))    $ruleArr[$index]['type']    = $rule['type'];
            if(isset($rule['format']))  $ruleArr[$index]['format']  = $rule['format'];

            $index ++;
        }

        return $ruleArr;
    }
}

/**
 * Create test data from yaml file.
 *
 * @copyright Copyright 2009-2022 QingDao Nature Easy Soft Network Technology Co,LTD (www.cnezsoft.com)
 * @author    liyang <liyang@easycorp.ltd>
 * @package
 * @uses      field
 * @license   LGPL
 * @version   1.0
 * @Link      https://www.zentao.net
 */
class yaml
{
    /**
     * Set fields for yaml file.
     *
     * @var int
     * @access public
     */
    public $fields;

    /**
     * Global config.
     *
     * @var object
     * @access public
     */
    public $config;

    /**
     * The generated data table name.
     *
     * @var string
     * @access public
     */
    public $tableName;

    /**
     * The config files will be merged.
     *
     * @var string[]
     * @access private
     */
    private $configFiles = array();

    /**
     * __construct function load config and tableName.
     * @param  string $tableName
     * @access public
     * @return void
     */
    public function __construct($tableName)
    {
        global $config;
        $this->config    = $config;
        $this->tableName = $tableName;
        $this->fields    = new fields();

        $yamlPath = dirname(dirname(__FILE__)) . "/data/{$this->tableName}.yaml";
        $this->configFiles[] = $yamlPath;

        if(!file_exists($yamlPath)) $this->buildYamlFile($this->tableName);
    }

    /**
     * Build the initial yaml file.
     *
     * @param  string $tableName
     * @access public
     * @return viod
     */
    public function buildYamlFile($tableName)
    {
        $yamlData['title']   = 'table zt_' . $tableName;
        $yamlData['author']  = 'automated export';
        $yamlData['version'] = '1.0';
        $yamlData['fields'][0]['field'] = 'id';
        $yamlData['fields'][0]['range'] = '1-1000';

        $yamlFile = dirname(dirname(__FILE__)) . "/data/{$this->tableName}.yaml";
        yaml_emit_file($yamlFile, $yamlData, YAML_UTF8_ENCODING);
    }

    /**
     * Yaml configuration file for script。
     *
     * @param  string $fileName
     * @param  bool   $useCommon
     * @access public
     * @return object $this
     */
    public function config($fileName, $useCommon = false)
    {
        $backtrace = debug_backtrace();
        $runPath   = $backtrace[count($backtrace)-1]['file'];

        if($useCommon)
        {
            $yamlFile = dirname($runPath, 2) . DS . 'yaml' . DS . "{$fileName}.yaml";
            if(is_file($yamlFile)) $this->configFiles[] = $yamlFile;
            return $this;
        }

        $runFileName = str_replace(strrchr($_SERVER['SCRIPT_FILENAME'], "."), "", $_SERVER['SCRIPT_FILENAME']);

        $pos = strripos($runFileName, DS);
        if($pos !== false) $runFileName = mb_substr($runFileName, $pos+1);

        $yamlFile = dirname($runPath) . DS . 'yaml' . DS . $runFileName . DS . "{$fileName}.yaml";

        /* Try to load common yaml file if yaml file not found in $runFileName path.*/
        if(!is_file($yamlFile)) $yamlFile = dirname($runPath, 2) . DS . 'yaml' . DS . "{$fileName}.yaml";

        if(is_file($yamlFile)) $this->configFiles[] = $yamlFile;

        return $this;
    }

    /**
     * Magic method, return fild.
     *
     * @param  string    $property_name
     * @access protected
     * @return object
     */
    public function __get($property_name)
    {
        $this->fields->setField($property_name);
        return $this->fields;
    }

    /**
     * 合并原始yaml与用户自定义yaml文件
     * Merege yaml file.
     *
     * @param string $runFileDir
     * @param string $runFileName
     * @access private
     * @return string
     */
    private function mergeYaml($runFileDir, $runFileName)
    {
        $mergeData = array('fields' => array());
        foreach($this->configFiles as $configFile)
        {
            $configData = yaml_parse_file($configFile);
            $configData['title']   = $configData['title'];
            $configData['author']  = $configData['author'];
            $configData['version'] = $configData['version'];

            foreach($configData['fields'] as $configItem)
            {
                $field = $configItem['field'];
                $mergeData['fields'][$field] = $configItem;
            }
        }

        if(!is_dir("{$runFileDir}/data")) mkdir("{$runFileDir}/data", 0777);
        $yamlFile = "{$runFileDir}/data/{$this->tableName}_{$runFileName}.yaml";

        if(!empty($this->fields->fieldArr))
        {
            $fields = $this->fields->setFieldRule($this->fields->fieldArr);
            foreach($fields as $field) $mergeData['fields'][$field['field']] = $field;
        }
        $mergeData['fields'] = array_values($mergeData['fields']);

        yaml_emit_file($yamlFile, $mergeData, YAML_UTF8_ENCODING);

        return $yamlFile;
    }

    /**
     * 查找当前执行脚本的相对路径与文件名
     * Find script path and name.
     *
     * @access private
     * @return array
     */
    private function getScriptPathAndName()
    {
        $runFileName = str_replace(strrchr($_SERVER['SCRIPT_FILENAME'], "."), "", $_SERVER['SCRIPT_FILENAME']);

        $pos = strripos($runFileName, DS);
        if($pos !== false) $runFileName = mb_substr($runFileName, $pos+1);

        $runFileDir = dirname($_SERVER['SCRIPT_FILENAME']);

        return array($runFileDir, $runFileName);
    }

    /**
     * Build yaml file and insert table.
     *
     * @param  int     $rows
     * @param  bool    $isClear Truncate table if set isClear to true.
     * @access public
     * @return void
     */
    public function gen($rows, $isClear = true)
    {
        list($runFileDir, $runFileName) = $this->getScriptPathAndName();

        $sqlPath    = sprintf("%s%sdata%ssql%s%s_%s_zd.sql", $runFileDir, DS, DS, DS, $this->tableName, $runFileName);
        $scriptPath = sprintf("%s%s%s.php", $runFileDir, DS, $runFileName);
        $yamlPath   = sprintf("%s%sdata%s%s_%s.yaml", $runFileDir, DS, DS, $this->tableName, $runFileName);

        if($rows && $this->checkNeedReGenerateSql($sqlPath, $scriptPath, $yamlPath))
        {
            $runtimeRoot = dirname(dirname(__FILE__)) . '/runtime/';
            $zdPath      = $runtimeRoot . 'zd';
            $configYaml  = $runtimeRoot . 'tmp/config.yaml';
            $tableName   = $this->config->db->prefix . $this->tableName;

            $yamlFile = $this->mergeYaml($runFileDir, $runFileName);

            $genSQL     = "$zdPath -c %s -d %s -n %d -t %s -o %s";
            $execGenSQL = sprintf($genSQL, $configYaml, $yamlFile, $rows, $tableName, $sqlPath);

            system($execGenSQL);
        }

        $this->insertDB($sqlPath, $this->tableName, $isClear);
    }

    /**
     * 根据脚本以及yaml文件修改时间判断是否需要重新生成sql文件.
     * Check if it is necessary to regenerate the SQL file.
     *
     * @param  string  $sqlPath
     * @param  string  $scriptPath
     * @param  string  $yamlPath
     * @access private
     * @return bool
     */
    private function checkNeedReGenerateSql($sqlPath, $scriptPath, $yamlPath)
    {
        $sqlFileUpdateTime = file_exists($sqlPath) ? filemtime($sqlPath) : 0;
        $scriptUpdateTime  = filemtime($scriptPath);

        if($sqlFileUpdateTime < $scriptUpdateTime) return true;

        foreach($this->configFiles as $configFile)
        {
            if($sqlFileUpdateTime < filemtime($configFile)) return true;
        }

        /* Check if the range function is used in the YAML file */
        $content = file_get_contents($yamlPath);
        if(preg_match('/range:.*?:R\s*(,|$|\n)/', $content)) return true;

        return false;
    }

    /**
     * Insert the data into database.
     *
     * @param  string    $sqlPath
     * @param  string    $tableName
     * @param  bool      $isClear Truncate table if set isClear to true.
     * @access public
     * @return string
     */
    function insertDB($sqlPath, $tableName, $isClear = true)
    {
        $tableName = $this->config->db->prefix . $tableName;
        $dbName    = $this->config->db->name;
        $dbHost    = $this->config->db->host;
        $dbPort    = $this->config->db->port;
        $dbUser    = $this->config->db->user;
        $dbPWD     = $this->config->db->password;

        $tableSqlDir = dirname($sqlPath);

        if(!is_dir($tableSqlDir)) mkdir($tableSqlDir, 0777, true);

        if($isClear === true)
        {
            /* Truncate table to reset auto increment number. */
            system(sprintf("mysql -u%s -p%s -h%s -P%s %s -e 'truncate %s' 2>/dev/null", $dbUser, $dbPWD, $dbHost, $dbPort, $dbName, $tableName));
        }

        if(!file_exists($sqlPath)) return;

        $command    = "mysql -u%s -p%s -h%s -P%s --default-character-set=utf8 -D%s < %s";
        $execInsert = sprintf($command, $dbUser, $dbPWD, $dbHost, $dbPort, $dbName, $sqlPath);
        $this->execWithStderr($execInsert);
    }

    /**
     * 执行系统命令，并捕捉错误。
     * exec command and catch stderror.
     *
     * @param  string    $cmd
     * @access private
     * @return void
     */
    private function execWithStderr($cmd)
    {
        $proc   = proc_open($cmd, array(2 => array('pipe', 'w')), $pipes);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[2]);
        proc_close($proc);

        if(empty($stderr)) return;

        $errors = explode(PHP_EOL, $stderr);
        $errors = array_filter($errors, function($error)
        {
            return !empty($error) && !strpos($error, 'Using a password on the command line interface can be insecure');
        });

        if(!empty($errors)) echo implode(PHP_EOL, $errors) . PHP_EOL . "error cmd: '{$cmd}'" . PHP_EOL;
    }

    /**
     * Restore table data.
     *
     * @param  string    $tableName
     * @access public
     * @return mixed
     */
    public function restoreTable($tableName)
    {
        $tableSql = "{$_SERVER['PWD']}/data/sql/$tableName.sql";
        if(!is_file($tableSql)) return false;

        $dbName = $this->config->db->name;
        $dbHost = $this->config->db->host;
        $dbPort = $this->config->db->port;
        $dbUser = $this->config->db->user;
        $dbPWD  = $this->config->db->password;

        $command     = "mysql -u%s -p%s -h%s -P%s %s < %s";
        $execRestore = sprintf($command, $dbUser, $dbPWD, $dbHost, $dbPort, $dbName, $tableSql);
        system($execRestore);
    }
}

/**
 * Return yaml class
 *
 * @param  string $table
 * @access public
 * @return mixed
 */
function zdTable($table)
{
    return new yaml($table);
}
