<?php
/**
 * Class that is responsible for generating a CSV export of campaigns.
 *
 * @package   Charitable/Classes/Charitable_Export_Campaigns
 * @author    Eric Daams
 * @copyright Copyright (c) 2018, Studio 164a
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.6.0
 * @version   1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Charitable_Export_Campaigns' ) ) :

	/* Include Charitable_Export base class. */
	if ( ! class_exists( 'Charitable_Export' ) ) {
		require_once( charitable()->get_path( 'includes' ) . 'abstracts/abstract-class-charitable-export.php' );
	}

	/**
	 * Charitable_Export_Campaigns
	 *
	 * @since 1.6.0
	 */
	class Charitable_Export_Campaigns extends Charitable_Export {

		/* The type of export. */
		const EXPORT_TYPE = 'campaigns';

		/**
		 * Default arguments.
		 *
		 * @since 1.6.0
		 *
		 * @var   mixed[]
		 */
		protected $defaults;

		/**
		 * List of donation statuses.
		 *
		 * @since 1.6.0
		 *
		 * @var   string[]
		 */
		protected $statuses;

		/**
		 * Exportable donation fields.
		 *
		 * @since 1.6.0
		 *
		 * @var   array
		 */
		protected $fields;

		/**
		 * Set Charitable_Campaign objects.
		 *
		 * @since 1.6.0
		 *
		 * @var   Charitable_Campaign[]
		 */
		protected $campaigns = array();

		/**
		 * Set Charitable_User objects.
		 *
		 * @since 1.6.0
		 *
		 * @var   Charitable_User[]
		 */
		protected $creators = array();

		/**
		 * Create class object.
		 *
		 * @since 1.6.0
		 *
		 * @param mixed[] $args Arguments for the report.
		 */
		public function __construct( $args ) {
			$this->defaults = array(
				'start_date'  => '',
				'end_date'    => '',
				'status'      => '',
			);

			add_filter( 'charitable_export_data_key_value', array( $this, 'set_custom_field_data' ), 10, 3 );

			parent::__construct( $args );
		}

		/**
		 * Filter the cell values.
		 *
		 * @since  1.6.0
		 *
		 * @param  mixed  $value       The value to set.
		 * @param  string $key         The key to set.
		 * @param  int    $campaign_id The campaign ID.
		 * @return mixed
		 */
		public function set_custom_field_data( $value, $key, $campaign_id ) {
			switch ( $key ) {
				case 'campaign_id':
					$value = $campaign_id;
					break;

				case 'campaign_name':
					$value = get_the_title( $campaign_id );
					break;

				case 'post_date':
					$value = get_post_field( 'post_date', $campaign_id );
					break;

				case 'end_date':
					$value = $this->get_campaign( $campaign_id )->get_end_date();
					break;

				case 'goal':
					$value = $this->get_campaign( $campaign_id )->get_goal();
					break;

				case 'amount_raised':
					$value = $this->get_campaign( $campaign_id )->get_donated_amount();
					break;

				case 'percentage_of_goal':
					$value = number_format( $this->get_campaign( $campaign_id )->get_percent_donated_raw(), 2 );
					break;

				case 'status':
					$value = $this->get_campaign( $campaign_id )->get_status();
					break;

				case 'campaign_creator':
					$value = $this->get_campaign_creator( $campaign_id )->get_name();
					break;

				case 'campaign_creator_id':
					$value = $this->get_campaign( $campaign_id )->get_campaign_creator();
					break;

				case 'campaign_creator_email':
					$value = $this->get_campaign_creator( $campaign_id )->get_email();
					break;

				case 'campaign_creator_address':
					$value = $this->get_campaign_creator( $campaign_id )->get( 'donor_address' );
					break;

				case 'campaign_creator_address_2':
					$value = $this->get_campaign_creator( $campaign_id )->get( 'donor_address_2' );
					break;

				case 'campaign_creator_city':
					$value = $this->get_campaign_creator( $campaign_id )->get( 'donor_city' );
					break;

				case 'campaign_creator_state':
					$value = $this->get_campaign_creator( $campaign_id )->get( 'donor_state' );
					break;

				case 'campaign_creator_postcode':
					$value = $this->get_campaign_creator( $campaign_id )->get( 'donor_postcode' );
					break;

				case 'campaign_creator_country':
					$value = $this->get_campaign_creator( $campaign_id )->get( 'donor_country' );
					break;

				case 'campaign_creator_phone':
					$value = $this->get_campaign_creator( $campaign_id )->get( 'donor_phone' );
					break;

				case 'campaign_creator_full_address':
					$value = str_replace( '<br/>', PHP_EOL, $this->get_campaign_creator( $campaign_id )->get_address() );
					break;

			}

			return $value;
		}

		/**
		 * Return the CSV column headers.
		 *
		 * The columns are set as a key=>label array, where the key is used to retrieve the data for that column.
		 *
		 * @since  1.6.0
		 *
		 * @return string[]
		 */
		protected function get_csv_columns() {
			return array(
				'campaign_id'                   => __( 'Campaign ID', 'charitable' ),
				'campaign_name'                 => __( 'Title', 'charitable' ),
				'post_date'                     => __( 'Date Created', 'charitable' ),
				'end_date'                      => __( 'End Date', 'charitable' ),
				'goal'                          => __( 'Goal', 'charitable' ),
				'amount_raised'                 => __( 'Amount Raised', 'charitable' ),
				'percentage_of_goal'            => __( 'Percentage of Goal Raised', 'charitable' ),
				'status'                        => __( 'Status', 'charitable' ),
				'campaign_creator'              => __( 'Campaign Creator', 'charitable' ),
				'campaign_creator_id'           => __( 'Campaign Creator ID', 'charitable' ),
				'campaign_creator_email'        => __( 'Campaign Creator Email', 'charitable' ),
				'campaign_creator_address'      => __( 'Campaign Creator Address', 'charitable' ),
				'campaign_creator_address_2'    => __( 'Campaign Creator Address Line 2', 'charitable' ),
				'campaign_creator_city'         => __( 'Campaign Creator City', 'charitable' ),
				'campaign_creator_state'        => __( 'Campaign Creator State', 'charitable' ),
				'campaign_creator_postcode'     => __( 'Campaign Creator Postcode', 'charitable' ),
				'campaign_creator_country'      => __( 'Campaign Creator Country', 'charitable' ),
				'campaign_creator_phone'        => __( 'Campaign Creator Phone', 'charitable' ),
				'campaign_creator_full_address' => __( 'Campaign Creator Full Address', 'charitable' ),
			);
		}

		/**
		 * Return a Campaign object for the given ID.
		 *
		 * @since  1.6.0
		 *
		 * @param  int $campaign_id The campaign ID.
		 * @return Charitable_Campaign
		 */
		protected function get_campaign( $campaign_id ) {
			if ( ! array_key_exists( $campaign_id, $this->campaigns ) ) {
				$this->campaigns[ $campaign_id ] = charitable_get_campaign( $campaign_id );
			}

			return $this->campaigns[ $campaign_id ];
		}

		/**
		 * Return a User object for a campaign creator.
		 *
		 * @since  1.6.0
		 *
		 * @param  int $campaign_id The campaign ID.
		 * @return Charitable_User
		 */
		public function get_campaign_creator( $campaign_id ) {
			$creator = $this->get_campaign( $campaign_id )->get_campaign_creator();

			if ( ! array_key_exists( $creator, $this->creators ) ) {
				$this->creators[ $creator ] = charitable_get_user( $creator );
			}

			return $this->creators[ $creator ];
		}

		/**
		 * Get the data to be exported.
		 *
		 * @since  1.6.0
		 *
		 * @return array
		 */
		protected function get_data() {
			$query_args = array(
				'fields' => 'ids',
			);

			if ( strlen( $this->args['start_date'] ) || strlen( $this->args['end_date'] ) ) {
				$date_query = array(
					'inclusive' => true,
				);

				if ( strlen( $this->args['start_date'] ) ) {
					$date_query['after'] = charitable_sanitize_date( $this->args['start_date'], 'Y-m-d' );
				}

				if ( strlen( $this->args['end_date'] ) ) {
					$date_query['before'] = charitable_sanitize_date( $this->args['end_date'], 'Y-m-d' );
				}

				$query_args['date_query'] = $date_query;
			}

			if ( ! empty( $this->args['status'] ) ) {
				switch ( $this->args['status'] ) {
					case 'active':
						$query_args['post_status'] = 'publish';
						$query_args['meta_query']  = array(
							'relation' => 'OR',
							array(
								'key'     => '_campaign_end_date',
								'value'   => date( 'Y-m-d H:i:s' ),
								'compare' => '>',
								'type'    => 'datetime',
							),
							array(
								'key'     => '_campaign_end_date',
								'value'   => 0,
								'compare' => '=',
							),
						);
						break;

					case 'finish':
						$query_args['post_status'] = 'publish';
						$query_args['meta_query']  = array(
							array(
								'key'     => '_campaign_end_date',
								'value'   => date( 'Y-m-d H:i:s' ),
								'compare' => '<=',
								'type'    => 'datetime',
							),
						);
						break;

					default:
						$query_args['post_status'] = $this->args['status'];
				}
			}

			/**
			 * Filter the campaigns export query arguments.
			 *
			 * @since 1.6.0
			 *
			 * @param array $query_args The query arguments.
			 * @param array $args       The export arguments.
			 */
			$query_args = apply_filters( 'charitable_export_campaigns_query_args', $query_args, $this->args );

			return Charitable_Campaigns::query( $query_args )->posts;
		}

		/**
		 * Receives a row of data and maps it to the keys defined in the columns.
		 *
		 * @since  1.6.0
		 *
		 * @param  int $data The campaign ID.
		 * @return mixed
		 */
		protected function map_data( $data ) {
			$row = array();

			foreach ( $this->columns as $key => $label ) {
				$value = isset( $data[ $key ] ) ? $data[ $key ] : '';
				$value = apply_filters( 'charitable_export_data_key_value', $value, $key, $data );
				$row[] = $value;
			}

			return $row;
		}
	}

endif;
