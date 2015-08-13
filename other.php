<?php
//优图直传面板
function wp_youtu_other(){
	if($_POST['action'] == 'upload' ) {
		$mime_type = sanitize_mime_type($_FILES['FileContent']['type']);

		if ($mime_type == 'image/png' || $mime_type == 'image/gif' || $mime_type == 'image/jpeg') {	
			$fileid = 'other-' . time();
			$filepath = $_FILES['FileContent']['tmp_name'];
			$upload_res = youtu_upload($fileid, $filepath);
			if (0 === $upload_res['code']) {
				$upload_data = $upload_res['data'];
				$download_url = $upload_data['download_url'];
				$part_url = parse_url($download_url);
				$part_url = 'http://' . $part_url['host'] . '/';
				$path = str_replace($part_url, '', $download_url);
				$imgdata = array(
					'img_id' => $fileid,
					'url' => $path,
				);
				youtu_insert_sql_var(YOUTU_IMGTABLE, $imgdata);
				$upload_message = '图片地址：<br />' . YOUTU_BASEURL . $path . '<br />以后您可以在本表格中获得图片地址';
			} else {
				$upload_message = '错误提示：' . $upload_res['message'];
			}
		} else {
			$upload_message = '上传错误！';
		}
		if($upload_message)
			echo '<div id="message" class="updated fade">' . $upload_message . '</div>';
	}
	
	if($_POST['action'] == 'delete' ) {
		$post_img_ids = $_POST['img_ids'];
		if(!empty($post_img_ids)) {
			foreach($post_img_ids as $id){
				$delete_res = youtu_delete($id);
				if(0 === $delete_res['code']) {
					$where = 'img_id="' . $id . '"';
					youtu_delete_sql_var(YOUTU_IMGTABLE, $where);
				} else {
					$delete_message .= '<br />失败图片ID：' . $id . '<br />错误信息：' . $delete_res['message'];
				}
			}
			
			if(!$delete_message) $delete_message = '选中图片已全部删除！';
			echo '<div id="message" class="updated fade">' . $delete_message . '</div>';
		}
	}
?>
<style type="text/css">
#message {
	margin: 1em 0;
	padding: .5em;
}
#youtu-upload {
	margin-top: 20px;
	padding: 10px;
	background-color: #ffffff;
	border: 1px solid #e6e6e6;
	box-shadow: 0 0 3px #e3e3e3;
}
.wrap tfoot span, .wrap tfoot a {
	padding: 2px 5px;
	border: 1px solid #cdcdcd;
	backgroud-color: #eeeeee;
	margin-right: 5px;
}
.upload-message {
	color: blue;
}
.wrap .widefat img {
	max-width: 80px;
	max-height: 60px;
}
</style>
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)) . '/youtu.js'; ?>"></script>
<div class="wrap">
	<form method="post" action="">
		<table class="widefat">
			<thead>
				<tr>
					<th><input type="checkbox" id="allid" value="" /></th>
					<th>图片预览</th>
					<th>图片地址</th>
				</tr>
			</thead>
			<tfoot>
			<?php
				$other_imgs = youtu_get_sql_all(YOUTU_IMGTABLE, 'img_id,url',  'img_id like "other-%"');
				$other_imgs = array_reverse($other_imgs);
				if($other_imgs) {
					$paged = $_GET['paged'];
					if(!$paged) $paged = 1;
					$max_page = ceil(count($other_imgs)/20);
					
					youtu_nav($max_page, $paged, 'youtu-other', 3);
					echo '<tr><td colspan="3"><input class="button-primary" type="submit" name="delete" value="删除选中" /><input type="hidden" name="action" value="delete" /></td></tr>';
				}
			?>
			</tfoot>
			<tbody>
			<?php
				if($other_imgs) {
					$count_start = ($paged-1)*20;
					$count_end = $count_start+20;
					if($count_end > count($other_imgs))
						$count_end = count($other_imgs);
					for($i=$count_start; $i<$count_end; $i++) {
						$otherimg = $other_imgs[$i];
						$img_url = YOUTU_BASEURL . $otherimg->url;
						$img_t = $img_url . youtu_thumb(80, 60, true);
						if($otherimg) {
							echo '<tr>';
							echo '<td class="check-id"><input type="checkbox" name="img_ids[]" value="' . $otherimg->img_id . '" /></td>';
							echo '<td><img class="attachment-80x60" src="' . $img_t . '" alt="" /></td>';
							echo '<td><a href="' . $img_url . '" target="_blank">' . $img_url . '</a></td>';
							echo '</tr>';
						}
					}
				}
			?>
			</tbody>	
		</table>
	</form>
	<div id="youtu-upload">
		<form action="" method="post" enctype="multipart/form-data">
			<input type="file" accept="image/pjpeg,image/jpeg,image/gif,image/png" name="FileContent" id="file" /> 
			<input class="button" type="submit" value="上传" /><input type="hidden" name="action" value="upload" />
		</form>
		<p>只会上传到腾讯万象优图，您的服务器上不会保存。</p>
	</div>
</div>
<?php
}
?>