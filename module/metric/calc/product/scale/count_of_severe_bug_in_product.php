<?php
/**
 * 按产品统计的严重程度为1、2级的Bug数。
 * .
 *
 * 范围：product
 * 对象：Bug
 * 目的：scale
 * 度量名称：按产品统计的严重程度为1、2级的Bug数
 * 单位：个
 * 描述：复用：
 *       按产品统计的严重程度为1级的Bug数
 *       按产品统计的严重程度为2级的Bug数
 *       公式：
 *       按产品统计的严重程度为1、2级的Bug数=按产品统计的严重程度为1级的Bug数+按产品统计的严重程度为2级的Bug数
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
class count_of_severe_bug_in_product extends baseCalc
{
    public $dataset = 'getBugs';

    public $fieldList = array('t1.severity', 't1.product');

    public $result = array();

    public function calculate($data)
    {
        $severity = $data->severity;
        $product  = $data->product;

        if(!isset($this->result[$product])) $this->result[$product] = 0;

        if($severity == '1' || $severity == '2') $this->result[$product] += 1;
    }

    public function getResult($options = array())
    {
        $records = array();
        foreach($this->result as $product => $value)
        {
            $records[] = array('product' => $product, 'value' => $value);
        }

        return $this->filterByOptions($records, $options);
    }
}
