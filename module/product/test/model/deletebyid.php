#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/product.class.php';

function initData()
{
    zdTable('product')->config('product')->gen(10);
    zdTable('doclib')->config('doclib')->gen(10);
}
initData();

/**
title=productTao->deleteByID();
cid=2

 */

$product = new productTest('admin');

$products = $product->objectModel->getByIdList(array(1,2,3,4,5));
r($products[3]->deleted) && p('') && e('0');

$doclib = $product->objectModel->dao->select('deleted')->from(TABLE_DOCLIB)->where('product')->eq(3)->andWhere('deleted')->eq(0)->fetchAll();
r(count($doclib)) && p('') && e(5);

/* Delete product 3. */
$product->objectModel->deleteByID(3);

$products = $product->objectModel->getByIdList(array(1,2,3,4,5));
r($products[3]->deleted) && p('') && e('1');

$product->objectModel->dao->reset();
$doclib = $product->objectModel->dao->select('deleted')->from(TABLE_DOCLIB)->where('product')->eq(3)->andWhere('deleted')->eq(1)->fetchAll();
r(count($doclib)) && p('') && e(5);
