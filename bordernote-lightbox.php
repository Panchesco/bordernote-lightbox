<?php
/**
 * Plugin Name: Bordernote Lightbox
 * Description: Returns JSON-formatted WordPress posts by post ID or category via AJAX.
 * Version: 1.0.0
 * Author: Richard Whitmer
 */

if (!defined('ABSPATH')) {
    exit;
}

class Bordernote_Lightbox {
    const NONCE_ACTION = 'bordernote_lightbox_nonce';

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

        add_action('wp_ajax_get_json_post', [$this, 'get_json_post']);
        add_action('wp_ajax_nopriv_get_json_post', [$this, 'get_json_post']);

        add_action('wp_ajax_get_json_category_posts', [$this, 'get_json_category_posts']);
        add_action('wp_ajax_nopriv_get_json_category_posts', [$this, 'get_json_category_posts']);
    }

    public function enqueue_scripts() {
        wp_register_script(
            'bordernote-lightbox',
            plugin_dir_url(__FILE__) . 'js/js.js',
            [],
            '1.0.0',
            true
        );

        wp_register_style(
            'bordernote-lightbox',
            plugin_dir_url(__FILE__) . 'css/style.css',
            [],
            '1.0.0',
            true
        );

        wp_localize_script('bordernote-lightbox', 'BordernoteLightbox', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce(self::NONCE_ACTION),
        ]);

        wp_enqueue_script('bordernote-lightbox');
        wp_enqueue_style('bordernote-lightbox');
    }

private function get_adjacent_posts($post_id) {
    
    global $post;
    
    $post = get_post($post_id);
    
    if($post) {
        setup_postdata($post);

        $adjacent = [
            'previous' => get_previous_post(true,'','category'),
            'next' => get_next_post(true,'','category')
        ];

    } else {
        $adjacent = ['previous' => null,'next' => null];
    }

    foreach($adjacent as $key => $entry) {

        if (function_exists('get_field_objects')) {
        $fields = get_field_objects($entry->ID);

            $adjacent[$key]->acf = $fields;
            
        }

    }

    wp_reset_postdata();
    
    return $adjacent;
    
}

private function format_post($post) {
    
    $acf_fields = [];

    if (function_exists('get_field_objects')) {
        $fields = get_field_objects($post->ID);

        if ($fields) {
            foreach ($fields as $field_name => $field) {
                $acf_fields[$field_name] = [
                    'label' => $field['label'] ?? '',
                    'name'  => $field['name'] ?? $field_name,
                    'key'   => $field['key'] ?? '',
                    'type'  => $field['type'] ?? '',
                    'value' => $field['value'] ?? null,
                ];
            }
        }
    }

    return [
        'id'        => $post->ID,
        'title'     => get_the_title($post),
        'slug'      => $post->post_name,
        'date'      => get_the_date('c', $post),
        'excerpt'   => get_the_excerpt($post),
        'content'   => apply_filters('the_content', $post->post_content),
        'link'      => get_permalink($post),
        'featured_image' => get_the_post_thumbnail_url($post, 'full'),
        'categories' => wp_get_post_categories($post->ID, [
            'fields' => 'names',
        ]),
        'acf' => $acf_fields,
        'adjacent' => $this->get_adjacent_posts($post->ID)
    ];
}

    public function get_json_post() {
        check_ajax_referer(self::NONCE_ACTION, 'nonce');

        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;

        if (!$post_id) {
            wp_send_json_error(['message' => 'Missing or invalid post ID.'], 400);
        }

        $post = get_post($post_id);

        if (!$post || $post->post_status !== 'publish') {
            wp_send_json_error(['message' => 'Post not found.'], 404);
        } 

        wp_send_json_success($this->format_post($post));
    }

    public function get_json_category_posts() {
        check_ajax_referer(self::NONCE_ACTION, 'nonce');

        $category_id = isset($_POST['category_id']) ? absint($_POST['category_id']) : 0;
        $limit = isset($_POST['limit']) ? absint($_POST['limit']) : 10;

        if (!$category_id) {
            wp_send_json_error(['message' => 'Missing or invalid category ID.'], 400);
        }

        $posts = get_posts([
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'category'       => $category_id,
            'posts_per_page' => $limit,
        ]);

        $data = array_map([$this, 'format_post'], $posts);

        wp_send_json_success($data);
    }
}

// Filter to add next and previous post ids of same category to REST response
add_filter('rest_prepare_post', function ($response, $post, $request) {

    // Skip collection requests
    if (!isset($request['id'])) {
        return $response;
    }

    global $post;
    $post = get_post($response->data['id']);
    setup_postdata($post);

    $next = get_adjacent_post(true, '', false,'category');
    $prev = get_adjacent_post(true, '', true, 'category');

    wp_reset_postdata();

    $response->data['next_post_id'] = $next ? $next->ID : null;
    $response->data['previous_post_id'] = $prev ? $prev->ID : null;

    return $response;

}, 10, 3);

new Bordernote_Lightbox();