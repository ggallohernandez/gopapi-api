<?php

namespace App\DTOs;

class Certificate
{
    protected string $domain;
    protected string $certificate;
    protected string $private_key;

    public function __construct(string $domain, string $certificate, string $private_key)
    {
        $this->domain = $domain;
        $this->certificate = $certificate;
        $this->private_key = $private_key;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getCertificate(): string
    {
        return $this->certificate;
    }

    public function getPrivateKey(): string
    {
        return $this->private_key;
    }
}