#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/product.class.php';

zdTable('product')->config('product')->gen(100);
zdTable('story')->config('story')->gen(100);
zdTable('case')->gen(100);
zdTable('user')->gen(5);
su('admin');

/**
title=productModel->getCaseCoveragePairs();
timeout=0
cid=1

 */

$tester = new productTest('admin');

$productList   = $tester->objectModel->getList();
$productIdList = array_keys($productList);
$pairs         = $tester->objectModel->getCaseCoveragePairs($productIdList);

r($pairs[1])           && p('') && e('30');
r($pairs[7])           && p('') && e('30');
r($pairs[10])          && p('') && e('20');
r(isset($pairs[1000])) && p('') && e('0');
