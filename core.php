<?php  
$wp_quicken = get_option('wp_quicken');
$wp_quicken = unserialize($wp_quicken);

//禁用Google Open Sans字体
if($wp_quicken['open_sans'] == 'yes') {
	if (!function_exists('remove_wp_open_sans') ) {
		function remove_wp_open_sans() {
			wp_deregister_style( 'open-sans' );
			wp_register_style( 'open-sans', false );
		}
		// 前台删除Google字体CSS
		add_action('wp_enqueue_scripts', 'remove_wp_open_sans');
		// 后台删除Google字体CSS
		add_action('admin_enqueue_scripts', 'remove_wp_open_sans');
	}
}

//禁用emoji表情
if($wp_quicken['emoji'] == 'yes') {
	if (!function_exists('disable_emojis') ) {
		function disable_emojis() {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );    
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );  
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		}
		add_action( 'init', 'disable_emojis' );
	}
}

//开启友情链接管理	
if($wp_quicken['link_manage'] == 'yes')
	add_filter( 'pre_option_link_manager_enabled', '__return_true' );

//关闭顶部工具条	
if($wp_quicken['admin_bar'] == 'yes')
	add_filter( 'show_admin_bar', '__return_false' );

//禁止向站内链接发送 PingBack 引用通告
if($wp_quicken['pingback'] == 'yes') {
	if (!function_exists('no_self_ping') ) {
		function no_self_ping( &$links ) {
			$home = get_option( 'home' );
			foreach ( $links as $l => $link )
				if ( 0 === strpos( $link, $home ) )
					unset($links[$l]);
		}
		add_action( 'pre_ping', 'no_self_ping' );
	}
}

//移除 XML-RPC
if($wp_quicken['xml_rpc'] == 'yes')
	remove_action('wp_head', 'rsd_link');

//禁用 XML-RPC 接口
if($wp_quicken['xml_rpc_api'] == 'yes')
	add_filter('xmlrpc_enabled', '__return_false');

//移除 Windows Live Writer
if($wp_quicken['windows_live_writer'] == 'yes')
	remove_action('wp_head', 'wlwmanifest_link');

//移除前台head标签中显示WP版本号
if($wp_quicken['wp_version'] == 'yes')
	remove_action('wp_head', 'wp_generator');

//禁用 auto-embeds
if($wp_quicken['auto_embeds'] == 'yes')
	remove_filter( 'the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );

//移除短链接
if($wp_quicken['wp_shortlink'] == 'yes')
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

//移除后台登录错误提示
if($wp_quicken['login_errors'] == 'yes')
	add_filter('login_errors', create_function('$a', 'return null;'));

//删除仪表盘模块
if($wp_quicken['dashboard_activity'] == 'yes' || $wp_quicken['dashboard_primary'] == 'yes') {
	if (!function_exists('remove_dashboard_widgets') ) {
		function remove_dashboard_widgets() {
			global $wp_meta_boxes, $wp_quicken;
			
			//删除 "活动" 模块
			if($wp_quicken['dashboard_activity'] == 'yes')
				unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
			
			//删除 "WordPress 新闻" 模块
			if($wp_quicken['dashboard_primary'] == 'yes')
				unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
			
		}
		add_action('wp_dashboard_setup', 'remove_dashboard_widgets' );
	}
}

//删除仪表盘欢迎模块
if($wp_quicken['welcome_panel'] == 'yes')
	remove_action('welcome_panel', 'wp_welcome_panel');

//删除文章编辑页面模块
if($wp_quicken['post_trackbacksdiv'] == 'yes' || $wp_quicken['post_postcustom'] == 'yes') {
	if (!function_exists('remove_meta_boxes') ) {
		function remove_meta_boxes() {
			global $wp_quicken;
			if($wp_quicken['post_trackbacksdiv'] == 'yes')
				remove_meta_box('trackbacksdiv','post','normal');
			if($wp_quicken['post_postcustom'] == 'yes')
				remove_meta_box('postcustom','post','normal');
		}
		add_action('admin_init', 'remove_meta_boxes');
	}
}

//移除WP自动修正大小写
if($wp_quicken['capital_P_dangit'] == 'yes') {
	remove_filter( 'the_content', 'capital_P_dangit' );
	remove_filter( 'the_title', 'capital_P_dangit' );
	remove_filter( 'comment_text', 'capital_P_dangit' );
}

//禁止WP文章修订和自动保存
if($wp_quicken['wp_save_post'] == 'yes') {
	if (!function_exists('disable_post_auto_save') ) {  
		function disable_post_auto_save(){   
			wp_deregister_script('autosave');   
		}
		remove_action('pre_post_update', 'wp_save_post_revision' );
		add_action( 'wp_print_scripts', 'disable_post_auto_save' );
	}
}

//禁止前台加载语言包
if($wp_quicken['no_lang'] == 'yes') {
	if (!function_exists('no_lang') ) { 
		function no_lang($locale) {
			$locale = ( is_admin() ) ? $locale : 'en_US';
			return $locale;
		}
		add_filter( 'locale', 'no_lang' );
	}
}

//禁止定时任务
if($wp_quicken['no_cron'] == 'yes')
	remove_action( 'init', 'wp_cron' );

//将Gravatar头像替换为多说头像
if($wp_quicken['gravatar'] == 'yes') {
	if (!function_exists('duoshuo_avatar') ) {  
		function duoshuo_avatar($avatar) {
			$avatar = str_replace(array('www.gravatar.com', '0.gravatar.com', '1.gravatar.com', '2.gravatar.com'), 'gravatar.duoshuo.com', $avatar);
			return $avatar;
		}
		add_filter( 'get_avatar', 'duoshuo_avatar', 10, 3 );
	}
}

?>