<?php
//if uninstall/delete not called from WordPress exit
if( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit ();
// Delete options array from options table
delete_option( 'number_of_posts' );
delete_option( 'post_category' );
delete_option( 'post_date' );
?>