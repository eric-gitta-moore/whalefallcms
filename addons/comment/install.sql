
-- ----------------------------
-- Table structure for fa_comment_article
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__comment_article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_id` int(10) DEFAULT NULL COMMENT '站点ID',
  `key` varchar(50) DEFAULT NULL COMMENT '唯一标识',
  `title` varchar(255) DEFAULT NULL COMMENT '文章标题',
  `url` varchar(255) DEFAULT NULL COMMENT '文章URL',
  `createtime` int(10) DEFAULT NULL COMMENT '添加时间',
  `comments` int(10) unsigned DEFAULT '0' COMMENT '评论数',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `status` enum('normal','hidden') DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='评论文章表';
INSERT INTO `__PREFIX__comment_article`(`id`, `site_id`, `key`, `title`, `url`, `createtime`, `comments`, `updatetime`, `status`) VALUES (1, 1, '1', 'FastAdmin社会化评论整合插件', 'https://www.fastadmin.net/store/comment.html', 1537661340, 1, 1537661340, 'normal');

-- ----------------------------
-- Table structure for fa_comment_like
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__comment_like` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) DEFAULT NULL COMMENT '会员ID',
  `site_id` int(10) DEFAULT NULL COMMENT '站点ID',
  `article_id` int(10) DEFAULT NULL COMMENT '文档ID',
  `post_id` int(10) DEFAULT NULL COMMENT '评论ID',
  `ip` varchar(50) DEFAULT NULL COMMENT '评论IP',
  `createtime` int(10) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`,`article_id`,`post_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评论点赞表';

-- ----------------------------
-- Table structure for fa_comment_post
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__comment_post` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) DEFAULT NULL COMMENT '会员ID',
  `article_id` int(10) DEFAULT NULL COMMENT '文章ID',
  `pid` int(10) DEFAULT NULL COMMENT '父评论ID',
  `site_id` int(10) DEFAULT NULL COMMENT '站点ID',
  `content` text COMMENT '评论内容',
  `ip` varchar(50) DEFAULT NULL COMMENT 'IP',
  `useragent` varchar(255) DEFAULT NULL COMMENT 'UserAgent',
  `comments` int(10) unsigned DEFAULT '0' COMMENT '回复次数',
  `likes` int(10) unsigned DEFAULT '0' COMMENT '点赞次数',
  `dislikes` int(10) unsigned DEFAULT '0' COMMENT '点踩次数',
  `createtime` int(10) DEFAULT NULL COMMENT '添加时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `deletetime` int(10) DEFAULT NULL COMMENT '删除时间',
  `status` enum('normal','hidden','deleted') DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`,`article_id`,`pid`) USING BTREE,
  KEY `pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='评论内容表';
INSERT INTO `__PREFIX__comment_post`(`id`, `user_id`, `article_id`, `pid`, `site_id`, `content`, `ip`, `useragent`, `comments`, `likes`, `dislikes`, `createtime`, `updatetime`, `deletetime`, `status`) VALUES (1, 1, 1, 0, 1, '这是第一篇评论内容', '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36', 0, 0, 0, 1537661340, 1537661340, NULL, 'normal');

-- ----------------------------
-- Table structure for fa_comment_report
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__comment_report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) DEFAULT NULL COMMENT '会员ID',
  `site_id` int(10) DEFAULT NULL COMMENT '站点ID',
  `article_id` int(10) DEFAULT NULL COMMENT '文档ID',
  `post_id` int(10) DEFAULT NULL COMMENT '评论ID',
  `type` int(10) DEFAULT NULL COMMENT '举报类型',
  `content` varchar(255) DEFAULT NULL COMMENT '举报原因',
  `ip` varchar(50) DEFAULT NULL COMMENT '反馈IP',
  `createtime` int(10) DEFAULT NULL COMMENT '添加时间',
  `status` enum('settled','unsettled') DEFAULT 'unsettled' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`,`article_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评论举报表';

-- ----------------------------
-- Table structure for fa_comment_site
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__comment_site` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) DEFAULT NULL COMMENT '站点名称',
  `title` varchar(100) DEFAULT NULL COMMENT '站点标题',
  `website` varchar(100) DEFAULT NULL COMMENT '站点URL',
  `createtime` int(10) DEFAULT NULL COMMENT '添加时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `status` enum('normal','hidden') DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='评论站点表';
INSERT INTO `__PREFIX__comment_site`(`id`, `name`, `title`, `website`, `createtime`, `updatetime`, `status`) VALUES (1, 'test', '测试站点', 'http://www.test.com', 1500000000, 1500000000, 'normal');