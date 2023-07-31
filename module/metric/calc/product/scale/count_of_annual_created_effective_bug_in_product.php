<?php
/**
 * 按产品统计的年度新增有效Bug数。
 * Count of annual created effective bug in product.
 *
 * 范围：product
 * 对象：Bug
 * 目的：scale
 * 度量名称：按产品统计的年度新增有效Bug数
 * 单位：个
 * 描述：产品中创建时间为某年的解决方案为已解决和延期处理的或者状态为激活的Bug的个数求和
 *       过滤已删除的Bug
 *       过滤已删除的产品
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
class count_of_annual_created_effective_bug_in_product extends baseCalc
{
    public $dataset = 'getBugs';

    public $fieldList = array('t1.product', 't1.status', 't1.resolution', 't1.openedDate');

    public $result = array();

    public function calculate($data)
    {
        $openedDate = $data->openedDate;

        if(empty($openedDate)) return false;
        $year = substr($openedDate, 0 ,4);
        if($year == '0000') return false;

        $product = $data->product;
        if(!isset($this->result[$product])) $this->result[$product] = array();
        if(!isset($this->result[$product][$year])) $this->result[$product][$year] = 0;

        $resolution = $data->resolution;
        $status     = $data->status;

        if($status == 'active' or $resolution == 'fixed' or $resolution == 'postponed') $this->result[$product][$year] += 1;
    }

    public function getResult($options = array())
    {
        $records = array();
        foreach($this->result as $product => $years)
        {
            foreach($years as $year => $value)
            {
                $records[] = array('product' => $product, 'year' => $year, 'value' => $value);
            }
        }

        return $this->filterByOptions($records, $options);
    }
}
