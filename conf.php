<?php
$youtu_setting = get_option('youtu_setting');
$youtu_setting = unserialize($youtu_setting);
if(!empty($youtu_setting)) {
	$secretid = $youtu_setting['secretid'];
	$secretkey = $youtu_setting['secretkey'];
	$appid = $youtu_setting['appid'];
	$bucket = $youtu_setting['bucket'];
	$domain = $youtu_setting['domain'];
	if(empty($domain)) 
		$domain = $bucket . '-' . $appid . '.image.myqcloud.com';
	$baseurl = 'http://' . $domain . '/';
}
//万象优图Secret Id
define('YOUTU_SECRETID', $secretid);
//万象优图Secret Key
define('YOUTU_SECRETKEY', $secretkey);
//万象优图项目ID
define('YOUTU_APPID', $appid);
//万象优图空间名称
define('YOUTU_BUCKET', $bucket);
//万象优图域名
define('YOUTU_BASEURL', $baseurl);
//图片数据表名称
define('YOUTU_IMGTABLE', 'youtu');
//日志数据表名称
define('YOUTU_LOGTABLE', 'youtulog');
?>