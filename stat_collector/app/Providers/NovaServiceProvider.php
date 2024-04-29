<?php

namespace App\Providers;

use Laravel\Nova\Nova;
use Laravel\Nova\Fields\ID;
use App\Nova\Metrics\PHPCurl;
use App\Nova\Metrics\PHPSoap;
use App\Nova\Metrics\MultiSite;
use App\Nova\Metrics\TopThemes;
use App\Nova\Metrics\WPVersion;
use App\Nova\Metrics\CFWVersion;
use App\Nova\Metrics\PHPVersion;
use App\Nova\Metrics\ServerType;
use App\Nova\Metrics\SiteLocale;
use App\Nova\Metrics\TotalItems;
use App\Nova\Metrics\TotalSales;
use App\Nova\Metrics\MemoryLimit;
use App\Nova\Metrics\TopGateways;
use App\Nova\Metrics\TotalOrders;
use App\Nova\Metrics\MySQLVersion;
use App\Nova\Metrics\PHPFsockopen;
use Illuminate\Support\Facades\Gate;
use App\Nova\Metrics\NewlyActiveSites;
use App\Nova\Metrics\PHPMaxUploadSize;
use App\Nova\Metrics\TopActivePlugins;
use App\Nova\Metrics\PHPDefaultTimezone;
use App\Nova\Metrics\TopShippingMethods;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        ID::useComponent('nova-id-link');
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                env('ADMIN_EMAIL'),
				env('ADMIN_EMAIL2')
            ]);
        });
    }

    /**
     * Get the cards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            new NewlyActiveSites,
            new TotalSales,
            new TotalOrders,
            new TotalItems,
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
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
