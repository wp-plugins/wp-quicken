<?php  
/*
Plugin Name: WordPress加速优化
Plugin URI: http://www.yercms.com/1828.html
Description: 通过禁用Google字体、emoji表情等使WordPress速度更快，更符合国人使用。
Version: 1.0.0
Author: 野人建站
Author URI: http://www.yercms.com/
*/
require_once('core.php');
function wp_quicken_control() {
	$options = array (
		array('var_name' => 'open_sans', 'label_name' => '禁用 Google Open Sans 字体', 'button' => '描述 &raquo;', 'dsc' => '由于大陆普遍无法访问谷歌服务器，谷歌字体会严重拖慢网站速度，强烈推荐勾选此项'),
		array('var_name' => 'emoji', 'label_name' => '禁用 emoji 表情', 'button' => '描述 &raquo;', 'dsc' => '海外资源大陆加载比较慢，影响网站访问速度，强烈推荐勾选此项'),
		array('var_name' => 'link_manage', 'label_name' => '开启友情链接管理', 'button' => '描述 &raquo;', 'dsc' => '在WordPress后台左侧菜单上显示“链接”，如果已显示，则不用勾选此项'),
		array('var_name' => 'admin_bar', 'label_name' => '关闭前台顶部工具条'),
		array('var_name' => 'pingback', 'label_name' => '禁止向站内链接发送 PingBack 引用通告', 'button' => '描述 &raquo;', 'dsc' => '向站内链接发送PingBack并没有意义，本功能不会影响向站外发送PingBack'),
		array('var_name' => 'xml_rpc', 'label_name' => '移除 XML-RPC', 'button' => '描述 &raquo;', 'dsc' => '移除前台RSD标签'),
		array('var_name' => 'xml_rpc_api', 'label_name' => '禁用 XML-RPC 接口', 'button' => '描述 &raquo;', 'dsc' => 'XML-RPC接口使得第三方的博客写作软件(如Windows Live Writer等)可以与之通信来发布和修改博客，国内用户使用比较少'),
		array('var_name' => 'windows_live_writer', 'label_name' => '移除 Windows Live Writer', 'button' => '描述 &raquo;', 'dsc' => '一般情况下我们都没有使用Windows Live Writer来发布文章'),
		array('var_name' => 'wp_version', 'label_name' => '移除前台head标签中显示WP版本号', 'button' => '描述 &raquo;', 'dsc' => '防止一些黑客扫描特定WordPress版本，利用该版本漏洞发起攻击'),
		array('var_name' => 'auto_embeds', 'label_name' => '禁用 auto-embeds', 'button' => '描述 &raquo;', 'dsc' => 'WordPress Easy Embeds使得在日志中输入一个视频网站或者图片分享的 URL，这个 URL 里面含有的视频或者图片就自动显示出来，但支持的都是国外网站，国内网站意义不大。'),
		array('var_name' => 'wp_shortlink', 'label_name' => '移除短链接', 'button' => '描述 &raquo;', 'dsc' => '短链接就是WordPress默认的固定链接方式，如果自定义了固定链接，就不应该在网站的任何地方出现短链接，以免影响SEO'),
		array('var_name' => 'login_errors', 'label_name' => '移除后台登录错误提示', 'button' => '描述 &raquo;', 'dsc' => '防止登录错误提示被黑客利用'),
		array('var_name' => 'dashboard_activity', 'label_name' => '删除仪表盘“活动”模块'),
		array('var_name' => 'dashboard_primary', 'label_name' => '删除仪表盘“WordPress 新闻”模块', 'button' => '描述 &raquo;', 'dsc' => '该模块在仪表盘显示WordPress官方新闻资讯，引用海外资源影响站点打开速度'),
		array('var_name' => 'welcome_panel', 'label_name' => '删除仪表盘欢迎模块'),
		array('var_name' => 'post_trackbacksdiv', 'label_name' => '删除文章编辑页面 trackback 模块', 'button' => '描述 &raquo;', 'dsc' => '发表文章页面的TrackBack模块，不常用功能，可以选择隐藏'),
		array('var_name' => 'post_postcustom', 'label_name' => '删除文章编辑页面自定义字段模块', 'button' => '描述 &raquo;', 'dsc' => '发表文章页面的自定义字段模块，不常用功能，可以选择隐藏'),
		array('var_name' => 'capital_P_dangit', 'label_name' => '移除WP自动修正大小写', 'button' => '描述 &raquo;', 'dsc' => '非英文站点自动修正大小写功能没有意义，可选择关闭'),
		array('var_name' => 'wp_save_post', 'label_name' => '禁用WP文章修订版和自动保存', 'button' => '描述 &raquo;', 'dsc' => '自动保存影响写文章的速度，修订版还很浪费数据库，如果觉得烦人就关闭吧！'),
		array('var_name' => 'no_lang', 'label_name' => '禁止前台加载语言包', 'button' => '描述 &raquo;', 'dsc' => '大多数中文主题都直接在主题里写中文了，没有必要加载语言包'),
		array('var_name' => 'no_cron', 'label_name' => '禁用WP Cron', 'button' => '描述 &raquo;', 'dsc' => 'WordPress定时任务功能在页面每次被访问时都会加载wp-cron.php文件，非常占用资源，关闭此功能后为了保证定时任务顺利执行，可在监控宝添加监控这个页面：' . home_url('wp-cron.php')),
		array('var_name' => 'gravatar', 'label_name' => '将Gravatar头像替换为多说头像', 'button' => '描述 &raquo;', 'dsc' => '海外资源大陆加载比较慢，影响网站访问速度，强烈推荐勾选此项'),
	);
	if($_POST['save'] == 'save' ) {
		$form_data = $_POST['wp_quicken'];
		if($form_data) {
			foreach($form_data as $data){
				$updatas[$data] = 'yes';
			}
			$updatas = serialize($updatas);
			update_option('wp_quicken', $updatas);
		}
		echo '<div id="message" class="updated fade">设置已保存！</div>';
	}
?>
<style type="text/css">
#wp-quicken-option {
	margin-left: 2em;
	line-height: 1.7;
}
#wp-quicken-option label {
	position: relative;
	display: block;
}
#wp-quicken-option span.dsc {
	display: none;
	margin-left: 2em;
	color: #ff9900;
}
#wp-quicken-option span.but {
	color: #0000ff;
	text-decoration: underline;
	margin-left: .5em;
}
</style>
<script type="text/javascript">
	jQuery(document).ready(function(){
		var checkall = jQuery('#checkall');
		var option = jQuery('#wp-quicken-option input[name="wp_quicken[]"]');
		checkall.click(function(){
			if(jQuery(this).attr('checked') == 'checked') {
				option.attr('checked', 'checked');
			} else {
				option.removeAttr('checked');
			}
		});
		function is_check(){
			var check = true;
			for(var i=0; i<option.length; i++){
				if(option.eq(i).attr('checked') != 'checked')
					check = false;
			}
			return check;
		}
		option.click(function(){
			if(is_check()) {
				checkall.attr('checked', 'checked');
			} else {
				checkall.removeAttr('checked');
			}
		});
		if(is_check())
			checkall.attr('checked', 'checked');
		jQuery('.but').click(function(){
			var dsc = jQuery(this).parent().find('.dsc');
			if(dsc.css('display') == 'none') {
				dsc.slideDown();
				dsc.css('display', 'block');
			} else {
				dsc.slideUp();
			}
			return false;
		});
	});
</script>
<div class="wrap">
	<h2>WordPress加速优化</h2>
	<p>您可以终身免费使用本插件，并且插件会持续更新升级，如果觉得好用请多多关注<a href="http://www.yercms.com" target="_blank">野人建站</a></p>
	<form method="post" action="">
		<div id="wp-quicken-option">
			<fieldset>
				<label for="checkall"><input type="checkbox" id="checkall" name="checkall" value="yes" /> 全选</label>
				<?php
					$wp_quicken = get_option('wp_quicken');
					$wp_quicken = unserialize($wp_quicken);
					foreach($options as $val){
						$checked = $dsc = $but = '';
						if($wp_quicken[$val['var_name']] == 'yes') $checked = ' checked="checked"';
						if($val['dsc']) $dsc = '<span class="dsc">' . $val['dsc'] . '</span>';
						if($val['button']) $but = '<span class="but">' . $val['button'] . '</span>';
						echo '<label for="' . $val['var_name'] . '">
								<input type="checkbox" id="' . $val['var_name'] . '" name="wp_quicken[]" value="' . $val['var_name'] . '"' . $checked . ' /> '
								. $val['label_name'] . $but . $dsc . '</label>';
					}
				?>
			</fieldset>
			<p><input class="button-primary" type="submit" value="保存设置" /><input type="hidden" name="save" value="save" /></p>
		</div>
	</form>
</div>

<?php
} 
//添加菜单
function wp_quicken_menu() {
	if (function_exists('add_options_page') )
		add_options_page('WordPress加速优化', '加速优化', 'administrator', 'wp-quicken', 'wp_quicken_control');
}
add_action('admin_menu', 'wp_quicken_menu');

function wp_quicken_settings_link($action_links, $plugin_file) {
	if($plugin_file == plugin_basename(__FILE__) ){
		$wp_quicken_settings_link = '<a href="options-general.php?page=wp-quicken">设置</a>';
		array_unshift($action_links, $wp_quicken_settings_link);
	}
	return $action_links;
}
add_filter('plugin_action_links', 'wp_quicken_settings_link', 10, 2);
?>