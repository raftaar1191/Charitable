<?php

class Test_Charitable_Privacy extends Charitable_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	/**
	 * @covers Charitable_Privacy::__construct
	 * @covers Charitable_Privacy::register_exporter
	 */
	public function test_is_exporter_registered() {
		if ( ! function_exists( 'wp_privacy_anonymize_data' ) ) {
			return $this->markTestSkipped( 'Privacy features not available in this version of WordPress.' );
		}

		$exporters = apply_filters( 'wp_privacy_personal_data_exporters', array() );	
		$names     = wp_list_pluck( $exporters, 'exporter_friendly_name' );

		$this->assertContains( 'Charitable Donor Data', $names );
	}

	/**
	 * @covers Charitable_Privacy::__construct
	 * @covers Charitable_Privacy::register_eraser
	 */
	public function test_is_eraser_registered() {
		if ( ! function_exists( 'wp_privacy_anonymize_data' ) ) {
			return $this->markTestSkipped( 'Privacy features not available in this version of WordPress.' );
		}

		$erasers = apply_filters( 'wp_privacy_personal_data_erasers', array() );	
		$names   = wp_list_pluck( $erasers, 'eraser_friendly_name' );

		$this->assertContains( 'Charitable Donor Data Eraser', $names );
	}

	/**
	 * @covers Charitable_Privacy::__construct
	 * @covers Charitable_Privacy::add_privacy_policy_content
	 */
	public function test_privacy_content_added() {
		if ( ! class_exists( 'WP_Privacy_Policy_Content' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/misc.php' );

			/* If the class is still missing, this is an earlier version of WP. */
			if ( ! class_exists( 'WP_Privacy_Policy_Content' ) ) {
				return $this->markTestSkipped( 'Privacy features not available in this version of WordPress.' );
			}
		}

		/**
		 * wp_add_privacy_policy_content requires both an admin request
		 * and for admin_init to be firing or have been fired, so we need
		 * to simulate that.
		 */
		set_current_screen( 'edit.php' );

		global $wp_current_filter;
		$wp_current_filter[] = 'admin_init';

		$privacy = new Charitable_Privacy;
		$privacy->add_privacy_policy_content();

		foreach ( WP_Privacy_Policy_Content::get_suggested_policy_text() as $policy ) {
			if ( 'Charitable' === $policy['plugin_name'] ) {
				return;
			}
		}

		$this->fail( 'Charitable privacy policy content not found.' );
	}

	/**
	 * @covers Charitable_Privacy::get_data_retention_period
	 */
	public function test_get_data_retention_period() {
		$privacy = new Charitable_Privacy();

		$this->set_charitable_option( 'minimum_data_retention_period', 3 );

		$this->assertEquals( 3, $privacy->get_data_retention_period() );
	}

	/**
	 * @covers Charitable_Privacy::get_user_donation_fields
	 */
	public function test_get_user_donation_fields() {
		$privacy = new Charitable_Privacy();

		$this->assertNotEmpty( $privacy->get_user_donation_fields() );
	}

	/**
	 * @covers Charitable_Privacy::is_donation_in_data_retention_period
	 */
	public function test_with_no_data_retention_period_donation_is_not_in_period() {
		$privacy = new Charitable_Privacy();

		$this->set_charitable_option( 'minimum_data_retention_period', 0 );

		$donation_id = Charitable_Donation_Helper::create_donation( array(
			'campaigns' => array(
				array(
					'campaign_id' => Charitable_Campaign_Helper::create_campaign(),
					'amount'      => 5,
				),
			),
		) );
		
		$this->assertFalse( $privacy->is_donation_in_data_retention_period( $donation_id ) );
	}

	/**
	 * @covers Charitable_Privacy::is_donation_in_data_retention_period
	 */
	public function test_with_endless_data_retention_period_donation_is_in_period() {
		$privacy = new Charitable_Privacy();

		$this->set_charitable_option( 'minimum_data_retention_period', 'endless' );

		$donation_id = Charitable_Donation_Helper::create_donation( array(
			'campaigns' => array(
				array(
					'campaign_id' => Charitable_Campaign_Helper::create_campaign(),
					'amount'      => 5,
				),
			),
		) );

		$this->assertTrue( $privacy->is_donation_in_data_retention_period( $donation_id ) );
	}

	/**
	 * @covers Charitable_Privacy::is_donation_in_data_retention_period
	 */
	public function test_new_donation_in_data_retention_period_with_one_year_period() {
		$privacy = new Charitable_Privacy();

		$this->set_charitable_option( 'minimum_data_retention_period', 1 );

		$donation_id = Charitable_Donation_Helper::create_donation( array(
			'campaigns' => array(
				array(
					'campaign_id' => Charitable_Campaign_Helper::create_campaign(),
					'amount'      => 5,
				),
			),
		) );
		
		$this->assertTrue( $privacy->is_donation_in_data_retention_period( $donation_id ) );
	}

	/**
	 * @covers Charitable_Privacy::is_donation_in_data_retention_period
	 */
	public function test_old_donation_in_data_retention_period_with_one_year_period() {
		$privacy = new Charitable_Privacy();

		$this->set_charitable_option( 'minimum_data_retention_period', 1 );

		$donation_id = Charitable_Donation_Helper::create_donation( array(
			'date_gmt'  => date( 'Y-m-d H:i:s', strtotime( '2 years ago' ) ),
			'campaigns' => array(
				array(
					'campaign_id' => Charitable_Campaign_Helper::create_campaign(),
					'amount'      => 5,
				),
			),
		) );
		
		$this->assertFalse( $privacy->is_donation_in_data_retention_period( $donation_id ) );
	}

	/**
	 * @covers Charitable_Privacy::get_erasable_fields_for_donation
	 * @depends test_get_user_donation_fields
	 */
	public function test_get_erasable_fields_for_donation_in_data_retention_period() {
		$privacy     = new Charitable_Privacy();
		$user_fields = $privacy->get_user_donation_fields();

		$this->set_charitable_option( 'minimum_data_retention_period', 1 );
		$this->set_charitable_option( 'data_retention_fields', array (
			'first_name',
			'last_name',
			'email',
			'phone',
		) );

		$donation_id = Charitable_Donation_Helper::create_donation( array(
			'campaigns' => array(
				array(
					'campaign_id' => Charitable_Campaign_Helper::create_campaign(),
					'amount'      => 5,
				),
			),
		) );

		$this->assertCount( count( $user_fields ) - 4, $privacy->get_erasable_fields_for_donation( $donation_id ) );
		$this->assertArrayNotHasKey( 'first_name', $privacy->get_erasable_fields_for_donation( $donation_id ), 'Assert that first_name is not erasable for donation in data retention period.' );
	}

	/**
	 * @covers Charitable_Privacy::get_erasable_fields_for_donation
	 * @depends test_get_user_donation_fields
	 */
	public function test_get_erasable_fields_for_donation_outside_data_retention_period() {
		$privacy     = new Charitable_Privacy();
		$user_fields = $privacy->get_user_donation_fields();

		$this->set_charitable_option( 'minimum_data_retention_period', 1 );
		$this->set_charitable_option( 'data_retention_fields', array (
			'first_name',
			'last_name',
			'email',
			'phone',
		) );

		$donation_id = Charitable_Donation_Helper::create_donation( array(
			'date_gmt'  => date( 'Y-m-d H:i:s', strtotime( '2 years ago' ) ),
			'campaigns' => array(
				array(
					'campaign_id' => Charitable_Campaign_Helper::create_campaign(),
					'amount'      => 5,
				),
			),
		) );

		$this->assertCount( count( $user_fields ), $privacy->get_erasable_fields_for_donation( $donation_id ) );
		$this->assertArrayHasKey( 'first_name', $privacy->get_erasable_fields_for_donation( $donation_id ), 'Assert that first_name is erasable for donation in data retention period.' );
	}

	/**
	 * @covers Charitable_Privacy::export_user_data
	 */
	public function test_no_export_data() {		
		$privacy  = new Charitable_Privacy();
		$exporter = $privacy->export_user_data( 'james@gotham.com' );

		$this->assertEmpty( $exporter['data'] );
	}

	/**
	 * @covers Charitable_Privacy::export_user_data
	 */
	public function test_export_only_registered_donor_data() {
		$user = Charitable_User::create_profile( array(
			'user_email' => 'james@gotham.com',
			'first_name' => 'James',
			'last_name'	 => 'Gordon', 
			'user_pass'  => 'password', // Required for the user to be created at the moment.
			'address'    => '22 Batman Avenue',
			'address_2'  => '',
			'city'       => 'Gotham',
			'state'      => 'Gotham State',
			'postcode'   => '29292',
			'country'    => 'US',
		) );
		
		// For some reason this is otherwise set to a null user, so we force it to
		// user 1 here to ensure the call to Charitable_Profile_Form::get_user()
		// returns the expected value.
		wp_set_current_user( 1 );

		$privacy  = new Charitable_Privacy();
		$exporter = $privacy->export_user_data( 'james@gotham.com' );
		
		$this->assertCount( 2, $exporter['data'], 'Contains two groups.' );
		$this->assertEquals( 'charitable_users', $exporter['data'][0]['group_id'], 'Contains charitable_users group.' );
		$this->assertEquals( 'charitable_donors', $exporter['data'][1]['group_id'], 'Contains charitable_donors group.' );
	}
}