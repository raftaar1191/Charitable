<?php

class Test_Charitable extends Charitable_UnitTestCase {

    function setUp() {
        parent::setUp();
        $this->charitable = charitable();
        $this->directory_path = $this->charitable->get_path( 'directory' );
        $this->directory_url = $this->charitable->get_path( 'directory', false );
    }

    function test_static_instance() {
        $this->assertClassHasStaticAttribute( 'instance', get_class( $this->charitable ) );
    }

    /**
     * @dataProvider files
     * @covers Charitable::load_dependencies()
     */
    function test_load_dependencies( $file ) {
        $this->assertFileExists( $file );        
    }

    /**
     * @covers Charitable::autoloader()
     */
    public function test_autoloader_with_valid_class() {
        // We don't call autoloader directly because it will return
        // false if this class has already been autoloaded.
        $this->assertTrue( class_exists( 'Charitable_Export_Donations' ) );
    }

    /**
     * @covers Charitable::autoloader()
     */
    public function test_autoloader_with_invalid_class() {
        $this->assertFalse( charitable()->autoloader( 'Charitable_Not_A_Valid_Class' ) );
    }

    /**
     * @covers Charitable::registry()
     */
    public function test_registry() {
        $this->assertInstanceOf( 'Charitable_Registry', charitable()->registry() );
    }

    /**
     * @dataProvider init_classes
     * @covers Charitable::registry()
     */
    public function test_registry_init_classes( $class ) {
        $this->assertTrue( charitable()->registry()->has( $class ) );
    }

    /**
     * @covers Charitable::attach_hooks_and_filters()
     */
    function test_attach_hooks_and_filters() {
        $this->assertEquals( 10, has_action( 'wpmu_new_blog', array( charitable(), 'maybe_activate_charitable_on_new_site' ) ) );
        $this->assertEquals( 100, has_action( 'plugins_loaded', array( charitable(), 'charitable_install' ) ) );
        $this->assertEquals( 100, has_action( 'plugins_loaded', array( charitable(), 'charitable_start' ) ) );
        $this->assertEquals( 100, has_action( 'plugins_loaded', array( charitable(), 'endpoints' ) ) );
        $this->assertEquals( 100, has_action( 'plugins_loaded', array( charitable(), 'donation_fields' ) ) );
        $this->assertEquals( 100, has_action( 'plugins_loaded', array( charitable(), 'campaign_fields' ) ) );
        $this->assertEquals( 10, has_action( 'plugins_loaded', 'charitable_load_compat_functions' ) );
        $this->assertEquals( 10, has_action( 'setup_theme', array( 'Charitable_Customizer', 'start' ) ) );
        $this->assertEquals( 100, has_action( 'wp_enqueue_scripts', array( charitable(), 'maybe_start_qunit' ) ) );
        $this->assertEquals( 20, has_action( 'init', array( charitable(), 'do_charitable_actions' ) ) );
    }

    /**
     * @covers Charitable::is_start()
     */
    function test_is_start() {
        $this->assertFalse( $this->charitable->is_start() );
    }

    /**
     * @covers Charitable::started()
     */
    function test_started() {
        $this->assertTrue( $this->charitable->started() );
    }   

    /**
     * @covers Charitable::donation_fields()
     */
    public function test_donation_fields() {
        $this->assertInstanceOf( 'Charitable_Donation_Field_Registry', $this->charitable->donation_fields() );
    }

    /**
     * @covers Charitable::donation_fields()
     * @depends test_donation_fields
     * @dataProvider donation_fields
     */
    public function test_donation_fields_has_registered_fields( $field ) {
        $this->assertInstanceOf( 'Charitable_Field', $this->charitable->donation_fields()->get_field( $field ) );
    }

    /**
     * @covers Charitable::campaign_fields()
     */
    public function test_campaign_fields() {
        $this->assertInstanceOf( 'Charitable_Campaign_Field_Registry', $this->charitable->campaign_fields() );
    }

    /**
     * @covers Charitable::campaign_fields()
     * @depends test_campaign_fields
     * @dataProvider campaign_fields
     */
    public function test_campaign_fields_has_registered_fields( $field ) {
        $this->assertInstanceOf( 'Charitable_Field', $this->charitable->campaign_fields()->get_field( $field ) );
    }

    /**
     * @covers Charitable::get_path()
     */
    function test_get_path() {
        $this->assertEquals( $this->directory_path . 'charitable.php', $this->charitable->get_path() ); // __FILE__
        $this->assertEquals( $this->directory_path, $this->charitable->get_path( 'directory' ) );
        $this->assertEquals( $this->directory_url, $this->charitable->get_path( 'directory', false ) );
        $this->assertEquals( $this->directory_path . 'includes/', $this->charitable->get_path( 'includes' ) );
        $this->assertEquals( $this->directory_path . 'includes/admin/', $this->charitable->get_path( 'admin' ) );
        $this->assertEquals( $this->directory_path . 'includes/public/', $this->charitable->get_path( 'public' ) );     
        $this->assertEquals( $this->directory_path . 'assets/', $this->charitable->get_path( 'assets' ) );
        $this->assertEquals( $this->directory_path . 'templates/', $this->charitable->get_path( 'templates' ) );
    }

    /**
     * @covers Charitable::is_activation()
     */
    function test_is_activation() {
        $this->assertFalse( $this->charitable->is_activation() );
    }

    /**
     * @covers Charitable::is_deactivation()
     */
    function test_is_deactivation() {
        $this->assertFalse( $this->charitable->is_deactivation() );
    }

    /**
     * @covers Charitable::endpoints()
     */
    public function test_is_donate_endpoint_added() {
        charitable()->endpoints()->setup_rewrite_rules();
        $this->assertContains( 'donate', $GLOBALS['wp']->public_query_vars );
    }

    /**
     * @covers Charitable::endpoints()
     */
    public function test_is_widget_endpoint_added() {
        charitable()->endpoints()->setup_rewrite_rules();
        $this->assertContains( 'widget', $GLOBALS['wp']->public_query_vars );
    }

    /**
     * @covers Charitable::endpoints()
     */
    public function test_is_donation_receipt_endpoint_added() {
        charitable()->endpoints()->setup_rewrite_rules();
        $this->assertContains( 'donation_receipt', $GLOBALS['wp']->public_query_vars );
    }

    /**
     * @covers Charitable::endpoints()
     */
    public function test_is_donation_processing_endpoint_added() {
        charitable()->endpoints()->setup_rewrite_rules();
        $this->assertContains( 'donation_processing', $GLOBALS['wp']->public_query_vars );
    }

    /**
     * Files.
     */
    public function files() {
        $includes_path = charitable()->get_path( 'includes' );

        return array(
            array( $includes_path . 'charitable-core-functions.php' ),
            array( $includes_path . 'campaigns/charitable-campaign-functions.php' ),
            array( $includes_path . 'campaigns/charitable-campaign-hooks.php' ),
            array( $includes_path . 'compat/charitable-compat-functions.php' ),
            array( $includes_path . 'currency/charitable-currency-functions.php' ),
            array( $includes_path . 'deprecated/charitable-deprecated-functions.php' ),
            array( $includes_path . 'donations/charitable-donation-hooks.php' ),
            array( $includes_path . 'donations/charitable-donation-functions.php' ),
            array( $includes_path . 'emails/charitable-email-hooks.php' ),
            array( $includes_path . 'endpoints/charitable-endpoints-functions.php' ),
            array( $includes_path . 'public/charitable-template-helpers.php' ),
            array( $includes_path . 'shortcodes/charitable-shortcodes-hooks.php' ),
            array( $includes_path . 'upgrades/charitable-upgrade-hooks.php' ),
            array( $includes_path . 'users/charitable-user-functions.php' ),
            array( $includes_path . 'user-management/charitable-user-management-hooks.php' ),
            array( $includes_path . 'utilities/charitable-utility-functions.php' ),
        );
    }

    /**
     * Init classes.
     */
    public function init_classes() {
        return array(
            array( 'Charitable_Emails' ),
            array( 'Charitable_Request' ),
            array( 'Charitable_Gateways' ),
            array( 'Charitable_i18n' ),
            array( 'Charitable_Post_Types' ),
            array( 'Charitable_Cron' ),
            array( 'Charitable_Widgets' ),
            array( 'Charitable_Licenses' ),
            array( 'Charitable_User_Dashboard' ),
            array( 'Charitable_Locations' ),
        );
    }

    /**
     * Donation fields.
     */
    public function donation_fields() {
        $fields = include( charitable()->get_path( 'includes' ) . 'fields/default-fields/donation-fields.php' );

        return array_map( function( $field ) {
            return array( $field );
        }, array_keys( $fields ) );
    }

    /**
     * Campaign fields.
     */
    public function campaign_fields() {
        $fields = include( charitable()->get_path( 'includes' ) . 'fields/default-fields/campaign-fields.php' );

        return array_map( function( $field ) {
            return array( $field );
        }, array_keys( $fields ) );
    }
}