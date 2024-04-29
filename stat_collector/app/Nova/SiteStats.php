<?php

namespace App\Nova;

use Laravel\Nova\Panel;
use Laravel\Nova\Fields\ID;
use App\Nova\Filters\DateTo;
use Illuminate\Http\Request;
use App\Nova\Metrics\PHPCurl;
use App\Nova\Metrics\PHPSoap;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use App\Nova\Filters\DateFrom;
use App\Nova\Metrics\MultiSite;
use App\Nova\Metrics\TopThemes;
use App\Nova\Metrics\WPVersion;
use App\Nova\Metrics\CFWVersion;
use App\Nova\Metrics\PHPVersion;
use App\Nova\Metrics\ServerType;
use App\Nova\Metrics\SiteLocale;
use App\Nova\Metrics\MemoryLimit;
use App\Nova\Metrics\TopGateways;
use App\Nova\Metrics\MySQLVersion;
use App\Nova\Metrics\PHPFsockopen;
use Illuminate\Support\Facades\Log;
use App\Nova\Metrics\PHPMaxUploadSize;
use App\Nova\Metrics\TopActivePlugins;
use App\Nova\Metrics\PHPDefaultTimezone;
use App\Nova\Metrics\TopShippingMethods;
use Laravel\Nova\Http\Requests\NovaRequest;

class SiteStats extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\SiteStats';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        // Log::info(print_r($this->theme['name'], true));

        $array = [
            ID::make()->sortable(),
            Text::make('Site ID', 'site_id')->sortable(),
            Date::make('Date Installed', 'install_date')->sortable(),
            Date::make('Date Submitted', 'created_at')->sortable(),

            // Add Panel for theme information.
            new Panel('WordPress', $this->wordpressFields()),

            // Add Panel for theme information.
            new Panel('Theme', $this->themeFields()),

            // Add Panel for payment gateway information.
            new Panel('Payment Gateways', $this->gatewaysFields()),

            // Add Panel for WooCommerce settings.
            new Panel('WooCommerce Settings', $this->wooSettingsFields()),

            // Add Panel for CFW settings.
            new Panel('Checkout for WooCommerce Settings', $this->cfwSettingsFields()),

            // Add Panel for Active Plugins.
            new Panel('Active Plugins', $this->activePluginsFields()),
            // Add Panel for Inactive Plugins.
            new Panel('Inactive Plugins', $this->inactivePluginsFields()),

            // Add Panel for Server information.
            new Panel('Server', $this->serverFields()),
        ];

        // Add onto the main panel.
        $debug_modes = $this->debug_modes;
        foreach ( $debug_modes as $key => $value ) {
            $key = str_replace( array( 'wp_debug_mode', 'cfw_debug_mode' ), array( 'WP Debug Mode',  'CFW Debug Mode'), $key );
            $array[] = Text::make( $key, 'debug_modes')->resolveUsing(function($debug_modes) use($key, $value) {
                return $value;
            })->hideFromIndex();
        }

        return $array;
    }

    /**
     * Get the fields for the wordpress panel
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return array
     */
    public function wordpressFields() {
        return [
            Text::make('Checkout for Woocommerce Version', 'cfw_version')->hideFromIndex(),
            Text::make('WordPress Version', 'wp_version')->hideFromIndex(),
            Text::make('Miltisite', 'multisite')->resolveUsing(function($multisite) {
                $value = $multisite ? 'Yes' : 'No';
                return $value;
            })->hideFromIndex()
        ];
    }

    /**
     * Get the fields for the theme panel
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return array
     */
    public function gatewaysFields() {
        return [
            Text::make('Gateways', 'gateways')->resolveUsing(function($gateways) {
                return implode( ', ', $this->gateways );
            })->hideFromIndex()
        ];
    }

    /**
     * Get the fields for the theme panel
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return array
     */
    public function themeFields() {
        return [
            Text::make('Theme', 'theme')->resolveUsing(function($theme) {
                return $this->theme['name'];
            })->hideFromIndex(),
            Text::make('Version', 'theme')->resolveUsing(function($theme) {
                return $this->theme['version'];
            })->hideFromIndex(),
            Text::make('Woocommerce Support', 'theme')->resolveUsing(function($theme) {
                return $this->theme['wc_support'];
            })->hideFromIndex(),
            Text::make('Child Theme', 'theme')->resolveUsing(function($theme) {
                return $this->theme['child_theme'];
            })->hideFromIndex(),
        ];
    }

    /**
     * Get the fields for the WooCommerce Settings panel
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return array
     */
    public function wooSettingsFields() {
        $settings = $this->wc_settings;
        $array = [];

        foreach ( $settings as $key => $value ) {
            $array[] = Text::make($key, 'wc_settings')->resolveUsing(function($settings) use($key, $value) {
                return $value;
            })->hideFromIndex();
        }

        return $array;
    }

    /**
     * Get the fields for the CFW Settings panel
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return array
     */
    public function cfwSettingsFields() {
        $settings = $this->cfw_settings;
        $array = [];

        foreach ( $settings as $key => $value ) {
            $array[] = Text::make($key, 'cfw_settings')->resolveUsing(function($settings) use($key, $value) {
                return $value;
            })->hideFromIndex();
        }

        return $array;
    }

    /**
     * Get the fields for the server panel
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return array
     */
    public function serverFields() {
        return [
            Text::make('Server', 'server')->hideFromIndex(),
            Text::make('MySQL Version', 'mysql_version')->hideFromIndex(),
            Text::make('PHP Version', 'php_version')->hideFromIndex(),
            Text::make('PHP Max Upload Size', 'php_max_upload_size')->hideFromIndex(),
            Text::make('PHP Default Time Zone', 'php_default_timezone')->hideFromIndex(),
            Text::make('SOAP', 'php_soap')->hideFromIndex(),
            Text::make('fsockopen', 'php_fsockopen')->hideFromIndex(),
            Text::make('Memory Linit', 'memory_limit')->hideFromIndex(),
            Text::make('Locale', 'locale')->hideFromIndex(),
        ];
    }

    /**
     * Active Plugins
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return void
     */
    public function activePluginsFields() {
        $plugins = $this->active_plugins;
        $array = [];

        foreach ( $plugins as $key => $value ) {
            $array[] = Text::make($value, 'active_plugins')->resolveUsing(function($plugins) use($key, $value) {
                return ' ';
            })->hideFromIndex();
        }

        return $array;
    }

    /**
     * Inactive Plugins
     *
     * @author Jason Witt
     * @since  1.0.0
     *
     * @return void
     */
    public function inactivePluginsFields() {
        $plugins = $this->inactive_plugins;
        $array = [];

        foreach ( $plugins as $key => $value ) {
            $array[] = Text::make($value, 'inactive_plugins')->resolveUsing(function($plugins) use($key, $value) {
                return ' ';
            })->hideFromIndex();
        }

        return $array;
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            new CFWVersion,
            new PHPVersion,
            new TopGateways,
            new TopShippingMethods,
            new MultiSite,
            new WPVersion,
            new MySQLVersion,
            new ServerType,
            new PHPMaxUploadSize,
            new PHPDefaultTimezone,
            new PHPSoap,
            new PHPFsockopen,
            new PHPCurl,
            new MemoryLimit,
            new SiteLocale,
            new TopThemes,
            new TopActivePlugins
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new Filters\DateFrom(),
            new Filters\DateTo()
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
