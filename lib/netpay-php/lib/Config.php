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

class Config
{
    const CURLOPT_TIMEOUT = 40; //Timeout in seconds

    const API_URL = "https://suite.netpay.com.mx/gateway-ecommerce";

    const AUTH_LOGIN_URL = self::API_URL."/v1.1/auth/login";

    const CHECKOUT_URL = self::API_URL."/v2/checkout";

    const TRANSACTION_URL = self::API_URL."/v1/transaction-report/transaction/%s/%s";

    const CANCELLED_URL = self::API_URL."/v1/transaction/refund";

    const CHARGE_URL = self::API_URL."/v1/transaction/charge";

    const URL_PORT = null;

    const CARD_TYPES = array(
        '001' => 'Visa',
        '002' => 'MasterCard',
        '003' => 'American Express',
    );
}
