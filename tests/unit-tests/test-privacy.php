<?php

class Test_Charitable_Privacy extends Charitable_UnitTestCase {

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
		$this->assertTrue( false );
	}

	/**
	 * @covers Charitable_Privacy::get_data_retention_period
	 */
	public function test_get_data_retention_period() {
		$privacy = new Charitable_Privacy();

		$this->set_charitable_option( 'minimum_data_retention_period', 2 );

		$this->assertEquals( 2, $privacy->get_data_retention_period() );
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

		$donation_id = Charitable_Donation_Helper::create_donation();
		
		$this->assertFalse( $privacy->is_donation_in_data_retention_period( $donation_id ) );
	}

	/**
	 * @covers Charitable_Privacy::is_donation_in_data_retention_period
	 */
	public function test_with_endless_data_retention_period_donation_is_in_period() {
		$privacy = new Charitable_Privacy();

		$this->set_charitable_option( 'minimum_data_retention_period', 'endless' );

		$donation_id = Charitable_Donation_Helper::create_donation();
		
		$this->assertTrue( $privacy->is_donation_in_data_retention_period( $donation_id ) );
	}

	/**
	 * @covers Charitable_Privacy::is_donation_in_data_retention_period
	 */
	public function test_new_donation_in_data_retention_period_with_one_year_period() {
		$privacy = new Charitable_Privacy();

		$this->set_charitable_option( 'minimum_data_retention_period', 1 );

		$donation_id = Charitable_Donation_Helper::create_donation();
		
		$this->assertTrue( $privacy->is_donation_in_data_retention_period( $donation_id ) );
	}

	/**
	 * @covers Charitable_Privacy::is_donation_in_data_retention_period
	 */
	public function test_old_donation_in_data_retention_period_with_one_year_period() {
		$privacy = new Charitable_Privacy();

		$this->set_charitable_option( 'minimum_data_retention_period', 1 );

		$donation_id = Charitable_Donation_Helper::create_donation( array(
			'date_gmt' => date( 'Y-m-d H:i:s', strtotime( '2 years ago' ) ),
		) );
		
		$this->assertFalse( $privacy->is_donation_in_data_retention_period( $donation_id ) );
	}

	/**
	 * @covers Charitable_Privacy::get_erasable_fields_for_donation
	 * @depends test_get_user_donation_fields
	 */
	public function test_get_erasable_fields_for_donation_in_data_retention_period() {
		$this->assertTrue( false );
	}

	/**
	 * @covers Charitable_Privacy::get_erasable_fields_for_donation
	 * @depends test_get_user_donation_fields
	 */
	public function test_get_erasable_fields_for_donation_outside_data_retention_period() {
		$this->assertTrue( false );
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
		
		$privacy  = new Charitable_Privacy();
		$exporter = $privacy->export_user_data( 'james@gotham.com' );
		$groups   = wp_list_pluck( $exporter, 'group_id' );
		
		$this->assertContains( 'charitable_users', $groups, 'Contains charitable_users group.' );
		$this->assertNotContains( 'charitable_donors', $groups, 'Does not contain charitable_donors group.' );
		$this->assertNotContains( 'charitable_donations', $groups, 'Does not contain charitable_donations group.' );
	}
}