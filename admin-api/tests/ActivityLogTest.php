<?php

class ActivityLogTest extends TestCase
{
    /**
     * @test
     *
     * Returns activity logs
     *
     * @return void
     */
    public function it_should_return_activity_logs()
    {
        $this->get("tenants/logs?from_date=".date('Y-m-d')."&to_date=".date('Y-m-d'), [])
        ->seeStatusCode(200);

        $this->get("tenants/logs?type=".config("constants.activity_log_types.TENANT"), [])
        ->seeStatusCode(200);

        $this->get("tenants/logs?action=".config("constants.activity_log_actions.CREATED"), [])
        ->seeStatusCode(200);

        $this->get("tenants/logs?users=1", [])
        ->seeStatusCode(200);

        $this->get("tenants/logs?type=test", [])
        ->seeStatusCode(422);
    }
}
