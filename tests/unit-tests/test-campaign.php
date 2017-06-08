<?php

class Test_Charitable_Campaign extends Charitable_UnitTestCase {	

	/**
	 * There are two campaigns.
	 *
	 * Campaign 1: Goal of $40,000. Expiry 300 days from now.
	 * Campaign 2: No goal. No end date.
	 */
	private $post_1;
	private $campaign_1;
	private $end_date_1;
	private $end_time_1;

	private $post_2;
	private $campaign_2;		

	function setUp() {
		parent::setUp();

		/* Campaign 1: Goal of $40,000. Expiry 300 days from now. */
		$this->end_date_1   = date( 'Y-m-d 00:00:00', strtotime( '+7201 hours') );
		$this->end_time_1 	= strtotime( $this->end_date_1 );
		$campaign_1_id 		= Charitable_Campaign_Helper::create_campaign( array( 
			'_campaign_goal' 					=> 40000.00,
			'_campaign_end_date' 				=> $this->end_date_1, 
			'_campaign_suggested_donations' 	=> array( 
				array( 'amount' => 5 ), 
				array( 'amount' => 20 ), 
				array( 'amount' => 50 ), 
				array( 'amount' => 100 ), 
				array( 'amount' => 250 ) 
			)
		) );

		$this->post_1 		= get_post( $campaign_1_id );
		$this->campaign_1 	= new Charitable_Campaign( $this->post_1 );

		/* Campaign 2: No goal. No end date. */

		$campaign_2_id 		= Charitable_Campaign_Helper::create_campaign( array(
			'_campaign_suggested_donations' 	=> '5|50|150|500'
		) );

		$this->post_2 		= get_post( $campaign_2_id );
		$this->campaign_2 	= new Charitable_Campaign( $this->post_2 );

		/* Create a few users and donations */

		$user_id_1 = $this->factory->user->create( array( 'display_name' => 'John Henry' ) );
		$user_id_2 = $this->factory->user->create( array( 'display_name' => 'Mike Myers' ) );
		$user_id_3 = $this->factory->user->create( array( 'display_name' => 'Fritz Bolton' ) );

		$donations = array(
			array(
				'user_id' 				=> $user_id_1,
				'campaigns'				=> array(
					array(
						'campaign_id'	=> $campaign_1_id,
						'amount' 		=> 10,
					),
				),
				'status'				=> 'charitable-completed',
				'gateway' 				=> 'paypal',
				'note'					=> 'This is a note',
			),
			array( 
				'user_id' 				=> $user_id_2, 
				'campaigns'				=> array(
					array(
						'campaign_id'	=> $campaign_1_id,
						'amount' 		=> 20
					)
				),
				'status'				=> 'charitable-completed', 
				'gateway' 				=> 'paypal', 
				'note'					=> ''
			),
			array( 
				'user_id' 				=> $user_id_3, 
				'campaigns'				=> array(
					array(
						'campaign_id'	=> $campaign_1_id,
						'amount' 		=> 30
					)
				),
				'status'				=> 'charitable-completed', 
				'gateway' 				=> 'manual', 
				'note'					=> ''
			), 
			array(
				'user_id'				=> $user_id_1, 
				'campaigns'				=> array(
					array( 
						'campaign_id' 	=> $campaign_2_id,
						'amount'		=> 25
					)
				), 
				'status'				=> 'charitable-completed', 
				'gateway'				=> 'paypal'
			)
		);

		foreach ( $donations as $donation ) {
			Charitable_Donation_Helper::create_donation( $donation );		
		}		
	}	

	/**
	 * @covers Charitable_Campaign::get
	 */
	function test_get_goal_using_get() {
		$this->assertEquals( 40000.00, $this->campaign_1->get('goal') );
	}

	/**
	 * @covers Charitable_Campaign::get
	 */
	function test_get_end_date_using_get() {
		$this->assertEquals( date( 'Y-m-d 00:00:00', $this->end_time_1 ), $this->campaign_1->get('end_date') );
	}

	/**
	 * @covers Charitable_Campaign::get
	 */
	function test_get_no_goal_using_get() {
		$this->assertEquals( 0, $this->campaign_2->get('goal') );
	}

	/**
	 * @covers Charitable_Campaign::get
	 */
	function test_get_no_end_date_using_get() {
		$this->assertEquals( 0, $this->campaign_2->get('end_date') );
	}

	/**
	 * @covers Charitable_Campaign::is_endless
	 */
	function test_is_endless_for_finite_campaign() {
		$this->assertFalse( $this->campaign_1->is_endless() );		
	}

	/**
	 * @covers Charitable_Campaign::is_endless
	 */
	function test_is_endless_for_endless_campaign() {
		$this->assertTrue( $this->campaign_2->is_endless() );		
	}

	/**
	 * @covers Charitable_Campaign::get_end_time
	 */
	function test_get_end_time_for_finite_campaign() {
		$this->assertEquals( $this->end_time_1, $this->campaign_1->get_end_time() );		
	}

	/**
	 * @depends test_is_endless_for_endless_campaign
	 * @covers Charitable_Campaign::get_end_time
	 * @covers Charitable_Campaign::is_endless
	 */
	function test_get_end_time_for_endless_campaign() {
		$this->assertFalse( $this->campaign_2->get_end_time() );
	}

	/**
	 * @covers Charitable_Campaign::get_end_date
	 */
	function test_get_end_date_for_finite_campaign() {
		$this->assertEquals( date( 'Y-m-d', $this->end_time_1 ), $this->campaign_1->get_end_date( 'Y-m-d' ) );
	}

	/**
	 * @depends test_is_endless_for_endless_campaign
	 * @covers Charitable_Campaign::get_end_date
	 * @covers Charitable_Campaign::is_endless
	 */
	function test_get_end_date_for_endless_campaign() {
		$this->assertFalse( $this->campaign_2->get_end_date() );
	}

	/**
	 * @covers Charitable_Campaign::get_seconds_left
	 */
	function test_get_seconds_left_for_finite_campaign() {
		$seconds_left = $this->end_time_1 - current_time( 'timestamp' );
		$diff = $this->campaign_1->get_seconds_left() - $seconds_left;
		$this->assertFalse( $diff > 4 ); // The different should not be greater than 4 seconds.
	}

	/**
	 * @depends test_is_endless_for_endless_campaign
	 * @covers Charitable_Campaign::get_seconds_left
	 * @covers Charitable_Campaign::is_endless
	 */
	function test_get_seconds_left_for_endless_campaign() {
		$this->assertFalse( $this->campaign_2->get_seconds_left() );
	}

	/**
	 * @depends test_get_seconds_left_for_finite_campaign
	 * @covers Charitable_Campaign::get_time_left
	 * @covers Charitable_Campaign::get_seconds_left
	 */
	function test_get_time_left_with_end_date() {
		$this->assertEquals( '<span class="amount time-left days-left">299</span> Days Left', $this->campaign_1->get_time_left() );		
	}

	/**
	 * @depends test_is_endless_for_endless_campaign
	 * @covers Charitable_Campaign::get_time_left
	 * @covers Charitable_Campaign::is_endless
	 */
	function test_get_time_left_with_no_end_date() {
		$this->assertEquals( '', $this->campaign_2->get_time_left() );
	}

	/**
	 * @covers Charitable_Campaign::get_goal
	 */
	function test_get_goal_with_goal() {
		$this->assertEquals( 40000.00, $this->campaign_1->get_goal() );
	}

	/**
	 * @covers Charitable_Campaign::get_goal
	 */
	function test_get_goal_with_no_goal() {
		$this->assertFalse( $this->campaign_2->get_goal() );
	}

	/**
	 * @covers Charitable_Campaign::has_goal
	 */
	function test_has_goal_with_goal() {
		$this->assertTrue( $this->campaign_1->has_goal() );
	}

	/**
	 * @covers Charitable_Campaign::has_goal
	 */
	function test_has_goal_with_no_goal() {
		$this->assertFalse( $this->campaign_2->has_goal() );
	}

	/**
	 * @covers Charitable_Campaign::get_monetary_goal
	 */
	function test_get_monetary_goal_with_goal() {
		$this->assertEquals( '&#36;40,000.00', $this->campaign_1->get_monetary_goal() );
	}

	/**
	 * @covers Charitable_Campaign::get_monetary_goal
	 */
	function test_get_monetary_goal_with_no_goal() {
		$this->assertEquals( '', $this->campaign_2->get_monetary_goal() );
	}

	/**
	 * @covers Charitable_Campaign_Donations_DB::get_donations_on_campaign
	 */
	function test_get_donations_db_call() {
		$count = charitable_get_table('campaign_donations')->get_donations_on_campaign( $this->campaign_1->ID );
		$this->assertCount( 3, $count, 'Testing call to campaign_donations table to retrieve campaign donations.' );
	}

	/**
	 * @depends test_get_donations_db_call
	 * @covers Charitable_Campaign::get_donations
	 */
	function test_get_donations_correct_count() {
		$this->assertCount( 3, $this->campaign_1->get_donations(), 'Testing Campaign model\'s get_donations method.' );
	}

	/**
	 * @covers Charitable_Campaign::get_donated_amount
	 */
	function test_get_donated_amount() {
		$this->assertEquals( 60.00, $this->campaign_1->get_donated_amount() );
	}

	/**
	 * @depends test_get_donated_amount
	 * @depends test_get_goal_with_goal
	 * @covers Charitable_Campaign::get_percent_donated
	 */
	function test_get_percent_donated_with_goal() {
		$this->assertEquals( '0.15%', $this->campaign_1->get_percent_donated() );
	}

	/**
   	 * @depends test_get_donated_amount
	 * @depends test_get_goal_with_no_goal
	 * @covers Charitable_Campaign::get_percent_donated
	 */
	function test_get_percent_donated_with_no_goal() {
		$this->assertFalse( $this->campaign_2->get_percent_donated() );
	}

	/**
	 * @covers Charitable_Campaign::flush_donations_cache
	 * @covers Charitable_Campaign::get_donations
	 * @covers Charitable_Campaign_Donations_DB::insert
	 * @depends test_get_donations_correct_count
	 */
	function test_flush_donations_cache() {

		// At the start the count of donations is 3. See test_get_donations_correct_count

		// Create a new donation
		Charitable_Donation_Helper::create_donation( array(
			'user_id'			=> $this->factory->user->create( array( 'display_name' => 'Abraham Lincoln' ) ), 
			'campaigns'			=> array(
				array(
					'campaign_id' 		=> $this->campaign_1->ID, 
					'campaign_name'		=> 'Test Campaign',
					'amount'			=> 100
				)				
			),		
			'gateway'			=> 'paypal',
			'status'			=> 'charitable-completed'
		) );

		// Test count of donations again, before flush caching
		$this->assertCount( 4, $this->campaign_1->get_donations() );
	}

	/**
	 * @covers Charitable_Campaign::get_donor_count
	 * @depends test_get_donations_correct_count
	 */
	function test_get_donor_count_with_multiple_donations() {
		$this->assertEquals( 3, $this->campaign_1->get_donor_count() );
	}

	/**
	 * @covers Charitable_Campaign::get_donor_count
	 * @depends test_get_donations_correct_count
	 */
	function test_get_donor_count_with_single_donation() {
		$this->assertEquals( 1, $this->campaign_2->get_donor_count() );
	}

	/**
	 * @covers Charitable_Campaign::get_suggested_donations
	 */
	function test_suggested_amounts() {	
		$this->assertCount( 5, $this->campaign_1->get_suggested_donations() );
	}

	/**
	 * @covers Charitable_Campaign::get_donation_form
	 */
	function test_get_donation_form() {
		$this->assertInstanceOf( 'Charitable_Donation_Form', $this->campaign_1->get_donation_form() );
	}
}