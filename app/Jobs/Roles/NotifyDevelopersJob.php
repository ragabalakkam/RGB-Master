<?php

namespace App\Jobs\Roles;

class NotifyDevelopersJob extends NotifyUsersByRoleJob
{
    public function __construct(...$args)
    {
        parent::__construct('developer', ...$args);
    }
}
