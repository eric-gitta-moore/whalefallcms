
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

#
# 增加用户规则表字段
#
ALTER TABLE `__PREFIX__user_rule` 
  add `icon` char(30) NOT NULL DEFAULT 'fa fa-circle-o' COMMENT '图标',
  add `condition` varchar(255) NOT NULL DEFAULT '' COMMENT '条件',
  add `type` enum('menu','file') NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点';

#
# 修改用户规则表相应记录
#

REPLACE INTO `__PREFIX__user_rule` (`id`,`pid`,`name`,`title`,`remark`,`ismenu`,`createtime`,`updatetime`,`weigh`,`status`,`icon`,`condition`,`type`) VALUES 
(1,0,'dashboard','Dashboard','Dashboard tips',1,1516168079,1516168079,99,'normal','fa fa-dashboard','','file'),
(2,0,'api','API接口','',1,1516168062,1537758838,2,'hidden','','','file'),
(3,1,'dashboard/index','View','',0,1515386247,1515386247,5,'normal','fa fa-circle-o','','file'),
(4,2,'api/user','会员模块','',1,1515386221,1537758859,11,'hidden','','','file'),
(5,0,'user','User center','',1,1515386262,1516015236,7,'normal','fa fa-users','','file'),
(6,5,'user/secure','安全管理','',1,1516015012,1516015012,10,'normal','fa fa-shield','','file'),
(7,5,'user/rich','财富管理','',1,1541045799,1541052272,9,'normal','fa fa-money','','file'),
(8,5,'user/general','常规管理','',1,1541045799,1541052272,9,'normal','fa fa-cogs','','file'),
(9,4,'api/user/login','登录','',0,1515386247,1537758859,6,'hidden','','','file'),
(10,4,'api/user/register','注册','',0,1515386262,1537758859,8,'hidden','','','file'),
(11,4,'api/user/index','会员中心','',0,1516015012,1537758859,10,'hidden','','','file'),
(12,4,'api/user/profile','个人资料','',0,1516015012,1537758859,3,'hidden','','','file'),
(13,6,'user/profile','Profile','',1,1516015012,1516015012,10,'normal','fa fa-user-o','','file'),
(14,13,'user/profile/index','View','',0,1516015012,1516015012,4,'normal','fa fa-circle-o','','file'),
(15,13,'user/profile/edit','Edit','',0,1516015012,1516015012,4,'normal','fa fa-circle-o','','file'),
(16,6,'user/changepwd','Change password','',1,1541045799,1541056067,8,'normal','fa fa-key','','file'),
(17,16,'user/changepwd/index','View','',0,1541045799,1541045799,0,'normal','fa fa-circle-o','','file'),
(18,6,'user/log','用户日志','',1,1516015012,1541043105,7,'normal','fa fa-file-text-o','','file'),
(19,18,'user/log/index','View','',0,1516015012,1537758859,3,'normal','fa fa-circle-o','','file'),
(20,7,'user/scorelog','积分日志','',1,1541045799,1541050931,0,'normal','fa fa-file-text-o','','file'),
(21,20,'user/scorelog/index','View','',0,1541045799,1541050931,0,'normal','fa fa-circle-o','','file'),
(22,7,'user/recharge','充值余额','',1,1541045799,1541050931,0,'normal','fa fa-cny','','file'),
(23,22,'user/recharge/index','View','',0,1541045799,1541045799,8,'normal','fa fa-circle-o','','file'),
(24,7,'user/moneylog','余额日志','',1,1541045799,1541050931,0,'normal','fa fa-file-text-o','','file'),
(25,24,'user/moneylog/index','View','',0,1516015012,1541043105,7,'normal','fa fa-circle-o','','file'),
(26,7,'user/withdraw','余额提现','',1,1541045799,1541050931,0,'normal','fa fa-cny','','file'),
(27,26,'user/withdraw/index','View','',0,1541045799,1541045799,8,'normal','fa fa-circle-o','','file'),
(28,7,'user/withdrawlog','提现日志','',1,1541045799,1541050931,0,'normal','fa fa-file-text-o','','file'),
(29,28,'user/withdrawlog/index','View','',0,1516015012,1541043105,7,'normal','fa fa-circle-o','','file'),
(30,8,'user/invite','邀请好友','',1,1541045799,1541050931,0,'normal','fa fa-users','','file'),
(31,30,'user/invite/index','View','',0,1516015012,1541043105,7,'normal','fa fa-circle-o','','file'),
(32,8,'general/attachment','附件管理','',1,1541045799,1541050931,0,'normal','fa fa-file-image-o','','file'),
(33,32,'general/attachment/index','View','',0,1516015012,1541043105,7,'normal','fa fa-circle-o','','file'),
(34,32,'general/attachment/add','Add','',0,1516015012,1541043105,7,'normal','fa fa-circle-o','','file'),
(35,32,'general/attachment/edit','Edit','',0,1516015012,1541043105,7,'normal','fa fa-circle-o','','file'),
(36,32,'general/attachment/del','Del','',0,1516015012,1541043105,7,'normal','fa fa-circle-o','','file'),
(37,32,'general/attachment/select','Select','',0,1516015012,1541043105,7,'normal','fa fa-circle-o','','file'),
(99,32,'general/attachment/multi','Multi','',0,1516015012,1541043105,7,'normal','fa fa-circle-o','','file');

#
# 修改用户分组记录
#

REPLACE INTO `__PREFIX__user_group` (`id`,`name`,`rules`,`createtime`,`updatetime`,`status`) VALUES 
(1,'默认组','1,3,5,6,7,8,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,99',1515386468,1541065475,'normal');

#
# 增加用户日志表
#
CREATE TABLE IF NOT EXISTS `__PREFIX__user_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '管理员名字',
  `url` varchar(1500) NOT NULL DEFAULT '' COMMENT '操作页面',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '日志标题',
  `content` text NOT NULL COMMENT '内容',
  `ip` varchar(50) NOT NULL DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) NOT NULL DEFAULT '' COMMENT 'User-Agent',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  PRIMARY KEY (`id`),
  KEY `name` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=322 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户日志';

#
# 增加用户等级表
#
CREATE TABLE IF NOT EXISTS `__PREFIX__user_level` (
  `level_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `level_name` varchar(30) DEFAULT NULL COMMENT '头衔名称',
  `amount` decimal(10,2) DEFAULT NULL COMMENT '必要积分',
  `discount` smallint(4) DEFAULT '0' COMMENT '折扣',
  `describe` varchar(200) DEFAULT NULL COMMENT '头街描述',
  `level_img` varchar(150) DEFAULT NULL COMMENT '标识图片',
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='会员等级';

#
# 增加用户等级数据
#
INSERT INTO `__PREFIX__user_level` VALUES 
(1,'初级会员',0,90,'若如初相见，若如初相恋','/assets/img/level/lv-1.png'),
(2,'中级会员',100,80,'','/assets/img/level/lv-2.png'),
(3,'高级会员',1000,70,'','/assets/img/level/lv-3.png'),
(4,'荣誉会员',10000,60,'','/assets/img/level/lv-4.png');
COMMIT;