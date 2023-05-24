#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/product.class.php';

zdTable('product')->gen(50);
zdTable('project')->gen(50);
$projectproduct = zdTable('projectproduct');
$projectproduct->product->range('1-2');
$projectproduct->project->range('1-50');
$projectproduct->gen(50);

/**

title=测试productModel->fetchPairs();
cid=1
pid=1

*/

global $tester;
$product = $tester->loadModel('product');
su('admin');
$product->app->user->admin = true;
$product->app->moduleName = 'product';
$product->app->methodName = 'project';

r(count($product->fetchAllProductProjects($productID = 0, 'all',    '',    'order_desc')))   && p() && e('0');
r(count($product->fetchAllProductProjects($productID = 1, 'all',    '',    'order_desc')))   && p() && e('20');
r(count($product->fetchAllProductProjects($productID = 1, 'all',    '0',   'order_desc')))   && p() && e('17');
r(count($product->fetchAllProductProjects($productID = 1, 'undone', '',    'order_desc')))   && p() && e('15');
r(count($product->fetchAllProductProjects($productID = 1, 'wait',   'all', 'order_desc')))   && p() && e('5');

$product->app->loadClass('pager', $static = true);
$pager = new pager(0, 50, 1);
r(count($product->fetchAllProductProjects($productID = 1, 'all', '', 'order_desc', $pager))) && p() && e('20');

$pager = new pager(0, 5, 1);
r(count($product->fetchAllProductProjects($productID = 1, 'all', '', 'order_desc', $pager))) && p() && e('5');

$product->app->user->admin = false;
$product->app->user->view->projects = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,48,49,50';
r(count($product->fetchAllProductProjects($productID = 1, 'all',    '',    'order_desc')))   && p() && e('7');
r(count($product->fetchAllProductProjects($productID = 1, 'all',    '0',   'order_desc')))   && p() && e('7');
r(count($product->fetchAllProductProjects($productID = 1, 'undone', '',    'order_desc')))   && p() && e('5');
r(count($product->fetchAllProductProjects($productID = 1, 'wait',   'all', 'order_desc')))   && p() && e('2');

$product->app->loadClass('pager', $static = true);
$pager = new pager(0, 50, 1);
r(count($product->fetchAllProductProjects($productID = 1, 'all', '', 'order_desc', $pager))) && p() && e('7');

$pager = new pager(0, 5, 1);
r(count($product->fetchAllProductProjects($productID = 1, 'all', '', 'order_desc', $pager))) && p() && e('5');
