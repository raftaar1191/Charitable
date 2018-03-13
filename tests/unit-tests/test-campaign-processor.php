<?php

class Test_Charitable_Campaign_Processor_Class extends Charitable_UnitTestCase {

    function setUp() {
        parent::setUp();
    }

    /**
     * @covers Charitable_Campaign_Processor::__construct()
     */
    function test_create_campaign_processor_object() {
        // Set the current user to 1.
        wp_set_current_user( 1 );
        $processor = new Charitable_Campaign_Processor();
        $this->assertInstanceOf( 'Charitable_Campaign_Processor', $processor );
    }

    /**
     * @covers Charitable_Campaign_Processor::is_new_campaign()
     */
    function test_is_new_campaign() {
        $processor = new Charitable_Campaign_Processor();
        $this->assertTrue( $processor->is_new_campaign() );
    }

    /**
     * @covers Charitable_Campaign_Processor::save()
     */
    function test_save_without_content() {
        $processor = new Charitable_Campaign_Processor();
        $this->assertGreaterThan( 0, $processor->save() );
    }

    /**
     * @depends test_is_new_campaign
     * @covers Charitable_Campaign_Processor::save()
     */
    function test_save() {
        $processor = new Charitable_Campaign_Processor( array(
            'title' => 'Test Campaign',
        ) );
        $this->assertGreaterThan( 0, $processor->save() );
    }

    /**
     * @depends test_save
     * @covers ::charitable_create_campaign
     */
    function test_charitable_create_campaign() {
        $campaign_id = charitable_create_campaign( array(
            'title' => 'Test Campaign',
        ) );
        $this->assertGreaterThan( 0, $campaign_id );
    }

    /**
     * @depends test_save
     * @covers Charitable_Campaign_Processor::parse_args
     * @dataProvider default_campaign_args
     */
    function test_campaign_processor_default_args( $key, $value ) {
        wp_set_current_user( 1 );
        $processor = new Charitable_Campaign_Processor();
        $this->assertEquals( $value, $processor->get( $key ) );        
    }

    /**     
     * @depends test_campaign_processor_default_args
     * @covers Charitable_Campaign_Processor::parse_args
     * @covers Charitable_Campaign_Processor::set_initial_arg
     * @covers Charitable_Campaign_Processor::set
     * @covers Charitable_Campaign_Processor::get
     * @dataProvider custom_campaign_args
     */
    function test_campaign_processor_custom_args_at_instantiation( $key, $value ) {
        // Transform the custom args into a key=>value array we can pass to the object.
        $custom_args = $this->custom_campaign_args();
        $args = array_combine( wp_list_pluck( $custom_args, 0 ), wp_list_pluck( $custom_args, 1 ) );
        $processor = new Charitable_Campaign_Processor( $args );
        $this->assertEquals( $value, $processor->get( $key ) );
    }

    /**     
     * @depends test_campaign_processor_default_args
     * @covers Charitable_Campaign_Processor::parse_args
     * @covers Charitable_Campaign_Processor::set_initial_arg
     * @covers Charitable_Campaign_Processor::set
     * @covers Charitable_Campaign_Processor::get
     * @dataProvider custom_campaign_args
     */
    function test_campaign_processor_disable_custom_donations() {
        $processor = new Charitable_Campaign_Processor( array(
            'title' => 'Test Campaign',
            'suggested_donations' => array( array(
                'amount' => 5,
                'description' => 'gold'
            ) ),
            'allow_custom_donations' => 1,
        ) );

        $this->assertEquals(
            1, 
            $processor->get( 'allow_custom_donations' )
        );
    }

    /**
     * @depends test_campaign_processor_custom_args_at_instantiation
     * @covers Charitable_Campaign_Processor::parse_args
     * @covers Charitable_Campaign_Processor::set
     * @covers Charitable_Campaign_Processor::get
     * @dataProvider custom_campaign_args
     */
    function test_campaign_processor_custom_args_deferred( $key, $value ) {
        $processor = new Charitable_Campaign_Processor();

        foreach ( $this->custom_campaign_args() as $arg ) {
            $processor->set( $arg[0], $arg[1] );
        }

        $this->assertEquals( $value, $processor->get( $key ) );
    }

    /**
     * @depends test_save
     * @covers Charitable_Campaign_Processor::save
     */
    function test_save_campaign_without_args() {
        $processor = new Charitable_Campaign_Processor();
        $this->assertGreaterThan( 0, $processor->save() );
    }
    
    /**
     * @depends test_save
     * @covers Charitable_Campaign_Processor::save
     */
    function test_save_campaign_with_title() {
        $processor = new Charitable_Campaign_Processor( array(
            'title' => 'Test Campaign',
        ) );
        $this->assertGreaterThan( 0, $processor->save() );
    }

    /**
     * @depends test_save
     * @covers Charitable_Campaign_Processor::save
     */
    function test_default_campaign_goal() {
        $processor = new Charitable_Campaign_Processor( array(
            'title' => 'Test Campaign',
        ) );
        $campaign = charitable_get_campaign( $processor->save() );
        $this->assertFalse( $campaign->get_goal() );
    }

    /**
     * @depends test_save
     * @covers Charitable_Campaign_Processor::save
     */
    function test_default_campaign_end_date() {
        $processor = new Charitable_Campaign_Processor( array(
            'title' => 'Test Campaign',
        ) );
        $campaign = charitable_get_campaign( $processor->save() );
        $this->assertTrue( $campaign->is_endless() );
    }

    /**
     * @depends test_save
     * @covers Charitable_Campaign_Processor::save
     */
    function test_default_campaign_suggested_donations() {
        $processor = new Charitable_Campaign_Processor( array(
            'title' => 'Test Campaign',
        ) );
        $campaign = charitable_get_campaign( $processor->save() );
        $this->assertCount( 0, $campaign->get_suggested_donations() );
    }

    /**
     * @depends test_save
     * @covers Charitable_Campaign_Processor::save
     */
    function test_default_campaign_custom_donations() {
        $processor = new Charitable_Campaign_Processor( array(
            'title' => 'Test Campaign',
        ) );
        $campaign = charitable_get_campaign( $processor->save() );
        $this->assertEquals( '1', $campaign->get( 'allow_custom_donations' ) );
    }

    /**
     * @depends test_save
     * @covers Charitable_Campaign_Processor::save
     */
    function test_save_campaign_with_goal() {
        $processor   = new Charitable_Campaign_Processor( array(
            'title' => 'Test Campaign',
            'goal' => 500,
        ) );
        $campaign_id = $processor->save();
        $this->assertEquals( 500, get_post_meta( $campaign_id, '_campaign_goal', true ) );
    }

    /**
     * @depends test_save
     * @covers Charitable_Campaign_Processor::save
     */
    function test_save_campaign_with_end_date() {
        $processor   = new Charitable_Campaign_Processor( array(
            'title' => 'Test Campaign',
            'end_date' => '2017-11-11',
        ) );
        $campaign_id = $processor->save();
        $this->assertEquals( '2017-11-11 23:59:59', get_post_meta( $campaign_id, '_campaign_end_date', true ) );
    }

    /**
     * @depends test_save
     * @covers Charitable_Campaign_Processor::save
     */
    function test_save_campaign_with_category() {
        $processor   = new Charitable_Campaign_Processor( array(
            'title' => 'Test Campaign',
            'categories' => 'campaign-category-1',
        ) );

        $campaign_id = $processor->save();

        $this->assertContains(
            'campaign-category-1',
            wp_get_object_terms( $campaign_id, 'campaign_category', array( 'fields' => 'slugs' ) )
        );
    }

    /**
     * @covers Charitable_Campaign_Processor::sanitize
     * @covers Charitable_Campaign_Processor::save
     * @covers Charitable_Campaign_Processor::save_core
     * @covers Charitable_Campaign_Processor::save_taxonomies
     */
    function test_save_campaign_with_single_category() {
        $processor = new Charitable_Campaign_Processor( array(
            'title' => 'Test Campaign',
            'categories' => array( 'campaign-category-1' ),
        ) );
        
        $campaign_id = $processor->save();

        $this->assertCount(
            1,
            wp_get_object_terms( $campaign_id, 'campaign_category', array( 'fields' => 'slugs' ) )
        );
    }
    
    /**
     * @covers Charitable_Campaign_Processor::sanitize
     * @covers Charitable_Campaign_Processor::save
     * @covers Charitable_Campaign_Processor::save_core
     * @covers Charitable_Campaign_Processor::save_taxonomies
     */
    function test_save_campaign_with_multiple_categories() {
        $processor = new Charitable_Campaign_Processor( array(
            'title' => 'Test Campaign',
            'categories' => array( 'campaign-category-1', 'campaign-category-2', 'campaign-category-3' ),
        ) );
        
        $campaign_id = $processor->save();

        $this->assertCount(
            3,
            wp_get_object_terms( $campaign_id, 'campaign_category', array( 'fields' => 'slugs' ) )
        );
    }

    /**
     * DATA PROVIDERS
     */
    public function default_campaign_args() {
        return array(
            array( 'ID', 0 ),
            array( 'title', '' ),
            array( 'content', '' ),
            array( 'creator', 1 ),
            array( 'status', 'publish' ),
            array( 'goal', 0 ),
            array( 'end_date', 0 ),
            array( 'suggested_donations', array() ),
            array( 'allow_custom_donations', 1 ),
            array( 'categories', array() ),
            array( 'tags', array() ),
        );
    }

    public function custom_campaign_args() {
        return array(
            'ID'                     => array( 'ID', 123 ),
            'title'                  => array( 'title', 'Test Title' ),
            'content'                => array( 'content', 'Test Content' ),
            'creator'                => array( 'creator', 1 ),
            'status'                 => array( 'status', 'pending' ),
            'goal'                   => array( 'goal', 500 ),
            'end_date'               => array( 'end_date', '2017-11-11 23:59:59' ),            
            'categories'             => array( 'categories', array( 'campaign-category-1' ) ),
            'tags'                   => array( 'tags', array( 'campaign-tag-1', 'campaign-tag-2' ) ),
             'suggested_donations'    => array( 'suggested_donations', array(
                array( 'amount' => '5' ),
                array( 'amount' => '20' ),
                array( 'amount' => '50' ),
                array( 'amount' => '100' ),
                array( 'amount' => '250' ),
            ) ),
            'allow_custom_donations' => array( 'allow_custom_donations', 0 ),
        );
    }    
}