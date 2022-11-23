<?php 
/**
 * Plugin Name:       Posts by date OOP
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
 * Text Domain:       posts_by_date
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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


Class Posts_by_date {

	public function __construct() {

		add_action( 'init', array( $this, 'att_textdomain') );
		add_action( 'admin_menu', array( $this, 'pbd_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'pbd_plugin_admin_init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'pbd_enqueue_scripts') );
		add_action( 'wp_ajax_pbd_ajax_pagination', array( $this, 'pbd_ajax_pagination' ) );
		add_action( 'wp_ajax_nopriv_pbd_ajax_pagination', array( $this, 'pbd_ajax_pagination' ) );
		add_shortcode( 'pbd-posts', array( $this, 'pbd_create_shortcode' ) );
	
	}
	

	function att_textdomain() {
		load_plugin_textdomain( 'posts_by_date', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
	}


	//add_action('wp_enqueue_scripts', 'pbd_enqueue_scripts');
	function pbd_enqueue_scripts() {

		wp_register_script( 'ajax-pagination-handle', plugin_dir_url( __FILE__ ) . 'js/ajax-pagination.js', array( 'jquery' ), '', true );
	
		wp_localize_script( 'ajax-pagination-handle', 'ajax_js_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	
		wp_enqueue_script( 'ajax-pagination-handle' );

	}
	

	//add_action('admin_menu', 'add_plugin_page');


	function pbd_add_plugin_page() {
		add_menu_page( 'Posts by date', 'Posts by date', 'manage_options', 'pbd_slug', array( $this, 'hellothere' ) );
	}

	public function hellothere() {
	?>

	<form action="options.php" method="post">
		<?php settings_fields( 'pbd_plugin_options' ); ?>
		<?php // must be the same name as the add_settings_section / add_settings_field ?>
		<?php do_settings_sections( 'pbd_plugin_options' ); ?>
	
		<input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
	</form>
	<?php
	}

	//add_action('admin_init', 'pbd_plugin_admin_init');


	function pbd_plugin_admin_init() {

		if (false == get_option( 'pbd_plugin_options' ) ) {  
			$default_options = array(
				'pbd_posts_per_page'=> '66',
				'pbd_date' => date('Y-d-m', strtotime('-1 year')),
				'pbd_category' => 'demo',
			);

			update_option( 'pbd_plugin_options', $default_options );
		}

		register_setting( 'pbd_plugin_options', 'pbd_plugin_options', array( $this, 'pbd_theme_validate_input_examples' ) );

		add_settings_section( 'settings_section', __( 'Main Settings','posts_by_date' ), array( $this, 'render_section' ), 'pbd_plugin_options' );
		
		add_settings_field( 'pbd_posts_per_page', __( 'Number of posts', 'posts_by_date' ), array( $this, 'render_numberof_posts' ), 'pbd_plugin_options', 'settings_section' );
		add_settings_field( 'pbd_date', __( 'Show posts after', 'posts_by_date' ), array( $this, 'render_date' ), 'pbd_plugin_options', 'settings_section' );
		add_settings_field( 'pbd_category', __( 'Category', 'posts_by_date' ), array( $this, 'render_category' ), 'pbd_plugin_options', 'settings_section' );
	}


	// validate data before saving them in db
	function pbd_theme_validate_input_examples( $input ) {
 
		// Create our array for storing the validated options
		$output = array();		 
		// Loop through each of the incoming options
		foreach( $input as $key => $value ) {

			// Check to see if the current option has a value. If so, process it.
			if( isset ( $input [ $key ] ) ) {

				// Strip all HTML and PHP tags and properly handle quoted strings
				$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );

			} // end if

		} // end foreach
		 
		// Return the array processing any additional functions filtered by this action
		return $output;
		
	}

	function render_section() {
		echo __( 'Section text', 'posts_by_date' );
	}


	function pbd_get_categories() {
		$post_categories = get_categories();
		return $post_categories;
	}

	function render_numberof_posts($args ) {

		$options = get_option( 'pbd_plugin_options' );
		echo "<input id='pbd_posts_per_page' name='pbd_plugin_options[pbd_posts_per_page]' size='40' type='number' value='{$options['pbd_posts_per_page']}'> ";
	}


	function render_date( $args) {

		$options = get_option( 'pbd_plugin_options' );
		echo "<input id='pbd_date' name='pbd_plugin_options[pbd_date]' size='40' type='date' value='{$options['pbd_date']}' />";
	}
		
	function render_category() {

		$options = get_option( 'pbd_plugin_options' );

		$post_categories = $this->pbd_get_categories();

		?>
		<select class='post-type-select' name="pbd_plugin_options[pbd_category]">
			<option value="" selected disabled hidden>Choose here</option>
				<?php
				foreach ( $post_categories as $pbd_category ) {
						echo '<option value="' . esc_html( $pbd_category -> name ) . '"'.selected ( $options['pbd_category'], $pbd_category->name ) .'>' . esc_html( $pbd_category -> name ) . '</option>';
				}
					?>
		</select>
		<?php

	}

	function pbd_create_shortcode( $user_atts1 ) {

		$options1 = get_option( 'pbd_plugin_options' );
		
		$defaults = array (
			'noof_posts' => ( isset( $options1['pbd_posts_per_page']) ) ? $options1['pbd_posts_per_page'] : 5,
			'category'   => ( isset( $options1['pbd_category']) ) ? $options1['pbd_category'] : ' ',
			'date'       => ( isset( $options1['pbd_date']) ) ? $options1['pbd_date'] : '',
		);
		
		$atts = shortcode_atts( $defaults, $user_atts1, 'pbd-posts' );

		date_default_timezone_set( "Europe/Athens" );

		// build query from shortcode
		$args = array(

			'posts_per_page' => $atts['noof_posts'],
			'category_name'  => $atts['category'],
			'date_query' => array(
				array(
					'after'     => date('d M Y', strtotime( $atts['date'] ) ), 
					'before'    => date('d - M - Y'), // should be to current date
					'inclusive' => true,
					),
					
				),
		
		);
		
		$wp_posts   = new WP_Query( $args );

		$found      = $wp_posts->found_posts; 
		$post_count = $wp_posts->post_count; 
	
		if ( $wp_posts->found_posts == 0 ) {
			return 'no posts found';
		} else {
			
			$output = '<ul class="shortcode_list">';
			
			while ( $wp_posts->have_posts() ) {
				$wp_posts->the_post();
				$output .= '<li><a href="' . get_permalink( get_the_ID() ) . '">' . the_title( '', '', false ) . '</a></li>' ;
			}
			wp_reset_postdata();


			$output .= '</ul>';
			
		}

		if ( $wp_posts->post_count < $wp_posts->found_posts ) {
			$output .= '<button style="width:300px; margin:0 auto; display:block;" data-date=' . $atts['date'] . ' data-total-posts=' . $found . ' data-posts-per-page=' . $post_count . ' data-category= ' .$args['category_name'] .' class=pbd-more><span class=post_count>' . $post_count .'</span> out of ' . $found . '</button>';
		}
		return  $output;
		
	}

	function pbd_ajax_pagination() {

		$args = array (
			'category_name'  => $_POST['category'], 
			'posts_per_page' => $_POST['number_of_posts'], 
			'offset'  => $_POST['offset'],
			'date_query' => array(
				array(
					'after'     => date('d M Y', strtotime( $_POST['date'] ) ), 
					'before'    => date('d - M - Y'), // should be to current date
					'inclusive' => true,
					),
					
				),
		);

		$wp_posts = new WP_Query( $args );

		while ( $wp_posts->have_posts() ) {
			$wp_posts->the_post();
			$output .= '<li><a href="' . get_permalink( get_the_ID() ) . ' " > ' . the_title( '', '', false ) . ' </a></li>' ;
		}   

		echo $output;
		die();
	}
} // end of class

$obj = new Posts_by_date;

//delete_option('pbd_plugin_options');

//add_filter('widget_text', 'do_shortcode');