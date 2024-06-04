<?php
$lang->dataview->id     = '编号';
$lang->dataview->name   = '名称';
$lang->dataview->export = '导出';

$lang->dataview->common         = '数据表';
$lang->dataview->id             = '编号';
$lang->dataview->type           = '类型';
$lang->dataview->name           = '名称';
$lang->dataview->code           = '代号';
$lang->dataview->group          = '所属分组';
$lang->dataview->view           = '视图名称';
$lang->dataview->desc           = '描述';
$lang->dataview->length         = '长度';
$lang->dataview->data           = '数据';
$lang->dataview->schema         = '字段';
$lang->dataview->details        = '详情';
$lang->dataview->fieldName      = '字段名称';
$lang->dataview->fieldType      = '字段类型';
$lang->dataview->create         = '创建中间表';
$lang->dataview->browse         = '查看';
$lang->dataview->edit           = '编辑';
$lang->dataview->design         = '设计';
$lang->dataview->delete         = '删除';
$lang->dataview->createPriv     = '创建中间表';
$lang->dataview->browsePriv     = '查看数据表';
$lang->dataview->editPriv       = '编辑中间表';
$lang->dataview->designPriv     = '设计中间表';
$lang->dataview->deletePriv     = '删除中间表';
$lang->dataview->viewAction     = '查看数据集';
$lang->dataview->sql            = '查询语句';
$lang->dataview->sqlPlaceholder = '请输入查询语句，只支持select查询';
$lang->dataview->query          = '查询';
$lang->dataview->add            = '添加';
$lang->dataview->sqlQuery       = 'SQL语句查询';
$lang->dataview->onlyOne        = '只能输入一条SQL语句';
$lang->dataview->empty          = '请输入一条正确的SQL语句';
$lang->dataview->allowSelect    = '只允许SELECT查询';
$lang->dataview->noStar         = "考虑性能，不允许使用 'SELECT *' 查询";
$lang->dataview->fieldSettings  = "设置字段名";
$lang->dataview->queryFilters   = "查询筛选器";
$lang->dataview->varSettings    = "变量设置";
$lang->dataview->result         = "查询结果";
$lang->dataview->chart          = "图表";
$lang->dataview->table          = "透视表";
$lang->dataview->save           = "保存";
$lang->dataview->field          = "字段";
$lang->dataview->varError       = "变量格式错误";
$lang->dataview->time           = "时间";
$lang->dataview->confirmDelete  = '您确认要删除吗?';
$lang->dataview->manageGroup    = '维护分组';
$lang->dataview->noModule       = "<div>您现在还没有分组信息</div><div>请维护分组</div>";
$lang->dataview->notSelect      = "请选择数据表进行展示";
$lang->dataview->existView      = "视图已存在";
$lang->dataview->noQueryData    = "暂无查询结果";
$lang->dataview->builtIn        = '内置数据分组';
$lang->dataview->default        = '默认分组';
$lang->dataview->relatedTable   = '所属表';
$lang->dataview->relatedField   = '对应字段';
$lang->dataview->multilingual   = '切换系统语言项后，将显示对应名称';
$lang->dataview->duplicateField = '存在重复的字段名： <strong>%s</strong>。建议您：（1）修改 <strong>*</strong> 查询为具体的字段。（2）使用 <strong>as</strong> 为字段设置别名。';
$lang->dataview->errorField     = '当前字段%s为非法字符,请[as]成英文或英文下划线的格式。';
$lang->dataview->queryFilterTip = '查询筛选器是通过在 SQL 中插入变量实现动态查询的筛选方式，第三步配置的结果筛选器是对SQL查询结果进行进一步筛选。';

$lang->dataview->varFilter = new stdclass();
$lang->dataview->varFilter->varCode     = '变量代号';
$lang->dataview->varFilter->varLabel    = '变量标签';
$lang->dataview->varFilter->default     = '默认值';
$lang->dataview->varFilter->requestType = '筛选器类型';

$lang->dataview->varFilter->noticeVarName     = '变量名称不能为空';
$lang->dataview->varFilter->noticeRequestType = '筛选器类型不能为空';
$lang->dataview->varFilter->noticeShowName    = '显示名称不能为空';

$lang->dataview->varFilter->requestTypeList['input']    = '文本框';
$lang->dataview->varFilter->requestTypeList['date']     = '日期选择';
$lang->dataview->varFilter->requestTypeList['datetime'] = '时间选择';
$lang->dataview->varFilter->requestTypeList['select']   = '下拉选择';

$lang->dataview->varFilter->selectList['user']           = '用户列表';
$lang->dataview->varFilter->selectList['product']        = $lang->productCommon . '列表';
$lang->dataview->varFilter->selectList['project']        = '项目列表';
$lang->dataview->varFilter->selectList['execution']      = $lang->executionCommon . '列表';
$lang->dataview->varFilter->selectList['dept']           = '部门列表';
$lang->dataview->varFilter->selectList['project.status'] = '项目状态列表';

$lang->dataview->objects = array();
$lang->dataview->objects['product']     = $lang->product->common;
$lang->dataview->objects['story']       = $lang->story->common;
$lang->dataview->objects['build']       = $lang->build->common;
$lang->dataview->objects['productplan'] = '产品计划';
$lang->dataview->objects['release']     = $lang->release->common;
$lang->dataview->objects['bug']         = $lang->bug->common;
$lang->dataview->objects['project']     = $lang->project->common;
$lang->dataview->objects['task']        = $lang->task->common;
$lang->dataview->objects['team']        = '团队';
$lang->dataview->objects['user']        = '用户';
$lang->dataview->objects['execution']   = '阶段';
$lang->dataview->objects['testtask']    = '测试单';
$lang->dataview->objects['testrun']     = '测试执行';
$lang->dataview->objects['testcase']    = '测试用例';
$lang->dataview->objects['testresult']  = '执行结果';
$lang->dataview->objects['casemodule']  = '用例模块';
$lang->dataview->objects['action']      = '动态';
$lang->dataview->objects['effort']      = '日志';
if($this->config->edition != 'open') $lang->dataview->objects['feedback'] = '反馈';

$lang->dataview->tables = array();
$lang->dataview->tables['build']       = array('name' => '版本数据', 'desc' => '包括版本名称、所属产品、所属项目、所属执行、创建人等');
$lang->dataview->tables['product']     = array('name' => '产品数据', 'desc' => '包括产品名称、所属产品线、所属项目集、产品代号、创建人等');
$lang->dataview->tables['productplan'] = array('name' => '产品计划数据', 'desc' => '包括计划名称、所属产品、父计划、开始时间、结束时间等');
$lang->dataview->tables['release']     = array('name' => '产品发布数据', 'desc' => '包括发布名称、所属产品、所属项目、相关版本、创建人等');
$lang->dataview->tables['project']     = array('name' => '项目数据', 'desc' => '包括项目名称、项目代号、项目模型、开始时间、结束时间等');
$lang->dataview->tables['execution']   = array('name' => '执行数据', 'desc' => '包括执行名称、执行代号、所属项目、开始时间、结束时间等');
$lang->dataview->tables['task']        = array('name' => '任务数据', 'desc' => '包括任务名称、优先级、指派人、所属执行、创建人等');
//$lang->dataview->tables['team']        = array('name' => '团队数据', 'desc' => '包括所属项目、所属执行、团队成员等');
$lang->dataview->tables['bug']         = array('name' => 'Bug数据', 'desc' => '包括Bug严重程度、优先级、解决人、所属项目、所属产品等');
$lang->dataview->tables['bugbuild']    = array('name' => '版本Bug数据', 'desc' => '版本的Bug严重程度、优先级、解决人、所属项目、所属产品等');
$lang->dataview->tables['story']       = array('name' => '需求数据', 'desc' => '包括需求优先级、创建人、所属产品、状态、阶段等');
$lang->dataview->tables['testcase']    = array('name' => '用例数据', 'desc' => '包括用例优先级、创建人、所属产品、状态、看护人等');
$lang->dataview->tables['casestep']    = array('name' => '用例步骤数据', 'desc' => '包括所属用例、用例步骤、期望结果等');
$lang->dataview->tables['testtask']    = array('name' => '测试单列表', 'desc' => '包括用例优先级、创建人、所属产品、状态等');
$lang->dataview->tables['testrun']     = array('name' => '测试单用例执行情况', 'desc' => '包括所属测试单、所属用例、用例编号、用例版本、指派人、最后执行人等');
$lang->dataview->tables['testresult']  = array('name' => '测试单用例每次执行结果', 'desc' => '包括用例编号、用例版本、执行人、执行结果等');

$lang->dataview->typeList = array();
$lang->dataview->typeList['view']  = '中间表';
$lang->dataview->typeList['table'] = '原始表';

$lang->dataview->fieldTypeList = array();
$lang->dataview->fieldTypeList['string'] = '文本';
$lang->dataview->fieldTypeList['option'] = '选项';
$lang->dataview->fieldTypeList['date']   = '日期';

$lang->dataview->groupList['my']        = '我的地盘';
$lang->dataview->groupList['program']   = '项目集';
$lang->dataview->groupList['product']   = $lang->productCommon;
$lang->dataview->groupList['project']   = '项目';
$lang->dataview->groupList['execution'] = $lang->execution->common;
$lang->dataview->groupList['kanban']    = '看板';
$lang->dataview->groupList['qa']        = '测试';
$lang->dataview->groupList['doc']       = '文档';
$lang->dataview->groupList['assetlib']  = '资产库';
$lang->dataview->groupList['report']    = '统计';
$lang->dataview->groupList['company']   = '组织';
$lang->dataview->groupList['repo']      = '持续集成';
$lang->dataview->groupList['api']       = 'API';
$lang->dataview->groupList['message']   = '消息';
$lang->dataview->groupList['search']    = '搜索';
$lang->dataview->groupList['admin']     = '后台';
$lang->dataview->groupList['system']    = '系统';
$lang->dataview->groupList['other']     = '其他';

$lang->dataview->secondGroup['story']          = '需求';
$lang->dataview->secondGroup['review']         = '评审';
$lang->dataview->secondGroup['projectContent'] = '项目内容';
$lang->dataview->secondGroup['measure']        = '度量';
$lang->dataview->secondGroup['user']           = '用户';

$lang->dataview->error = new stdclass();
$lang->dataview->error->canNotDesign  = '该中间表已被使用，不能重新设计。';
$lang->dataview->error->canNotDelete  = '该中间表已被使用，不能删除。';
$lang->dataview->error->warningDesign = '中间表已被引用，设计会导致图表、透视表、大屏无法展示，是否继续？';
$lang->dataview->error->warningDelete = '中间表已被引用，删除会导致图表、透视表、大屏无法展示，是否继续？';

$lang->dataview->querying      = '正在查询，请稍后...';
$lang->dataview->queryResult   = '查询到 %s行 x %s列数据';
$lang->dataview->viewResult    = '共 %s行 x %s列数据';
$lang->dataview->recTotalTip   = '共 <strong> %s </strong> 项';
$lang->dataview->recPerPageTip = "每页 <strong> %s </strong> 项 <span class='caret'></span>";

$lang->dataview->projectStatusList['done']   = '已完成';
$lang->dataview->projectStatusList['cancel'] = '已取消';
$lang->dataview->projectStatusList['pause']  = '已暂停';
