<?php declare(strict_types=1);

namespace App\DTOs;


class CreateCertificateRequest extends Certificate
{
    public int $keySize = 2048;
    public string $keyType = 'RSA';
    public int $validityInDays = 365;
    public string $csr = '';

    public function __construct(string $domain, int $validityInDays, string $csr)
    {
        $this->commonName = $domain;
        $this->validityInDays = $validityInDays;
        $this->csr = $csr;
    }

    public function getDomain(): string
    {
        return $this->commonName;
    }
}