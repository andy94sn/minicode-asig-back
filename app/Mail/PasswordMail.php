<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordMail extends Mailable
{
    use SerializesModels;

    public string $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    public function build()
    {
        return $this->markdown('vendor.mail.html.reset')
            ->with([
                'password' => $this->password,
            ])
            ->subject('Reset Password');
    }
}
