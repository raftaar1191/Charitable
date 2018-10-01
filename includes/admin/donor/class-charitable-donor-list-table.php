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
	 * @since  1.0
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
			 * @since 1.0
			 */
			do_action( 'charitable_donors_table_top' );
			?>

            <hr class="wp-header-end">
            <form id="charitable-donors-search-filter" method="get"
                  action="<?php echo admin_url( 'edit.php?post_type=charitable_forms&page=charitable-donors' ); ?>">
				<?php $donors_table->search_box( __( 'Search Donors', 'charitable' ), 'charitable-donors' ); ?>
                <input type="hidden" name="post_type" value="charitable_forms"/>
                <input type="hidden" name="page" value="charitable-donors"/>
                <input type="hidden" name="view" value="donors"/>
            </form>
            <form id="charitable-donors-filter" method="get">
				<?php $donors_table->display(); ?>
                <input type="hidden" name="post_type" value="charitable_forms"/>
                <input type="hidden" name="page" value="charitable-donors"/>
                <input type="hidden" name="view" value="donors"/>
            </form>
			<?php
			/**
			 * Fires in donors screen, below the table.
			 *
			 * @since 1.0
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
		?>
        <p class="search-box" role="search">
            <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>"/>
			<?php submit_button( $text, 'button', false, false, array(
				'donor_id' => 'search-submit',
			) ); ?>
        </p>
		<?php
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
			default:
				$value = isset( $donor[ $column_name ] ) ? $donor[ $column_name ] : null;
				break;
		}

		return apply_filters( "charitable_donors_column_{$column_name}", $value, $donor['donor_id'] );

	}

	/**
	 * For CheckBox Column
	 *
	 * @param array $donor Donor Data.
	 *
	 * @access public
	 * @since  1.7.0
	 *
	 * @return string
	 */
	public function column_cb( $donor ) {
		return sprintf(
			'<input class="donor-selector" type="checkbox" name="%1$s[]" value="%2$d" data-name="%3$s" />',
			$this->_args['singular'],
			$donor['donor_id'],
			$donor['name']
		);
	}

	/**
	 * Column name.
	 *
	 * @param array $donor Donor Data.
	 *
	 * @access public
	 * @since  1.7.0
	 *
	 * @return string
	 */
	public function column_name( $donor ) {
		$name     = ! empty( $donor['name'] ) ? $donor['name'] : '<em>' . __( 'Unnamed Donor', 'charitable' ) . '</em>';
		$view_url = admin_url( 'edit.php?post_type=charitable_forms&page=charitable-donors&view=overview&id=' . $donor['donor_id'] );
		$actions  = $this->get_row_actions( $donor );

		return '<a href="' . esc_url( $view_url ) . '">' . $name . '</a>' . $this->row_actions( $actions );
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
			'cb'              => '<input type="checkbox" />', // Render a checkbox instead of text.
			'donor_id'        => __( 'Donor ID', 'charitable' ),
			'email'           => __( 'Email', 'charitable' ),
			'name'            => __( 'Name', 'charitable' ),
			'num_donations'   => __( 'Donations', 'charitable' ),
			'amount_spent'    => __( 'Lifetime value', 'charitable' ),
			'date_joined'     => __( 'Date joined', 'charitable' ),
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
			'date_created' => array( 'date_created', true ),
			'name'         => array( 'name', true ),
		);

		return apply_filters( 'charitable_list_donors_sortable_columns', $columns );
	}

	/**
	 * Retrieve row actions.
	 *
	 * @param array $donor Donor Data.
	 *
	 * @since  1.7.0
	 * @access public
	 *
	 * @return array An array of action links.
	 */
	public function get_row_actions( $donor ) {

		$actions = array(
			'view' => sprintf( '<a href="%1$s" aria-label="%2$s">%3$s</a>', admin_url( 'edit.php?post_type=charitable_forms&page=charitable-donors&view=overview&id=' . $donor['donor_id'] ), sprintf( esc_attr__( 'View "%s"', 'charitable' ), $donor['name'] ), __( 'View Donor', 'charitable' ) ),
		);

		return apply_filters( 'charitable_donor_row_actions', $actions, $donor );

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
	 * Get the Bulk Actions.
	 *
	 * @access public
	 * @since  1.7.0
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'charitable' ),
		);

		return $actions;
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
		if ( 'top' === $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'], '_wpnonce', false );
		}
		?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">
			<?php if ( $this->has_items() ) : ?>
                <div class="alignleft actions bulkactions">
					<?php $this->bulk_actions( $which ); ?>
                </div>
			<?php endif;
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

		// Get donor query.
		$args = $this->get_donor_query();

		$donors = new Charitable_Donor_Query( $args );

		if ( $donors ) {

			foreach ( $donors as $donor ) {

				$data[] = array(
					'donor_id'      => $donor->donor_id,
					'user_id'       => $donor->donor_id,
					'name'          => $donor->first_name . ' ' . $donor->last_name,
					'email'         => $donor->email,
					'num_donations' => 'Not yet',
					'amount_spent'  => 'Not yet',
					'date_created'  => $donor->date_joined,
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
		// Get donor query.
		$_donor_query = $this->get_donor_query();

		$_donor_query['number'] = - 1;
		$_donor_query['offset'] = 0;
		$donors                 = new Charitable_Donor_Query( $_donor_query );

		return count( $donors );
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

		$get_data = sanitize_text_field( $_GET ); // WPCS: input var ok, sanitization ok, CSRF ok.

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
            <tr class="hidden"></tr>
            <tr id="charitable-bulk-delete"
                class="inline-edit-row inline-edit-row-page inline-edit-page bulk-edit-row bulk-edit-row-page bulk-edit-page inline-editor"
                style="display: none;">
                <td colspan="6" class="colspanchange">

                    <fieldset class="inline-edit-col-left">
                        <legend class="inline-edit-legend"><?php esc_attr_e( 'BULK DELETE', 'charitable' ); ?></legend>
                        <div class="inline-edit-col">
                            <div id="bulk-titles">
                                <div id="charitable-bulk-donors" class="charitable-bulk-donors">

                                </div>
                            </div>
                    </fieldset>

                    <fieldset class="inline-edit-col-right">
                        <div class="inline-edit-col">
                            <label>
                                <input class="charitable-donor-delete-confirm" type="checkbox"
                                       name="charitable-donor-delete-confirm"/>
								<?php esc_attr_e( 'Are you sure you want to delete the selected donor(s)?', 'charitable' ); ?>
                            </label>
                            <label>
                                <input class="charitable-donor-delete-records" type="checkbox"
                                       name="charitable-donor-delete-records"/>
								<?php esc_attr_e( 'Delete all associated donations and records?', 'charitable' ); ?>
                            </label>
                        </div>
                    </fieldset>

                    <p class="submit inline-edit-save">
                        <input type="hidden" name="charitable_action" value="delete_bulk_donor"/>
                        <input type="hidden" name="s" value="<?php echo esc_html( $search_keyword ); ?>"/>
                        <input type="hidden" name="orderby" value="<?php echo esc_html( $order_by ); ?>"/>
                        <input type="hidden" name="order" value="<?php echo esc_html( $order ); ?>"/>
                        <button type="button" id="charitable-bulk-delete-cancel"
                                class="button cancel alignleft"><?php esc_attr_e( 'Cancel', 'charitable' ); ?></button>
                        <input type="submit" id="charitable-bulk-delete-button" disabled
                               class="button button-primary alignright"
                               value="<?php esc_attr_e( 'Delete', 'charitable' ); ?>">
                        <br class="clear">
                    </p>
                </td>
            </tr>
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
