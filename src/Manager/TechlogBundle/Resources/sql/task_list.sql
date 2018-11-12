Create Table: CREATE TABLE `task_list` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL DEFAULT '' COMMENT '任务名称',
	`insert_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
	`update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
	`finish_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '完成或取消时间',
	`status` int(4) NOT NULL DEFAULT '0' COMMENT '任务状态, 0. 创建，1. 开始执行 2. 完成，3.取消',
	`remark` varchar(2048) NOT NULL DEFAULT '' COMMENT '备注',
	`category` int(4) NOT NULL DEFAULT '0' COMMENT '任务分类',
	`priority` int(4) NOT NULL DEFAULT '0' COMMENT '任务分类',
	`start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间',
	PRIMARY KEY (`id`),
	UNIQUE KEY `name` (`name`),
	KEY `idx_status_inserttime` (`status`,`insert_time`),
	KEY `idx_finish_time` (`finish_time`),
	KEY `idx_update_time` (`update_time`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COMMENT='任务表'
