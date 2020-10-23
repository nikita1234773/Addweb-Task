<?php
/**
 * @package Akismet
 */
/*
Plugin Name: Custom Plugin for order count
Description: It will show the count of orders for each product on dashboard.
Version: 4.1.6
Text Domain: ordercount
*/

//creating database on plugin activation
global $jal_db_version;
$cus_db_version = '1.0';
function cus_install() {
 //    global $wpdb;
 //    global $jal_db_version;
 //    $table_name = $wpdb->prefix . 'employee_list';
 //    $charset_collate = $wpdb->get_charset_collate();
 //    $sql = "CREATE TABLE $table_name (
 //  id mediumint(9) NOT NULL AUTO_INCREMENT,
 //  name tinytext NOT NULL,
 //  address text NOT NULL,
 //  role text NOT NULL,
 //  contact bigint(12), 
 //  PRIMARY KEY  (id)
 // ) $charset_collate;";
 //    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
 //    dbDelta( $sql );
    add_option( 'cus_db_version', $jal_db_version );
}
register_activation_hook( __FILE__, 'cus_install' );

// add_filter( 'manage_edit-product_columns', 'misha_brand_column', 20 );
// function misha_brand_column( $columns_array ) {
 
// 	// I want to display Brand column just after the product name column
// 	return array_slice( $columns_array, 0, 3, true )
// 	+ array( 'brand' => 'Brand' )
// 	+ array_slice( $columns_array, 3, NULL, true );
 
 
// }



function my_manage_columns( $columns ) {
    unset($columns['sku'], $columns['product_tag']);
    return $columns;
}

function my_column_init() {
    add_filter( 'manage_posts_columns' , 'my_manage_columns' );
}

add_action( 'admin_init' , 'my_column_init' );



// add
add_filter( 'manage_edit-product_columns', 'c_total_sales_1', 20 );
// populate
add_action( 'manage_posts_custom_column', 'c_total_sales_2' );
// make sortable
add_filter('manage_edit-product_sortable_columns', 'c_total_sales_3');
// how to sort
add_action( 'pre_get_posts', 'c_total_sales_4' );
 
function c_total_sales_1( $col_th ) {
 
	// a little different way of adding new columns
	return wp_parse_args( array( 'total_sales' => 'Total Sales' ), $col_th );
 
}
 
function c_total_sales_2( $column_id ) {
 
	if( $column_id  == 'total_sales' )
		echo get_post_meta( get_the_ID(), 'total_sales', true );
 
}
 
function c_total_sales_3( $a ){
	return wp_parse_args( array( 'total_sales' => 'by_total_sales' ), $a );
 
}
 
function c_total_sales_4( $query ) {
 
	if( !is_admin() || empty( $_GET['orderby']) || empty( $_GET['order'] ) )
		return;
 
	if( $_GET['orderby'] == 'by_total_sales' ) {
		$query->set('meta_key', 'total_sales' );
		$query->set('orderby', 'meta_value_num');
		$query->set('order', $_GET['order'] );
	}
 
	return $query;
 
}

//2. To add custom field on Cart page
// Add a new checkout field

// function prefix_after_cart_item_name( $cart_item, $cart_item_key ) {
//  $notes = isset( $cart_item['notes'] ) ? $cart_item['notes'] : '';
//  printf(
//  '<div><textarea class="%s" id="cart_notes_%s" data-cart-id="%s">%s</textarea></div>',
//  'prefix-cart-notes',
//  $cart_item_key,
//  $cart_item_key,
//  $notes
//  );
// }
// add_action( 'woocommerce_after_cart_item_name', 'prefix_after_cart_item_name', 10, 2 );

// *
//  * Enqueue our JS file
 
// function prefix_enqueue_scripts() {
//  wp_register_script( 'prefix-script', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'update-cart-item-ajax.js', array( 'jquery-blockui' ), time(), true );
//  wp_localize_script(
//  'prefix-script',
//  'prefix_vars',
//  array(
//  'ajaxurl' => admin_url( 'admin-ajax.php' )
//  )
//  );
//  wp_enqueue_script( 'prefix-script' );
// }
// add_action( 'wp_enqueue_scripts', 'prefix_enqueue_scripts' );


add_action( 'woocommerce_before_add_to_cart_button', 'add_fields_before_add_to_cart' );

function add_fields_before_add_to_cart( ) {
	global $product;
    $id = $product->get_id();
	$term_list = wp_get_post_terms($id,'product_cat',array('fields'=>'ids'));	
    $cat_id = $term_list[0];
    if($cat_id==16) {
	echo "<table>
		<tr>
			<td>". _e( 'Message:', 'aoim')."</td>
			<td>
				<input type = 'text' name = 'customer_message' id = 'customer_message' placeholder = 'Your Message on Gift Card' style='padding: 5px; width: 83%; height:54%'>
			</td>
		</tr>
	</table>";
	}
}
add_filter( 'woocommerce_add_cart_item_data', 'add_cart_item_data', 25, 2 );

function add_cart_item_data( $cart_item_meta, $product_id ) {

	if ( isset( $_POST ['customer_name'] ) && isset( $_POST ['customer_message'] ) ) {
		$custom_data  = array() ;
		//$custom_data [ 'customer_name' ]    = isset( $_POST ['customer_name'] ) ?  sanitize_text_field ( $_POST ['customer_name'] ) : "" ;
		$custom_data [ 'customer_message' ] = isset( $_POST ['customer_message'] ) ? sanitize_text_field ( $_POST ['customer_message'] ): "" ;
		$cart_item_meta ['custom_data']     = $custom_data ;
	}
	
	return $cart_item_meta;
}
add_filter( 'woocommerce_get_item_data', 'get_item_data' , 25, 2 );

function get_item_data ( $other_data, $cart_item ) {

	if ( isset( $cart_item [ 'custom_data' ] ) ) {
		$custom_data  = $cart_item [ 'custom_data' ];
			
		//$other_data[] = array( 'name' => 'Name',
					//'display'  => $custom_data['customer_name'] );
		$other_data[] = array( 'name' => 'Message',
				       'display'  => $custom_data['customer_message'] );
	}
	
	return $other_data;
}
add_action( 'woocommerce_add_order_item_meta', 'add_order_item_meta' , 10, 2);

function add_order_item_meta ( $item_id, $values ) {

	if ( isset( $values [ 'custom_data' ] ) ) {

		$custom_data  = $values [ 'custom_data' ];
		//wc_add_order_item_meta( $item_id, 'Name', $custom_data['customer_name'] );
		wc_add_order_item_meta( $item_id, 'Message', $custom_data['customer_message'] );
	}
}