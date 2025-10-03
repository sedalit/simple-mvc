<?php

namespace PHPFramework\Services\Mail;

use PHPFramework\Factory;
use PHPFramework\Interfaces\ServiceProviderInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPFramework\ServiceContainer;

class MailService implements ServiceProviderInterface {
    protected PHPMailer $phpMailer;
    protected array $mails = [];

    public function register(ServiceContainer $c) : void
    {
        $c->setFactory('mail', new Factory(self::class));
    }

    public function __construct()
    {
        require_once CONFIG . '/mail.php';

        $this->phpMailer = new PHPMailer();
        $this->phpMailer->isSMTP(); 
        $this->phpMailer->Host = MAIL['host'];
        $this->phpMailer->Username = MAIL['username'];
        $this->phpMailer->Password = MAIL['password'];
        $this->phpMailer->SMTPAuth = MAIL['smtpAuth'];
        $this->phpMailer->SMTPSecure = MAIL['smtpSecure'];
        $this->phpMailer->Port = MAIL['port'];
    }

    public function send(Mail|array $mail) : bool|array
    {
        if (is_array($mail)) {
            $results = [];
            foreach ($mail as $value) {
                $results[] = $this->sendMail($value);
            }

            return $results;
        }

        return $this->sendMail($mail);
    }

    protected function sendMail(Mail $mail) : bool
    {
        $this->phpMailer->setFrom($mail->from);
        
        foreach ($mail->to as $value) {
            $this->phpMailer->addAddress($value);
        }

        foreach ($mail->cc as $value) {
            $this->phpMailer->addCC($value);
        }

        foreach ($mail->bcc as $value) {
            $this->phpMailer->addBCC($value);
        }

        foreach ($mail->attachments as $value) {
            $this->phpMailer->addAttachment($value);
        }

        $this->phpMailer->Subject = $mail->subject;
        $this->phpMailer->Body = $mail->body;

        return $this->phpMailer->send();
    }
}