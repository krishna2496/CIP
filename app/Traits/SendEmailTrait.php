<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppMailer;
use Illuminate\Support\Facades\Log;

trait SendEmailTrait
{
    /**
     * Send email
     *
     * @param array $param
     * @return void
     */
    protected function sendEmail(array $params)
    {
        Mail::to($params['to'])->send(new AppMailer($params));
        Log::info('Mail sent to '. $params['to']. 'with subject :' . $params['subject']);
    }
}
