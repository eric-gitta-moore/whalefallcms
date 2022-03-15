CREATE TABLE `__PREFIX__invite` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `invited_user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '被邀请人',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT '注册IP',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='邀请表';