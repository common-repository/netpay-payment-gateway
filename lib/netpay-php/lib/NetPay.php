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

if (!function_exists('curl_init')) {
    throw new Exception('NetPay needs the CURL PHP extension.');
}

if (!function_exists('json_decode')) {
    throw new Exception('NetPay needs the JSON PHP extension.');
}

if (!function_exists('get_called_class')) {
    throw new Exception('NetPay needs to be run on PHP >= 5.3.0.');
}

require_once dirname(__FILE__).'/Config.php';

require_once dirname(__FILE__).'/NetPay/Api/Checkout.php';
require_once dirname(__FILE__).'/NetPay/Api/Curl.php';
require_once dirname(__FILE__).'/NetPay/Api/Login.php';
require_once dirname(__FILE__).'/NetPay/Api/Cancelled.php';
require_once dirname(__FILE__).'/NetPay/Api/Transaction.php';
require_once dirname(__FILE__).'/NetPay/Api/Charge.php';

require_once dirname(__FILE__).'/NetPay/Exceptions/HandlerBank.php';
require_once dirname(__FILE__).'/NetPay/Exceptions/HandlerHTTP.php';

require_once dirname(__FILE__).'/NetPay/Handlers/CheckoutDataHandler.php';
require_once dirname(__FILE__).'/NetPay/Handlers/LoginDataHandler.php';
require_once dirname(__FILE__).'/NetPay/Handlers/CancelledDataHandler.php';
require_once dirname(__FILE__).'/NetPay/Handlers/ChargeDataHandler.php';

require_once dirname(__FILE__).'/NetPay/Billing.php';
require_once dirname(__FILE__).'/NetPay/Functions.php';
require_once dirname(__FILE__).'/NetPay/ItemList.php';
require_once dirname(__FILE__).'/NetPay/Order.php';
require_once dirname(__FILE__).'/NetPay/Shipping.php';

require_once dirname(__FILE__).'/NetPay/MID/Agencias.php';
require_once dirname(__FILE__).'/NetPay/MID/Donaciones.php';
require_once dirname(__FILE__).'/NetPay/MID/Escuelas.php';
require_once dirname(__FILE__).'/NetPay/MID/Generales.php';
require_once dirname(__FILE__).'/NetPay/MID/Profesionales.php';
require_once dirname(__FILE__).'/NetPay/MID/Retail.php';
require_once dirname(__FILE__).'/NetPay/MID/Tickets.php';
require_once dirname(__FILE__).'/NetPay/MID/Restaurant.php';
