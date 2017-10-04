<?php

class Color_Marker
{
	/**
	 * Displays the form for the metabox. The nonce will be added automatically.
	 *
	 * @param object $post The object of the post.
	 * @param array $args The argumets passed from `add_meta_box()`.
	 * @return none
	 */
	public static function form( $term_id )
	{
		$icon_images = self::icon_images();
		if ( $term_id ) {
			$color = get_term_meta( $term_id, '__color', true );
		} else {
			$color = 'blue';
		}

		wp_nonce_field( basename( __FILE__ ), '__term_meta' );

		echo '<div style="display: flex; flex-wrap: wrap; margin: 1em 0;">';

		foreach ( $icon_images as $name => $url ) {
			$checked = ( $color === $name )? 'checked': '';
			printf(
				'<div style="text-align: center; margin: 10px;"><label><img src="%2$s"><br><input type="radio" name="__color" value="%1$s" %3$s></label></div>',
				esc_attr( $name ),
				esc_url( $url ),
				$checked
			);
		}

		echo '</div>';
	}

	public static function edit_form( $term )
	{
		if ( @$term->term_id ) {
			$term_id = $term->term_id;
		} else {
			$term_id = '';
		}
		?>
    <tr class="form-field term-meta-text-wrap">
        <th scope="row"><label for="term-meta-text">Color</label></th>
        <td><?php self::form( $term_id ); ?></td>
    </tr>
		<?php
	}

	/**
	 * Save the metadata from the `form()`. The nonce will be verified automatically.
	 *
	 * @param int $post_id The ID of the post.
	 * @return none
	 */
	public static function save( $term_id )
	{
		// verify the nonce --- remove if you don't care
		if ( ! isset( $_POST['__term_meta'] )
			|| ! wp_verify_nonce( $_POST['__term_meta'], basename( __FILE__ ) ) ) {
			return;
		}

		$old_value  = get_term_meta( $term_id, '__color', true );
		$new_value = $_POST['__color'];

		if ( $old_value && '' === $new_value ) {
			delete_term_meta( $term_id, '__color' );
		} elseif ( $old_value !== $new_value ) {
			update_term_meta( $term_id, '__color', $new_value );
		}
	}

	public static function icon_images() {
		return array(
			'blue' => 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
			'red' => 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
			'green' => 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
			'orange' => 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png',
			'yellow' => 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-yellow.png',
			'violet' => 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-violet.png',
			'grey' => 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png',
			'black' => 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-black.png'
		);
	}
}

add_action( 'poi-category_add_form_fields', 'Color_Marker::form' );
add_action( 'poi-category_edit_form_fields', 'Color_Marker::edit_form' );


add_action( 'edit_poi-category',   'Color_Marker::save' );
add_action( 'create_poi-category', 'Color_Marker::save' );
