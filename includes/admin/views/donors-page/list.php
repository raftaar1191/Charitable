<?php
$donors_table = new Charitable_Donor_List_Table();
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
        <form id="charitable-donors-search-filter" method="get"
              action="<?php echo admin_url( 'admin.php?page=donors' ); ?>">
			<?php $donors_table->search_box( __( 'Search Donors', 'charitable' ), 'charitable-donors' ); ?>
            <input type="hidden" name="page" value="donors"/>
        </form>
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