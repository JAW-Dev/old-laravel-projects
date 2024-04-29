<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
		Schema::defaultStringLength(191);

        Collection::macro('arrayCountValues', $this->arrayCountValuesMacro());

        $url->forceScheme('https');
    }

	/**
	 * Returns the macro function for array_count_values
	 *
	 * @return callable
	 */
	public function arrayCountValuesMacro (): callable {
		/**
		 * @param string $pluck
		 *
		 * @return Collection
		 */
		return function (string $pluck) {
            $array = $this->pluck( $pluck )->collapse()->toArray();



            foreach($array as $key => $value) {
                if(is_null($value)) {
                    unset($array[$key]);
                }
            }

			return new Collection( array_count_values( $array ) );
            return;
		};
	}
}
