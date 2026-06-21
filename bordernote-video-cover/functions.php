<?php


// Ensure this file is being included by WordPress.

defined('ABSPATH') or die();

// Register the widget

function register_bordernote_video_cover_widget() {

register_widget('Bordernote_Video_Cover');

}

add_action('widgets_init', 'register_bordernote_video_cover_widget');

function bordernote_register_widgets(){
	genesis_register_sidebar( array(
		'id'          => 'bordernote-body-open',
		'name'        => __( 'Bordernote after Body Open', 'bordernote' ),
        'description' => __('Executes immediately after the HTML body tag on a page opens.','bordernote')
	) );
}

function bordernote_body_open() {
	if(is_active_sidebar('bordernote-body-open')) {
		$cover = get_sidebar("bordernote-body-open");
	}
}

add_action('init','bordernote_register_widgets');
add_action('wp_body_open','bordernote_body_open');



