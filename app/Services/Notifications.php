<?php
namespace App\Services;

class Notifications
{
    private $mailer;

    public function __construct(Mail $mailer)
    {
        $this->mailer = $mailer;
    }

    public function emailWasChanged($email, $selector, $token)
    {
        //Используем $email для отправки вместо gtestovik39@gmail.com
        $message = 'https://project/verify_email?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);
        $this->mailer->send($email, $message); // используем вместо gtestovik39@gmail.com
    }

    public function passwordReset($email, $selector, $token)
    {
        //Используем $email для отправки вместо gtestovik39@gmail.com
        $message = 'https://project/password-recovery/form?selector=' . \urlencode($selector) . '&token=' . \urlencode($token);
        $this->mailer->send($email, $message); // используем вместо gtestovik39@gmail.com
    }

    public function passwordUpdate($email)
    {
        //Используем $email для отправки вместо gtestovik39@gmail.com
        $message = 'Ваш пароль был изменён';
        $this->mailer->send($email, $message); // используем вместо gtestovik39@gmail.com
    }


}