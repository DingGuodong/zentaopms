<?php
/**
 * 按全局统计的反馈总数。
 * count_of_feedback.
 *
 * 范围：global
 * 对象：feedback
 * 目的：scale
 * 度量名称：按全局统计的反馈总数
 * 单位：个
 * 描述：按全局统计的反馈总数是指收集到的所有用户反馈的数量。这个度量项可以帮助团队了解用户对产品的关注点和问题，并作为改进产品质量和用户满意度的依据。较高的反馈总数可能暗示着用户的活跃度和关注度较高，需要团队及时响应和处理，同时暗示产品问题可能有很多。
 * 定义：所有的反馈个数求和
过滤已删除的反馈
过滤已删除的产品
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
class count_of_feedback extends baseCalc
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