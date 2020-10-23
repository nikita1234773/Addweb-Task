<?php
//To count user order based on order status completed
function has_bought() {
    // Get all customer orders
    $customer_orders = get_posts( array(
        'numberposts' => 1, // one order is enough
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => 'shop_order', // WC orders post type
        'post_status' => 'wc-completed', // Only orders with "completed" status
        'fields'      => 'ids', // Return Ids "completed"
    ) );

    // return "true" when customer has already at least one order (false if not)
   return count($customer_orders); 
}

//Custom code to apply discount
function wc_order_add_discount( $order_id, $title, $amount, $tax_class = '' ) {
    $order    = wc_get_order($order_id);
    $subtotal = $order->get_subtotal();
    $item     = new WC_Order_Item_Fee();

    if ( strpos($amount, '%') !== false ) {
        $percentage = (float) str_replace( array('%', ' '), array('', ''), $amount );
        $percentage = $percentage > 100 ? -100 : -$percentage;
        $discount   = $percentage * $subtotal / 100;
    } else {
        $discount = (float) str_replace( ' ', '', $amount );
        $discount = $discount > $subtotal ? -$subtotal : -$discount;
    }

    $item->set_tax_class( $tax_class );
    $item->set_name( $title );
    $item->set_amount( $discount );
    $item->set_total( $discount );

    if ( '0' !== $item->get_tax_class() && 'taxable' === $item->get_tax_status() && wc_tax_enabled() ) {
        $tax_for   = array(
            'country'   => $order->get_shipping_country(),
            'state'     => $order->get_shipping_state(),
            'postcode'  => $order->get_shipping_postcode(),
            'city'      => $order->get_shipping_city(),
            'tax_class' => $item->get_tax_class(),
        );
        $tax_rates = WC_Tax::find_rates( $tax_for );
        $taxes     = WC_Tax::calc_tax( $item->get_total(), $tax_rates, false );
        print_pr($taxes);

        if ( method_exists( $item, 'get_subtotal' ) ) {
            $subtotal_taxes = WC_Tax::calc_tax( $item->get_subtotal(), $tax_rates, false );
            $item->set_taxes( array( 'total' => $taxes, 'subtotal' => $subtotal_taxes ) );
            $item->set_total_tax( array_sum($taxes) );
        } else {
            $item->set_taxes( array( 'total' => $taxes ) );
            $item->set_total_tax( array_sum($taxes) );
        }
        $has_taxes = true;
    } else {
        $item->set_taxes( false );
        $has_taxes = false;
    }
    $item->save();

    $order->add_item( $item );
    $order->calculate_totals( $has_taxes );
    $order->save();
}


add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

function extra_user_profile_fields( $user ) { ?>
    <h3><?php _e("Discount Coupon Availability", "blank"); ?></h3>
    <?php
      $userid = get_current_user_id();

      $value = get_the_author_meta( 'ccount', $user->ID);
      if($value!=0 && $value!=''){
        $va = $value;
      } else {
        $va=0;
      }
      // $fval = has_bought($userid);
       //$va = get_user_meta( $user->ID, 'ccount', true );
    ?>
    <table class="form-table">
    <tr>
        <th><label for="ccount"><?php _e("Coupon Count"); ?></label></th>
        <td>
            <input type="text" name="ccount" id="ccount" class="regular-text" value="<?php echo $va; ?>" /><br />
            <span class="description"><?php _e("Discount availability field got from purchase"); ?></span>
        </td>
    </tr>
    </table>
<?php }

// function mysite_woocommerce_order_status_completed( $order_id ) {
//     $order = wc_get_order( $order_id );
//     $us_id = $order->get_user_id();
//     $current_count = get_user_meta( $user_id, 'ccount', true );
//     update_user_meta( $user_id, 'ccount', $current_count + 1 );
// }
// add_action( 'woocommerce_order_status_completed', 'mysite_woocommerce_order_status_completed', 10, 1 );
add_action( 'woocommerce_order_status_completed', 'order_completed');
function order_completed($order_id) {
   $order = wc_get_order( $order_id );
   $user_id = $order->get_user_id();
   $current_count = get_user_meta( $user_id, 'ccount', true );

      update_user_meta( $user_id, 'ccount', $current_count + 1 );

}
add_action( 'woocommerce_thankyou', 'bbloomer_checkout_save_user_meta');
function bbloomer_checkout_save_user_meta( $order_id ) {
    
   $order = wc_get_order( $order_id );
   $user_id = $order->get_user_id();
   $current_count = get_user_meta( $user_id, 'ccount', true );
   if($current_count>0)
   {
   $new = $current_count-1;
   update_user_meta( $user_id, 'ccount', $new );
   }
 
}
// Hook before calculate fees
add_action('woocommerce_cart_calculate_fees' , 'add_user_discounts');
/**
 * Add custom fee if more than three article
 * @param WC_Cart $cart
 */
function add_user_discounts( WC_Cart $cart ){
    //any of your rules
    $userid = get_current_user_id();
    //echo "USERID =";
    //echo $userid;
    //echo $us_ccount = 1;
    // Calculate the amount to reduce
    // if($us_ccount>0)
    // {
     $current_count = get_user_meta( $userid, 'ccount', true );
     //$new = $current_count-1;
    if($current_count>0) {
      $discount = $cart->get_subtotal() * 0.3;
      $cart->add_fee( 'Test discount 30%', -$discount);   
      //update_user_meta( $userid, 'ccount', $new );
    }

     //update_user_meta( $userid, 'ccount', $current_count - 1 );
    //}
}


add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
    table.wp-list-table .column-name {
    width: 18%;
}
table.wp-list-table .column-sku {
    width: 8%;
}
table.wp-list-table .column-product_cat, table.wp-list-table .column-product_tag {
    width: 10%!important;
}
  </style>';
}