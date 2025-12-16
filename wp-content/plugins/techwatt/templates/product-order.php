<h2 class="ps-mb-5 ps-hdflex"><span>Ordered Products</span> <span><a href="<?php _e(twUrl("PS_Shop"),'tw'); ?>" class="btn btn-xs btn-success"><i class="bi bi-plus"></i> Buy Product</a></span></h2>
<?php
    $orders = wc_get_orders(['customer_id' => $userID, 'status' => array_keys(wc_get_order_statuses()),  'limit' => -1,'orderby' => 'date','order' => 'DESC',]);
?>
<table>
<tr style="font-weight:bold;background:#f5f5f5;"><td>Product Description</td><td>Quantity</td><td>Amount paid</td><td>Order Date</td><td></td></tr>
<?php
$output = '';
if ( empty( $orders ) ) { $output = '<tr><td colspan="5" align="center">No orders found.</td></tr>'; }
else{
    foreach ( $orders as $order ) {
        $orderNo = $order->get_order_number();
        $orderDate = $order->get_date_created()->date_i18n( get_option('date_format') );
        foreach ( $order->get_items() as $itemID => $item ) {
            $itemName = $item->get_name();
            $quantity = $item->get_quantity();
            //$subtotal = $item->get_subtotal();   // Line subtotal (before discount)
            $total = $item->get_total();  // Line total (after discount)
            //$tax = $item->get_total_tax();
            $product = $item->get_product();
            $product_SKU = $product ? $product->get_sku() : '';
            //$product_id = $product->get_id();
            $product_url = $product ? get_permalink( $product->get_id() ) : '';
            $product_image = $product ? wp_get_attachment_image( $product->get_image_id(), 'thumbnail',false, 
    array( 'style' => 'width:90px;height:90px;object-fit:cover;') ) : '';

            $output .= '<tr><td>'.$itemName.'<div><b>Order Number: </b>#'.$orderNo.'<br><a href="'.$product_url.'" target="_Blank" class="smalllink">View full description</a></div></td><td>'.$quantity.'</td><td>'.number_format($total,2).'</td><td>'.$orderDate.'</td><td align="center">'.$product_image.'</td></tr>';
        }
    }
}
echo $output;
?>
</table>

<div id="ufproducts" style="padding:35px 0 0 0;">
<h3 class="morehd">Other Products You May Like</h3>
<?php 
$args = array('status'  => 'publish','limit'   => 1,'featured'=> true,);
$featured_products = wc_get_products($args);

if (!empty($featured_products)) {
    echo do_shortcode('[featured_products limit="5" columns="5"]');
}else{
    echo do_shortcode('[recent_products limit="5" columns="5"]');
}
?>
</div>