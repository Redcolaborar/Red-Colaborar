<div class="wrap">
	<h2><?php echo $plugin_title; ?></h2>
	<div class="card">
		<h3><?php _e( 'AnsPress and BBPress Mapping Relationship', $this->_plugin_name ); ?></h3>
		<form action="<?php echo esc_url( admin_url('admin.php')); ?>?page=<?php echo $plugin_name; ?>" method="post" enctype="multipart/form-data" name="<?php echo $plugin_name; ?>_form">
			<table class="wp-list-table widefat fixed pages">
			  <thead>
				<tr>
				  <td class="manage-column column-cb check-column"><strong>
					<label class="screen-reader-text" for="cb-select-all-1"><?php _e( 'Select All', $this->_plugin_name ); ?></label>
					<input id="cb-select-all-1" type="checkbox">
					</strong></td>
				  <th scope="col" class="manage-column column"><strong><?php _e( 'AnsPress Category', $this->_plugin_name ); ?></strong></th>
				  <th width="3">&nbsp;  </th>
				  <th scope="col" class="manage-column column"><strong><?php _e( 'BBPress Category', $this->_plugin_name ); ?></strong></th>
				</tr>
			  </thead>
			  <tbody>
				<?php foreach ( $cat as $key=>$val ) : ?>
				<tr>
				  <th scope="row" class="check-column"> <label class="screen-reader-text" for="cb-select-<?php echo $val->aptobploader_id;?>"><?php _e( 'Select', $this->_plugin_name ); ?></label>
					<input id="cb-select-<?php echo $val->aptobploader_id;?>" type="checkbox" name="aptobploader_id[]" value="<?php echo $val->aptobploader_id;?>">
					<div class="locked-indicator"></div>
				  </th>
				  <td><?php echo $val->ap_category;?></td>
				  <td> &raquo; </td>
				  <td><?php echo $val->bp_category;?></td>
				</tr>
				<?php endforeach;?>
			  </tbody>
			</table>
			<div class="tablenav">
				<button type="submit" name="<?php echo $plugin_name; ?>_deleteoptions" value="1" class="button button-primary"><?php _e( 'Delete', $this->_plugin_name ); ?></button>
			</div>
		</form>	
	</div>
	<div class="card">
	<h3><?php _e( 'Map AnsPress Category to BBPress Category', $this->_plugin_name ); ?></h3>
	  <form action="<?php echo esc_url( admin_url('admin.php')); ?>?page=<?php echo $plugin_name; ?>" method="post" enctype="multipart/form-data" name="<?php echo $plugin_name; ?>_form">
		<p><?php _e( 'For multilple AnsPress category, add comma to seperate each category e.g. (AnsPress1,AnsPress2,AnsPress3,AnsPres(n))', $this->_plugin_name ); ?></p>
			<?php wp_nonce_field(); ?>
			<label for="<?php echo $plugin_name; ?>_anspress" class="screen-reader-text"><?php _e( 'AnsPress Category', $this->_plugin_name ); ?></label>
			<input type="text" name="<?php echo $plugin_name; ?>_anspress" placeholder="<?php _e( 'AnsPress Category', $this->_plugin_name ); ?>" style="width: 100%"/>
			<label for="<?php echo $plugin_name; ?>_bbpress" class="screen-reader-text"><?php _e( 'BBPress Category', $this->_plugin_name ); ?></label>
			<input type="text" name="<?php echo $plugin_name; ?>_bbpress" placeholder="<?php _e( 'BBPress Category', $this->_plugin_name ); ?>" style="width: 100%"/>
			<input type="submit" name="<?php echo $plugin_name; ?>_saveoptions" value="<?php _e( 'Add Mapping', $this->_plugin_name ); ?>" class="button button-primary"/>
		</form>		
	</div>
</div>