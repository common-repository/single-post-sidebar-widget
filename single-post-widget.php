<?php
/**
 * Plugin Name: Single Post Sidebar Widget
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: Display a single post in your sidebar
 * Version: 1.1
 * Author: LibraFire
 * Author URI: http://www.librafire.com/
 * Text Domain: single_post_widget
 * Domain Path: Optional. Plugin's relative directory path to .mo files. Example: /locale/
 * Network:true
 * License:GPL2
 */


class sinlge_post_widget extends WP_Widget
{


    /* ------------------------------------------------
        Widget Setup
    ------------------------------------------------ */

    public function __construct()
    {
        $widget_ops = array(
            'classname' => 'single_post_widget',
            'description' => __('Single Post Widget', 'single_post_widget')
        );
        $control_ops = array(
            'width' => 300,
            'height' => 350,
            'id_base' => 'single_post_widget'
        );
        parent::__construct('single_post_widget', __('Single Post Widget', 'single_post_widget'), $widget_ops, $control_ops);
    }

    /* ------------------------------------------------
        Display Widget
    ------------------------------------------------ */

    function widget($args, $instance)
    {
        extract($args);
        echo $before_widget;
        global $post;

        $args = array(
            'post_type' => 'any',
            'p' => $instance['selected_post']
        );

        $loop = new WP_Query($args);

        if ($loop->have_posts()) : ?>
            <?php while ($loop->have_posts()) :
                $loop->the_post(); ?>
                <a href="<?php the_permalink(); ?>">
                    <?php if ($instance['title'] != '') { ?><h4> <?php the_title(); ?></h4><?php } ?>
                    <?php if ($instance['thumbnail'] != '') {
                        the_post_thumbnail( $instance['image_size'] );
                    } ?>
                    <?php if ($instance['excerpt'] != '') {
                        the_excerpt();
                    } ?>
                </a>
            <?php endwhile; else : ?>

            <p><?php _e('<h4>No post selected yet </h4>', 'single_post_widget'); ?></p>

        <?php endif;

        echo $after_widget;
    }


    /* ------------------------------------------------
        Update Widget
    ------------------------------------------------ */

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['selected_post'] = (!empty($new_instance['selected_post'])) ? strip_tags($new_instance['selected_post']) : '';
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['thumbnail'] = (!empty($new_instance['thumbnail'])) ? strip_tags($new_instance['thumbnail']) : '';
        $instance['excerpt'] = (!empty($new_instance['excerpt'])) ? strip_tags($new_instance['excerpt']) : '';
        $instance['image_size'] = (!empty($new_instance['image_size'])) ? strip_tags($new_instance['image_size']) : '';
        return $instance;
    }


    /* ------------------------------------------------
        Widget Input Form
    ------------------------------------------------ */

    function form($instance)
    {
        $defaults = array(
            'selected_post' => '0',
            'title' => '',
            'thumbnail' => '',
            'excerpt' => '',
            'image_size' => array()
        );
        $instance = wp_parse_args((array)$instance, $defaults); ?>
        <p>
        <label for="<?php echo $this->get_field_id('selected_post'); ?>"><?php _e('Select your post:', 'single_post_widget'); ?></label>

        <?php

        $args = array(
            'post_type' => 'any',
            'posts_per_page' => -1
        );
        $wp_query = new WP_Query($args);

        if ($wp_query->have_posts()):
            ?><select style="width: 100%;" id='<?php echo $this->get_field_id('selected_post'); ?>' name="<?php echo $this->get_field_name('selected_post'); ?>"><?php
            while ($wp_query->have_posts()):$wp_query->the_post();
                ?>
                <option value="<?php echo get_the_ID(); ?>" <?php echo ($instance['selected_post'] == get_the_ID()) ? 'selected' : ''; ?>><?php the_title(); ?></option>
            <?php
            endwhile;
            ?></select><?php
        else:
            ?><p><strong><?php _e('No posts found', 'single_post_widget'); ?></strong></p><?php
        endif;

        ?>

        </p>

        <p>
        <h4><?php _e('Choose the layout options:', 'single_post_widget') ?></h4>
        <label for="<?php echo $this->get_field_id('thumbnail'); ?>"><?php _e('Post thumbnail:', 'single_post_widget') ?></label>
        <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id('thumbnail'); ?>"
               name="<?php echo $this->get_field_name('thumbnail'); ?>"
               value="thumbnail" <?php echo ($instance['thumbnail'] == 'thumbnail') ? 'checked' : ''; ?> />

        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Post title:', 'single_post_widget') ?></label>
        <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
               name="<?php echo $this->get_field_name('title'); ?>"
               value="title" <?php echo ($instance['title'] == 'title') ? 'checked' : ''; ?> />

        <label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e('Post excerpt:', 'single_post_widget') ?></label>
        <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id('excerpt'); ?>"
               name="<?php echo $this->get_field_name('excerpt'); ?>"
               value="excerpt" <?php echo ($instance['excerpt'] == 'excerpt') ? 'checked' : ''; ?> />

        <h4><?php _e('Choose the post image size:', 'single_post_widget') ?></h4>

        <select name="<?php echo $this->get_field_name('image_size'); ?>" id="<?php echo $this->get_field_id('image_size'); ?>">
            <?php

            foreach (get_intermediate_image_sizes() as $get_intermediate_image_size) {
                ?>
                <option <?php echo ($instance['image_size'] == $get_intermediate_image_size) ? 'selected' : ''; ?>  value="<?php echo $get_intermediate_image_size; ?>"><?php echo $get_intermediate_image_size; ?></option>
                <?php
            }

            ?>
        </select>


        </p>

        <?php
    }

}

// Add widget function to widgets_init
add_action('widgets_init', 'sinlge_post_widget_init');

// Register Widget
function sinlge_post_widget_init()
{
    register_widget('sinlge_post_widget');
}