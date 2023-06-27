<?php

/**
 * The helper functions and classes for ZentaoPHP file of zin of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Hao Sun <sunhao@easycorp.ltd>
 * @package     zin
 * @link        https://www.zentao.net
 */

namespace zin;

require_once __DIR__ . DS . 'pager.func.php';

class helper extends \helper
{
}

class html extends \html
{
}

class commonModel extends \commonModel
{
}

class common extends \commonModel
{
}

/**
 * Created by typecasting to object.
 *
 * @link https://php.net/manual/en/reserved.classes.php
 */
class stdClass extends \stdClass
{
}

function createLink($moduleName, $methodName = 'index', $vars = array(), $viewType = '', $onlybody = false)
{
    if(empty($moduleName)) return '';
    return \helper::createLink($moduleName, $methodName, $vars, $viewType, $onlybody);
}

function inLink($methodName = 'index', $vars = '', $viewType = '', $onlybody = false)
{
    return \inlink($methodName, $vars, $viewType, $onlybody);
}

function zget($var, $key, $valueWhenNone = false, $valueWhenExists = false)
{
    return \zget($var, $key, $valueWhenNone, $valueWhenExists);
}

function getWebRoot($full = false)
{
    return \getWebRoot($full);
}

function hasPriv($module, $method, $object = null, $vars = '')
{
    return \common::hasPriv($module, $method, $object, $vars);
}

function isFieldRequired($name)
{
    global $config, $app;
    $moduleName = $app->moduleName;
    $methodName = $app->methodName;
    $required   = false;
    if (isset($config->$moduleName->$methodName->requiredFields)) $required = in_array($name, explode(',', $config->$moduleName->$methodName->requiredFields));

    return $required;
}

/**
 * Determine whether the request is ajax.
 *
 * @param string|null $type 'zin'|'modal'|null
 */
function isAjaxRequest(string|null $type = null): bool
{
    return \helper::isAjaxRequest($type);
}

/**
 * Bind global event listener to widget element.
 *
 * @param string       $name
 * @param string|array $callback
 * @param array|null   $options
 * @return directive
 */
function bind(string $name, string|array $callback, array $options = null): directive
{
    $data = array('on' => $name);
    if(is_string($callback)) $data['call'] = $callback;
    if(is_array($callback))  $data = array_merge($data, $callback);
    if($options)             $data = array_merge($data, $options);

    return setData($data);
}
