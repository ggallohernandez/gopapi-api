<?php declare(strict_types=1);

namespace App\DTOs;

use JsonSerializable;

class CertificateSigningRequest implements JsonSerializable
{
    public string $commonName = '';
    public string $content = '';
    public string $private_key = '';
    
    public string $organization = '';
    public string $organizationalUnit = '';
    public string $city = '';
    public string $state = '';
    public string $country = 'US';
    public int $keySize = 2048;
    public string $keyType = 'RSA';

    public function jsonSerialize()
    {
        return [
            'commonName' => $this->commonName,
            'csr' => $this->content,
            'private_key' => $this->private_key
        ];
    }
}