CREATE TABLE IF NOT EXISTS `__PREFIX__qnbuygroup_order`
(
    `id`         int(10) unsigned                  NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `orderid`    varchar(100)                      NOT NULL COMMENT '订单ID',
    `user_id`    int(10) unsigned                  NOT NULL DEFAULT '0' COMMENT '会员ID',
    `group_id`   int(10) unsigned                  NOT NULL COMMENT '购买用户组ID',
    `amount`     double(10, 2) unsigned            NOT NULL DEFAULT '0.00' COMMENT '订单金额',
    `payamount`  double(10, 2) unsigned                     DEFAULT '0.00' COMMENT '支付金额',
    `paytype`    varchar(50)                                DEFAULT NULL COMMENT '支付类型',
    `paytime`    int(10)                                    DEFAULT NULL COMMENT '支付时间',
    `ip`         varchar(50)                                DEFAULT NULL COMMENT 'IP地址',
    `useragent`  varchar(255)                               DEFAULT NULL COMMENT 'UserAgent',
    `memo`       varchar(255)                               DEFAULT NULL COMMENT '备注',
    `createtime` int(10)                                    DEFAULT NULL COMMENT '添加时间',
    `updatetime` int(10)                                    DEFAULT NULL COMMENT '更新时间',
    `status`     enum ('created','paid','expired') NOT NULL DEFAULT 'created' COMMENT '状态',
    PRIMARY KEY (`id`),
    UNIQUE KEY `orderid_` (`orderid`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8 COMMENT ='用户组购买表';


CREATE TABLE IF NOT EXISTS `__PREFIX__qnbuygroup_set`
(
    `id`          int(10) unsigned       NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `groupname`   varchar(30)            NOT NULL COMMENT '用户组',
    `group_id`    int(10)                NOT NULL COMMENT '系统用户组ID',
    `expgroup_id` int(10)                NOT NULL COMMENT '过期后的系统用户组ID',
    `amount`      double(10, 2) unsigned NOT NULL DEFAULT '0.00' COMMENT '价格',
    `exp`         int(10)                NOT NULL COMMENT '有效期(单位:天)',
    `description` varchar(255)                    DEFAULT NULL COMMENT '特权说明',
    `weigh`       int(10)                NOT NULL DEFAULT '0' COMMENT '权重(越小越在前)',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8 COMMENT ='用户组设置表';



CREATE TABLE `__PREFIX__qnbuygroup_usergroup`
(
    `id`          int(10) unsigned          NOT NULL AUTO_INCREMENT COMMENT '主键ID',
    `user_id`     int(10) unsigned          NOT NULL DEFAULT '0' COMMENT '会员ID',
    `group_id`    int(10) unsigned          NOT NULL COMMENT '用户组ID',
    `expiredtime` int(10)                   NOT NULL COMMENT '过期时间',
    `createtime`  int(10)                            DEFAULT NULL COMMENT '添加时间',
    `updatetime`  int(10)                            DEFAULT NULL COMMENT '更新时间',
    `orderid`     varchar(64)               NOT NULL COMMENT '订单号',
    `status`      enum ('normal','expired') NOT NULL DEFAULT 'normal' COMMENT '状态',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8 COMMENT ='用户表';