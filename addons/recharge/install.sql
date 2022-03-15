
BEGIN;
CREATE TABLE `__PREFIX__recharge_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `orderid` varchar(100) DEFAULT NULL COMMENT '订单ID',
  `user_id` int(10) unsigned DEFAULT '0' COMMENT '会员ID',
  `amount` double(10,2) unsigned DEFAULT '0.00' COMMENT '订单金额',
  `payamount` double(10,2) unsigned DEFAULT '0.00' COMMENT '支付金额',
  `paytype` varchar(50) DEFAULT NULL COMMENT '支付类型',
  `paytime` int(10) DEFAULT NULL COMMENT '支付时间',
  `ip` varchar(50) DEFAULT NULL COMMENT 'IP地址',
  `useragent` varchar(255) DEFAULT NULL COMMENT 'UserAgent',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `createtime` int(10) DEFAULT NULL COMMENT '添加时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `status` enum('created','paid','expired') DEFAULT 'created' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='充值表';
COMMIT;

BEGIN;
ALTER TABLE `__PREFIX__user` ADD COLUMN `money` decimal(10, 2) UNSIGNED NULL DEFAULT 0 COMMENT '余额' AFTER `bio`;
COMMIT;

BEGIN;
CREATE TABLE `__PREFIX__user_money_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更余额',
  `before` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更前余额',
  `after` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更后余额',
  `memo` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='会员余额变动表';
COMMIT;