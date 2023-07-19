<?php

namespace WPForms\Integrations\Stripe\Admin;

use WPForms\Integrations\Stripe\Helpers;
use Stripe\Account;

/**
 * Stripe Connect functionality.
 *
 * @since 1.8.2
 */
class Connect {

	/**
	 * WPForms Stripe OAuth URL.
	 *
	 * @since 1.8.2
	 */
	const WPFORMS_URL = 'https://wpforms.com/oauth/stripe-connect';

	/**
	 * Stripe live/test account objects.
	 *
	 * @since 1.8.2
	 *
	 * @var array
	 */
	protected $accounts = [];

	/**
	 * Initialize.
	 *
	 * @since 1.8.2
	 *
	 * @return Connect
	 */
	public function init() {

		$this->hooks();

		return $this;
	}

	/**
	 * Hooks.
	 *
	 * @since 1.8.2
	 */
	private function hooks() {

		add_action( 'admin_init', [ $this, 'handle_oauth_handshake' ] );
	}

	/**
	 * Handle Stripe Connect OAuth handshake and save Stripe keys.
	 *
	 * @since 1.8.2
	 */
	public function handle_oauth_handshake() {

		if ( ! wpforms_current_user_can() || ! isset( $_GET['stripe_connect'] ) || $_GET['stripe_connect'] !== 'complete' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$state = isset( $_GET['state'] ) ? sanitize_text_field( wp_unslash( $_GET['state'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( empty( $state ) ) {
			return;
		}

		$credentials   = $this->fetch_stripe_credentials( $state );
		$required_keys = [ 'stripe_user_id', 'stripe_publishable_key', 'access_token', 'refresh_token', 'live_mode' ];

		if ( 0 !== count( array_diff( $required_keys, array_keys( $credentials ) ) ) ) {
			return;
		}

		$mode = empty( $credentials['live_mode'] ) ? 'test' : 'live';

		$this->set_connected_user_id( $credentials['stripe_user_id'], $mode );

		Helpers::set_stripe_key( $credentials['stripe_publishable_key'], 'publishable', $mode );
		Helpers::set_stripe_key( $credentials['access_token'], 'secret', $mode );

		$this->update_account_meta( $credentials['stripe_user_id'], $mode );
		$this->set_connected_account_country( $mode );

		$settings_url = $this->get_payments_settings_url();

		wp_safe_redirect( $settings_url );
		exit;
	}

	/**
	 * Fetch Stripe credentials from https://wpforms.com.
	 *
	 * @since 1.8.2
	 *
	 * @param string $state Anonymous autogenerated ID to safely fetch Stripe credentials.
	 *
	 * @return array
	 */
	protected function fetch_stripe_credentials( $state ) {

		$response = wp_remote_post(
			self::WPFORMS_URL,
			[
				'body' => [
					'action' => 'credentials',
					'state'  => $state,
				],
			]
		);

		if ( is_wp_error( $response ) ) {
			return [];
		}

		$body = wpforms_json_decode( wp_remote_retrieve_body( $response ), true );

		return is_array( $body ) ? $body : [];
	}

	/**
	 * Fetch Stripe Account from Stripe.
	 *
	 * @since 1.8.2
	 *
	 * @param string $mode Stripe mode (e.g. 'live' or 'test').
	 *
	 * @return null|Account
	 */
	protected function fetch_stripe_account( $mode = '' ) {

		$api_key = Helpers::get_stripe_key( 'secret', $mode );

		if ( ! $api_key ) {
			return null;
		}

		try {
			$account = Account::retrieve( null, sanitize_text_field( $api_key ) );
		} catch ( \Exception $e ) {
			$account = null;
		}

		return $account;
	}

	/**
	 * Update connected account meta.
	 *
	 * @since 1.8.2
	 *
	 * @param string $account_id Account ID.
	 * @param string $mode       Stripe mode (e.g. 'live' or 'test').
	 */
	public function update_account_meta( $account_id = '', $mode = '' ) {

		if ( ! $mode ) {
			$mode = Helpers::get_stripe_mode();
		}

		// Stripe API has limited update method for live accounts only.
		if ( $mode !== 'live' ) {
			return;
		}

		if ( ! $account_id ) {
			$account_id = $this->get_connected_user_id( $mode );
		}

		// Return early if no connected account.
		if ( ! $account_id ) {
			return;
		}

		$licence_type = wpforms_get_license_type();

		$metadata = [
			'wpforms_stripe'  => Helpers::is_addon_active() ? 'addon' : 'core',
			'wpforms_type'    => wpforms()->is_pro() ? 'pro' : 'lite',
			'wpforms_license' => $licence_type ? $licence_type : 'lite',
		];

		try {
			Account::update( $account_id, [ 'metadata' => $metadata ], Helpers::get_auth_opts() );
		} catch ( \Exception $e ) {
			wpforms_log(
				'Unable to update connected Stripe account meta.',
				$e->getMessage(),
				[
					'type' => [ 'payment', 'error' ],
				]
			);
		}
	}

	/**
	 * Generate random alphanumeric token string.
	 * Token length is always 32 chars.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	public function generate_random_token() {

		$random = false;

		if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
			$strong_result = false; // This has been added as argument #2 ($strong_result) cannot be passed by reference.
			$random        = openssl_random_pseudo_bytes( 16, $strong_result );
		}

		if ( $random === false ) {
			return md5( wp_rand() );
		}

		return bin2hex( $random );
	}

	/**
	 * Set fetched Stripe Account for caching purposes.
	 *
	 * @since 1.8.2
	 *
	 * @param string $mode Stripe mode (e.g. 'live' or 'test').
	 */
	protected function set_connected_account( $mode = '' ) {

		$user_id = $this->get_connected_user_id( $mode );

		if ( ! $user_id ) {
			return;
		}

		$account                 = $this->fetch_stripe_account( $mode );
		$this->accounts[ $mode ] = null;

		if ( ! isset( $account->id ) || $account->id !== $user_id ) {
			return;
		}

		$this->accounts[ $mode ] = $account;
	}

	/**
	 * Get cached Stripe Account or fetch it from Stripe.
	 *
	 * @since 1.8.2
	 *
	 * @param string $mode Stripe mode (e.g. 'live' or 'test').
	 *
	 * @return null|Account
	 */
	public function get_connected_account( $mode = '' ) {

		$mode = Helpers::validate_stripe_mode( $mode );

		if ( empty( $this->accounts ) || ( is_array( $this->accounts ) && ! array_key_exists( $mode, $this->accounts ) ) ) {
			$this->set_connected_account( $mode );
		}

		if ( ! empty( $this->accounts[ $mode ] ) ) {
			return $this->accounts[ $mode ];
		}

		return null;
	}

	/**
	 * Save connected user id to an option.
	 *
	 * @since 1.8.2
	 *
	 * @param string $user_id User id to set.
	 * @param string $mode    Stripe mode (e.g. 'live' or 'test').
	 *
	 * @return bool
	 */
	protected function set_connected_user_id( $user_id, $mode = '' ) {

		$mode = Helpers::validate_stripe_mode( $mode );

		return update_option( "wpforms_stripe_{$mode}_connect_user_id", sanitize_text_field( $user_id ) );
	}

	/**
	 * Get saved Stripe Connect user id from DB.
	 *
	 * @since 1.8.2
	 *
	 * @param string $mode Stripe mode (e.g. 'live' or 'test').
	 *
	 * @return string
	 */
	public function get_connected_user_id( $mode = '' ) {

		$mode    = Helpers::validate_stripe_mode( $mode );
		$user_id = get_option( "wpforms_stripe_{$mode}_connect_user_id", '' );

		/**
		 * User ID associated with the Stripe account.
		 *
		 * @since 1.8.2
		 *
		 * @param string $user_id User ID.
		 */
		return (string) apply_filters( 'wpforms_stripe_admin_connect_get_connected_user_id', $user_id ); // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName
	}

	/**
	 * Get Stripe Account name.
	 *
	 * @since 1.8.2
	 *
	 * @param string $mode Stripe mode (e.g. 'live' or 'test').
	 *
	 * @return string
	 */
	public function get_connected_account_name( $mode = '' ) {

		$account = $this->get_connected_account( $mode );

		if ( isset( $account->display_name ) ) {
			return $account->display_name;
		}

		if ( isset( $account->settings, $account->settings->dashboard->display_name ) ) {
			return $account->settings->dashboard->display_name;
		}

		return '';
	}

	/**
	 * Set Stripe Account country.
	 *
	 * @since 1.8.2
	 *
	 * @param string $mode Stripe mode (e.g. 'live' or 'test').
	 */
	private function set_connected_account_country( $mode = '' ) {

		$account = $this->get_connected_account( $mode );

		if ( ! isset( $account->country ) ) {
			return;
		}

		update_option( "wpforms_stripe_{$mode}_account_country", strtolower( $account->country ) );
	}

	/**
	 * Get Stripe Connect button URL.
	 *
	 * @since 1.8.2
	 *
	 * @param string $mode Stripe mode (e.g. 'live' or 'test').
	 *
	 * @return string
	 */
	public function get_connect_with_stripe_url( $mode = '' ) {

		$mode         = Helpers::validate_stripe_mode( $mode );
		$settings_url = $this->get_payments_settings_url();

		return add_query_arg(
			[
				'action'    => 'init',
				'live_mode' => absint( $mode === 'live' ),
				'state'     => $this->generate_random_token(),
				'site_url'  => rawurlencode( $settings_url ),
			],
			self::WPFORMS_URL
		);
	}

	/**
	 * Get "Payments" settings page URL.
	 *
	 * @since 1.8.2
	 *
	 * @return string
	 */
	private function get_payments_settings_url() {

		return add_query_arg(
			[
				'page' => 'wpforms-settings',
				'view' => 'payments',
			],
			admin_url( 'admin.php' )
		);
	}
}
