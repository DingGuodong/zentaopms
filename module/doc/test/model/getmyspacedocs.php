#!/usr/bin/env php
<?php

/**

title=测试 docModel->getMySpaceDocs();
cid=1

- 获取最近浏览的所有文档
 - 第3条的title属性 @我的文档3
 - 第3条的status属性 @normal
- 获取最近浏览的草稿文档
 - 第6条的title属性 @我的草稿文档6
 - 第6条的status属性 @draft
- 获取最近浏览并且文档名字中有“文档”的文档
 - 第3条的title属性 @我的文档3
 - 第3条的status属性 @normal
- 获取最近收藏的所有文档
 - 第1条的title属性 @我的文档1
 - 第1条的status属性 @normal
- 获取最近收藏的草稿文档
 - 第7条的title属性 @我的草稿文档7
 - 第7条的status属性 @draft
- 获取最近收藏并且文档名字中有“文档”的文档
 - 第1条的title属性 @我的文档1
 - 第1条的status属性 @normal
- 获取我创建的所有文档
 - 第1条的title属性 @我的文档1
 - 第1条的status属性 @normal
- 获取我创建的草稿文档
 - 第6条的title属性 @我的草稿文档6
 - 第6条的status属性 @draft
- 获取我创建并且文档名字中有“文档”的文档
 - 第1条的title属性 @我的文档1
 - 第1条的status属性 @normal
- 获取我编辑的所有文档
 - 第1条的title属性 @我的文档1
 - 第1条的status属性 @normal
- 获取我编辑的草稿文档
 - 第7条的title属性 @我的草稿文档7
 - 第7条的status属性 @draft
- 获取我编辑并且文档名字中有“文档”的文档
 - 第1条的title属性 @我的文档1
 - 第1条的status属性 @normal

*/

include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/doc.unittest.class.php';

zenData('doclib')->loadYaml('doclib')->gen(30);
zenData('doc')->loadYaml('doc')->gen(50);
zenData('docaction')->loadYaml('docaction')->gen(20);
zenData('action')->loadYaml('action')->gen(20);
zenData('user')->gen(5);
su('admin');

$types       = array('view', 'collect', 'createdby', 'editedby');
$browseTypes = array('all', 'draft', 'bysearch');
$queries     = array('', "(( 1 AND `title` LIKE '%文档%' ) AND ( 1 ))");

$docTester = new docTest();
r($docTester->getMySpaceDocsTest($types[0], $browseTypes[0], $queries[0])) && p('3:title,status') && e('我的文档3,normal');    // 获取最近浏览的所有文档
r($docTester->getMySpaceDocsTest($types[0], $browseTypes[1], $queries[0])) && p('6:title,status') && e('我的草稿文档6,draft'); // 获取最近浏览的草稿文档
r($docTester->getMySpaceDocsTest($types[0], $browseTypes[2], $queries[1])) && p('3:title,status') && e('我的文档3,normal');    // 获取最近浏览并且文档名字中有“文档”的文档
r($docTester->getMySpaceDocsTest($types[1], $browseTypes[0], $queries[0])) && p('1:title,status') && e('我的文档1,normal');    // 获取最近收藏的所有文档
r($docTester->getMySpaceDocsTest($types[1], $browseTypes[1], $queries[0])) && p('7:title,status') && e('我的草稿文档7,draft'); // 获取最近收藏的草稿文档
r($docTester->getMySpaceDocsTest($types[1], $browseTypes[2], $queries[1])) && p('1:title,status') && e('我的文档1,normal');    // 获取最近收藏并且文档名字中有“文档”的文档
r($docTester->getMySpaceDocsTest($types[2], $browseTypes[0], $queries[0])) && p('1:title,status') && e('我的文档1,normal');    // 获取我创建的所有文档
r($docTester->getMySpaceDocsTest($types[2], $browseTypes[1], $queries[0])) && p('6:title,status') && e('我的草稿文档6,draft'); // 获取我创建的草稿文档
r($docTester->getMySpaceDocsTest($types[2], $browseTypes[2], $queries[1])) && p('1:title,status') && e('我的文档1,normal');    // 获取我创建并且文档名字中有“文档”的文档
r($docTester->getMySpaceDocsTest($types[3], $browseTypes[0], $queries[0])) && p('1:title,status') && e('我的文档1,normal');    // 获取我编辑的所有文档
r($docTester->getMySpaceDocsTest($types[3], $browseTypes[1], $queries[0])) && p('7:title,status') && e('我的草稿文档7,draft'); // 获取我编辑的草稿文档
r($docTester->getMySpaceDocsTest($types[3], $browseTypes[2], $queries[1])) && p('1:title,status') && e('我的文档1,normal');    // 获取我编辑并且文档名字中有“文档”的文档
