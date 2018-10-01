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
	 * @var int
	 * @since 1.7.0
	 */
	public $per_page = 30;

	/**
	 * Number of donors found.
	 *
	 * @var int
	 * @since 1.7.0
	 */
	public $count = 0;

	/**
	 * Total donors.
	 *
	 * @var int
	 * @since 1.7.0
	 */
	public $total = 0;

	/**
	 * Contain the donors list
	 *
	 * @since 1.7.1
	 *
	 * @var array
	 */
	public $donors = array();

	/**
	 * Get things started.
	 *
	 * @since 1.7.0
	 * @see   WP_List_Table::__construct()
	 */
	public function __construct() {

		// Set parent defaults.
		parent::__construct( array(
			'singular' => __( 'Donor', 'charitable' ), // Singular name of the listed records.
			'plural'   => __( 'Donors', 'charitable' ), // Plural name of the listed records.
			'ajax'     => false, // Does this table support ajax?.
		) );

	}

	/**
	 * List table of donors.
	 *
	 * @since  1.7.0
	 * @return void
	 */
	static function donors_list() {

		$donors_table = new charitable_Donor_List_Table();
		$donors_table->prepare_items();
		?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php echo get_admin_page_title(); ?></h1>
			<?php
			/**
			 * Fires in donors screen, above the table.
			 *
			 * @since 1.7.0
			 */
			do_action( 'charitable_donors_table_top' );
			?>

            <hr class="wp-header-end">
            <form id="charitable-donors-filter" method="get">
				<?php $donors_table->display(); ?>
                <input type="hidden" name="page" value="donors"/>
            </form>
			<?php
			/**
			 * Fires in donors screen, below the table.
			 *
			 * @since r
			 */
			do_action( 'charitable_donors_table_bottom' );
			?>
        </div>
		<?php
	}

	/**
	 * Show the search field.
	 *
	 * @param string $text Label for the search box.
	 * @param string $input_id ID of the search box.
	 *
	 * @since  1.7.0
	 * @access public
	 *
	 * @return void
	 */
	public function search_box( $text, $input_id ) {
		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo sprintf( '<input type="hidden" name="orderby" value="%1$s" />', esc_attr( $_REQUEST['orderby'] ) );
		}

		if ( ! empty( $_REQUEST['order'] ) ) {
			echo sprintf( '<input type="hidden" name="order" value="%1$s" />', esc_attr( $_REQUEST['order'] ) );
		}
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param array $donor Contains all the data of the donors.
	 * @param string $column_name The name of the column.
	 *
	 * @access public
	 * @since  1.7.0
	 *
	 * @return string Column Name.
	 */
	public function column_default( $donor, $column_name ) {

		switch ( $column_name ) {
			case 'donations' :
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

		return apply_filters( "charitable_donors_column_{$column_name}", $value, $donor, $column_name );
	}

	/**
	 * Retrieve the table columns.
	 *
	 * @access public
	 * @since  1.7.0
	 *
	 * @return array $columns Array of all the list table columns.
	 */
	public function get_columns() {
		$columns = array(
			'donor_id'        => __( 'Donor ID', 'charitable' ),
			'name'            => __( 'Name', 'charitable' ),
			'email'           => __( 'Email', 'charitable' ),
			'donations'       => __( 'Donations', 'charitable' ),
			'amount'          => __( 'Lifetime value', 'charitable' ),
			'date'            => __( 'Date joined', 'charitable' ),
			'contact_consent' => __( 'Contact consent', 'charitable' ),
		);

		return apply_filters( 'charitable_list_donors_columns', $columns );

	}

	/**
	 * Get the sortable columns.
	 *
	 * @access public
	 * @since  1.7.0
	 * @return array Array of all the sortable columns.
	 */
	public function get_sortable_columns() {
		$columns = array(
			'donor_id'  => array( 'donor_id', true ),
			'name'      => array( 'name', true ),
			'donations' => array( 'donations', true ),
			'amount'    => array( 'amount', true ),
			'date'      => array( 'date', true ),
		);

		return apply_filters( 'charitable_list_donors_sortable_columns', $columns );
	}

	/**
	 * Retrieve the current page number.
	 *
	 * @access public
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
	 * @access public
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
	 * @access protected
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
	 * @access public
	 * @since  1.7.0
	 *
	 * @return array $data The Donor data.
	 */
	public function donor_data() {

		$data = array();

		if ( empty( $this->donors ) ) {
			// Get donor query.
			$args         = $this->get_donor_query();
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

		return apply_filters( 'charitable_donors_column_query_data', $data );
	}

	/**
	 * Get donor count.
	 *
	 * @since  1.7.0
	 * @access private
	 */
	private function get_donor_count() {

		if ( empty( $this->donors ) ) {

			// Get donor query.
			$_donor_query = $this->get_donor_query();

			$_donor_query['number'] = - 1;

			$this->donors = new Charitable_Donor_Query( $_donor_query );
		}

		$count = 0;
		foreach ( $this->donors as $donor ) {
			$count ++;
		}

		return $count;
	}

	/**
	 * Get donor query.
	 *
	 * @since  1.7.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_donor_query() {
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
				$args['name'] = $search;
			}
		}

		return $args;
	}

	/**
	 * Generates content for a single row of the table
	 *
	 * @param object $item The current item.
	 *
	 * @since  1.7.0
	 * @access public
	 */
	public function single_row( $item ) {
		echo sprintf( '<tr id="donor-%1$d" data-id="%2$d" data-name="%3$s">', $item['donor_id'], $item['donor_id'], $item['name'] );
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Display the final donor table
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function display() {
		$singular = $this->_args['singular'];

		$this->display_tablenav( 'top' );

		$this->screen->render_screen_reader_content( 'heading_list' );

		$get_data = $_GET; // WPCS: input var ok, sanitization ok, CSRF ok.

		$search_keyword = ! empty( $get_data['s'] ) ? $get_data['s'] : '';
		$order          = ! empty( $get_data['order'] ) ? $get_data['order'] : 'DESC';
		$order_by       = ! empty( $get_data['orderby'] ) ? $get_data['orderby'] : 'donor_id';
		?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
            <thead>
            <tr>
				<?php $this->print_column_headers(); ?>
            </tr>
            </thead>

            <tbody id="the-list"<?php
			if ( $singular ) {
				echo " data-wp-lists='list:$singular'";
			} ?>>
			<?php $this->display_rows_or_placeholder(); ?>
            </tbody>

            <tfoot>
            <tr>
				<?php $this->print_column_headers( false ); ?>
            </tr>
            </tfoot>

        </table>
		<?php
		$this->display_tablenav( 'bottom' );
	}

	/**
	 * Setup the final data for the table.
	 *
	 * @access public
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
