<?php
/**
 * The group module zh-cn file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2023 禅道软件（青岛）有限公司(ZenTao Software (Qingdao) Co., Ltd. www.cnezsoft.com)
 * @license     ZPL(http://zpl.pub/page/zplv12.html) or AGPL(https://www.gnu.org/licenses/agpl-3.0.en.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     group
 * @version     $Id: zh-cn.php 4719 2013-05-03 02:20:28Z chencongzhi520@gmail.com $
 * @link        https://www.zentao.net
 */
$lang->group->common             = '权限分组';
$lang->group->browse             = '浏览分组';
$lang->group->browseAction       = '分组列表';
$lang->group->create             = '新增分组';
$lang->group->edit               = '编辑分组';
$lang->group->copy               = '复制分组';
$lang->group->delete             = '删除分组';
$lang->group->manageView         = '视野维护';
$lang->group->managePriv         = '分配权限';
$lang->group->managePrivByGroup  = '分配权限';
$lang->group->managePrivByModule = '按模块分配权限';
$lang->group->byModuleTips       = '（可以按住Shift或者Ctrl键进行多选）';
$lang->group->allTips            = '勾选此项后，管理员可管理系统中所有对象，包括后续创建的对象。';
$lang->group->manageMember       = '成员维护';
$lang->group->manageProjectAdmin = "维护{$lang->projectCommon}管理员";
$lang->group->editManagePriv     = '维护权限';
$lang->group->confirmDelete      = '您确定删除“%s”用户分组吗？';
$lang->group->confirmDeleteAB    = '您确定删除吗？';
$lang->group->successSaved       = '成功保存';
$lang->group->errorNotSaved      = '没有保存，请确认选择了权限数据。';
$lang->group->viewList           = '可访问视图';
$lang->group->object             = '可管理对象';
$lang->group->manageProgram      = '可管理项目集';
$lang->group->manageProject      = '可管理' . $lang->projectCommon;
$lang->group->manageExecution    = '可管理' . $lang->execution->common;
$lang->group->manageProduct      = '可管理' . $lang->productCommon;
$lang->group->programList        = '可访问项目集';
$lang->group->productList        = '可访问' . $lang->productCommon;
$lang->group->projectList        = '可访问' . $lang->projectCommon;
$lang->group->executionList      = "可访问{$lang->execution->common}";
$lang->group->dynamic            = '可查看动态';
$lang->group->noticeVisit        = '空代表没有访问限制';
$lang->group->noticeNoChecked    = '请先勾选权限！';
$lang->group->noneProgram        = "暂时没有项目集";
$lang->group->noneProduct        = "暂时没有{$lang->productCommon}";
$lang->group->noneExecution      = "暂时没有{$lang->execution->common}";
$lang->group->project            = $lang->projectCommon;
$lang->group->group              = '分组';
$lang->group->more               = '更多';
$lang->group->allCheck           = '全部';
$lang->group->noGroup            = '暂时没有分组。';
$lang->group->repeat             = "『%s』已经有『%s』这条记录了，请调整后再试。";
$lang->group->noneProject        = '暂时没有' . $lang->projectCommon;
$lang->group->createPriv         = '新增权限';
$lang->group->editPriv           = '编辑权限';
$lang->group->deletePriv         = '删除权限';
$lang->group->privName           = '权限名称';
$lang->group->privDesc           = '描述';
$lang->group->add                = '添加';
$lang->group->privModuleName     = '模块名';
$lang->group->privMethodName     = '方法名';
$lang->group->privView           = '所属视图';
$lang->group->privModule         = '所属模块';
$lang->group->repeatPriv         = '同一模块名下的方法名不能相同，请修改后再试。';
$lang->group->dependPrivTips     = '此处所列的是左侧选中权限的依赖权限列表，是必须要分配的。';
$lang->group->recommendPrivTips  = '此处所列的是左侧选中权限的推荐权限列表，推荐分配。';
$lang->group->dependPrivsSaveTip = '权限及依赖权限保存成功';

$lang->group->batchActions              = '批量操作';
$lang->group->batchSetDependency        = '批量设置依赖';
$lang->group->batchSetRecommendation    = '批量设置推荐';
$lang->group->batchDeleteDependency     = '批量删除依赖';
$lang->group->batchDeleteRecommendation = '批量删除推荐';
$lang->group->managePrivPackage         = '维护权限包';
$lang->group->createPrivPackage         = '新增权限包';
$lang->group->editPrivPackage           = '编辑权限包';
$lang->group->deletePrivPackage         = '删除权限包';
$lang->group->sortPrivPackages          = '权限包排序';
$lang->group->addRecommendation         = '添加推荐';
$lang->group->addDependent              = '添加依赖';
$lang->group->deleteRecommendation      = '删除推荐';
$lang->group->deleteDependent           = '删除依赖';
$lang->group->selectedPrivs             = '选中权限：%s';
$lang->group->selectModule              = '选择模块';
$lang->group->recommendPrivs            = '推荐的权限';
$lang->group->dependentPrivs            = '依赖的权限';
$lang->group->addRelation               = '添加权限关联';
$lang->group->deleteRelation            = '删除权限关联';
$lang->group->batchDeleteRelation       = '批量删除权限关联';
$lang->group->batchChangePackage        = '批量修改权限包';

$lang->group->id         = '编号';
$lang->group->name       = '分组名称';
$lang->group->desc       = '分组描述';
$lang->group->role       = '角色';
$lang->group->acl        = '权限';
$lang->group->users      = '用户列表';
$lang->group->module     = '模块';
$lang->group->method     = '方法';
$lang->group->priv       = '权限';
$lang->group->option     = '选项';
$lang->group->inside     = '组内用户';
$lang->group->outside    = '组外用户';
$lang->group->limited    = '受限用户组';
$lang->group->general    = '通用';
$lang->group->all        = '所有权限';
$lang->group->config     = '配置';
$lang->group->unassigned = '未分配';
$lang->group->view       = '视图';
$lang->group->other      = '其他';

if(!isset($lang->privpackage)) $lang->privpackage = new stdclass();
$lang->privpackage->common = '权限包';
$lang->privpackage->id     = '编号';
$lang->privpackage->name   = '权限包名称';
$lang->privpackage->module = '所属模块';
$lang->privpackage->desc   = '权限包说明';
$lang->privpackage->belong = '所属权限包';

$lang->group->copyOptions['copyPriv'] = '复制权限';
$lang->group->copyOptions['copyUser'] = '复制用户';

$lang->group->versions['']            = '修改历史';
$lang->group->versions['18_4_alpha1'] = '禅道18.4.alpha1';
$lang->group->versions['18_3']        = '禅道18.3';
$lang->group->versions['18_2']        = '禅道18.2';
$lang->group->versions['18_1']        = '禅道18.1';
$lang->group->versions['18_0']        = '禅道18.0';
$lang->group->versions['18_0_beta3']  = '禅道18.0.beta3';
$lang->group->versions['18_0_beta2']  = '禅道18.0.beta2';
$lang->group->versions['18_0_beta1']  = '禅道18.0.beta1';
$lang->group->versions['17_6_2']      = '禅道17.6.2';
$lang->group->versions['17_6']        = '禅道17.6';
$lang->group->versions['17_5']        = '禅道17.5';
$lang->group->versions['17_4']        = '禅道17.4';
$lang->group->versions['17_3']        = '禅道17.3';
$lang->group->versions['17_2']        = '禅道17.2';
$lang->group->versions['17_1']        = '禅道17.1';
$lang->group->versions['17_0_beta2']  = '禅道17.0.beta2';
$lang->group->versions['17_0_beta1']  = '禅道17.0.beta1';
$lang->group->versions['16_5_beta1']  = '禅道16.5.beta1';
$lang->group->versions['16_4']        = '禅道16.4';
$lang->group->versions['16_3']        = '禅道16.3';
$lang->group->versions['16_2']        = '禅道16.2';
$lang->group->versions['16_1']        = '禅道16.1';
$lang->group->versions['16_0']        = '禅道16.0';
$lang->group->versions['16_0_beta1']  = '禅道16.0.beta1';
$lang->group->versions['15_8']        = '禅道15.8';
$lang->group->versions['15_7']        = '禅道15.7';
$lang->group->versions['15_0_rc1']    = '禅道15.0.rc1';
$lang->group->versions['12_5']        = '禅道12.5';
$lang->group->versions['12_3']        = '禅道12.3';
$lang->group->versions['11_6_2']      = '禅道11.6.2';
$lang->group->versions['10_6']        = '禅道10.6';
$lang->group->versions['10_1']        = '禅道10.1';
$lang->group->versions['10_0_alpha']  = '禅道10.0.alpha';
$lang->group->versions['9_8']         = '禅道9.8';
$lang->group->versions['9_6']         = '禅道9.6';
$lang->group->versions['9_5']         = '禅道9.5';
$lang->group->versions['9_2']         = '禅道9.2';
$lang->group->versions['9_1']         = '禅道9.1';
$lang->group->versions['9_0']         = '禅道9.0';
$lang->group->versions['8_4']         = '禅道8.4';
$lang->group->versions['8_3']         = '禅道8.3';
$lang->group->versions['8_2_beta']    = '禅道8.2.beta';
$lang->group->versions['8_0_1']       = '禅道8.0.1';
$lang->group->versions['8_0']         = '禅道8.0';
$lang->group->versions['7_4_beta']    = '禅道7.4.beta';
$lang->group->versions['7_3']         = '禅道7.3';
$lang->group->versions['7_2']         = '禅道7.2';
$lang->group->versions['7_1']         = '禅道7.1';
$lang->group->versions['6_4']         = '禅道6.4';
$lang->group->versions['6_3']         = '禅道6.3';
$lang->group->versions['6_2']         = '禅道6.2';
$lang->group->versions['6_1']         = '禅道6.1';
$lang->group->versions['5_3']         = '禅道5.3';
$lang->group->versions['5_1']         = '禅道5.1';
$lang->group->versions['5_0_beta2']   = '禅道5.0.beta2';
$lang->group->versions['5_0_beta1']   = '禅道5.0.beta1';
$lang->group->versions['4_3_beta']    = '禅道4.3.beta';
$lang->group->versions['4_2_beta']    = '禅道4.2.beta';
$lang->group->versions['4_1']         = '禅道4.1';
$lang->group->versions['4_0_1']       = '禅道4.0.1';
$lang->group->versions['4_0']         = '禅道4.0';
$lang->group->versions['4_0_beta2']   = '禅道4.0.beta2';
$lang->group->versions['4_0_beta1']   = '禅道4.0.beta1';
$lang->group->versions['3_3']         = '禅道3.3';
$lang->group->versions['3_2_1']       = '禅道3.2.1';
$lang->group->versions['3_2']         = '禅道3.2';
$lang->group->versions['3_1']         = '禅道3.1';
$lang->group->versions['3_0_beta2']   = '禅道3.0.beta2';
$lang->group->versions['3_0_beta1']   = '禅道3.0.beta1';
$lang->group->versions['2_4']         = '禅道2.4';
$lang->group->versions['2_3']         = '禅道2.3';
$lang->group->versions['2_2']         = '禅道2.2';
$lang->group->versions['2_1']         = '禅道2.1';
$lang->group->versions['2_0']         = '禅道2.0';
$lang->group->versions['1_5']         = '禅道1.5';
$lang->group->versions['1_4']         = '禅道1.4';
$lang->group->versions['1_3']         = '禅道1.3';
$lang->group->versions['1_2']         = '禅道1.2';
$lang->group->versions['1_1']         = '禅道1.1';
$lang->group->versions['1_0_1']       = '禅道1.0.1';

$lang->group->package = new stdclass();
$lang->group->package->browse                = '浏览';
$lang->group->package->manage                = '创建维护';
$lang->group->package->delete                = '删除';
$lang->group->package->other                 = '其他';
$lang->group->package->browseTodo            = '浏览待办';
$lang->group->package->manageTodo            = '创建维护待办';
$lang->group->package->deleteTodo            = '删除待办';
$lang->group->package->importTodo            = '导入导出待办';
$lang->group->package->manageContact         = '维护联系人';
$lang->group->package->profile               = '个人档案';
$lang->group->package->preference            = '个性化设置';
$lang->group->package->browseProgram         = '浏览项目集';
$lang->group->package->manageProgram         = '创建维护项目集';
$lang->group->package->deleteProgram         = '删除项目集';
$lang->group->package->invest                = '投入人员';
$lang->group->package->accessible            = '可访问人员';
$lang->group->package->whitelist             = '维护白名单';
$lang->group->package->stakeholder           = '维护干系人';
$lang->group->package->my                    = '浏览地盘';
$lang->group->package->browseProduct         = '浏览产品';
$lang->group->package->manageProductLine     = '创建维护产品线';
$lang->group->package->createProduct         = '创建维护产品';
$lang->group->package->importProduct         = '导入导出产品';
$lang->group->package->productWhitelist      = '白名单管理';
$lang->group->package->branch                = '分支管理';
$lang->group->package->browseStory           = '浏览研发需求';
$lang->group->package->manageStory           = '创建维护研发需求';
$lang->group->package->importStory           = '导入导出研发需求';
$lang->group->package->deleteStory           = '删除研发需求';
$lang->group->package->reviewStory           = '评审研发需求';
$lang->group->package->deleteProduct         = '删除产品';
$lang->group->package->browseRequirement     = '浏览用户需求';
$lang->group->package->manageRequirement     = '创建维护用户需求';
$lang->group->package->importRequirement     = '导入导出用户需求';
$lang->group->package->deleteRequirement     = '删除用户需求';
$lang->group->package->reviewRequirement     = '评审用户需求';
$lang->group->package->browseProductPlan     = '浏览产品计划';
$lang->group->package->manageProductPlan     = '创建维护产品计划';
$lang->group->package->deleteProductPlan     = '删除产品计划';
$lang->group->package->browseRelease         = '浏览发布';
$lang->group->package->manageRelease         = '创建维护发布';
$lang->group->package->importRelease         = '导入导出发布';
$lang->group->package->deleteRelease         = '删除发布';
$lang->group->package->releaseNotify         = '发布通知';
$lang->group->package->projectPlan           = '项目计划';
$lang->group->package->manageProjectStory    = '维护项目研发需求';
$lang->group->package->browseProject         = '浏览项目';
$lang->group->package->manageProject         = '创建维护项目';
$lang->group->package->importProject         = '导入导出项目';
$lang->group->package->projectTeam           = '团队权限';
$lang->group->package->deleteProject         = '删除项目';
$lang->group->package->projectWhitelist      = '项目白名单';
$lang->group->package->browseExecution       = '浏览执行';
$lang->group->package->manageExecution       = '创建维护执行';
$lang->group->package->deleteExecution       = '删除执行';
$lang->group->package->importExecution       = '导入导出执行';
$lang->group->package->executionWhitelist    = '执行白名单';
$lang->group->package->gantt                 = '执行甘特图';
$lang->group->package->browseTask            = '浏览任务';
$lang->group->package->manageTask            = '创建维护任务';
$lang->group->package->deleteTask            = '删除任务';
$lang->group->package->importTask            = '导入导出任务';
$lang->group->package->executionTeam         = '执行团队';
$lang->group->package->kanban                = '看板';
$lang->group->package->groupView             = '分组视图';
$lang->group->package->burndown              = '燃尽图';
$lang->group->package->cfd                   = '累积流图';
$lang->group->package->manageExecutionStory  = '维护执行研发需求';
$lang->group->package->manageExecutionEffort = '维护执行日志';
$lang->group->package->manageBuild           = '创建维护版本';
$lang->group->package->deleteBuild           = '删除版本';
$lang->group->package->browseStoryLib        = '浏览需求库';
$lang->group->package->manageStoryLib        = '创建维护需求库';
$lang->group->package->deleteStoryLib        = '删除需求库';
$lang->group->package->reviewStoryLib        = '评审需求库';
$lang->group->package->browseIssueLib        = '浏览问题库';
$lang->group->package->manageIssueLib        = '创建编辑问题库';
$lang->group->package->deleteIssueLib        = '删除问题库';
$lang->group->package->reviewIssue           = '审批问题';
$lang->group->package->browseRiskLib         = '浏览风险库';
$lang->group->package->manageRiskLib         = '创建维护风险库';
$lang->group->package->deleteRiskLib         = '删除风险库';
$lang->group->package->reivewRisk            = '审批风险';
$lang->group->package->browseOpportunityLib  = '浏览机会库';
$lang->group->package->manageOpportunityLib  = '创建维护机会库';
$lang->group->package->deleteOpportunityLib  = '删除机会库';
$lang->group->package->reviewOpportunity     = '审批机会';
$lang->group->package->browsePracticeLib     = '浏览最佳实践库';
$lang->group->package->managePracticeLib     = '创建维护最佳实践库';
$lang->group->package->deletePracticeLib     = '删除最佳实践库';
$lang->group->package->reviewPractice        = '审批最佳实践';
$lang->group->package->browseComponentLib    = '浏览组件库';
$lang->group->package->manageComponentLib    = '创建维护组件库';
$lang->group->package->deleteComponentLib    = '删除组件库';
$lang->group->package->reviewComponent       = '审批组件';
$lang->group->package->browseCaseLib         = '浏览用例库';
$lang->group->package->manageCaseLib         = '创建维护用例库';
$lang->group->package->deleteCaseLib         = '删除用例库';
$lang->group->package->importCaseLib         = '导入导出用例库';
$lang->group->package->importToCaseLib       = '导入到用例库';
$lang->group->package->officeApproval        = '办公审批';
$lang->group->package->attend                = '个人考勤';
$lang->group->package->officeSetting         = '办公设置';
$lang->group->package->dataPermission        = '数据权限';
$lang->group->package->leave                 = '个人请假';
$lang->group->package->makeup                = '个人补班';
$lang->group->package->overtime              = '个人加班';
$lang->group->package->lieu                  = '个人调休';
$lang->group->package->holiday               = '节假日';
$lang->group->package->exportOffice          = '办公导出';
$lang->group->package->browseFeedback        = '浏览反馈';
$lang->group->package->manageFeedback        = '创建维护反馈';
$lang->group->package->importFeedback        = '导入导出反馈';
$lang->group->package->manageFeedback        = '处理反馈';
$lang->group->package->faq                   = 'FAQ';
$lang->group->package->browseTicket          = '浏览工单';
$lang->group->package->manageTicket          = '创建维护工单';
$lang->group->package->importTicket          = '导入导出工单';
$lang->group->package->deleteFeedback        = '删除反馈';
$lang->group->package->deleteTIcket          = '删除工单';
$lang->group->package->feedbackPriv          = '设置反馈权限';
$lang->group->package->browseCourse          = '课程';
$lang->group->package->manageTrainCourse     = '后台管理';
$lang->group->package->system                = '平台';
$lang->group->package->host                  = '主机管理';
$lang->group->package->serverRoom            = '机房管理';
$lang->group->package->account               = '账号管理';
$lang->group->package->domain                = '域名管理';
$lang->group->package->service               = '服务管理';
$lang->group->package->deployPlan            = '运维计划';
$lang->group->package->deployScope           = '上线范围';
$lang->group->package->deployStep            = '上线步骤';
$lang->group->package->deployCase            = '上线用例';
$lang->group->package->qaIndex               = '测试仪表盘';
$lang->group->package->browseBug             = '浏览Bug';
$lang->group->package->manageBug             = '创建维护Bug';
$lang->group->package->deleteBug             = '删除Bug';
$lang->group->package->importBug             = '导入导出Bug';
$lang->group->package->browseCase            = '浏览用例';
$lang->group->package->manageCase            = '创建维护用例';
$lang->group->package->importCase            = '导入导出用例';
$lang->group->package->deleteCase            = '删除用例';
$lang->group->package->reviewCase            = '评审用例';
$lang->group->package->browseTesttask        = '浏览测试单';
$lang->group->package->manageTesttask        = '创建维护测试单';
$lang->group->package->deleteTesttask        = '删除测试单';
$lang->group->package->unitTest              = '单元测试';
$lang->group->package->testsuite             = '浏览套件';
$lang->group->package->manageTestsuite       = '创建维护套件';
$lang->group->package->deleteTestsuite       = '删除套件';
$lang->group->package->browseTestreport      = '浏览测试报告';
$lang->group->package->manageTestreport      = '创建维护测试报告';
$lang->group->package->deleteTestreport      = '删除测试报告';
$lang->group->package->importTestreport      = '导入导出测试报告';
$lang->group->package->browseZAHost          = '浏览宿主机';
$lang->group->package->manageZAHost          = '创建维护宿主机';
$lang->group->package->deleteZAHost          = '删除宿主机';
$lang->group->package->image                 = '镜像';
$lang->group->package->browseZANode          = '浏览执行节点';
$lang->group->package->manageZANode          = '创建维护执行节点';
$lang->group->package->importZANode          = '导入导出执行节点';
$lang->group->package->snapshot              = '快照';
$lang->group->package->companyTeam           = '浏览组织团队';
$lang->group->package->companyCalendar       = '浏览组织日程';
$lang->group->package->companyEffort         = '浏览组织日志';
$lang->group->package->companyDynamic        = '浏览组织动态';
$lang->group->package->companySetting        = '维护公司信息';
$lang->group->package->companyDataPermission = '部门数据权限';
$lang->group->package->programPlan           = '项目阶段';
$lang->group->package->browseDesign          = '浏览设计';
$lang->group->package->manageDesign          = '创建维护设计';
$lang->group->package->deleteDesign          = '删除设计';
$lang->group->package->manageReview          = '维护基线评审';
$lang->group->package->manageIssue           = '维护问题';
$lang->group->package->projectSetting        = '项目配置';
$lang->group->package->browseProjectRelease  = '浏览项目发布';
$lang->group->package->projectWeekly         = '项目周报';
$lang->group->package->projectMilestone      = '项目里程碑报告';
$lang->group->package->researchPlan          = '调研计划';
$lang->group->package->researchReport        = '调研报告';
$lang->group->package->budget                = '费用估算';
$lang->group->package->workEstimation        = '工作量估算';
$lang->group->package->durationEstimation    = '项目工期估算';
$lang->group->package->browseIssue           = '浏览问题';
$lang->group->package->manageIssue           = '创建维护问题';
$lang->group->package->importIssue           = '导入导出问题';
$lang->group->package->deleteIssue           = '删除问题';
$lang->group->package->importIssueLib        = '导入问题库';
$lang->group->package->browseRisk            = '浏览风险';
$lang->group->package->manageRisk            = '创建维护风险';
$lang->group->package->importRisk            = '导入导出风险';
$lang->group->package->deleteRisk            = '删除风险';
$lang->group->package->importRiskLib         = '导入风险库';
$lang->group->package->browseOpportunity     = '浏览机会';
$lang->group->package->manageOpportunity     = '创建维护机会';
$lang->group->package->deleteOpportunity     = '删除机会';
$lang->group->package->importOpportunityLib  = '导入机会库';
$lang->group->package->importOpportunity     = '导入导出机会';
$lang->group->package->pssp                  = '项目过程';
$lang->group->package->manageAuditPlan       = '维护质量保证计划';
$lang->group->package->nc                    = '不符合项';
$lang->group->package->meeting               = '会议';
$lang->group->package->trainPlan             = '培训计划';
$lang->group->package->gapAnalysis           = '能力差距分析';
$lang->group->package->workflowField         = '工作流字段';
$lang->group->package->workflowAction        = '工作流动作';
$lang->group->package->workflowLayout        = '工作流界面';
$lang->group->package->workflowCondition     = '工作流触发条件';
$lang->group->package->workflowLinkage       = '工作流界面联动';
$lang->group->package->workflowHook          = '工作流扩展动作';
$lang->group->package->workflowLabel         = '工作流标签';
$lang->group->package->workflowReport        = '工作流报表';
$lang->group->package->workflowDatasource    = '工作流数据源';
$lang->group->package->workflowRule          = '工作流验证规则';
$lang->group->package->workflow              = '工作流';
$lang->group->package->downloadCode          = '下载代码';
$lang->group->package->dev                   = '二次开发';
$lang->group->package->browseCodeIssue       = '浏览代码问题';
$lang->group->package->editor                = '编辑器';
$lang->group->package->serverLink            = '接入禅道';
$lang->group->package->browseMR              = '浏览合并请求';
$lang->group->package->managePriv            = '权限后台设置';
$lang->group->package->dept                  = '部门结构';
$lang->group->package->group                 = '权限分组';
$lang->group->package->user                  = '维护用户';
$lang->group->package->extension             = '插件管理';
$lang->group->package->message               = '通知设置';
$lang->group->package->mail                  = '邮件配置';
$lang->group->package->webhook               = 'Webhook';
$lang->group->package->gitlab                = 'GitLab';
$lang->group->package->sms                   = '短信配置';
$lang->group->package->gogs                  = 'Gogs';
$lang->group->package->gitea                 = 'Gitea';
$lang->group->package->sonarqube             = 'SonarQube';
$lang->group->package->repoRules             = '指令';
$lang->group->package->browseJob             = '浏览流水线';
$lang->group->package->manageJob             = '创建维护流水线';
$lang->group->package->manageMR              = '创建维护合并请求';
$lang->group->package->backup                = '系统备份';
$lang->group->package->trash                 = '回收站';
$lang->group->package->security              = '安全';
$lang->group->package->cron                  = '定时';
$lang->group->package->ldap                  = 'LDAP';
$lang->group->package->chat                  = '聊天';
$lang->group->package->jenkins               = 'jenkins';
$lang->group->package->systemSetting         = '系统设置';
$lang->group->package->search                = '搜索';
$lang->group->package->comment               = '备注';
$lang->group->package->module                = '模块维护';
$lang->group->package->file                  = '附件';
$lang->group->package->commonEffort          = '通用日志';
$lang->group->package->docTemplate           = '文档模板';
$lang->group->package->importStoryLib        = '导入需求库';
$lang->group->package->projectStakeholder    = '项目干系人';
$lang->group->package->projectBuild          = '项目版本';
$lang->group->package->importCaseLib         = '导入用例库';
$lang->group->package->commonSetting         = '通用配置';
$lang->group->package->stageSetting          = '阶段列表设置';
$lang->group->package->classify              = '分类项设置';
$lang->group->package->cmcl                  = '审计设置';
$lang->group->package->auditcl               = 'QA检查项设置';
$lang->group->package->reviewcl              = '评审设置';
$lang->group->package->process               = '过程设置';
$lang->group->package->activity              = '活动设置';
$lang->group->package->zoutput               = '文档设置';
$lang->group->package->custom                = '自定义配置';
$lang->group->package->approvalflow          = '审批设置';
$lang->group->package->usercl                = '用户设置';
$lang->group->package->meetingroom           = '会议室设置';
$lang->group->package->sqlBuilder            = 'sql构建器';
$lang->group->package->designSetting         = '设计设置';
$lang->group->package->kanbanSpace           = '维护空间';
$lang->group->package->deleteKanbanSpace     = '删除空间';
$lang->group->package->browseKanban          = '浏览看板';
$lang->group->package->manageKanban          = '创建维护看板';
$lang->group->package->deleteKanban          = '删除看板';
$lang->group->package->deleteZANode          = '删除执行节点';
$lang->group->package->autotesting           = '自动化测试';
$lang->group->package->executionTesting      = '执行测试';
$lang->group->package->manageEffort          = '维护日志';
$lang->group->package->projectTesting        = '项目测试';
$lang->group->package->track                 = '矩阵';
$lang->group->package->workflowRelation      = '工作流跨流程设置';
$lang->group->package->template              = '模板';
$lang->group->package->table                 = '数据表格';
$lang->group->package->automation            = '自动化';
$lang->group->package->git                   = 'Git';
$lang->group->package->subversion            = 'Subversion';
$lang->group->package->ping                  = '防超时';
$lang->group->package->review                = '审批';
$lang->group->package->manageProjectRelease  = '创建维护项目发布';
$lang->group->package->deleteProjectRelease  = '删除项目发布';
$lang->group->package->importProjectRelease  = '导入导出项目发布';
$lang->group->package->projectReleaseNotify  = '项目发布通知';
$lang->group->package->gantt                 = '阶段甘特图';
$lang->group->package->executionRelation     = '维护任务关系';
$lang->group->package->browseBuild           = '浏览版本';
$lang->group->package->browseExecutionStory  = '浏览执行研发需求';
$lang->group->package->manageCard            = '维护卡片';
$lang->group->package->browseProjectStory    = '浏览项目研发需求';
$lang->group->package->chckAuditPlan         = '检查质量保证计划';
$lang->group->package->reviewAssess          = '评审基线评审';
$lang->group->package->reviewAudit           = '审计基线评审';
$lang->group->package->dimension             = '管理维度';
$lang->group->package->browseScreen          = '浏览大屏';
$lang->group->package->manageScreen          = '创建维护大屏';
$lang->group->package->deleteScreen          = '删除大屏';
$lang->group->package->screenDataPermission  = '大屏数据权限';
$lang->group->package->browsePivot           = '浏览透视表';
$lang->group->package->designPivot           = '设计透视表';
$lang->group->package->exportPivot           = '导出透视表';
$lang->group->package->pivotDataPermission   = '透视表数据权限';
$lang->group->package->browseChart           = '浏览图表';
$lang->group->package->designChart           = '设计图表';
$lang->group->package->exportChart           = '导出图表';
$lang->group->package->browseDataview        = '浏览数据表';
$lang->group->package->manageDataview        = '创建维护数据表';
$lang->group->package->deleteDataview        = '删除数据表';
$lang->group->package->browseMetric          = '浏览度量项';
$lang->group->package->manageMetric          = '管理度量项';
$lang->group->package->browseDoc             = '浏览文档';
$lang->group->package->manageDoc             = '创建与维护文档';
$lang->group->package->deleteDoc             = '删除文档';
$lang->group->package->exportDoc             = '导出文档';
$lang->group->package->browseAPI             = '浏览接口空间';
$lang->group->package->manageAPI             = '创建维护接口';
$lang->group->package->exportAPI             = '导出接口';
$lang->group->package->deleteAPI             = '删除接口';
$lang->group->package->callAPI               = '接口调用';
$lang->group->package->scene                 = '维护场景';
$lang->group->package->executionTree         = '树状图';
$lang->group->package->taskEffort            = '工时明细表';
$lang->group->package->taskCalendar          = '任务日历';
$lang->group->package->code                  = '代码';
$lang->group->package->repo                  = '代码库';
$lang->group->package->browseDemandPool      = '浏览需求池';
$lang->group->package->browseCharter         = '浏览立项';
$lang->group->package->manageDemandPool      = '创建维护需求池';
$lang->group->package->manageCharter         = '创建维护立项';
$lang->group->package->reviewCharter         = '立项审批';
$lang->group->package->browseDemand          = '浏览需求';
$lang->group->package->manageDemand          = '创建维护需求';
$lang->group->package->reviewDemand          = '评审需求';
$lang->group->package->importDemand          = '导入导出需求';
$lang->group->package->deleteDemand          = '删除需求';
$lang->group->package->admin                 = '后台';
$lang->group->package->browseRoadmap         = '浏览产品路标';
$lang->group->package->manageRoadmap         = '创建维护产品路标';
$lang->group->package->deleteRoadmap         = '删除产品路标';
$lang->group->package->deleteDemandPool      = '删除需求池';
$lang->group->package->exportDatatable       = '导出数据表';
$lang->group->package->ai                    = 'AI';
$lang->group->package->aiChatting            = 'AI 聊天';
$lang->group->package->executePrompt         = '执行提词';
$lang->group->package->manageLLM             = '语言模型管理';
$lang->group->package->browsePrompt          = '浏览提词';
$lang->group->package->managePrompt          = '维护和设计提词';
$lang->group->package->publishPrompt         = '提词上下架';
$lang->group->package->deletePrompt          = '删除提词';
$lang->group->package->manageMiniProgram     = '维护和设计小程序';
$lang->group->package->browseMiniProgram     = '浏览小程序';
$lang->group->package->miniProgramSquare     = 'AI小程序广场';
$lang->group->package->publishMiniProgram    = 'AI小程序上下架';
$lang->group->package->deleteMiniProgram     = '删除AI小程序';
$lang->group->package->impAndExpMiniProgram  = '导入导出AI小程序';
$lang->group->package->dashboard             = '仪表盘';
$lang->group->package->resource              = '资源';
$lang->group->package->manageServiceProvider = '服务商管理';
$lang->group->package->manageCity            = '城市管理';
$lang->group->package->manageCPU             = 'CPU管理';
$lang->group->package->manageOS              = '系统版本管理';
$lang->group->package->browseRepo            = '浏览代码库';
$lang->group->package->manageRepo            = '创建维护代码库';
$lang->group->package->deleteRepo            = '删除代码库';
$lang->group->package->browseCode            = '浏览代码';
$lang->group->package->manageCode            = '维护代码';
$lang->group->package->CodeIssule            = '问题';
$lang->group->package->manageCodeIssue       = '创建维护代码问题';
$lang->group->package->deleteCodeIssue       = '删除代码问题';
$lang->group->package->deleteMR              = '删除合并请求';
$lang->group->package->deleteJob             = '删除流水线';
$lang->group->package->artifactrepo          = '制品库';
$lang->group->package->browseArtifactrepo    = '浏览制品库';
$lang->group->package->manageArtifactrepo    = '创建维护制品库';
$lang->group->package->deleteArtifactrepo    = '删除制品库';
$lang->group->package->browseApplication     = '浏览应用';
$lang->group->package->mangeApplication      = '管理应用';
$lang->group->package->trainPracticeLib      = '实践库';

include (dirname(__FILE__) . '/resource.php');
