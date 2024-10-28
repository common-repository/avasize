<?php
/**
 * Avasize Widget.
 *
 * Displays avasize button on product page
 *
 * @package Avasize/Widgets
 */

defined( 'ABSPATH' ) || exit;

/**
 * Widget avasize library class.
 */
class AVSZ_Widget_AvszBtn extends WP_Widget {
    public $configFields = array(
        'activeLogErrorDetail',
    );
    public $configFieldsDefaultValues = array(
        'activeLogErrorDetail' => 'off'
    );
    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct(
// widget ID
            'avasz_widget_avszbtn',
// widget name
            __('Avasize Button', ' avasize'),
// widget description
            array( 'description' => __( 'Display the avasize button.', 'avasize' ), )
        );
    }
    /**
     * Updates a particular instance of a widget.
     *
     * @see WP_Widget->update
     *
     * @param array $new_instance New Instance.
     * @param array $old_instance Old Instance.
     *
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        foreach ($this->configFields as $field) {
            $instance[$field] = isset( $old_instance[$field] ) ? wp_strip_all_tags( $old_instance[$field] ) : isset( $this->configFieldsDefaultValues[$field] ) ? wp_strip_all_tags( $this->configFieldsDefaultValues[$field] ) : '';
            if ( isset( $new_instance[$field] ) ) {
                $instance[$field] = $new_instance[$field];
            }
        }
        return $instance;
    }

    /**
     * Outputs the settings update form.
     *
     * @see WP_Widget->form
     *
     * @param array $instance Instance.
     */
    public function form( $instance ) {
        foreach ($this->configFields as $field) {
            $fVal = isset( $this->configFieldsDefaultValues[$field] ) ? wp_strip_all_tags( $this->configFieldsDefaultValues[$field] ) : '';
            if ( isset( $instance[$field] ) ) {
                $fVal = $instance[$field];
            }
            if ($field === 'activeLogErrorDetail') {
                echo '<p>
    <input class="checkbox" type="checkbox" id="' . $this->get_field_id($field) . '" name="' . $this->get_field_name($field) . '"' . ($fVal === 'on' ? ' checked="checked"' : '') . ' /> 
    <label for="' . $this->get_field_id($field) . '">' . __( 'avsz_' . $field, 'avasize' ) . '</label>
</p>';
            }
        }
    }

    /**
     * Output widget.
     *
     * @param array $args     Arguments.
     * @param array $instance Widget instance.
     */
    public function widget( $args, $instance ) {
        global $wp;

        $configOfPage = '';
        /** Attempt to get product id */
        global $product;
        if (is_product() && is_object($product) && method_exists($product, 'get_id')) {
            $configOfPage = "
            window.avasizeStore.product = {
                    reference : '" . $product->get_id() . "',
                    language: '". substr( get_bloginfo ( 'language' ), 0, 2 ) ."',
                };
            ";
        } elseif (is_order_received_page()) {
            $ordId = (int) $wp->query_vars['order-received'];
            if ($ordId > 0) {
                $order = wc_get_order($ordId);
                if (is_object($order) && method_exists($order, 'get_items')) {
                    $res = array(
                        'transaction' => array()
                    );
                    $products = array();
                    $items = $order->get_items();
                    foreach ($items as $itemData)
                    {
                        $product = wc_get_product($itemData->get_product_id());
                        if ($product) {
                            $products[$product->get_id() . '_' . $itemData->get_variation_id()] = array(
                                'product_unique_ref' => $product->get_id() . '_' . $itemData->get_variation_id(),
//                                'sku' => $product->get_sku(),
                                'product_quantity' => $itemData->get_quantity(),
                                'product_name' => $product->get_name(),
                                'price_tax_exc' => round(wc_get_price_excluding_tax($product), 2),
                                'price_tax_inc' => round(wc_get_price_including_tax($product), 2),
                                'item_id' => 0
                            );
                        }
                    }
                    $res['transaction'] = array(
                        'currency_iso_code' => $order->get_currency(),
                        'total_of_reference' => round($order->get_total(), 2),
                        'original_total_tax_exc' => round($order->get_total() - $order->get_total_tax(), 2),
                        'original_total_tax_inc' => $order->get_total(),
                        'reference' => (string) $order->get_id(),
                        'order_detail' => $products
                    );
                    $configOfPage = "
                        window.avasizeStore.order = '" . str_replace("'", "\\'", json_encode($res)) . "';
                    ";
                }
            }
        }

        wc_enqueue_js("
        window.avasizeStore = {};
        ". $configOfPage . "
        window.avasizeStore.shopModuleVersion = '" . AVSZ_VERSION . "';
        window.avasizeStore.shopVersion = 'WC" . WOOCOMMERCE_VERSION . "+WP" . $GLOBALS['wp_version'] . "';
        /** Include avasize library */
        (function(v,s,i){a=v.createElement(s),m=v.getElementsByTagName(s)[0];a.async=1;a.src=i;m.parentNode.insertBefore(a,m)})(document,'script','https://cdn.avasize.com/ext/shops-scripts/avsz-main?shop='+document.location.hostname);

");

    }
}