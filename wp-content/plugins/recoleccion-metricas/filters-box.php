<form action="" method="get">
	<?php
	$url_query = array();
	foreach ($_GET as $key => $value) {
		$url_query[] = '<input type="hidden" name="'.$key.'" value="'.$value.'">';
	}

	echo implode(' ', $url_query);
	?>
	<ul class="metrica-filters">

		<li class="filter_user">
			<label>User</label>
			<select name="filter_user" style="width: 250px!important;">
				<option value="0">All</option>
				<?php
				$users = get_users();
				foreach ($users as $user) {
					if ($user->ID > 0) {
						?>
						<option <?php echo @selected( $_GET['filter_user'], $user->ID ); ?> value="<?php echo $user->ID; ?>"><?php echo $user->first_name.' '.$user->last_name; ?> (<?php echo $user->user_login; ?>)</option>
						<?php
					}
				}
				?>
			</select>
		</li>

		<li class="filter_action">
			<label>Action</label>
			<select name="filter_action">
				<option <?php echo @selected( $_GET['filter_action'], '' ); ?> value="">All</option>
				<option <?php echo @selected( $_GET['filter_action'], 'post' ); ?> value="post">post</option>
				<option <?php echo @selected( $_GET['filter_action'], 'page' ); ?> value="page">page</option>
				<option <?php echo @selected( $_GET['filter_action'], 'tag' ); ?> value="tag">tag</option>
				<option <?php echo @selected( $_GET['filter_action'], 'search' ); ?> value="search">search</option>
				<option <?php echo @selected( $_GET['filter_action'], 'taxonomy' ); ?> value="taxonomy">taxonomy</option>
				<option <?php echo @selected( $_GET['filter_action'], 'archive' ); ?> value="archive">archive</option>
				<option <?php echo @selected( $_GET['filter_action'], 'day archive' ); ?> value="day archive">day archive</option>
				<option <?php echo @selected( $_GET['filter_action'], 'month archive' ); ?> value="month archive">month archive</option>
				<option <?php echo @selected( $_GET['filter_action'], 'year archive' ); ?> value="year archive">year archive</option>
				<option <?php echo @selected( $_GET['filter_action'], 'front page' ); ?> value="front page">front page</option>
				<option <?php echo @selected( $_GET['filter_action'], 'attachment/media' ); ?> value="attachment/media">attachment/media</option>
				<option <?php echo @selected( $_GET['filter_action'], 'navigate' ); ?> value="navigate">other</option>
			</select>
		</li>

		<li>
			<label>From</label>
			<input name="filter_from" class="text filter_from" value="<?php echo ((isset($_GET['filter_from'])) ? $_GET['filter_from'] : date('m/d/Y', strtotime('-30 day') )); ?>">
			<label>To</label>
			<input name="filter_to" class="text filter_to" value="<?php echo ((isset($_GET['filter_to'])) ? $_GET['filter_to'] : date('m/d/Y', strtotime('+1 day') )); ?>">
		</li>

		<br>

		<li>
			<label>Group by</label>
			<select name="filter_group_by" class="filter_group_by">
				<option value="">No grouping</option>
				<option <?php echo @selected( $_GET['filter_group_by'], 'time' ); ?> value="time">Time</option>
				<option <?php echo @selected( $_GET['filter_group_by'], 'logins' ); ?> value="logins">Logins</option>
			</select>
		</li>

		<script>
		jQuery(document).ready(function() {
			jQuery('.filter_group_by').change(function() {
				jQuery('.filter_by_num').hide();
				jQuery('.filter_by_num_seconds').hide();
				var _t = jQuery(this);

				if (_t.val() != '') {
					jQuery('.filter_by_num').show();
					jQuery('.filter_by_num label span').html(_t.val());

					if (_t.val() == 'time') {
						jQuery('.filter_by_num_seconds').show();
					}
				}

			}).change();
		});
		</script>

		<li class="filter_by_num">
			<label>Only show users with <span>time</span> higher (or equal) than</label>
			<input type="text" name="filter_higher_than" value="<?php echo ((isset($_GET['filter_higher_than'])) ? $_GET['filter_higher_than'] : '' ); ?>"> <span class="filter_by_num_seconds">seconds</span>
		</li>

		<li>
			<label>Order by</label>
			<select name="filter_order_by" class="filter_order_by">
				<option <?php echo @selected( $_GET['filter_order_by'], 'date' ); ?> value="date">Date</option>
				<option <?php echo @selected( $_GET['filter_order_by'], 'logins_time' ); ?> value="logins_time">Logins / Time</option>
			</select>
		</li>

		<br>

		<li>
			<label>Generate file</label>
			<select name="generate_file">
				<option value="">No</option>
				<option <?php echo @selected( $_GET['generate_file'], 'excel' ); ?> value="excel">CSV para Excel</option>
				<option <?php echo @selected( $_GET['generate_file'], 'numbers' ); ?> value="numbers">CSV para Numbers (apple osx)</option>
			</select>
		</li>

		<br>

		<li>
			<input type="submit" class="button button-primary" value="submit">
		</li>

	</ul>
</form>

<script>
jQuery(document).ready(function() {
	jQuery( ".filter_from, .filter_to" ).datepicker({
		dateFormat: 'mm/dd/yy'
	});
} );
</script>

<style>
.metrica-filters {

}
.metrica-filters li {
	display: inline-block;
	padding-right: 15px;
}
.metrica-filters li:last-child {
	padding-right: 0px;
}
</style>