<?php
class Test_Charitable_Campaign_Field_Registry extends Charitable_UnitTestCase {
	
    /**
     * @covers Charitable_Field_Registry::__construct
     */
    public function test_construct() {
        $registry = new Charitable_Campaign_Field_Registry();
        $this->assertInstanceOf( 'Charitable_Campaign_Field_Registry', $registry );
    }

    /**
     * @depends test_construct
     * @covers Charitable_Campaign_Field_Registry::register_field
     */
    public function test_register_field() {
        $registry = new Charitable_Campaign_Field_Registry();
        $this->assertTrue( $registry->register_field( new Charitable_Campaign_Field( 'field' ) ) );
    }

    /**
     * @depends test_register_field
     * @covers Charitable_Campaign_Field_Registry::register_field
     */
    public function test_register_invalid_field() {
        $registry = new Charitable_Campaign_Field_Registry();
        $this->assertFalse( $registry->register_field( new Charitable_Donation_Field( 'field' ) ) );
    }

    /**
     * @depends test_register_field
     * @covers Charitable_Field_Registry::get_fields
     * @covers Charitable_Campaign_Field_Registry::register_field
     */
    public function test_get_fields() {
        $registry = new Charitable_Campaign_Field_Registry();
        $registry->register_field( new Charitable_Campaign_Field( 'field' ) );
        $this->assertCount( 1, $registry->get_fields() );
    }

    /**
     * @depends test_register_field
     * @covers Charitable_Field_Registry::get_field
     * @covers Charitable_Campaign_Field_Registry::register_field
     */
    public function test_get_field() {
        $registry = new Charitable_Campaign_Field_Registry();
        $registry->register_field( new Charitable_Campaign_Field( 'field' ) );
        $this->assertInstanceOf( 'Charitable_Campaign_Field', $registry->get_field( 'field' ) );
    }
}