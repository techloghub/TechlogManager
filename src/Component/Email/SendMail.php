<?php
//发送邮件
namespace Component\Email;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SendMail {
    private $container;
    private $logger;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->logger = $container->get("sdk_mail.logger");
    }

    public function sendMail($toMail, $title, $content, $ccMails=array()) {
        $this->logger->info("send mail: $toMail|$title|".json_encode($ccMails));
        $this->logger->info("mail content:\n".$content);
        $message = \Swift_Message::newInstance()
        ->setSubject($title)
        ->setFrom('open@'.php_uname("n"))
        ->setTo($toMail);
        if ($ccMails) {
            foreach ($ccMails as $email) {
                $message->addCc($email);
            }
        }
        $message->setBody($content, 'text/html');
        $this->container->get('mailer')->send($message);
    }
}