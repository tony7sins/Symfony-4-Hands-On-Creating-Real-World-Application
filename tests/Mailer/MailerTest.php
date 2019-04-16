<?php
namespace App\Tests\Mailer;

use App\Entity\User;
use App\Mailer\Mailer;
// use PHPUnit\Framework\TestCase;
use Symfony\Bundle\WebProfilerBundle\Tests\TestCase;

class MailerTest extends TestCase
{
    public function testConfirmationEmail()
    {
        $user = new User();
        $user->setEmail('john@mail.ru');

        $swiftMailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $swiftMailer->expects($this->once())->method('send')
            ->with($this->callback(function ($subject){
                $messageStr = (string)$subject;
                dump($messageStr);
                // return true;

                return  strpos($messageStr, 'From: test@mail.com') !== false 
                        && strpos($messageStr, 'Content-Type: text/html; charset=utf-8') !== false 
                        && strpos($messageStr, 'Subject: Wonderful Subject') !== false
                        && strpos($messageStr, 'To: john@mail.ru') !== false
                        && strpos($messageStr, 'This is a message body') !== false;
            }));

        $twigMock = $this->getMockBuilder(\Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $twigMock->expects($this->once())->method('render')
            ->with('email/registration.html.twig', 
                [
                    'user' =>$user
                ]
            )->willReturn('This is a message body');

        $mailer = new Mailer($swiftMailer, $twigMock, 'test@mail.com');
        $mailer->sendConfirmationEmail($user);
    }
}