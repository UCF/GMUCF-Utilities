<?php
/*
Plugin Name: GMUCF Utilities
Description: Feature and utility plugin for the GMUCF WordPress site.
Version: 1.0.3
Author: UCF Web Communications
License: GPL3
GitHub Plugin URI: UCF/GMUCF-Utilities
*/
namespace GMUCF\Utils;


if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GMUCF_UTILS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


require_once GMUCF_UTILS__PLUGIN_DIR . 'admin/email-admin.php';
require_once GMUCF_UTILS__PLUGIN_DIR . 'includes/email-send-functions.php';
