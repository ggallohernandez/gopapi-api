<?php declare(strict_types=1);

namespace App\Services;

interface IDomainVerifier
{
    public function verify(string $domain): bool;
}