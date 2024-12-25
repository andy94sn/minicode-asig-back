<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $page;

    public function __construct($page)
    {
        $this->page = $page;
    }

    public function build(){
        $subject = 'Notificare - Pagina '.$this->page->title.'OzonCar';
        $welcome = 'Salutare,';
        $body =   'Ați primit un mesaj in secțiunea Contacte. Vă rugăm să procesați această solicitare cât mai curând posibil.';
        $footer = 'Cu apreciere, OzonCar';

        return $this->subject($subject)
            ->markdown('vendor.mail.html.contact')
            ->with([
                'url'   => 'https://ozoncar.md',
                'image' => asset('storage/images/ozoncar.png'),
                'welcome'  => $welcome,
                'slot'     => nl2br($body),
                'footer' => nl2br($footer)
            ]);
    }
}
