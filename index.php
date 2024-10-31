<?php
/**
 * Plugin Name: NetPay Payment Gateway
 * Plugin URI:  https://www.netpay.mx/
 * Description: Add high conversion NetPay payment iframes to your WordPress site in minutes.
 * Author: NetPay
 * Author URI:  https://developers.netpay.com.mx/
 * Version: 1.2.6
 * Requires at least: 4.9.8
 *
 * Text Domain: NetPay
 *
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

add_action('plugins_loaded', 'netpay_payment_gateway_checkout_init_gateway', 0);

/**
 * Function that is called once the plugin have been loaded.
 * Init all the actions to allow the use of NetPay Payment Gateway.
 */
function netpay_payment_gateway_checkout_init_gateway()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

    include_once('netpay_card_gateway.php');
}