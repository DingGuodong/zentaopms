<?php
/**
 * 按全局统计的任务消耗工时数。
 * Consume of task.
 *
 * 范围：global
 * 对象：task
 * 目的：scale
 * 度量名称：按全局统计的任务消耗工时数
 * 单位：h
 * 描述：按全局统计的任务消耗工时数是指已经花费的工时总和，用于完成所有任务。该度量项可以用来评估团队或组织在任务执行过程中的工时投入情况，以及在完成任务方面的效率和资源利用情况。较高的任务消耗工时总数可能表明需要审查工作流程和资源分配，以提高工作效率。
 * 定义：所有的任务的消耗工时数求和
 *       过滤已删除的任务
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
class consume_of_task extends baseCalc
{
    public $dataset = 'getTasks';

    public $fieldList = array('t1.consumed');

    public $result = 0;

    public function calculate($row)
    {
        if(empty($row->consumed)) return false;

        $this->result += $row->consumed;
    }

    public function getResult($options = array())
    {
        $records = $this->getRecords(array('value'));
        return $this->filterByOptions($records, $options);
    }
}
