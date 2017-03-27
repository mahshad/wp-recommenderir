<?php namespace WPRecommenderIr;

defined( 'ABSPATH' ) || exit;

use WPRecommenderIr\Helper;

class Shortcode extends Base
{
	public function __construct()
	{
		parent::__construct();

		add_shortcode( 'wp-recommenderir', [&$this, 'shortcode'] );
	}

	public function shortcode( $atts, $content = null )
	{
		extract(shortcode_atts( array(
				'method' => 'recommend',
				'how_many' => '4',
				'dither' => false,
				'radius' => null,
				'post_type' => 'post',
				'category_display' => 'false',
				'category_label' => __('Category:', 'recommender-ir'),
				'include_title' => 'true',
				'include_excerpt' => 'false',
				'excerpt_length' => 'false',
				'excerpt_more_link' => 'false',
				'excerpt_more' => __('Read more...', 'recommender-ir'),
				'include_date' => 'true',
				'include_time' => 'true',
				'date_format' => get_option( 'date_format' ),
				'time_format' => get_option( 'time_format' ),
				'image_size' => 'thumbnail',
				'columns' => 2,
				'style' => 'recom-custom-style',
				'post_ids' => ''
			), $atts ));

		$function = "get_{$method}";
		$dither = filter_var($dither, FILTER_VALIDATE_BOOLEAN);

		$post_ids = $this->recommender->{$function}( ['howMany' => $how_many, 'dither' => $dither, 'radius' => $radius, 'post_ids' => $post_ids] );
		$id = implode( ',', $post_ids );

		$display_shortcode = do_shortcode( '[display-posts post_type="'.$post_type.'" posts_per_page="'.$how_many.'" id="'.$id.'" category_display="'.$category_display.'" category_label="'.$category_label.'" include_title="'.$include_title.'" include_excerpt="'.$include_excerpt.'" include_date="'.$include_date.'" date_format="'.$date_format.'" include_time="'.$include_time.'" time_format="'.$time_format.'" image_size="'.$image_size.'" excerpt_length="'.$excerpt_length.'" excerpt_more="'.$excerpt_more.'" excerpt_more_link="'.$excerpt_more_link.'" columns="'.$columns.'" style="'.$style.'"]' );

		if( $this->active_hash )
			return Helper::add_hash_to_links( $display_shortcode, $this->hash );

		return $display_shortcode;
	}
}