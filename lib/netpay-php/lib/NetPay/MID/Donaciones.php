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

class Donaciones
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
                "id" => 37,
                "value" => "No",
            ],
            [
                "id" => 20,
                "value" => "Donaciones",
            ],
            [
                "id" => 23,
                "value" => $input['bill']['firstName'].' '.$input['bill']['lastName'],
            ],
            [
                "id" => 35,
                "value" => $input['avg_value'],
            ],
            [
                "id" => 40,
                "value" => $input['total_sum'],
            ],
            [
                "id" => 36,
                "value" => "Regular",
            ],
            [
                "id" => 39,
                "value" => $input['count_orders_completed'],
            ],
            [
                "id" => 41,
                "value" => "Donacion",
            ],
            [
                "id" => 95,
                "value" => $input['fecha_registro'],
            ],
            [
                "id" => 96,
                "value" => "No",
            ],
            [
                "id" => 97,
                "value" => $input['regimen_fiscal'],
            ],
            [
                "id" => 98,
                "value" => $input['sku'],
            ],
            [
                "id" => 99,
                "value" => $input['campaign'],
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