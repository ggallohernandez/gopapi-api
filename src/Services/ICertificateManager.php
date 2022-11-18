<?php declare(strict_types=1);

namespace App\Services;

use App\DTOs\Certificate;
use App\DTOs\CreateCertificateRequest;

interface ICertificateManager
{
    public function createCertificate(CreateCertificateRequest $request): Certificate;
}