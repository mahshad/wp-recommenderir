<?php namespace WPRecommenderIr;

defined( 'ABSPATH' ) || exit;

use WPRecommenderIr\Helper,
	WPRecommenderIr\Ajax,
	WPRecommenderIr\DisplayPostsShortcode,
	WPRecommenderIr\Shortcode,
	WPRecommenderIr\Widget,
	WPRecommenderIr\Settings;

class Init extends Base
{
	public static function instance()
	{
		$class = __CLASS__;
		new $class;
	}

	public function __construct()
	{
		parent::__construct();

		add_action( 'init', [&$this, 'init'] );
		add_action( 'init', [&$this, 'init_i18n_support'] );
		add_action( 'wp_enqueue_scripts', [&$this, 'scripts'] );
		add_action( 'admin_enqueue_scripts', [&$this, 'admin_scripts'] );
		add_action( 'wp_head', [&$this, 'buffer_start'] );
		add_action( 'wp_footer', [&$this, 'buffer_end'] );

		if( !empty( $this->send_tags ) )
		{
			add_action( 'save_post', [&$this, 'add_terms_admin_side'] );
			add_action( 'wp', [&$this, 'add_terms_user_side'] );
		}

		if ( class_exists( 'WooCommerce' ) )
			add_filter( 'woocommerce_add_cart_item_data', [&$this, 'woocommerce_add_to_cart'], 99, 2 );

		add_action( 'widgets_init', function(){ register_widget( 'WPRecommenderIr\Widget' ); });

		add_filter( "plugin_action_links_".RECOM_PLUGIN, [&$this, 'plugin_settings_link'] );
	}

	public function activation()
	{
		$array = [ 'active_scroll', 'active_read', 'active_cart', 'active_copy' ];

		foreach($array as $arr)
		{
			if( !get_option( 'recom_'.$arr ) )
				add_option( 'recom_'.$arr, '1' );
		}
	}

	function plugin_settings_link( $links )
	{
		$url = get_admin_url() . 'admin.php?page=recommender_settings';
		$settings_link = '<a href="'.$url.'">'.__('Settings page', 'recommender').'</a>'; 
		array_unshift( $links, $settings_link );

		return $links; 
	}

	public function init()
	{
		new Ajax();
		new DisplayPostsShortcode();
		new Shortcode();
		new Settings();
	}

	public function init_i18n_support()
	{
		load_plugin_textdomain( 'recommender', false, dirname( RECOM_PLUGIN ) . '/langs' );
	}

	public function scripts()
	{
		global $post;

		$recom = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'recom-ajax-nonce' ),
			'item' => ''
		);

		wp_register_script( 'recommender-reading-time', RECOM_ASSETS_URL.'js/reading-time.min.js', null );
		wp_register_script( 'recommender-script', RECOM_ASSETS_URL.'js/scripts.min.js', ['recommender-reading-time'] );

		if( is_single() )
		{
			$recom = array_merge($recom, [
						'item' => $post->ID,
						'scroll' => $this->active_scroll,
						'read' => $this->active_read,
						'cart' => $this->active_cart,
						'like' => $this->active_like,
						'like_selector' => $this->selector_like,
						'share' => $this->active_share,
						'share_selector' => $this->selector_share,
						'copy' => $this->active_copy,
						'hash' => $this->active_hash,
					]);
		}

		wp_enqueue_script( 'recommender-reading-time' );
		wp_localize_script( 'recommender-script', 'recom', $recom );
		wp_enqueue_script( 'recommender-script' );
	}

	public function admin_scripts() {
		wp_register_script( 'recommender-admin-script', RECOM_ASSETS_URL.'js/admin-scripts.js', array('jquery') );

		wp_enqueue_script( 'recommender-admin-script' );
	}

	public function callback($buffer) {
		if( $this->active_hash )
			return Helper::add_hash_to_links( $buffer, $this->hash );

		return $buffer;
	}

	public function buffer_start() { ob_start([&$this, 'callback']); }

	public function buffer_end() { ob_end_flush(); }

	public function woocommerce_add_to_cart( $cart_item_key , $product_id )
	{
		$result = null;

		$id = $this->user_id;
		$url = "'wp-{$product_id}'";
		$value = $this->cart_rate;
		
		if( $this->active_cart )
			$result = $this->recommender->post_ingest( compact('id', 'url', 'value') );

		return $result;
	}

	public function add_terms_admin_side( $post_id )
	{
		if( $this->send_tags )
		{
			$nocheck = true;

			$get_terms = Helper::get_terms( $post_id );

			if( !is_array( $get_terms ) ) return;

			$terms = array_slice( $get_terms, 0, 3 ); // Recommender.ir needs only 3 items

			$list = $this->recommender->termItemList( compact( 'post_id', 'nocheck' ) );
			if( !empty( $list ) )
			{
				$removed_terms = array_diff( $list, $terms );
				$terms = array_diff( $terms, $list );

				$this->recommender->termItemRemove( compact( 'post_id', 'removed_terms', 'nocheck' ) );
			}

			$result = $this->recommender->termItemAdd( compact( 'post_id', 'terms', 'nocheck' ) );

			update_post_meta( $post_id, 'recom_tags_is_send', $result );
		}
	}

	public function add_terms_user_side()
	{
		if( is_single() )
		{
			$queried_object = get_queried_object();
			$post_id = $queried_object->ID;
			$meta_key = 'recom_tags_is_send';

			$tags_send = get_post_meta( $post_id, $meta_key, true );
			if( empty( $tags_send ) )
			{
				$nocheck = true;

				$get_terms = Helper::get_terms( $post_id );

				if( !is_array( $get_terms ) ) return;

				$terms = array_slice( $get_terms, 0, 3 ); // Recommender.ir needs only 3 items

				$result = $this->recommender->termItemAdd( compact( 'post_id', 'terms', 'nocheck' ) );
				
				if( $result )
					update_post_meta( $post_id, $meta_key, $result );
			}
		}
	}
}