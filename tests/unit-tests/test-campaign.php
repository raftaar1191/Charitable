<?php

class Test_Charitable_Campaign extends Charitable_UnitTestCase {

	private $campaigns;

	public function setUp() {
		parent::setUp();

		$args = array(
			array(
				'_campaign_goal'                => 40000.00,
				'_campaign_end_date'            => date( 'Y-m-d 00:00:00', strtotime( '+7201 hours') ),
				'_campaign_suggested_donations' => array(
					array( 'amount' => 5 ),
					array( 'amount' => 20 ),
					array( 'amount' => 50 ),
					array( 'amount' => 100 ),
					array( 'amount' => 250 ),
				),
			),
			array(
				'_campaign_suggested_donations' => array(
					array( 'amount' => 5 ),
					array( 'amount' => 50 ),
					array( 'amount' => 150 ),
					array( 'amount' => 500 ),
				),
			),
		);

		$this->campaigns = array_reduce( $args, array( $this, 'map_campaign_args' ) );
	}

	// public function tearDown() {
	// 	$this->add_stub( 'posts', $this->post_stubs );
	// 	$this->add_stub( 'users', $this->user_stubs );

	// 	parent::tearDown();
	// }

	// public static function tearDownAfterClass() {
	// 	// global $wpdb;
	// 	// $wpdb->query( "TRUNCATE {$wpdb->prefix}charitable_donors" );
	// 	// $wpdb->query( "TRUNCATE {$wpdb->prefix}charitable_campaign_donations" );

	// 	parent::tearDownAfterClass();
	// }

	/**
	 * @covers Charitable_Campaign::get
	 */
	function test_get_goal_using_get() {
		foreach ( $this->campaigns as $stub ) {		
			$this->assertEquals(
				$stub['expected']['goal'],
				$stub['campaign']->get('goal')
			);	
		}
	}

	/**	 
	 * @covers Charitable_Campaign::get_goal
	 * @depends test_get_goal_using_get
	 */
	function test_get_goal_using_get_goal() {
		foreach ( $this->campaigns as $args ) {
			$args['expected'] = 0 === $args['expected']['goal'] ? false : $args['expected']['goal'];

			$this->assertEquals(
				$args['expected'],
				$args['campaign']->get_goal()
			);
		}
	}

	/**	 
	 * @covers Charitable_Campaign::has_goal
	 * @depends test_get_goal_using_get
	 */
	function test_has_goal() {
		foreach ( $this->campaigns as $args ) {
			if ( 0 === $args['expected']['goal'] ) {
				$this->assertFalse(
					$args['campaign']->has_goal()
				);
			} else {
				$this->assertTrue(
					$args['campaign']->has_goal()
				);
			}
		}		
	}

	/**	 
	 * @covers Charitable_Campaign::get_monetary_goal
	 * @depends test_get_goal_using_get
	 */
	function test_get_monetary_goal_with_goal() {
		foreach ( $this->campaigns as $args ) {
			$args['expected'] = 0 === $args['expected']['goal'] ? '' : '&#36;' . number_format( $args['expected']['goal'], 2 );

			$this->assertEquals(
				$args['expected'],
				$args['campaign']->get_monetary_goal()
			);
		}
	}

	/**	 
	 * @covers Charitable_Campaign::get
	 */
	function test_get_end_date_using_get() {		
		foreach ( $this->campaigns as $args ) {
			$this->assertEquals(
				$args['expected']['end_date'],
				$args['campaign']->get('end_date')
			);
		}
	}

	/**	 
	 * @covers Charitable_Campaign::get_end_date
	 * @depends test_get_end_date_using_get
	 */
	function test_get_end_date_using_get_end_date() {
		foreach ( $this->campaigns as $args ) {
			$args['expected'] = 0 === $args['expected']['end_date'] ? 0 : substr( $args['expected']['end_date'], 0, 10 );

			$this->assertEquals(
				$args['expected'],
				$args['campaign']->get_end_date( 'Y-m-d' )
			);
		}		
	}

	/**	 
	 * @covers Charitable_Campaign::is_endless
	 * @depends test_get_end_date_using_get
	 */
	function test_is_endless() {
		foreach ( $this->campaigns as $args ) {
			$this->assertEquals(
				$args['expected']['is_endless'],
				$args['campaign']->is_endless()
			);
		}		
	}

	/**	 
	 * @covers Charitable_Campaign::get_end_time
	 * @covers Charitable_Campaign::is_endless
	 * @depends test_get_end_date_using_get
	 */
	function test_get_end_time() {
		foreach ( $this->campaigns as $args ) {
			$this->assertEquals(
				$args['expected']['end_time'], 
				$args['campaign']->get_end_time()
			);
		}
	}

	/**	 
	 * @covers Charitable_Campaign::get_seconds_left
	 * @covers Charitable_Campaign::is_endless
	 */
	function test_get_seconds_left() {
		foreach ( $this->campaigns as $args ) {
			if ( 0 === $args['expected']['end_time'] ) {
				return $this->assertFalse( $args['campaign']->get_seconds_left() );
			}

			$diff = $args['campaign']->get_seconds_left() - ( $args['expected']['end_time'] - current_time( 'timestamp' ) );

			$this->assertFalse(
				$diff > 4 // The difference should not be greater than 4 seconds.
			);
		}		
	}

	/**	 
	 * @depends test_get_seconds_left_for_finite_campaign
	 * @covers Charitable_Campaign::get_time_left
	 * @covers Charitable_Campaign::get_seconds_left
	 */
	function test_get_time_left() {
		foreach ( $this->campaigns as $args ) {
			if ( 0 === $args['expected']['end_time'] ) {
				$args['expected'] = '';
			} else {
				$days_left = floor( ( $args['expected']['end_time'] - current_time( 'timestamp' ) ) / 86400 );
				$args['expected'] = '<span class="amount time-left days-left">' . $days_left . '</span> Days Left';
			}

			$this->assertEquals(
				$args['expected'],
				$args['campaign']->get_time_left()
			);
		}	
	}

	/**
	 * @covers Charitable_Campaign_Donations_DB::get_donations_on_campaign
	 */
	function test_get_donations_db_call() {
		foreach ( $this->campaigns_with_donations() as $args ) {
			$this->assertCount(
				$args['expected']['count'], 
				charitable_get_table( 'campaign_donations' )->get_donations_on_campaign( $args['campaign']->ID ),
				'Testing call to campaign_donations table to retrieve campaign donations.'
			);
		}		
	}

	/**
	 * @depends test_get_donations_db_call
	 * @covers Charitable_Campaign::get_donations
	 */
	function test_get_donations_count() {
		foreach ( $this->campaigns_with_donations() as $args ) {
			$this->assertCount(
				$args['expected']['count'], 
				$args['campaign']->get_donations(),
				'Testing Campaign model\'s get_donations method.'
			);
		}
	}

	/**
	 * @depends test_get_donations_count
	 * @covers Charitable_Campaign::get_donated_amount
	 */
	function test_get_donated_amount() {
		foreach ( $this->campaigns_with_donations() as $args ) {
			$this->assertEquals(
				number_format( $args['expected']['amount'], 2 ),
				$args['campaign']->get_donated_amount()
			);
		}
	}

	/**
	 * @depends test_get_donated_amount
	 * @covers Charitable_Campaign::get_donated_amount
	 */
	function test_get_donated_amount_sanitized() {
		foreach ( $this->campaigns_with_donations() as $args ) {
			$this->assertEquals(
				number_format( $args['expected']['amount'], 2 ),
				$args['campaign']->get_donated_amount( true )
			);
		}
	}

	/**
	 * @depends test_get_donated_amount
	 * @covers Charitable_Campaign::get_donated_amount
	 */
	function test_get_donated_amount_with_comma_decimal() {
		foreach ( $this->campaigns_with_donations() as $args ) {
			Charitable_Campaign::flush_donations_cache( $args['campaign']->ID );

			$this->set_charitable_option( 'decimal_separator', ',' );
			$this->set_charitable_option( 'thousands_separator', '.' );

			$expected = number_format( $args['expected']['amount'], 4, ',', '.' );

			$this->assertEquals(
				$expected, 
				$args['campaign']->get_donated_amount()
			);
		}
	}

	/**
	 * @depends test_get_donated_amount
	 * @depends test_get_donated_amount_with_comma_decimal
	 * @covers Charitable_Campaign::get_donated_amount
	 */
	function test_get_donated_amount_with_comma_decimal_sanitized() {
		foreach ( $this->campaigns_with_donations() as $args ) {
			Charitable_Campaign::flush_donations_cache( $args['campaign']->ID );

			$this->set_charitable_option( 'decimal_separator', ',' );
			$this->set_charitable_option( 'thousands_separator', '.' );

			$expected = number_format( $args['expected']['amount'], 4, '.', ',' );

			$this->assertEquals(
				$expected, 
				$args['campaign']->get_donated_amount( true )
			);
		}
	}

	/**
	 * @depends test_get_donated_amount
	 * @depends test_get_goal_using_get
	 * @covers Charitable_Campaign::get_percent_donated_raw
	 */
	function test_get_percent_donated_raw() {
		foreach ( $this->campaigns_with_donations() as $args ) {
			$expected = 0 === $args['expected']['goal'] ? false : ( 60 / $args['expected']['goal'] ) * 100;

			$this->assertEquals(
				$expected,
				$args['campaign']->get_percent_donated_raw()
			);
		}
	}

	/**
	 * @depends test_get_percent_donated_raw
	 * @depends test_get_donated_amount
	 * @depends test_get_goal_using_get
	 * @covers Charitable_Campaign::get_percent_donated
	 */
	function test_get_percent_donated() {
		foreach ( $this->campaigns_with_donations() as $args ) {
			$expected = 0 === $args['expected']['goal'] ? false : number_format( ( 60 / $args['expected']['goal'] ) * 100, 2 ) . '%';

			$this->assertEquals(
				$expected,
				$args['campaign']->get_percent_donated()
			);
		}
	}

	/**
	 * @covers Charitable_Campaign::flush_donations_cache
	 * @covers Charitable_Campaign::get_donations
	 * @covers Charitable_Campaign_Donations_DB::insert
	 * @depends test_get_donations_count
	 */
	function test_flush_donations_cache() {
		foreach ( $this->campaigns_with_donations() as $args ) {
			$u = $this->factory->user->create( array( 'display_name' => 'Abraham Lincoln' ) );
			$d = Charitable_Donation_Helper::create_donation( array(
				'user_id'   => $u, 
				'gateway'   => 'paypal',
				'status'    => 'charitable-completed',
				'campaigns' => array(
					array(
						'campaign_id'   => $args['campaign']->ID, 
						'campaign_name' => 'Test Campaign',
						'amount'        => 100,
					),
				),
			) );

			$this->add_stub( 'users', $u );
			$this->add_stub( 'posts', $d );

			// Test count of donations again, before flush caching
			$this->assertCount(
				$args['expected']['count'] + 1,
				$args['campaign']->get_donations()
			);
		}
	}

	/**
	 * @covers Charitable_Campaign::get_donor_count
	 * @depends test_get_donations_count
	 */
	function test_get_donor_count() {
		foreach ( $this->campaigns_with_donations() as $args ) {
			$this->assertEquals(
				$args['expected']['donors'], 
				$args['campaign']->get_donor_count()
			);
		}
	}

	/**	 
	 * @covers Charitable_Campaign::get_suggested_donations
	 */
	public function test_get_suggested_amounts() {	
		foreach ( $this->campaigns as $args ) {
			$this->assertCount(
				$args['expected']['suggested'],
				$args['campaign']->get_suggested_donations()
			);
		}
	}

	/**
	 * @covers Charitable_Campaign::get_donation_form
	 */
	public function test_get_donation_form() {
		$campaign = current( $this->campaigns );

		$this->assertInstanceOf(
			'Charitable_Donation_Form',
			$campaign['campaign']->get_donation_form()
		);
	}

	/**
	 * Campaigns data provider.
	 */
	public function campaigns_with_donations() {
		$campaigns    = $this->campaigns;
		$campaign_ids = array_keys( $campaigns );

		/* Create a few users */
		$user_id_1 = $this->factory->user->create( array( 'display_name' => 'John Henry' ) );
		$user_id_2 = $this->factory->user->create( array( 'display_name' => 'Mike Myers' ) );
		$user_id_3 = $this->factory->user->create( array( 'display_name' => 'Fritz Bolton' ) );

		$this->add_stub( 'users', array( $user_id_1, $user_id_2, $user_id_3 ) );

		$donations = array(
			array(
				'user_id'   => $user_id_1,
				'status'    => 'charitable-completed',
				'gateway'   => 'paypal',
				'campaigns' => array(
					array(
						'campaign_id' => $campaign_ids[0],
						'amount'      => 10,
					),
				),
			),
			array(
				'user_id'   => $user_id_1,
				'status'    => 'charitable-completed',
				'gateway'   => 'paypal',
				'campaigns' => array(
					array(
						'campaign_id' => $campaign_ids[0],
						'amount'      => 20,
					),
				),
			),
			array(
				'status'    => 'charitable-completed',
				'gateway'   => 'manual',
				'user_id'   => $user_id_3,
				'campaigns' => array(
					array(
						'campaign_id' => $campaign_ids[0],
						'amount'      => 30,
					)
				),
			), 
			array(
				'status'    => 'charitable-completed',
				'gateway'   => 'paypal',
				'user_id'   => $user_id_1, 
				'campaigns' => array(
					array(
						'campaign_id' => $campaign_ids[1],
						'amount'      => 25,
					),
				),
			),
		);

		foreach ( $donations as $donation ) {
			$this->add_stub( 'posts', Charitable_Donation_Helper::create_donation( $donation ) );
		}

		$campaigns[ $campaign_ids[0] ]['expected'] = array_merge(
			array(
				'count'  => 3,
				'amount' => 60,
				'donors' => 3,
			),
			$campaigns[ $campaign_ids[0] ]['expected']
		);

		$campaigns[ $campaign_ids[1] ]['expected'] = array_merge( 
			array(
				'count'  => 1,
				'amount' => 25,
				'donors' => 1,
			),
			$campaigns[ $campaign_ids[1] ]['expected']
		);

		return $campaigns;
	}

	/**
	 * Receives campaign args and returns an array
	 * containing the Charitable_Campaign object,
	 * and expected results for different tests.
	 *
	 * @param  array|null|0 $ret
	 * @param  array        $args
	 * @return array
	 */
	private function map_campaign_args( $ret, $args ) {
		if ( ! is_array( $ret ) ) {
			$ret = array();
		}

		$defaults = array(
			'_campaign_goal'                   => 0,
			'_campaign_end_date'               => 0,
			'_campaign_suggested_donations'    => '',
			'_campaign_allow_custom_donations' => 1,
		);

		$args                = array_merge( $defaults, $args );
		$campaign_id         = Charitable_Campaign_Helper::create_campaign( $args );
		$ret[ $campaign_id ] = array(
			'campaign'     => charitable_get_campaign( $campaign_id ),
			'expected'   => array(
				'goal'       => $args['_campaign_goal'],
				'end_date'   => 0 === $args['_campaign_end_date'] ? 0 : date( 'Y-m-d 00:00:00', strtotime( $args['_campaign_end_date'] ) ),
				'end_time'   => 0 === $args['_campaign_end_date'] ? 0 : strtotime( $args['_campaign_end_date'] ),
				'is_endless' => 0 === $args['_campaign_end_date'],
				'suggested'  => count( $args['_campaign_suggested_donations'] ),
			),
		);		

		$this->add_stub( 'posts', $campaign_id );

		return $ret;
	} 
}