<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'handle_contact_form', methods: ['POST'])]
    public function handleContactForm(Request $request, MailerInterface $mailer): Response
    {
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $subject = $request->request->get('subject');
        $message = $request->request->get('message');

        $email = (new Email())
            ->from($email)
            ->to('contact@garageparrot.com') // Adresse e-mail destinataire
            ->subject($subject)
            ->text($message);

        $mailer->send($email);

        // Redirection ou réponse appropriée après l'envoi du message
        return $this->redirectToRoute('app_default_contact');
    }
}
