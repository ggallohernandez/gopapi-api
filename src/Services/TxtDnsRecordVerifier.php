<?php declare(strict_types=1);

namespace App\Services;


class TxtDnsRecordVerifier implements IDomainVerifier
{
    public function verify(string $domain): bool
    {
        // todo: use dig +short @8.8.8.8 domain.com txt
        return true;
    }
}