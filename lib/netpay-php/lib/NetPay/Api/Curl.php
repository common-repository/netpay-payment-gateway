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

namespace NetPay\Api;

use \NetPay\Config;

class Curl
{
    /**
     * Execute a post request with the necessary configuration using curl.
     */
    public static function post($url, $fields_string, $jwt = null)
    {
        //open connection
        $ch = curl_init();

        $http_header = array(
            'Content-Type: application/json',
            'Connection: Keep-Alive',
            'Cache-Control: no-cache',
            'Expect:',
        );

        if (isset($jwt)) {
            $http_header[] = 'Authorization: Bearer '.$jwt;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, strlen($fields_string));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, Config::CURLOPT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, Config::CURLOPT_TIMEOUT);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!is_null(Config::URL_PORT)) {
            curl_setopt($ch, CURLOPT_PORT, Config::URL_PORT);
        }

        //execute post
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //close connection
        curl_close($ch);

        return compact('code', 'result');
    }

    /**
     * Execute a get request with the necessary configuration using curl.
     */
    public static function get($url, $jwt = null)
    {
        //open connection
        $ch = curl_init();

        $http_header = array(
            'Content-Type: application/json',
            'Connection: Keep-Alive',
            'Cache-Control: no-cache',
            'Expect:',
        );

        if (isset($jwt)) {
            $http_header[] = 'Authorization: Bearer '.$jwt;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING , "gzip, deflate");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, Config::CURLOPT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, Config::CURLOPT_TIMEOUT);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!is_null(Config::URL_PORT)) {
            curl_setopt($ch, CURLOPT_PORT, Config::URL_PORT);
        }

        //execute post
        $result = curl_exec($ch);

        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        //close connection
        curl_close($ch);

        return compact('code', 'result');
    }
}