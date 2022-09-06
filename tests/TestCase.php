<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public static $max_execution_time = 0.05;

    function login_as_master()
    {
        $user = User::where('username', 'like', 'minaalfy')->first();
        return $this->actingAs($user);
    }

    function check_execution_time(Callable $callback, $max = null)
    {
        $time = microtime(true);
        $output = $callback();
        
        $time_diff = microtime(true) - $time;
        $this->assertLessThan($max ?? static::$max_execution_time, $time_diff);

        return $output;
    }
}
