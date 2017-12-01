<?php

class Test_Charitable_Campaigns extends Charitable_UnitTestCase {

	// public function tearDown() {
	// 	global $wpdb;

	// 	$wpdb->query( "TRUNCATE {$wpdb->prefix}charitable_donors;" );
	// 	$wpdb->query( "TRUNCATE {$wpdb->prefix}charitable_campaign_donations;" );
	// 	$wpdb->query( "TRUNCATE $wpdb->posts;" );
	// 	$wpdb->query( "TRUNCATE $wpdb->postmeta;" );

	// 	parent::tearDown();
	// }

	/**
	 * @covers Charitable_Campaigns::query()
	 */
	public function test_query() {
		$expected = count( $this->campaigns() );

		$this->assertEquals(
			$expected,
			Charitable_Campaigns::query()->found_posts
		);
	}

	function test_ordered_by_ending_soon_count() {
		$this->campaigns();

		$this->assertEquals(
			3,
			Charitable_Campaigns::ordered_by_ending_soon()->found_posts
		);
	}

	function test_ordered_by_ending_soon_order() {
		$this->campaigns();

		$query = Charitable_Campaigns::ordered_by_ending_soon();

		$i = 0;

		while( $query->have_posts() ) {
			$query->the_post();

			$this->assertEquals(
				$this->campaigns_ordered_by_ending_soon[$i],
				get_the_ID(),
				sprintf( 'Index %d for campaigns orderd by date ending', $i )
			);

			$i++;
		}
	}

	/**
	 * Campaigns data provider.
	 */
	public function campaigns() {
		$u1 = $this->factory->user->create( array( 'display_name' => 'John Henry' ) );

		/**
		 * Campaign 1:
		 *
		 * End date: 			300 days from now
		 * Donations received: 	$1000
		 */
		$c1 = Charitable_Campaign_Helper::create_campaign( array( 
			'_campaign_end_date' => date( 'Y-m-d H:i:s', strtotime( '+300 days') )
		) );

		$d1 = Charitable_Donation_Helper::create_campaign_donation_for_user( $u1, $c1, 1000 );

		/**
		 * Campaign 2:
		 *
		 * End date: 			100 days from now
		 * Donations received: 	$50
		 */
		$c2 = Charitable_Campaign_Helper::create_campaign( array( 
			'_campaign_end_date' => date( 'Y-m-d H:i:s', strtotime( '+100 days') )
		) );

		$d2 = Charitable_Donation_Helper::create_campaign_donation_for_user( $u1, $c2, 50 );

		/**
		 * Campaign 3:
		 *
		 * End date: 			2 days from now
		 * Donations received: 	$200
		 */
		$c3 = Charitable_Campaign_Helper::create_campaign( array( 
			'_campaign_end_date' => date( 'Y-m-d H:i:s', strtotime( '+2 days') )
		) );

		$d3 = Charitable_Donation_Helper::create_campaign_donation_for_user( $u1, $c3, 200 );

		/**
		 * Campaign 4:
		 *
		 * End date: 			2 days ago
		 * Donations received: 	$40
		 */
		$c4 = Charitable_Campaign_Helper::create_campaign( array( 
			'_campaign_end_date' => date( 'Y-m-d H:i:s', strtotime( '-2 days') )
		) );

		$d4 = Charitable_Donation_Helper::create_campaign_donation_for_user( $u1, $c4, 40 );

		/* Prepare for deletion. */
		$this->add_stub( 'posts', array( $c1, $c2, $c3, $c4, $d1, $d2, $d3, $d4 ) );
		$this->add_stub( 'users', array( $u1 ) );

		/* The array of campaign IDs, ordered by ending soon */
		$this->campaigns_ordered_by_ending_soon = array(
			$c3, 
			$c2,
			$c1
		);

		/* The array of campaign IDs, ordered by amount raised */
		$this->campaigns_ordered_by_amount = array(
			$c1, 
			$c3,
			$c2,
			$c4
		);

		return array(
			array( $c1 ),
			array( $c2 ),
			array( $c3 ),
			array( $c4 ),
		);
	}
}