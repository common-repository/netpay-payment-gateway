<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright 2018 NetPay. All rights reserved.
 */

require_once("lib/netpay-php/lib/NetPay.php");

class WC_NetPay_Card_Gateway extends WC_Payment_Gateway
{
    protected $GATEWAY_NAME = "WC_NetPay_Card_Gateway";
    protected $store_customer = null;
    protected $store_user = null;
    protected $store_password = null;
    public $lang_options;
    protected $method_of_delivery = null;
    protected $category = null;
    public $mid = null;
    protected $trans_type = null;
    protected $store_city = null;
    protected $store_postcode = null;
    protected $store_primary_type_food = null;
    protected $store_secundary_type_food = null;
    protected $store_level = null;
    protected $store_service_type = null;
    protected $promotion_products = null;

    /**
     * Initialize the class.
     */
    public function __construct()
    {
        $this->lang_options = \NetPay\Functions::get_lang_options();

        $this->id = 'netpaycard';
        $this->method_title = 'NetPay';
        $this->method_description = $this->lang_options['method_description'];
        $this->has_fields = true;

        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->settings['title'];
        $this->description = $this->lang_options['description_payment'];
        $this->icon = $this->settings['alternate_imageurl'] ?
            $this->settings['alternate_imageurl'] :
            WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__))
            . '/images/tarjetas.png';

        $this->store_customer = $this->settings['store_customer'];
        $this->store_user = $this->settings['store_user'];
        $this->store_password = $this->settings['store_password'];
        $this->method_of_delivery = $this->settings['method_of_delivery'];
        $this->category = $this->settings['category'];
        
        $this->msg['message'] = "";
        $this->msg['class'] = "";

        $this->trans_type = $this->settings['trans_type'];
        $this->promotion_products = $this->settings['promotion_products'];
        $this->mid = $this->settings['mid'];

        $this->store_city = $this->settings['store_city'];
        $this->store_postcode = $this->settings['store_postcode'];
        $this->store_primary_type_food = $this->settings['store_primary_type_food'];
        $this->store_secundary_type_food = $this->settings['store_secundary_type_food'];

        $this->store_level = $this->settings['store_level'];
        $this->store_service_type = $this->settings['store_service_type'];

        $this->init_actions();
    }

    /**
     * Hooks a function on to a specific woocommerce action.
     */
    private function init_actions()
    {
        add_action(
            'woocommerce_api_' . strtolower(get_class($this)),
            array($this, 'check_netpaycard_response')
        );

        add_action(
            'woocommerce_update_options_payment_gateways_' . $this->id,
            array($this, 'process_admin_options')
        );

        add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));

        //add_action('woocommerce_after_order_notes', array($this, 'promotion_select'));

        add_action('woocommerce_checkout_update_order_meta', array($this, 'custom_checkout_field_update_order_meta'));

        add_action('woocommerce_order_status_changed', array($this, 'custom_order_status_change_update_order_meta'), 10, 3);

        add_filter('gettext', array($this, 'translate_woocommerce_strings'), 10, 4);

        add_filter( 'woocommerce_default_address_fields' , array($this, 'custom_override_default_address_fields') );

        add_filter('woocommerce_billing_fields', array($this, 'custom_billing_fields'));

        if ($this->trans_type == 'PreAuth') 
        {
            add_filter( 'wc_order_statuses', 'netpay_add_order_statuses' );
            add_filter( 'bulk_actions-edit-shop_order', 'netpay_custom_dropdown_bulk_actions_shop_order', 50, 1 );
        }
    }

    // Our hooked in function - $address_fields is passed via the filter!
    function custom_override_default_address_fields( $address_fields ) {
        $address_fields['first_name']['custom_attributes'] = array(
            'maxlength' => 35
        );
        $address_fields['last_name']['custom_attributes'] = array(
            'maxlength' => 35
        );
        $address_fields['address_1']['custom_attributes'] = array(
            'maxlength' => 50
        );
        $address_fields['address_2']['custom_attributes'] = array(
            'maxlength' => 50
        );
        $address_fields['city']['custom_attributes'] = array(
            'maxlength' => 90
        );
        $address_fields['state']['custom_attributes'] = array(
            'maxlength' => 30
        );
        $address_fields['postcode']['custom_attributes'] = array(
            'maxlength' => 20
        );

        return $address_fields;
    }

    function custom_billing_fields( $fields ) {
        $fields['billing_email']['custom_attributes'] = array(
            'maxlength' => 50
        );

        $fields['billing_address_2']['required'] = true;
        $fields['billing_phone']['custom_attributes'] = array(
            'maxlength' => 15
        );
    
        return $fields;
    }

    /**
     * Build the administration fields for the Gateway.
     */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'type' => 'checkbox',
                'title' => $this->lang_options['form_fields']['enabled']['title'],
                'label' => $this->lang_options['form_fields']['enabled']['label'],
                'default' => 'yes'
            ),
            'title' => array(
                'type' => 'text',
                'title' => $this->lang_options['form_fields']['title']['title'],
                'description' => $this->lang_options['form_fields']['title']['description'],
                'default' => $this->lang_options['form_fields']['title']['default'],
            ),
            'store_customer' => array(
                'type' => 'text',
                'title' => $this->lang_options['form_fields']['store_customer']['title'],
                'default' => '',
            ),
            'store_user' => array(
                'type' => 'text',
                'title' => $this->lang_options['form_fields']['store_user']['title'],
                'default' => ''
            ),
            'store_password' => array(
                'type' => 'password',
                'title' => $this->lang_options['form_fields']['store_password']['title'],
                'default' => ''
            ),
            'promotion' => array(
                'type' => 'checkbox',
                'title' => $this->lang_options['form_fields']['promotion']['title'],
                'label' => $this->lang_options['form_fields']['promotion']['label'],
                'default' => 'no'
            ),
            'promotion_products' => array(
                'title' => __($this->lang_options['form_fields']['promotion_products']['title']),
                'type' => 'multiselect',
                'description' => __($this->lang_options['form_fields']['promotion_products']['description']),
                'options'       => $this->get_products(),
                'default' => '',
                'css'     => 'height:150px;',
            ),
            'promotion_03' => array(
                'type' => 'checkbox',
                'title' => $this->lang_options['form_fields']['promotion']['number_months'],
                'label' => $this->lang_options['form_fields']['promotion']['months_without_interest_3'],
                'default' => 'yes'
            ),
            'promotion_06' => array(
                'type' => 'checkbox',
                'label' => $this->lang_options['form_fields']['promotion']['months_without_interest_6'],
                'default' => 'yes'
            ),
            'promotion_09' => array(
                'type' => 'checkbox',
                'label' => $this->lang_options['form_fields']['promotion']['months_without_interest_9'],
                'default' => 'yes'
            ),
            'promotion_12' => array(
                'type' => 'checkbox',
                'label' => $this->lang_options['form_fields']['promotion']['months_without_interest_12'],
                'default' => 'yes'
            ),
            'promotion_18' => array(
                'type' => 'checkbox',
                'label' => $this->lang_options['form_fields']['promotion']['months_without_interest_18'],
                'default' => 'yes'
            ),
            'trans_type' => array(
                'title' => __($this->lang_options['form_fields']['trans_type']['title']),
                'type' => 'select',
                'description' => __($this->lang_options['form_fields']['trans_type']['description']),
                'options'       => array(
                    'Auth' => __( 'Auth'),
                    'PreAuth' => __( 'PreAuth / PostAuth'),
                ),
                'default' => 'Auth'
            ),
            'mid' => array(
                'title' => __($this->lang_options['form_fields']['mid']['title']),
                'type' => 'select',
                'description' => __($this->lang_options['form_fields']['mid']['description']),
                'options'       => array(
                    'agencias' => __( $this->lang_options['form_fields']['mid']['agencias']),
                    'donaciones' => __( $this->lang_options['form_fields']['mid']['donaciones']),
                    'escuelas' => __( $this->lang_options['form_fields']['mid']['escuelas']),
                    'tickets' => __( $this->lang_options['form_fields']['mid']['tickets']),
                    'restaurant' => __( $this->lang_options['form_fields']['mid']['restaurant']),
                    'retail' => __( $this->lang_options['form_fields']['mid']['retail']),
                    'generales' => __( $this->lang_options['form_fields']['mid']['generales']),
                    'profesionales' => __( $this->lang_options['form_fields']['mid']['profesionales']),
                ),
                'default' => 'retail'
            ),
            'method_of_delivery' => array(
                'type' => 'text',
                'title' => $this->lang_options['form_fields']['method_of_delivery']['title'],
                'description' => __($this->lang_options['form_fields']['method_of_delivery']['description']),
                'default' => ''
            ),
            'category' => array(
                'type' => 'text',
                'title' => $this->lang_options['form_fields']['category']['title'],
                'description' => __($this->lang_options['form_fields']['category']['description']),
                'default' => ''
            ),
            'store_city' => array(
                'type' => 'text',
                'title' => $this->lang_options['form_fields']['store_city']['title'],
                'description' => __($this->lang_options['form_fields']['store_city']['description']),
                'default' => ''
            ),
            'store_postcode' => array(
                'type' => 'text',
                'title' => $this->lang_options['form_fields']['store_postcode']['title'],
                'description' => __($this->lang_options['form_fields']['store_postcode']['description']),
                'default' => ''
            ),
            'store_primary_type_food' => array(
                'type' => 'text',
                'title' => $this->lang_options['form_fields']['store_primary_type_food']['title'],
                'description' => __($this->lang_options['form_fields']['store_primary_type_food']['description']),
                'default' => ''
            ),
            'store_secundary_type_food' => array(
                'type' => 'text',
                'title' => $this->lang_options['form_fields']['store_secundary_type_food']['title'],
                'description' => __($this->lang_options['form_fields']['store_secundary_type_food']['description']),
                'default' => ''
            ),
            'store_level' => array(
                'type' => 'text',
                'title' => $this->lang_options['form_fields']['store_level']['title'],
                'description' => __($this->lang_options['form_fields']['store_level']['description']),
                'default' => ''
            ),
            'store_service_type' => array(
                'type' => 'text',
                'title' => $this->lang_options['form_fields']['store_service_type']['title'],
                'description' => __($this->lang_options['form_fields']['store_service_type']['description']),
                'default' => ''
            ),
        
        );
    }

    /**
     * Get the products that already created.
     */
    public function get_products()
    {
        //return array_map('wc_get_product', get_posts(['post_type'=>'product','nopaging'=>true]));
        $get_posts = json_decode(json_encode(get_posts( array( 'post_type' => 'product', 'posts_per_page' => -1, "post_status" => "publish" ))), true);
        $get_product_variation = json_decode(json_encode(get_posts( array( 'post_type' => 'product_variation', 'posts_per_page' => -1, "post_status" => "publish" ))), true);
        $products = array();

        for($i=0;$i<count($get_product_variation);$i++) {
            $products[$get_product_variation[$i]['ID']] = $get_product_variation[$i]['post_title'];
        }
        for($i=0;$i<count($get_posts);$i++) {
            $exist = false;
            for($j=0;$j<count($get_product_variation);$j++) {
                if($get_posts[$i]['ID'] == $get_product_variation[$j]['post_parent']) {
                    $exist = true;
                }
            }
            if(!$exist) {
                $products[$get_posts[$i]['ID']] = $get_posts[$i]['post_title'];
            }
        }
        
        return $products;
    }

    /**
     * Display the form in the admin for the configuration of the Gateway.
     */
    public function admin_options()
    {
        include_once('templates/admin.php');
    }

    /**
     * Show the payment description that is set in plugin.
     * There are no payment fields during the fill of billing and shipping information, but we want to show the description if set.
     */
    public function payment_fields()
    {
        // ok, let's display some description before the payment form
        if ( $this->description ) {
            // you can instructions for test mode, I mean test card numbers etc.
            if ( $this->testmode ) {
                $this->description .= ' TEST MODE ENABLED. Si realizas transacciones no se enviarán al banco emisor.';
                $this->description  = trim( $this->description );
            }
            // display the description with <p> tags etc.
            echo wpautop( wp_kses_post( $this->description ) );
        }
     
        if($this->trans_type == 'Auth' && $this->method_title == "NetPay")
        {
            $total = (int)(WC()->cart->total);
            $meses_sin_interes_original = \NetPay\Functions::promotion_options($this->settings);
            $meses_sin_interes = null;
            $items = WC()->cart->get_cart();
            $apply_msi = true;
            $product_msi = false;

            if($this->promotion_products != null && $this->settings['promotion'] == 'yes') {
                foreach($items as $item => $values) { 
                    $_product_id = $values['data']->get_id();
                    if(in_array($_product_id, $this->promotion_products)) {
                        $product_msi = true;
                    }
                }
                foreach($items as $item => $values) { 
                    $_product_id = $values['data']->get_id();
                    if(!in_array($_product_id, $this->promotion_products) && $product_msi) {
                        $apply_msi = false;
                        $product = wc_get_product( $values['data']->get_id() );
                        wc_add_notice( 'El producto '.$product->get_title().' NO aplica a meses sin intereses.', 'notice');
                    }
                }
            }    

            if ($total < 300) {
                $meses_sin_interes = array_slice($meses_sin_interes_original, 0, 0, true);
            } elseif ($total >= 300 && $total < 600) {
                $meses_sin_interes = array_slice($meses_sin_interes_original, 0, 2, true);
            } elseif ($total >= 600 && $total < 900) {
                $meses_sin_interes = array_slice($meses_sin_interes_original, 0, 3, true);
            } elseif ($total >= 900 && $total < 1200) {
                $meses_sin_interes = array_slice($meses_sin_interes_original, 0, 4, true);
            } elseif ($total >= 1200 && $total < 1800) {
                $meses_sin_interes = array_slice($meses_sin_interes_original, 0, 5, true);
            } elseif ($total >= 1800) {
                $meses_sin_interes = $meses_sin_interes_original;
            }

            if($apply_msi === false && $this->settings['promotion'] == 'yes' && $product_msi) {
                wc_add_notice( 'El pago se realizará en una sola exhibición. Para comprar a meses sin intereses, todos los productos del carrito deben tener activa la promoción. Te sugerimos realizar compras separadas para aprovechar los MSI.', 'notice');
                $meses_sin_interes = array_slice($meses_sin_interes_original, 0, 0, true);
            }

            $this->meses_sin_interes = $meses_sin_interes;
            if ($this->settings['promotion'] == 'yes' && $product_msi) {
                echo '<div>';
                woocommerce_form_field('netpay_promotion', array(
                    'type' => 'select',
                    'required' => true,
                    'options' => $meses_sin_interes,
                    'label' => $this->lang_options['form_fields']['promotion']['title'],
                ), '00');
                echo '</div>';
            }
        }
    }

    /**
     * Display in the receipt page the NetPay payment form.
     */
    function receipt_page($order)
    {
        if (empty(WC()->cart->get_cart())) {
            wp_redirect('/');
            exit();
        }

        $result = $this->generate_netpay_variables($order);

        wc_get_template(
            'payment.php',
            compact('result'),
            '',
            plugin_dir_path(__FILE__) . '/templates/'
        );
    }

    /**
     * Display the months without interest select if is enable.
     */
    public function promotion_select()
    {
        if($this->trans_type == 'Auth' && $this->method_title == "NetPay")
        {
            $total = (int)(WC()->cart->total);
            $meses_sin_interes_original = \NetPay\Functions::promotion_options($this->settings);
            $meses_sin_interes = null;
            $items = WC()->cart->get_cart();
            $apply_msi = true;
            $product_msi = false;
            if($this->promotion_products != null && $this->settings['promotion'] == 'yes') {
                foreach($items as $item => $values) { 
                    $_product_id = $values['data']->get_id();
                    if(in_array($_product_id, $this->promotion_products)) {
                        $product_msi = true;
                    }
                }
                foreach($items as $item => $values) { 
                    $_product_id = $values['data']->get_id();
                    if(!in_array($_product_id, $this->promotion_products) && $product_msi) {
                        $apply_msi = false;
                        $product = wc_get_product( $values['data']->get_id() );
                        wc_add_notice( 'El producto '.$product->get_title().' NO aplica a meses sin intereses.', 'notice');
                    }
                }
            }    

            if ($total < 300) {
                $meses_sin_interes = array_slice($meses_sin_interes_original, 0, 0, true);
            } elseif ($total >= 300 && $total < 600) {
                $meses_sin_interes = array_slice($meses_sin_interes_original, 0, 2, true);
            } elseif ($total >= 600 && $total < 900) {
                $meses_sin_interes = array_slice($meses_sin_interes_original, 0, 3, true);
            } elseif ($total >= 900 && $total < 1200) {
                $meses_sin_interes = array_slice($meses_sin_interes_original, 0, 4, true);
            } elseif ($total >= 1200 && $total < 1800) {
                $meses_sin_interes = array_slice($meses_sin_interes_original, 0, 5, true);
            } elseif ($total >= 1800) {
                $meses_sin_interes = $meses_sin_interes_original;
            }

            if($apply_msi === false && $this->settings['promotion'] == 'yes' && $product_msi) {
                wc_add_notice( 'El pago se realizará en una sola exhibición. Para comprar a meses sin intereses, todos los productos del carrito deben tener activa la promoción. Te sugerimos realizar compras separadas para aprovechar los MSI.', 'notice');
                $meses_sin_interes = array_slice($meses_sin_interes_original, 0, 0, true);
            }

            if ($this->settings['promotion'] == 'yes' && $product_msi) {
                echo '<div>';
                woocommerce_form_field('netpay_promotion', array(
                    'type' => 'select',
                    'required' => true,
                    'options' => $meses_sin_interes,
                    'label' => $this->lang_options['form_fields']['promotion']['title'],
                ), '00');
                echo '</div>';
            }
        }
    }

    /**
     * Save the months without interest in the order if the user select.
     */
    function custom_checkout_field_update_order_meta($order_id)
    {
        if (isset($_POST['netpay_promotion'])) {
            update_post_meta($order_id, '_promotion', sanitize_text_field($_POST['netpay_promotion']));
        }
    }

    /**
     * Save the transaction token in the order.
     */
    function custom_token_field_update_order_meta($order_id, $token)
    {
        if (isset($token)) {
            update_post_meta($order_id, '_transaction_token_id', sanitize_text_field($token));
        }
    }

    /**
     * Save the transaction info in the order.
     */
    function custom_transaction_fields_update_order_meta($order_id, $transaction)
    {
        foreach ($transaction as $key => $value) {
            $key_lower = strtolower($key);
            update_post_meta($order_id, '_transaction_' . $key_lower, sanitize_text_field($value));
        }
    }

    /**
     * Save the new status of a order.
     */
    function custom_order_status_change_update_order_meta($order_id, $old_status, $new_status)
    {
        update_post_meta($order_id, '_old_status', sanitize_text_field($old_status));
        update_post_meta($order_id, '_new_status', sanitize_text_field($new_status));

        if ($new_status == 'cancelled') {
            if ($this->cancelled_order($order_id) == false) {
                if (version_compare(WOOCOMMERCE_VERSION, '3.0.0', '>=')) {
                    throw new Exception('', 1);
                }
            }
        }
    }

    /**
     * Modify translated texts.
     */
    function translate_woocommerce_strings($translated, $original, $context)
    {
        // Use the text string exactly as it is in the translation file
        if ($translated == 'This order&rsquo;s status is &ldquo;%s&rdquo;&mdash;it cannot be paid for. Please contact us if you need assistance.') {
            $translated = "This order&rsquo;s status is &ldquo;%s&rdquo;.";
        }

        if ($translated == 'El estado de este pedido es "%s". No se ha podido pagar. Por favor, ponte en contacto con nosotros si necesitas ayuda.') {
            $translated = "El estado de este pedido es \"%s\".";
        }

        return $translated;
    }

    /**
     * Rollback the status of a order.
     */
    public function rollback_order_status($order_id)
    {
        $order = new WC_Order($order_id);
        \NetPay\Functions::rollback_order_status($order);
    }

    /**
     * Request to NetPay to cancelled a transaction.
     */
    public function cancelled_order($order_id)
    {
        try {
            $order = new WC_Order($order_id);
            $date_paid = $order->get_date_paid();
            if(!empty($date_paid)) {
                if (!$this->can_cancelled($order_id)) {
                    return false;
                }
    
                $result = $this->post_cancelled($order_id);
    
                if ($result === false) {
                    return false;
                }

                $this->update_order_cancelled($order_id, $result['result']);
            }

            return true;
        } catch (Exception $e) {
            $order = new WC_Order($order_id);
            $order->add_order_note($e->getMessage());

            $this->rollback_order_status($order_id);

            return false;
        }
    }

    /**
     * Check if the user can cancel an order
     */
    private function can_cancelled($order_id)
    {
        $order = new WC_Order($order_id);

        try {
            if (!$this->is_today_day_paid($order)) {
                $message = $this->lang_options['cancelled']['cannot_cancelled'];

                throw new Exception($message, 1);
            }

            return true;
        } catch (Exception $e) {
            $description = $e->getMessage();

            $order->add_order_note($description);

            $this->rollback_order_status($order_id);

            return false;
        }
    }

    /**
     * Compare current date with the date that the order was paid
     */
    private function is_today_day_paid($order)
    {
        $timezone = wc_timezone_string();
        if(empty($timezone) || $timezone == null) {
            $timezone = 'America/Monterrey';
        }
        if (version_compare(WOOCOMMERCE_VERSION, '3.0.0', '<')) {
            $current_date = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone($timezone));
            $current_date = $current_date->format('Y-m-d');

            $date_paid = new DateTime($order->paid_date, new DateTimeZone($timezone));
            $date_paid = $date_paid->format('Y-m-d');
        } else {
            $timestamp = strtotime(date('Y-m-d H:i:s'));

            $current_date = new WC_DateTime("@{$timestamp}", new DateTimeZone($timezone));
            $current_date = $current_date->format('Y-m-d');

            $date_paid = $order->get_date_paid();
            if(!empty($date_paid)) {
                $date_paid->setTimezone(new DateTimeZone($timezone));
                $date_paid = $date_paid->format('Y-m-d');
            }
        }

        return strtotime($current_date) == strtotime($date_paid);
    }

    /**
     * Save and order note if the cancelled is success.
     */
    private function update_order_cancelled($order_id, $result)
    {
        if (isset($result['response']) && $result['response']['responseCode'] != '00') {
            $message_bank = \NetPay\Functions::bank_code_message($result['response']['responseCode']);
            $message = $this->lang_options['cancelled']['error_bank'] . " " . $message_bank;

            throw new Exception($message, 1);
        }

        $note = sprintf($this->lang_options['cancelled']['complete'], $order_id);

        $order = new WC_Order($order_id);

        $order->add_order_note($note);

        $this->restore_order_stock($order_id);
    }

    /**
     * Restore the stock base in the order items if a transaction cancelled is success.
     */
    private function restore_order_stock($order_id)
    {
        $order = new WC_Order($order_id);

        if (!get_option('woocommerce_manage_stock') == 'yes' && !sizeof($order->get_items()) > 0) {
            return;
        }

        foreach ($order->get_items() as $item) {
            if ($item['product_id'] > 0) {
                $_product = $order->get_product_from_item($item);

                if ($_product && $_product->exists() && $_product->managing_stock()) {
                    $old_stock = $_product->stock;

                    $qty = apply_filters('woocommerce_order_item_quantity', $item['qty'], $this, $item);

                    $new_quantity = $_product->increase_stock($qty);

                    do_action('woocommerce_auto_stock_restored', $_product, $item);

                    $order->add_order_note(sprintf($this->lang_options['cancelled']['stock_restored'], $item['product_id'], $old_stock, $new_quantity));
                }
            }
        }
    }

    /**
     * Generate NetPay url and token.
     */
    public function generate_netpay_variables($order_id)
    {
        $jwt = $this->jwt();

        if ($jwt === false) {
            return false;
        }

        $order = new WC_Order($order_id);

        $result = $this->post_checkout($order, $jwt);

        if ($result === false) {
            return false;
        }

        $redirect_url = add_query_arg(
            'wc-api',
            strtolower(get_class($this)),
            get_site_url()
        );

        $merchant_response_url = \NetPay\Functions::base64url_encode($redirect_url);

        $merchant_reference_code = $result['merchant_reference_code'];

        if (!empty($merchant_reference_code)) {
            update_post_meta($order->id, '_merchant_reference_code', sanitize_text_field($merchant_reference_code));
        }

        $web_authorizer_url = add_query_arg(
            'checkoutDetail',
            'true',
            $result['web_authorizer_url']
        );

        $web_authorizer_url = add_query_arg(
            'checkoutTokenId',
            $result['checkout_token_id'],
            $web_authorizer_url
        );

        $web_authorizer_url = add_query_arg(
            'MerchantResponseURL',
            $merchant_response_url,
            $web_authorizer_url
        );

        $receipt_page_title = $this->lang_options['receipt_page_title'];

        return compact('receipt_page_title', 'web_authorizer_url', 'jwt');
    }

    /**
     * Process the payment and return the result.
     */
    public function process_payment($order_id)
    {
        global $woocommerce;

        $order = new WC_Order($order_id);

        return array(
            'result' => 'success',
            'redirect' => add_query_arg(
                'order-pay',
                $order->id,
                add_query_arg('key', $order->order_key, $order->get_checkout_payment_url(true))
            )
        );
    }

    /**
     * Check for valid NetPay transaction server callback.
     */
    function check_netpaycard_response()
    {
        if (!isset($_REQUEST['transactionToken'])) {
            $this->thank_you($this->lang_options['transaction']['error']['callback'], 'error');

            exit();
        }

        $result = $this->get_transaction($_REQUEST['transactionToken']);

        if ($result === false) {
            exit();
        }

        try {
            $this->update_order_transaction($result['result']);
        } catch (Exception $e) {
            $this->thank_you($e->getMessage(), 'error');

            exit();
        }
    }

    /**
     * Check the status of the transaction and if is complete call the payment complete method of the order.
     */
    private function update_order_transaction($result)
    {
        global $woocommerce;

        $merchant_reference_code = $result['transaction']['merchantReferenceCode'];
        $response_code = $result['response']['responseCode'];
        $transaction = $result['transaction'];
        $transaction_token_id = $result['transactionTokenId'];

        $orders_post = get_posts(array(
            'numberposts' => 1,
            'meta_key' => '_merchant_reference_code',
            'meta_value' => $merchant_reference_code,
            'post_type' => 'shop_order',
            'post_status' => 'any',
        ));

        if (empty($orders_post)) {
            $message = $this->lang_options['transaction']['error']['empty_order'];

            throw new Exception($message, 1);
        }

        $order_post = $orders_post[0];

        $order = new WC_Order($order_post->ID);

        if (isset($result['response']) && $result['response']['responseCode'] != '00') {
            $message_bank = \NetPay\Functions::bank_code_message($result['response']['responseCode']);
            $message = $this->lang_options['transaction']['error']['error_bank'] . " " . $message_bank;

            $order->add_order_note($message);

            throw new Exception($message, 1);
        }

        if ($order->status === 'completed') {
            $message = sprintf($this->lang_options['transaction']['error']['is_complete'], $order->id);

            throw new Exception($message, 1);
        }

        if ($response_code == '00') {
            $message = $this->lang_options['transaction']['complete'];

            $this->custom_token_field_update_order_meta($order->id, $transaction_token_id);

            $this->custom_transaction_fields_update_order_meta($order->id, $transaction);

            if ($order->status == 'pending') {
                $order->payment_complete();
                $order->add_order_note($message);
                $woocommerce->cart->empty_cart();

                $note = $this->lang_options['transaction']['payment_complete'];

                \NetPay\Functions::transaction_note($order, $transaction, $note);
            }

            $this->thank_you($message);
        }

        exit();
    }

    /**
     * Display a message of the NeyPay transaction result.
     */
    function thank_you($message = '', $type = 'message')
    {
        wc_get_template(
            'thank-you.php',
            compact('message', 'type'),
            '',
            plugin_dir_path(__FILE__) . '/templates/'
        );
    }

    /**
     * Call a API to get a token.
     */
    public function jwt()
    {
        try {
            $data = array(
                'user' => $this->store_user,
                'password' => $this->store_password,
                'storeIdAdq' => $this->store_customer,
            );

            $result = \NetPay\Api\Login::post($data);

            return $result['result']['token'];
        } catch (Exception $e) {
            $description = $e->getMessage();

            $this->display_error($description);

            return false;
        }
    }

    /**
     * Call a API to submit the shopping cart and get the data to generate a url to pay.
     */
    private function post_checkout($order, $jwt)
    {
        try {
            $fields = $this->order_fields($order);

            $mdds = self::get_mdds($fields);

            $result = \NetPay\Api\Checkout::post($jwt, $fields, $mdds, $this->trans_type);

            $checkout_token_id = $result['result']['response']['checkoutTokenId'];
            $merchant_reference_code = $result['result']['response']['merchantReferenceCode'];
            $web_authorizer_url = $result['result']['response']['webAuthorizerUrl'];

            return compact('checkout_token_id', 'merchant_reference_code', 'web_authorizer_url');
        } catch (Exception $e) {
            $description = $e->getMessage();

            $checkout_error = $this->lang_options['checkout']['error'];

            $order->add_order_note($checkout_error . "\n" . $description);

            $this->display_error($checkout_error . " " . $description);

            return false;
        }
    }

    private function get_mdds($input)
    {
        $mdds = array();
        switch($this->mid)
        {
            case 'retail':
                $mdds = \NetPay\MID\Retail::get_mdds($input);
                break;
            case 'agencias':
                $mdds = \NetPay\MID\Agencias::get_mdds($input);
                break;
            case 'donaciones':
                $mdds = \NetPay\MID\Donaciones::get_mdds($input);
                break;
            case 'escuelas':
                $mdds = \NetPay\MID\Escuelas::get_mdds($input);
                break;
            case 'tickets':
                $mdds = \NetPay\MID\Tickets::get_mdds($input);
                break;
            case 'restaurant':
                $mdds = \NetPay\MID\Restaurant::get_mdds($input);
                break;
            case 'generales':
                $mdds = \NetPay\MID\Generales::get_mdds($input);
                break;
            case 'profesionales':
                $mdds = \NetPay\MID\Profesionales::get_mdds($input);
                break;
            default:
                $mdds = \NetPay\MID\Retail::get_mdds($input);
        }

        return $mdds;
    }

    /**
     * Get the data that is necessary for the checkout of the shopping cart.
     */
    private function order_fields($order)
    {
        $days = \NetPay\Order::days_first_last_order();

        $currency = "MXN";

        if (version_compare(WOOCOMMERCE_VERSION, '3.0.0', '<')) {
            $currency = $order->get_order_currency();
        }

        if (version_compare(WOOCOMMERCE_VERSION, '3.0.0', '>=')) {
            $currency = $order->get_currency();
        }

        $promotion = \NetPay\Functions::promotion_format($order, $this->settings['promotion']);
        if(strlen($promotion) != 6)
        {
            $promotion = "000000";
        }

        $itemList = \NetPay\ItemList::format($order);

        $regimen_fiscal = self::get_regimen_fiscal($order->id);
        $one_way = get_post_meta($order->id, '_one_way', true);
        $passegers_number = get_post_meta($order->id, '_passegers_number', true);
        $frequency_number = get_post_meta($order->id, '_frequency_number', true);
        $name_passenger1 = get_post_meta($order->id, '_name_passenger1', true);
        $phone_passenger1 = get_post_meta($order->id, '_phone_passenger1', true);
        $name_passenger2 = get_post_meta($order->id, '_name_passenger2', true);
        $phone_passenger2 = get_post_meta($order->id, '_phone_passenger2', true);
        $name_passenger3 = get_post_meta($order->id, '_name_passenger3', true);
        $phone_passenger3 = get_post_meta($order->id, '_phone_passenger3', true);
        $name_passenger4 = get_post_meta($order->id, '_name_passenger4', true);
        $phone_passenger4 = get_post_meta($order->id, '_phone_passenger4', true);
        $ida = get_post_meta($order->id, '_ida', true);
        $persona_fisica = 'N';
        $cuenta_empresarial = 'N';
        if($regimen_fiscal == 'Persona fisica')
        {
            $persona_fisica = 'Y';
        }
        else
        {
            $cuenta_empresarial = 'Y';
        }

        $attributes = self::get_attributes();
        $site = get_bloginfo( 'name', 'raw' );

        $billing_phone = str_replace("+", "", $order->billing_phone);

        return [
            //--retail
            "store_customer" => $this->store_customer,
            "promotion" => $promotion,
            "order_id" => $order->id,
            "bill" => \NetPay\Billing::format($order),
            "ship" => \NetPay\Shipping::format($order),
            "itemList" => $itemList,
            "total" => $order->get_total(),
            "currency" => $currency,
            "days_until_register" => \NetPay\Functions::days_until_register(),
            "completed_orders" => \NetPay\Order::total_completed_orders(),
            "first_order_days" => $days['first_order_days'],
            "last_order_days" => $days['last_order_days'],
            "method_of_delivery" => (!empty($this->method_of_delivery) ? $this->method_of_delivery : '0'),
            "phoneNumber"  => (!empty($order->billing_phone) ? $billing_phone : '0'),
            "category" => (!empty($this->category) ? $this->category : '0'),
            //--donaciones
            "avg_value" => \NetPay\Functions::get_avg(),
			"count_orders_completed" => \NetPay\Functions::get_count_orders_completed(),
			"fecha_registro" => \NetPay\Functions::date_joined(),
			"sku" => \NetPay\Functions::get_sku($itemList),
			"campaign" => \NetPay\Functions::get_campaign($itemList),
			"regimen_fiscal"  => $regimen_fiscal,
            "total_sum" => \NetPay\Functions::get_total_sum(),
            //--restaurant
            'site' => $site,
            "store_city" => (!empty($this->store_city) ? $this->store_city : '0'),
            "store_postcode" => (!empty($this->store_postcode) ? $this->store_postcode : '0'),
            "store_primary_type_food" => (!empty($this->store_primary_type_food) ? $this->store_primary_type_food : '0'),
            "store_secundary_type_food" => (!empty($this->store_secundary_type_food) ? $this->store_secundary_type_food : '0'),
            //--escuelas
            "store_level" => (!empty($this->store_level) ? $this->store_level : '0'),
            //--generales
            "client_id" => \NetPay\Functions::get_current_cliend_id(),
            "store_service_type" => (!empty($this->store_service_type) ? $this->store_service_type : '0'),
            //--agencia de viajes
            "third_party" => (!empty($attributes [0]['third_party']) ? $attributes [0]['third_party'] : '0'),
            "servicio_terrestre" => (!empty($attributes [0]['servicio_terrestre']) ? $attributes [0]['servicio_terrestre'] : '0'),
            "servicio_aereo" => (!empty($attributes [0]['servicio_aereo']) ? $attributes [0]['servicio_aereo'] : '0'),
            "horas_despegue" => (!empty($attributes [0]['horas_despegue']) ? $attributes [0]['horas_despegue']: '0'),
            "horas_uso_servicio_despegue" => (!empty($attributes [0]['horas_uso_servicio_despegue']) ? $attributes [0]['horas_uso_servicio_despegue'] : '0'),
            "ruta" => (!empty($attributes [0]['ruta']) ? $attributes [0]['ruta'] : '0'),
            "ciudad_origen" => (!empty($attributes [0]['ciudad_origen']) ? $attributes [0]['ciudad_origen'] : '0'),
            "ciudad_destino" => (!empty($attributes [0]['ciudad_destino']) ? $attributes [0]['ciudad_destino']: '0'),
            "ruta_completa" => (!empty($attributes [0]['ruta_completa']) ? $attributes [0]['ruta_completa']: '0'),
            "nombre_third_party" => (!empty($attributes [0]['nombre_third_party']) ? $attributes [0]['nombre_third_party']: '0'),
            "incluye_hotel" => (!empty($attributes [0]['incluye_hotel']) ? $attributes [0]['incluye_hotel']: '0'),
            "nombre_hotel" => (!empty($attributes [0]['nombre_hotel']) ? $attributes [0]['nombre_hotel']: '0'),
            "nombre_aerolinea" => (!empty($attributes [0]['nombre_aerolinea']) ? $attributes [0]['nombre_aerolinea']: '0'),
            "nombre_servicio_terrestre" => (!empty($attributes [0]['nombre_servicio_terrestre']) ? $attributes [0]['nombre_servicio_terrestre']: '0'),
            "one_way" => (!empty($one_way) ? $one_way : '0'),
            "passegers_number" => (!empty($passegers_number) ? $passegers_number : '0'),
            "frequency_number" => (!empty($frequency_number) ? $frequency_number : '0'),
            "name_passenger1" => (!empty($name_passenger1) ? $name_passenger1 : '0'),
            "phone_passenger1" => (!empty($phone_passenger1) ? $phone_passenger1 : '0'),
            "name_passenger2" => (!empty($name_passenger2) ? $name_passenger2 : '0'),
            "phone_passenger2" => (!empty($phone_passenger2) ? $phone_passenger2 : '0'),
            "name_passenger3" => (!empty($name_passenger3) ? $name_passenger3 : '0'),
            "phone_passenger3" => (!empty($phone_passenger3) ? $phone_passenger3 : '0'),
            "name_passenger4" => (!empty($name_passenger4) ? $name_passenger4 : '0'),
            "phone_passenger4" => (!empty($phone_passenger4) ? $phone_passenger4 : '0'),
            "persona_fisica" => (!empty($persona_fisica) ? $persona_fisica : '0'),
            "cuenta_empresarial" => (!empty($cuenta_empresarial) ? $cuenta_empresarial : '0'),
            "ida" => (!empty($ida) ? \NetPay\Functions::days_to_depart($ida) : '0'),
            "full_description" => (!empty(\NetPay\Functions::get_full_description($itemList) ? \NetPay\Functions::get_full_description($itemList) : '0')),
            "short_description" => (!empty(\NetPay\Functions::get_short_description($itemList)) ? \NetPay\Functions::get_short_description($itemList) : '0'),
        ];
    }

    private function get_attributes()
    {
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        $attributes = array();  
        foreach($items as $item => $values) { 
            $_product =  wc_get_product( $values['data']->get_id());

            $third_party = $_product->get_attribute( 'third_party' );
            $servicio_terrestre = $_product->get_attribute( 'servicio_terrestre' );
            $servicio_aereo = $_product->get_attribute( 'servicio_aereo' );
            $horas_despegue = $_product->get_attribute( 'horas_despegue' );
            $horas_uso_servicio_despegue = $_product->get_attribute( 'horas_uso_servicio_despegue' );
            $ruta = $_product->get_attribute( 'ruta' );
            $ciudad_origen = $_product->get_attribute( 'ciudad_origen' );
            $ciudad_destino = $_product->get_attribute( 'ciudad_destino' );
            $ruta_completa = $_product->get_attribute( 'ruta_completa' );
            $nombre_third_party = $_product->get_attribute( 'nombre_third_party' );
            $incluye_hotel = $_product->get_attribute( 'incluye_hotel' );
            $nombre_hotel = $_product->get_attribute( 'nombre_hotel' );
            $nombre_aerolinea = $_product->get_attribute( 'nombre_aerolinea' );
            $nombre_servicio_terrestre = $_product->get_attribute( 'nombre_servicio_terrestre' );

            $attributes[] = array(
                'third_party' => $third_party,
                'servicio_terrestre' => $servicio_terrestre,
                'servicio_aereo' => $servicio_aereo,
                'horas_despegue' => $horas_despegue,
                'horas_uso_servicio_despegue' => $horas_uso_servicio_despegue,
                'ruta' => $ruta,
                'ciudad_origen' => $ciudad_origen,
                'ciudad_destino' => $ciudad_destino,
                'ruta_completa' => $ruta_completa,
                'nombre_third_party' => $nombre_third_party,
                'incluye_hotel' => $incluye_hotel,
                'nombre_hotel' => $nombre_hotel,
                'nombre_aerolinea' => $nombre_aerolinea,
                'nombre_servicio_terrestre' => $nombre_servicio_terrestre
            );
        } 
        return $attributes;
    }

    private function get_regimen_fiscal($order_id)
    {
        return get_post_meta($order_id, '_regimen_fiscal', true);
    }

    /**
     * Call a API to get a transaction.
     */
    public function get_transaction($transaction_token_id)
    {
        $jwt = $this->jwt();

        if ($jwt === false) {
            return false;
        }

        try {
            return \NetPay\Api\Transaction::get($jwt, $transaction_token_id, $this->store_customer);
        } catch (Exception $e) {
            $this->thank_you($e->getMessage(), 'error');

            return false;
        }
    }

    /**
     * Call a API to submit the cancelled of a transaction.
     */
    private function post_cancelled($order_id)
    {
        $jwt = $this->jwt();

        if ($jwt === false) {
            return false;
        }

        try {
            $transaction_token_id = get_post_meta($order_id, '_transaction_token_id', true);

            $data = array(
                'transaction_token_id' => $transaction_token_id
            );

            return \NetPay\Api\Cancelled::post($jwt, $data);
        } catch (Exception $e) {
            $description = $e->getMessage();

            $order = new WC_Order($order_id);
            $order->add_order_note($description);

            $this->rollback_order_status($order_id);

            return false;
        }
    }

    /**
     * Display a error in the page.
     */
    private function display_error($description)
    {
        global $woocommerce;
        global $wp_version;

        if (version_compare($wp_version, '4.1', '>=')) {
            wc_add_notice(__('Error: ', 'woothemes') . $description, 'error');
        } else {
            $woocommerce->add_error(__('Error: ', 'woothemes') . $description);
        }
    }
}

/**
 * Add the Gateway to WooCommerce
 */
function woocommerce_netpay_card_add_gateway($methods)
{
    array_push($methods, 'WC_NetPay_Card_Gateway');

    return $methods;
}

add_filter('woocommerce_payment_gateways', 'woocommerce_netpay_card_add_gateway');

/**
 * Campo para regimen fiscal
 */
add_action('woocommerce_after_checkout_billing_form', 'customise_checkout_field');
function customise_checkout_field($checkout)
{
    $WC = new WC_NetPay_Card_Gateway();

    //$WC->promotion_select();

    if($WC->mid == 'donaciones' || $WC->mid == 'agencias')
    {
        echo '<div id="customise_checkout_field">';
        woocommerce_form_field('regimen_fiscal', array(
            'type' => 'select',
            'class' => array(
                'my-field-class form-row-wide'
            ) ,
            'options'  => array(
                'Persona fisica' => $WC->lang_options['client_form_fields']['regimen_fical']['option1'],
                'Persona fisica con actividad empresarial' => $WC->lang_options['client_form_fields']['regimen_fical']['option2'],
                'Persona moral' => $WC->lang_options['client_form_fields']['regimen_fical']['option3'],
            ),
            'label' => __($WC->lang_options['client_form_fields']['regimen_fical']['label']) ,
            'placeholder' => __($WC->lang_options['client_form_fields']['regimen_fical']['placeholder']) ,
            'required' => true,
        ) , $checkout->get_value('regimen_fiscal'));
        echo '</div>';
    }
    
    if($WC->mid == 'agencias')
    {
        echo "<fieldset><legend> " . $WC->lang_options['client_form_fields']['legend']['label'] . " : </legend>";
        echo '<div id="customise_checkout_field">';

        woocommerce_form_field('one_way', array(
            'type' => 'select',
            'class' => array(
                'my-field-class form-row-wide'
            ) ,
            'options'  => array(
                'Y' => $WC->lang_options['client_form_fields']['type_travel']['option1'],
                'N' => $WC->lang_options['client_form_fields']['type_travel']['option2'],
            ),
            'label' => __($WC->lang_options['client_form_fields']['type_travel']['label']),
            'required' => true,
        ) , $checkout->get_value('one_way'));

        
        _e( "<b>".$WC->lang_options['client_form_fields']['depart']['label'].": </b>", "add_extra_fields");
	    ?>
	    <br>
	    <input type="text" name="ida" class="ida" required="required">
	    <script>
	        jQuery(document).ready(function( $ ) {
	            $( ".ida").datepicker( {
	        	    minDate: 0,	
                    dateFormat: 'yy-mm-dd'
	            } );
	        } );
        </script>
        <br>
        <?php
        _e( "<b>".$WC->lang_options['client_form_fields']['return']['label'].": </b>", "add_extra_fields");
        ?>
        <br>
        <input type="text" name="vuelta" class="vuelta">
        <script>
            jQuery(document).ready(function( $ ) {
                $( ".vuelta").datepicker( {
                    minDate: 0,
                    dateFormat: 'yy-mm-dd'
                } );
            } );
        </script>
        <br>
        <?php 
    
        woocommerce_form_field('passegers_number', array(
            'type' => 'select',
            'class' => array(
                'my-field-class form-row-wide'
            ) ,
            'options'  => array(
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
            ),
            'label' => __($WC->lang_options['client_form_fields']['passegers_number']['label']),
            'required' => true,
        ) , $checkout->get_value('passegers_number'));

        woocommerce_form_field('frequency_number', array(
            'type' => 'text',
            'class' => array(
                'my-field-class form-row-wide'
            ) ,
          
            'label' => __($WC->lang_options['client_form_fields']['frequency_number']['label']),
            'required' => false,
        ) , $checkout->get_value('frequency_number'));

        woocommerce_form_field('name_passenger1', array(
            'type' => 'text',
            'class' => array(
                'my-field-class form-row-wide'
            ) ,
          
            'label' => __($WC->lang_options['client_form_fields']['name_passenger1']['label']),
            'required' => true,
        ) , $checkout->get_value('name_passenger1'));
        woocommerce_form_field('phone_passenger1', array(
            'type' => 'text',
            'class' => array(
                'my-field-class form-row-wide'
            ) ,
          
            'label' => __($WC->lang_options['client_form_fields']['phone_passenger1']['label']),
            'required' => true,
        ) , $checkout->get_value('phone_passenger1'));

        woocommerce_form_field('name_passenger2', array(
            'type' => 'text',
            'class' => array(
                'my-field-class form-row-wide'
            ) ,
          
            'label' => __($WC->lang_options['client_form_fields']['name_passenger2']['label']),
            'required' => false,
        ) , $checkout->get_value('name_passenger2'));
        woocommerce_form_field('phone_passenger2', array(
            'type' => 'text',
            'class' => array(
                'my-field-class form-row-wide'
            ) ,
          
            'label' => __($WC->lang_options['client_form_fields']['phone_passenger2']['label']),
            'required' => false,
        ) , $checkout->get_value('phone_passenger2'));

        woocommerce_form_field('name_passenger3', array(
            'type' => 'text',
            'class' => array(
                'my-field-class form-row-wide'
            ) ,
          
            'label' => __($WC->lang_options['client_form_fields']['name_passenger3']['label']),
            'required' => false,
        ) , $checkout->get_value('name_passenger3'));
        woocommerce_form_field('phone_passenger3', array(
            'type' => 'text',
            'class' => array(
                'my-field-class form-row-wide'
            ) ,
          
            'label' => __($WC->lang_options['client_form_fields']['phone_passenger3']['label']),
            'required' => false,
        ) , $checkout->get_value('phone_passenger3'));

        woocommerce_form_field('name_passenger4', array(
            'type' => 'text',
            'class' => array(
                'my-field-class form-row-wide'
            ) ,
          
            'label' => __($WC->lang_options['client_form_fields']['name_passenger4']['label']),
            'required' => false,
        ) , $checkout->get_value('name_passenger4'));
        woocommerce_form_field('phone_passenger4', array(
            'type' => 'text',
            'class' => array(
                'my-field-class form-row-wide'
            ) ,
          
            'label' => __($WC->lang_options['client_form_fields']['phone_passenger4']['label']),
            'required' => false,
        ) , $checkout->get_value('phone_passenger4'));
        echo '</div>';
        echo '</fieldset>';
    }
}

add_action('woocommerce_checkout_update_order_meta', 'customise_checkout_field_update_order_meta');
function customise_checkout_field_update_order_meta($order_id)
{
    $order = new WC_Order($order_id);

    $regimen_fiscal = sanitize_text_field($_POST['regimen_fiscal']);
    if (!empty($regimen_fiscal)) {
        update_post_meta($order_id, '_regimen_fiscal', $regimen_fiscal );

        $order->add_order_note("regimen_fiscal: " . $regimen_fiscal);
    }

    $one_way = sanitize_text_field($_POST['one_way']);
    if (!empty($one_way)) {
        update_post_meta($order_id, '_one_way', $one_way);

        $order->add_order_note("one_way: " . $one_way);
    }

    $passegers_number = sanitize_text_field($_POST['passegers_number']);
    if (!empty($passegers_number)) {
        update_post_meta($order_id, '_passegers_number', $passegers_number);

        $order->add_order_note("passegers_number: " . $passegers_number);
    }

    $frequency_number = sanitize_text_field($_POST['frequency_number']);
    if (!empty($frequency_number)) {
        update_post_meta($order_id, '_frequency_number', $frequency_number);

        $order->add_order_note("frequency_number: " . $frequency_number);
    }

    $name_passenger1 = sanitize_text_field($_POST['name_passenger1']);
    if (!empty($name_passenger1)) {
        update_post_meta($order_id, '_name_passenger1', $name_passenger1);

        $order->add_order_note("name_passenger1: " . $name_passenger1);
    }

    $phone_passenger1 = sanitize_text_field($_POST['phone_passenger1']);
    if (!empty($phone_passenger1)) {
        update_post_meta($order_id, '_phone_passenger1', $phone_passenger1);

        $order->add_order_note("phone_passenger1: " . $phone_passenger1);
    }

    $name_passenger2 = sanitize_text_field($_POST['name_passenger2']);
    if (!empty($name_passenger2 )) {
        update_post_meta($order_id, '_name_passenger2', $name_passenger2);

        $order->add_order_note("name_passenger2: " . $name_passenger2);
    }

    $phone_passenger2 = sanitize_text_field($_POST['phone_passenger2']);
    if (!empty($phone_passenger2)) {
        update_post_meta($order_id, '_phone_passenger2', $phone_passenger2);

        $order->add_order_note("phone_passenger2: " . $phone_passenger2);
    }

    $name_passenger3 = sanitize_text_field($_POST['name_passenger3']);
    if (!empty($name_passenger3)) {
        update_post_meta($order_id, '_name_passenger3', $name_passenger3);

        $order->add_order_note("name_passenger3: " . $name_passenger3);
    }

    $phone_passenger3 = sanitize_text_field($_POST['phone_passenger3']);
    if (!empty($phone_passenger3)) {
        update_post_meta($order_id, '_phone_passenger3', $phone_passenger3);

        $order->add_order_note("phone_passenger3: " . $phone_passenger3);
    }

    $name_passenger4 = sanitize_text_field($_POST['name_passenger4']);
    if (!empty($name_passenger4)) {
        update_post_meta($order_id, '_name_passenger4', $name_passenger4);

        $order->add_order_note("name_passenger4: " . $name_passenger4);
    }

    $phone_passenger4 = sanitize_text_field($_POST['phone_passenger4']);
    if (!empty($phone_passenger4)) {
        update_post_meta($order_id, '_phone_passenger4', $phone_passenger4);

        $order->add_order_note("phone_passenger4: " . $phone_passenger4);
    }

    $ida = sanitize_text_field($_POST['ida']);
    if (!empty($ida)) {
        $ida = date("Y-m-d", strtotime($ida));
        update_post_meta($order_id, '_ida', $ida);

        $order->add_order_note("ida: " . $ida);
    }
}

add_action( 'wp_enqueue_scripts', 'enqueue_datepicker' );
function enqueue_datepicker() {
    if ( is_checkout() ) {
        // Load the datepicker script (pre-registered in WordPress).
         wp_enqueue_script( 'jquery-ui-datepicker' );
         // You need styling for the datepicker. For simplicity I've linked to Google's hosted jQuery UI CSS.
         wp_register_style( 'jquery-ui', plugins_url( 'netpay-payment-gateway/assets/css/jquery-ui.css' ));
         wp_enqueue_style( 'jquery-ui' );  
    }  
}

function netpay_woocommerce_order_status_postauth($order_id) {
    $WC = new WC_NetPay_Card_Gateway();
    $transaction_token_id = get_post_meta($order_id, '_transaction_token_id', true);
    $order = new WC_Order($order_id);
    if(!empty($transaction_token_id))
    {
        $transaction = $WC->get_transaction($transaction_token_id);
        if($transaction['result']['transactionType'] == 'PreAuth' &&
            $transaction['result']['transaction']['status'] == 'PREAUTH')
        {
            $jwt = $WC->jwt();
            $charge = \NetPay\Api\Charge::post($jwt, $transaction_token_id, $order->get_total());
            if($charge['result']['response']['responseCode'] != '00')
            {
                $order->update_status('processing');
                $order->add_order_note($WC->lang_options['charge']['error']);
            } 
        }
        elseif($transaction['result']['transactionType'] == 'PostAuth')
        {
            $order->add_order_note($WC->lang_options['charge']['double_charge']);
        }
        elseif($transaction['result']['transactionType'] == 'Auth')
        {
            $order->update_status('processing');
            $order->add_order_note($WC->lang_options['charge']['type_error']);
        }
    }
}
add_action('woocommerce_order_status_postauth', 'netpay_woocommerce_order_status_postauth', 10, 1 );

// Register new custom order status
add_filter( 'init', 'netpay_register_post_statuses' );
function netpay_register_post_statuses() {
    register_post_status('wc-postauth ', array(
        'label' => __( 'PostAuth'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('PostAuth (%s)', 'PostAuth (%s)')
    ));
}


// Add new custom order status to list of WC Order statuses
function netpay_add_order_statuses($order_statuses) {
    $new_order_statuses = array();

    // add new order status before processing
    foreach ($order_statuses as $key => $status) {
        $new_order_statuses[$key] = $status;
        if ('wc-processing' === $key) {
            $new_order_statuses['wc-postauth'] = __('PostAuth' );
        }
    }
    return $new_order_statuses;
}


// Adding new custom status to admin order list bulk dropdown

function netpay_custom_dropdown_bulk_actions_shop_order( $actions ) {
    $WC = new WC_NetPay_Card_Gateway();
    $new_actions = array();

    // add new order status before processing
    foreach ($actions as $key => $action) {
        if ('mark_processing' === $key)
            $new_actions['mark_postauth'] = __( $WC->lang_options['change_status']['to_postauth']);

        $new_actions[$key] = $action;
    }
    return $new_actions;
}