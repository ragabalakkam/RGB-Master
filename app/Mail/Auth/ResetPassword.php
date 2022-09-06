<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
  use Queueable, SerializesModels;

  public $data;

  public function __construct($data)
  {
    $data['name'] = firstName($data['name']);
    $this->data = $data;
  }

  public function build()
  {
    return $this
      ->subject('Reset Your Password')
      ->markdown('Mail\Auth\ResetPassword')->with($this->data);
  }
}
