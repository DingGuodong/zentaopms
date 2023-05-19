#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . "/test/lib/init.php";
include dirname(__FILE__, 2) . '/product.class.php';

zdTable('module')->config('line')->gen(5);
zdTable('product')->config('product')->gen(30);

/**

title=productModel->concatProductLine();
cid=1
pid=1

*/

$product = new productTest('admin');

$lineList = array(1, 2, 3, 4, 5);
$line1Products  = range(1, 5);
$line2Products  = range(6, 10);
$line3Products  = range(11, 15);
$line4Products  = range(16, 20);
$line5Products  = range(21, 25);
$noLineProducts = range(26,30);

$testSuit1 = array_merge($line1Products, $line2Products, $line3Products);
$testSuit2 = array_merge($line3Products, $line4Products, $line5Products);
$testSuit3 = array_merge($line3Products, $line4Products, $noLineProducts);

r($product->concatProductLineTest($testSuit1)) && p('0:name;1:name;2:name')    && e('产品线1/产品1,产品线1/产品2,产品线1/产品3');    // 测试第一组产品列表的0，1，2条数据
r($product->concatProductLineTest($testSuit1)) && p('5:name;6:name;7:name')    && e('产品线2/产品6,产品线2/产品7,产品线2/产品8');    // 测试第一组产品列表的5，6，7条数据
r($product->concatProductLineTest($testSuit1)) && p('10:name;11:name;12:name') && e('产品线3/产品11,产品线3/产品12,产品线3/产品13'); // 测试第一组产品列表的10，11，12条数据

r($product->concatProductLineTest($testSuit2)) && p('0:name;1:name;2:name')    && e('产品线3/产品11,产品线3/产品12,产品线3/产品13'); // 测试第二组产品列表的0，1，2条数据
r($product->concatProductLineTest($testSuit2)) && p('5:name;6:name;7:name')    && e('产品线4/产品16,产品线4/产品17,产品线4/产品18'); // 测试第二组产品列表的5，6，7条数据
r($product->concatProductLineTest($testSuit2)) && p('10:name;11:name;12:name') && e('产品线5/产品21,产品线5/产品22,产品线5/产品23'); // 测试第二组产品列表的10，11，12条数据

r($product->concatProductLineTest($testSuit3)) && p('0:name;1:name;2:name')    && e('产品线3/产品11,产品线3/产品12,产品线3/产品13'); // 测试第三组产品列表的0，1，2条数据
r($product->concatProductLineTest($testSuit3)) && p('5:name;6:name;7:name')    && e('产品线4/产品16,产品线4/产品17,产品线4/产品18'); // 测试第三组产品列表的5，6，7条数据
r($product->concatProductLineTest($testSuit3)) && p('10:name;11:name;12:name') && e('产品26,产品27,产品28');                         // 测试第三组产品列表的10，11，12条数据
