<?php
/**
 * 按全局统计的年度新增发布数。
 * count_of_annual_created_release.
 *
 * 范围：global
 * 对象：release
 * 目的：scale
 * 度量名称：按全局统计的年度新增发布数
 * 单位：个
 * 描述：按全局统计的年度新增发布数表示每年新增加的发布数量。该度量项反映了组织每年增加的发布数量，可以用于评估组织产品发布的速度和规模。
 * 定义：所有的发布个数求和
发布时间为某年
过滤已删除的发布
 * 度量库：
 * 收集方式：realtime
 *
 * @copyright Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @author    qixinzhi <qixinzhi@easycorp.ltd>
 * @package
 * @uses      func
 * @license   ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @Link      https://www.zentao.net
 */
class count_of_annual_created_release extends baseCalc
{
    public $dataset = null;

    public $fieldList = array();

    //public funtion getStatement($dao)
    //{
    //}

    public function calculate($data)
    {
    }

    public function getResult($options = array())
    {
        return $this->filterByOptions($this->result, $options);
    }
}