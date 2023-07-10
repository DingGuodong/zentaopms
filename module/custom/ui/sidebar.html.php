<?php
declare(strict_types=1);
/**
 * The yyy view file of xxx module of ZenTaoPMS.
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @license     ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Shujie Tian<tianshujie@easycorp.ltd>
 * @package     xxx
 * @link        https://www.zentao.net
 */
namespace zin;

$menuItems = array();
if(!empty($lang->custom->{$module}->fields))
{
    foreach($lang->custom->{$module}->fields as $key => $value)
    {
        $currentModule = 'custom';
        $method        = $key;
        $params        = $key == 'required' ? "module=$module" : '';
        $active        = $app->rawMethod == strtolower($key) ? 'active' : '';
        if(!in_array($key, $config->custom->notSetMethods))
        {
            $params = "module=$module&field=$key";
            $method = 'set';
            $active = (isset($field) and $field == $key) ? 'active' : $active;
        }

        if($module == 'approvalflow')
        {
            $currentModule = 'approvalflow';
            if(in_array($key, array('project', 'workflow')))
            {
                $method = 'browse';
                $params = "type=$key";
                $active = (isset($type) and $type == $key) ? 'active' : $active;
            }
        }

        if(common::hasPriv($currentModule, $method))
        {
            $menuItems[] = li
                (
                    setClass('menu-item py-1 px-4'),
                    a
                    (
                        setClass($active),
                        set::href(inlink($method, $params)),
                        $value,
                    )
                );
        }
    }
}

$sidebarMenu = $menuItems ? menu(
        setClass('w-48'),
        $menuItems
    ) : null;
