<?php declare(strict_types=1);

namespace App\DTOs;

class Certificate
{
    public string $commonName = '';
    public string $content = '';

    public string $privateKey = '';
}