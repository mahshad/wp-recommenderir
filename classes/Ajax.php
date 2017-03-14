<?php namespace WPRecommenderIr;

defined( 'ABSPATH' ) || exit;

use WPRecommenderIr\Helper;

class Ajax extends Base
{

	public function __construct()
	{
		parent::__construct();

		add_action( 'wp_ajax_recommender_ajax', [&$this, 'recommender_ajax'] );
		add_action( 'wp_ajax_nopriv_recommender_ajax', [&$this, 'recommender_ajax'] );
	}

	function recommender_ajax()
	{
		if( ! wp_verify_nonce( $_POST['nonce'], 'recom-ajax-nonce' ) )
			wp_send_json_error();

		$type = $_POST['type'];
		$item = $_POST['item'];
		$user = $_POST['user'];

		if( !isset($type, $item) || !isset($this->recom_address) ) return;

		$user_id = ( $this->active_hash && $user != $this->user_id && $type == 'hash' ) ? $user : $this->user_id;

		$result = null;

		$id = $user_id;
		$url = "'wp-{$item}'";
		$value = $this->{$type.'_rate'};

		$result = $this->recommender->post_ingest( compact('id', 'url', 'value') );

		wp_send_json_success( compact('result') );
	}
}