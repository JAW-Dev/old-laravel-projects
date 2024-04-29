<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use App\Nova\Filters\DateTo;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use App\Nova\Filters\DateFrom;
use App\Nova\Metrics\TotalItems;
use App\Nova\Metrics\TotalSales;
use App\Nova\Metrics\TotalOrders;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Http\Requests\NovaRequest;

class OrderStats extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\OrderStats';

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
        'site_id'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Site ID', 'site_id')->sortable(),
            Text::make('Total Sales', 'total_sales')->resolveUsing(function($total_sales) {
                setlocale(LC_MONETARY, 'en_US.UTF-8');
                return money_format('%.2n', $this->total_sales );
            })->sortable(),
            Text::make('Total Orders', 'total_orders')->sortable(),
            Text::make('Total Items', 'total_items')->sortable(),
            Date::make('Date Submitted', 'created_at')->sortable(),
        ];
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
            new TotalSales,
            new TotalOrders,
            new TotalItems
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
