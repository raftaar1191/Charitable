<?php

/**
 * Contains tests for functions added in `includes/endpoints/charitable-endpoints-functions.php`.
 */

class Test_Charitable_Endpoints_Functions extends Charitable_UnitTestCase {

	private static $campaign_id;

	private static $donation_id;

	public function setUp() {
		parent::setUp();

		self::$campaign_id = Charitable_Campaign_Helper::create_campaign();

		self::$donation_id = Charitable_Donation_Helper::create_donation( array(
			'campaigns' => array(
				array(
					'campaign_id' => self::$campaign_id,
					'amount' => 50,
					'campaign_name' => 'Test Campaign',
				),
			),
			'user' => array(
				'first_name' => 'Matthew',
				'last_name' => 'Murdoch',
				'email' => 'matthew.murdoch@example.com',
			),
		) );

		$settings = get_option( 'charitable_settings' );

		$settings['login_page'] = wp_insert_post( array(
			'post_content' => '[charitable_login]',
			'post_status'  => 'publish',
		) );

		$settings['registration_page'] = wp_insert_post( array(
			'post_content' => '[charitable_registration]',
			'post_status'  => 'publish',
		) );

		$settings['profile_page'] = wp_insert_post( array(
			'post_content' => '[charitable_profile]',
			'post_status'  => 'publish',
		) );

		update_option( 'charitable_settings', $settings );

		/**
		 * Temporary workaround for issue noted below.
		 * @see https://core.trac.wordpress.org/ticket/37207
		 */
		charitable()->endpoints()->setup_rewrite_rules();
	}

	/**
	 * @covers charitable_is_campaign_donation_page
	 */
	public function test_is_campaign_donation_page() {

		$page = charitable_get_campaign_donation_page_permalink( false, array( 'campaign_id' => self::$campaign_id ) );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_campaign_donation_page( false ) );
	}

	/**
	 * @covers charitable_get_permalink
	 * @depends test_is_campaign_donation_page
	 */
	public function test_is_campaign_donation_page_with_wrapper() {

		$page = charitable_get_permalink( 'campaign_donation_page', array( 'campaign_id' => self::$campaign_id ) );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_page( 'campaign_donation_page' ) );
	}

	/**
	 * @covers charitable_is_campaign_donation_page
	 * @depends test_is_campaign_donation_page
	 */
	public function test_is_campaign_donation_page_strict() {

		$this->set_charitable_option( 'donation_form_display', 'separate_page' );

		$page = charitable_get_campaign_donation_page_permalink( false, array( 'campaign_id' => self::$campaign_id ) );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_campaign_donation_page( false, array( 'strict' => true ) ) );
	}

	/**
	 * @covers charitable_is_campaign_donation_page
	 * @depends test_is_campaign_donation_page
	 */
	public function test_is_not_campaign_donation_page_strict() {

		$this->set_charitable_option( 'donation_form_display', 'same_page' );

		$page = charitable_get_campaign_donation_page_permalink( false, array( 'campaign_id' => self::$campaign_id ) );

		$this->go_to( $page );

		$this->assertFalse( charitable_is_campaign_donation_page( false, array( 'strict' => true ) ) );
	}

	/**
	 * @covers charitable_get_campaign_widget_page_permalink
	 * @covers charitable_is_campaign_widget_page
	 */
	public function test_is_campaign_widget_page() {

		$page = charitable_get_campaign_widget_page_permalink( false, array( 'campaign_id' => self::$campaign_id ) );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_campaign_widget_page( false ) );

	}

	/**
	 * @covers charitable_get_permalink
	 * @covers charitable_is_page
	 * @depends test_is_campaign_widget_page
	 */
	public function test_is_campaign_widget_page_with_wrapper() {

		$page = charitable_get_permalink( 'campaign_widget_page', array( 'campaign_id' => self::$campaign_id ) );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_page( 'campaign_widget_page' ) );

	}

	/**
	 * @covers charitable_is_donation_receipt_page
	 */
	public function test_is_donation_receipt_page() {

		$page = charitable_get_donation_receipt_page_permalink( false, array( 'donation_id' => self::$donation_id ) );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_donation_receipt_page() );

	}

	/**
	 * @covers charitable_get_permalink
	 * @covers charitable_is_page
	 * @depends test_is_donation_receipt_page
	 */
	public function test_is_donation_receipt_page_with_wrapper() {

		$page = charitable_get_permalink( 'donation_receipt', array( 'donation_id' => self::$donation_id ) );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_page( 'donation_receipt' ) );

	}

	/**
	 * @covers charitable_is_donation_processing_page
	 */
	public function test_is_donation_processing_page() {

		$page = charitable_get_donation_processing_page_permalink( false, array( 'donation_id' => self::$donation_id ) );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_donation_processing_page() );

	}

	/**
	 * @covers charitable_get_permalink
	 * @covers charitable_is_page
	 * @depends test_is_donation_processing_page
	 */
	public function test_is_donation_processing_page_with_wrapper() {

		$page = charitable_get_permalink( 'donation_processing', array( 'donation_id' => self::$donation_id ) );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_page( 'donation_processing' ) );

	}

	/**
	 * @covers charitable_is_donation_cancel_page
	 */
	public function test_charitable_is_donation_cancel_page() {

		$page = charitable_get_donation_cancel_page_permalink( false, array( 'donation_id' => self::$donation_id ) );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_donation_cancel_page( false ) );

	}

	/**
	 * @covers charitable_get_permalink
	 * @covers charitable_is_page
	 * @depends test_charitable_is_donation_cancel_page
	 */
	public function test_charitable_is_donation_cancel_page_with_wrapper() {

		$page = charitable_get_permalink( 'donation_cancellation', array( 'donation_id' => self::$donation_id ) );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_page( 'donation_cancellation' ) );

	}

	/**
	 * @covers charitable_is_login_page
	 */
	public function test_charitable_is_login_page() {

		$page = charitable_get_login_page_permalink();

		$this->go_to( $page );

		$this->assertTrue( charitable_is_login_page( false ) );

	}

	/**
	 * @covers charitable_get_permalink
	 * @covers charitable_is_page
	 * @depends test_charitable_is_login_page
	 */
	public function test_charitable_is_login_page_with_wrapper() {

		$page = charitable_get_permalink( 'login' );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_page( 'login' ) );

	}

	/**
	 * @covers charitable_is_registration_page
	 */
	public function test_charitable_is_registration_page() {

		$page = charitable_get_registration_page_permalink();

		$this->go_to( $page );

		$this->assertTrue( charitable_is_registration_page( false ) );

	}

	/**
	 * @covers charitable_get_permalink
	 * @covers charitable_is_page
	 * @depends test_charitable_is_registration_page
	 */
	public function test_charitable_is_registration_page_with_wrapper() {

		$page = charitable_get_permalink( 'registration' );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_page( 'registration' ) );

	}

	/**
	 * @covers charitable_is_profile_page
	 */
	public function test_charitable_is_profile_page() {

		$page = charitable_get_profile_page_permalink();

		$this->go_to( $page );

		$this->assertTrue( charitable_is_profile_page( false ) );

	}

	/**
	 * @covers charitable_get_permalink
	 * @covers charitable_is_page
	 * @depends test_charitable_is_profile_page
	 */
	public function test_charitable_is_profile_page_with_wrapper() {

		$page = charitable_get_permalink( 'profile' );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_page( 'profile' ) );

	}

	/**
	 * @covers charitable_is_donation_cancel_page
	 */
	public function test_charitable_is_forgot_password_page() {

		$page = charitable_get_forgot_password_page_permalink();

		$this->go_to( $page );

		$this->assertTrue( charitable_is_forgot_password_page( false ) );

	}

	/**
	 * @covers charitable_get_permalink
	 * @covers charitable_is_page
	 * @depends test_charitable_is_forgot_password_page
	 */
	public function test_charitable_is_forgot_password_page_with_wrapper() {

		$page = charitable_get_permalink( 'forgot_password' );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_page( 'forgot_password' ) );

	}

	/**
	 * @covers charitable_is_reset_password_page
	 */
	public function test_charitable_is_reset_password_page() {

		$page = charitable_get_reset_password_page_permalink();

		$this->go_to( $page );

		$this->assertTrue( charitable_is_reset_password_page( false ) );

	}

	/**
	 * @covers charitable_get_permalink
	 * @covers charitable_is_page
	 * @depends test_charitable_is_reset_password_page
	 */
	public function test_charitable_is_reset_password_page_with_wrapper() {

		$page = charitable_get_permalink( 'reset_password' );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_page( 'reset_password' ) );

	}

	/**
	 * @covers charitable_is_email_preview
	 */
	public function test_is_email_preview() {

		$page = charitable_get_permalink( 'email_preview', array( 'email_id' => 'campaign_end' ) );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_email_preview() );

	}

	/**
	 * @covers charitable_is_page
	 * @depends test_is_email_previeww
	 */
	public function test_is_email_preview_with_wrapper() {

		$page = charitable_get_permalink( 'email_preview', array( 'email_id' => 'campaign_end' ) );

		$this->go_to( $page );

		$this->assertTrue( charitable_is_page( 'email_preview' ) );

	}
}
