<?php
/**
 * Template used to display the pixproof gallery
 *
 * @global string       $client_name
 * @global string       $event_date
 * @global int          $number_of_images
 * @global string       $file
 */
?>
<div id="pixproof_data" class="pixproof-data">
	<div class="grid">
		<?php if ( ! empty( $client_name ) ) { ?>
		<div class="grid__item  one-half  lap-and-up-one-quarter  push-half--bottom">
			<div class="entry__meta-box">
				<span class="meta-box__title"><?php esc_attr_e( 'Client','pixproof' );?></span>
				<span><?php echo $client_name; ?></span>
			</div>
		</div>
		<?php
		}
		if ( ! empty( $event_date ) ) { ?>
		<div class="grid__item  one-half  lap-and-up-one-quarter  push-half--bottom">
			<div class="entry__meta-box">
				<span class="meta-box__title"><?php esc_html_e( 'Event date','pixproof' );?></span>
				<span><?php echo $event_date; ?></span>
			</div>
		</div>
		<?php
		}
		if ( ! empty( $number_of_images ) ) { ?>
		<div class="grid__item  one-half  lap-and-up-one-quarter">
			<div class="entry__meta-box">
				<span class="meta-box__title"><?php esc_html_e( 'Images','pixproof' );?></span>
				<span><?php echo $number_of_images; ?></span>
			</div>
		</div>
		<?php
		}
		if ( ! empty( $file ) ) { ?>
		<div class="grid__item  one-half  lap-and-up-one-quarter">
			<div class="entry__meta-box">
				<button class="button-download  js-download" onclick="window.open('<?php echo $file; ?>')"><?php esc_html_e( 'Download selected','pixproof' ); ?></button>
			</div>
		</div>
		<?php } ?>
	</div>
	<hr class="separator  separator--data"/>
<?php
