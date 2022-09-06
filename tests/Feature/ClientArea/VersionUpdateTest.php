<?php

namespace Tests\Feature\ClientArea;

use App\Models\Apps\AppClient;
use Tests\TestCase;

use Illuminate\Support\Str;

class VersionUpdateTest extends TestCase
{
    private function getTestApp()
    {
        return random(AppClient::all());
    }

    /** @test */
    public function cannot_proceed_request_with_non_exist_ids()
    {
        $app = $this->getTestApp();
        $wrong_id = Str::random(9);

        $url = "/api/v1/clients/$wrong_id/apps/{$app->id}/latest-version?secret={$app->secret}";
        $this->get($url)->assertStatus(404);

        $url = "/api/v1/clients/{$app->client_id}/apps/{$wrong_id}/latest-version?secret={$app->secret}";
        $this->get($url)->assertStatus(404);

        $url = "/api/v1/clients/{$wrong_id}/apps/{$wrong_id}/latest-version?secret={$app->secret}";
        $this->get($url)->assertStatus(404);
    }

    /** @test */
    public function cannot_proceed_request_with_mismatched_ids()
    {
        $app = $this->getTestApp();
        $app2 = $this->getTestApp();
        
        $url = "/api/v1/clients/{$app->client_id}/apps/{$app2->id}/latest-version?secret={$app->secret}";
        $response = $this->get($url);
        $response->assertStatus(422);
        $response->assertExactJson(['errors' => ['client_id' => ['invalid'], 'app_id' => ['invalid']]]);
    }

    /** @test */
    public function cannot_proceed_request_with_invalid_app_secret()
    {
        $app = $this->getTestApp();
        $secrets = ['', 'secret=', 'secret=' . Str::random(50)];
        
        foreach ($secrets as $secret) {
            $url = "/api/v1/clients/{$app->client_id}/apps/{$app->id}/latest-version?" . $secret;
            $response = $this->get($url);
            $response->assertStatus(422);
            $response->assertExactJson(['errors' => ['secret' => ['invalid']]]);
        }
    }
    
    /** @test */
    public function can_proceed_valid_request()
    {
        $app = $this->getTestApp();
        $url = "/api/v1/clients/{$app->client_id}/apps/{$app->id}/latest-version?secret={$app->secret}";
        $response = $this->get($url);
        $response->assertStatus(200);
    }

    /** @test */
    public function can_update_installation_status()
    {
        $seconds = 0;
        $process = 'installation';
        $timeCol = "{$process}_time";
        
        # start as $process already running
        $app = $this->getTestApp();
        $app->start_process($process);
        $url = "/api/v1/clients/{$app->client_id}/apps/{$app->id}/installation-status?secret={$app->secret}";

        sleep($seconds);

        # fail
        $response = $this->put($url, ['status' => "{$process}_failed", 'exception' => 'some error']);
        $response->assertStatus(200);
        $app = $response->getData();
        $this->assertNull($app->active_process);
        $this->assertNull($app->started_process_at);
        $this->assertEquals($seconds, $app->{$timeCol});

        # start
        $response = $this->put($url, ['status' => "{$process}_started"]);
        $response->assertStatus(200);
        $app = $response->getData();
        $this->assertNotNull($app->started_process_at);
        $this->assertEquals($process, $app->active_process);
        $this->assertEquals(0, $app->{$timeCol});

        sleep($seconds);

        # end
        $response = $this->put($url, ['status' => "{$process}_succeeded"]);
        $response->assertStatus(200);
        $app = $response->getData();
        $this->assertNull($app->active_process);
        $this->assertNull($app->started_process_at);
        $this->assertEquals($seconds, $app->{$timeCol});
    }

    /** @test */
    public function can_update_version_status()
    {
        $seconds = 0;
        $process = 'update';
        $timeCol = "{$process}_time";
        
        # start as $process already running
        $app = $this->getTestApp();
        $app->start_process($process);
        $url = "/api/v1/clients/{$app->client_id}/apps/{$app->id}/installation-status?secret={$app->secret}";
        $payload = ['new_version_id' => $app->app->latest_version_id, 'old_version_id' => $app->version_id]; 

        sleep($seconds);

        # fail
        $response = $this->put($url, array_merge($payload, ['status' => "{$process}_failed", 'exception' => 'some error']));
        $response->assertStatus(200);
        $app = $response->getData();
        $this->assertNull($app->active_process);
        $this->assertNull($app->started_process_at);
        $this->assertEquals($seconds, $app->{$timeCol});
        $this->assertEquals($payload['old_version_id'], $app->version_id);

        # start
        $response = $this->put($url, array_merge($payload, ['status' => "{$process}_started"]));
        $response->assertStatus(200);
        $app = $response->getData();
        $this->assertNotNull($app->started_process_at);
        $this->assertEquals($process, $app->active_process);
        $this->assertEquals(0, $app->{$timeCol});
        $this->assertEquals($payload['old_version_id'], $app->version_id);

        sleep($seconds);

        # end
        $response = $this->put($url, array_merge($payload, ['status' => "{$process}_succeeded"]));
        $response->assertStatus(200);
        $app = $response->getData();
        $this->assertNull($app->active_process);
        $this->assertNull($app->started_process_at);
        $this->assertEquals($seconds, $app->{$timeCol});
        $this->assertEquals($payload['new_version_id'], $app->version_id);
    }
}
