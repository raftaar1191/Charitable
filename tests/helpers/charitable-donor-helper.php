<?php
/**
 * Class Charitable_Donor_Helper
 *
 * Helper class to create and delete a donor easily.
 */
class Charitable_Donor_Helper extends WP_UnitTestCase {

	/**
	 * Delete a donor 
	 *
	 * @since   1.0.0
	 *
	 * @return 	void
	 */
	public function delete_donor( $donor_id ) {
		wp_delete_user( $donor_id );
	}

	/**
	 * Create a donor.
	 *
	 * @since   1.0.0	 
	 *
	 * @param 	array 		$args 				Optional arguments.
	 * @return 	int 		$donor_id
	 */
	public static function create_donor( $args = array() ) {
		$defaults = array(
			'user_email'	=> 'johndoe@example.com', 
			'user_login'	=> 'johndoe',
			'first_name'	=> 'John',	
			'last_name'		=> 'Doe',
			'address'		=> 'Unit A',
			'address_2'		=> '164 Studio Street',		
			'city'			=> 'Melbourne',
			'state'			=> 'VIC',		
			'postcode'		=> '3000',
			'country'		=> 'AU',
			'phone'			=> '0390009000',
			'user_pass'     => 'apple',
		);	

		$args = array_merge( $defaults, $args );

		$user = new Charitable_User();
		$user->update_profile( $args );
		return $user->ID;
	}

	/**
	 * Create a donor with a name.  
	 *
	 * @since   1.0.0
	 *
	 * @param 	string 		$first_name
	 * @param 	string 		$last_name
	 * @param 	array 		$args
	 * @return 	int
	 */
	public static function create_named_donor( $first_name, $last_name, $args = array() ) {
		$args['first_name'] = $first_name;
		$args['last_name'] = $last_name;
		$args['user_email'] = $first_name . $last_name . '@example.com';
		return self::create_donor( $args );
	}
}