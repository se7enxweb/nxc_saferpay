<?php
/**
 * @package nxcSaferPay
 * @author  Serhey Dolgushev <serhey.dolgushev@nxc.no>
 * @date    21 Apr 2010
 **/

/**
 * Extension information shown on the administration's about page.
 */
class nxc_saferpayInfo
{
    public static function info()
    {
        return array(
            'Name'      => 'NXC SaferPay Payment Gateway',
            'Version'   => '1.0.0',
            'Author'    => 'Serhey Dolgushev / NXC International SA',
            'Copyright' => 'Copyright &copy; 2010 NXC Consulting; 2026 7x',
            'License'   => 'Not stated by the original authors (see README.md)',
            'Info_url'  => 'https://github.com/se7enxweb/nxc_saferpay'
        );
    }
}
?>
