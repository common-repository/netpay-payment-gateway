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

class Restaurant
{
    public static function get_mdds($input) {
        return [
            [
                "id" => 2,
                "value" => 'Web',
            ],
            [
                "id" => 20,
                "value" => $input['category']
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
                "id" => 23,
                "value" => $input['bill']['firstName'].' '.$input['bill']['lastName'],
            ],
            [
                "id" => 24,
                "value" => \NetPay\Functions::replace_caracters($input['site']),
            ],
            [
                "id" => 25,
                "value" => $input['store_customer'],
            ],
            [
                "id" => 26,
                "value" => $input['store_city'],
            ],
            [
                "id" => 27,
                "value" => $input['store_postcode'],
            ],
            [
                "id" => 28,
                "value" => $input['store_customer'],
            ],
            [
                "id" => 29,
                "value" => $input['store_primary_type_food'],
            ],
            [
                "id" => 30,
                "value" => $input['store_secundary_type_food'],
            ],
            [
                "id" => 31,
                "value" => $input["method_of_delivery"],
            ],
            [
                "id" => 32,
                "value" => $input['days_until_register'],
            ],
            [
                "id" => 33,
                "value" => $input['count_orders_completed'],
            ],
            [
                "id" => 34,
                "value" => "0",
            ],
            [
                "id" => 35,
                "value" => $input['avg_value'],
            ],
            [
                "id" => 36,
                "value" => "Regular",
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