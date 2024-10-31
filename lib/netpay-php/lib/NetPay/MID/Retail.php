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

namespace NetPay\MID;

class Retail
{
    public static function get_mdds($input) {
        return [
            [
                "id" => 93,
                "value" => $input['phoneNumber'],
            ],
            [
                "id" => 2,
                "value" => "Web",
            ],
            [
                "id" => 6,
                "value" => $input['first_order_days'],
            ],
            [
                "id" => 7,
                "value" => $input['last_order_days'],
            ],
            [
                "id" => 20,
                "value" => $input['category'],
            ],
            [
                "id" => 13,
                "value" => "N",
            ],
            [
                "id" => 48,
                "value" => $input['days_until_register'],
            ],
            [
                "id" => 40,
                "value" => $input['completed_orders'],
            ],
            [
                "id" => 49,
                "value" => $input['method_of_delivery'],
            ],
            [
                "id" => 21,
                "value" => "No",
            ],
            [
                "id" => 22,
                "value" => "R",
            ],
            [
                "id" => 25,
                "value" => $input['store_customer'],
            ],
            [
                "id" => 28,
                "value" => $input['store_customer'],
            ],
            [
                "id" => 38,
                "value" => $input['days_until_register'],
            ],
            [
                "id" => 10,
                "value" => "3DS",
            ],
            [
                "id" => 50,
                "value" => "Si",
            ],
            [
                "id" => 17,
                "value" => $input['store_customer'],
            ],
            [
                "id" => 9,
                "value" => "Retail",
            ],
            [
                "id" => 16,
                "value" => $input['store_customer'],
            ],
            [
                "id" => 0,
                "value" => 'dummy',
            ],
        ];
    }
}
?>