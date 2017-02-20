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
		add_menu_page( 'رکامندر', 'رکامندر', 'manage_options', 'recommender_settings', [&$this, 'settings_page'], null, 999 );
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
					'label' => 'آدرس سرور',
					'description' => 'مثلا <span dir="ltr">http://185.83.114.53:8090</span>',
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
					'label' => 'یکسان سازی کوکی کاربر در دستگاه های مختلف',
					'type' => 'checkbox',
					'option' => 'فعال',
					'description' => 'اگر کاربر با چند سیستم در سایت لاگین کند، در هر سیستم کوکی یکسانی ذخیره می‌شود'
				),
				array(
					'id' => 'active_scroll',
					'label' => 'محسابه‌گر اسکرول',
					'type' => 'checkbox',
					'option' => 'فعال',
					'description' => 'اگر کاربر صفحه را اسکرول کند، میزانی از علاقه‌مندی برای او ثبت می‌شود'
				),
				array(
					'id' => 'active_read',
					'label' => 'محسابه‌گر خواندن مطلب',
					'type' => 'checkbox',
					'option' => 'فعال',
					'description' => 'اگر کاربر درحال خواندن مطلب باشد، میزانی از علاقه‌مندی برای او ثبت می‌شود'
				),
				array(
					'id' => 'active_cart',
					'label' => 'محسابه‌گر سبد خرید',
					'type' => 'checkbox',
					'option' => 'فعال',
					'description' => 'اگر کاربر محصولی به سبد خرید اضافه کند، میزانی از علاقه‌مندی برای او ثبت می‌شود'
				),
				array(
					'id' => 'active_like',
					'label' => 'محسابه‌گر لایک',
					'type' => 'checkbox',
					'option' => 'فعال',
					'description' => 'اگر کاربر اقدام به لایک مطلب کند، میزانی از علاقه‌مندی برای او ثبت می‌شود'
				),
				array(
					'id' => 'selector_like',
					'label' => 'سلکتور لایک',
					'type' => 'text',
					'dir' => 'ltr',
					'description' => 'مسیر سلکتور دکمه لایک<br>مثلا <span dir="ltr">#somediv .likebox a</span>'
				),
				array(
					'id' => 'active_share',
					'label' => 'محسابه‌گر اشتراک‌گذاری',
					'type' => 'checkbox',
					'option' => 'فعال',
					'description' => 'اگر کاربر اقدام به اشتراک‌گذاری مطلب کند، میزانی از علاقه‌مندی برای او ثبت می‌شود'
				),
				array(
					'id' => 'selector_share',
					'label' => 'سلکتور اشتراک‌گذاری',
					'type' => 'text',
					'dir' => 'ltr',
					'description' => 'مسیر سلکتور دکمه اشتراک‌گذاری<br>مثلا <span dir="ltr">#somediv .sharebox a</span>'
				),
				array(
					'id' => 'active_copy',
					'label' => 'محسابه‌گر کپی',
					'type' => 'checkbox',
					'option' => 'فعال',
					'description' => 'اگر کاربر اقدام به کپی از مطلب کند، میزانی از علاقه‌مندی برای او ثبت می‌شود'
				),
				array(
					'id' => 'active_hash',
					'label' => 'محسابه‌گر هش',
					'type' => 'checkbox',
					'option' => 'فعال',
					'description' => 'با فعال‌سازی این گزینه به تمام لینک ها کد شناسایی کاربر اضافه خواهد شد<br>در صورتی که کاربر لینکی را برای جایی بفرستد می‌توان فهمید کدام کاربر بوده و میزانی از علاقه‌مندی را برایش ثبت کرد'
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