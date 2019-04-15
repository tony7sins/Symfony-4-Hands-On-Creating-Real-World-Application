<?php
namespace App\Event;

use App\Event\UserRegisterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use \Swift_Mailer as Swift_Mailer;
use \Twig_Environment as Twig_Environment;

class UserSubscriber implements EventSubscriberInterface
{
    /** @var \Swift_Mailer $mailer */
    private $mailer;

    /** @var \Twig_Environment $twig */
    private $twig;

    public function __construct(
        Swift_Mailer $mailer,
        Twig_Environment $twig 
    ) {
        $this->mailer = $mailer;  
        $this->twig = $twig;  
    }
    public static function getSubscribedEvents()
    {
        return [
            UserRegisterEvent::NAME => 'onUserRegister'
        ];
    }

    public function onUserRegister(UserRegisterEvent $event)
    {
        $body = $this->twig->render('email/registration.html.twig', [
            'user' =>$event->getRegisteredUser()
        ]);

        $message = (new \Swift_Message())
            ->setSubject('Wonderful Subject')
            ->setFrom('micropost@micropost.dev')
            ->setTo($event->getRegisteredUser()->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}
