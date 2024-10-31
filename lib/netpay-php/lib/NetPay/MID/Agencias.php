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

class Agencias
{
    public static function get_mdds($input) {
        return [
            [
                "id" => 2,
                "value" => 'Web',
            ],
            [
                "id" => 20,
                "value" => $input['category'],
            ],
            [
                "id" => 37,
                "value" => "No",
            ],
            [
                "id" => 23,
                "value" => $input['bill']['firstName'].' '.$input['bill']['lastName'],
            ],
            [
                "id" => 36,
                "value" => "Regular",
            ],
            [
                "id" => 35,
                "value" => $input['avg_value'],
            ],
            [
                "id" => 40,
                "value" => $input['count_orders_completed'],
            ],
            [
                "id" => 54,
                "value" => $input['third_party'],
            ],
            [
                "id" => 55,
                "value" => $input['servicio_terrestre'],
            ],
            [
                "id" => 56,
                "value" => $input['servicio_aereo'],
            ],
            [
                "id" => 57,
                "value" => $input['horas_despegue'],
            ],
            [
                "id" => 58,
                "value" => $input['horas_uso_servicio_despegue'],
            ],
            [
                "id" => 59,
                "value" => $input['ruta'],
            ],
            [
                "id" => 60,
                "value" => $input['ciudad_origen'],
            ],
            [
                "id" => 61,
                "value" => $input['ciudad_destino'],
            ],
            [
                "id" => 62,
                "value" => $input['ruta_completa'],
            ],
            [
                "id" => 64,
                "value" => $input['one_way'],
            ],
            [
                "id" => 65,
                "value" => $input['passegers_number'],
            ],
            [
                "id" => 66,
                "value" => $input['ida'],
            ],
            [
                "id" => 68,
                "value" => $input['name_passenger1'],
            ],
            [
                "id" => 69,
                "value" => $input['name_passenger2'],
            ],
            [
                "id" => 70,
                "value" => $input['name_passenger3'],
            ],
            [
                "id" => 71,
                "value" => $input['name_passenger4'],
            ],
            [
                "id" => 72,
                "value" => $input['phone_passenger1'],
            ],
            [
                "id" => 73,
                "value" => $input['phone_passenger2'],
            ],
            [
                "id" => 74,
                "value" => $input['phone_passenger3'],
            ],
            [
                "id" => 75,
                "value" => $input['phone_passenger4'],
            ],
            [
                "id" => 76,
                "value" => $input['nombre_third_party'],
            ],
            [
                "id" => 77,
                "value" => $input['incluye_hotel'],
            ],
            [
                "id" => 78,
                "value" => $input['nombre_hotel'],
            ],
            [
                "id" => 79,
                "value" => $input['nombre_aerolinea'],
            ],
            [
                "id" => 80,
                "value" => $input['nombre_servicio_terrestre'],
            ],
            [
                "id" => 81,
                "value" => $input['frequency_number'],
            ],
            [
                "id" => 82,
                "value" => $input['persona_fisica'],
            ],
            [
                "id" => 83,
                "value" => $input['cuenta_empresarial'],
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