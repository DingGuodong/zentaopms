#!/usr/bin/env php
<?php

/**

title=测试获取产品的路线图数量 productModel->getRoadmap();
cid=0

- 测试获取产品1的roadmap数量属性total @261
- 测试获取产品2的roadmap数量属性total @107
- 测试获取产品3的roadmap数量属性total @6
- 测试获取产品4的roadmap数量属性total @9
- 测试获取产品5的roadmap数量属性total @12
- 测试获取不存在产品的roadmap数量属性total @0

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/product.unittest.class.php';

zenData('productplan')->gen(500);
zenData('product')->gen(50);
zenData('release')->gen(500);

$productIDList = array('1', '2', '3', '4', '5', '1000001');

$product = new productTest('admin');

r($product->getRoadmapTest($productIDList[0])) && p('total') && e('261'); // 测试获取产品1的roadmap数量
r($product->getRoadmapTest($productIDList[1])) && p('total') && e('107'); // 测试获取产品2的roadmap数量
r($product->getRoadmapTest($productIDList[2])) && p('total') && e('6');   // 测试获取产品3的roadmap数量
r($product->getRoadmapTest($productIDList[3])) && p('total') && e('9');   // 测试获取产品4的roadmap数量
r($product->getRoadmapTest($productIDList[4])) && p('total') && e('12');  // 测试获取产品5的roadmap数量
r($product->getRoadmapTest($productIDList[5])) && p('total') && e('0');   // 测试获取不存在产品的roadmap数量
