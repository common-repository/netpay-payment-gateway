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

class Profesionales
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
                "id" => 17,
                "value" => $input['store_customer'],
            ],
            [
                "id" => 20,
                "value" => $input['category'],
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
                "id" => 37,
                "value" => "No",
            ],
            [
                "id" => 38,
                "value" => $input['days_until_register'],
            ],
            [
                "id" => 39,
                "value" => $input['completed_orders'],
            ],
            [
                "id" => 36,
                "value" => "Regular",
            ],
            [
                "id" => 40,
                "value" => $input['completed_orders'],
            ],
            [
                "id" => 41,
                "value" => $input['store_service_type'],
            ],
            [
                "id" => 42,
                "value" => $input['site'],
            ],
            [
                "id" => 43,
                "value" => $input['store_customer'],
            ],
            [
                "id" => 44,
                "value" => $input['store_city'],
            ],
            [
                "id" => 45,
                "value" => $input['store_postcode'],
            ],
            [
                "id" => 46,
                "value" => $input['store_customer'],
            ],
            [
                "id" => 10,
                "value" => "3DS",
            ],
            [
                "id" => 47,
                "value" => "Servicios profesionales",
            ],
            [
                "id" => 94,
                "value" => $input['client_id'],
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