<?php

class Test_Charitable_Donor extends Charitable_UnitTestCase {

    private $donors = array();
    private $donor_ids = array();

    public function setUp() {
        parent::setUp();

        add_filter( 'charitable_auto_login_after_registration', '__return_false' );

        $anita_fite      = $this->create_anita_fite();
        $slade_wilson    = $this->create_slade_wilson();
        $charles_mcnider = $this->create_charles_mcnider();

        $this->donors    = array_merge( $this->donors, $anita_fite['donor'], $slade_wilson['donor'], $charles_mcnider['donor'] );
        $this->donor_ids = array_merge( $this->donor_ids, $anita_fite['donor_id'], $slade_wilson['donor_id'], $charles_mcnider['donor_id'] );
    }

    /**
     * @dataProvider expectations
     * @covers Charitable_Donor::__get
     */
    public function test_donor_id( $i, $expected ) {
        if ( isset( $this->donor_ids[ $i ] ) ) {
            $this->assertEquals(
                $this->donor_ids[ $i ],
                $this->donors[ $i ]->donor_id
            );
        }
    }

    /**
     * @dataProvider expectations
     * @covers Charitable_Donor::get
     */
    public function test_get_with_mapped_key( $i, $expected ) {
        $this->assertEquals(
            $expected['profile_address'],
            $this->donors[ $i ]->get( 'address' )
        );
    }

    /**
     * @dataProvider expectations
     * @covers Charitable_Donor::get
     */
    public function test_get_with_meta_key( $i, $expected ) {
        $this->assertEquals(
            $expected['profile_address'],
            $this->donors[ $i ]->get( 'donor_address' )
        );
    }

    /**
     * @dataProvider expectations
     * @covers Charitable_Donor::get_user
     */
    public function test_get_user( $i, $expected ) {
        if ( $expected['user_id'] ) {
            $this->assertGreaterThan( 0, $this->donors[ $i ]->get_user()->ID );
        } else {
            $this->assertEquals( 0, $this->donors[ $i ]->get_user()->ID );
        }
    }

    /**
     * @dataProvider expectations
     * @covers Charitable_Donor::get_email
     */
    public function test_get_email( $i, $expected ) {
        $this->assertEquals(
            $expected['email'],
            $this->donors[ $i ]->get_email()
        );
    }

    /**
     * @dataProvider expectations
     * @covers Charitable_Donor::get_user
     */
    public function test_get_address( $i, $expected ) {
        $this->assertEquals(
            $expected['formatted_address'],
            $this->donors[ $i ]->get_address()
        );
    }

    /**
     * @dataProvider expectations
     * @covers Charitable_Donor::get_donor_meta
     */
    public function test_get_donor_meta_address( $i, $expected ) {
        $this->assertEquals(
            $expected['address'],
            $this->donors[ $i ]->get_donor_meta( 'address' )
        );
    }

    /**
     * @dataProvider expectations
     * @covers Charitable_Donor::get_donor_meta
     */
    public function test_get_donor_meta_address_2( $i, $expected ) {
        $this->assertEquals(
            $expected['address_2'],
            $this->donors[ $i ]->get_donor_meta( 'address_2' )
        );
    }

    /**
     * @dataProvider expectations
     * @covers Charitable_Donor::get_donor_meta
     */
    public function test_get_donor_meta_city( $i, $expected ) {
        $this->assertEquals(
            $expected['city'],
            $this->donors[ $i ]->get_donor_meta( 'city' )
        );
    }

    /**
     * @dataProvider expectations
     * @covers Charitable_Donor::get_donor_meta
     */
    public function test_get_donor_meta_country( $i, $expected ) {
        $this->assertEquals(
            $expected['country'],
            $this->donors[ $i ]->get_donor_meta( 'country' )
        );
    }

    /**
     * @dataProvider expectations
     * @covers Charitable_Donor::get_donor_meta
     */
    public function test_get_donor_meta_phone( $i, $expected ) {
        $this->assertEquals(
            $expected['phone'],
            $this->donors[ $i ]->get_donor_meta( 'phone' )
        );
    }

    /**
     * Set up Anita Fite data.
     *
     * @return array
     */
    private function create_anita_fite() {
        $campaign_id = Charitable_Campaign_Helper::create_campaign();
        $donation_id = Charitable_Donation_Helper::create_donation( array(
            'user_id'   => 0,
            'user'      => array(
                'email'      => 'anita.fite@dc.com',
                'first_name' => 'Anita',
                'last_name'  => 'Fite',
                'address'    => '133 Fite Street',
                'city'       => 'New York',
                'state'      => 'NY',
                'postcode'   => '12345',
                'phone'      => '133133133',
                'country'    => 'US',
            ),
            'campaigns' => array(
                array( 
                    'campaign_id' => $campaign_id,
                    'amount'      => 10,
                )
            )
        ) );

        return array(
            'donor_id' => array_fill( 0, 2, charitable_get_donation( $donation_id )->get_donor_id() ),
            'donor'    => array(
                new Charitable_Donor( charitable_get_donation( $donation_id )->get_donor_id() ),
                charitable_get_donation( $donation_id )->get_donor(),
            )
        );
    }

    /**
     * Set up Slade Wilson data.
     *
     * We use this to test a donor with multiple donations, who has used different
     * details on their most recent donation.
     *
     * @return array
     */
    private function create_slade_wilson() {
        $campaign_id   = Charitable_Campaign_Helper::create_campaign();
        $donation_id_1 = Charitable_Donation_Helper::create_donation( array(
            'user_id'   => 0,
            'date_gmt'  => '2017-01-01 00:00:00',
            'user'      => array(
                'email'      => 'slade.wilson@dc.com',
                'first_name' => 'Slade',
                'last_name'  => 'Wilson',
                'address'    => '299 Wilson Street',
                'city'       => 'Melbourne',
                'state'      => 'VIC',
                'postcode'   => '3000',
                'phone'      => '244244244',
                'country'    => 'AU',
            ),
            'campaigns' => array(
                array( 
                    'campaign_id' => $campaign_id,
                    'amount'      => 20,
                )
            ),            
        ) );

        $donor = charitable_get_donation( $donation_id_1 )->get_donor();

        // Second donation, different address
        $donation_id_2 = Charitable_Donation_Helper::create_donation( array(
            'user_id'   => 0,
            'donor_id'  => $donor->donor_id,
            'user'      => array(
                'email'      => 'slade.wilson@dc.com',
                'first_name' => 'Slade',
                'last_name'  => 'Wilson',
                'address'    => '377 Main Street',
                'city'       => 'Boston',
                'state'      => 'MA',
                'postcode'   => '55445',
                'phone'      => '87878787',
                'country'    => 'US',
            ),
            'campaigns' => array(
                array( 
                    'campaign_id' => $campaign_id,
                    'amount'      => 30,
                )
            )
        ) );        

        return array(
            'donor_id' => array_fill( 0, 3, $donor->donor_id ),
            'donor'    => array(
                new Charitable_Donor( $donor->donor_id ),
                new Charitable_Donor( $donor->donor_id, $donation_id_1 ),
                new Charitable_Donor( $donor->donor_id, $donation_id_2 ),
            ),
        );
    }

    /**
     * Set up Charles McNider data.
     *
     * We use this to test a donor who registered a profile and created donations. 
     *
     * @return Charitable_Donor
     */
    private function create_charles_mcnider() {
        $user_id = Charitable_Donor_Helper::create_donor( array(
            'display_name' => 'Charles McNider',
            'user_email'   => 'charles.mcnider@dc.com',
            'user_login'   => 'charles',
            'first_name'   => 'Charles',
            'last_name'    => 'McNider',
            'address'      => '77 McNider Lane',
            'address_2'    => '',
            'city'         => 'Toronto',
            'state'        => 'ON',
            'postcode'     => 'M6E 2S3',
            'phone'        => '99988877',
            'country'      => 'CA',
        ) );
        $campaign_id = Charitable_Campaign_Helper::create_campaign();
        $donation_id = Charitable_Donation_Helper::create_donation( array(
            'user_id'   => $user_id,
            'user'      => array(
                'email'      => 'charles.mcnider@dc.com',
                'first_name' => 'Charles',
                'last_name'  => 'McNider',
                'address'    => 'Unit 1',
                'address_2'  => '29 Apple Avenue',
                'city'       => 'Vancouver',
                'state'      => 'BC',
                'country'    => 'CA',
                'postcode'   => 'V5K 0A1',
                'phone'      => '99988877',
            ),
            'campaigns' => array(
                array( 
                    'campaign_id' => $campaign_id,
                    'amount'      => 10,
                )
            )
        ) );

        $donor_id = charitable_get_donation( $donation_id )->get_donor_id();

        return array(
            'donor_id' => array_fill( 0, 2, $donor_id ),
            'donor'    => array(
                new Charitable_Donor( $donor_id ),
                new Charitable_Donor( $donor_id, $donation_id ),
            ),
        );
    }

    /**
     * Expectations data provider.
     */
    public function expectations() {
        return array(
            array( 0, array(
                'user_id'           => 0,
                'email'             => 'anita.fite@dc.com',
                'first_name'        => 'Anita',
                'last_name'         => 'Fite',
                'address'           => '133 Fite Street',
                'address_2'         => '',
                'city'              => 'New York',
                'state'             => 'New York',
                'postcode'          => '12345',
                'country'           => 'US',
                'phone'             => '133133133',
                'formatted_address' => '',
                'profile_address'   => '',
            ) ),
            array( 1, array(
                'user_id'           => 0,
                'email'             => 'anita.fite@dc.com',
                'first_name'        => 'Anita',
                'last_name'         => 'Fite',
                'address'           => '133 Fite Street',
                'address_2'         => '',
                'city'              => 'New York',
                'state'             => 'New York',
                'postcode'          => '12345',
                'country'           => 'US',
                'phone'             => '133133133',
                'formatted_address' => 'Anita Fite<br/>133 Fite Street<br/>New York, NY 12345<br/>United States (US)',
                'profile_address'   => '',
            ) ),
            array( 2, array(
                'user_id'           => 0,
                'email'             => 'slade.wilson@dc.com',
                'first_name'        => 'Slade',
                'last_name'         => 'Wilson',
                'address'           => '377 Main Street',
                'address_2'         => '',
                'city'              => 'Boston',
                'state'             => 'Massachusetts',
                'postcode'          => '55445',
                'country'           => 'US',
                'phone'             => '87878787',
                'formatted_address' => '',
                'profile_address'   => '',
            ) ),
            array( 3, array(
                'user_id'           => 0,
                'email'             => 'slade.wilson@dc.com',
                'first_name'        => 'Slade',
                'last_name'         => 'Wilson',
                'address'           => '299 Wilson Street',
                'address_2'         => '',
                'city'              => 'Melbourne',
                'state'             => 'Victoria',
                'postcode'          => '3000',
                'country'           => 'AU',
                'phone'             => '244244244',
                'formatted_address' => 'Slade Wilson<br/>299 Wilson Street<br/>Melbourne Victoria 3000<br/>Australia',
                'profile_address'   => '',
            ) ),
            array( 4, array(
                'user_id'           => 0,
                'email'             => 'slade.wilson@dc.com',
                'first_name'        => 'Slade',
                'last_name'         => 'Wilson',
                'address'           => '377 Main Street',
                'address_2'         => '',
                'city'              => 'Boston',
                'state'             => 'Massachusetts',
                'postcode'          => '55445',
                'country'           => 'US',
                'phone'             => '87878787',
                'formatted_address' => 'Slade Wilson<br/>377 Main Street<br/>Boston, MA 55445<br/>United States (US)',
                'profile_address'   => '',
            ) ),
            array( 5, array(
                'user_id'           => 1,
                'email'             => 'charles.mcnider@dc.com',
                'first_name'        => 'Charles',
                'last_name'         => 'McNider',
                'address'           => '77 McNider Lane',
                'address_2'         => '',
                'city'              => 'Toronto',
                'state'             => 'Ontario',
                'postcode'          => 'M6E 2S3',
                'phone'             => '99988877',
                'country'           => 'CA',
                'phone'             => '99988877',
                'formatted_address' => 'Charles McNider<br/>77 McNider Lane<br/>Toronto Ontario M6E 2S3<br/>Canada',
                'profile_address'   => '77 McNider Lane',
            ) ),
            array( 6, array(
                'user_id'           => 1,
                'email'             => 'charles.mcnider@dc.com',
                'first_name'        => 'Charles',
                'last_name'         => 'McNider',
                'address'           => 'Unit 1',
                'address_2'         => '29 Apple Avenue',
                'city'              => 'Vancouver',
                'state'             => 'British Columbia',
                'postcode'          => 'V5K 0A1',
                'phone'             => '99988877',
                'country'           => 'CA',
                'phone'             => '99988877',
                'formatted_address' => 'Charles McNider<br/>Unit 1<br/>29 Apple Avenue<br/>Vancouver British Columbia V5K 0A1<br/>Canada',
                'profile_address'   => '77 McNider Lane',
            ) ),
        );
    }
}