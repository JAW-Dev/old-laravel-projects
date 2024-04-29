<?php

namespace App\Faker;

use App\Helpers\Generators\VersionGenerator;
use Faker\Generator;
use App\Helpers\Remote\GithubRemote;

/**
 * Faker provider that generates database data for testing stats
 *
 * @package App\Faker
 * @author Brandon Tassone <brandon@objectiv.co>
 * @version 1.0.0
 */
class SiteStatsFaker extends BaseStatFaker
{
	/**
	 * Github oauth token for the API requests
	 *
	 * @var string
	 */
	private $github_oauth_token = '';

	/**
	 * The default options to be passed along with the api call
	 *
	 * @var array
	 */
	protected $default_fetch_options = [];

	/**
	 * PHP api url for release tags
	 *
	 * @var string
	 */
	protected $php_version_path = 'repos/php/php-src/tags';

	/**
	 * Checkout For WooCommerce api url for release tags
	 *
	 * @var string
	 */
	protected $cfw_version_path = 'repos/Objectivco/checkout-for-woocommerce/tags';

	/**
	 * Wordpress api url for release tags
	 *
	 * @var string
	 */
	protected $wp_version_path = 'repos/Wordpress/Wordpress/tags';

	/**
	 * List of wordpress themes for faker to choose from
	 *
	 * @var string[]
	 */
	protected $wp_themes = ['storefront', 'twentyfifteen', 'twentysixteen', 'twentyseventeen', 'twentynineteen'];

	/**
	 * Some wordpress options use yes and no instead of true and false
	 *
	 * @var string[]
	 */
	protected $yes_no = ['yes', 'no'];

	/**
	 * Currency to be used for testing
	 *
	 * @var string
	 */
	protected $wc_currency = 'USD';

	/**
	 * The tax classes to be used for testing
	 *
	 * @var string
	 */
	protected $wc_tax_classes = 'Reduced rate\r\nZero rate';

	/**
	 * The types the taxes are based on
	 *
	 * @var string[]
	 */
	protected $taxes_based_on = ['base', 'billing', 'shipping'];

	/**
	 * Determines whether the item is included or excluded
	 *
	 * @var string[]
	 */
	protected $incl_excl = ['incl', 'excl'];

	/**
	 * The options for the tax total display
	 *
	 * @var string[]
	 */
	protected $tax_total_display = ['single', 'itemized'];

	/**
	 * The options for ship to destination
	 *
	 * @var string[]
	 */
	protected $ship_to_dest = ['shipping', 'billing', 'billing_only'];

	/**
	 * The list of Checkout for WooCommerce templates
	 *
	 * @var string[]
	 */
	protected $cfw_template = ['copify', 'default', 'futurist'];

	/**
	 * List of active plugins to choose randomly from
	 *
	 * @var string[]
	 */
	protected $plugins = [
		'checkout-for-woocommerce/checkout-for-woocommerce.php',
		'woocommerce/woocommerce.php',
		'pixelyoursite-pro/pixelyoursite-pro.php',
		'relative-url/relative-url.php',
		'wordpress-importer/wordpress-importer.php',
		'acf-pro/acf-pro.php',
		'genesis/genesis.php',
		'sometestframework/sometestframework.php'
	];

	/**
	 * List of gateways to choose randomly from
	 *
	 * @var string[]
	 */
	protected $gateways = [
		'cheque',
		'cod',
		'paypal',
		'klarna',
		'square',
		'stripe',
		'amazon'
	];

	/**
	 * Taxable statuses
	 *
	 * @var array
	 */
	protected $shipping_taxable = ["taxable", ""];

	/**
	 * Shipping id's to choose from
	 *
	 * @var array
	 */
	protected $shipping_id = ["Flat Rate", "Free Shipping", "Local Pickup", "FedEx", "UPS"];

	/**
	 * The locale of the site
	 *
	 * @var string
	 */
	protected $cfw_locale = 'en_US';

	/**
	 * Filtered data holder for the PHP version data
	 *
	 * @var array
	 */
	public static $php_version_data = [];

	/**
	 * Filtered data holder for the Checkout for WooCommerce version data
	 *
	 * @var array
	 */
	public static $cfw_version_data = [];

	/**
	 * Filtered data holder for the Wordpress version data
	 *
	 * @var array
	 */
	public static $wp_version_data = [];

	/**
	 * SiteStatsFaker constructor.
	 *
	 * @param Generator $generator
	 * @param string $github_oauth_token
	 * @param int $per_page
	 */
	public function __construct( Generator $generator, string $github_oauth_token, int $per_page = 100 ) {
		parent::__construct( $generator );

		$this->github_oauth_token = $github_oauth_token;
		$this->default_fetch_options = [ 'per_page' => $per_page ];
	}

	/**
	 * Fetches and sets the static version data variable specified if not already retrieved. This way when the faker
	 * object is instantiated many times we don't make extra calls to the api
	 *
	 * @param string $version_data_name
	 * @param string $path_attr_name
	 * @param int $min_major_version
	 * @param array $fetch_options
	 *
	 * @return void
	 */
	private function set_version_data(
		string $version_data_name,
		string $path_attr_name,
		int $min_major_version = 0,
		array $fetch_options = []
	): void {
		// If its already filled, return
		if(!empty(static::$$version_data_name))
			return;

		// Create the GithubRemote class with the relevant data to the github api call
		$github = new GithubRemote($this->github_oauth_token, $this->$path_attr_name);
		// Merge any passed in options that aren't default
		$fetch_options = array_merge($this->default_fetch_options, $fetch_options);

		// Fetch and assign the data
		static::$$version_data_name = $github->get_parsed_versions($fetch_options, $min_major_version);
	}

	/**
	 * Returns a random php version number
	 *
	 * @return string
	 */
	public function php_version(): string
	{
		// Fetch and set the php version data if not already set
		$this->set_version_data('php_version_data', 'php_version_path', 5);

		// Get random value from the data
		return static::randomElement(static::$php_version_data);
	}

	/**
	 * Get a random Checkout for WooCommerce version number from github
	 *
	 * @return string
	 */
	public function cfw_version(): string {
		// Fetch and set the cfw version data if not already set
		$this->set_version_data('cfw_version_data', 'cfw_version_path');

		// Get random value from the data
		return static::randomElement(static::$cfw_version_data);
	}

	/**
	 * Get a random Wordpress version number from github
	 *
	 * @return string
	 */
	public function wp_version(): string {
		// Fetch and set the wp version data if not already set
		$this->set_version_data('wp_version_data', 'wp_version_path', 4);

		// Get random value from the data
		return static::randomElement(static::$wp_version_data);
	}

	/**
	 * Returns a random mysql version
	 *
	 * @return string
	 */
	public function mysql_version(): string {
		return VersionGenerator::random_version_number([5, 5], [0, 20]);
	}

	/**
	 * Returns a random up to 3 digit max upload size in MB
	 *
	 * @return string
	 */
	public function php_max_upload_size(): string {
		return static::randomNumber(3) . " MB";
	}

	/**
	 * Return a random timezone
	 *
	 * @return string
	 */
	public function php_default_timezone(): string {
		return $this->generator->timezone;
	}

	/**
	 * Returns yes no on whether the user has the php soap extension enabled
	 *
	 * @return string
	 */
	public function php_soap(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns yes no on whether the user has the php fsockopen extension enabled
	 *
	 * @return string
	 */
	public function php_fsockopen(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns yes no on whether the user has the php curl extension enabled
	 *
	 * @return string
	 */
	public function php_curl(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a random up to 3 digit memory limit in MB
	 *
	 * @return string
	 */
	public function memory_limit(): string  {
		return static::randomNumber(3) . " MB";
	}

	/**
	 * Returns random server string
	 *
	 * @return string
	 */
	public function server(): string {
		return static::randomElement(['nginx/', 'apache/']) . VersionGenerator::random_version_number();
	}

	/**
	 * Returns a random date from a year ago until now
	 *
	 * @return \DateTime
	 */
	public function install_date(): string {
		return $this->generator->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s');
	}

	/**
	 * Returns a random true false value on if the site is a multisite or not
	 *
	 * @return bool
	 */
	public function multisite(): bool {
		return $this->generator->boolean(10);
	}

	/**
	 * Returns a random theme array of wordpress theme properties
	 *
	 * @return array[
	 * 	'name' => string,
	 * 	'version' => string,
	 *  'child_theme' => string,
	 *  'wc_support' => string
	 * ]
	 */
	public function theme(): array {
		return array(
			'name' => $this->theme_name(),
			'version' => $this->theme_version(),
			'child_theme' => $this->theme_child_theme(),
			'wc_support' => $this->theme_wc_support()
		);
	}

	/**
	 * Returns a random wordpress theme from the list above in wp_themes
	 *
	 * @return string
	 */
	public function theme_name(): string {
		return static::randomElement($this->wp_themes);
	}

	/**
	 * Returns a random version number for the wordpress theme
	 *
	 * @return string
	 */
	public function theme_version(): string {
		return VersionGenerator::random_version_number();
	}

	/**
	 * Returns random yes or no string on whether the theme uses a child theme
	 *
	 * @return string
	 */
	public function theme_child_theme(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a random yes or no string on whether the theme has WooCommerce support
	 *
	 * @return string
	 */
	public function theme_wc_support(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * @return array[
	 * 'woocommerce_default_country' => string,
	 * 'woocommerce_default_customer_address' => string,
	 * 'woocommerce_calc_taxes' => string,
	 * 'woocommerce_enable_coupons' => string,
	 * 'woocommerce_calc_discounts_sequentially' => string,
	 * 'woocommerce_currency' => string,
	 * 'woocommerce_prices_include_tax' => string,
	 * 'woocommerce_tax_based_on' => string,
	 * 'woocommerce_tax_round_at_subtotal' => string,
	 * 'woocommerce_tax_classes' => string,
	 * 'woocommerce_tax_display_shop' => string,
	 * 'woocommerce_tax_display_cart' => string,
	 * 'woocommerce_tax_total_display' => string,
	 * 'woocommerce_enable_shipping_calc' => string,
	 * 'woocommerce_shipping_cost_requires_address' => string,
	 * 'woocommerce_ship_to_destination' => string,
	 * 'woocommerce_enable_guest_checkout' => string,
	 * 'woocommerce_enable_checkout_login_reminder' => string,
	 * 'woocommerce_enable_signup_and_login_from_checkout' => string,
	 * 'woocommerce_registration_generate_username' => string,
	 * 'woocommerce_registration_generate_password' => string
	 * ]
	 */
	public function wc_settings(): array {
		return [
			'woocommerce_default_country' => $this->woocommerce_default_country(),
			'woocommerce_default_customer_address' => $this->generator->address,
			'woocommerce_calc_taxes' => $this->woocommerce_calc_taxes(),
			'woocommerce_enable_coupons' => $this->woocommerce_enable_coupons(),
			'woocommerce_calc_discounts_sequentially' => $this->woocommerce_calc_discounts_sequentially(),
			'woocommerce_currency' => $this->woocommerce_currency(),
			'woocommerce_prices_include_tax' => $this->woocommerce_prices_include_tax(),
			'woocommerce_tax_based_on' => $this->woocommerce_tax_based_on(),
			'woocommerce_tax_round_at_subtotal' => $this->woocommerce_tax_round_at_subtotal(),
			'woocommerce_tax_classes' => $this->woocommerce_tax_classes(),
			'woocommerce_tax_display_shop' => $this->woocommerce_tax_display_shop(),
			'woocommerce_tax_display_cart' => $this->woocommerce_tax_display_cart(),
			'woocommerce_tax_total_display' => $this->woocommerce_tax_total_display(),
			'woocommerce_enable_shipping_calc' => $this->woocommerce_enable_shipping_calc(),
			'woocommerce_shipping_cost_requires_address' => $this->woocommerce_shipping_cost_requires_address(),
			'woocommerce_ship_to_destination' => $this->woocommerce_ship_to_destination(),
			'woocommerce_enable_guest_checkout' => $this->woocommerce_enable_guest_checkout(),
			'woocommerce_enable_checkout_login_reminder' => $this->woocommerce_enable_checkout_login_reminder(),
			'woocommerce_enable_signup_and_login_from_checkout' => $this->woocommerce_enable_signup_and_login_from_checkout(),
			'woocommerce_registration_generate_username' => $this->woocommerce_registration_generate_username(),
			'woocommerce_registration_generate_password' => $this->woocommerce_registration_generate_password()
		];
	}

	/**
	 * Returns the default country and state. Four our purposes the country is always US and returns a random state
	 *
	 * @return string
	 */
	public function woocommerce_default_country(): string {
		return "US:{$this->generator->stateAbbr}";
	}

	/**
	 * Returns a yes/no value for if the site calculates taxes
	 *
	 * @return string
	 */
	public function woocommerce_calc_taxes(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a random yes/no value for if the site has enabled coupons
	 *
	 * @return string
	 */
	public function woocommerce_enable_coupons(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a random yes/no value for if the site calculates discounts sequentially or not
	 *
	 * @return string
	 */
	public function woocommerce_calc_discounts_sequentially(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns USD for the currency since we are just using USD values
	 *
	 * @return string
	 */
	public function woocommerce_currency(): string {
		return $this->wc_currency;
	}

	/**
	 * Returns a random yes/no value for if prices are included in the tax
	 *
	 * @return string
	 */
	public function woocommerce_prices_include_tax(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a random value from taxes_based_on for what the sites taxes are based on
	 *
	 * @return string
	 */
	public function woocommerce_tax_based_on(): string {
		return static::randomElement($this->taxes_based_on);
	}

	/**
	 * Returns a random yes/no value for if the taxes are rounded at subtotal
	 *
	 * @return string
	 */
	public function woocommerce_tax_round_at_subtotal(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a static string for the tax classes. See tax_classes for the string
	 *
	 * @return string
	 */
	public function woocommerce_tax_classes(): string {
		return $this->wc_tax_classes;
	}

	/**
	 * Returns a random value of incl/excl for if the taxes are displayed on the shop
	 *
	 * @return string
	 */
	public function woocommerce_tax_display_shop(): string {
		return static::randomElement($this->incl_excl);
	}

	/**
	 * Returns a random value of incl/excl for if the taxes are displayed on the cart
	 *
	 * @return string
	 */
	public function woocommerce_tax_display_cart(): string {
		return static::randomElement($this->incl_excl);
	}

	/**
	 * Returns a random value from tax_total_display
	 *
	 * @return string
	 */
	public function woocommerce_tax_total_display(): string {
		return static::randomElement($this->tax_total_display);
	}

	/**
	 * Returns a random yes/no value for if shipping calculation is enabled
	 *
	 * @return string
	 */
	public function woocommerce_enable_shipping_calc(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a random yes/no value for if the shipping cost requires and address
	 *
	 * @return string
	 */
	public function woocommerce_shipping_cost_requires_address(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a random value from ship_to_dest
	 *
	 * @return string
	 */
	public function woocommerce_ship_to_destination(): string {
		return static::randomElement($this->ship_to_dest);
	}

	/**
	 * Returns a random yes/no value for whether guest checkout is enabled
	 *
	 * @return string
	 */
	public function woocommerce_enable_guest_checkout(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a random yes/no value if the checkout login reminder is enabled
	 *
	 * @return string
	 */
	public function woocommerce_enable_checkout_login_reminder(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a random yes/no value if the signup and login from checkout is enabled
	 *
	 * @return string
	 */
	public function woocommerce_enable_signup_and_login_from_checkout(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a random yes/no value if the registration function generates a username for the user
	 *
	 * @return string
	 */
	public function woocommerce_registration_generate_username(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a random yes/no value if the registration function generates a password for the user
	 *
	 * @return string
	 */
	public function woocommerce_registration_generate_password(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns an array of random cfw_settings
	 *
	 * @return array[
	 * 		'active_template => string,
	 * 		'enable' => string,
	 * 		'enable_phone_fields' => string,
	 * 		'header_scripts_empty' => bool,
	 * 		'footer_scripts_empty' => bool
	 * ]
	 */
	public function cfw_settings() {
		return array(
			"active_template" => $this->active_template(),
			"enable" => $this->enable(),
			"enable_phone_fields" => $this->enable_phone_fields(),
			"header_scripts_empty" => $this->header_scripts_empty(),
			"footer_scripts_empty" => $this->footer_scripts_empty()
		);
	}

	/**
	 * Returns at random a template from the cfw_themes list
	 *
	 * @return string
	 */
	public function active_template(): string {
		return static::randomElement($this->cfw_template);
	}

	/**
	 * Returns at random a yes/no value if Checkout for WooCommerce is enabled
	 *
	 * @return string
	 */
	public function enable(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a random yes/no value if phone fields are enabled for Checkout for WooCommerce
	 *
	 * @return string
	 */
	public function enable_phone_fields(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a random true/false value if the header scripts are empty
	 *
	 * @return bool
	 */
	public function header_scripts_empty(): bool {
		return $this->generator->boolean();
	}

	/**
	 * Returns a random true/false value if the footer scripts are empty
	 *
	 * @return bool
	 */
	public function footer_scripts_empty(): bool {
		return $this->generator->boolean();
	}

	/**
	 * Returns a json string containing 4 random plugins from the active_plugins list
	 *
	 * @return string[]
	 */
	public function active_plugins(): array {
		return $this->generator->randomElements($this->plugins, 4);
	}

	/**
	 * Returns a json string containing 4 random plugins from the inactive_plugins list
	 *
	 * @return string[]
	 */
	public function inactive_plugins(): array {
		return $this->generator->randomElements($this->plugins, 4);
	}

	/**
	 * Returns a json string containing 3 random gateways from the active_gateways list
	 *
	 * @return string[]
	 */
	public function gateways(): array {
		return $this->generator->randomElements($this->gateways, 3);
	}

	/**
	 * Returns a static locale for US since that's all we need to test at the moment
	 *
	 * @return string
	 */
	public function site_locale(): string {
		return $this->cfw_locale;
	}

	/**
	 * Returns the sites debug mode options
	 *
	 * @return array[
	 * 	'wp_debug_mode' => string,
	 *  'cfw_debug_mode' => string
	 * ]
	 */
	public function debug_modes(): array {
		return array(
			'wp_debug_mode' => $this->wp_debug_mode(),
			'cfw_debug_mode' => $this->cfw_debug_mode()
		);
	}

	/**
	 * Returns a yes no value on whether the wordpress debug mode is on
	 *
	 * @return string
	 */
	public function wp_debug_mode(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns a yes no value on whether the Checkout for WooCommerce debug mode is on
	 *
	 * @return string
	 */
	public function cfw_debug_mode(): string {
		return static::randomElement($this->yes_no);
	}

	/**
	 * Returns random shipping methods
	 *
	 * @return array[
	 * 	'id' => string
	 * 	'tax_status' => string
	 * ]
	 */
	public function shipping_methods(): array {
		$count = rand(1,5);
		$shipping_methods = [];

		for($i = 0; $i < $count; $i++) {
			$shipping_methods[] = [
				'id' => $this->shipping_method_id()
			];
		}

		return $shipping_methods;
	}

	/**
	 * Returns a random shipping method id
	 *
	 * @return string
	 */
	public function shipping_method_id(): string {
		return static::randomElement($this->shipping_id);
	}

	/**
	 * Returns a random shipping method tax status
	 *
	 * @return string
	 */
	public function shipping_method_tax_status(): string {
		return static::randomElement($this->shipping_taxable);
	}
}
