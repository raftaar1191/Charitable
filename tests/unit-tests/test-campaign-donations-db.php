<?php

class Test_Campaign_Donations_DB extends Charitable_UnitTestCase {

	public function tearDown() {
		global $wpdb;

		$wpdb->query( "TRUNCATE {$wpdb->prefix}charitable_donors;" );
		$wpdb->query( "TRUNCATE {$wpdb->prefix}charitable_campaign_donations;" );

		parent::tearDown();
	}

	/**
	 * @covers Charitable_Campaign_Donations_DB::insert()
	 */
	public function test_insert() {
		$campaign_donation_id = charitable_get_table('campaign_donations')->insert( array(
			'donation_id' => 0,
			'campaign_id' => Charitable_Campaign_Helper::create_campaign( array( 'post_title' => 'Campaign 1' ) ),
			'amount'      => 10,
		) );

		$this->assertGreaterThan( 0, $campaign_donation_id );
	}

	/**
	 * @covers Charitable_Campaign_Donations_DB::count_all()
	 */
	public function test_count_all() {
		$this->create_donations();

		$this->assertEquals( '4', charitable_get_table( 'campaign_donations' )->count_all() );
	}

	/**
	 * Create donations.
	 */
	private function create_donations() {
		$c1 = Charitable_Campaign_Helper::create_campaign( array( 'post_title' => 'Campaign 1' ) );
		$c2 = Charitable_Campaign_Helper::create_campaign( array( 'post_title' => 'Campaign 2' ) );

		$d1 = Charitable_Donation_Helper::create_donation( array( 'campaigns' => array(
			array(
				'campaign_id' 	=> $c1, 
				'campaign_name' => get_the_title( $c1 ), 
				'amount'		=> 10
			)
		) ) );

		$d2 = Charitable_Donation_Helper::create_donation( array( 'campaigns' => array(
			array(
				'campaign_id' 	=> $c2, 
				'campaign_name' => get_the_title( $c2 ), 
				'amount'		=> 10
			)
		) ) );

		$d3 = Charitable_Donation_Helper::create_donation( array( 'campaigns' => array(
			array(
				'campaign_id' 	=> $c1, 
				'campaign_name' => get_the_title( $c1 ), 
				'amount'		=> 30
			), 
			array(
				'campaign_id' 	=> $c2, 
				'campaign_name' => get_the_title( $c2 ), 
				'amount'		=> 40
			)
		) ) );

		$this->add_stub( 'posts', array( $d1, $d2, $d3, $c1, $c2 ) );
	}
}