<?php
//功能设置面板
function wp_youtu_control() {
	if($_POST['action'] == 'save' ) {
		$updatas['secretid'] = sanitize_text_field(trim($_POST['secretid']));
		$updatas['secretkey'] = sanitize_text_field(trim($_POST['secretkey']));
		$updatas['appid'] = intval(trim($_POST['appid']));
		$updatas['bucket'] = sanitize_text_field(trim($_POST['bucket']));
		$updatas['domain'] = sanitize_text_field(trim($_POST['domain']));
		$updatas['mode'] = sanitize_text_field($_POST['mode']);
		$updatas['direction'] = sanitize_text_field($_POST['direction']);
		$updatas['thumb_width'] = intval($_POST['thumb_width']);
		$updatas['thumb_height'] = intval($_POST['thumb_height']);
		
		$updatas = serialize($updatas);
		update_option('youtu_setting', $updatas);
		echo '<div id="message" class="updated fade">设置已保存！</div>';
	}
	if($_POST['action'] == 'upload' ) {
		if( wp_next_scheduled( 'youtu_sync_cron' ) ) {
			$sync_message = '同步图片任务正在执行，您无法操作，请稍候查看系统日志';
		} else {
			youtu_add_sync_cron();	
			$sync_message = '同步图片任务已开始执行，完成后会在系统日志中显示结果，执行时长视您的图片多少，请稍候再查看日志。';
		}
		
		if($sync_message)
			echo '<div id="message" class="updated fade">' . $sync_message . '</div>';
	}
	
	$setting = get_option('youtu_setting');
	$setting = unserialize($setting);
?>
<style type="text/css">
#message {
	margin: 1em 0;
	padding: .5em;
}
#youtu-setting, #youtu-upload {
	margin-bottom: 20px;
	padding: 10px;
	background-color: #ffffff;
	border: 1px solid #e6e6e6;
	box-shadow: 0 0 3px #e3e3e3;
}
</style>
<div class="wrap">
	<div id="youtu-setting">
		<p>WordPress主机，选野人，<a href="http://www.yercms.com" target="_blank">野人建站</a>，专注于WordPress企业主题制作。</p>
		<form method="post" action="">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="secretid">Secret ID</label></th>
						<td><input size="35" type="text" id="secretid" name="secretid" value="<?php echo $setting['secretid']; ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="secretkey">Secret Key</label></th>
						<td><input size="35" type="text" id="secretkey" name="secretkey" value="<?php echo $setting['secretkey']; ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="appid">项目ID</label></th>
						<td><input size="35" type="text" id="appid" name="appid" value="<?php echo $setting['appid']; ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="bucket">空间名称</label></th>
						<td><input size="35" type="text" id="bucket" name="bucket" value="<?php echo $setting['bucket']; ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="domain">绑定的域名</label></th>
						<td>
							<input size="35" type="text" id="domain" name="domain" value="<?php echo $setting['domain']; ?>" />
							<p class="description">例如：pic.yercms.com，不要输入http://</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="mode">缩略图模式</label></th>
						<td>
							<select id="mode" name="mode">
								<option value="crop"<?php if($setting['mode'] == 'crop') echo ' selected="selected"'; ?>>裁剪</option>
								<option value="view"<?php if($setting['mode'] == 'view') echo ' selected="selected"'; ?>>缩放</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="direction">裁剪位置</label></th>
						<td>
							<select id="direction" name="direction">
								<option value="NorthWest"<?php if($setting['direction'] == 'NorthWest') echo ' selected="selected"'; ?>>左上(西北)</option>
								<option value="North"<?php if($setting['direction'] == 'North') echo ' selected="selected"'; ?>>中上(正北)</option>
								<option value="NorthEast"<?php if($setting['direction'] == 'NorthEast') echo ' selected="selected"'; ?>>右上(东北)</option>
								<option value="West"<?php if($setting['direction'] == 'West') echo ' selected="selected"'; ?>>左中(正西)</option>
								<option value="Center"<?php if($setting['direction'] == 'Center') echo ' selected="selected"'; ?>>居中</option>
								<option value="East"<?php if($setting['direction'] == 'East') echo ' selected="selected"'; ?>>右中(正东)</option>
								<option value="SouthWest"<?php if($setting['direction'] == 'SouthWest') echo ' selected="selected"'; ?>>左下(西南)</option>
								<option value="South"<?php if($setting['direction'] == 'South') echo ' selected="selected"'; ?>>中下(正南)</option>
								<option value="SouthEast"<?php if($setting['direction'] == 'SouthEast') echo ' selected="selected"'; ?>>右下(东南)</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="thumb_width">缩略图尺寸</label></th>
						<td>
							<label for="thumb_width">宽：</label><input size="15" type="text" id="thumb_width" name="thumb_width" value="<?php echo $setting['thumb_width']; ?>" /><br />
							<label for="thumb_height">高：</label><input size="15" type="text" id="thumb_height" name="thumb_height" value="<?php echo $setting['thumb_height']; ?>" />
							<p class="description">一般不建议设置，这里的设置将覆盖WordPress默认的缩略图尺寸</p>
						</td>
					</tr>
				</tbody>
			</table>
			<p><input class="button-primary" type="submit" value="保存设置" /><input type="hidden" name="action" value="save" /></p>
		</form>
	</div>
	<div id="youtu-upload">
		<p>同步图片功能只会将本地图片上传到腾讯万象优图，不会影响本地数据，初次使用本插件需要执行一次同步图片操作，以便上传旧数据，以后如果您发现本地图片有未上传到万象优图的，也可以执行同步图片操作。</p>
		<p>本操作不会覆盖万象优图中已有的图片，仅为增量上传，如果需要替换万象优图上的图片，请先在图片管理中删除相应的图片再同步图片。</p>
		<p>本操作需要依赖WordPress的WP Cron定时任务功能，请不要禁用该功能。</p>
		<form action="" method="post">
			<input class="button" type="submit" value="同步图片" /><input type="hidden" name="action" value="upload" />
		</form>
	</div>
</div>

<?php
}
?>