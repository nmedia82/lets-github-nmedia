<tr>
	<th><?php _e( 'Include or Exclude', 'nm-opw' ); ?></th>
	<td>
		<select class="the_chosen shortcode_select">
			<option value="include"><?php _e( 'Include Following', 'nm-opw' ); ?></option>
			<option value="exclude"><?php _e( 'Exclude Following', 'nm-opw' ); ?></option>
		</select>
	</td>
	<td><?php _e( 'Include or Exclude following categories from shop', 'nm-opw' ); ?></td>
</tr>
<tr>
	<th><?php _e( 'Categories', 'nm-opw' ); ?></th>
	<td>
		<?php $product_categories = get_terms( 'product_cat'); ?>
		<select class="widefat shortcode_ids" multiple="">
			<?php foreach ($product_categories as $cat) {
				echo '<option value="'.$cat->term_id.'">'.$cat->name.'</option>';
			} ?>
		</select>
	</td>
	<td><?php _e( 'Select Categories you want to exclude or include', 'nm-opw' ); ?>.</td>
</tr>
<tr>
	<th><?php _e( 'Shortcode', 'nm-opw' ); ?></th>
	<td>
		<input type="text" style="color: green;text-align: center;" value="[ng-woostore]" class="widefat shortcode_ready" disabled="disabled">
	</td>
	<td><?php _e( 'Copy and use this shortcode', 'nm-opw' ); ?>.</td>
</tr>
<script>
	jQuery(document).ready(function($) {
		jQuery('tr').on('change', '.shortcode_select', function(event) {
			event.preventDefault();
			var ids = jQuery('.shortcode_ids').val();
			var inex = jQuery(this).val();
			jQuery('.shortcode_ready').val('[ng-woostore '+inex+'="'+ids+'"]');
		});
		jQuery('tr').on('change', '.shortcode_ids', function(event) {
			event.preventDefault();
			var ids = jQuery(this).val();
			var inex = jQuery('.shortcode_select').val();
			jQuery('.shortcode_ready').val('[ng-woostore '+inex+'="'+ids+'"]');
		});
	});
</script>