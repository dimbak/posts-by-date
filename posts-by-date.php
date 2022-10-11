<?php 
/**
 * Plugin Name:       Posts by date
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Use a shortcode to display posts by date and category
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            DB
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       posts-by-date
 * Domain Path:       /languages
 * 
 * 
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

add_action('admin_menu', 'add_plugin_page');


function add_plugin_page() 
{
    add_menu_page('Posts by date', 'Posts by date', 'manage_options', 'pbd_slug', 'pbd_options_page');
}

function pbd_options_page() 
{
?>

<form action="options.php" method="post">
    <?php settings_fields('pbd_plugin_options'); ?>
    <?php // must be the same name as the add_settings_section / add_settings_field ?>
    <?php do_settings_sections('pbd_plugin_options'); ?>
 
    <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form>


<?php
}

add_action('admin_init', 'plugin_admin_init');


function plugin_admin_init() 
{

    if (false == get_option('pbd_plugin_options') ) {  
        $default_options=array(
        'pbd_posts_per_page'=> '66',
        //'pbd_date' => date('Y-m-d'),
        'pbd_date' => date('Y-d-m', strtotime('-1 year')),
        'pbd_category' => 'demo',
         );

        update_option('pbd_plugin_options', $default_options);
    }

    register_setting('pbd_plugin_options', 'pbd_plugin_options');
    
    add_settings_section('settings_section', 'Main Settings', 'render_section', 'pbd_plugin_options');
    
    add_settings_field('pbd_posts_per_page', 'Number of post', 'render_numberof_posts', 'pbd_plugin_options', 'settings_section');

    add_settings_field('pbd_date', 'Show posts after', 'render_date', 'pbd_plugin_options', 'settings_section');

    add_settings_field('pbd_category', 'Category', 'render_category', 'pbd_plugin_options', 'settings_section');
    
}
function render_section()
{
    echo "Section text";   
}

function render_debug_section()
{
    echo "Debug Section text";   
}


function pbd_get_categories() 
{
    $post_categories = get_categories();
    return $post_categories;
}

function render_numberof_posts($args ) 
{
    
    $options = get_option('pbd_plugin_options');
    
    echo "<input id='pbd_posts_per_page' name='pbd_plugin_options[pbd_posts_per_page]' size='40' type='number' value='{$options['pbd_posts_per_page']}'> ";
}


function render_date( $args) 
{
    
    $options = get_option('pbd_plugin_options');


    echo "<input id='pbd_date' name='pbd_plugin_options[pbd_date]' size='40' type='date' value='{$options['pbd_date']}' />";
}
    
function render_category()
{
    
    $options = get_option('pbd_plugin_options');

    $post_categories = pbd_get_categories();
    
    ?>
    <select class='post-type-select' name="pbd_plugin_options[pbd_category]">
        <option value="" selected disabled hidden>Choose here</option>
               <?php
                foreach( $post_categories as $pbd_category  ) {
                    echo '<option value="' . $pbd_category->name . '"' .selected($options['pbd_category'], $pbd_category->name) .'>' . $pbd_category->name . '</option>';
                }
                ?>
    </select>
<?php

}

function sanitize_plugin_options( $options1 ) 
{
    
    // $options1['pbd_posts_per_page'] = ( !empty($options1['pbd_posts_per_page']) ) ? sanitize_text_field($options1['pbd_posts_per_page']) : '';
    
    // return $options1;
    
}

function create_shortcode( $user_atts1 ) 
{


    $options1 = get_option('pbd_plugin_options');
    
    $defaults = array (
        "noof_posts" => ( isset($options1['pbd_posts_per_page']) ) ? $options1['pbd_posts_per_page'] : 5,
        "category" => ( isset($options1['pbd_category']) ) ? $options1['pbd_category'] : ' ',
        "date" => ( isset($options1['pbd_date']) ) ? $options1['pbd_date'] : '',
    );
    
    $atts = shortcode_atts($defaults, $user_atts1, 'bc-post');



    date_default_timezone_set("Europe/Athens");

    // build query from shortcode
    $args = array(

        'posts_per_page' => $atts['noof_posts'],
        'category_name'  => $atts['category'],
        'date_query' => array(
            array(
                'after'     => date('d M Y', strtotime($atts['date'])), 
                'before'    => date('d - M - Y'), // should be to current date
                'inclusive' => true,
                ),
                
            ),
       
    );

    $unread = new WP_Query($args);

    if ($unread->found_posts == 0) {
        return 'no posts found';
    } else {
        
        $output = '<ul>';
        
        while ( $unread->have_posts() ) {
            $unread->the_post();
            $output .= '<li><a href="' . get_permalink(get_the_ID()) . '">' . the_title('', '', false) . '</a></li>' ;
        }
        
        $output .= '</ul>';
        return  $output;
    }

    
}
//delete_option('pbd_plugin_options');
add_shortcode('pbd-posts', 'create_shortcode');

