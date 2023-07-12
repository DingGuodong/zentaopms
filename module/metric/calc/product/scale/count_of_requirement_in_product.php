<?php
/**
 * 按产品统计的用户需求总数。
 * Count of requirement in product.
 *
 * 范围：product
 * 对象：requirement
 * 目的：scale
 * 度量名称：按产品统计的用户需求总数
 * 单位：个
 * 描述：产品中用户需求的个数求和
 *       过滤已删除的用户需求
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
class count_of_requirement_in_product extends baseCalc
{
    public $dataset = 'getStories';

    public $fieldList = array('t1.product', 't1.type');

    public $result = array();

    public function calculate($data)
    {
        $product = $data->product;
        $type    = $data->type;

        if(!isset($this->result[$product])) $this->result[$product] = 0;
        if($type == 'requirement') $this->result[$product] += 1;
    }

    public function getResult($options = null)
    {
        $records = array();
        foreach($this->result as $product => $value)
        {
            $records[] = array('product' => $product, 'value' => $value);
        }
        return $this->filterByOptions($records, $options);
    }
}
