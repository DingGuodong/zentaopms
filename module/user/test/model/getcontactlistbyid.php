#!/usr/bin/env php
<?php

/**

title=测试 userModel->getContactListByID();
cid=0

- 获取 id 为 0 的联系人列表，返回空。 @0
- 获取 id 为 4 的联系人列表，返回空。 @0
- 获取 id 为 1 的联系人列表，查看创建者、名称和联系人。
 - 属性account @user1
 - 属性listName @联系人列表1
 - 属性userList @user20,user40
- 获取 id 为 2 的联系人列表，查看创建者、名称和联系人。
 - 属性account @dev2
 - 属性listName @联系人列表2
 - 属性userList @dev21,dev41

*/
include dirname(__FILE__, 5) . '/test/lib/init.php';
include dirname(__FILE__, 2) . '/lib/user.unittest.class.php';

zenData('usercontact')->gen(2);

$userTest = new userTest();

r($userTest->getContactListByIDTest(0)) && p() && e(0); //获取 id 为 0 的联系人列表，返回空。
r($userTest->getContactListByIDTest(3)) && p() && e(0); //获取 id 为 4 的联系人列表，返回空。

r($userTest->getContactListByIDTest(1)) && p('account|listName|userList', '|') && e('user1|联系人列表1|user20,user40'); //获取 id 为 1 的联系人列表，查看创建者、名称和联系人。
r($userTest->getContactListByIDTest(2)) && p('account|listName|userList', '|') && e('dev2|联系人列表2|dev21,dev41');    //获取 id 为 2 的联系人列表，查看创建者、名称和联系人。
