<?php declare(strict_types=1);

namespace App\DTOs;


class CreateCertificateRequest
{
    protected string $domain;
    protected int $validity_days;
    protected string $csr;

    public function __construct(string $domain, int $validity_days, string $csr)
    {
        $this->domain = $domain;
        $this->validity_days = $validity_days;
        $this->csr = $csr;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }
}