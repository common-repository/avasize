<?php
/**
 * Avasize Widget.
 *
 * Handle order return and cancellation
 *
 * @package Avasize/Widgets
 */

defined( 'ABSPATH' ) || exit;

/**
 * Action return class.
 */
class AVSZ_Action_Return {

    public static function getDomainFromWebsite($website = '') {
        if ((string) $website !== '' && preg_match('/^(?:https?:\/\/)?(?:[^@\/\n]+@)?(:www\.)?([^:\/?\n]+)/i', $website, $matches)) {
            if (isset($matches[2])) {
                return $matches[2];
            }
        }
        return '';
    }

    public static function handleReturn($ordId, $isCancelledOrder = false) {
        $returns = array();
        $order = wc_get_order($ordId);
        if ($isCancelledOrder) {
            $orderRefunds = array(
                $order
            );
        } else {
            $orderRefunds = $order->get_refunds();
        }
        foreach( $orderRefunds as $refund ) {
            foreach( $refund->get_items() as $itemData ) {
                $returnData = array();
                $returnData['order_unique_reference'] = (string) $ordId;
                $product = wc_get_product($itemData->get_product_id());
                $returnData['product_name'] = (string) $product->get_name();
                $returnData['product_unique_ref'] = (string) $itemData->get_product_id() . '_' . (string) $itemData->get_variation_id();
                $returnData['quantity_return'] = (string) abs($itemData->get_quantity());
                if (isset($returns[$returnData['product_unique_ref']])) {
                    $returns[$returnData['product_unique_ref']]['quantity_return'] = (string) ((int) $returns[$returnData['product_unique_ref']]['quantity_return'] + (int) $returnData['quantity_return']);
                } else {
                    $returns[$returnData['product_unique_ref']] = $returnData;
                }
            }
        }
        /** Get avasize plugin widget settings */
        $avmConf = array_shift(get_option('widget_avasz_widget_avszbtn'));
        $urlAPI = 'https://api.avasize.com/v1/';
        /** Get site domain */
        $siteDomain = self::getDomainFromWebsite(get_site_url());
        /** Foreach return send curl to avasize API */
        foreach ($returns as $product_return) {
            $res = wp_remote_post($urlAPI . 'returns?shop=' . $siteDomain, array(
                'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
                'body'        => json_encode($product_return),
                'method'      => 'POST',
                'data_format' => 'body',
            ));

            if ($avmConf['activeLogErrorDetail'] === 'on') {
                /** Write debug details */
                ob_start();
                echo "\n--- AVASIZE-PRODUCT-RETURN - DEBUG ---\n";
                var_dump(json_encode($product_return));
                echo "\n---\n";
                var_dump($urlAPI . 'returns?shop=' . $siteDomain);
                echo "\n---\n";
                var_dump($res);
                echo "\n--- END AVASIZE-PRODUCT-RETURN - DEBUG ---\n";
                error_log(ob_get_clean());
            }

            if (isset($res['response'], $res['response']['code']) && (floor((int) $res['response']['code'] / 100) == 5 || floor((int) $res['response']['code'] / 100) == 4)) {
                error_log("Avasize plugin cURL response with status " . (int)(int) $res['response']['code'] . "\n");
                $errLog = "*" . $callType . "* error in avasize woocommerce module version *" . AVSZ_VERSION . "* on *". get_bloginfo('name') ."* store (status):\n". (int) (int) $res['response']['code'] . "\n";
                error_log($errLog);
                $errLog = "*" . $callType . "* error in avasize woocommerce module version *" . AVSZ_VERSION . "* on *". get_bloginfo('name') ."* store (response content):\n". $res['body'] . "\n" . "------------------------" . "\n";
                error_log($errLog);
            }
        }
    }

}