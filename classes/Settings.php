<?php namespace WPRecommenderIr;

defined( 'ABSPATH' ) || exit;

class Settings
{
	private $base = 'recom_';
	private $settings = array();

	public function __construct()
	{
		add_action( 'admin_menu', [&$this, 'settings_menu'] );

		add_action( 'admin_init', [&$this, 'init_settings'] );
		add_action( 'admin_init', [&$this, 'register_settings'] );
	}

	public function settings_menu()
	{
		add_menu_page( __('Recommender', 'recommender-ir'), __('Recommender', 'recommender-ir'), 'manage_options', 'recommender_settings', [&$this, 'settings_page'], null, 999 );
	}

	public function settings_page()
	{
		if( !current_user_can('manage_options') )
		wp_die(__('You do not have sufficient permissions to access this page.'));

		require_once RECOM_ASSETS_DIR.'templates/settings.php';
	}

	public function init_settings()
	{
		$this->settings = $this->settings_fields();
	}

	private function settings_fields()
	{
		$settings['recommender_setting_options'] = array(
			'title' => null,
			'fields' => array(
				array(
					'id' => 'address',
					'label' => __('IP&Port Address', 'recommender-ir'),
					'description' => __('For example:', 'recommender-ir') .' <span dir="ltr">http://192.168.0.1:1234</span>',
					'type' => 'text',
					'dir' => 'ltr'
				)
			)
		);

		$settings['recommender_advanced_setting_options'] = array(
			'title' => null,
			'fields' => array(
				array(
					'id' => 'cookie_check',
					'label' => __('Unification of Cookies', 'recommender-ir'),
					'type' => 'checkbox',
					'option' => __('Activate', 'recommender-ir'),
					'description' => __('If user is logged in with different devices, then the same cookie will be stored in each device.', 'recommender-ir')
				),
				array(
					'id' => 'active_scroll',
					'label' => __('Scroll Counter', 'recommender-ir'),
					'type' => 'checkbox',
					'option' => __('Activate', 'recommender-ir'),
					'description' => __('If user is scrolling the screen, then some rate of interest will be ingested to recommender service.', 'recommender-ir')
				),
				array(
					'id' => 'active_read',
					'label' => __('Post Reading Counter', 'recommender-ir'),
					'type' => 'checkbox',
					'option' => __('Activate', 'recommender-ir'),
					'description' => __('If user is reading the post, then some rate of interest will be ingested to recommender service.', 'recommender-ir')
				),
				array(
					'id' => 'active_cart',
					'label' => __('Add to Cart Counter', 'recommender-ir'),
					'type' => 'checkbox',
					'option' => __('Activate', 'recommender-ir'),
					'description' => __('If user is adding the product to cart, then some rate of interest will be ingested to recommender service.', 'recommender-ir')
				),
				array(
					'id' => 'active_like',
					'label' => __('Like Counter', 'recommender-ir'),
					'type' => 'checkbox',
					'option' => __('Activate', 'recommender-ir'),
					'description' => __('If user likes the post, then some rate of interest will be ingested to recommender service.', 'recommender-ir')
				),
				array(
					'id' => 'selector_like',
					'label' => __('Like Selector Path', 'recommender-ir'),
					'type' => 'text',
					'dir' => 'ltr',
					'description' => __('The selector path of like button<br>For example: <span dir="ltr">#somediv .likebox a</span>', 'recommender-ir')
				),
				array(
					'id' => 'active_share',
					'label' => __('Share Counter', 'recommender-ir'),
					'type' => 'checkbox',
					'option' => __('Activate', 'recommender-ir'),
					'description' => __('If user shared the post, then some rate of interest will be ingested to recommender service.', 'recommender-ir')
				),
				array(
					'id' => 'selector_share',
					'label' => __('Share Selector Path', 'recommender-ir'),
					'type' => 'text',
					'dir' => 'ltr',
					'description' => __('The selector path of share button<br>For example: <span dir="ltr">#somediv .sharebox a</span>', 'recommender-ir')
				),
				array(
					'id' => 'active_copy',
					'label' => __('Content Copy Counter', 'recommender-ir'),
					'type' => 'checkbox',
					'option' => __('Activate', 'recommender-ir'),
					'description' => __('If user is copy the post, then some rate of interest will be ingested to recommender service.', 'recommender-ir')
				),
				array(
					'id' => 'active_hash',
					'label' => __('Hash Counter', 'recommender-ir'),
					'type' => 'checkbox',
					'option' => __('Activate', 'recommender-ir'),
					'description' => __('Enabling this option, the user cookie will be added to all links as a hash<br>If the user share the post link, we can determine the user, then some rate of interest will be ingested to recommender service.', 'recommender-ir')
				)
			)
		);

		return $settings;
	}

	public function register_settings()
	{
		if (is_array($this->settings)) {
			foreach ($this->settings as $section => $data) {

				// Add section to page
				add_settings_section($section, $data['title'], [&$this, 'settings_section'], $section);

				foreach ($data['fields'] as $field) {

					// Validation callback for field
					$validation = '';
					if (isset($field['callback'])) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting($section, $option_name, $validation);

					// Add field to page
					add_settings_field($field['id'], $field['label'], [&$this, 'display_field'], $section, $section, ['field' => $field]);
				}
			}
		}
	}

	public function display_field($args)
	{
		$field = $args['field'];

		$html = '';
		$data = '';

		$option_name = $this->base . $field['id'];
		$option = get_option($option_name);

		if ($option)
			$data = $option;
		elseif (isset($field['default']))
			$data = $field['default'];

		switch ($field['type']) {

			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="' . $data . '" dir="' . $field['dir'] . '" class="regular-text" />' . "\n";
				break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr($field['id']) . '" rows="5" cols="50" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" dir="' . $field['dir'] . '" class="regular-text">' . $data . '</textarea><br/>' . "\n";
				break;

			case 'checkbox':
				$checked = '';
				$v = ($field['option']) ? $field['option'] : '';
				if ($option && 'on' == $option) {
					$checked = 'checked="checked"';
				}
				$html .= '<label for="' . esc_attr($field['id']) . '"><input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" ' . $checked . '/> ' . $v . '</label>' . "\n";
				break;

			case 'checkbox_multi':
				foreach ($field['options'] as $k => $v) {
					$checked = false;
					if (in_array($k, $data)) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr($field['id'] . '_' . $k) . '"><input type="checkbox" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '[]" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '" /> ' . $v . '</label>';
				}
				break;

			case 'radio':
				foreach ($field['options'] as $k => $v) {
					$checked = false;
					if ($k == $data) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr($field['id'] . '_' . $k) . '"><input type="radio" ' . checked($checked, true, false) . ' name="' . esc_attr($option_name) . '" value="' . esc_attr($k) . '" id="' . esc_attr($field['id'] . '_' . $k) . '" /> ' . $v . '</label>';
				}
				break;

			case 'select':
				$html .= '<select name="' . esc_attr($option_name) . '" id="' . esc_attr($field['id']) . '" dir="' . $field['dir'] . '" class="regular-text">';
				foreach ($field['options'] as $k => $v) {
					$selected = false;
					if ($k == $data) {
						$selected = true;
					}
					$html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '">' . $v . '</option>';
				}
				$html .= '</select>';
				break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr($option_name) . '[]" id="' . esc_attr($field['id']) . '" dir="' . $field['dir'] . '" class="regular-text" multiple="multiple">';
				foreach ($field['options'] as $k => $v) {
					$selected = false;
					if (in_array($k, $data)) {
						$selected = true;
					}
					$html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '" />' . $v . '</label> ';
				}
				$html .= '</select>';
				break;
		}

		switch ($field['type']) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><p class="description">' . $field['description'] . '</p>';
				break;

			default:
				$html .= '<p class="description"><label for="' . esc_attr($field['id']) . '">' . $field['description'] . '</label></p>' . "\n";
				break;
		}

		echo $html;
	}
}