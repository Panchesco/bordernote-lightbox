<?php
// Ensure this file is being included by WordPress.

defined('ABSPATH') or die();

class Bordernote_Video_Cover extends WP_Widget {

    // Constructor

    function __construct() {

        parent::__construct(

            'bordernote_video_cover', // Base ID

            __('Bordernote - Video Cover', 'bordernote'), // Name

            array('description' => __('Adds a full-screen video cover to the Bordernote theme.', 'text_domain'),) // Args

        );

    }

    // The widget() method to display the widget content on the front-end

    public function widget($args, $instance) {

        echo $args['before_widget'];

        if (!empty($instance['title'])) {

            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];

        }

        echo __('[video cover]', 'bordernote');

        echo $args['after_widget'];

    }

    // The form() method to create the widget settings form in the admin area

    public function form($instance) {

        $title = !empty($instance['title']) ? $instance['title'] : __('New title', 'bordernote');

        ?>

        <p>

        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>

        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">

        </p>

        <?php

    }

    // The update() method to save the widget settings

    public function update($new_instance, $old_instance) {

        $instance = array();

        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

        return $instance;

    }

}

