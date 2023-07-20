<?php
/**
 * 按产品统计的每日解决Bug数。
 *
 * 范围：product
 * 对象：Bug
 * 目的：scale
 * 度量名称：按产品统计的每日解决Bug数
 * 单位：个
 * 描述：产品中每日解决的Bug数求和
 *       过滤已删除的Bug
 *       过滤已删除的产品
 * 度量库：
 * 收集方式：realtime
 *
 * @copyright Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.zentao.net)
 * @author    zhouxin <zhouxin@easycorp.ltd>
 * @package
 * @uses      func
 * @license   ZPL(https://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @Link      https://www.zentao.net
 */
class count_of_daily_resolved_bug_in_product extends baseCalc
{
    public $dataset = 'getBugs';

    public $fieldList = array('t1.product', 't1.status', 't1.resolvedDate');

    public function calculate($row)
    {
        if($row->status != 'resolved' || empty($row->resolvedDate)) return;

        $date = substr($row->resolvedDate, 0, 10);
        list($year, $month, $day) = explode('-', $date);
        if($year == '0000') return;

        if(!isset($this->result[$row->product]))                      $this->result[$row->product] = array();
        if(!isset($this->result[$row->product][$year]))               $this->result[$row->product][$year] = array();
        if(!isset($this->result[$row->product][$year][$month]))       $this->result[$row->product][$year][$month] = array();
        if(!isset($this->result[$row->product][$year][$month][$day])) $this->result[$row->product][$year][$month][$day] = 0;

        $this->result[$row->product][$year][$month][$day] ++;
    }

    public function getResult($options = array())
    {
        $records = array();
        foreach($this->result as $product => $years)
        {
            foreach($years as $year => $months)
            {
                foreach($months as $month => $days)
                {
                    foreach($days as $day => $value)
                    {
                        $records[] = array('product' => $product, 'year' => $year, 'month' => $month, 'day' => $day, 'value' => $value);
                    }
                }
            }
        }

        return $this->filterByOptions($records, $options);
    }
}
