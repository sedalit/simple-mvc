<?php

namespace PHPFramework\Services\Mail;

class Mail {
    public string $from;
    public string $subject;
    public string $body;
    public array $to = [];
    public array $cc = [];
    public array $bcc = [];
    public array $attachments = [];

    public function __construct(string $from = '', string $subject = '', string $body = '', array $to = [], array $cc = [], array $bcc = [], array $attachments = []) {
        $this->from = $from;
        $this->subject = $subject;
        $this->body = $body;
        $this->to = $to;
        $this->cc = $cc;
        $this->bcc = $bcc;
        $this->attachments = $attachments;
    }
}