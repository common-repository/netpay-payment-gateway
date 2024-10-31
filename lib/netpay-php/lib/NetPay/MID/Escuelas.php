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

class Escuelas
{
    public static function get_mdds($input) {
        return [
            [
                "id" => 2,
                "value" => 'Web',
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
                "value" => "No",
            ],
            [
                "id" => 48,
                "value" => $input['days_until_register'],
            ],
            [
                "id" => 84,
                "value" => $input['count_orders_completed'],
            ],
            [
                "id" => 93,
                "value" => $input['phoneNumber'],
            ],
            [
                "id" => 10,
                "value" => "3DS",
            ],
            [
                "id" => 17,
                "value" => $input['store_customer'],
            ],
            [
                "id" => 9,
                "value" => "Escuela",
            ],
            [
                "id" => 85,
                "value" => $input['site'],
            ],
            [
                "id" => 86,
                "value" => $input['store_level'],
            ],
            [
                "id" => 87,
                "value" => "Pagos de padres de familia",
            ],
            [
                "id" => 88,
                "value" => $input['sku'],
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