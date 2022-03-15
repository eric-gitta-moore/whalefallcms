SET FOREIGN_KEY_CHECKS=0;
CREATE TABLE IF NOT EXISTS `__PREFIX__adszone_zone`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '广告位名称',
  `mark` varchar(50) NOT NULL DEFAULT '' COMMENT '广告位标记',
  `type` enum('1','2','3') NOT NULL DEFAULT '1' COMMENT '广告位类型:1=图片广告,2=多图&幻灯广告,3=代码广告',
  `width` int(10) NOT NULL DEFAULT '0' COMMENT '广告宽度',
  `height` int(10) NOT NULL DEFAULT '0' COMMENT '广告高度',
  `createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `code` text NOT NULL COMMENT '广告代码',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '广告位管理' ROW_FORMAT = Compact;

DELETE FROM `__PREFIX__adszone_zone` WHERE (`id`<='5');
INSERT INTO `__PREFIX__adszone_zone` VALUES ('1', '顶部轮播图广告', 'slide_01', '2', '1920', '500', '1552380328', '1552898345', '');
INSERT INTO `__PREFIX__adszone_zone` VALUES ('2', '图片广告示例', 'image_01', '1', '640', '192', '1552380346', '1552986014', '');
INSERT INTO `__PREFIX__adszone_zone` VALUES ('3', '代码广告示例', 'adsCode_01', '3', '0', '0', '1552380399', '1552989119', '<div >\r\n	<p>我是代码广告位中，HTML标签中的文字。</p>\r\n	<button onclick=\"alert(\'广告代码中的按钮被点击了\')\">广告代码中的按钮</button>\r\n	<p>下面是一个img标签，用来显示图片：</p>\r\n	<p><img src=\"https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo_top_86d58ae1.png\" width=\"640\" /></p>\r\n</div>');
INSERT INTO `__PREFIX__adszone_zone` VALUES ('4', '演示轮播图广告2', 'slide_02', '2', '640', '192', '1552898865', '1552986002', '');
INSERT INTO `__PREFIX__adszone_zone` VALUES ('5', '图片矩阵广告', 'imgTable_01', '2', '225', '180', '1552899027', '1552903770', '');


CREATE TABLE IF NOT EXISTS `__PREFIX__adszone_ads`  (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `imageurl` varchar(255) NOT NULL DEFAULT '' COMMENT '图片地址',
  `linkurl` varchar(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `target` enum('_self','_blank') NOT NULL DEFAULT '_blank' COMMENT 'URL打开方式',
  `code` text NOT NULL COMMENT '广告代码',
  `expiretime` int(11) NOT NULL DEFAULT '0' COMMENT '到期时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `createtime` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `updatetime` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `zone_id` int(11) NOT NULL DEFAULT '0' COMMENT '广告位ID',
  `effectime` int(11) NOT NULL DEFAULT '0' COMMENT '生效时间',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '广告项目管理' ROW_FORMAT = Compact;

DELETE FROM `__PREFIX__adszone_ads` WHERE (`id`<='11');
INSERT INTO `__PREFIX__adszone_ads` VALUES ('1',  '顶部轮播图广告04', '/assets/addons/adszone/img/ea836cc23ff20bd005fa92ee405e214b.jpg', '#', '_blank', '', '1583983600', '1', '1552447609', '1552986080','1', '1552447600');
INSERT INTO `__PREFIX__adszone_ads` VALUES ('2',  '顶部轮播图广告03', '/assets/addons/adszone/img/2d0e68c86eea9a0dcc1e676e34121326.jpg', '#', '_blank', '', '1583983612', '2', '1552447625', '1552986073', '1','1552447612');
INSERT INTO `__PREFIX__adszone_ads` VALUES ('3',  '演示广告1','4', '/assets/addons/adszone/img/ea836cc23ff20bd005fa92ee405e214b.jpg', '#', '_blank', '', '1584434873', '3', '1552898892', '1552985566', '1552898873');
INSERT INTO `__PREFIX__adszone_ads` VALUES ('4',  '演示广告2', '/assets/addons/adszone/img/2d0e68c86eea9a0dcc1e676e34121326.jpg', '#', '_blank', '', '1584434894', '4', '1552898907', '1552985558','4', '1552898894');
INSERT INTO `__PREFIX__adszone_ads` VALUES ('5',  '图片矩阵1', '/assets/addons/adszone/img/dbe06c000032b03d42c9381c7a973f99.png', 'https://www.fastadmin.net/store/tablemake.html', '_blank', '', '1584435031', '5', '1552899045', '1552984226','5', '1552899031');
INSERT INTO `__PREFIX__adszone_ads` VALUES ('6',  '图片矩阵2', '/assets/addons/adszone/img/2839e5271ec86ea377c53ba1dc7b57f7.png', 'https://www.fastadmin.net/store/comment.html', '_blank', '', '1584435047', '6', '1552899056', '1552984244','5', '1552899047');
INSERT INTO `__PREFIX__adszone_ads` VALUES ('7',  '图片矩阵3', '/assets/addons/adszone/img/125a66671ac4f1dd23e29ef265f2e86a.png', 'https://www.fastadmin.net/store/cms.html', '_blank', '', '1584435057', '7', '1552899068', '1552984260','5', '1552899057');
INSERT INTO `__PREFIX__adszone_ads` VALUES ('8',  '图片矩阵4', '/assets/addons/adszone/img/ff16f3ca80087978883f26d5dbcf0f1b.png', 'https://www.fastadmin.net/store/ask.html', '_blank', '', '1584435069', '8', '1552899080', '1552984269','5', '1552899069');
INSERT INTO `__PREFIX__adszone_ads` VALUES ('9',  '顶部轮播图广告02', '/assets/addons/adszone/img/a6d88b55223919c27f696cd0f4e2c39d.jpg', '#', '_blank', '', '1584521477', '9', '1552985488', '1552986067', '1','1552985477');
INSERT INTO `__PREFIX__adszone_ads` VALUES ('10',  '顶部轮播图广告01', '/assets/addons/adszone/img/072c33d1451f254659515da013ecb21c.jpg', '#', '_blank', '', '1584521490', '10', '1552985499', '1552986061','1', '1552985490');
INSERT INTO `__PREFIX__adszone_ads` VALUES ('11',  '图片广告示例', '/assets/addons/adszone/img/2d0e68c86eea9a0dcc1e676e34121326.jpg', '#', '_blank', '', '1584525204', '11', '1552989214', '1552989214', '2','1552989204');

SET FOREIGN_KEY_CHECKS=1;

