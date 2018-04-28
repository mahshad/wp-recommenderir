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
		if( $this->recom_address )
		{
			$args = '';

			if( !empty( $parameters ) )
			{
				$param = [];
				foreach( $parameters as $pkey => $pvalue )
					$param[] = $pkey . '=' . $pvalue;

				$param = implode( '&', $param );
				$args = '?' . $param;
			}

			$url = $this->recom_address . $method . $args;
			$options = [ 'timeout' => 120 ];

			$response = wp_remote_get( $url, $options );

			if( is_wp_error( $response ) )
				return null;

			if( !empty( $response['body'] ) )
				return json_decode( $response['body'] );

			return true;
		}

		return null;
	}

	public function post_ingest( $args )
	{
		$body = implode( ',', $args );
		$url = $this->recom_address . 'ingest';
		$options = [ 'method' => 'POST', 'timeout' => 120, 'body' => $body ];

		wp_remote_post( $url, $options );

		return null;
	}

	public function get_recommend( $args )
	{
		extract( $args );

		$method = 'recommend/'.$this->user_id;
		$options = compact( 'howMany', 'dither', 'radius' );

		$recommends = $this->recommender_method( $method, $options );

		return Helper::get_ids( $recommends );
	}

	public function get_similarity( $args )
	{
		global $post;
		extract( $args );

		if( !$post->ID && !$post_ids ) return;

		$item = ( $post_ids ) ? explode( ',', $post_ids ) : [ $post->ID ];
		array_walk( $item, function( &$value ) { $value = 'wp-'.$value; } );

		$item = implode( '/', $item );

		$method = 'similarity/'.$item;
		$options = compact( 'howMany' );

		$similarities = $this->recommender_method( $method, $options );

		return Helper::get_ids( $similarities );
	}

	public function get_trendShortTime( $args )
	{
		extract( $args );

		$method = 'trendShortTime';
		$options = compact( 'howMany' );

		$trends = $this->recommender_method( $method, $options );

		return Helper::get_ids( $trends );
	}

	public function get_trendLongTime( $args )
	{
		extract( $args );

		$method = 'trendLongTime';
		$options = compact( 'howMany' );

		$trends = $this->recommender_method( $method, $options );

		return Helper::get_ids( $trends );
	}

	public function get_termBasedRecommendInclusive( $args )
	{
		global $post;
		extract( $args );

		if( !$post->ID && !$post_ids ) return;

		$item = ( $post_ids ) ? explode( ',', $post_ids ) : [ $post->ID ];

		$post_id = $item[0];
		$terms = Helper::get_terms( $post_id );

		if( !is_array( $terms ) ) return;

		$terms = array_slice( $terms, 0, 3 ); // Recommender.ir needs only 3 items
		$terms = Helper::change_array_dash_to_underline( $terms );

		$term = implode( '/', $terms );

		$method = 'termBasedRecommendInclusive/'.$this->user_id.'/'.$term;
		$options = compact( 'dither', 'radius' );

		$recommends = $this->recommender_method( $method, $options );

		$items = Helper::get_ids( $recommends );

		return array_slice($items, 0, $howMany);
	}

	public function get_termBasedSimilarityInclusive( $args )
	{
		global $post;
		extract( $args );

		if( !$post->ID && !$post_ids ) return;

		$item = ( $post_ids ) ? explode( ',', $post_ids ) : [ $post->ID ];

		$post_id = $item[0];
		$terms = Helper::get_terms( $post_id );

		if( !is_array( $terms ) ) return;

		$terms = array_slice( $terms, 0, 3 ); // Recommender.ir needs only 3 items
		$terms = Helper::change_array_dash_to_underline( $terms );

		$item = 'wp-'.$post_id;
		$term = implode( '/', $terms );

		$method = 'termBasedSimilarityInclusive/'.$item.'/'.$term;

		$similarities = $this->recommender_method( $method );

		$items = Helper::get_ids( $similarities );

		return array_slice($items, 0, $howMany);
	}

	public function termItemAdd( $args )
	{
		extract( $args );

		if( empty( $terms ) || empty( $post_id ) ) return;

		$item = 'wp-'.$post_id;

		$terms = Helper::change_array_dash_to_underline( $terms );
		$term = implode( '/', $terms );

		$method = 'termItemAdd/'.$item.'/'.$term;
		$options = [];

		if( !empty( $overwrite ) ) $options['overwrite'] = $overwrite;
		if( !empty( $nocheck ) ) $options['nocheck'] = $nocheck;

		$result = $this->recommender_method( $method, $options );

		return $result;
	}

	public function termItemList( $args )
	{
		extract( $args );

		if( empty( $post_id ) ) return;

		$item = 'wp-'.$post_id;

		$method = 'termItemList/'.$item;
		$options = [];

		if( !empty( $nocheck ) ) $options['nocheck'] = $nocheck;

		$terms_list = $this->recommender_method( $method, $options );

		if( !empty( $terms_list ) )
			$terms_list = Helper::change_array_underline_to_dash( $terms_list );

		return $terms_list;
	}

	public function termItemRemove( $args )
	{
		extract( $args );
		$terms = $removed_terms;

		if( empty( $terms ) || empty( $post_id ) ) return;

		$item = 'wp-'.$post_id;

		$terms = Helper::change_array_dash_to_underline( $terms );
		$term = implode( '/', $terms );

		$method = 'termItemRemove/'.$item.'/'.$term;
		$options = [];

		if( !empty( $nocheck ) ) $options['nocheck'] = $nocheck;

		$result = $this->recommender_method( $method, $options );

		return $result;
	}
}