<?php

namespace App\Models\Roles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
	use HasFactory;

	public $timestamps = false;

	protected $fillable = [
		'name',
		'permission_id',
	];



	# relationships

	public function permission()
	{
		return $this->belongsTo(Permission::class, 'permission_id');
	}
}
