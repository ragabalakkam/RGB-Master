<?php

namespace Tests\Unit;

use App\Models\Apps\AppClient;
use Tests\TestCase;

class AppProcessTest extends TestCase
{
    /** @test */
    public function can_handle_active_process()
    {
        $app = random(AppClient::all());
        $app->update(['active_process' => null, 'started_process_at' => null]);

        $seconds = 0;
        $process = "installation";
        $timeCol = "{$process}_time";

        # start process
        $app->start_process($process);
        $this->assertEquals($process, $app->active_process);
        $this->assertNotNull($app->started_process_at);
        $this->assertNull($app->{$timeCol});

        sleep($seconds);

        # end process
        $app->end_process($process);
        $this->assertEquals($seconds, $app->{$timeCol});
        $this->assertNull($app->active_process);
        $this->assertNull($app->started_process_at);
        $this->assertNotNull($app->{$timeCol});

        # 0-time
        $app->start_process($process);
        $app->end_process($process);
        $this->assertEquals(0, $app->{$timeCol});
        $this->assertNull($app->active_process);
        $this->assertNull($app->started_process_at);
    }
}
