<?php

if (!defined('ABSPATH')) exit;

class class_acf_field_image_annotation extends acf_field
{

	/**
	 * Set name / label needed for actions / filters
	 *
	 * @param $settings
	 */
	public function __construct($settings)
	{

		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/
		$this->name = 'image_annotation';

		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/
		$this->label = __('Image Annotation', 'TEXTDOMAIN');

		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/
		$this->category = 'content';

		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/
		$this->defaults = [
			'font_size'	=> 14,
		];

		$this->settings = $settings;

		parent::__construct();
	}


	/**
	 * Create extra settings for your field. These are visible when editing a field
	 *
	 * @param $field
	 */
	public function render_field_settings($field)
	{

		// return format
		acf_render_field_setting($field, [
			'label'        =>  __('Return Value', 'acf-annotation'),
			'instructions' =>  __('Specify the returned value on front end', 'acf-annotation'),
			'type'         => 'radio',
			'name'         => 'return_format',
			'layout'       => 'horizontal',
			'choices'      => [
				'array' => __('Image Array', 'acf-annotation'),
				'url'   => __('Image URL', 'acf-annotation'),
				'id'    => __('Image ID', 'acf-annotation')
			]
		]);
	}

	/**
	 * Create the HTML interface for your field
	 *
	 * @param $field
	 */
	public function render_field($field)
	{

		global $post;

		if (empty($field['value'])) {
			echo '<div id="' . esc_attr($field['key']) . '" class="acf-image-annotation-field"><image-annotation post-id="' . $post->ID . '" field-name="' . esc_attr($field['name']) . '" field-key="' . $field['key'] . '"></image-annotation></div>';
			return;
		}



		$value = htmlentities(json_encode($field['value'], JSON_HEX_QUOT), ENT_QUOTES);
		echo "<div id='" . esc_attr($field['key']) . "' class='acf-image-annotation-field'><image-annotation post-id='" . $post->ID . "' field-name='" . esc_attr($field['name']) . "' field-key='" . $field['key'] . "' :field-data='" . $value . "'></image-annotation></div>";
	}

	/**
	 * This filter is applied to the $value after it is loaded from the db
	 *
	 * @param $value
	 * @param $post_id
	 * @param $field
	 *
	 * @return  $value
	 */
	public function load_value($value, $post_id, $field)
	{
		if (is_admin()) {
			return $value;
		}

		$data = maybe_unserialize($value);
		return json_decode($data);
	}

	/**
	 * This filter is applied to the $value before it is saved in the db
	 *
	 * @param $value
	 * @param $post_id
	 * @param $field
	 *
	 * @return mixed
	 */
	function update_value($value, $post_id, $field)
	{
		return maybe_serialize($value);
	}

	/**
	 * This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	 * Use this action to add CSS + JavaScript to assist your render_field() action.
	 *
	 */
	public function input_admin_enqueue_scripts()
	{

		$url = $this->settings['url'];
		$version = $this->settings['version'];

		// register & include JS
		wp_register_script('acf_annotate_image', "{$url}assets/js/app.js", ['acf-input'], $version);
		wp_enqueue_script('acf_annotate_image');
	}

	public function dd()
	{
		$vars = func_get_args();
		echo '<pre>';
		array_map(function ($var) {
			print_r($var);
			echo '<br>';
		}, $vars);
		echo '</pre>';
		die();
	}
}

new class_acf_field_image_annotation($this->settings);
