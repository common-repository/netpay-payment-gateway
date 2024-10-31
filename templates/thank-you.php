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

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>

<style>
    .woocommerce-error, .woocommerce-info, .woocommerce-message {
        margin-bottom: 1.5em;
        padding: 2em;
        background: #eee;
        font-size: 16px;
        font-family: "Libre Franklin", "Helvetica Neue", helvetica, arial, sans-serif;
    }

    .woocommerce-message {
        background:teal;
        color:#fff
    }

    .woocommerce-error {
        background: #b22222;
        color: #fff;
    }

    .woocommerce-info {
        background:#4169e1;
        color:#fff
    }

    .woocommerce-error a,.woocommerce-info a,.woocommerce-message a {
        color:#fff;
        box-shadow:0 1px 0 #fff!important;
        -webkit-transition:box-shadow ease-in-out 130ms;
        transition:box-shadow ease-in-out 130ms
    }

    .woocommerce-error a:hover,.woocommerce-info a:hover,.woocommerce-message a:hover {
        color:#fff!important;
        box-shadow:0 3px 0 #fff!important
    }
</style>

<div class="woocommerce">
    <div class="woocommerce-<?php echo $type; ?>" role="alert">
        <?php echo $message == '' ? $this->lang_options['thank_you_default'] : $message; ?>
    </div>
</div>