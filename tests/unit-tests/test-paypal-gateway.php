<?php

class Test_Charitable_Paypal_Gateway extends Charitable_UnitTestCase {

    /**
     * @dataProvider testmode_redirect_urls
     * @covers Charitable_Gateway_Paypal::get_redirect_url
     */
    public function test_get_testmode_redirect_url( $expected, $ssl_check, $ipn_check ) {
        $this->set_charitable_option( 'test_mode', 1 );

        $gateway = new Charitable_Gateway_Paypal();

        $this->assertEquals(
            $expected,
            $gateway->get_redirect_url( $ssl_check, $ipn_check )
        );
    }

    /**
     * @dataProvider livemode_redirect_urls
     * @covers Charitable_Gateway_Paypal::get_redirect_url
     */
    public function test_get_livemode_redirect_url( $expected, $ssl_check, $ipn_check ) {
        $this->set_charitable_option( 'test_mode', 0 );

        $gateway = new Charitable_Gateway_Paypal();

        $this->assertEquals(
            $expected,
            $gateway->get_redirect_url( $ssl_check, $ipn_check )
        );
    }

    /**
     * Redirect URL data provider.
     */
    public function testmode_redirect_urls() {
        return array(
            array( 'https://sandbox.paypal.com/cgi-bin/webscr', false, false ),
            array( 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr', false, true ),
        );
    }

    public function livemode_redirect_urls() {
        return array(
            array( 'https://www.paypal.com/cgi-bin/webscr', false, false ),
            array( 'https://ipnpb.paypal.com/cgi-bin/webscr', false, true ),
        );
    }
        
}
