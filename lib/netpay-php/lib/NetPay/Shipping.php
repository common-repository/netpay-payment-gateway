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

class Shipping {
    /**
     * Prepares the shipping information of a order for being send to the checkout.
     */
    public static function format($order)
    {
        $shipping_method = 'flatrate_flatrate';
        $shipping_method_title = $order->get_shipping_method();

        if (strpos($shipping_method_title, 'Free shipping') !== false) {
            $shipping_method = 'free_shipping';
        }

        if (strpos($shipping_method_title, 'Local pickup') !== false) {
            $shipping_method = 'local_pickup';
        }

        if (strpos($shipping_method_title, 'Flat rate') !== false) {
            $shipping_method = 'flatrate_flatrate';
        }

        if (version_compare(WOOCOMMERCE_VERSION, '3.0.0', '<')) {
            if($order->shipping_country != '')
            {
                $phoneNumber = str_replace("+52", "", $order->billing_phone);
            }
            else
            {
                $shipping_method = '';
                $phoneNumber = '';
            }
            
            return [
                "city" => \NetPay\Functions::replace_caracters($order->shipping_city),
                "country" => $order->shipping_country,
                "firstName" => \NetPay\Functions::replace_caracters($order->shipping_first_name),
                "lastName" => \NetPay\Functions::replace_caracters($order->shipping_last_name),
                "phoneNumber" => $phoneNumber,
                "postalCode" => $order->shipping_postcode,
                "state" => \NetPay\Functions::replace_caracters($order->shipping_state),
                "street1" => \NetPay\Functions::replace_caracters($order->shipping_address_1),
                "street2" => \NetPay\Functions::replace_caracters($order->shipping_address_2),
                "shippingMethod" => $shipping_method,
            ];
        }

        $order_data = $order->get_data();

        if($order_data['shipping']['country'] != '')
        {
            $phoneNumber = str_replace("+52", "", $order_data['billing']['phone']);
        }
        else
        {
            $shipping_method = '';
            $phoneNumber = '';
        }

        return [
            "city" => \NetPay\Functions::replace_caracters($order_data['shipping']['city']),
            "country" => $order_data['shipping']['country'],
            "firstName" => \NetPay\Functions::replace_caracters($order_data['shipping']['first_name']),
            "lastName" => \NetPay\Functions::replace_caracters($order_data['shipping']['last_name']),
            "phoneNumber" => $phoneNumber,
            "postalCode" => $order_data['shipping']['postcode'],
            "state" => \NetPay\Functions::replace_caracters($order_data['shipping']['state']),
            "street1" => \NetPay\Functions::replace_caracters($order_data['shipping']['address_1']),
            "street2" => \NetPay\Functions::replace_caracters($order_data['shipping']['address_2']),
            "shippingMethod" => $shipping_method,
        ];
    }
}