<?php
/**
 * 按全局统计的已完成执行中延期完成执行数。
 * count of delayed finished execution which finished.
 *
 * 范围：global
 * 对象：execution
 * 目的：scale
 * 度量名称：按全局统计的已完成执行中延期完成执行数
 * 单位：个
 * 描述：按全局统计的已完成执行中延期完成执行数表示在整个系统中延期完成的执行项的数量，可以用来评估任务的延期情况和团队的执行能力。
 * 定义：所有的执行个数求和
 *       状态为已关闭
 *       关闭日期>执行开始时计划截止日期
 *       过滤已删除的执行
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
class count_of_delayed_finished_execution_which_finished extends baseCalc
{
    public $dataset = 'getExecutions';

    public $fieldList = array('t1.status', 't1.closedDate', 't1.firstEnd');

    public $result = 0;

    public function calculate($row)
    {
        if(empty($row->closedDate) or empty($row->firstEnd)) return false;

        if($row->status == 'closed' and $row->closedDate <= $row->firstEnd) $this->result ++;
    }

    public function getResult($options = array())
    {
        return $this->filterByOptions($this->result, $options);
    }
}
