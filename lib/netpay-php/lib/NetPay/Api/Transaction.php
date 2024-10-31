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

use Exception;
use \NetPay\Config;
use \NetPay\Api\Curl;
use \NetPay\Exceptions\HandlerHTTP;
class Transaction
{
    /**
     * Send a get request to Curl to check the transaction of an order.
     */
    public static function get($jwt, $transaction_token_id, $store_customer)
    {
        $url = self::format_url($transaction_token_id, $store_customer);

        $curl_result = Curl::get($url, $jwt);

        $result = json_decode($curl_result['result'], true);

        if ($curl_result['code'] != 200) {
            throw HandlerHTTP::errorHandler($result, $curl_result['code']);
        }

        return compact('result');
    }

    /**
     * Format the transaction url.
     */
    private function format_url($transaction_token_id, $store_customer)
    {
        return sprintf(Config::TRANSACTION_URL, $transaction_token_id, $store_customer);
    }
}