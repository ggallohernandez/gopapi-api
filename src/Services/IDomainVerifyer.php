<?php

namespace App\Services;

interface IDomainVerifyer
{
    public function verify(string $domain): bool;
}