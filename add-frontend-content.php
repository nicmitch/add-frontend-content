<?php

/*
Plugin Name: Add Frontend content
Description: Simple frontend editor
Version:     1
Author:      Nicola Merici
Author URI:  http://www.nicolamerici.com
*/

/*
function afec_plugin_menu() {
	add_menu_page( __( 'Add Frontend content', 'afec-plugin' ), __( 'Add F-E Content', 'afec-plugin' ), 'manage_options', 'afec_main_menu', 'afec_settings' );
	add_submenu_page( 'afec_main_menu', __( 'Add/Edit Post Types', 'afec-plugin' ), __( 'Add/Edit Post Types', 'afec-plugin' ), 'manage_options', 'afec_manage_post_types', 'afec_manage_post_types' );
	add_submenu_page( 'afec_main_menu', __( 'Add/Edit Taxonomies', 'afec-plugin' ), __( 'Add/Edit Taxonomies', 'afec-plugin' ), 'manage_options', 'afec_manage_taxonomies', 'afec_manage_taxonomies' );
	add_submenu_page( 'afec_main_menu', __( 'Registered Types and Taxes', 'afec-plugin' ), __( 'Registered Types/Taxes', 'afec-plugin' ), 'manage_options', 'afec_listings', 'afec_listings' );
	add_submenu_page( 'afec_main_menu', __( 'Import/Export', 'afec-plugin' ), __( 'Import/Export', 'afec-plugin' ), 'manage_options', 'afec_importexport', 'afec_importexport' );
	add_submenu_page( 'afec_main_menu', __( 'Help/Support', 'afec-plugin' ), __( 'Help/Support', 'afec-plugin' ), 'manage_options', 'afec_support', 'afec_support' );

	# Remove the default one so we can add our customized version.
	remove_submenu_page('afec_main_menu', 'afec_main_menu');
	add_submenu_page( 'afec_main_menu', __( 'About afec UI', 'afec-plugin' ), __( 'About afec UI', 'afec-plugin' ), 'manage_options', 'afec_main_menu', 'afec_settings' );
}
add_action( 'admin_menu', 'afec_plugin_menu' );
*/


function afec_add_form_func( $atts ) {
    $a = shortcode_atts( array(
        'foo' => 'something',
        'bar' => 'something else',
    ), $atts );


    $postTitleError = '';
 

    $output = "";
    $output .= "<form action=\"\" id=\"primaryPostForm\" method=\"POST\">";


	      $output .= "<div class=\"form-group\">\n";
	        $output .= "<label for=\"postTitle\">". __('Post Title:', 'afec') . "</label>\n";
	        $output .= "<input type=\"text\" name=\"postTitle\" id=\"postTitle\" class=\"required\" value=\"";
	        $output .= isset( $_POST['postTitle'] ) ? $_POST['postTitle'] : "";
	        $output .= "\"/>\n";
	      $output .= "</div>\n";


	      $output .= "<div class=\"form-group\">\n";
	        $output .= "<label for=\"postContent\">". __('Post Content:', 'framework') ."</label>\n";
	        $output .= "<textarea name=\"postContent\" id=\"postContent\" rows=\"8\" cols=\"30\" class=\"required\">";

	        if ( isset( $_POST['postContent'] ) ) {
	        	if ( function_exists( 'stripslashes' ) ) {
	        		$output .= stripslashes( $_POST['postContent'] );
	        	} else {
	        		$output .= $_POST['postContent']; 
	        	}
	        }
	        $output .= "</textarea>\n";
	      $output .= "</div>\n";



	      $output .= "<div class=\"form-group\">\n";

	      	$output .= wp_nonce_field( 'post_nonce', 'post_nonce_field' );
	        $output .= "\n<input type=\"hidden\" name=\"submitted\" id=\"submitted\" value=\"true\" />\n";
	        $output .= "<button type=\"submit\">". __('Add Post', 'framework') ."</button>\n";
	      $output .= "</div>\n";


		if ( $postTitleError != '' ) {
		    $output .= "<div class=\"alert alert-danger\" role=\"alert\">". $postTitleError ."</div>";
		}

    $output .= "</form>";


    return $output;
}
add_shortcode( 'form-add', 'afec_add_form_func' );


function afec_check_form_func(){


	if ( isset( $_POST['submitted'] ) && isset( $_POST['post_nonce_field'] ) && wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce' ) ) {
	 
	    if ( trim( $_POST['postTitle'] ) === '' ) {
	        $postTitleError = 'Please enter a title.';
	        $hasError = true;
	    }
	 
	    $post_information = array(
	        'post_title' => wp_strip_all_tags( $_POST['postTitle'] ),
	        'post_content' => $_POST['postContent'],
	        'post_type' => 'post',
	        'post_status' => 'pending'
	    );

	 	
	    $post_id = wp_insert_post( $post_information );
	    
	    __update_post_meta( $post_id, 'caccole', 'prova' );

		if ( $post_id ) {
		    wp_redirect( home_url() );
		    exit;
		}
	 
	}

}
add_action('wp_head', 'afec_check_form_func');



function __update_post_meta( $post_id, $field_name, $value = '' ){
    if ( empty( $value ) OR ! $value )
    {
        delete_post_meta( $post_id, $field_name );
    }
    elseif ( ! get_post_meta( $post_id, $field_name ) )
    {
        add_post_meta( $post_id, $field_name, $value );
    }
    else
    {
        update_post_meta( $post_id, $field_name, $value );
    }
}