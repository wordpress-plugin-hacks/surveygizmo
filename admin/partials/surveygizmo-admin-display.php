<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://squibble-fish.com
 * @since      1.0.0
 *
 * @package    Surveygizmo
 * @subpackage Surveygizmo/admin/partials
 */
?>

<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
<form id="<?php echo $this->plugin_name; ?>_sg_keys" name="<?php echo $this->plugin_name; ?>_sg-api-keys" method="POST" action="options.php" >
	<?php
	$options = get_option( $this->plugin_name );
	?>
	<?php
	settings_fields( $this->plugin_name );
	do_settings_sections( $this->plugin_name );
	?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="<?php echo $this->plugin_name ?>[key]">API Key</label>
				</th>
				<td>
					<input type="text" name="<?php echo $this->plugin_name ?>[key]" placeholder="API Key" value="<?php echo $options['key'] ?>" class="regular-text">
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="<?php echo $this->plugin_name ?>[secret]">Secret</label>
				</th>
				<td>
					<input type="password" name="<?php echo $this->plugin_name ?>[secret]" placeholder="Secret Key" value="<?php echo $options['secret'] ?>" class="regular-text">
<!--					<p>--><?php //echo $options['secret'] ?><!--</p>-->
				</td>
			</tr>
		</tbody>
	</table>
	<?php submit_button( 'Save all keys', 'primary','submit', TRUE ); ?>
</form>