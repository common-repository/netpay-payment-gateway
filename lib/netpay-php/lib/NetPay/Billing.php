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

class Billing {
    /**
     * Prepares the billing information of a order for being send to the checkout.
     */
    public static function format($order)
    {
        if (version_compare(WOOCOMMERCE_VERSION, '3.0.0', '<')) {
            $billing_phone = str_replace("+52", "", $order->billing_phone);
            $country = $order->billing_country;
            if(empty($country)) {
                $country = 'MX';
            }
            return [
                "city" => \NetPay\Functions::replace_caracters($order->billing_city),
                "country" => $country,
                "firstName" => \NetPay\Functions::replace_caracters($order->billing_first_name),
                "lastName" => \NetPay\Functions::replace_caracters($order->billing_last_name),
                "email" => $order->billing_email,
                "phoneNumber" => $billing_phone,
                "postalCode" => $order->billing_postcode,
                "state" => \NetPay\Functions::replace_caracters($order->billing_state),
                "street1" => \NetPay\Functions::replace_caracters($order->billing_address_1),
                "street2" => \NetPay\Functions::replace_caracters($order->billing_address_2),
                "ipAddress" => $order->customer_ip_address,
            ];
        }

        $order_data = $order->get_data();
        $billing_phone = str_replace("+52", "", $order_data['billing']['phone']);
        $country = $order_data['billing']['country'];
        if(empty($country)) {
            $country = 'MX';
        }
        return [
            "city" => \NetPay\Functions::replace_caracters($order_data['billing']['city']),
            "country" => $country,
            "firstName" => \NetPay\Functions::replace_caracters($order_data['billing']['first_name']),
            "lastName" => \NetPay\Functions::replace_caracters($order_data['billing']['last_name']),
            "email" => $order_data['billing']['email'],
            "phoneNumber" => $billing_phone,
            "postalCode" => $order_data['billing']['postcode'],
            "state" => \NetPay\Functions::replace_caracters($order_data['billing']['state']),
            "street1" => \NetPay\Functions::replace_caracters($order_data['billing']['address_1']),
            "street2" => \NetPay\Functions::replace_caracters($order_data['billing']['address_2']),
            "ipAddress" => $order->get_customer_ip_address(),
        ];
    }
}