<?php

namespace App\Service;

use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailManager
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * The method is used to send an email in HTML format
     *
     * @param array $sendParameter
     * @param string $bodyHtmlFile
     * @param array $bodyData
     * @throws Exception
     */
    public function sendMessage(array $sendParameter, string $bodyHtmlFile, array $bodyData = [])
    {
        $error = '';

        $email = (new TemplatedEmail())
            ->from(Address::fromString($sendParameter['from']))
            ->to(Address::fromString($sendParameter['to']))
            ->subject($sendParameter['subject'])
            ->htmlTemplate($bodyHtmlFile)
            ->context($bodyData)
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $error = $e;
        }

        if (!empty($error)) {
            throw new Exception("Une erreur a été détecté, ce qui a empêché l'envoie du mail : " . $error);
        }
    }
}
