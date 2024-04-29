<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Traits\UsesUuid;

/**
 * The model that handles the order stat side of the data sent to us from a Checkout for WooCommerce installation
 *
 * @package App
 * @author  Brandon Tassone <brandon@objectiv.co>
 * @version 1.0.0
 */
class OrderStats extends Model
{
    use UsesUuid;

    /**
     * Turn off auto incrementing
     *
     * @var boolean
     */
    public $incrementing = false;

    /**
     * Turn off auto generated timestamps
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * How to return the json data columns
     *
     * @var array
     */
    protected $casts = ['submitted' => 'date'];

    /**
     * @var array
     */
    protected $fillable = [
        'site_id',
        'total_sales',
        'total_orders',
        'total_items',
        'total_sales_localized',
    ];
}//end class
