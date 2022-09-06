<?php

namespace App\Jobs\Roles;

class NotifyMastersJob extends NotifyUsersByRoleJob
{
    public function __construct(...$args)
    {
        parent::__construct('master', ...$args);
    }
}
