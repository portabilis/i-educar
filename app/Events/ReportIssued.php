<?php

namespace App\Events;

class ReportIssued
{
    private string $content;

    public function __construct(
        public string $render,
        public string $template,
        public bool $success,
        public bool $authenticate = false,
    ) {
    }

    public function replace(string $content): void
    {
        $this->content = $content;
    }

    public function content(): string
    {
        return $this->content;
    }
}
