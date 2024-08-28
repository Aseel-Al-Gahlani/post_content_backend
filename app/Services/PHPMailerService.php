<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerService
{
    protected $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);


        $this->mailer->isSMTP();
        $this->mailer->Host       = env('MAIL_HOST', 'smtp.example.com');
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = env('MAIL_USERNAME', 'aseel@example.com');
        $this->mailer->Password   = env('MAIL_PASSWORD', 'aseel');
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = env('MAIL_PORT', 587);


        $this->mailer->setFrom(env('MAIL_FROM_ADDRESS', 'aseel@example.com'), env('MAIL_FROM_NAME', 'Post System'));
    }

    public function send($toEmail, $toName, $subject, $body)
    {
        try {
            $this->mailer->addAddress($toEmail, $toName);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;

            return $this->mailer->send();
        } catch (Exception $e) {
            return false;
        }
    }
}
