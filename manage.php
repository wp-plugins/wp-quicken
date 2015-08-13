<?php
//图片管理面板
function wp_youtu_manage(){

	if($_POST['action'] == 'delete' ) {
		$img_ids = $_POST['img_ids'];
		if(!empty($img_ids)) {
			foreach($img_ids as $id){
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
.wrap tfoot span, .wrap tfoot a {
	padding: 2px 5px;
	border: 1px solid #cdcdcd;
	backgroud-color: #eeeeee;
	margin-right: 5px;
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
					<th>对应文章</th>
				</tr>
			</thead>
			<tfoot>
			<?php	
				$post_imgs = youtu_get_sql_all(YOUTU_IMGTABLE, 'img_id,url',  'img_id NOT LIKE "other-%"');
				$post_imgs = array_reverse($post_imgs);
				if($post_imgs) {
					$paged = $_GET['paged'];
					if(!$paged) $paged = 1;
					$max_page = ceil(count($post_imgs)/20);
					youtu_nav($max_page, $paged, 'youtu-manage', 4);
					echo '<tr><td colspan="4"><input class="button-primary" type="submit" name="delete" value="删除选中" /><input type="hidden" name="action" value="delete" /></td></tr>';
				}
			?>
			</tfoot>
			<tbody>
			<?php
				if($post_imgs) {
					$count_start = ($paged-1)*20;
					$count_end = $count_start+20;
					if($count_end > count($post_imgs))
						$count_end = count($post_imgs);
					for($i=$count_start; $i<$count_end; $i++) {
						$postimg = $post_imgs[$i];
						$img_url = YOUTU_BASEURL . $postimg->url;
						$img_t = $img_url . youtu_thumb(80, 60, true);
						$post_parent = get_post($postimg->img_id)->post_parent;
						$the_post = get_post($post_parent);
						$post_title = $the_post->post_title;
						$post_link = $the_post->guid;
						if($postimg) {
							echo '<tr>';
							echo '<td class="check-id"><input type="checkbox" name="img_ids[]" value="' . $postimg->img_id . '" /></td>';
							echo '<td><img class="attachment-80x60" src="' . $img_t . '" alt="" /></td>';
							echo '<td><a href="' . $img_url . '" target="_blank">' . $img_url . '</a></td>';
							echo '<td><a href="' . $post_link . '" target="_blank">' . $post_title . '</a></td>';
							echo '</tr>';
						}
					}
				}
			?>
			</tbody>	
		</table>
		
	</form>
	<p>这里显示已上传到万象优图的WordPress图片，删除这里的图片不会影响本地图片，仅会删除万象优图上的版本。</p>
</div>
<?php
}
?>