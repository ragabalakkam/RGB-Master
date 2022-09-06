<?php

namespace App\Jobs\Roles;

use App\Models\Roles\Role;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyUsersByRoleJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $tries = 1;
  public $deleteWhenMissingModels = true;

  public $role_key;

  public $notification;
  public $params;

  public function __construct($role_key, $notification, ...$params)
  {
    $this->role_key = $role_key;
    $this->notification = $notification;
    $this->params = $params;
  }

  public function handle()
  {
    $role_key = $this->role_key;

    if (!($role = Role::where('key', $role_key)->first()))
      throw new Exception("Cannot find role ($role_key)");

    foreach ($role->employees->whereNotNull('email')->whereNotNull('email_verified_at') as $user) {
      $user->notify(new $this->notification(...$this->params));
    }
  }
}
