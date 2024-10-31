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

class ItemList {
    /**
     * Prepares the item list information of a order for being send to the checkout.
     */
    public static function format($order)
    {
        $items = $order->get_items();
        $item_list = [];
        $i = 1;
        foreach ($items as $item_data) {
            $product = new \WC_Product($item_data['product_id']);

            $sku = str_replace('', '-', trim($item_data['name']));

            if ($product->get_sku()) {
                $sku = $product->get_sku();
            }

            $full_description = $product->get_description();
            $short_description = $product->get_short_description();

            //$unitPrice = $product->get_price();
            $unitPrice = $item_data['subtotal'] / $item_data['quantity'];

            if($unitPrice < 1) {
                $unitPrice = 1;
            }

            $item_list[] = [
                "id" => ($i) . $item_data['product_id'],
                "productSKU" => substr(preg_replace('/[^A-Za-z0-9 \-]/', '', $sku), 0, 49),
                "unitPrice" => $unitPrice,
                "productName" => substr(preg_replace('/[^A-Za-z0-9 \-]/', '', $item_data['name']), 0, 49),
                "quantity" => $item_data['qty'],
                "productCode" => "Terminal",
                "full_description" => substr(preg_replace('/[^A-Za-z0-9 \-]/', '', $full_description), 0, 49),
                "short_description" => substr(preg_replace('/[^A-Za-z0-9 \-]/', '', $short_description), 0, 49),
            ];
            $i++;
        }

        return $item_list;
    }
}