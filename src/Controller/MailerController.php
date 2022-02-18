<?php
namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailerController extends AbstractController {


    #[Route('mail/test', name:'testEmail')]
    public function testEmail (MailerInterface $mailer):Response
    {
        try {
            $email = (new TemplatedEmail())
            ->from('contact@superblog.com')
            ->to('john@doe.fr')
            ->subject('Un nouvel article est en ligne')
            ->htmlTemplate('email/newArticle.html.twig')
            ->textTemplate('email/newArticle.html.twig');
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            dump($e->getMessage());
            die();
        }
        return $this->redirectToRoute('article');
    }
}