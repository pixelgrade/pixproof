<?php
	/**
	 * Represents the view for the administration dashboard.
	 *
	 * This includes the header, options, and other information that should
	 * provide the user interface to the end user.
	 *
	 * @package   PixTypes
	 * @author    Pixelgrade <contact@pixelgrade.com>
	 * @license   GPL-2.0+
	 * @link      http://pixelgrade.com
	 * @copyright 2013 Pixel Grade Media
	 */

	$config = include pixproof::pluginpath().'plugin-config'.EXT;

	// invoke processor
	$processor = pixproof::processor($config);
	$status = $processor->status();
	$errors = $processor->errors(); ?>

<div class="wrap" id="pixproof_form">

	<div id="icon-options-general" class="icon32"><br></div>

	<h2><?php _e('Pixproof', 'pixproof_txtd'); ?></h2>

	<?php if ($processor->ok()): ?>

		<?php if ( ! empty($errors)): ?>
			<br/>
			<p class="update-nag">
				<strong><?php _e('Unable to save settings.', 'pixproof_txtd'); ?></strong>
				<?php _e('Please check the fields for errors and typos.', 'pixproof_txtd'); ?>
			</p>
		<?php endif; ?>

		<?php if ($processor->performed_update()): ?>
			<br/>
			<p class="update-nag">
				<?php _e('Settings have been updated.', 'pixproof_txtd');?>
			</p>
		<?php endif; ?>

		<?php echo $f = pixproof::form($config, $processor);
			echo $f->field('hiddens')->render();
			echo $f->field('general')->render();
			echo $f->field('post_types')->render();
		?>

<!--			--><?php //echo $f->field('taxonomies')->render(); ?>

			<button type="submit" class="button button-primary">
				<?php _e('Save Changes', 'pixproof_txtd'); ?>
			</button>

		<?php echo $f->endform() ?>

	<?php elseif ($status['state'] == 'error'): ?>

		<h3>Critical Error</h3>

		<p><?php echo $status['message'] ?></p>

	<?php endif; ?>
</div>