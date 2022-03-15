
CREATE TABLE IF NOT EXISTS `__PREFIX__message_notice` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `message_type` enum('system','user') NOT NULL DEFAULT 'system' COMMENT '消息类型',
  `message_title` varchar(255) DEFAULT NULL COMMENT '消息标题',
  `message_content` text NOT NULL COMMENT '消息内容',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='站内消息表';

CREATE TABLE IF NOT EXISTS `__PREFIX__message_user` (
  `rec_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `message_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '消息id',
  `is_see` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否查看：0未查看, 1已查看',
  `deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户假删除标识,1:删除,0未删除',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`rec_id`),
  KEY `user_id` (`user_id`),
  KEY `message_id` (`message_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户的消息表';
