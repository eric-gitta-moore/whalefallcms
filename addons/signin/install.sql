CREATE TABLE `__PREFIX__signin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `successions` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '连续签到次数',
  `type` enum('normal','fillup') DEFAULT 'normal' COMMENT '签到类型',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='签到表';