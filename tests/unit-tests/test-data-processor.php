<?php

class Test_Charitable_Data_Processor extends Charitable_UnitTestCase {

	/**
	 * @dataProvider data_single_tests
	 * @covers Charitable_Data_Processor::__construct
	 * @covers Charitable_Data_Processor::process_data
	 * @covers Charitable_Data_Processor::set_output
	 * @covers Charitable_Data_Processor::process_field
	 * @covers Charitable_Data_Processor::process_checkbox
	 * @covers Charitable_Data_Processor::get
	 */
	public function test_output_values( $field, $value ) {
		$processor = new Charitable_Data_Processor( $this->data_single(), $this->fields_single() );

		$this->assertEquals(
			$value,
			$processor->get( $field )
		);
	}

	/**
	 * @dataProvider data_multidimensional_tests
	 * @covers Charitable_Data_Processor::__construct
	 * @covers Charitable_Data_Processor::process_data
	 * @covers Charitable_Data_Processor::set_output
	 * @covers Charitable_Data_Processor::process_field
	 * @covers Charitable_Data_Processor::process_checkbox
	 * @covers Charitable_Data_Processor::get
	 * @covers Charitable_Data_Processor::get_from_data_type
	 */
	public function test_output_values_with_data_type( $field, $value, $data_type ) {
		$processor = new Charitable_Data_Processor( $this->data_multidimensional(), $this->fields_multidimensional() );

		$this->assertEquals(
			$value,
			$processor->get( $field, $data_type )
		);
	}

	/**
	 * @covers Charitable_Data_Processor::__construct
	 * @covers Charitable_Data_Processor::process_data
	 * @covers Charitable_Data_Processor::set_output
	 * @covers Charitable_Data_Processor::process_field
	 * @covers Charitable_Data_Processor::process_checkbox
	 * @covers Charitable_Data_Processor::output
	 */
	public function test_output_array_count() {
		$processor = new Charitable_Data_Processor( $this->data_single(), $this->fields_single() );

		$this->assertCount(
			4,
			$processor->output()
		);
	}

	/**
	 * @covers Charitable_Data_Processor::__construct
	 * @covers Charitable_Data_Processor::process_data
	 * @covers Charitable_Data_Processor::set_output
	 * @covers Charitable_Data_Processor::process_field
	 * @covers Charitable_Data_Processor::process_checkbox
	 * @covers Charitable_Data_Processor::output
	 */
	public function test_output_multidimensional_array_count() {
		$processor = new Charitable_Data_Processor( $this->data_multidimensional(), $this->fields_multidimensional() );

		$this->assertCount(
			2,
			$processor->output()
		);
	}

	/**
	 * The data provider for the test.
	 */
	public function data_single_tests() {
		return array(
			array( 'checkbox_checked', true ),
			array( 'checkbox_unchecked', false ),
			array( 'non_existent', null ),
			array( 'text_field', 'Lorem Ipsum' ),
			array( 'number_field', 150 ),
		);
	}

	/**
	 * The set of data for the processor.
	 */
	private function data_single() {
		return array(
			'checkbox_checked' => 1,
			'text_field'       => 'Lorem Ipsum',
			'number_field'     => '150',
		);
	}

	/**
	 * The set of fields for the processor.
	 */
	public function fields_single() {
		return array(
			'checkbox_checked'   => 'checkbox',
			'checkbox_unchecked' => 'checkbox',
			'text_field'         => 'text',
			'number_field'       => 'number',
		);
	}

	/**
	 * The data provider for the test.
	 */
	public function data_multidimensional_tests() {
		return array(
			array( 'checkbox_checked', true, 'data_type_1' ),
			array( 'checkbox_unchecked', false, 'data_type_1' ),
			array( 'non_existent', null, 'data_type_1' ),
			array( 'text_field', 'Lorem Ipsum', 'data_type_1' ),
			array( 'number_field', 150, 'data_type_1' ),
			array( 'new_text_field', 'Hello World', 'data_type_2' ),
			array( 'text_field', null, 'data_type_2' ), // Incorrect data type, should return null.
		);
	}

	/**
	 * The set of data for the processor.
	 */
	private function data_multidimensional() {
		return array(
			'checkbox_checked' => 1,
			'text_field'       => 'Lorem Ipsum',
			'number_field'     => '150',
			'new_text_field'   => 'Hello World',
		);
	}

	/**
	 * The set of fields for the processor.
	 */
	public function fields_multidimensional() {
		return array(
			'data_type_1' => array(
				'checkbox_checked'   => 'checkbox',
				'checkbox_unchecked' => 'checkbox',
				'text_field'         => 'text',
				'number_field'       => 'number',
			),
			'data_type_2' => array(
				'new_text_field'     => 'text',
			),
		);
	}
}