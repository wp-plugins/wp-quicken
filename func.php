<?php
require_once('conf.php');
require_once('Http.php');
//生成签名
function youtu_appSign($fileid='', $one=false) {
	$expiredTime = time() + 3600;
	if($one) $expiredTime = 0;
	$plainText = 'a=' . YOUTU_APPID . '&b=' . YOUTU_BUCKET . '&k=' . YOUTU_SECRETID . '&e=' . $expiredTime . '&t=' . time() . '&r=' . rand() . '&u=0&f=' . $fileid;
	$bin = hash_hmac('SHA1', $plainText, YOUTU_SECRETKEY, true);
	$bin = $bin.$plainText;        
	$sign = base64_encode($bin);
	return $sign;	
}
//上传图片
function youtu_upload($fileid, $filepath) {
	if ( !is_file($filepath) || !$fileid )
		return array('message'=>'图片路径或ID为空');
	
	$url = 'http://web.image.myqcloud.com/photos/v2/' . YOUTU_APPID . '/' . YOUTU_BUCKET . '/0/' . $fileid;
	$sign = youtu_appSign($fileid);
	
	$data['FileContent'] = '@'.$filepath;
	$data['Md5'] = md5_file($filepath);
	$args = array(
		'url' => $url,
		'method' => 'post',
		'timeout' => 10,
		'data' => $data,
		'header' => array(
			'Authorization:QCloud '.$sign,
		),
	);
	$Http = new YouTu_Http;
	$res = $Http->send($args);
	$ret = json_decode($res, true);
	return $ret;
}

//删除图片
function youtu_delete($fileid) {
	if(empty($fileid))
		return false;
	
	$url = 'http://web.image.myqcloud.com/photos/v2/' . YOUTU_APPID . '/' . YOUTU_BUCKET . '/0/' . $fileid . '/del';
	$sign = youtu_appSign($fileid, true);
	$args = array(
		'url' => $url,
		'method' => 'post',
		'timeout' => 10,
		'header' => array(
			'Host:web.image.myqcloud.com',
			'Authorization:QCloud '.$sign,
		),
	);
	$Http = new YouTu_Http;
	$res = $Http->send($args);
	$ret = json_decode($res, true);
	return $ret;
}
//查询图片
function youtu_get_info($fileid) {
	$url = 'http://web.image.myqcloud.com/photos/v2/' . YOUTU_APPID . '/' . YOUTU_BUCKET . '/0/' . $fileid . '/';
	$args = array(
		'url' => $url,
		'method' => 'get',
		'timeout' => 10,
		'header' => array(
			'Host:web.image.myqcloud.com',
		),
	);
	$Http = new YouTu_Http;
	$res = $Http->send($args);
	$ret = json_decode($res, true);
	return $ret;
}
//图片缩放、裁剪
function youtu_thumb($width, $height, $forcesize=false) {
	$setting = get_option('youtu_setting');
	$setting = unserialize($setting);
	$mode = $setting['mode'];
	$direction = $setting['direction'];
	if($setting['thumb_width'] && !$forcesize) $width = $setting['thumb_width'];
	if($setting['thumb_height'] && !$forcesize) $height = $setting['thumb_height'];
	if(!$direction) $direction = 'NorthWest';
	$size = '?imageMogr2/gravity/' . $direction . '/crop/' . $width . 'x' . $height;
	
	if($mode == 'view')
		$size = '?imageView2/0/w/' . $width . '/h/' . $height;
	
	return $size;
}
//读取数据库数据
function youtu_get_sql_all($table_name, $select, $w) {
	global $wpdb;
	$table_name = $wpdb->prefix . $table_name;
	$where = ' WHERE ' . $w;
	if($w === 'all') $where = '';
	$results = $wpdb->get_results('SELECT ' . $select . ' FROM `' . $table_name . '`' . $where);
	return $results;
}
//读取字段
function youtu_get_sql_var($table_name, $select, $where) {
	global $wpdb;
	$table_name = $wpdb->prefix . $table_name;
	$query = $wpdb->prepare('SELECT ' . $select . ' FROM `' . $table_name . '` WHERE ' . $where, '');
	$var = $wpdb->get_var($query);
	return $var;
}
//插入字段
function youtu_insert_sql_var($table_name, $data_array) {
	global $wpdb;
	$table_name = $wpdb->prefix . $table_name;
	$wpdb->insert($table_name, $data_array);
}
//更新字段
function youtu_update_sql_var($table_name, $data_array) {
	global $wpdb;
	$table_name = $wpdb->prefix . $table_name;
	$wpdb->update($table_name, $data_array);
}
//删除字段
function youtu_delete_sql_var($table, $w) {
	global $wpdb;
	$table_name = $wpdb->prefix . $table;
	if(is_array($w)) {
		foreach($w as $k=>$v)
			$str .= ' AND ' . $k . '=' . $v;
		$where = ' WHERE ' . substr($str, 5);
	} else {
		$where = ' WHERE ' . $w;
	}
	if($w === 'all') $where = '';
	$sql = 'DELETE FROM `' . $table_name . '`' . $where;
	$wpdb->query($sql);
}
//翻页
function youtu_nav($max_page, $paged, $pagename, $colspan){
	if($max_page > 1) {	
		$prev_page = $paged;
		if($paged > 1) $prev_page = $paged-1;
		$next_page = $paged;
		if($paged < $max_page) $next_page = $paged+1;
		$baseurl = admin_url('admin.php?page=' . $pagename);
		echo '<tr><td class="" colspan="' . $colspan . '">';
		echo '<span>页码：' . $paged . '/' . $max_page . '</span>';
		echo '<a href="' . $baseurl . '">首页</a>';
		echo '<a href="' . $baseurl . '&paged=' . $prev_page . '">上一页</a>';
		echo '<a href="' . $baseurl . '&paged=' . $next_page . '">下一页</a>';
		echo '<a href="' . $baseurl . '&paged=' . $max_page . '">末页</a>';
		echo '</td></tr>';
	}
}
//判断是否万象优图图片
function youtu_is_qqimg($url) {
	$part_url = parse_url($url);
	$part_url = $part_url['host'];
	$base_url = parse_url(YOUTU_BASEURL);
	$base_url = $base_url['host'];
	if($part_url == $base_url)
		return true;
	return false;
}
//获取WordPress所有图片ID
function youtu_get_attachment_ids(){
	$ids = array();
	$args = array(
		'posts_per_page' => -1,
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'post_status' => 'closed',
	);
	$attachment_query = new WP_Query( $args );
	while ( $attachment_query->have_posts() ) {
		$attachment_query->the_post();
		$ids[] = get_the_ID();
	}
	wp_reset_postdata();
	return $ids;
}
//根据图片名字获得缩略版本名称
function youtu_get_size_name($post_id, $filename) {
	$meta = wp_get_attachment_metadata($post_id);
	$sizes = $meta['sizes'];
	if($sizes) {
		foreach($sizes as $k=>$v) 
			if($v['file'] == $filename) {
				$size_name = $k;
				$width = $v['width'];
				$height = $v['height'];
			}
	}
	if(($filename == wp_basename($meta['file'])) || !$size_name || ($size_name && $width == $meta['width'] && $height == $meta['height']) )
		$size_name = 'default';
	return $size_name;
}

//获取图片地址
function youtu_get_attachment_url($post_id) {
	$post_id = (int) $post_id;
	if ( !$post = get_post( $post_id ) )
		return false;
	
	if ( 'attachment' != $post->post_type || !wp_attachment_is_image($post_id) )
		return false;
	
	$url = wp_get_attachment_url($post_id);
	
	$path = youtu_get_sql_var(YOUTU_IMGTABLE, 'url', 'img_id=' . $post_id);
	if($path)
		$url = YOUTU_BASEURL . $path;
	
	return $url;
}
//过滤image_downsize
function youtu_image_downsize($data=false, $id, $size) {
	if ( !wp_attachment_is_image($id) )
		return false;
	$img_url = youtu_get_attachment_url($id);
	$meta = wp_get_attachment_metadata($id);
	$width = $height = 0;
	$is_intermediate = $is_thumbnail = false;
	$img_url_basename = wp_basename($img_url);
	$is_qqimg = youtu_is_qqimg($img_url);
	
	if ( $intermediate = image_get_intermediate_size($id, $size) ) {
		$width = $intermediate['width'];
		$height = $intermediate['height'];
		$is_intermediate = true;
		if(!$is_qqimg)
			$img_url = str_replace($img_url_basename, $intermediate['file'], $img_url);
		if($width != $meta['width'] || $height != $meta['height'])
			$is_thumbnail = true;
	} elseif ( $size == 'thumbnail' ) {
		if ( ($thumb_file = wp_get_attachment_thumb_file($id)) && $info = getimagesize($thumb_file) ) {
			$width = $info[0];
			$height = $info[1];
			$is_intermediate = true;
			if(!$is_qqimg)
				$img_url = str_replace($img_url_basename, wp_basename($thumb_file), $img_url);
			if($width != $meta['width'] || $height != $meta['height'])
				$is_thumbnail = true;
		}
	}
	
	if ( !$width && !$height && isset( $meta['width'], $meta['height'] ) ) {
		$width = $meta['width'];
		$height = $meta['height'];
	}

	if ( $img_url) {
		list( $width, $height ) = image_constrain_size_for_editor( $width, $height, $size );
		if($is_qqimg && $is_thumbnail)
			$img_url = $img_url . youtu_thumb($width, $height);
		$data = array( $img_url, $width, $height, $is_intermediate );
	}
	return $data;
}
if(!is_admin())
	add_filter('image_downsize', 'youtu_image_downsize', 10, 3);
//根据图地址获取图片ID
function get_attachment_id_from_src($link) {
	global $wpdb;
	$link = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $link);
	return $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE guid='$link'");
}
//文章内容输出万象优图
function youtu_the_content($content) {
	preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
	if($matches[1]) {
		foreach($matches[1] as $link) {
			$post_id = get_attachment_id_from_src($link);
			if($post_id) {
				$filename = wp_basename($link);
				$size_name = youtu_get_size_name($post_id, $filename);
				$img_url = youtu_image_downsize('', $post_id, $size_name);
				$url = $img_url[0];
				if($url != $link)
					$content = str_replace($link, $url, $content);
			}
		}
	}
	return $content;
}
if(!is_admin())
	add_filter( 'the_content', 'youtu_the_content', 10, 1 );

//上传WP图片时同步上传到万象优图
function youtu_add_attachment($post_ID) {
	if ( !wp_attachment_is_image($post_ID) )
		return false;
	
	$img_url = get_attached_file($post_ID);
	$res = youtu_upload($post_ID, $img_url);
	if($res['code'] === 0) {
		$res_data = $res['data'];
		$download_url = $res_data['download_url'];
		$part_url = parse_url($download_url);
		$part_url = 'http://' . $part_url['host'] . '/';
		$path = str_replace($part_url, '', $download_url);
		$imgdata = array(
			'img_id' => $post_ID,
			'url' => $path,
		);
		youtu_insert_sql_var(YOUTU_IMGTABLE, $imgdata);
	} else {
		$post_parent = get_post($post_ID)->post_parent;
		$post_title = get_post($post_parent)->post_title;
		$log = '';
		if($post_title) $log .= '文章“' . $post_title . '”同步图片时发生错误<br />';
		$log .= '失败图片ID：' . $post_ID . '<br />失败原因：' . $res['message'];
		$logdata = array(
			'datetime' => date('Y-m-d H:i:s', current_time('timestamp')),
			'log' => $log,
		);
		youtu_insert_sql_var(YOUTU_LOGTABLE, $logdata);
	}
}
add_action( 'add_attachment', 'youtu_add_attachment', 10, 1 );

//删除WP图片时删除万象优图上的图片
function youtu_delete_attachment($post_id) {
	if ( !wp_attachment_is_image($post_id) )
		return false;

	$res = youtu_delete($post_id);
	if($res['code'] === 0) {
		$where = 'img_id="' . $post_id . '"';
		youtu_delete_sql_var(YOUTU_IMGTABLE, $where);
	} else {
		$log = '删除文章图片：“' . $post_id . '”失败<br />失败原因：' . $res['message'];
		$logdata = array(
			'datetime' => date('Y-m-d H:i:s', current_time('timestamp')),
			'log' => $log,
		);
		youtu_insert_sql_var(YOUTU_LOGTABLE, $logdata);
	}
}
add_action( 'delete_attachment', 'youtu_delete_attachment', 10, 1 );

//创建任务
function youtu_add_sync_cron() {
	$timestamp = time();
	$timestamp = $timestamp-3599;
	if( !wp_next_scheduled( 'youtu_sync_cron' ) ) 
		wp_schedule_event( $timestamp, 'hourly', 'youtu_sync_cron');
}
//删除同步任务
function youtu_delete_sync_cron() {
	if( wp_next_scheduled( 'youtu_sync_cron' ) ) 
		wp_clear_scheduled_hook( 'youtu_sync_cron' );
}
//同步图片任务执行函数
function youtu_sync_cron_fun() {
	$post_ids = youtu_get_attachment_ids();
	$post_ids = array_reverse($post_ids);
	$imgres = youtu_get_sql_all(YOUTU_IMGTABLE, 'img_id',  'img_id NOT LIKE "other-%"');
	if($post_ids && $imgres) {
		foreach($imgres as $val)
			$imgids[] = $val->img_id;
		foreach($post_ids as $postid)
			if(!in_array($postid, $imgids)) $attachment_ids[] = $postid;
	} else {
		$attachment_ids = $post_ids;
	}
	if($attachment_ids) {
		$max_execution_time = ini_get('max_execution_time');
		ini_set('max_execution_time', '1800');
		$log_start = array(
			'datetime' => date('Y-m-d H:i:s', current_time('timestamp')),
			'log' => '同步图片任务开始运行',
		);
		youtu_insert_sql_var(YOUTU_LOGTABLE, $log_start);
		
		foreach($attachment_ids as $attachment_id)
			$cron[$attachment_id] = get_attached_file($attachment_id);
		foreach($cron as $key=>$var) {
			$res = youtu_upload($key, $var);
			if($res['code'] === 0) {
				$res_data = $res['data'];
				$download_url = $res_data['download_url'];
				$part_url = parse_url($download_url);
				$part_url = 'http://' . $part_url['host'] . '/';
				$path = str_replace($part_url, '', $download_url);
				$imgdata = array(
					'img_id' => $key,
					'url' => $path,
				);
				youtu_insert_sql_var(YOUTU_IMGTABLE, $imgdata);
			} else {
				$log .= '失败图片ID：' . $key . '<br />失败原因：' . $res['message'] . '<br />';
			}
		}
		
		$log_end['log'] = '同步图片成功！';
		if(!empty($log))
			$log_end['log'] = '同步图片任务未能全部完成<br />' . $log;
		
		$log_end['datetime'] = date('Y-m-d H:i:s', current_time('timestamp'));
		youtu_insert_sql_var(YOUTU_LOGTABLE, $log_end);
		
		ini_set('max_execution_time', $max_execution_time);
	} else {
		$log_err = array(
			'datetime' => date('Y-m-d H:i:s', current_time('timestamp')),
			'log' => '同步图片时，没有找到要上传的图片',
		);
		youtu_insert_sql_var(YOUTU_LOGTABLE, $log_err);
	}
	//删除定时任务
	youtu_delete_sync_cron();
}
add_action( 'youtu_sync_cron', 'youtu_sync_cron_fun' );

?>