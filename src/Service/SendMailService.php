<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService 
{
    private $mailer = null;

    public function __construct(MailerInterface $mailer){
        $this->mailer = $mailer;
    }

    public function sendMail(
        $from, 
        $to, 
        $subject, 
        $template, 
        $context = null
        ){
            //On cree le mail
            $email = (new TemplatedEmail())
                ->from($from)
                ->to($to)
                ->subject($subject)
                ->htmlTemplate("_emaills/$template.html.twig")
                ->context($context);

            //On envoi le mail
            $this->mailer->send($email);
        }
}












