<?php

namespace App\Helpers\Traits;

use Illuminate\Support\Str;

/**
 * Trait used on models to specify that the database should use UUID's instead of incremental id's
 *
 * @package App\Helpers
 * @author Brandon Tassone <brandon@objectiv.co>
 * @version 1.0.0
 */
trait UsesUuid
{
	protected static function bootUsesUuid()
	{
		static::creating(function ($model) {
			if (! $model->getKey()) {
				$model->{$model->getKeyName()} = (string) Str::uuid();
			}
		});
	}

	public function getKeyType()
	{
		return 'string';
	}
}