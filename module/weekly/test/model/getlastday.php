#!/usr/bin/env php
<?php
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/weekly.class.php';
su('admin');

/**

title=测试 weeklyModel->getLastDay();
cid=1
pid=1

查询日期为星期日 >> 2022-05-06
查询日期为其他 >> 2022-04-29
查询日期为空 >> 2022-08-29

*/

$date = array('2022-05-08', '2022-04-29', '');

$weekly = new weeklyTest();
r($weekly->getLastDayTest($date[0])) && p() && e('2022-05-06'); //查询日期为星期日
r($weekly->getLastDayTest($date[1])) && p() && e('2022-04-29'); //查询日期为其他
r($weekly->getLastDayTest($date[2])) && p() && e('2022-08-29'); //查询日期为空