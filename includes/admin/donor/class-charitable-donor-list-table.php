<?php
/**
 * Sets up the campaign list table in the admin.
 *
 * @package   Charitable/Classes/Charitable_Donor_List_Table
 * @version   1.7.0
 * @author    Deepak Gupta
 * @copyright Copyright (c) 2018, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load WP_List_Table if not loaded.
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Charitable_Donor_List_Table Class.
 *
 * @since 1.7.0
 */
class Charitable_Donor_List_Table extends WP_List_Table {

	/**
	 * Number of items per page.
	 *
	 * @since 1.7.0
	 *
	 * @var   int
	 */
	public $per_page = 20;

	/**
	 * Number of donors found.
	 *
	 * @since 1.7.0
	 *
	 * @var   int
	 */
	public $count = 0;

	/**
	 * Total donors.
	 *
	 * @since 1.7.0
	 *
	 * @var   int
	 */
	public $total = 0;

	/**
	 * Contain the donors list
	 *
	 * @since 1.7.0
	 *
	 * @var   array
	 */
	public $donors = array();

	/**
	 * Get things started.
	 *
	 * @since 1.7.0
	 *
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'Donor', 'charitable' ), // Singular name of the listed records.
				'plural'   => __( 'Donors', 'charitable' ), // Plural name of the listed records.
				'ajax'     => false, // Does this table support ajax?.
			)
		);
	}

	/**
	 * List table of donors.
	 *
	 * @since  1.7.0
	 *
	 * @return void
	 */
	static function donors_list() {
		charitable_admin_view( 'donors-page/list' );
	}

	/**
	 * Show the search field.
	 *
	 * @since  1.7.0
	 *
	 * @param  string $text     Label for the search box.
	 * @param  string $input_id ID of the search box.
	 * @return void
	 */
	public function search_box( $text, $input_id ) {
		charitable_admin_view( 'donors-page/search' );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @since  1.7.0
	 *
	 * @param  array  $donor       Contains all the data of the donors.
	 * @param  string $column_name The name of the column.
	 * @return string Column Name.
	 */
	public function column_default( $donor, $column_name ) {
		switch ( $column_name ) {
			case 'donations':
				$value = sprintf(
					'<a href="%s">%s</a>',
					admin_url( 'edit.php?post_type=donation&donor_id=' . absint( $donor['donor_id'] ) ),
					esc_html( $donor['donations'] )
				);
				break;

			default:
				$value = isset( $donor[ $column_name ] ) ? $donor[ $column_name ] : null;
				break;
		}

		/**
		 * Filter the value to display in a particular donor table column.
		 *
		 * @since 1.7.0
		 *
		 * @param string $value The default value to show.
		 * @param array  $donor Contains all the donor data.
		 */
		return apply_filters( 'charitable_donors_column_' . $column_name, $value, $donor );
	}

	/**
	 * Retrieve the table columns.
	 *
	 * @since  1.7.0
	 *
	 * @return array $columns Array of all the list table columns.
	 */
	public function get_columns() {
		/**
		 * Filter list of columns in Donors table.
		 *
		 * @since 1.7.0
		 *
		 * @param array $columns The list of columns.
		 */
		return apply_filters(
			'charitable_list_donors_columns',
			array(
				'donor_id'        => __( 'Donor ID', 'charitable' ),
				'name'            => __( 'Name', 'charitable' ),
				'email'           => __( 'Email', 'charitable' ),
				'donations'       => __( 'Donations', 'charitable' ),
				'amount'          => __( 'Lifetime value', 'charitable' ),
				'date'            => __( 'Date joined', 'charitable' ),
				'contact_consent' => __( 'Contact consent', 'charitable' ),
			)
		);
	}

	/**
	 * Get the sortable columns.
	 *
	 * @since  1.7.0
	 *
	 * @return array Array of all the sortable columns.
	 */
	public function get_sortable_columns() {
		/**
		 * Filter the list of sortable columns in the Donors table.
		 *
		 * @param array $columns The list of columns.
		 */
		return apply_filters(
			'charitable_list_donors_sortable_columns',
			array(
				'donor_id'  => array( 'donor_id', true ),
				'name'      => array( 'name', true ),
				'donations' => array( 'donations', true ),
				'amount'    => array( 'amount', true ),
				'date'      => array( 'date', true ),
			)
		);
	}

	/**
	 * Retrieve the current page number.
	 *
	 * @since  1.7.0
	 *
	 * @return int Current page number.
	 */
	public function get_paged() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}

	/**
	 * Retrieves the search query string.
	 *
	 * @since  1.7.0
	 *
	 * @return mixed string If search is present, false otherwise.
	 */
	public function get_search() {
		return ! empty( $_GET['s'] ) ? urldecode( trim( $_GET['s'] ) ) : false;
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @param string $which Position to trigger i.e. Top/Bottom.
	 *
	 * @since  1.7.0
	 */
	protected function display_tablenav( $which ) {
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>
			<br class="clear"/>
		</div>
		<?php
	}

	/**
	 * Retrieves the donor data from db.
	 *
	 * @since  1.7.0
	 *
	 * @return array $data The Donor data.
	 */
	public function donor_data() {
		$data = array();

		if ( empty( $this->donors ) ) {
			$args         = $this->get_donor_query_args();
			$this->donors = new Charitable_Donor_Query( $args );
		}

		if ( $this->donors ) {
			foreach ( $this->donors as $donor ) {

				$charitable_donor = new Charitable_Donor( $donor->donor_id );

				$data[] = array(
					'donor_id'        => $charitable_donor->donor_id,
					'user_id'         => $donor->user_id,
					'name'            => $charitable_donor->get_name(),
					'email'           => $charitable_donor->get_email(),
					'donations'       => $charitable_donor->count_donations(),
					'amount'          => $charitable_donor->get_amount(),
					'date'            => $donor->date_joined,
					'contact_consent' => $donor->contact_consent,
				);
			}
		}

		/**
		 * Filter the query data to include for a particular donor.
		 *
		 * @since 1.7.0
		 *
		 * @param array $data The data relating to a particular donor.
		 */
		return apply_filters( 'charitable_donors_column_query_data', $data );
	}

	/**
	 * Get donor count.
	 *
	 * @since  1.7.0
	 */
	private function get_donor_count() {

		// Get donor query.
		$_donor_query = $this->get_donor_query_args();

		$_donor_query['number'] = - 1;

		$query = new Charitable_Donor_Query( $_donor_query );
		return $query->count();
	}

	/**
	 * Get donor query.
	 *
	 * @since  1.7.0
	 *
	 * @return array
	 */
	public function get_donor_query_args() {
		$paged   = $this->get_paged();
		$offset  = $this->per_page * ( $paged - 1 );
		$search  = $this->get_search();
		$order   = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'DESC';
		$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'donor_id';

		$args = array(
			'number'  => $this->per_page,
			'offset'  => $offset,
			'order'   => $order,
			'orderby' => $orderby,
		);

		if ( $search ) {
			if ( is_email( $search ) ) {
				$args['email'] = $search;
			} elseif ( is_numeric( $search ) ) {
				$args['donor_id'] = $search;
			} else {
				$args['s'] = $search;
			}
		}

		return $args;
	}

	/**
	 * Setup the final data for the table.
	 *
	 * @since  1.7.0
	 *
	 * @return void
	 */
	public function prepare_items() {

		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns.
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = $this->donor_data();

		$this->total = $this->get_donor_count();

		$this->set_pagination_args( array(
			'total_items' => $this->total,
			'per_page'    => $this->per_page,
			'total_pages' => ceil( $this->total / $this->per_page ),
		) );
	}
}
