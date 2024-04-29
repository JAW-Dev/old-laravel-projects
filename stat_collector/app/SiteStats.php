<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Traits\UsesUuid;

/**
 * The model that handles the site specific data from a Checkout for WooCommerce installation
 *
 * @package App
 * @author Brandon Tassone <brandon@objectiv.co>
 * @version 1.0.0
 */
class SiteStats extends Model
{
    use UsesUuid;

	/**
	 * Turn off incrementing for uuid's
	 *
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * Turn off auto generated timestamps
	 *
	 * @var bool
	 */
	public $timestamps = true;

	/**
	 * How to return the json data columns
	 *
	 * @var array
	 */
	protected $casts = [
		'gateways' => 'array',
		'active_plugins' => 'array',
		'inactive_plugins' => 'array',
		'theme' => 'array',
		'wc_settings' => 'array',
		'cfw_settings' => 'array',
		'debug_modes' => 'array',
        'shipping_methods' => 'array',
        'install_date' => 'date',
        'created_at' => 'date',
	];

	/**
	 * @var array
	 */
	protected $fillable = [
        'site_id',
		'php_version',
		'cfw_version',
		'wp_version',
		'mysql_version',
		'server',
		'php_max_upload_size',
		'php_default_timezone',
		'php_soap',
		'php_fsockopen',
		'php_curl',
		'memory_limit',
		'install_date',
		'multisite',
		'locale',
		'theme',
		'gateways',
		'wc_settings',
		'cfw_settings',
		'inactive_plugins',
		'active_plugins',
		'shipping_methods',
		'debug_modes'
	];
}
