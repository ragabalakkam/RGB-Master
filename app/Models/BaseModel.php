<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
	use HasFactory;

	protected static function boot()
	{
		parent::boot();
		static::bootTraits();

		# creating
		self::creating(function ($model) {
			if (method_exists(static::class, 'onCreating'))
				static::onCreating($model);
		});

		# updating
		self::updating(function ($model) {
			if (method_exists(static::class, 'onUpdating'))
				static::onUpdating($model);
		});

		# deleting
		self::deleting(function ($model) {
			if (method_exists(static::class, 'onDeleting'))
				static::onDeleting($model);
		});
	}

	protected static function bootTraits()
	{
		$class = static::class;

		$booted = [];

		static::$traitInitializers[$class] = [];

		foreach (class_uses_recursive($class) as $trait) {
			$method = 'boot' . class_basename($trait);

			if (method_exists($class, $method) && !in_array($method, $booted)) {
				forward_static_call([$class, $method]);

				$booted[] = $method;
			}

			if (method_exists($class, $method = 'initialize' . class_basename($trait))) {
				static::$traitInitializers[$class][] = $method;

				static::$traitInitializers[$class] = array_unique(
					static::$traitInitializers[$class]
				);
			}
		}
	}
}
