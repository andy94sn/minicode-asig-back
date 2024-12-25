<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $lang;
    public string $type;
    public array $files;

    public function __construct($files, $type, $lang)
    {
        $this->files = $files;
        $this->type = $type;
        $this->lang = $lang;

        app()->setLocale($this->lang);
    }

    public function build()
    {
        $subject = $this->getSubjectMessage($this->type, $this->lang);
        $welcome  = $this->getTextMessage($this->type, $this->lang);

        $body  = $this->lang == 'ro'
            ? 'În atașament, veți găsi polița de asigurare RCAI/E, în format PDF, conform comenzii plasate. Vă rugăm să o verificați și să vă asigurați că toate informațiile sunt corecte. !'
            : 'В прикрепленном к письму файле вы найдете страховой полис ОСАГО в формате PDF, соответствующий размещенному заказу. Просим вас проверить его и убедиться, что все информация верна.';

        $caption = $this->lang == 'ro'
            ? 'Păstrând acest email, veți avea întotdeauna acces rapid la documentele Dvs. de asigurare.'
            : 'Сохраняя это электронное письмо, вы всегда будете иметь быстрый доступ к вашим документам по страхованию.';

        $text_phone =  $this->lang == 'ro'
            ? 'Pentru orice întrebări sau asistență suplimentară, vă rugăm să nu ezitați să ne contactați la numerele de telefon:'
            : 'Для любых вопросов или дополнительной помощи, пожалуйста, не стесняйтесь связаться с нами по следующим телефонным номерам:';

        $phones = array();

        $text_email =  $this->lang == 'ro'
            ? 'sau prin email la:'
            : 'или по электронной почте:';

        $email = 'office@ozoncar.md';

        $thanks = $this->lang == 'ro'
            ? 'Vă mulțumim pentru încrederea acordată și vă asigurăm de tot suportul nostru. Drumuri bune și sigure!'
            : 'Спасибо за ваше доверие, мы готовы оказать вам всю необходимую поддержку. Хорошей и безопасной поездки!';

        $footer = $this->lang == 'ro'
            ? 'Cu apreciere, OzonCar.'
            : 'С уважением, OzonCar.';




        $mail =  $this->subject($subject)
            ->markdown('vendor.mail.html.message')
            ->with([
                'url'   => 'https://ozoncar.md',
                'image' => asset('storage/images/ozoncar.png'),
                'welcome'  => $welcome,
                'slot'     => nl2br($body),
                'caption'  => nl2br($caption),
                'textPhone'   =>  nl2br($text_phone),
                'phones'  => $phones,
                'textEmail' => $text_email,
                'email' => $email,
                'thanks' => $thanks,
                'footer' => nl2br($footer)
            ]);

        foreach ($this->files as $file) {
            if (file_exists($file)) {
                $mail->attachData(file_get_contents($file), uniqid() . '.pdf', [
                    'mime' => 'application/pdf',
                ]);
            }
        }

        return $mail;

    }

    private function getSubjectMessage($type, $lang): ?string
    {
        $string = null;

        if($type == 'rca'){
           $string = $lang == 'ro' ? 'Asigurare RCAI - OzonCar' : 'Страхования ОСАГО - OzonCar';
        }elseif($type =='greenCard'){
           $string = $lang == 'ro' ? 'Asigurare RCAE - OzonCar' : 'Страхования RCAE - OzonCar';
        }

        return $string;
    }

    private function getTextMessage($type, $lang): ?string
    {
        $string = null;

        if($type == 'rca'){
            $string = $lang == 'ro' ? 'Mulțumim că ați ales OzonCar.MD pentru asigurarea RCAI!' : 'Благодарим вас за выбор OzonCar.MD для страхования ОСАГО';
        }elseif($type =='greenCard'){
            $string = $lang == 'ro' ? 'Mulțumim că ați ales OzonCar.MD pentru asigurarea RCAE!' : 'Благодарим вас за выбор OzonCar.MD для страхования RCAE!';
        }

        return $string;
    }
}
