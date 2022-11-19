<?php declare(strict_types=1);

namespace App\Services;

interface IDnsRecordFetcher
{
    public function getTxtRecords(string $domain): array;
}