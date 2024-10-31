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
    #np-payment-iframe {
        border: 0;
        height: 510px;
        width: 100%;
    }
    .site-content{
        overflow: scroll !important;
    }
</style>

<?php if($result === false) { ?>
    <?php
        wc_print_notices();
    ?>
<?php } else { ?>
    <p><?php echo $result['receipt_page_title']; ?></p>

    <form action="<?php echo $result['web_authorizer_url'] ?>" method="post" id="np-payment-form" target="np-payment-iframe">
        <input type="hidden" name="jwt" id="np-payment-jwt" value="<?php echo $result['jwt'] ?>">
    </form>

    <iframe name="np-payment-iframe" id="np-payment-iframe" src=""></iframe>

    <script>
        jQuery(document).ready(function ($) {
            $('#np-payment-form').submit();
       });
        //function showWebAuthorizer(){
			//document.getElementById("np-payment-form").submit();
		//}
		//window.onload = showWebAuthorizer;
    </script>
<?php } ?>
