<?php namespace WPRecommenderIr;

defined( 'ABSPATH' ) || exit;

class Widget extends \WP_Widget {

    public function __construct()
    {
        $widget_ops = array( 
            'classname' => 'recommender_widget',
            'description' => __('Recommender', 'recommender-ir'),
        );
        parent::__construct( 'recommender_widget', __('Recommender', 'recommender-ir') );
    }

    public function widget( $args, $instance )
    {
        global $post;
        $method = $instance['method'];
        $how_many = $instance['how_many'];
        $dither = $instance['dither'] ? 'true' : 'false';
        $radius = $instance['radius'];
        $columns = $instance['columns'];
        // $category_display = $instance['category_display'] ? 'true' : 'false';
        // $category_label = $instance['category_label'];
        $image_size = $instance['image_size'];
        $include_title = $instance['include_title'] ? 'true' : 'false';
        $date_format = get_option( 'date_format' );
        $time_format = get_option( 'time_format' );
        $include_date = $instance['include_date'] ? 'true' : 'false';
        $include_time = $instance['include_time'] ? 'true' : 'false';
        $include_excerpt = $instance['include_excerpt'] ? 'true' : 'false';
        $excerpt_length = $instance['excerpt_length'] ? $instance['excerpt_length'] : 'false';

    $queried_object = get_queried_object();
    $post_id = $queried_object->ID;
        $post_ids = ( $method == 'similarity' ) ? ' post_ids="'.$post_id.'"' : '';

        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) )
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        
        echo do_shortcode( '[wp-recommenderir method="'.$method.'" how_many="'.$how_many.'" dither="'.$dither.'" radius="'.$radius.'" columns="'.$columns.'" include_title="'.$include_title.'" include_date="'.$include_date.'" date_format="'.$date_format.'" include_time="'.$include_time.'" time_format="'.$time_format.'" image_size="'.$image_size.'" include_excerpt="'.$include_excerpt.'" excerpt_length="'.$excerpt_length.'"'.$post_ids.']' );

        echo $args['after_widget'];
    }

    public function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;

        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['method'] = strip_tags( $new_instance['method'] );
        $instance['how_many'] = strip_tags( $new_instance['how_many'] );
        $instance['dither'] = ! empty( $new_instance['dither'] );
        $instance['radius'] = ! empty( $new_instance['radius'] );
        $instance['columns'] = strip_tags( $new_instance['columns'] );
        // $instance['category_display'] = ! empty( $new_instance['category_display'] );
        // $instance['category_label'] = strip_tags( $new_instance['category_label'] );
        $instance['image_size'] = ! empty( $new_instance['image_size'] ) ? 'thumbnail' : '';
        $instance['include_title'] = ! empty( $new_instance['include_title'] );
        $instance['include_date'] = ! empty( $new_instance['include_date'] );
        $instance['include_time'] = ! empty( $new_instance['include_time'] );
        $instance['include_excerpt'] = ! empty( $new_instance['include_excerpt'] );
        $instance['excerpt_length'] = strip_tags( $new_instance['excerpt_length'] );

        return $instance;
    }

    public function form( $instance )
    {
        $instance = wp_parse_args( (array) $instance,
                        [
                            'title' => '',
                            'method' => '',
                            'how_many' => '',
                            'dither' => '',
                            'radius' => '',
                            'columns' => '',
                            'image_size' => '',
                            'include_title' => '',
                            'include_date' => '',
                            'include_time' => '',
                            'include_excerpt' => '',
                            'excerpt_length' => '',
                            'recommend_style' => '',
                            'similar_style' => ''
                        ]
                    );
        $title = strip_tags($instance['title']);
        $method = strip_tags($instance['method']);
        $how_many = strip_tags($instance['how_many']);
        $dither = isset($instance['dither']) ? $instance['dither'] : 0;
        $radius = strip_tags($instance['radius']);
        $columns = isset($instance['columns']) ? $instance['columns'] : 1;
        // $category_display = isset($instance['category_display']) ? $instance['category_display'] : 0;
        // $category_label = isset($instance['category_label']) ? $instance['category_label'] : 'دسته‌بندی: ';
        $image_size = isset($instance['image_size']) ? 1 : 0;
        $include_title = isset($instance['include_title']) ? $instance['include_title'] : 1;
        $include_date = isset($instance['include_date']) ? $instance['include_date'] : 0;
        $include_time = isset($instance['include_time']) ? $instance['include_time'] : 0;
        $include_excerpt = isset($instance['include_excerpt']) ? $instance['include_excerpt'] : 0;
        $excerpt_length = isset($instance['excerpt_length']) ? $instance['excerpt_length'] : 7;

        $recommend_style = ( !$method || $method == 'recommend' ) ? 'display:block;' : 'display:none;';
        $similar_style = ( $method == 'similarity' ) ? 'display:block;' : 'display:none;';
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'recommender-ir') ?>:</label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

        <p><label for="<?php echo $this->get_field_id('method'); ?>"><?php _e('Method', 'recommender-ir') ?>:</label>
        <select class="widefat method" id="<?php echo $this->get_field_id('method'); ?>" name="<?php echo $this->get_field_name('method'); ?>">
        <?php
            foreach($this->get_recommender_list_type() as $lkey => $lvalue)
                echo '<option value="'.$lkey.'"'. selected($lkey, $method, false) .'>'.$lvalue.'</option>';
        ?>
        </select></p>

        <p class="similar" style="<?php echo $similar_style; ?>color:red;"><?php _e('Please use this method only on single pages', 'recommender-ir') ?></p>

        <p><label for="<?php echo $this->get_field_id('how_many'); ?>"><?php _e('The number of items', 'recommender-ir') ?>:</label>
        <input class="widefat" id="<?php echo $this->get_field_id('how_many'); ?>" name="<?php echo $this->get_field_name('how_many'); ?>" type="number" value="<?php echo esc_attr($how_many); ?>" min="1" /></p>

        
        <p class="recommend" style="<?php echo $recommend_style; ?>"><input id="<?php echo $this->get_field_id('dither'); ?>" name="<?php echo $this->get_field_name('dither'); ?>" type="checkbox" <?php checked($dither); ?> />&nbsp;<label for="<?php echo $this->get_field_id('dither'); ?>"><?php _e('Dither', 'recommender-ir') ?></label></p>
        
        <p class="recommend" style="<?php echo $recommend_style; ?>">
            <label for="<?php echo $this->get_field_id('radius'); ?>"><?php _e('Radius', 'recommender-ir') ?>:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('radius'); ?>" name="<?php echo $this->get_field_name('radius'); ?>" type="number" value="<?php echo esc_attr($radius); ?>" min="1" />
        </p>

        <p><label for="<?php echo $this->get_field_id('columns'); ?>"><?php _e('Column layouts of items', 'recommender-ir') ?>:</label>
        <select class="widefat columns" id="<?php echo $this->get_field_id('columns'); ?>" name="<?php echo $this->get_field_name('columns'); ?>">
        <?php
            foreach($this->get_columns() as $lkey => $lvalue)
                echo '<option value="'.$lkey.'"'. selected($lkey, $columns, false) .'>'.$lvalue.'</option>';
        ?>
        </select></p>

        <?php /*
        <p><input id="<?php echo $this->get_field_id('category_display'); ?>" name="<?php echo $this->get_field_name('category_display'); ?>" type="checkbox" <?php checked($category_display); ?> />&nbsp;<label for="<?php echo $this->get_field_id('category_display'); ?>">نمایش دسته‌ آیتم‌ها</label></p>

        <p><label for="<?php echo $this->get_field_id('category_label'); ?>">متن دسته‌‌بندی:</label>
        <input class="widefat" id="<?php echo $this->get_field_id('category_label'); ?>" name="<?php echo $this->get_field_name('category_label'); ?>" type="text" value="<?php echo esc_attr($category_label); ?>" /></p>
        */ ?>

        <p><input id="<?php echo $this->get_field_id('image_size'); ?>" name="<?php echo $this->get_field_name('image_size'); ?>" type="checkbox" <?php checked($image_size); ?> />&nbsp;<label for="<?php echo $this->get_field_id('image_size'); ?>"><?php _e('Display Post Thumbnail', 'recommender-ir') ?></label></p>

        <p><input id="<?php echo $this->get_field_id('include_title'); ?>" name="<?php echo $this->get_field_name('include_title'); ?>" type="checkbox" <?php checked($include_title); ?> />&nbsp;<label for="<?php echo $this->get_field_id('include_title'); ?>"><?php _e('Display Post Title', 'recommender-ir') ?></label></p>

        <p><input id="<?php echo $this->get_field_id('include_date'); ?>" name="<?php echo $this->get_field_name('include_date'); ?>" type="checkbox" <?php checked($include_date); ?> />&nbsp;<label for="<?php echo $this->get_field_id('include_date'); ?>"><?php _e('Display Post Date', 'recommender-ir') ?></label></p>

        <p><input id="<?php echo $this->get_field_id('include_time'); ?>" name="<?php echo $this->get_field_name('include_time'); ?>" type="checkbox" <?php checked($include_time); ?> />&nbsp;<label for="<?php echo $this->get_field_id('include_time'); ?>"><?php _e('Display Post Time', 'recommender-ir') ?></label></p>

        <p><input id="<?php echo $this->get_field_id('include_excerpt'); ?>" name="<?php echo $this->get_field_name('include_excerpt'); ?>" type="checkbox" <?php checked($include_excerpt); ?> />&nbsp;<label for="<?php echo $this->get_field_id('include_excerpt'); ?>"><?php _e('Display Post Summary', 'recommender-ir') ?></label></p>

        <p><label for="<?php echo $this->get_field_id('excerpt_length'); ?>"><?php _e('Post Summary Length', 'recommender-ir') ?>:</label>
        <input class="widefat" id="<?php echo $this->get_field_id('excerpt_length'); ?>" name="<?php echo $this->get_field_name('excerpt_length'); ?>" type="number" value="<?php echo esc_attr($excerpt_length); ?>" min="1" /></p>

        <?php
    }

    protected function get_recommender_list_type()
    {
        return [
            'recommend' => __('Recommend to user', 'recommender-ir'),
            'similarity' => __('Similar Items', 'recommender-ir'),
            'trendShortTime' => __('The most popular items in the moment (Trends) - Short time', 'recommender-ir'),
            'trendLongTime' => __('The most popular items in the moment (Trends) - Long time', 'recommender-ir')
        ];
    }

    protected function get_columns()
    {
        return [
            '1' => __('1 column', 'recommender-ir'),
            '2' => __('2 columns', 'recommender-ir'),
            '3' => __('3 columns', 'recommender-ir'),
            '4' => __('4 columns', 'recommender-ir'),
            '5' => __('5 columns', 'recommender-ir'),
            '6' => __('6 columns', 'recommender-ir'),
        ];
    }
}
?>