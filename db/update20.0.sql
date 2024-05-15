REPLACE INTO `zt_lang` (`lang`, `module`, `section`, `key`, `value`, `system`, `vision`) VALUES
('zh-cn', 'custom', 'URSRList', '1', '{\"ERName\":\"\\u4e1a\\u52a1\\u9700\\u6c42\",\"SRName\":\"\\u8f6f\\u4ef6\\u9700\\u6c42\",\"URName\":\"\\u7528\\u6237\\u9700\\u6c42\"}', '0', 'rnd'),
('zh-cn', 'custom', 'URSRList', '2', '{\"ERName\":\"\\u4e1a\\u52a1\\u9700\\u6c42\",\"SRName\":\"\\u7814\\u53d1\\u9700\\u6c42\",\"URName\":\"\\u7528\\u6237\\u9700\\u6c42\"}', '0', 'rnd'),
('zh-cn', 'custom', 'URSRList', '3', '{\"ERName\":\"\\u4e1a\\u9700\",\"SRName\":\"\\u8f6f\\u9700\",\"URName\":\"\\u7528\\u9700\"}', '0', 'rnd'),
('zh-cn', 'custom', 'URSRList', '4', '{\"ERName\":\"\\u53f2\\u8bd7\",\"SRName\":\"\\u6545\\u4e8b\",\"URName\":\"\\u7279\\u6027\"}', '0', 'rnd'),
('zh-cn', 'custom', 'URSRList', '5', '{\"ERName\":\"\\u4e1a\\u52a1\\u9700\\u6c42\",\"SRName\":\"\\u9700\\u6c42\",\"URName\":\"\\u7528\\u6237\\u9700\\u6c42\"}', '0', 'rnd'),
('en',    'custom', 'URSRList', '1', '{\"ERName\":\"Epic\",\"SRName\":\"Story\",\"URName\":\"Feature\"}',     '0', 'rnd'),
('en',    'custom', 'URSRList', '2', '{\"ERName\":\"Epic\",\"SRName\":\"Story\",\"URName\":\"Requirement\"}', '0', 'rnd'),
('fr',    'custom', 'URSRList', '1', '{\"ERName\":\"Epic\",\"SRName\":\"Story\",\"URName\":\"Feature\"}',     '0', 'rnd'),
('fr',    'custom', 'URSRList', '2', '{\"ERName\":\"Epic\",\"SRName\":\"Story\",\"URName\":\"Requirement\"}', '0', 'rnd'),
('de',    'custom', 'URSRList', '1', '{\"ERName\":\"Epic\",\"SRName\":\"Story\",\"URName\":\"Feature\"}',     '0', 'rnd'),
('de',    'custom', 'URSRList', '2', '{\"ERName\":\"Epic\",\"SRName\":\"Story\",\"URName\":\"Requirement\"}', '0', 'rnd'),
('zh-tw', 'custom', 'URSRList', '1', '{\"ERName\":\"\\u696d\\u52d9\\u9700\\u6c42\",\"SRName\":\"\\u8edf\\u4ef6\\u9700\\u6c42\",\"URName\":\"\\u7528\\u6236\\u9700\\u6c42\"}', '0', 'rnd'),
('zh-tw', 'custom', 'URSRList', '2', '{\"ERName\":\"\\u696d\\u52d9\\u9700\\u6c42\",\"SRName\":\"\\u7814\\u767c\\u9700\\u6c42\",\"URName\":\"\\u7528\\u6236\\u9700\\u6c42\"}', '0', 'rnd'),
('zh-tw', 'custom', 'URSRList', '3', '{\"ERName\":\"\\u696d\\u9700\",\"SRName\":\"\\u8edf\\u9700\",\"URName\":\"\\u7528\\u9700\"}', '0', 'rnd'),
('zh-tw', 'custom', 'URSRList', '4', '{\"ERName\":\"\\u53f2\\u8a69\",\"SRName\":\"\\u6545\\u4e8b\",\"URName\":\"\\u7279\\u6027\"}', '0', 'rnd'),
('zh-tw', 'custom', 'URSRList', '5', '{\"ERName\":\"\\u696d\\u52d9\\u9700\\u6c42\",\"SRName\":\"\\u9700\\u6c42\",\"URName\":\"\\u7528\\u6236\\u9700\\u6c42\"}', '0', 'rnd');

CREATE TABLE `zt_storygrade` (
  `type` enum('story','requirement','epic') NOT NULL,
  `grade` smallint NOT NULL,
  `name` char(30) NOT NULL,
  `status` char(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `zt_storygrade` (`type`, `grade`, `name`, `status`) VALUES
('requirement', 1,    'UR', 'enable'),
('epic',        1,    'BR', 'enable'),
('story',       1,    'SR', 'enable'),
('story',       2,    '子', 'enable');

ALTER TABLE `zt_story` ADD `isParent` enum('0','1') NOT NULL DEFAULT '0' AFTER `parent`;
ALTER TABLE `zt_story` ADD `root` mediumint NOT NULL DEFAULT '0' AFTER `isParent`;
ALTER TABLE `zt_story` ADD `path` text NULL AFTER `root`;
ALTER TABLE `zt_story` ADD `grade` smallint(6) NOT NULL DEFAULT '0' AFTER `path`;
ALTER TABLE `zt_story` ADD `parentVersion` smallint NOT NULL DEFAULT '0' AFTER `version`;
ALTER TABLE `zt_story` CHANGE `stage` `stage` enum('','wait','defining','planning','planned','projected','designing','designed','developing','developed','testing','tested','verified','rejected','delivering','pending','released','closed') NOT NULL DEFAULT 'wait';
ALTER TABLE `zt_story` DROP `childStories`;
CREATE INDEX `root` ON `zt_story` (`root`);
UPDATE `zt_story` SET stage = 'defining'   WHERE stage = 'wait' AND (parent = -1 OR type != 'story');
UPDATE `zt_story` SET stage = 'planning'   WHERE stage in ('planned', 'projected') AND (parent = -1 OR type != 'story');
UPDATE `zt_story` SET stage = 'developing' WHERE stage in ('developed', 'testing', 'tested') AND (parent = -1 OR type != 'story');
UPDATE `zt_story` SET stage = 'delivering' WHERE stage in ('verified', 'released') AND (parent = -1 OR type != 'story');
UPDATE `zt_story` SET isParent = '1' WHERE parent = -1;
UPDATE `zt_story` SET grade = 1, parent = 0, root = id, path = concat(',', id, ',') WHERE type != 'story';
UPDATE `zt_story` SET grade = 1, parent = 0, root = id, path = concat(',', id, ',') WHERE type = 'story' AND parent <= 0;
UPDATE `zt_story` SET grade = 2, root = parent, path = concat(',', parent, ',', id, ',') WHERE type = 'story' AND parent > 0;

INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'story', '', 'gradeRule', 'stepwise');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'requirement', '', 'gradeRule', 'stepwise');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'epic', '', 'gradeRule', 'stepwise');
INSERT INTO `zt_config` (`owner`, `module`, `section`, `key`, `value`) VALUES ('system', 'custom', '', 'enableER', '1');

ALTER TABLE `zt_product`
ADD `draftRequirements` mediumint(8) NOT NULL DEFAULT '0' AFTER `reviewer`,
ADD `activeRequirements` mediumint(8) NOT NULL DEFAULT '0' AFTER `draftRequirements`,
ADD `changingRequirements` mediumint(8) NOT NULL DEFAULT '0' AFTER `activeRequirements`,
ADD `reviewingRequirements` mediumint(8) NOT NULL DEFAULT '0' AFTER `changingRequirements`,
ADD `finishedRequirements` mediumint NOT NULL DEFAULT '0' AFTER `reviewingRequirements`,
ADD `closedRequirements` mediumint(8) NOT NULL DEFAULT '0' AFTER `finishedRequirements`,
ADD `totalRequirements` mediumint(8) NOT NULL DEFAULT '0' AFTER `closedRequirements`;

ALTER TABLE `zt_product`
ADD `draftEpics` mediumint(8) NOT NULL DEFAULT '0' AFTER `reviewer`,
ADD `activeEpics` mediumint(8) NOT NULL DEFAULT '0' AFTER `draftEpics`,
ADD `changingEpics` mediumint(8) NOT NULL DEFAULT '0' AFTER `activeEpics`,
ADD `reviewingEpics` mediumint(8) NOT NULL DEFAULT '0' AFTER `changingEpics`,
ADD `finishedEpics` mediumint NOT NULL DEFAULT '0' AFTER `reviewingEpics`,
ADD `closedEpics` mediumint(8) NOT NULL DEFAULT '0' AFTER `finishedEpics`,
ADD `totalEpics` mediumint(8) NOT NULL DEFAULT '0' AFTER `closedEpics`;

ALTER TABLE `zt_project` ADD `storyType` char(30) NULL DEFAULT '' AFTER `auth`;
UPDATE `zt_project` SET storyType = 'story' WHERE storyType = '';

CREATE TABLE IF NOT EXISTS `zt_ai_assistant` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(30) NOT NULL,
    `modelId` mediumint(8) unsigned NOT NULL,
    `desc` text NOT NULL,
    `systemMessage` text NOT NULL,
    `greetings` text NOT NULL,
    `icon` varchar(30) DEFAULT 'coding-1' NOT NULL,
    `enabled` enum('0', '1') NOT NULL DEFAULT '1',
    `createdDate` datetime NOT NULL,
    `publishedDate` datetime DEFAULT NULL,
    `deleted` enum('0','1') NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `zt_kanbancell` MODIFY `cards` mediumtext NULL;
ALTER TABLE `zt_user` MODIFY `ip` varchar(255) NOT NULL DEFAULT '';

DELETE FROM `zt_config` WHERE `owner`='system' AND `module`='custom' AND `key`='productProject';

INSERT INTO `zt_metric` (`purpose`, `scope`, `object`, `stage`, `type`, `name`, `code`, `alias`, `unit`, `desc`, `definition`, `when`, `createdBy`, `createdDate`, `builtin`, `deleted`, `dateType`) VALUES
('scale', 'product', 'feedback', 'released', 'php', '按产品统计的每周新增反馈数', 'count_of_weekly_created_feedback_in_product', '新增反馈数', 'count', '按产品统计的每周新增反馈数是指在一个周内收集到的用户反馈的数量。这个度量项可以帮助团队了解用户对产品的发展趋势和需求变化，并进行产品策略的调整和优化', '产品中创建时间为某个周的反馈的个数求和\r\n过滤已删除的反馈\r\n过滤已删除的产品', 'realtime', 'system', '2024-05-07 08:00:00', '1', '0', 'week'),
('scale', 'product', 'feedback', 'released', 'php', '按产品统计的处理中的反馈数', 'count_of_doing_feedback_in_product', '处理中反馈数', 'count', '按产品统计的处理中的反馈数表示产品中状态为处理中的反馈数量之和。该数值越大，说明团队并行处理的反馈越多，可以帮助团队了解当前的工作负载情况', '产品中所有反馈个数求和\r\n状态为处理中\r\n过滤已删除的反馈\r\n过滤已删除的产品', 'realtime', 'system', '2024-05-07 08:00:00', '1', '0', 'nodate'),
('scale', 'product', 'feedback', 'released', 'php', '按产品统计的已处理的反馈数', 'count_of_done_feedback_in_product', '已处理反馈数', 'count', '按产品统计的已处理的反馈数表示产品中状态为已处理的反馈数量之和。该数值越大，说明团队成员处理的反馈越多，有利于提高用户满意度', '产品中所有反馈个数求和\r\n状态为已处理\r\n过滤已删除的反馈\r\n过滤已删除的产品', 'realtime', 'system', '2024-05-07 08:00:00', '1', '0', 'nodate'),
('scale', 'product', 'feedback', 'released', 'php', '按产品统计的待完善的反馈数', 'count_of_clarify_feedback_in_product', '待完善反馈数', 'count', '按产品统计的待完善的反馈数表示产品中状态为待完善的反馈数量之和。该数值越大，说明有较多的反馈信息不清晰或比较复杂。需要反馈者更多的澄清和解释', '产品中所有反馈个数求和\r\n状态为待完善\r\n过滤已删除的反馈\r\n过滤已删除的产品', 'realtime', 'system', '2024-05-07 08:00:00', '1', '0', 'nodate'),
('scale', 'product', 'feedback', 'released', 'php', '按产品统计的待处理的反馈数', 'count_of_wait_feedback_in_product', '待处理反馈数', 'count', '按产品统计的待处理的反馈数表示产品中状态为待处理的反馈数量之和。该度量项可能暗示产品团队的反馈处理效率，待处理反馈数越多，可能会导致客户满意度降低', '产品中所有反馈个数求和\r\n状态为待处理\r\n过滤已删除的反馈\r\n过滤已删除的产品', 'realtime', 'system', '2024-05-07 08:00:00', '1', '0', 'nodate'),
('scale', 'product', 'feedback', 'released', 'php', '按产品统计的追问中的反馈数', 'count_of_asked_feedback_in_product', '追问中反馈数', 'count', '按产品统计的追问中的反馈数表示产品中状态为追问中的反馈数量之和。该度量项可能暗示着反馈的复杂性或对处理方案的疑惑，追问中的反馈数量越多，可能意味着团队需要更多时间和资源来回复并解决这些问题', '产品中所有反馈个数求和\r\n状态为追问中\r\n过滤已删除的反馈\r\n过滤已删除的产品', 'realtime', 'system', '2024-05-07 08:00:00', '1', '0', 'nodate'),
('scale', 'product', 'feedback', 'released', 'php', '按产品统计的未关闭的反馈数', 'count_of_unclosed_feedback_in_product', '未关闭反馈数', 'count', '按产品统计的未关闭的反馈数表示产品中状态为未关闭的反馈数量之和。这个度量项可以一定程度反映产品团队响应用户反馈的效率和及时处理用户问题的能力', '产品中所有反馈个数求和\r\n过滤状态为已关闭的反馈\r\n过滤已删除的反馈\r\n过滤已删除的产品', 'realtime', 'system', '2024-05-07 08:00:00', '1', '0', 'nodate'),
('scale', 'user', 'ticket', 'released', 'php', '按人员统计的待处理工单数', 'count_of_assigned_ticket_in_user', '待处理工单数', 'count', '按人员统计的待处理工单数表示每个人待处理的工单数量之和。反映了每个人员需要处理的工单数量的规模。该数值越大，说明需要处理的反馈任务越多。', '所有工单个数求和\r\n指派给为某人\r\n过滤已删除的工单\r\n过滤已删除产品的工单', 'realtime', 'system', '2024-05-07 08:00:00', '1', '0', 'nodate'),
('scale', 'user', 'qa', 'released', 'php', '按人员统计的待处理QA数', 'count_of_assigned_qa_in_user', '待处理QA数', 'count', '按人员统计的待处理QA数表示每个人待处理的质量保证问题之和。反映了每个人员需要处理的质量保证问题的规模。该数值越大，说明需要处理的质量保证问题越多。', '所有待处理的QA个数求和（包含：待处理质量保证计划、待处理不符合项）\r\n指派给为某人\r\n质量保证计划状态为待检查、不符合项状态为待解决\r\n过滤已删除的质量保证计划和不符合项\r\n过滤已删除项目的质量保证计划和不符合项', 'realtime', 'system', '2024-05-07 08:00:00', '1', '0', 'nodate'),
('scale', 'user', 'risk', 'released', 'php', '按人员统计的待处理风险数', 'count_of_assigned_risk_in_user', '待处理风险数', 'count', '按人员统计的待处理用户需求数表示每个人待处理的用户需求数量之和。反映了每个人员需要处理的用户需求数量的规模。该数值越大，说明需要投入越多的时间处理用户需求。', '所有风险个数求和\r\n指派给为某人\r\n过滤已删除的风险\r\n过滤已关闭的风险\r\n过滤已删除项目的风险', 'realtime', 'system', '2024-05-07 08:00:00', '1', '0', 'nodate'),
('scale', 'user', 'issue', 'released', 'php', '按人员统计的待处理问题数', 'count_of_assigned_issue_in_user', '待处理问题数', 'count', '按人员统计的待处理问题数表示每个人待处理的问题数量之和。反映了每个人员需要处理的问题数量的规模。该数值越大，项目存在问题越多，需要投入越多的时间处理问题。', '所有问题个数求和\r\n指派给为某人\r\n过滤已删除的问题\r\n过滤已删除项目的问题', 'realtime', 'system', '2024-05-07 08:00:00', '1', '0', 'nodate'),
('scale', 'user', 'requirement', 'released', 'php', '按人员统计的待处理用户需求数', 'count_of_assigned_requirement_in_user', '待处理用需数', 'count', '按人员统计的待处理用户需求数表示每个人待处理的用户需求数量之和。反映了每个人员需要处理的用户需求数量的规模。该数值越大，说明需要投入越多的时间处理用户需求。', '所有用户需求个数求和\r\n指派给为某人\r\n过滤已删除的用户需求\r\n过滤已删除产品的用户需求', 'realtime', 'system', '2024-05-07 08:00:00', '1', '0', 'nodate');

ALTER TABLE `zt_chart` ADD `code` varchar(255) not NULL default '' AFTER `name`;
ALTER TABLE `zt_pivot` ADD `code` varchar(255) not NULL default '' AFTER `group`;

UPDATE `zt_kanbancard` SET `color` = '#937c5a' WHERE `color` = '#b10b0b';
UPDATE `zt_kanbancard` SET `color` = '#fc5959' WHERE `color` = '#cfa227';
UPDATE `zt_kanbancard` SET `color` = '#ff9f46' WHERE `color` = '#2a5f29';

INSERT INTO `zt_cron`(`m`, `h`, `dom`, `mon`, `dow`, `command`, `remark`, `type`, `buildin`, `status`, `lastTime`) VALUES ('0', '*/1', '*', '*', '*', 'moduleName=metric&methodName=updateDashboardMetricLib', '计算仪表盘数据', 'zentao', 1, 'normal', NUll);

UPDATE `zt_todo` SET `type` = 'custom' WHERE `type` = 'cycle' AND `cycle` = 0;
