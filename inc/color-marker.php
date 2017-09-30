<?php

class Color_Marker extends \Miya\WP\Custom_Field
{
	/**
	 * Displays the form for the metabox. The nonce will be added automatically.
	 *
	 * @param object $post The object of the post.
	 * @param array $args The argumets passed from `add_meta_box()`.
	 * @return none
	 */
	public function form( $post, $args )
	{
		$icon_images = self::icon_images();
		$color = get_post_meta( get_the_ID(), $this->id, true );
		if ( ! $color ) {
			$color = 'blue';
		}

		echo '<div style="display: flex; flex-wrap: wrap;">';

		foreach ( $icon_images as $name => $url ) {
			$checked = ( $color === $name )? 'checked': '';
			printf(
				'<div style="text-align: center; margin: 10px;"><label><img src="%3$s"><br><input type="radio" name="%1$s" value="%2$s" %4$s></label></div>',
				esc_attr( $this->id ),
				esc_attr( $name ),
				esc_url( $url ),
				$checked
			);
		}

		echo '</div>';
	}

	/**
	 * Save the metadata from the `form()`. The nonce will be verified automatically.
	 *
	 * @param int $post_id The ID of the post.
	 * @return none
	 */
	public function save( $post_id )
	{
		if ( isset( $_POST[ $this->id ] ) ) {
			update_post_meta( $post_id, $this->id, $_POST[ $this->id ] );
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

