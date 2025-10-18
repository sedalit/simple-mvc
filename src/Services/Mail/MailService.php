<?php

namespace PHPFramework\Services\Mail;

use PHPFramework\Factory;
use PHPFramework\Interfaces\ServiceProviderInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPFramework\ServiceContainer;
use PHPMailer\PHPMailer\Exception;

class MailService implements ServiceProviderInterface {
    protected PHPMailer $phpMailer;
    protected array $mails = [];

    public function register(ServiceContainer $c) : void
    {
        $c->setFactory('mail', new Factory(self::class));
    }

    public function __construct(string $configPath = CONFIG . '/mail.php')
    {
        if (file_exists($configPath)) {
            require_once CONFIG . '/mail.php';
        } else {
            throw new \Exception('Mail configuration file not found at ' . $configPath);
        }

        $this->phpMailer = new PHPMailer(true);
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

    public function getMails() : array
    {
        return $this->mails;
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

        $error = null;
        try {
            $result = $this->phpMailer->send();
        } catch (Exception $e) {
            $result = false;
            $error = $e->getMessage();
        }
        $data = ['mail' => $mail, 'result' => $result];
        if ($error) $data['error'] = $error;

        $this->mails[] = $data;

        return $result;
    }
}