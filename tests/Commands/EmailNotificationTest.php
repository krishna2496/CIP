<?php
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class EmailNotificationTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_should_run_email_notification_for_all_tenant()
    {
        $this->artisan('send:email-notification');        
    }
}
