#!/usr/bin/env php
<?php

/**

title=测试 mailModel->getConfigByMXRR();
cid=0

- 获取qq相关信息属性host @smtp.qq.com
- 获取sohu相关信息属性host @smtp.sohu.com

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/mail.unittest.class.php';
su('admin');

$mail = new mailTest();

$result1 = $mail->getConfigByMXRRTest('qq.com','test');
$result2 = $mail->getConfigByMXRRTest('263.net','test');

r($result1) && p('host') && e('smtp.qq.com');   //获取qq相关信息
r($result2) && p('host') && e('smtp.263.net');  //获取263相关信息
