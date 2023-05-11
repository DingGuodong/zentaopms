<?php
/**
 * The zin class file of zin of ZenTaoPMS.
 *
 * @copyright   Copyright 2023 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @author      Hao Sun <sunhao@easycorp.ltd>
 * @package     zin
 * @version     $Id
 * @link        https://www.zentao.net
 */

namespace zin;

require_once dirname(__DIR__) . DS . 'utils' . DS . 'deep.func.php';

class zin
{
    public static $globalRenderList = array();

    public static $enabledGlobalRender = true;

    public static $globalRenderMap = array();

    public static $data = array();

    public static function getData($namePath, $defaultValue = null)
    {
        return \zin\utils\deepGet(static::$data, $namePath, $defaultValue);
    }

    public static function setData($namePath, $value)
    {
        \zin\utils\deepSet(static::$data, $namePath, $value);
    }

    public static function enableGlobalRender()
    {
        static::$enabledGlobalRender = true;
    }

    public static function disableGlobalRender()
    {
        static::$enabledGlobalRender = false;
    }

    public static function renderInGlobal()
    {
        if(!static::$enabledGlobalRender) return false;

        static::$globalRenderList = array_merge(static::$globalRenderList, func_get_args());
    }
}
