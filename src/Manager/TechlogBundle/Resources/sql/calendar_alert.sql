CREATE TABLE calendar_alert (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '任务名称',
  `insert_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间',
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间',
  `alert_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '上次提醒时间',
  `status` int(4) NOT NULL DEFAULT 0 COMMENT '0.正常执行, 1.循环执行, 2.停止执行',
  `lunar` int(4) NOT NULL DEFAULT 0 COMMENT '是否是农历, 0. 否，1. 是',
  `cycle_type` int(4) NOT NULL DEFAULT 0 COMMENT '循环类型，0.不循环，1.日，2.周，3.月，4.年，5.工作日',
  `period` int(11) NOT NULL DEFAULT 0 COMMENT '循环周期',
  `category` int(4) NOT NULL DEFAULT '0' COMMENT '任务分类',
  `remark` varchar(2048) NOT NULL DEFAULT '' COMMENT '备注',  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='日历提醒表'
