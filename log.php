<?php
//系统日志面板
function wp_youtu_log(){
	if($_POST['action'] == 'delete' ) {
		youtu_delete_sql_var(YOUTU_LOGTABLE, 'all');
		echo '<div id="message" class="updated fade">日志已清空！</div>';
	}
?>
<style type="text/css">
#message {
	margin: 1em 0;
	padding: .5em;
}
</style>
<script type="text/javascript" src="<?php echo WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)) . '/youtu.js'; ?>"></script>
<div class="wrap">
	<form method="post" action="">
		<table class="widefat">
			<thead>
				<tr>
					<th>时间</th>
					<th>内容</th>
				</tr>
			</thead>
			<tfoot>
			<?php	
				$logs = youtu_get_sql_all(YOUTU_LOGTABLE, 'datetime,log', 'all');
				$logs = array_reverse($logs);
				if($logs) {
					$paged = $_GET['paged'];
					if(!$paged) $paged = 1;
					$max_page = ceil(count($logs)/20);
					
					youtu_nav($max_page, $paged, 'youtu-log', 2);
					echo '<tr><td colspan="2"><input class="button-primary" type="submit" value="清空日志" /><input type="hidden" name="action" value="delete" /></td></tr>';
				}
				
			?>
			</tfoot>
			<tbody>
			<?php
				if($logs) {
					$count_start = ($paged-1)*20;
					$count_end = $count_start+20;
					if($count_end > count($logs))
						$count_end = count($logs);
					for($i=$count_start; $i<$count_end; $i++) {
						$log = $logs[$i];
						if($log) {
							echo '<tr>';
							echo '<td>' . $log->datetime . '</td>';
							echo '<td>' . $log->log . '</td>';
							echo '</tr>';
						}
					}
				}
			?>
			</tbody>	
		</table>
	</form>
</div>
<?php
}
?>