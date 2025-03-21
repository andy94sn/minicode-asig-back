<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        $phones = explode(',', env('CONTACT_PHONES'));

        $text_email =  $this->lang == 'ro'
            ? 'sau prin email la:'
            : 'или по электронной почте:';

        $email = env('CONTACT_MAIL') ?? 'info@motoasig.md';

        $thanks = $this->lang == 'ro'
            ? 'Vă mulțumim pentru încrederea acordată și vă asigurăm de tot suportul nostru. Drumuri bune și sigure!'
            : 'Спасибо за ваше доверие, мы готовы оказать вам всю необходимую поддержку. Хорошей и безопасной поездки!';

        $footer = $this->lang == 'ro'
            ? 'Cu apreciere, ' . env('APP_NAME'). '.'
            : 'С уважением, ' . env('APP_NAME'). '.';


        $mail =  $this->subject($subject)
            ->markdown('vendor.mail.html.order')
            ->with([
                'url'   => 'https://motoasig.md',
                'image' => asset('storage/uploads/logo.png'),
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

        Log::info(print_r($this->files, true));

        foreach ($this->files as $file) {
            $response = Http::get($file);
            if ($response->successful()) {
                $fileContent = $response->body();
                $mail->attachData($fileContent, uniqid() . '.pdf', [
                    'mime' => 'application/pdf',
                ]);
            }
        }

        return $mail;

    }

    private function getSubjectMessage($type, $lang): ?string
    {
        $string = null;

        $app_name = env('APP_NAME');
        if($type == 'rca'){
           $string = $lang == 'ro' ? "Asigurare RCAI - $app_name" : "Страхования ОСАГО - $app_name";
        }elseif($type =='greenCard'){
           $string = $lang == 'ro' ? "Asigurare RCAE - $app_name" : "Страхования RCAE - $app_name";
        }

        return $string;
    }

    private function getTextMessage($type, $lang): ?string
    {
        $string = null;
        $app_name = env('APP_NAME');

        if($type == 'rca'){
            $string = $lang == 'ro' ? "Mulțumim că ați ales $app_name.MD pentru asigurarea RCAI!" : "Благодарим вас за выбор $app_name.MD для страхования ОСАГО";
        }elseif($type =='greenCard'){
            $string = $lang == 'ro' ? "Mulțumim că ați ales $app_name.MD pentru asigurarea RCAE!" : "Благодарим вас за выбор $app_name.MD для страхования RCAE!";
        }

        return $string;
    }
}
