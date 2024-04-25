UPDATE `zt_workflowfield` SET `default` = 0 WHERE `field` = 'approva' AND `role` = 'approval' AND `default` = '';
UPDATE `zt_workflowfield` SET `default` = 'wait' WHERE `field` = 'reviewStatus' AND `role` = 'approval' AND `default` = '';

ALTER TABLE `zt_project` ADD COLUMN `enabled` enum('on','off') NOT NULL DEFAULT 'on' AFTER `parallel`;
ALTER TABLE `zt_object`  ADD COLUMN `enabled` enum('0','1')    NOT NULL DEFAULT '1'  AFTER `type`;

INSERT INTO `zt_metric`(`purpose`, `scope`, `object`, `stage`, `type`, `name`, `code`, `alias`, `unit`, `desc`, `definition`, `when`, `createdBy`, `createdDate`, `builtin`, `deleted`, `dateType`)  VALUES
('scale', 'project', 'execution', 'released', 'php', '按项目统计的年度完成执行数', 'count_of_annual_finished_execution_in_project', '完成执行数', 'count', '按项目统计的年度完成执行数是指项目在某年度已经完成的执行数。该度量项反映了项目团队在某年的工作效率和完成能力。较高的年度完成执行数表示团队在完成任务方面表现出较高的效率，反之则可能需要审查工作流程和资源分配情况，以提高执行效率。', '项目的执行个数求和\r\n实际完成日期为某年\r\n过滤已删除的执行\r\n过滤已删除的项目', 'realtime', 'system', '2023-08-22 08:00:00', '1', '0', 'year');

UPDATE `zt_workflowaction` SET `action` = 'confirm' WHERE `action` = 'confirmBug' AND `module` = 'bug';
UPDATE `zt_workflowlayout` SET `action` = 'confirm' WHERE `action` = 'confirmBug' AND `module` = 'bug';

UPDATE `zt_metric` SET `definition` = '所有研发需求个数求和\r\n评审人为某人\r\n评审结果为空\r\n评审状态为评审中\r\n过滤已删除的需求\r\n过滤已删除产品的需求' WHERE `code` = 'count_of_reviewing_story_in_user';

ALTER TABLE zt_metriclib MODIFY id bigint AUTO_INCREMENT;

UPDATE `zt_chart` SET `sql` = 'select tt.topProgram,tt.programID as id,tt.`year`,sum(tt.product) as product,sum(tt.plan) as plan,sum(tt.`release`) as `release`,sum(tt.story) as story,sum(tt.bug) as bug,sum(tt.doc) as doc\r\nfrom (\r\nselect t2.name as topProgram,t2.id as programID,t0.`year`,count(1) as product,0 as plan,0 as story,0 as bug,0 as `release`, 0 as doc\r\nfrom zt_product t1\r\nleft join (SELECT DISTINCT YEAR(`date`) as \'year\' FROM zt_action) t0 on YEAR(t1.createdDate) = t0.`year`\r\nleft join zt_project t2 on t1.program = t2.id\r\nwhere t1.deleted = \'0\' and t1.shadow = \'0\'\r\nand t2.type = \'program\' and t2.grade = 1 and t2.deleted = \'0\'\r\ngroup by t2.id, t0.`year`\r\nunion all\r\nselect t3.name as topProgram,t3.id as programID,t0.`year`,0 as product,count(1) as plan,0 as story,0 as bug,0 as `release`, 0 as doc\r\nfrom zt_productplan t1\r\nleft join (SELECT DISTINCT YEAR(`date`) as \'year\' FROM zt_action) t0 on YEAR(t1.createdDate) = t0.`year`\r\nleft join zt_product t2 on t2.id = t1.product\r\nleft join zt_project t3 on t2.program = t3.id\r\nwhere t1.deleted = \'0\'\r\nand t2.deleted = \'0\'\r\nand t3.type = \'program\' and t3.grade = 1 and t3.deleted = \'0\'\r\ngroup by t3.id, t0.`year`\r\nunion all\r\nselect t3.name as topProgram,t3.id as programID,t0.`year`,0 as product,0 as plan,0 as story,0 as bug,0 as `release`, count(1) as doc\r\nfrom zt_doc t1\r\nleft join (SELECT DISTINCT YEAR(`date`) as \'year\' FROM zt_action) t0 on YEAR(t1.addedDate) = t0.`year`\r\nleft join zt_product t2 on t2.id = t1.product\r\nleft join zt_project t3 on t2.program = t3.id\r\nwhere t1.deleted = \'0\'\r\nand t2.deleted = \'0\'\r\nand t3.type = \'program\' and t3.grade = 1 and t3.deleted = \'0\'\r\ngroup by t3.id, t0.`year`\r\nunion all\r\nselect t3.name as topProgram,t3.id as programID,t0.`year`,0 as product,0 as plan,0 as story,0 as bug,0 as `release`, count(distinct t1.id) as doc\r\nfrom zt_doc t1\r\nleft join (SELECT DISTINCT YEAR(`date`) as \'year\' FROM zt_action) t0 on YEAR(t1.addedDate) = t0.`year`\r\nleft join zt_projectproduct t4 on t1.project = t4.project\r\nleft join zt_product t2 on t2.id = t4.product\r\nleft join zt_project t3 on t2.program = t3.id\r\nwhere t1.deleted = \'0\'\r\nand t2.deleted = \'0\'\r\nand t3.type = \'program\' and t3.grade = 1 and t3.deleted = \'0\'\r\ngroup by t3.id, t0.`year`\r\nunion all\r\nselect t3.name as topProgram,t3.id as programID,t0.`year`,0 as product,0 as plan,0 as story,0 as bug,count(1) as `release`, 0 as doc\r\nfrom zt_release t1\r\nleft join (SELECT DISTINCT YEAR(`date`) as \'year\' FROM zt_action) t0 on YEAR(t1.date) = t0.`year`\r\nleft join zt_product t2 on t2.id = t1.product\r\nleft join zt_project t3 on t2.program = t3.id\r\nwhere t1.deleted = \'0\'\r\nand t2.deleted = \'0\'\r\nand t3.type = \'program\' and t3.grade = 1 and t3.deleted = \'0\'\r\ngroup by t3.id, t0.`year`\r\nunion all\r\nselect t3.name as topProgram,t3.id as programID,t0.`year`,0 as product,0 as plan,count(1) as story,0 as bug,0 as `release`, 0 as doc\r\nfrom zt_story t1\r\nleft join (SELECT DISTINCT YEAR(`date`) as \'year\' FROM zt_action) t0 on YEAR(t1.openedDate) = t0.`year`\r\nleft join zt_product t2 on t2.id = t1.product\r\nleft join zt_project t3 on t2.program = t3.id\r\nwhere t1.deleted = \'0\'\r\nand t2.deleted = \'0\'\r\nand t3.type = \'program\' and t3.grade = 1 and t3.deleted = \'0\'\r\ngroup by t3.id, t0.`year`\r\nunion all\r\nselect t3.name as topProgram,t3.id as programID,t0.`year`,0 as product,0 as plan,0 as story,count(1) as bug,0 as `release`, 0 as doc\r\nfrom zt_bug t1\r\nleft join (SELECT DISTINCT YEAR(`date`) as \'year\' FROM zt_action) t0 on YEAR(t1.openedDate) = t0.`year`\r\nleft join zt_product t2 on t2.id = t1.product\r\nleft join zt_project t3 on t2.program = t3.id\r\nwhere t1.deleted = \'0\'\r\nand t2.deleted = \'0\'\r\nand t3.type = \'program\' and t3.grade = 1 and t3.deleted = \'0\'\r\ngroup by t3.id, t0.`year`\r\n) tt\r\ngroup by tt.programID, tt.`year`' WHERE `name` = '年度新增-项目集年度新增数据汇总表';
UPDATE `zt_chart` SET `sql` = 'SELECT YEAR(t1.date) AS `year`,IFNULL(t2.realname,t1.actor) AS realname,count(1) AS count FROM zt_action t1 LEFT JOIN zt_user AS t2 ON t1.actor=t2.account where t1.actor is not null and t1.actor not in(\'\', \'system\') GROUP BY `year`,t1.actor ORDER BY `year`, `count` DESC' WHERE `name` = '年度排行-个人-禅道操作次数榜';
UPDATE `zt_chart` SET `sql` = 'SELECT t1.id, t1.NAME AS project, IFNULL( t2.NAME, \'/\') AS program, IFNULL( t3.story, 0 ) AS story, IFNULL( t3.estimate, 0 ) AS estimate, IFNULL( t4.execution, 0 ) AS execution, IFNULL( t5.workhour, 0 ) AS workhour FROM zt_project AS t1 LEFT JOIN zt_project AS t2 ON FIND_IN_SET( t2.id, t1.path ) AND t2.deleted = \'0\' AND t2.type = \'program\' AND t2.grade = 1 LEFT JOIN( SELECT t1.parent AS project, COUNT( 1 ) AS story, ROUND( SUM( t1.estimate ), 1 ) AS estimate FROM ( SELECT DISTINCT t1.parent, t3.id, t3.estimate FROM zt_project AS t1 LEFT JOIN zt_projectstory AS t2 ON t1.id = t2.project LEFT JOIN zt_story AS t3 ON t2.story = t3.id AND t3.deleted = \'0\' AND t3.stage NOT IN ( \'verified\', \'released\', \'closed\' ) WHERE t1.deleted = \'0\' AND t1.type IN ( \'sprint\', \'stage\', \'kanban\' ) AND t3.id IS NOT NULL ) AS t1 GROUP BY project ) AS t3 ON t1.id = t3.project LEFT JOIN ( SELECT parent AS project, COUNT( 1 ) AS execution FROM zt_project WHERE deleted = \'0\' AND type IN ( \'sprint\', \'stage\', \'kanban\' ) AND multiple = \'1\' AND STATUS NOT IN ( \'done\', \'closed\' ) GROUP BY project ) AS t4 ON t1.id = t4.project LEFT JOIN ( SELECT t1.parent AS project, ROUND( SUM( t2.LEFT ), 1 ) AS workhour FROM zt_project AS t1 LEFT JOIN zt_task AS t2 ON t1.id = t2.execution AND t2.deleted = \'0\' AND t2.parent < 1 WHERE t1.deleted = \'0\' AND t1.type IN ( \'sprint\', \'stage\', \'kanban\' ) AND t1.STATUS NOT IN ( \'done\', \'closed\' ) AND t2.id IS NOT NULL GROUP BY project ) AS t5 ON t1.id = t5.project WHERE t1.deleted = \'0\' AND t1.type = \'project\' AND t1.STATUS = \'doing\'' WHERE `name` = '年度进行中项目-项目剩余工作量透视表';

UPDATE `zt_grouppriv` SET `method` = 'recordWorkhour' WHERE `module` = 'task' AND `method` = 'recordEstimate';
UPDATE `zt_grouppriv` SET `method` = 'editEffort'     WHERE `module` = 'task' AND `method` = 'editEstimate';
UPDATE `zt_grouppriv` SET `method` = 'deleteWorkhour' WHERE `module` = 'task' AND `method` = 'deleteEstimate';

ALTER TABLE `zt_demandreview` CHANGE `reviewDate` `reviewDate` datetime NULL;

DELETE FROM `zt_lang` WHERE `module` = 'project' AND `section` = 'menuOrder';
DELETE FROM `zt_config` WHERE `module` = 'bi' AND `key` IN ('update2BI','bizGuide','pmsGuide');
