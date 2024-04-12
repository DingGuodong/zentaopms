#!/usr/bin/env php
<?php
/**

title=测试 holidayModel->isHoliday();
cid=1

- 测试节假日 一个月之前的一天 @It is not a holiday
- 测试节假日 一个月加4天之前 的一天 @It is not a holiday
- 测试补班 一个月加1天 之前的一天 @It is not a holiday
- 测试补班 一个月加3天 之前的一天 @It is not a holiday

*/
declare(strict_types=1);
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/holiday.class.php';

zdTable('holiday')->gen(10);
zdTable('user')->gen(1);

su('admin');

$holidays    = array('2024-03-04', '2024-03-08');
$workingDays = array('2024-03-05', '2024-03-07');

$holiday = new holidayTest();

r($holiday->isHolidayTest($holidays[0])) && p()    && e('It is not a holiday'); // 测试节假日 一个月之前的一天
r($holiday->isHolidayTest($holidays[1])) && p()    && e('It is not a holiday'); // 测试节假日 一个月加4天之前 的一天
r($holiday->isHolidayTest($workingDays[0])) && p() && e('It is not a holiday'); // 测试补班 一个月加1天 之前的一天
r($holiday->isHolidayTest($workingDays[1])) && p() && e('It is not a holiday'); // 测试补班 一个月加3天 之前的一天
