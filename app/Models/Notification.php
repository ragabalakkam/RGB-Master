<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class Notification extends Model
{
  use SerializesModels;

  public $incrementing = false;

  protected $dates = [
    'created_at',
    'updated_at',
    'read_at'
  ];

  protected $fillable = [
    'id',
    'type',
    'notifiable_id',
    'notifiable_type',
    'data',
    'read_at',
  ];

  protected $hidden = [
    'notifiable_id',
    'notifiable_type',
    'type',
  ];

  protected $casts = [
    'data' => 'array',
  ];
}
