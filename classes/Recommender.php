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
	        $response = wp_remote_get( $url, ['timeout' => 120]);

	        return json_decode( $response['body'] );
	    }

	    return null;
	}

	public function get_recommend( $args )
	{
	    extract( $args );

	    $recommends = $this->recommender_method( 'recommend/'.$this->user_id, compact('howMany', 'dither', 'radius') );

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

	    $similarities = $this->recommender_method( 'similarity/'.$item, compact('howMany') );

	    return Helper::get_ids( $similarities );
	}

	public function get_trendShortTime( $args )
	{
	    extract( $args );

	    $trends = $this->recommender_method( 'trendShortTime', compact('howMany') );

	    return Helper::get_ids( $trends );
	}

	public function get_trendLongTime( $args )
	{
	    extract( $args );

	    $trends = $this->recommender_method( 'trendLongTime', compact('howMany') );

	    return Helper::get_ids( $trends );
	}
}