<?php

/**
 * Class Pixproof_CMB2_Field_Pixproof_Gallery.
 */
class Pixproof_CMB2_Field_Pixproof_Gallery {

	/**
	 * Current version number.
	 */
	const VERSION = '1.1.0';

	/**
	 * Initialize the plugin by hooking into CMB2.
	 */
	public function __construct() {
		add_filter( 'cmb2_render_pixproof_gallery', array( $this, 'render_pixproof_gallery' ), 10, 5 );
		add_filter( 'cmb2_sanitize_pixproof_gallery', array( $this, 'sanitize_pixproof_gallery' ), 10, 4 );

		add_action('wp_ajax_ajax_proof_pixgallery_preview', array( $this, 'ajax_preview' ) );
	}

	/**
	 * Render field.
	 *
	 * @param array $field The passed in `CMB2_Field` object
	 * @param mixed $field_escaped_value The value of this field escaped.
	 * @param int $field_object_id The ID of the current object
	 * @param string $field_object_type The type of object you are working with.
	 *                                   Most commonly, `post` (this applies to all post-types),
	 *                                   but could also be `comment`, `user` or `options-page`.
	 * @param CMB2_Types $field_type_object
	 */
	public function render_pixproof_gallery( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {

		$this->setup_admin_scripts();

		// output html ?>
		<div id="proof_pixgallery">
			<ul></ul>
			<a class="open_proof_pixgallery" href="#" class="wp-gallery">
				<input type="hidden" name="<?php echo esc_attr( $field->args('_name') . '[gallery]' ); ?>" id="pixgalleries"
				       value="<?php echo isset( $field_escaped_value['gallery'] ) ? $field_escaped_value['gallery'] : ''; ?>"/>
				<input type="hidden" name="<?php echo esc_attr( $field->args('_name') . '[random]' ); ?>" id="pixgalleries_random"
				       value="<?php echo isset( $field_escaped_value['random'] ) ? $field_escaped_value['random'] : ''; ?>"/>
				<input type="hidden" name="<?php echo esc_attr( $field->args('_name') . '[columns]' ); ?>" id="pixgalleries_columns"
				       value="<?php echo isset( $field_escaped_value['columns'] ) ? $field_escaped_value['columns'] : '';?>"/>
				<input type="hidden" name="<?php echo esc_attr( $field->args('_name') . '[size]' ); ?>" id="pixgalleries_size"
				       value="<?php echo isset( $field_escaped_value['size'] ) ? $field_escaped_value['size'] : ''; ?>"/>
				<i class="icon dashicons dashicons-images-alt"></i>
			</a>
		</div>
		<?php $field_type_object->_desc( true, true );
	}

	/**
	 * Optionally save the latitude/longitude values into two custom fields.
	 */
	public function sanitize_pixproof_gallery( $override_value, $value, $object_id, $field_args ) {
		if ( ! empty( $value['gallery'] ) ) {
			update_post_meta( $object_id, $field_args['id'] . '_gallery', $value['gallery'] );
		}

		if ( ! empty( $value['random'] ) ) {
			update_post_meta( $object_id, $field_args['id'] . '_random', $value['random'] );
		}

		if ( ! empty( $value['columns'] ) ) {
			update_post_meta( $object_id, $field_args['id'] . '_columns', $value['columns'] );
		}

		if ( ! empty( $value['size'] ) ) {
			update_post_meta( $object_id, $field_args['id'] . '_size', $value['size'] );
		}

		return $value;
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function setup_admin_scripts() {
		wp_register_style( 'pixproof-styles', trailingslashit( Pixproof_Plugin()->plugin_baseuri ) . 'includes/vendor/cmb2-pixproof_gallery-field/pixproof_gallery.css' );
		wp_enqueue_style( 'pixproof-styles' );


		wp_enqueue_media();
		wp_register_script( 'proof_pixgallery', trailingslashit( Pixproof_Plugin()->plugin_baseuri ) . 'includes/vendor/cmb2-pixproof_gallery-field/pixproof_gallery.js' );
		wp_enqueue_script( 'proof_pixgallery' );

		// ensure the wordpress modal scripts even if an editor is not present
		wp_enqueue_script( 'jquery-ui-dialog', false, array('jquery'), false, true );
		wp_localize_script( 'proof_pixgallery', 'locals', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	// create an ajax call which will return a preview to the current gallery
	public function ajax_preview(){
		$result = array('success' => false, 'output' => '');

		if (isset($_REQUEST['attachments_ids'])) {
			$ids = $_REQUEST['attachments_ids'];
		}
		if ( empty($ids) ) {
			echo json_encode( $result );
			exit;
		}

		$ids = explode( ',', $ids );

		foreach ( $ids as $id ) {
			$attach = wp_get_attachment_image_src( $id, 'thumbnail', false);

			$result["output"] .= '<li><img src="'.$attach[0] .'" /></li>';

		}
		$result["success"] = true;
		echo json_encode( $result );
		exit;
	}

}
$pixproof_cmb2_field_pixproof_gallery = new Pixproof_CMB2_Field_Pixproof_Gallery();
