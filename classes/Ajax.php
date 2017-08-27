<?php namespace WPRecommenderIr;

defined( 'ABSPATH' ) || exit;

use WPRecommenderIr\Helper;

class Ajax extends Base
{
	public function __construct()
	{
		parent::__construct();

		add_action( 'wp_ajax_recommender_ingest', [&$this, 'ingest'] );
		add_action( 'wp_ajax_nopriv_recommender_ingest', [&$this, 'ingest'] );
	}

	function ingest()
	{
		$data = $_POST;

		if( ! wp_verify_nonce( $data['nonce'], 'recom-ajax-nonce' ) )
			wp_send_json_error();

		$type = $data['type'];
		$item = $data['item'];
		$user = $data['user'];

		if( !isset($type, $item) || !isset($this->recom_address) ) return;

		$user_id = ( $this->active_hash && $user != $this->user_id && $type == 'hash' ) ? $user : $this->user_id;

		$id = $user_id;
		$url = "'wp-{$item}'";
		$value = 0;

		foreach($type as $t)
			$value += $this->{$t.'_rate'};

		$result = $this->recommender->post_ingest( compact('id', 'url', 'value') );

		wp_send_json_success( compact('result') );
	}
}