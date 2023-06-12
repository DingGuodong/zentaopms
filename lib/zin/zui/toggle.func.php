<?php
/**
 * The zui toggle function file of zin lib.
 *
 * @copyright   Copyright 2023 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @author      Hao Sun <sunhao@easycorp.ltd>
 * @package     zin
 * @version     $Id
 * @link        https://www.zentao.net
 */
namespace zin;

/**
 * Add data-toggle=* to widget element and set other data attributes.
 *
 * @param  string $name
 * @param  array  $dataset
 */
function toggle($name, $dataset = null): directive
{
    if(empty($dataset)) $dataset = array();
    $dataset['data-toggle'] = $name;
    return setData($dataset);
}
