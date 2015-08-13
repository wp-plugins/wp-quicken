<?php
/*
Plugin Name: 腾讯万象优图
Plugin URI: http://www.yercms.com/1865.html
Description: WordPress图片同步到腾讯万象优图，用户访问时加载腾讯万象优图服务器上图片，有效提高WordPress站点访问速度，减轻服务器压力。
Version: 2.1.0
Author: 野人建站
Author URI: http://www.yercms.com/
*/
require_once('func.php');
require_once('setting.php');
require_once('manage.php');
require_once('other.php');
require_once('log.php');

//创建数据表
function youtu_table_install() {
	global $wpdb;
	
	$admin_dir = str_replace(home_url() . '/', '', admin_url());
	require_once(ABSPATH . $admin_dir . '/includes/upgrade.php');
	$table_img = $wpdb->prefix . YOUTU_IMGTABLE;
	if($wpdb->get_var("show tables like $table_img") != $table_img) {
		$sql_img = 'CREATE TABLE IF NOT EXISTS `' . $table_img . '` (
			`ID` bigint(20) UNSIGNED NULL AUTO_INCREMENT,
			`img_id` varchar(255) NOT NULL,
			`url` varchar(255) NOT NULL,
			UNIQUE KEY `id` (`ID`)
			) CHARSET=utf8;';
		dbDelta($sql_img);
	}
	$table_log = $wpdb->prefix . YOUTU_LOGTABLE;
	if($wpdb->get_var("show tables like $table_log") != $table_log) {
		$sql_log = 'CREATE TABLE IF NOT EXISTS `' . $table_log . '` (
			`ID` bigint(20) UNSIGNED NULL AUTO_INCREMENT,
			`datetime` datetime DEFAULT "0000-00-00 00:00:00" NOT NULL,
			`log` longtext NOT NULL,
			UNIQUE KEY `id` (`ID`)
			) CHARSET=utf8;';
		dbDelta($sql_log);
	}
}
register_activation_hook(__FILE__, 'youtu_table_install');

register_deactivation_hook( __FILE__, 'youtu_delete_sync_cron' );
register_uninstall_hook( __FILE__, 'youtu_delete_sync_cron' );

//添加菜单
function youtu_menu() {
	add_menu_page( '万象优图', '万象优图', 'administrator', 'wp-youtu', 'wp_youtu_control', 'dashicons-format-gallery');
	add_submenu_page( 'wp-youtu', '万象优图接口设置', '接口设置', 'administrator', 'wp-youtu', 'wp_youtu_control');
	add_submenu_page( 'wp-youtu', '图片管理', '图片管理', 'administrator', 'youtu-manage', 'wp_youtu_manage');
	add_submenu_page( 'wp-youtu', '优图直传', '优图直传', 'administrator', 'youtu-other', 'wp_youtu_other');
	add_submenu_page( 'wp-youtu', '系统日志', '系统日志', 'administrator', 'youtu-log', 'wp_youtu_log');
}
add_action('admin_menu', 'youtu_menu');

function youtu_settings_link($action_links, $plugin_file) {
	if($plugin_file == plugin_basename(__FILE__) ){
		$youtu_settings_link = '<a href="' . admin_url('admin.php?page=wp-youtu') . '">设置</a>';
		array_unshift($action_links, $youtu_settings_link);
	}
	return $action_links;
}
add_filter('plugin_action_links', 'youtu_settings_link', 10, 2);
?>