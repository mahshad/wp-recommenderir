<?php namespace WPRecommenderIr;

defined( 'ABSPATH' ) || exit;

use WPRecommenderIr\Helper,
	WPRecommenderIr\Recommender;

class Base
{
	protected $recommender;
	protected $recom_address;
	protected $user_id;
	protected $hash;

	protected $send_tags = false;
	protected $active_scroll = false;
	protected $active_read = false;
	protected $active_cart = false;
	protected $active_like = false;
	protected $active_share = false;
	protected $active_copy = false;
	protected $active_hash = false;
	protected $selector_like = '';
	protected $selector_share = '';

	protected $view_rate = 1;
	protected $scroll_rate = 5;
	protected $read_rate = 10;
	protected $cart_rate = 15;
	protected $like_rate = 20;
	protected $share_rate = 25;
	protected $copy_rate = 25;
	protected $hash_rate = 25;

	public function __construct()
	{
		$this->hash = Helper::get_userID_hash();
		$this->user_id = Helper::get_userID();

		$this->recom_address = trailingslashit( Helper::add_http(get_option('recom_address')) );
		$this->recommender = new Recommender( $this->recom_address, $this->user_id );

		$array = [ 'send_tags', 'active_scroll', 'active_read', 'active_cart', 'active_like', 'active_share', 'active_copy', 'active_hash', 'selector_like', 'selector_share' ];

		foreach($array as $arr)
		{
			if( get_option( 'recom_'.$arr ) )
				$this->{$arr} = get_option( 'recom_'.$arr );
		}
	}
}