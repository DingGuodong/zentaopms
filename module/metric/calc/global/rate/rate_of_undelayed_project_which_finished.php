<?php
/**
 * 按全局统计的年度完成项目中项目的按期完成率。
 * rate_of_undelayed_project_which_finished.
 *
 * 范围：global
 * 对象：project
 * 目的：rate
 * 度量名称：按全局统计的年度完成项目中项目的按期完成率
 * 单位：个
 * 描述：按全局统计的年度完成项目中项目的按期完成率是指按全局统计的年度完成项目中按期完成项目数与关闭项目数之比。这个度量项可以帮助团队评估某年度项目按期关闭的能力和效果，并作为项目管理的绩效指标之一。较高的按期完成率表示团队能够按时完成项目，说明对项目管理和交付能力较高。
 * 定义：复用：
按全局统计的年度关闭项目数
按全局统计的年度完成项目中项目的按期完成率
公式：
按全局统计的年度项目按期关闭率=按全局统计的年度按时关闭项目数/按全局统计的年度关闭项目数
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
class rate_of_undelayed_project_which_finished extends baseCalc
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