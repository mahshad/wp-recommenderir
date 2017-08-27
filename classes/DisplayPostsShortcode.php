<?php namespace WPRecommenderIr;

defined( 'ABSPATH' ) || exit;
/**
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package Display Posts
 * @version 2.7.0
 * @author Bill Erickson <bill@billerickson.net>
 * @copyright Copyright (c) 2011, Bill Erickson
 * @link http://www.billerickson.net/shortcode-to-display-posts/
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
 
 
/**
 * To Customize, use the following filters:
 * @link https://github.com/billerickson/display-posts-shortcode/wiki#customization-with-filters
 */ 
 
// Create the shortcode
Class DisplayPostsShortcode
{
	public function __construct()
	{
		add_shortcode( 'display-posts', [&$this, 'be_display_posts_shortcode'] );
		add_filter( 'display_posts_shortcode_post_class', [&$this, 'be_dps_column_classes'], 10, 5 );
		add_action( 'wp_enqueue_scripts', [&$this, 'be_dps_column_class_styles'] );
	}

	function be_display_posts_shortcode( $atts ) {
		// Original Attributes, for filters
		$original_atts = $atts;
		// Pull in shortcode attributes and set defaults
		$atts = shortcode_atts( array(
			'title'                => '',
			'author'               => '',
			'category'             => '',
			'category_display'     => '',
			'category_label'       => 'Posted in: ',
			'content_class'        => 'content',
			'date_format'          => 'Y/j/n',
			'time_format'          => 'H:i',
			'date'                 => '',
			'date_column'          => 'post_date',
			'date_compare'         => '=',
			'display_posts_off'    => false,
			'excerpt_length'       => false,
			'excerpt_more'         => false,
			'excerpt_more_link'    => false,
			'exclude_current'      => false,
			'id'                   => false,
			'ignore_sticky_posts'  => false,
			'image_size'           => false,
			'include_title'        => true,
			'include_author'       => false,
			'include_content'      => false,
			'include_date'         => false,
			'include_time'         => false,
			'include_excerpt'      => false,
			'meta_key'             => '',
			'meta_value'           => '',
			'no_posts_message'     => '',
			'offset'               => 0,
			'order'                => 'DESC',
			'orderby'              => 'date',
			'post_parent'          => false,
			'post_status'          => 'publish',
			'post_type'            => 'post',
			'posts_per_page'       => '10',
			'tag'                  => '',
			'tax_operator'         => 'IN',
			'tax_term'             => false,
			'taxonomy'             => false,
			'wrapper'              => 'ul',
			'wrapper_class'        => 'recom-posts-listing',
			'wrapper_id'           => false,
			'style'                => '',
		), $atts, 'display-posts' );
		
		// End early if shortcode should be turned off
		if( $atts['display_posts_off'] )
			return;
		$shortcode_title = sanitize_text_field( $atts['title'] );
		$author = sanitize_text_field( $atts['author'] );
		$category_display = ('true' == $atts['category_display']) ? 'category' : sanitize_text_field( $atts['category_display'] );
		$category_label = sanitize_text_field( $atts['category_label'] );
		$content_class = array_map( 'sanitize_html_class', ( explode( ' ', $atts['content_class'] ) ) );
		$date_format = sanitize_text_field( $atts['date_format'] );
		$date = sanitize_text_field( $atts['date'] );
		$date_column = sanitize_text_field( $atts['date_column'] );
		$date_compare = sanitize_text_field( $atts['date_compare'] );
		$excerpt_length = sanitize_text_field( $atts['excerpt_length'] );
		$excerpt_more = sanitize_text_field( $atts['excerpt_more'] );
		$excerpt_more_link = sanitize_text_field( $atts['excerpt_more_link'] );
		$exclude_current = $this->be_display_posts_bool( $atts['exclude_current'] );
		$id = $atts['id']; // Sanitized later as an array of integers
		$ignore_sticky_posts = $this->be_display_posts_bool( $atts['ignore_sticky_posts'] );
		$image_size = sanitize_key( $atts['image_size'] );
		$include_title = $this->be_display_posts_bool( $atts['include_title'] );
		$include_author = $this->be_display_posts_bool( $atts['include_author'] );
		$include_content = $this->be_display_posts_bool( $atts['include_content'] );
		$include_date = $this->be_display_posts_bool( $atts['include_date'] );
		$include_time = $this->be_display_posts_bool( $atts['include_time'] );
		$include_excerpt = $this->be_display_posts_bool( $atts['include_excerpt'] );
		$meta_key = sanitize_text_field( $atts['meta_key'] );
		$meta_value = sanitize_text_field( $atts['meta_value'] );
		$no_posts_message = sanitize_text_field( $atts['no_posts_message'] );
		$offset = intval( $atts['offset'] );
		$order = sanitize_key( $atts['order'] );
		$orderby = sanitize_key( $atts['orderby'] );
		$post_parent = $atts['post_parent']; // Validated later, after check for 'current'
		$post_status = $atts['post_status']; // Validated later as one of a few values
		$post_type = sanitize_text_field( $atts['post_type'] );
		$posts_per_page = intval( $atts['posts_per_page'] );
		$tag = sanitize_text_field( $atts['tag'] );
		$tax_operator = $atts['tax_operator']; // Validated later as one of a few values
		$tax_term = sanitize_text_field( $atts['tax_term'] );
		$taxonomy = sanitize_key( $atts['taxonomy'] );
		$wrapper = sanitize_text_field( $atts['wrapper'] );
		$wrapper_class = sanitize_html_class( $atts['wrapper_class'] );
		if( !empty( $wrapper_class ) )
			$wrapper_class = ' class="' . $wrapper_class . '"';
		$wrapper_id = sanitize_html_class( $atts['wrapper_id'] );
		if( !empty( $wrapper_id ) )
			$wrapper_id = ' id="' . $wrapper_id . '"';
		$style = sanitize_html_class( $atts['style'] );
		
		// Set up initial query for post
		$args = array(
			'order'               => $order,
			'orderby'             => $orderby,
			'posts_per_page'      => $posts_per_page
		);

		if( $category )
			$args['category_name'] = $category;

		if( $post_type )
			$post_type = explode( ',', $post_type );

		$args['post_type'] = $post_type;

		if( $tag )
			$args['tag'] = $tag;
		
		// Ignore Sticky Posts
		if( $ignore_sticky_posts )
			$args['ignore_sticky_posts'] = true;
		
		// Meta key (for ordering)
		if( !empty( $meta_key ) )
			$args['meta_key'] = $meta_key;
		
		// Meta value (for simple meta queries)
		if( !empty( $meta_value ) )
			$args['meta_value'] = $meta_value;
			
		// If Post IDs
		if( $id ) {
			$posts_in = array_map( 'intval', explode( ',', $id ) );
			$args['post__in'] = $posts_in;
		}
		
		// If Exclude Current
		if( $exclude_current )
			$args['post__not_in'] = array( get_the_ID() );
		
		// Post Author
		if( !empty( $author ) )
			$args['author_name'] = $author;
			
		// Offset
		if( !empty( $offset ) )
			$args['offset'] = $offset;
		
		// Post Status	
		$post_status = explode( ', ', $post_status );		
		$validated = array();
		$available = array( 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash', 'any' );
		foreach ( $post_status as $unvalidated )
			if ( in_array( $unvalidated, $available ) )
				$validated[] = $unvalidated;
		if( !empty( $validated ) )		
			$args['post_status'] = $validated;
		
		
		// If taxonomy attributes, create a taxonomy query
		if ( !empty( $taxonomy ) && !empty( $tax_term ) ) {
		
			// Term string to array
			$tax_term = explode( ', ', $tax_term );
			
			// Validate operator
			if( !in_array( $tax_operator, array( 'IN', 'NOT IN', 'AND' ) ) )
				$tax_operator = 'IN';
						
			$tax_args = array(
				'tax_query' => array(
					array(
						'taxonomy' => $taxonomy,
						'field'    => 'slug',
						'terms'    => $tax_term,
						'operator' => $tax_operator
					)
				)
			);
			
			// Check for multiple taxonomy queries
			$count = 2;
			$more_tax_queries = false;
			while( 
				isset( $original_atts['taxonomy_' . $count] ) && !empty( $original_atts['taxonomy_' . $count] ) && 
				isset( $original_atts['tax_' . $count . '_term'] ) && !empty( $original_atts['tax_' . $count . '_term'] ) 
			):
			
				// Sanitize values
				$more_tax_queries = true;
				$taxonomy = sanitize_key( $original_atts['taxonomy_' . $count] );
		 		$terms = explode( ', ', sanitize_text_field( $original_atts['tax_' . $count . '_term'] ) );
		 		$tax_operator = isset( $original_atts['tax_' . $count . '_operator'] ) ? $original_atts['tax_' . $count . '_operator'] : 'IN';
		 		$tax_operator = in_array( $tax_operator, array( 'IN', 'NOT IN', 'AND' ) ) ? $tax_operator : 'IN';
		 		
		 		$tax_args['tax_query'][] = array(
		 			'taxonomy' => $taxonomy,
		 			'field' => 'slug',
		 			'terms' => $terms,
		 			'operator' => $tax_operator
		 		);
		
				$count++;
				
			endwhile;
			
			if( $more_tax_queries ):
				$tax_relation = 'AND';
				if( isset( $original_atts['tax_relation'] ) && in_array( $original_atts['tax_relation'], array( 'AND', 'OR' ) ) )
					$tax_relation = $original_atts['tax_relation'];
				$args['tax_query']['relation'] = $tax_relation;
			endif;
			
			$args = array_merge( $args, $tax_args );
		}
		
		// If post parent attribute, set up parent
		if( $post_parent ) {
			if( 'current' == $post_parent ) {
				global $post;
				$post_parent = get_the_ID();
			}
			$args['post_parent'] = intval( $post_parent );
		}
		
		// Set up html elements used to wrap the posts. 
		// Default is ul/li, but can also be ol/li and div/div
		$wrapper_options = array( 'ul', 'ol', 'div' );
		if( ! in_array( $wrapper, $wrapper_options ) )
			$wrapper = 'ul';
		$inner_wrapper = 'div' == $wrapper ? 'div' : 'li';

		$args['orderby'] = 'post__in';
		$args['order'] = '';
		$get_posts = new \WP_Query;
		$listing = $get_posts->query( apply_filters( 'display_posts_shortcode_args', $args, $original_atts ) );

		if ( ! $get_posts->post_count )
			return apply_filters( 'display_posts_shortcode_no_results', wpautop( $no_posts_message ) );
			
		$inner = '';
		$i=0;
		foreach ( $listing as $post ):
			
			$image = $date = $author = $excerpt = $content = '';
			$author_id = $post->post_author;
			
			if ( $include_title )
				$title = '<span class="recom-title">' . get_the_title($post) . '</span>';
			
			if ( $image_size && has_post_thumbnail($post) )  
				$image = '<span class="recom-image">' . get_the_post_thumbnail( $post->ID, $image_size ) . '</span> ';
				
			if ( $include_date || $include_time ) {
				$date .= ' <span class="recom-datetime">';

				if ( $include_date ) 
					$date .= get_the_date( $date_format, $post->ID );

				if ( $include_date && $include_time ) 
					$date .= ' <span class="recom-datetime-delimiter">|</span> ';

				if ( $include_time ) 
					$date .= get_the_time( $time_format, $post->ID );

				$date .= '</span>';
			}
					
			if( $include_author )
				$author = apply_filters( 'display_posts_shortcode_author', ' <span class="recom-author">by ' . get_the_author_meta( 'display_name', $author_id ) . '</span>' );

			if ( $include_excerpt ) {
			// Custom build excerpt based on shortcode parameters
				if( $excerpt_length || $excerpt_more || $excerpt_more_link ) {
					$length = $excerpt_length != 'false' ? $excerpt_length : apply_filters( 'excerpt_length', 13 );
					$more   = $excerpt_more  != 'false' ? $excerpt_more : apply_filters( 'excerpt_more', '' );
					$more   = $excerpt_more_link != 'false' ? ' <span class="recom-excerpt-dash">-</span> ' . $more : ' ';
					if( has_excerpt( $post ) && apply_filters( 'display_posts_shortcode_full_manual_excerpt', false ) ) {
						$excerpt = strip_shortcodes( $post->post_excerpt ) . $more;
					} elseif( has_excerpt( $post ) ) {
						$excerpt = wp_trim_words( strip_shortcodes( $post->post_excerpt ), $length, $more );
					} else {
						$excerpt = wp_trim_words( strip_shortcodes( $post->post_content ), $length, $more );
					}	
					// Use default, can customize with WP filters
				} else {
					$excerpt = strip_shortcodes( get_the_excerpt($post) );
				}
				
				$excerpt = '<span class="recom-excerpt">' . $excerpt . '</span>';
			}
				
			if( $include_content ) {
				add_filter( 'shortcode_atts_display-posts', [&$this, 'be_display_posts_off'], 10, 3 );
				$content = '<div class="recom-content">' . apply_filters( 'the_content', $post->post_content ) . '</div>'; 
				remove_filter( 'shortcode_atts_display-posts', [&$this, 'be_display_posts_off'], 10, 3 );
			}

			// Display categories the post is in
			// $category_display_text = '';
			// if( $category_display && is_object_in_taxonomy( get_post_type($post), $category_display ) ) {
			// 	// $terms = get_the_terms( $post->ID, $category_display );
			// 	// $term_output = array();
			// 	// foreach( $terms as $term )
			// 	// 	$term_output[] = '<a href="' . get_term_link( $term, $category_display ) . '">' . $term->name . '</a>';
			// 	// $category_display_text = ' <span class="category-display"><span class="category-display-label">' . $category_label . '</span> ' . implode( ', ', $term_output ) . '</span>';
			// 	// /**
			// 	//  * Filter the list of categories attached to the current post.
			// 	//  *
			// 	//  * @since 2.4.2
			// 	//  *
			// 	//  * @param string   $category_display Current Category Display text
			// 	//  */
			// 	// $category_display_text = apply_filters( 'display_posts_shortcode_category_display', $category_display_text );
			
			// // If they pass a taxonomy that doesn't exist on this post type	
			// }elseif( $category_display ) {
			// 	$category_display = '';
			// }
			
			$class = array( 'recom-item', $style );
			$class = sanitize_html_class( apply_filters( 'display_posts_shortcode_post_class', $class, $post, $listing, $original_atts, $i ) );
			$output = '<' . $inner_wrapper . ' class="' . implode( ' ', $class ) . '"><div class="recom-item-inner"><a class="recom-wrapper-link" href="' . apply_filters( 'the_permalink', get_permalink($post->ID) ) . '">' . $image . $title . $author . $excerpt . $content . $date . '</a></div></' . $inner_wrapper . '>';
			
			// If post is set to private, only show to logged in users
			if( 'private' == get_post_status( $post->ID ) && !current_user_can( 'read_private_posts' ) )
				$output = '';
			
			$inner .= apply_filters( 'display_posts_shortcode_output', $output, $original_atts, $image, $title, $date, $excerpt, $inner_wrapper, $content, $class );
			
			$i++;
		endforeach; wp_reset_postdata();
		
		$open = apply_filters( 'display_posts_shortcode_wrapper_open', '<' . $wrapper . $wrapper_class . $wrapper_id . '>', $original_atts );
		$close = apply_filters( 'display_posts_shortcode_wrapper_close', '</' . $wrapper . '>', $original_atts );
		
		$return = $open;
		if( $shortcode_title ) {
			$title_tag = apply_filters( 'display_posts_shortcode_title_tag', 'div', $original_atts );
			$return .= '<' . $title_tag . ' class="recom-posts-title">' . $shortcode_title . '</' . $title_tag . '>' . "\n";
		}
		$return .= $inner . $close;
		return $return;
	}
	/**
	 * Turn off display posts shortcode 
	 * If display full post content, any uses of [display-posts] are disabled
	 *
	 * @param array $out, returned shortcode values 
	 * @param array $pairs, list of supported attributes and their defaults 
	 * @param array $atts, original shortcode attributes 
	 * @return array $out
	 */
	function be_display_posts_off( $out, $pairs, $atts ) {
		$out['display_posts_off'] = true;
		return $out;
	}
	/**
	 * Convert string to boolean
	 * because (bool) "false" == true
	 *
	 */
	function be_display_posts_bool( $value ) {
		return !empty( $value ) && 'true' == $value ? true : false;
	}

	public function be_dps_column_classes( $classes, $post, $listing, $atts, $i ) {
		if( ! isset( $atts['columns'] ) )
			return $classes;
		$columns = intval( $atts['columns'] );
		if( $columns < 2 || $columns > 6 )
			return $classes;
			
		$column_classes = array( '', '', 'recom-one-half', 'recom-one-third', 'recom-one-fourth', 'recom-one-fifth', 'recom-one-sixth' );
		$classes[] = $column_classes[$columns];
		if( 0 == $i % $columns )
			$classes[] = 'recom-first';
		return $classes;
	}
	/**
	 * Column Class Styles 
	 *
	 */
	public function be_dps_column_class_styles() {
		if( apply_filters( 'dps_columns_extension_include_css', true ) )
			wp_enqueue_style( 'dps-columns', RECOM_ASSETS_URL.'css/dps-columns.css' );
	}
}