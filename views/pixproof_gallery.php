<?php
/**
 * Template used to display the pixproof gallery
 * Available vars:
 * array        $gallery_ids        An array with all attachments ids
 * object       $attachments        An object with all the attachments
 * string       $number_of_images   Count attachments
 * string       $columns            Number of columns
 */

//$specific = array();
//$i = 1;

//foreach ( $attachments as $attachment ) {
//	$specific[$attachment->ID] = $i;
//	++$i;
//}
// <span><?php echo "Image {$specific[$attachment->ID]} of {$number_of_images}"; </span>
?>
<div id="pixproof_gallery" class="gallery  gallery-columns-<?php echo $columns; ?>  cf  js-pixproof-gallery">
	<?php
	$idx = 1;
	foreach ( $attachments as $attachment ) {
		if ( 'selected' == self::get_attachment_class( $attachment ) ) {
			$select_label = __( 'Deselect', 'pixproof_l10n' );
		} else {
			$select_label = __( 'Select', 'pixproof_l10n' );
		}

		$thumb_img  = wp_get_attachment_image_src( $attachment->ID );
		$image_full = wp_get_attachment_image_src( $attachment->ID, 'full-size' );

		//lets determine what should we display under each image according to settings
		// also what id should we assign to that image so the auto comments linking works
		$image_name   = '';
		$image_id_tag = '';
		if ( isset( $photo_display_name ) ) {
			switch ( $photo_display_name ) {
				case 'unique_ids':
					$image_name   = '#' . $attachment->ID;
					$image_id_tag = 'item-' . $attachment->ID;
					break;
				case 'consecutive_ids':
					$image_name   = '#' . $idx;
					$image_id_tag = 'item-' . $idx;
					break;
				case 'file_name':
					$image_name   = '#' . $attachment->post_name;
					$image_id_tag = 'item-' . $attachment->post_name;
					break;
				case 'unique_ids_photo_title':
					$image_name   = '#' . $attachment->ID . ' ' . $attachment->post_title;
					$image_id_tag = 'item-' . $attachment->ID;
					break;
				case 'consecutive_ids_photo_title':
					$image_name   = '#' . $idx . ' ' . $attachment->post_title;
					$image_id_tag = 'item-' . $idx;
					break;
			}
		} else {
			//default to unique ids aka attachment id
			$image_name   = '#' . $attachment->ID;
			$image_id_tag = 'item-' . $attachment->ID;
		}
		?>

		<div class="proof-photo  js-proof-photo  gallery-item <?php self::attachment_class( $attachment ); ?>" <?php self::attachment_data( $attachment ); ?>  id="<?php echo $image_id_tag; ?>">
			<div class="proof-photo__bg">
			<div class="proof-photo__container">
				<img src="<?php echo $thumb_img[0]; ?>" alt="<?php echo $attachment->post_title; ?>"/>

				<div class="proof-photo__meta">
					<div class="flexbox">
						<div class="flexbox__item">
							<ul class="actions-nav  nav  nav--stacked">
								<li>
									<a class="meta__action  zoom-action" href="<?php echo $image_full[0]; ?>" data-photoid="<?php echo $image_id_tag; ?>">
										<span class="button-text"><?php _e( 'Zoom', 'pixproof_l10n' ); ?></span>
									</a>
								</li>
								<li>
									<hr class="separator"/>
								</li>
								<li>
									<a class="meta__action  select-action" href="#" data-photoid="<?php echo $image_id_tag; ?>">
										<span class="button-text"><?php echo $select_label; ?></span>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="proof-photo__status">
					<span class="ticker">&check;</span>
					<span class="spinner"></span>
				</div>
			</div>
			<span class="proof-photo__id"><?php echo $image_name; ?></span>
			</div>
		</div>
		<?php

		if ( $idx % $columns == 0 ) {
			echo '<br style="clear: both">';
		}

		$idx ++;
	} ?>
</div>
