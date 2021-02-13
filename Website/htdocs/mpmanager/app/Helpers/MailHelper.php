<?php

namespace App\Helpers;

use App\Exceptions\MailNotSentException;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

class MailHelper
{
    /**
     * @var PHPMailer
     */
    private $mailer;

    private $mailSettings;

    public function __construct(PHPMailer $mailer)
    {
        $this->mailer = $mailer;
        $this->mailSettings = config('mail');

        $this->configure();
    }


    private function configure(): void
    {
        $this->mailer->Host = $this->mailSettings['host'];
        $this->mailer->Port = $this->mailSettings['port'];
        $this->mailer->SMTPSecure = $this->mailSettings['smtp_secure'];
        $this->mailer->SMTPAuth = $this->mailSettings['smtp_auth'];
        $this->mailer->Username = $this->mailSettings['username'];
        $this->mailer->Password = $this->mailSettings['password'];
        $this->mailer->From = $this->mailSettings['default_sender'];
        $this->mailer->isSMTP();                       // telling the class to use SMTP
    }

    /**
     * @param  $to
     * @param  $title
     * @param  $body
     * @param  null $attachment
     * @throws MailNotSentException
     * @throws PHPMailerException
     */
    public function sendPlain($to, $title, $body, $attachment = null): void
    {
        //don't send any mails while  testing
        if (config('app.env') === 'testing') {
            return;
        }
        $this->mailer->setFrom($this->mailSettings['default_sender']);
        $this->mailer->addReplyTo($this->mailSettings['default_sender']);

        $this->mailer->addAddress($to);

        $this->mailer->Subject = $title;
        $this->mailer->Body = $body;

        if ($attachment) {
            $this->mailer->addAttachment($attachment);
        }

        $this->mailer->AltBody = $this->mailSettings['default_message'];


        if (!$this->mailer->send()) {
            throw new MailNotSentException($this->mailer->ErrorInfo);
        }
    }

    //TODO: add sendHTMLTemplate
}
