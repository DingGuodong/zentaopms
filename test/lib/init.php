<?php
/**
 * 本文件创建一个 app 实例，并且通过执行 $app->loadCommon() 方法创建名为 tester 的commonModel对象。
 * This file build a app instance and provide a instance of commonModel named as tester by $app->loadCommon().
 *
 * All request of entries should be routed by this router.
 *
 * @copyright   Copyright 2009-2017 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Guanxing <guanxiying@easycorp.ltd>
 * @package     ZenTaoPMS
 * @version     $Id: $
 * @link        http://www.zentao.net
 */
/* Set the error reporting. */
error_reporting(E_ALL);
define('RUN_MODE', 'test');

if($argc > 1 && $argv[1] == '-extract')
{
    printSteps();
    exit;
}

$testPath      = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR;
$frameworkRoot = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR;

/**
 * Assert status code and set body as $_result.
 *
 * @param  int $code
 * @access public
 * @return bool
 */
function c($code)
{
    global $_result;
    if(empty($_result) or empty($_result->status_code)) return false;

    $codeStr       = (string)$code;
    $statusCodeStr = (string)($_result->status_code);

    if($_result->status_code == $code or ($statusCodeStr[0] === '2' and $codeStr[0] === '2'))
    {
        $_result = $_result->body;
        return true;
    }

    echo "\n\n";
    return false;
}

/* Load the framework. */
include $frameworkRoot . 'router.class.php';
include $frameworkRoot . 'control.class.php';
include $frameworkRoot . 'model.class.php';
include $frameworkRoot . 'helper.class.php';

$app    = router::createApp('pms', dirname(dirname(__FILE__)), 'router');
$poolID = getenv('ZTF_POOL_ID');
$testDB = empty($config->testDB) ? array() : $config->testDB;

/* 根据ztf设置的poolID环境变量设置连接的数据库 */
if(!empty($testDB) && !empty($poolID)) 
{
    $selectDB = $testDB[$poolID%count($testDB)];

    !empty($selectDB['host'])     && $config->db->host     = $selectDB['host'];
    !empty($selectDB['port'])     && $config->db->port     = $selectDB['port'];
    !empty($selectDB['name'])     && $config->db->name     = $selectDB['name'];
    !empty($selectDB['user'])     && $config->db->user     = $selectDB['user'];
    !empty($selectDB['password']) && $config->db->password = $selectDB['password'];

    $app->connectDB();
}

$tester = $app->loadCommon();

/* Set configs. */
$config->zendataRoot = dirname(dirname(__FILE__)) . '/zendata';
$config->ztfPath     = dirname(dirname(__FILE__)) . '/tools/ztf';
$config->zdPath      = dirname(dirname(__FILE__)) . '/tools/zd';

/* init testDB. */
include $testPath . 'lib/yaml.class.php';
include $testPath . 'lib/rest.php';

if(!empty($config->test->account) and !empty($config->test->password) and !empty($config->test->base))
{
    $rest  = new rest($config->test->base);
    $token = $rest->post('/tokens', array('account' => $config->test->account, 'password' => $config->test->password));
    $token = $token->body;
}


/**
 * Save variable to $_result.
 *
 * @param  mixed    $result
 * @access public
 * @return bool true
 */
function r($result)
{
    global $_result;
    $_result = $result;
    return true;
}

/**
 * Print value or properties.
 *
 * @param  string    $key
 * @param  string    $delimiter
 * @access public
 * @return void
 */
function p($keys = '', $delimiter = ',')
{
    global $_result;

    if(empty($_result)) return print(implode("\n", array_fill(0, substr_count($keys, $delimiter) + 1, 0)) . "\n");

    if(is_array($_result) and isset($_result['code']) and $_result['code'] == 'fail') return print((string) $_result['message'] . "\n");

    /* Print $_result. */
    if(!$keys and is_array($_result)) return print_r($_result) . "\n";
    if(!$keys or !is_array($_result) and !is_object($_result)) return print((string) $_result . "\n");

    $parts  = explode(';', $keys);
    foreach($parts as $part)
    {
        $values = getValues($_result, $part, $delimiter);
        if(!is_array($values)) continue;

        foreach($values as $value) echo $value . "\n";
    }

    return true;
}

/**
 * 生成用例步骤描述
 * Generate step desc.
 *
 * @param  string   $userStep
 * @param  string   $moduleName
 * @param  string   $methodName
 * @param  string   $methodParam
 * @param  string   $isGrup
 * @access public
 * @return void
 */
function genStepDesc($userStep, $moduleName, $methodName, $methodParam, $isGrup)
{
    if($userStep) return '- ' . $userStep . ($isGrup ? "\n" : '');

    if ($methodName === 0 && $methodParam === 0)
    {
        $stepDesc = "- 执行{$moduleName}" . ($isGrup ? "\n" : '');
    }
    elseif($methodParam)
    {
        $stepDesc = "- 执行{$moduleName}模块的{$methodName}方法，参数是{$methodParam}" . ($isGrup ? "\n" : '');
    }
    else
    {
        $stepDesc = "- 执行{$moduleName}模块的{$methodName}方法" . ($isGrup ? "\n" : '');
    }

    return $stepDesc;
}

/**
 * 生成用例步骤并输出（ztf使用）
 * extract ZTF note.
 *
 * @param  string $key
 * @access public
 * @return void
 */
function printSteps()
{
    $debugInfo = debug_backtrace();
    if(empty($debugInfo)) return;

    $file     = $debugInfo[count($debugInfo)-1]['file'];
    $contents = file_get_contents($file);
    $rpeList  = genParamsByRPE($contents);
    $desc     = '';

    foreach($rpeList as $rpe)
    {
        list($moduleName, $methodName, $methodParam) = $rpe[0];
        $expectStr = trim($rpe[1], '"\'');
        $pParam    = $rpe[2];
        $userStep  = trim(str_replace('//', '', $rpe[3]));
        $keys      = $pParam[0];
        $delimiter = $pParam[1] ? $pParam[1] : ',';
        $isGrup    = false;
        $stepDesc  = '';
        $expects   = '' === $expectStr ? array() : explode($delimiter, $expectStr);

        $rowIndex = -1;
        $pos      = strpos($keys, ':');
        if($pos)
        {
            $arrKey   = substr($keys, 0, $pos);
            $keys     = substr($keys, $pos + 1);
            $rowIndex = $arrKey;
        }
        $keys = empty($keys) ? array() : explode($delimiter, $keys);
        if(count($keys) > 1) $isGrup = true;

        $stepDesc = genStepDesc($userStep, $moduleName, $methodName, $methodParam, $isGrup);
        if(empty($keys)) $stepDesc .= " @{$expectStr}\n";

        foreach($keys as $index => $row)
        {
            $stepExpect = isset($expects[$index]) ? $expects[$index] : '';
            if(count($expects) == 1) $stepExpect = $expectStr;
            if($rowIndex == -1)
            {
                $stepDesc .= ($isGrup ? ' - ' : '') . ($row ? "属性{$row}" : '') . " @{$stepExpect}\n";
            }
            else
            {
                $stepDesc .= ($isGrup ? ' - ' : '') . "第{$rowIndex}条的{$row}属性 @{$stepExpect}\n";
            }
        }

        $desc .= $stepDesc . "\n";
    }

    echo trim($desc);
}

/**
 * 从p()函数提取传入参数
 * Split function params.
 *
 * @param  array $params
 * @return array
 */
function splitParam($params)
{
    $newParams = array();
    foreach($params as $param)
    {
        $param          = trim($param);
        $firstSymbol    = substr($param, 0, 1);
        $paramList      = str_split($param);
        $delimiterIndex = -1;

        foreach($paramList as $i => $p)
        {
            if($i == 0) continue;
            if($p === $firstSymbol && (!isset($paramList[$i-1]) || '\\' != $paramList[$i-1]))
            {
                $delimiterIndex = $i + 1;
                break;
            }
        }

        if($delimiterIndex === -1)
        {
            $newParams[] = array($param, '');
            continue;
        }

        $firstParam  = substr($param, 0, $delimiterIndex);
        $firstParam  = trim(trim($firstParam), '\'"');
        $lastParam   = substr($param, $delimiterIndex + 1);
        $lastParam   = trim(trim($lastParam), '\'"');
        $newParams[] = array($firstParam, $lastParam);
    }

    return $newParams;
}

/**
 * 从r()函数提取调用的moduleName,methodName,methodParam
 * Generate module,method,param from r function.
 *
 * @param  array $rParams
 * @return array
 */
function genModuleAndMethod($rParams)
{
    $newParams = array();
    foreach($rParams as $index => $param)
    {
        $param = trim($param, "'");
        if($param[0] != '$') $param = trim(strchr($param, '$'), ')');

        $objArrowCount        = substr_count($param, '->');
        $rParamsStructureList = explode('->', $param);

        if($objArrowCount == 1)
        {
            $moduleName  = substr($rParamsStructureList[0], 1);
            $method      = $rParamsStructureList[1];
            $methodName  = substr(explode('(', $method)[0], 0);
            $methodParam = substr(explode('(', $method)[1], 0);
            $methodParam = trim($methodParam, "'()");
        }
        elseif($objArrowCount == 2)
        {
            $moduleName  = $rParamsStructureList[1];
            $method      = $rParamsStructureList[2];
            $methodName  = explode('(', $method)[0];
            $methodParam = trim(substr(explode('(', $method)[1], 0), "'()");
        }
        else
        {
            $newParams[] = array($param, 0, 0);
            continue;
        }

        $methodParam = preg_replace("/,\s*'/", ', ', $methodParam);
        $newParams[] = array($moduleName, $methodName, $methodParam);
    }

    return $newParams;
}

/**
 * 从RPE中获取模块，方法以及参数
 * Generate method,module,params from rpe.
 *
 * @param  string $rpe
 * @return array
 */
function genParamsByRPE($rpe)
{
    preg_match_all("/r\((.*?)\)\s*&&\s*p\((.*?)\)\s*&&\s*e\((.*?)\);(.*)/", $rpe, $matches);
    $rParams  = !empty($matches[1]) ? $matches[1] : array();
    $pParams  = !empty($matches[2]) ? $matches[2] : array();
    $eParams  = !empty($matches[3]) ? $matches[3] : array();
    $stepList = !empty($matches[4]) ? $matches[4] : array();
    $rParams  = is_array($rParams) ? $rParams : array($rParams);
    $pParams  = is_array($pParams) ? $pParams : array($pParams);
    $eParams  = is_array($eParams) ? $eParams : array($eParams);

    $pParamsList = splitParam($pParams);
    $rpeList     = array();
    $rParamList  = genModuleAndMethod($rParams);

    foreach($rParamList as $index => $param) $rpeList[] = array($param, $eParams[$index], $pParamsList[$index], $stepList[$index]);

    return $rpeList;
}

/**
 * Get values
 *
 * @param mixed  $value
 * @param string $keys
 * @param string $delimiter
 * @access public
 * @return void
 */
function getValues($value, $keys, $delimiter)
{
    $object = '';
    $index  = -1;
    $pos    = strpos($keys, ':');
    if($pos)
    {
        $arrKey = substr($keys, 0, $pos);
        $keys   = substr($keys, $pos + 1);

        $pos = strpos($arrKey, '[');
        if($pos)
        {
            $object = substr($arrKey, 0, $pos);
            $index  = trim(substr($arrKey, $pos + 1), ']');
        }
        else
        {
            $index = $arrKey;
        }
    }
    $keys = explode($delimiter, $keys);

    if($object)
    {
        if(is_array($value))
        {
            $value = $value[$object];
        }
        else if(is_object($value))
        {
            $value = $value->$object;
        }
        else
        {
            return print("Error: No object name '$object'.\n");
        }
    }

    if($index != -1)
    {
        if(is_array($value))
        {
            if(!isset($value[$index])) return print("Error: Cannot get index $index.\n");
            $value = $value[$index];
        }
        else if(is_object($value))
        {
            if(!isset($value->$index)) return print("Error: Cannot get index $index.\n");
            $value = $value->$index;
        }
        else
        {
            return print("Error: Not array, cannot get index $index.\n");
        }
    }

    $values = array();
    foreach($keys as $key) $values[] = zget($value, $key, '');

    return $values;
}

/**
 * Expect values, ztf will put params to step.
 *
 * @param  string    $exepect
 * @access public
 * @return void
 */
function e($expect)
{
}

/**
 * Check order
 *
 * @param array  $objs
 * @param string $orderBy
 * @access public
 * @return bool
 */
function checkOrder($objs, $orderBy)
{
    if(empty($objs)) return true;

    list($field, $sort) = explode('_', $orderBy);
    $last = current($objs)->$field;
    foreach($objs as $obj)
    {
        if($sort == 'desc')
        {
            if($obj->$field > $last) return false;
        }
        else
        {
            if($obj->$field < $last) return false;
        }
    }

    return true;
}

/**
 * Import data create by zendata to one table.
 *
 * @param  string    $table
 * @param  string    $yaml
 * @param  int       $count
 * @access public
 * @return void
 */
function zdImport($table, $yaml, $count = 10)
{
    chdir(dirname(__FILE__));

    global $app, $config;
    $dns   = "mysql://{$config->db->user}:{$config->db->password}@{$config->db->host}:{$config->db->port}/{$config->db->name}#utf8";
    $table = trim($table, '`');

    $command = "$config->zdPath -c $yaml -t $table -T -dns $dns --clear -n $count";
    system($command);
}

/**
 * Switch user.
 *
 * @param  string $account
 * @access public
 * @return bool
 */
function su($account)
{
    $userModel = new userModel();
    $user      = $userModel->getByID($account);
    if($user) return $userModel->login($user);

    $user = $userModel->identify($account, $user->password);
    return false;
}
