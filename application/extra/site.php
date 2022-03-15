<?php
/**
* 云凌鲸落小说漫画聚合分销CMS系统
* @Author Curtis - 云凌工作室
* @Website http://www.whalefallcms.com
* @Datetime 2020/4/8 下午 05:07
*/

return array (
  'name' => '鲸落CMS',
  'beian' => '',
  'cdnurl' => '',
  'version' => '1.0.1',
  'timezone' => 'Asia/Shanghai',
  'forbiddenip' => '',
  'languages' => 
  array (
    'backend' => 'zh-cn',
    'frontend' => 'zh-cn',
    'userend' => 'zh-cn',
  ),
  'fixedpage' => 'dashboard',
  'categorytype' => 
  array (
    'default' => 'Default',
    'page' => 'Page',
    'article' => 'Article',
    'test' => 'Test',
  ),
  'configgroup' => 
  array (
    'basic' => 'Basic',
    'email' => 'Email',
    'dictionary' => 'Dictionary',
    'user' => 'User',
    'template' => 'Template',
    'seo' => 'SEO',
    'pageset' => 'PageSet',
    'reward' => 'Reward',
    'contentSet' => 'ContentSet',
    'api' => 'API',
    'rank' => 'Rank',
    'performance' => 'Performance',
    'autoTask' => 'AutoTask',
  ),
  'mail_type' => '1',
  'mail_smtp_host' => 'smtp.qq.com',
  'mail_smtp_port' => '465',
  'mail_smtp_user' => '10000',
  'mail_smtp_pass' => 'password',
  'mail_verify_type' => '2',
  'mail_from' => '10000@qq.com',
  'pc_tpl' => 'default',
  'mobile_tpl' => 'default',
  'mip_tpl' => 'default',
  'index_seo_title' => '{site_name}-漫画大全,漫画连载,漫画在线观看,最新小说下载,免费小说阅读,恐怖漫画全集,言情小说,青春小说,原创网络,鲸落CMS文学',
  'index_seo_keywords' => '漫画大全,漫画连载,漫画在线观看,最新小说下载,免费小说阅读,恐怖漫画全集,言情小说,青春小说,原创网络文学,{site_name}',
  'index_seo_description' => '{site_name}提供漫画大全,漫画连载,漫画在线观看,最新小说下载,免费小说阅读,恐怖漫画全集,言情小说,青春小说,原创网络文学',
  'seo_tips' => '',
  'query_cache' => '900',
  'user_sign_update_score' => '1',
  'center_notice' => '用户中心公告',
  'help_faq' => 
  array (
    '如何充值金币？' => '答：在“我的”界面点击“金币余额”，点击“我要充值”按钮进行充值；或直接点击“充值送金币”进行充值；充值后的金币不可兑换为现金，不予退款。',
    '本平台怎么收费？' => '答：目前平台上的书籍分为两种，一种是免费阅读，一种是充值阅读。',
    '如何从书架中清除不想看的书？' => '答：点书架右上角“管理”，可以删除不想看的书。',
    '为什么我正在看的书看不了了？' => '答：可能您所看的书，在某章出现故障，此情况可联系客服处理。也可能涉及敏感话题或者其它问题被暂时下架了，下架的书不能阅读。',
    '所有书籍都是正版的吗？' => '答：本平台始终致力于支持正版内容的引入及合作，平台上所有书籍都是正版。',
  ),
  'common_kefu_qq' => '1462066778',
  'common_kefu_wx' => 'c1462066778',
  'common_kefu_qq_qr' => '/assets/img/qrcode.png',
  'common_kefu_wx_qr' => '/assets/img/qrcode.png',
  'help_tips' => '在线时间：周一至周六9:00-18:00',
  'domain' => 'http://cartoon.cc/',
  'score_name' => '书币',
  'reward_gift' => 
  array (
    '第1张图片书币' => '100',
    '第2张图片书币' => '388',
    '第3张图片书币' => '588',
    '第4张图片书币' => '888',
  ),
  'gift_images' => 
  array (
    0 => '/uploads/20200326/79c67377f3c156a6e720592d8a8eb40c.jpg',
    1 => '/uploads/20200326/d7b5ea847e0b54d780b5d928f264227e.jpg',
    2 => '/uploads/20200326/09cd043ca5673e9248eb990359a65cb6.jpg',
    3 => '/uploads/20200326/ea01e804533d448bbb9013da5f1798ff.jpg',
  ),
  'readme' => '',
  'tip' => '土豪，求打赏',
  'api_reward_num' => '5',
  'group_cartoon_vip_id' => '4',
  'group_novel_vip_id' => '3',
  'group_listen_vip_id' => '2',
  'group_all_vip_id' => '5',
  'loading_image' => '/uploads/20200328/bffe34ead07d5a0dd7edb6faa3572dbb.gif',
  'rank_boy_cate' => '1',
  'rank_girl_cate' => '1',
  'quick_login_qq' => '1',
  'quick_login_wx' => '1',
  'quick_login_weibo' => '1',
  'api_postbot_pwd' => '123456',
  'img_cdn_url' => 'http://s.cartoon.cc/',
  'put_off_update_view_number' => '0',
  'put_off_update_like_number' => '0',
  'put_off_update_collection' => '0',
  'put_off_update_buy_number' => '0',
  'group_agent_id' => '6',
  'novel_need_pay_cut_length' => '200',
  'novel_need_pay_show_html' => '<p>更多内容尽在VIP，欢迎您加入VIP大家庭</p>',
  'cartoon_need_pay_show_html' => '<p>更多内容尽在VIP，欢迎您加入VIP大家庭</p>',
  'auto_task_period' => '3600',
  'cartoon_book_seo_title' => '{book_name}-{book_author_name}-{book_cate,,},漫画大全,漫画连载,漫画在线观看,最新小说下载,免费小说阅读,恐怖漫画全集,言情小说,青春小说,原创网络文学-{site_name}',
  'cartoon_book_seo_keywords' => '{book_cate,,},漫画大全,漫画连载,漫画在线观看,最新小说下载,免费小说阅读,恐怖漫画全集,言情小说,青春小说,原创网络文学,{site_name}',
  'cartoon_book_seo_description' => '{book_summary,200}',
  'cartoon_chapter_seo_title' => '{chapter_name}-{book_name}-{book_author_name}-{book_cate,,},漫画大全,漫画连载,漫画在线观看,最新小说下载,免费小说阅读,恐怖漫画全集,言情小说,青春小说,原创网络文学-{site_name}',
  'cartoon_chapter_seo_keywords' => '{chapter_name},{book_name},{book_author_name},{book_cate,,},漫画大全,漫画连载,漫画在线观看,最新小说下载,免费小说阅读,恐怖漫画全集,言情小说,青春小说,原创网络文学-{site_name}',
  'cartoon_chapter_seo_description' => '{book_summary,200}',
  'cartoon_cate_seo_title' => '{cate_name}-{cate_end}-{cate_attribute},漫画大全,漫画连载,漫画在线观看,最新小说下载,免费小说阅读,恐怖漫画全集,言情小说,青春小说,原创网络文学--{site_name}',
  'cartoon_cate_seo_keywords' => '{cate_name},{cate_end},{cate_attribute},漫画大全,漫画连载,漫画在线观看,最新小说下载,免费小说阅读,恐怖漫画全集,言情小说,青春小说,原创网络文学--{site_name}',
  'cartoon_cate_seo_description' => '{cate_name},{cate_end},{cate_attribute},漫画大全,漫画连载,漫画在线观看,最新小说下载,免费小说阅读,恐怖漫画全集,言情小说,青春小说,原创网络文学--{site_name}',
  'novel_book_seo_title' => '{chapter_name}-{book_name}-{book_author_name}-{book_cate,,},漫画大全,漫画连载,漫画在线观看,最新小说下载,免费小说阅读,恐怖漫画全集,言情小说,青春小说,原创网络文学-{site_name}',
  'novel_book_seo_keywords' => '{chapter_name},{book_name},{book_author_name},{book_cate,,},漫画大全,漫画连载,漫画在线观看,最新小说下载,免费小说阅读,恐怖漫画全集,言情小说,青春小说,原创网络文学-{site_name}',
  'novel_book_seo_description' => '{book_summary,200}',
  'seo_header' => '<!-- Powered By Curtis -->',
  'analyse_code' => '<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?9896a3c230acf36516d940c89b7a9fc1";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>',
);