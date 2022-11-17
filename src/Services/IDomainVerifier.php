<?php

namespace App\Services;

interface IDomainVerifier
{
    public function verify(string $domain): bool;
}