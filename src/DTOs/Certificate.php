<?php declare(strict_types=1);

namespace App\DTOs;

use JsonSerializable;

class Certificate implements JsonSerializable
{
    public string $commonName = '';
    public string $content = '';

    public string $privateKey = '';

    public function jsonSerialize()
    {
        return [
            'commonName' => $this->commonName,
            'certificate' => $this->content,
            'private_key' => $this->privateKey
        ];
    }
}