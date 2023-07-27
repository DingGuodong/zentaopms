<?php
/**
 * 按全局统计的年度日志记录的工时总数。
 * hour_of_annual_effort.
 *
 * 范围：global
 * 对象：effort
 * 目的：hour
 * 度量名称：按全局统计的年度日志记录的工时总数
 * 单位：h
 * 描述：按全局统计的年度日志记录的工时总数是指组织在某年度实际花费的总工时数。该度量项可以用来评估组织的工时投入情况和对资源的利用效率。较高的消耗工时数可能需要审查工作流程和资源分配，以提高工作效率和进度控制。
 * 定义：所有日志记录的工时之和
 *       记录时间在某年
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
class hour_of_annual_effort extends baseCalc
{
    public $result = array();

    public function getStatement()
    {
        return $this->dao->select("year(date) as year,sum(consumed) as consumed")
            ->from(TABLE_EFFORT)
            ->where('deleted')->eq('0')
            ->andWhere('year(date)')->ne('0000')
            ->groupBy('`year`')
            ->query();
    }

    public function calculate($row)
    {
        $year         = $row->year;
        $consumed     = $row->consumed;

        $this->result[$year] = $consumed;
    }

    public function getResult($options = array())
    {
        $records = $this->getRecords(array('year', 'value'));
        return $this->filterByOptions($records, $options);
    }
}
