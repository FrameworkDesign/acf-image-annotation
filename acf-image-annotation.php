<?php

/*
Plugin Name: Advanced Custom Fields: Image Annotation
Plugin URI: https://weareframework.co.uk/
Description: simple acf add-on for image annotation field type
Version: 1.0.0
Author: The Framework Web Development Limited
Author URI: https://weareframework.co.uk/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists('acf_plugin_image_annotation')) {

	class acf_plugin_image_annotation {

		public $settings;

		public function __construct() {

			$this->settings = [
				'version' => '1.0.0',
				'url'     => plugin_dir_url( __FILE__ ),
				'path'    => plugin_dir_path( __FILE__ )
			];

			add_action( 'acf/include_field_types', [ $this, 'include_field' ] );
		}

		public function include_field() {
			// load text domain
			load_plugin_textdomain( 'TEXTDOMAIN', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );

			// include
			include_once( 'fields/class-acf-field-image-annotation.php');
		}
	}

	new acf_plugin_image_annotation();
}