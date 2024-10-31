=== NetPay Payment Gateway ===
Contributors: NetPayMX
Tags: woocommerce, netpaymx, mexico, payment gateway, pagos con tarjeta, ecommerce, msi
Requires at least: 4.9.8
Tested up to: 5.5.3
Stable tag: 1.2.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

NetPay Payment Gateway for WooCommerce

Pagos con tarjeta de crédito y débito como Visa, MasterCard y American Express.


Ir a lib/netpay-php/lib/Config.php y configurar:

Producción:
const API_URL = "https://suite.netpay.com.mx/gateway-ecommerce";

Sandbox:
const API_URL = "https://ecommerce.netpay.com.mx/gateway-ecommerce";