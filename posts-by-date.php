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
    <?php settings_fields('pbd_plugin_options_group'); ?>
    <?php // must be the same name as the add_settings_section / add_settings_field ?>
    <?php do_settings_sections('plugin_page'); ?>
 
    <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
</form>


<?php
}

add_action('admin_init', 'plugin_admin_init');


function plugin_admin_init() 
{

    // the pdb_plugin_options is the name of the option created in db! get_option('pdb_plugins_options')

    register_setting( 'pbd_plugin_options_group', 'pbd_plugin_options', 'sanitize_plugin_options' );
    add_settings_section('plugin_main', 'Main Settings', 'pbd_plugin_section_text', 'plugin_page');
    add_settings_field('plugin_text_string', 'djaldksj', 'render_input_settings', 'plugin_page', 'plugin_main', array( 


        'label_for' => 'plugin_text_string',
        'label_for1' => 'plugin_text_string', 
        'label_for2' => 'plugin_text_string', 
        )  
    );
    
}
function pbd_plugin_section_text ()
{
    
}

function pbd_get_categories() 
{
    $post_categories = get_categories();
    return $post_categories;
}

function render_input_settings( $args) 
{

    print_r($args);
    $defaults = array (
        'pbd_posts_per_page' => '5',
        'pbd_category' => '',
        'pbd_date' => '',
    );

    $options = wp_parse_args(get_option('pbd_plugin_options'), $defaults);

    echo "<input id='plugin_text_string' name='pbd_plugin_options[pbd_posts_per_page]' size='40' type='number' value='{$options['pbd_posts_per_page']}' />";
    
    echo "<br />";

    echo "<input id='plugin_text_string' name='pbd_plugin_options[pbd_date]' size='40' type='date' value='{$options['pbd_date']}' />";


    echo "<br />";
    
    $post_categories = pbd_get_categories();
    
    ?>
    <select class='post-type-select' name="pbd_plugin_options[pbd_category]">
        <option value="" selected disabled hidden>Choose here</option>
               <?php
                foreach( $post_categories as $pbd_category  ) {

                    echo $options['pbd_category'];
                    echo '<option value="' . $pbd_category->name . '"' .selected($options['pbd_category'], $pbd_category->name) .'>' . $pbd_category->name . '</option>';
                }
                ?>
    </select>
    <?php 
} 


function sanitize_plugin_options( $options1 ) 
{
    
    $options1['pbd_posts_per_page'] = ( !empty($options1['pbd_posts_per_page']) ) ? sanitize_text_field($options1['pbd_posts_per_page']) : '';
    
    return $options1;
    
}

//echo get_option('pbd_plugin_options')['pbd_posts_per_page'];

//print_r(get_option('pbd_plugin_options')) ;

//echo $_GET['pbd_plugin_options["pbd_date"]'];
//date( 'd/m/Y', strtotime( get_option( 'eg_custom_date' ) ) );

//delete_option('pbd_plugin_options');
function create_shortcode( $user_atts1 ) 
{

    $options1 = get_option('pbd_plugin_options');
    
    $defaults = array (
        "noof_posts" => ( isset($options1['pbd_posts_per_page']) ) ? $options1['pbd_posts_per_page'] : 5,
        "category" => ( isset($options1['pbd_category']) ) ? $options1['pbd_category'] : ' ',
        "date" => ( isset($options1['pbd_date']) ) ? $options1['pbd_date'] : '',
    );
    
    //shortcode_atts( array $pairs, array $atts, string $shortcode = '' ): array
    // $pairs = defaults $atts = user defined

    $atts = shortcode_atts($defaults, $user_atts1, 'bc-post');

    print_r($atts);
    

    date_default_timezone_set("Europe/Athens");
    //echo "The time is " . date("h:i:sa");


    //date( 'd/m/Y', strtotime( get_option( 'eg_custom_date' ) ) );

    // build query from shortcode
    $args = array(
    
        'posts_per_page' => $atts['noof_posts'],
        'category_name'  => $atts['category'],
        'date_query' => array(
            array(
                'after'     => date( 'd M Y', strtotime($atts['date'])   ), //$atts['date'],
                //'after'     => date( 'd M Y', strtotime( "-2 week", strtotime($atts['date'])  ) ), //$atts['date'],
                'before'    => date('d - M - Y'), // should be to current date
                'inclusive' => true,
                ),
                
            ),
       
    );
    echo "<br />";
    print_r($args);
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

add_shortcode('pbd-posts', 'create_shortcode');
