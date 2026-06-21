<?php
/*

Plugin Name: Bordernote Video Cover

Description: Adds a full-screen video cover to the Bordernote theme.

Version: 1.0

Author: Richard Whitmer

*/

// Ensure this file is being included by WordPress.

defined('ABSPATH') or die();

define('BORDERNOTE_PLUGIN_DIR', WP_PLUGIN_DIR . '/bordernote-video-cover');

require_once(BORDERNOTE_PLUGIN_DIR . '/class_bordernote-video-cover.php');
require_once(BORDERNOTE_PLUGIN_DIR . '/functions.php');

