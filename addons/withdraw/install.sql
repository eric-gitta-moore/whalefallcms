
BEGIN;
CREATE TABLE `__PREFIX__withdraw` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT '0' COMMENT '会员ID',
  `money` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '金额',
  `handingfee` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '手续费',
  `taxes` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '税费',
  `type` varchar(50) DEFAULT '' COMMENT '类型',
  `account` varchar(100) DEFAULT '' COMMENT '提现账户',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `orderid` varchar(50) DEFAULT '' COMMENT '订单号',
  `transactionid` varchar(50) DEFAULT '' COMMENT '流水号',
  `status` enum('created','successed','rejected') DEFAULT 'created' COMMENT '状态:created=申请中,successed=成功,rejected=已拒绝',
  `transfertime` int(10) DEFAULT NULL COMMENT '转账时间',
  `createtime` int(10) DEFAULT NULL COMMENT '添加时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='提现表';
COMMIT;