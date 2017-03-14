<?php namespace WPRecommenderIr;

defined( 'ABSPATH' ) || exit;

use WPRecommenderIr\Helper;

class Recommender
{
	protected $recom_address;
	protected $user_id;

	public function __construct( $recommender_address, $user_id )
	{
		$this->recom_address = $recommender_address;
		$this->user_id = $user_id;
	}

	public function recommender_method( $method, $parameters = [] )
	{
		$param = [];

		foreach( $parameters as $pkey => $pvalue )
			$param[] = $pkey . '=' . $pvalue;

		$param = implode( '&', $param );

		if( $this->recom_address )
		{
			$url = $this->recom_address . $method . '?' . $param;
			$options = ['timeout' => 120];

			$response = wp_remote_get( $url, $options );

			if( isset($response['body']) )
				return json_decode( $response['body'] );
		}

		return null;
	}

	public function post_ingest( $args )
	{
		$body = implode(',', $args);
		$url = $this->recom_address . 'ingest';
		$options = ['method' => 'POST', 'timeout' => 120, 'body' => $body];

		wp_remote_post( $url, $options );

		return null;
	}

	public function get_recommend( $args )
	{
		extract( $args );

		$method = 'recommend/'.$this->user_id;
		$options = compact('howMany', 'dither', 'radius');

		$recommends = $this->recommender_method( $method, $options );

		return Helper::get_ids( $recommends );
	}

	public function get_similarity( $args )
	{
		global $post;
		extract( $args );

		if( !$post->ID && !$post_ids ) return;

		$item = ( $post_ids ) ? explode( ',', $post_ids ) : [ $post->ID ];
		array_walk($item, function(&$value) { $value = 'wp-'.$value; });

		$item = implode('/', $item);

		$method = 'similarity/'.$item;
		$options = compact('howMany');

		$similarities = $this->recommender_method( $method, $options );

		return Helper::get_ids( $similarities );
	}

	public function get_trendShortTime( $args )
	{
		extract( $args );

		$method = 'trendShortTime';
		$options = compact('howMany');

		$trends = $this->recommender_method( $method, $options );

		return Helper::get_ids( $trends );
	}

	public function get_trendLongTime( $args )
	{
		extract( $args );

		$method = 'trendLongTime';
		$options = compact('howMany');

		$trends = $this->recommender_method( $method, $options );

		return Helper::get_ids( $trends );
	}
}