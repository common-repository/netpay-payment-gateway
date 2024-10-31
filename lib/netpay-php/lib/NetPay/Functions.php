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

namespace NetPay;

use \NetPay\Config;

class Functions
{
    /**
     * Return the lang options that need the plugin base in the locale.
     */
    public static function get_lang_options()
    {
        if (function_exists("get_locale") && get_locale() !== "") {
            $current_lang = explode("_", get_locale());
            $lang = $current_lang[0];
            $filename = self::get_plugin_directory()."/lang/" . $lang . ".php";

            if (!file_exists($filename)) {
                $filename = self::get_plugin_directory()."/lang/en.php";
            }

            return require($filename);
        }

        return array();
    }

    /**
     * Get the path of the plugin.
     */
    public static function get_plugin_directory()
    {
        $explode_string = explode("/",plugin_basename(__FILE__));

        return WP_PLUGIN_DIR.'/'.$explode_string[0];
    }

    /**
     * Encode a url to base64.
     */
    public static function base64url_encode($data)
    {
        return rtrim(base64_encode($data), '=');
    }

    /**
     * Get the days that has the user since they registration.
     */
    public static function days_until_register()
    {
        $now = time();

        $current_user = wp_get_current_user();
        $user_data = get_userdata($current_user->ID);
        $registered_date = strtotime($user_data->user_registered);

        $date_diff = $now - $registered_date;

        return round($date_diff / (60 * 60 * 24));
    }

    /**
     * Prepares the promotion string for being send to the checkout.
     */
    public static function promotion_format($order, $promotion_active)
    {
        $promotion = "000000";

        if ($promotion_active != 'yes') {
            return $promotion;
        }

        $months = get_post_meta($order->id, '_promotion', true);

        if ($months != '00') {
            $promotion = sprintf("00%s03", $months);
        }

        return $promotion;
    }

    /**
     * Get the month of the promotion string.
     */
    public static function promotion_month($promotion_string)
    {
        if ($promotion_string != '000000') {
            return intval(substr($promotion_string, -4, 2));
        }

        return 0;
    }

    /**
     * Generate the available months without interest options.
     */
    public static function promotion_options($settings)
    {
        $lang_options = self::get_lang_options();

        $promotions = array();
        $promotions_default = array(
            '03' => $lang_options['form_fields']['promotion']['months_without_interest_3'],
            '06' => $lang_options['form_fields']['promotion']['months_without_interest_6'],
            '09' => $lang_options['form_fields']['promotion']['months_without_interest_9'],
            '12' => $lang_options['form_fields']['promotion']['months_without_interest_12'],
            '18' => $lang_options['form_fields']['promotion']['months_without_interest_18'],
        );

        foreach ($promotions_default as $key => $value) {
            if ($settings["promotion_{$key}"] == 'yes') {
                $promotions[$key] = $value;
            }
        }

        if (empty($promotions)) {
            return array();
        }

        return array_replace(
            array('00' => $lang_options['form_fields']['promotion']['months_without_interest_0']),
            $promotions
        );
    }

    /**
     * Return the card type name base in a card type code.
     */
    public static function card_type_name($type)
    {
        $card_types = Config::CARD_TYPES;

        if (isset($card_types[$type])) {
           return $card_types[$type];
        }

        return '';
    }

    /**
     * Add the transaction info to an order note.
     */
    public static function transaction_note($order, $transaction = array(), $note = "")
    {
        if (empty($transaction)) {
            return;
        }

        $card_type_name = self::card_type_name($transaction['cardType']);

        $promotion = self::promotion_month($transaction['promotion']);

        $note = sprintf($note, $order->id, $transaction['orderId'], $transaction['spanRouteNumber'], $transaction['bankName'], $card_type_name, $promotion);

        $order->add_order_note($note);
    }

    /**
     * Rollback the status of a order.
     */
    public static function rollback_order_status($order)
    {
        $old_status = get_post_meta($order->id, '_old_status', true);
        $order->update_status($old_status);
    }

    /**
     * Return the http error message base in a code and the lang.
     */
    public static function http_code_message($code)
    {
        $lang_options = self::get_lang_options();

        $http_codes = $lang_options['http_codes'];

        $message = $lang_options['http_error'];

        if (isset($http_codes[$code])) {
            $message = $http_codes[$code];
        }

        return $message;
    }

    /**
     * Return the bank error message base in a code and the lang.
     */
    public static function bank_code_message($code)
    {
        $lang_options = self::get_lang_options();

        $bank_codes = $lang_options['bank_codes'];

        $message = $lang_options['bank_error'];

        if (isset($bank_codes[$code])) {
            $message = $bank_codes[$code];
        }

        return $message;
    }

    public static function get_count_orders_completed() {
        $customer_orders = get_posts( array(
            'numberposts' => - 1,
            'meta_key'    => '_customer_user',
            'meta_value'  => get_current_user_id(),
            'post_type'   => array( 'shop_order' ),
            'post_status' => array( 'wc-completed' )
        ) );
    
        return count($customer_orders);
    }

    public static function get_total_sum() {
        $customer_orders = get_posts( array(
            'numberposts' => - 1,
            'meta_key'    => '_customer_user',
            'meta_value'  => get_current_user_id(),
            'post_type'   => array( 'shop_order' ),
            'post_status' => array( 'wc-completed' )
        ) );
    
        $total = 0;
        foreach ( $customer_orders as $customer_order ) {
            $order = wc_get_order( $customer_order );
            $total += $order->get_total();
        }
    
        return number_format($total, 2, '.', '') ;
    }

    public static function get_avg() {
        $count= self::get_count_orders_completed();
        $sum_amount = self::get_total_sum();
        $promedio = 0;
        if($count > 0) {
            $promedio = number_format($sum_amount / $count, 2, '.', '');
        }
        return $promedio ;
    }

    public static function date_joined() {
        $udata = get_userdata( get_current_user_id());
        $registered = $udata->user_registered;
        return date( "d/m/Y", strtotime( $registered ) );
    }

    public static function get_sku($itemList){
        return $itemList[0]['productSKU'];
    } 

    public static function get_campaign($itemList){
        return $itemList[0]['productName'];
    } 

    public static function get_current_cliend_id()
    {
        return get_current_user_id();
    }

    public static function days_to_depart($ida)
    {
        $now = time();
        $date_diff = strtotime($ida) - $now;
        $days = (round($date_diff / (60 * 60 * 24))) + 1;
        return $days < 0 ? 0 : $days;
    }

    public static function get_full_description($itemList){
        return $itemList[0]['full_description'];
    }

    public static function get_short_description($itemList){
        return $itemList[0]['short_description'];
    }

    public static function replace_caracters($input) {
        $replaced = strtr(utf8_decode($input), utf8_decode('ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ'), 'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy');
        $replaced  = str_replace("#", " ", $replaced);
        return preg_replace('/[^A-Za-z0-9@.-_ \-]/', '', $replaced);
    }

}