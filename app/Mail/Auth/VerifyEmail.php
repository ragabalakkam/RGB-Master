<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    private $data;

    public function __construct($data)
    {
        $data['name'] = firstName($data['name']);
        $this->data = $data;
    }

    public function build()
    {
        return $this
            ->subject('Welcome to ' . parseName(getConfig('app_name'), 'en'))
            ->markdown('Mail\Auth\VerifyEmail')->with($this->data);
    }
}
